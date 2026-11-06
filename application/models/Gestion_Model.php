<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Gestion_Model extends CI_Model
{

    private $column_order = [null, 'm.tracking_number', 'due.id_estatus', 'e.nombre', 'due.fecha_ultimo_estatus', 'm.fecha',   'm.id_empresa'];
    private $column_search = ['m.tracking_number', 'CAST(due.id_estatus AS CHAR)', 'e.nombre'];
    private $default_order = ['due.fecha_ultimo_estatus' => 'DESC'];
    public function getLastStatus($warehouse)
    {
        $query = "SELECT d.warehouse, d.fecha AS status_date, e.nombre AS name_status
                FROM detalle d
                INNER JOIN estatus e ON e.id_estatus = d.id_estatus
                WHERE d.fecha = (
                    SELECT MAX(dd.fecha)
                    FROM detalle dd
                    WHERE dd.warehouse = d.warehouse 
                ) AND d.warehouse IN ?";

        $result = $this->db->query($query, array($warehouse))->result();

        return $result;
    }

    public function getStatusByWarehouse($warehouse)
    {

        $query = "SELECT d.warehouse, d.fecha AS status_date, e.nombre AS name_status
              FROM detalle d
              INNER JOIN estatus e ON e.id_estatus = d.id_estatus
              WHERE  d.warehouse = ? ORDER BY d.fecha DESC";

        $result = $this->db->query($query, $warehouse)->result();

        return $result;
    }


    public function obtener_detalle_manifiesto($id_manifiesto, $limit = null, $offset = null)
    {
        $this->db->select('id, tracking_number, id_estatus, nombre, fecha, problema_gestion, mala_gestion');
        $this->db->from('imile_problem');
        $this->db->where('enviado_imile', 0);
        //$this->db->where('tracking_number', '6120525110806');
        //$this->db->where('manifiesto', $id_manifiesto);
        $this->db->order_by('tracking_number', 'ASC');
        $this->db->order_by('fecha', 'ASC');


        // Si se especifica limit y offset, los aplicamos
        if (!is_null($limit)) {
            $this->db->limit($limit, $offset ?? 0);
        }

        return $this->db->get()->result();
    }

    public function marcar_como_enviado($id, $response, $error, $payload)
    {
        $this->db->where('id', $id);
        //$this->db->where('response_imile', $response);
        //$this->db->where('error_imile', $error);
        return $this->db->update('imile_problem', ['enviado_imile' => 1, 'response_imile' => $response, 'error_imile' => $error, 'payload' => $payload]);
    }


    public function buscar_trackings($master, $bag_number)
    {
        $this->db->select('bag_number, tracking_number, pickup_number, internal_tracking');
        $this->db->from('manifiesto');
        $this->db->where('id_manifiesto', $master);
        // $this->db->where('bag_number', $bag_number);

        $this->db->order_by('pickup_number', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }

    public function obtener_bolsa($master, $bag_number,)
    {
        $query = $this->db
            ->select('id, bag_number, tracking_number, pickup_number, internal_tracking,
            tipo_entrega, 
            consignee_account, 
            consignee, 
            consignee_email, 
            consignee_address1,
            consignee_address2, 
            consignee_address3,
            consignee_phone,
            tipo_servicio,
            cobro_final,
            agencia_destino,
            departamento_id,
            municipio_id,
            gweight,
            commodity,
            iva,
            dai,estado,id_manifiesto')
            //->where('bag_number', $bag_number)
            ->where('id_manifiesto',  $master)
            ->where('pickup_number', 0)

            // ->where('estado',  "V")
            //  ->order_by('fecha', 'DESC')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    //toma el id de empresa y token para gestionar apis
    public function get_Idempresa($master)
    {
        $query = $this->db->select('client_id,tipo_factura')
            ->where('idpreclasificacion', $master)
            ->get('preclasificacion')
            ->result();
        return $query;
    }

    public function datos_empresa($id)
    {
        $query = $this->db->select('id, token_cliente, id_tokencliente, token')
            ->where('id', $id)
            ->get('empresa')
            ->result();
        return $query;
    }

    public function push_events($empresa)
    {
        $query = $this->db->select('id,tracking_number')
            ->where('id_empresa', $empresa)
            ->where('id >', 1425574)  // ✅ CORRECTO

            ->get('manifiesto')
            ->result();
        return $query;
    }
    public function get_events($tracking)
    {
        $query = $this->db->select('detalle.*, estatus.nombre as nombre_estatus')
            ->from('detalle')
            ->join('estatus', 'estatus.id_estatus = detalle.id_estatus', 'left')
            ->where('detalle.warehouse', $tracking)

            ->get()
            ->result();

        return $query;
    }

    public function get_events_error()
    {

        $sql = "
        WITH ranked AS (
          SELECT
            le.*,
            ROW_NUMBER() OVER (
              PARTITION BY le.tracking, le.id_estatus, le.entregado
              ORDER BY le.fecha ASC, le.id ASC
            ) AS rn
          FROM log_entregas le
          WHERE le.entregado = 'NO'
          and estado='entregado'
          and   respuesta like '%Tracking Not Found%'
            
            
            
            
           
            
            
        )
        SELECT r.*, e.nombre
        FROM ranked r
        JOIN estatus e ON e.id_estatus = r.id_estatus
        WHERE r.rn = 1
        ORDER BY r.tracking, r.id_estatus, r.fecha limit 500
    ";

        $query = $this->db->query($sql);
        return $query->result();
    }



    public function actualizar_respuesta($response, $tracking, $valor, $id_estatus)
    {
        $this->db
            ->set('respuesta', $response)
            ->set('entregado', $valor)
            ->where('tracking', $tracking)
            ->where('id_estatus', $id_estatus)
            ->update('log_entregas');
    }

    public function validate_events()
    {

        $this->db->select('*');

        $this->db->from('log_entregas');

        $this->db->where('log_entregas.estado', 'error');

        $query = $this->db->get();

        return $query->result();
    }

    public function get_validate_events($tracking, $estatus)
    {

        $this->db->select('*');

        $this->db->from('log_entregas');

        $this->db->where('tracking', $tracking);

        $this->db->where('id_estatus', $estatus);

        $this->db->where('entregado', "NO");


        $query = $this->db->get();

        return $query->result();
    }

    public function ok_events($tracking, $estatus)
    {
        $this->db
            ->set('entregado', "OK")
            ->where('tracking', $tracking)
            ->where('id_estatus', $estatus)
            ->update('log_entregas');
    }


    public function buscarPorDescripcion($descripcion)
    {
        // Usar el Query Builder de CodeIgniter para hacer la consulta
        $this->db->where('descripcion', $descripcion);
        $query = $this->db->get('estatusapg');

        // Si hay resultados, devolverlos como un array
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return null;
        }
    }



    public function buscarPorCodigo($codigo)
    {
        // Usar el Query Builder de CodeIgniter para hacer la consulta
        $this->db->where('codigo_interno', $codigo);
        $query = $this->db->get('estatusapg');

        // Si hay resultados, devolverlos como un array
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return null;
        }
    }

    private function _base_query($date_from = null, $date_to = null, $empresa_id = null)
    {
        $this->db->from('manifiesto m');
        $this->db->join('detalle_ultimo_estatus due', 'due.tracking_number = m.tracking_number', 'left');
        $this->db->join('estatus e', 'e.id_estatus = due.id_estatus', 'left');
        $this->db->join('empresa emp', 'emp.id = m.id_empresa', 'left'); // 👈 nombre empresa

        $this->db->select('
            m.tracking_number,
            m.fecha,
            due.id_estatus,
            e.nombre AS nombre_estatus,
            due.fecha_ultimo_estatus,
            emp.nombre AS empresa_nombre
        ');

        if ($date_from) $this->db->where('due.fecha_ultimo_estatus >=', $date_from);
        if ($date_to)   $this->db->where('due.fecha_ultimo_estatus <=', $date_to);

        if (!empty($empresa_id)) {
            // Si tu esquema guarda la empresa en DUE, usa: $this->db->where('due.id_empresa', $empresa_id);
            $this->db->where('m.id_empresa', (int)$empresa_id);
        }
    }

    private function _make_datatables_query($date_from = null, $date_to = null, $empresa_id = null)
    {
        $this->_base_query($date_from, $date_to, $empresa_id);

        $search = $this->input->post('search')['value'] ?? null;
        if (!empty($search) && mb_strlen($search) >= 3) {
            $this->db->group_start();
            foreach ($this->column_search as $i => $col) {
                $i === 0 ? $this->db->like($col, $search) : $this->db->or_like($col, $search);
            }
            $this->db->group_end();
        }

        $order = $this->input->post('order');
        if (!empty($order) && isset($order[0]['column'])) {
            $idx = (int)$order[0]['column'];
            $dir = (strtolower($order[0]['dir'] ?? 'desc') === 'asc') ? 'ASC' : 'DESC';
            if ($idx > 0 && $idx < count($this->column_order)) {
                $col = $this->column_order[$idx];
                if ($col) $this->db->order_by($col, $dir);
            } else {
                foreach ($this->default_order as $k => $v) $this->db->order_by($k, $v);
            }
        } else {
            foreach ($this->default_order as $k => $v) $this->db->order_by($k, $v);
        }
    }

    public function get_datatables($date_from = null, $date_to = null, $empresa_id = null)
    {
        $this->_make_datatables_query($date_from, $date_to, $empresa_id);
        $len   = (int)$this->input->post('length');
        $start = (int)$this->input->post('start');
        if ($len != -1) $this->db->limit($len, $start);
        return $this->db->get()->result();
    }

    public function count_filtered($date_from = null, $date_to = null, $empresa_id = null)
    {
        $this->_make_datatables_query($date_from, $date_to, $empresa_id);
        return $this->db->get()->num_rows();
    }

    public function count_all($date_from = null, $date_to = null, $empresa_id = null)
    {
        $this->_base_query($date_from, $date_to, $empresa_id);
        return $this->db->count_all_results();
    }

    public function get_empresas()
    {
        return $this->db->select('id, nombre')
            ->from('empresa')
            ->order_by('nombre', 'ASC')
            ->get()
            ->result();
    }

    public function guardar_rendimiento($data)
    {
        $this->db->insert('rendimiento_status', $data);
        return ($this->db->affected_rows() > 0);
    }

    public function verify_ma_pending()
    {
        return $this->db->select('tracking,id_estatus')
            ->from('control_facturacion')
            ->where('facturado', 0)
            ->where('tracking', 'RR041156002XX')
            //->limit(50) // This adds the LIMIT 10 clause
            ->get()
            ->result();
    }

    public function data_gt_x_tracking($track,$id_preclasificacion){
        return $this->db->select('tracking_number, package_number, flete_gt, fob_gt, seguro_gt, value')
            ->from('manifiesto')
            ->where('id_manifiesto', $id_preclasificacion)
            ->where('package_number', $track)
            ->get()
            ->row();
    }

    public function get_idpreclasificacion($manifiesto)
    {
        return $this->db->select('idpreclasificacion')
            ->from('preclasificacion')
            ->where('manifiesto', $manifiesto)
            ->get()
            ->row();
    }



    public function update_status_ma($tracking_number)
    {
        $query = $this->db
            ->set('facturado', 1)
            ->where('tracking', $tracking_number)
            ->update('control_facturacion');
        return $query;
    }

    public function ver_contenido_tracking($id)
    {
        $query = $this->db->select('*')
            ->from('detalle_items')
            ->where('id_tracking', $id)
            ->get()
            ->result();
        return $query;
    }

    public function obtener_resumen_referencias($id_manifiesto)
    {
        $this->db->select("
        m.referencia,
        COUNT(DISTINCT m.tracking_number) AS total_paquetes,

        SUM(CASE WHEN d.id_estatus = 216 THEN 1 ELSE 0 END) AS recibido_aduana,
        MAX(CASE WHEN d.id_estatus = 216 THEN d.fecha END) AS fecha_recibido,

        SUM(CASE WHEN d.id_estatus = 319 THEN 1 ELSE 0 END) AS pro_despacho_aduana,
        MAX(CASE WHEN d.id_estatus = 319 THEN d.fecha END) AS fecha_pro_despacho,

        SUM(CASE WHEN d.id_estatus = 287 THEN 1 ELSE 0 END) AS demorado_aduana,
        MAX(CASE WHEN d.id_estatus = 287 THEN d.fecha END) AS fecha_demorado,

        SUM(CASE WHEN d.id_estatus = 288 THEN 1 ELSE 0 END) AS liberado_aduana,
        MAX(CASE WHEN d.id_estatus = 288 THEN d.fecha END) AS fecha_liberado,

        SUM(CASE WHEN d.id_estatus = 316 THEN 1 ELSE 0 END) AS entregado_ultima_milla,
        MAX(CASE WHEN d.id_estatus = 316 THEN d.fecha END) AS fecha_entregado
    ", FALSE);

        $this->db->from('manifiesto m');

        $this->db->join(
            'detalle d',
            "d.warehouse = m.tracking_number
        AND d.fecha >= DATE_SUB(NOW(), INTERVAL 2 MONTH)
        AND d.id_estatus IN (216,287,288,316,319)",
            'left'
        );

        $this->db->where('m.id_manifiesto', $id_manifiesto);

        $this->db->group_by('m.referencia');

        return $this->db->get()->result();
    }


    public function export_chunk($date_from, $date_to, $empresa_id, $search = '', $limit = 5000, $offset = 0)
    {
        $this->db->from('manifiesto m');
        $this->db->join('preclasificacion pre', 'pre.idpreclasificacion = m.id_manifiesto');
        $this->db->join('detalle_ultimo_estatus due', 'due.tracking_number = m.tracking_number', 'left');
        $this->db->join('estatus e', 'e.id_estatus = due.id_estatus', 'left');
        $this->db->join('empresa emp', 'emp.id = m.id_empresa', 'left');

        $this->db->select('
        m.tracking_number,
        due.id_estatus,
        e.nombre AS nombre_estatus,
        due.fecha_ultimo_estatus,
        pre.fecha fecha_manifiesto, 
        emp.nombre AS empresa_nombre
    ');

        $this->db->where('m.id_empresa', (int)$empresa_id);
        $this->db->where('due.fecha_ultimo_estatus >=', $date_from);
        $this->db->where('due.fecha_ultimo_estatus <=', $date_to);

        //$this->db->where('pre.fecha >=', $date_from);
        //$this->db->where('pre.fecha <=', $date_to);

        // Opcional: respeta el search si lo envías
        if (!empty($search) && mb_strlen($search) >= 3) {
            $this->db->group_start();
            $this->db->like('m.tracking_number', $search);
            $this->db->or_like('e.nombre', $search);
            $this->db->or_like('emp.nombre', $search);
            $this->db->or_like('CAST(due.id_estatus AS CHAR)', $search, 'both', false);
            $this->db->group_end();
        }

        $this->db->order_by('due.fecha_ultimo_estatus', 'DESC');
        //$this->db->order_by('pre.fecha', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }


    //Code Misa
    public function obtener_tracking_bg($trackings)
    {
        if (empty($trackings)) return [];

        $bg_array = [];

        // Dividir en bloques de 500
        $chunks = array_chunk($trackings, 500);

        foreach ($chunks as $chunk) {
            $result = $this->db
                ->select('tracking_number, package_number')
                ->where_in('package_number', $chunk)
                ->get('manifiesto')
                ->result_array();

            foreach ($result as $row) {
                $bg_array[$row['package_number']] = $row['tracking_number'];
            }
        }

        return $bg_array;
    }

    public function obtener_documentostransporte_by_chep($chep)
    {

        $this->db->select('declaracionesdetalle.DocumentoTransporte');
        $this->db->from('declaracionesdetalle');
        $this->db->join('declaraciones', 'declaraciones.id = declaracionesdetalle.Declaracion', 'inner');
        $this->db->where('correlativo_duca', $chep);

        $query = $this->db->get();

        return $query->result();
    }

    public function payload_guia($master, $tracking_number, $payload)
    {


        $data = [
            'master' => $master,
            'tracking_number' => $tracking_number,
            'payload'         => $payload
        ];

        return $this->db->insert('payloads', $data);
    }

    public function asignar_pobladosbk() //funciona
    {

        $sql = "
        UPDATE doc_master d
        JOIN poblados p
        ON REPLACE(LOWER(d.direccion),' ','') 
            LIKE CONCAT('%', REPLACE(LOWER(p.nombre_poblado),' ',''), '%')
        SET d.codigo_poblado = p.codigo_poblado,
            d.nombre_poblado = p.nombre_poblado
        WHERE (d.codigo_poblado IS NULL OR TRIM(d.codigo_poblado) = '')
        ";

        $this->db->query($sql);

        return $this->db->affected_rows();
    }
    public function asignar_pobladosbk01()
    {

        $sql = "
            UPDATE doc_master d
            JOIN poblados p 
                ON REPLACE(LOWER(d.direccion),' ','') 
                LIKE CONCAT('%', REPLACE(LOWER(p.nombre_poblado),' ',''), '%')

            JOIN municipios m 
                ON m.id = p.id_municipio

            JOIN departamentos dep 
                ON dep.id = m.id_departamento

            SET 
                d.codigo_poblado = p.codigo_poblado,
                d.nombre_poblado = p.nombre_poblado,
                d.municipio = m.nombre_municipio,
                d.departamento = dep.nombre_depto

            WHERE 
                d.codigo_poblado IS NULL
                OR TRIM(d.codigo_poblado) = ''
            ";

        $this->db->query($sql);

        return $this->db->affected_rows();
    }

    public function asignar_poblados()
    {
        $registros = $this->db->query("
        SELECT id, direccion 
        FROM doc_master 
        WHERE codigo_poblado IS NULL 
           OR TRIM(codigo_poblado) = ''
          ")->result();

        $poblados = $this->db->get('poblados')->result();

        foreach ($registros as $row) {

            $direccion = $this->normalizar($row->direccion);

            $mejor = null;
            $mejor_score = 0;

            foreach ($poblados as $p) {

                $poblado_norm   = $this->normalizar($p->poblado);
                $municipio_raw  = strtolower($p->municipio);
                $municipio_norm = $this->normalizar($p->municipio);
                $depto_norm     = $this->normalizar($p->departamento);

                $score = 0;

                //  MATCH POBLADO
                if (strpos($direccion, $poblado_norm) !== false) {
                    $score += 100;
                }

                //  MATCH MUNICIPIO POR PALABRAS
                $match_muni = $this->contienePalabras($direccion, $municipio_raw);
                $score += ($match_muni * 60);

                //  BONUS SI COINCIDE COMPLETO EL MUNICIPIO
                if ($match_muni >= 2) {
                    $score += 80;
                }

                //  MATCH DEPARTAMENTO
                if (strpos($direccion, $depto_norm) !== false) {
                    $score += 100;
                }

                //  BONUS SI ES ZONA
                if (strpos($poblado_norm, 'zona') !== false && strpos($direccion, $poblado_norm) !== false) {
                    $score += 40;
                }

                //  LONGITUD (más específico gana)
                $score += strlen($poblado_norm);

                //  PENALIZAR MUNICIPIOS MUY CORTOS (genéricos)
                if (strlen($municipio_norm) < 8) {
                    $score -= 40;
                }

                if ($score > $mejor_score) {
                    $mejor = $p;
                    $mejor_score = $score;
                }
            }

            //  GUARDAR SI HAY UN MATCH DECENTE
            if ($mejor && $mejor_score >= 90) {

                $this->db->where('id', $row->id);
                $this->db->update('doc_master', [
                    'codigo_poblado' => $mejor->codigo_poblado,
                    'nombre_poblado' => $mejor->poblado,
                    'municipio' => $mejor->municipio,
                    'departamento' => $mejor->departamento,
                ]);
            }
        }

        return count($registros);
    }

    private function normalizar($texto)
    {
        $texto = strtolower($texto);

        // quitar acentos
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú'],
            ['a', 'e', 'i', 'o', 'u'],
            $texto
        );

        // zona 2 → zona02
        $texto = preg_replace_callback('/zona\s*(\d+)/', function ($m) {
            return 'zona' . str_pad($m[1], 2, '0', STR_PAD_LEFT);
        }, $texto);

        // quitar espacios
        $texto = str_replace(' ', '', $texto);

        return $texto;
    }


    private function contienePalabras($texto, $frase)
    {
        $palabras = explode(' ', strtolower($frase));

        $coincidencias = 0;

        foreach ($palabras as $p) {

            if (strlen($p) < 3) continue;

            if (strpos($texto, $this->normalizar($p)) !== false) {
                $coincidencias++;
            }
        }

        return $coincidencias;
    }

    public function healthcheck()
    {
        if ($this->db->simple_query("SELECT 1")) {
            echo json_encode([
                'status' => 'ok',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function obtener_tracking_bg_sv($tracking, $referencia)
    {
        $query = $this->db
            ->select('id, tracking_number, value, id_manifiesto')
            ->from('manifiesto')
            ->where('package_number', $tracking)
            ->where('referencia', $referencia)
            ->get()
            ->row();
        return $query;
    }

    public function obtener_master_ch_sv($id_manifiesto)
    {
        $query = $this->db
            ->select('manifiesto_tempo')
            ->from('preclasificacion')
            ->where('idpreclasificacion', $id_manifiesto)
            ->get()
            ->row();
        return $query;
    }

    public function actualizar_impuestos_sv($id, $impuestos)
    {
        $this->db
            ->set('impuestos', $impuestos)
            ->where('id', $id)
            ->update('manifiesto');
        return $this->db->affected_rows() > 0;
    }
    public function actualizar_impuestos_gt($package_number, $impuestos)
    {
        $this->db
            ->set('impuestos', $impuestos)
            ->where('package_number', $package_number)
            ->update('manifiesto');
        return $this->db->affected_rows() > 0;
    }

    public function actualizar_impuestos_hn($tracking, $impuestos)
    {
        $this->db
            ->set('impuestos', $impuestos)
            ->where('package_number', $tracking)
            ->update('manifiesto');
        return $this->db->affected_rows() > 0;
    }

    public function actualizar_impuestos_hn_by_batch($data)
    {
        $this->db->update_batch('manifiesto',$data,'package_number');
        return $this->db->affected_rows() > 0;
    }

    /*public function insertar_impuestos_hn_by_batch($data){
        $this->db->insert_batch('control_impuestos', $data);
        return $this->db->affected_rows() > 0;
    }*/

    public function ver_impuestos_pendientes()
    {
        $query = $this->db->select('*')
            ->from('control_impuestos')
            ->where('flag_modificado', 0)
            ->get()
            ->result();
        return $query;
    }

    public function insertar_impuestos_hn_by_batch($data)
    {
        if (!$this->db->insert_batch('control_impuestos', $data)) {
            log_message('error', 'Error al insertar impuestos DUCA: ' . json_encode($this->db->error()));
            return false;
        }

        return $this->db->affected_rows() > 0;
    }

    public function sincronizar_impuestos_hn()
    {
        $this->db->trans_start();

        $this->db->query(
            'UPDATE manifiesto AS m
             INNER JOIN control_impuestos AS c
                ON c.package_number = m.package_number
               AND c.id_manifiesto = m.id_manifiesto
             SET m.impuestos = c.total_impuestos'
        );

        $this->db->query(
            'DELETE c
             FROM control_impuestos AS c
             INNER JOIN manifiesto AS m
                ON c.package_number = m.package_number
               AND c.id_manifiesto = m.id_manifiesto'
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            log_message('error', 'Error al sincronizar impuestos DUCA con manifiesto.');
            return false;
        }

        return true;
    }

    public function obtener_id_manifiesto_por_chepito($chepito)
    {
        return $this->db
            ->select('id_manifiesto')
            ->from('referencia')
            ->where('correlativo_duca', $chepito)
            ->get()
            ->row();
    }

    public function obtener_empresa_temu($empresas)
    {
        $query = $this->db
            ->select('id, nombre')
            ->from('empresa')
            ->where_in('id', $empresas)
            ->get()
            ->result();

        return $query;
    }


    public function actualizar_pago_imp_temu($tracking_number, $pago_imp_temu)
    {
        $registro = $this->db
            ->select('impuestos')
            ->where('tracking_number', $tracking_number)
            ->get('manifiesto')
            ->row();

        if (!$registro) {
            return 0;
        }

        if ($registro->impuestos === null) {
            $this->db
                ->where('tracking_number', $tracking_number)
                ->update('manifiesto', [
                    'impuestos'       => $pago_imp_temu,
                    'pago_imp_temu'   => $pago_imp_temu
                ]);
        } else {
            $this->db
                ->where('tracking_number', $tracking_number)
                ->update('manifiesto', [
                    'pago_imp_temu' => $pago_imp_temu
                ]);
        }

        return $this->db->affected_rows();
    }

    public function existe_tracking_number($tracking_number)
    {
        return $this->db
            ->where('tracking_number', $tracking_number)
            ->count_all_results('manifiesto') > 0;
    }

    public function obtener_detalle_impuestos_temu($id_empresa, $estado, $master = null, $referencia = null)
    {
        $query = $this->db
            ->select('m.tracking_number, m.package_number, p.manifiesto_tempo, p.manifiesto, m.referencia, m.impuestos, m.pago_imp_temu')
            ->from('manifiesto AS m')
            ->join('preclasificacion AS p', 'p.idpreclasificacion = m.id_manifiesto')
            ->where('p.client_id', $id_empresa);

        if ($estado == 1) {
            $query->where('m.pago_imp_temu >', 0.00);
        } else {
            $query->where('m.impuestos >', 0);
            $query->where('m.pago_imp_temu', 0.00);
        }

        if (!empty($master)) {
            $query->where('p.manifiesto_tempo', $master);
        }

        if (!empty($referencia)) {
            $query->where('m.referencia', $referencia);
        }

        return $query->get()->result();
    }

    public function obtener_dashboard_impuestos_temu($id_empresa, $estado, $master = null, $referencia = null)
    {
        $query = $this->db
            ->select('
                COUNT(
                    CASE 
                        WHEN m.impuestos IS NOT NULL 
                            AND m.impuestos > 0 
                        THEN 1 
                    END
                ) AS total_paquetes_declarados,

                COALESCE(
                    SUM(
                        CASE 
                            WHEN m.impuestos IS NOT NULL 
                                AND m.impuestos > 0 
                            THEN m.impuestos 
                            ELSE 0 
                        END
                    ), 
                    0
                ) AS total_impuestos_declarados,

                COUNT(
                    CASE 
                        WHEN m.impuestos IS NOT NULL 
                            AND m.impuestos > 0 
                            AND m.pago_imp_temu > 0 
                        THEN 1 
                    END
                ) AS total_paquetes_pagados,

                COALESCE(
                    SUM(
                        CASE 
                            WHEN m.impuestos IS NOT NULL 
                                AND m.impuestos > 0 
                                AND m.pago_imp_temu > 0 
                            THEN m.pago_imp_temu 
                            ELSE 0 
                        END
                    ), 
                    0
                ) AS total_impuestos_pagados,

                COUNT(
                    CASE 
                        WHEN m.impuestos IS NOT NULL 
                            AND m.impuestos > 0 
                            AND (
                                m.pago_imp_temu IS NULL 
                                OR m.pago_imp_temu = 0
                            )
                        THEN 1 
                    END
                ) AS total_paquetes_pendientes,

                COALESCE(
                    SUM(
                        CASE 
                            WHEN m.impuestos IS NOT NULL 
                                AND m.impuestos > 0 
                                AND (
                                    m.pago_imp_temu IS NULL 
                                    OR m.pago_imp_temu = 0
                                )
                            THEN m.impuestos 
                            ELSE 0 
                        END
                    ), 
                    0
                ) AS total_impuestos_pendientes,

                COALESCE(
                    SUM(
                        CASE 
                            WHEN m.impuestos IS NOT NULL 
                                AND m.impuestos > 0 
                            THEN m.impuestos 
                            ELSE 0 
                        END
                    )
                    -
                    SUM(
                        CASE 
                            WHEN m.impuestos IS NOT NULL 
                                AND m.impuestos > 0 
                                AND m.pago_imp_temu > 0 
                            THEN m.pago_imp_temu 
                            ELSE 0 
                        END
                    ),
                    0
                ) AS diferencia_impuestos
            ')
            ->from('manifiesto AS m')
            ->join(
                'preclasificacion AS p',
                'p.idpreclasificacion = m.id_manifiesto'
            )
            ->where('p.client_id', $id_empresa);

        if (!empty($master)) {
            $query->where('p.manifiesto_tempo', $master);
        }

        if (!empty($referencia)) {
            $query->where('m.referencia', $referencia);
        }

        return $query->get()->row();
    }

    public function obtener_tracking_number_GT($package_number)
    {
        $query = $this->db
            ->select('tracking_number, fob_gt, flete_gt, seguro_gt')
            ->from('manifiesto')
            ->where('package_number', $package_number)
            ->get()
            ->row();

        return $query;
    }




    
}
