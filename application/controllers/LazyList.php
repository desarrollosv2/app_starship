<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LazyList extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('America/El_Salvador');

        
        $this->load->model('Gestion_Model', 'gestion');

        $this->load->library('EmpresaLib');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
       
    }

   
    // Retorna SOLO el HTML del contenido (sin <head>) para inyectar en #principal (inicia proceso para lazy list)
    public function ultimos_estatus()
    {
        $this->load->view('gestion_ultimos_estatus'); // vista "ligera"
    }

    // Endpoint DataTables (exige fechas + empresa)
    public function ultimos_estatus_data()
    {
        // Debug opcional:
        if ($this->input->get('debug') == '1') {
            $out = [
                'draw' => (int)$this->input->post('draw'),
                'recordsTotal' => 2,
                'recordsFiltered' => 2,
                'data' => [
                    [1, 'T123', 219, 'Ingresado', '2025-10-08 10:00:00', 'Empresa Demo'],
                    [2, 'T456', 20, 'En tránsito', '2025-10-08 11:00:00', 'Empresa Demo'],
                ],
            ];
            while (ob_get_level()) {
                ob_end_clean();
            }
            return $this->output->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $draw        = (int)$this->input->post('draw');
        $fecha_desde = $this->input->post('fecha_desde', true);
        $fecha_hasta = $this->input->post('fecha_hasta', true);
        $empresa_id  = (int)$this->input->post('empresa_id');

        // ✅ Requisito: fechas y empresa obligatorias
        if (empty($fecha_desde) || empty($fecha_hasta) || empty($empresa_id)) {
            $out = ['draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []];
            while (ob_get_level()) {
                ob_end_clean();
            }
            return $this->output->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_desde) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_hasta)) {
            $out = ['draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []];
            while (ob_get_level()) {
                ob_end_clean();
            }
            return $this->output->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        if (strtotime($fecha_desde) > strtotime($fecha_hasta)) {
            $tmp = $fecha_desde;
            $fecha_desde = $fecha_hasta;
            $fecha_hasta = $tmp;
        }

        $date_from = $fecha_desde . ' 00:00:00';
        $date_to   = $fecha_hasta . ' 23:59:59';

        $list  = $this->gestion->get_datatables($date_from, $date_to, $empresa_id);
        $start = (int)$this->input->post('start');

        $data = [];
        foreach ($list as $i => $row) {
            $data[] = [
                $start + $i + 1,
                $row->tracking_number,
                $row->id_estatus,
                $row->nombre_estatus,
                $row->fecha_ultimo_estatus,
                $row->fecha,
                $row->empresa_nombre ?? '', // mostramos el nombre de la empresa
            ];
        }

        $recordsTotal    = $this->gestion->count_all($date_from, $date_to, $empresa_id);
        $recordsFiltered = $this->gestion->count_filtered($date_from, $date_to, $empresa_id);

        $out = [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];

        while (ob_get_level()) {
            ob_end_clean();
        }
        $this->output->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    //fin lazy list
    public function empresas()
    {
        $rows = $this->gestion->get_empresas(); // ✅ desde el modelo
        while (ob_get_level()) {
            ob_end_clean();
        }
        $this->output->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function export_ultimos_estatus()
    {

        


        $empresa_id  = (int)$this->input->get('empresa_id');
        $fecha_desde = $this->input->get('fecha_desde', true);
        $fecha_hasta = $this->input->get('fecha_hasta', true);
        $search      = $this->input->get('search', true); // opcional: si quieres respetar búsqueda

        // Validaciones básicas (mismos requisitos que el grid)
        if (empty($empresa_id) || empty($fecha_desde) || empty($fecha_hasta)) {
            show_error('Faltan parámetros (empresa y rango de fechas).', 400);
            return;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_desde) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_hasta)) {
            show_error('Formato de fecha inválido.', 400);
            return;
        }
        if (strtotime($fecha_desde) > strtotime($fecha_hasta)) {
            $tmp = $fecha_desde;
            $fecha_desde = $fecha_hasta;
            $fecha_hasta = $tmp;
        }

        $date_from = $fecha_desde . ' 00:00:00';
        $date_to   = $fecha_hasta . ' 23:59:59';

        //echo $date_from . " " . $date_to;

        //$date_from = $fecha_desde;
        //$date_to   = $fecha_hasta;

        //die();

        // Nombre de archivo
        $filename = sprintf('ultimos_estatus_%s_%s_emp%s.csv', $fecha_desde, $fecha_hasta, $empresa_id);

        // Headers para CSV (Excel-friendly)
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // BOM UTF-8 para Excel
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');

        // Encabezados
        fputcsv($out, ['#', 'Tracking', 'ID Estatus', 'Nombre Estatus', 'Fecha Último Estatus', 'Fecha Manifiesto', 'Empresa', 'Devolucion']);

        // Exportación por lotes para no reventar memoria
        $batchSize = 5000;
        $offset = 0;
        $rownum = 0;

        

        


        do {

            //$date_from = '01/11/2025 00:00:00';
            //$date_to = '12/11/2025 23:59:59';

            //echo $date_from." ".$date_to." ".$empresa_id;

            $rows = $this->gestion->export_chunk($date_from, $date_to, $empresa_id, $search, $batchSize, $offset);


            
            
            
            if (empty($rows)) break;

            foreach ($rows as $r) {
                $rownum++;
                fputcsv($out, [
                    $rownum,
                    $r->tracking_number,
                    $r->id_estatus,
                    $r->nombre_estatus,
                    $r->fecha_ultimo_estatus,
                    $r->fecha_manifiesto,
                    $r->empresa_nombre,
                    ($r->id_estatus == 311) ? '1' : '0' 
                ]);
            }

            $offset += $batchSize;
            // Volcado del buffer para streaming
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        } while (count($rows) === $batchSize);

        fclose($out);
        exit;
    }
}
