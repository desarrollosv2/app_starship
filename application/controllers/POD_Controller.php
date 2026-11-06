<?php
defined('BASEPATH') or exit('No direct script access allowed');


class POD_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('POD_Model');
        $this->load->library('Validation_form');
    }

    private function FechaFormateada($fechaRaw)
    {
        if (empty($fechaRaw) || $fechaRaw === '0000-00-00' || $fechaRaw === '0000-00-00 00:00:00') {
            return '';
        }

        try {
            $fecha = new DateTime($fechaRaw);
        } catch (Exception $e) {

            return '';
        }

        $fecha = new DateTime($fechaRaw);
        $dia = $fecha->format('j');
        $mes_num = $fecha->format('m');
        $anio = $fecha->format('Y');

        $meses = [
            '01' => 'enero',
            '02' => 'febrero',
            '03' => 'marzo',
            '04' => 'abril',
            '05' => 'mayo',
            '06' => 'junio',
            '07' => 'julio',
            '08' => 'agosto',
            '09' => 'septiembre',
            '10' => 'octubre',
            '11' => 'noviembre',
            '12' => 'diciembre'
        ];

        return "$dia de {$meses[$mes_num]} de $anio";
    }

    public function pod_principal()
    {

        $data['empresa'] = $this->POD_Model->lista_empresas();

        $data['proveedor_lm'] = $this->POD_Model->listar_provedores_lm();

        
        $this->load->view('pod/pod_principal', $data);
    }

    public function listado_pod()
    {
        $id_pais = $_SESSION["pais"];
        $data['lista'] = $this->POD_Model->lista_pod($id_pais);

        if ($_SESSION['pais'] == 2) {
            $this->load->view('pod/listado_pod_gt', $data);
        } else {
            $this->load->view('pod/listado_pod', $data);
        }
    }

    public function buscar_manifiesto_pod() //copiado
    {
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/

        $desde          = $this->input->post('fecha_desde');
        $hasta          = $this->input->post('fecha_hasta');
        $empresa        = $this->input->post('empresa');
        $proveedor_lm   = $this->input->post('proveedor_lm');
        $id_pais        = $_SESSION["pais"];

        $rules = [];

        $rules['fecha_desde']   = ['required'];
        $rules['fecha_hasta']   = ['required'];
        $rules['empresa']       = ['required'];
        $rules['proveedor_lm']       = ['required'];

        $this->validation_form->setRules($rules);
        $form_data = $this->input->post();

        if (!$this->validation_form->validate($form_data)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $this->validation_form->getErrors()
            ]);
            return;
        }

        $data['manifiestos'] = $this->POD_Model->buscar_manifiesto_pod($id_pais, $desde, $hasta, $empresa, $proveedor_lm);

        //var_dump($data['manifiestos'] );

        foreach ($data['manifiestos'] as &$manifiesto) {
            $totales = $this->POD_Model->obtener_totales_por_preclasificacion($manifiesto->idpreclasificacion);

            $manifiesto->total_cajas     = $totales->total_cajas ?? 0;
            $manifiesto->total_paquetes  = $totales->total_paquetes ?? 0;

            if ($_SESSION['pais'] == 2) {
                $estado_gt = $this->POD_Model->obtener_estado_pod_gt_busqueda($manifiesto->idpreclasificacion);

                $manifiesto->cajas_procesadas = $estado_gt->procesadas ?? 0;
                $manifiesto->cajas_pendientes = $estado_gt->pendientes ?? 0;
            } else {
                $estado = $this->POD_Model->obtener_estado_cajas_por_preclasificacion($manifiesto->idpreclasificacion);
                if ($estado && ($estado->procesadas > 0 || $estado->pendientes > 0)) {
                    $manifiesto->cajas_procesadas = $estado->procesadas;
                    $manifiesto->cajas_pendientes = $estado->pendientes;
                } else {

                    $manifiesto->cajas_procesadas = 0;
                    $manifiesto->cajas_pendientes = $manifiesto->total_cajas;
                }
            }
        }

        unset($manifiesto);
        $data['pilotos']     = $this->POD_Model->lista_pilotos($id_pais);
        $data['cliente']     = $empresa;

        $html = $this->load->view('pod/form_pod', $data, TRUE);

        echo json_encode(['status' => 'success', 'html'   => $html]);
    }


    public function crear_pod() //copiado
    {
        $id_usuario           = $_SESSION['user_id'];
        $piloto_input         = $this->input->post('piloto');
        $placa                = $this->input->post('placa');
        $marchamo             = $this->input->post('marchamo');
        $idpreclasificaciones = $this->input->post('idpreclasificaciones');
        $direccion            = $this->input->post('direccion');
        $id_pais              = $_SESSION["pais"];

        $rules = [
            'piloto'    => ['required'],
            'placa'     => ['required'],
            'marchamo'  => ['required'],
            'direccion' => ['required']
        ];

        $this->validation_form->setRules($rules);

        if (!$this->validation_form->validate($this->input->post())) {
            echo json_encode([
                'status' => 'error',
                'errors' => $this->validation_form->getErrors()
            ]);
            return;
        }

        if (is_numeric($piloto_input)) {
            $id_piloto = $piloto_input;
        } else {
            $id_piloto = $this->POD_Model->insertar_piloto(['nombre' => trim($piloto_input), 'interno' => 0, 'id_pais' => $id_pais]);
        }

        if ($_SESSION['pais'] == 2) {
            return $this->pod_guatemala($id_usuario, $id_piloto, $placa, $marchamo, $idpreclasificaciones, $direccion);
        }


        $estadoPod = $this->POD_Model->obtener_estado_pod($idpreclasificaciones);

        $ids_con_pod = [];
        $ids_sin_pod = [];

        foreach ($estadoPod as $row) {
            if ($row->estado_pod == 1) {
                $ids_con_pod[] = $row->idpreclasificacion;
            } else {
                $ids_sin_pod[] = $row->idpreclasificacion;
            }
        }

        $dataManifiesto = $this->POD_Model->obtener_data_manifiesto($idpreclasificaciones);


        $mapManifiesto = [];
        foreach ($dataManifiesto as $row) {
            $mapManifiesto[$row->idpreclasificacion] = [
                'client_id'       => $row->client_id,
                'id_proveedor_lm' => $row->id_proveedor_lm,
                'id_pais'         => $row->id_pais
            ];
        }

        $insert_data = [];

        foreach ($idpreclasificaciones as $idpre) {

            if (isset($mapManifiesto[$idpre])) {
                $cliente   = $mapManifiesto[$idpre]['client_id'];
                $proveedor = $mapManifiesto[$idpre]['id_proveedor_lm'];
                $id_pais   = $mapManifiesto[$idpre]['id_pais'];
            } else {
                $cliente   = null;
                $proveedor = null;
                $id_pais   = null;
            }

            $insert_data[] = [
                'fecha_entrega'      => date('Y-m-d'),
                'idpreclasificacion' => $idpre,
                'id_pais'            => $id_pais,
                'cliente_id'         => $cliente,
                'id_piloto'          => $id_piloto,
                'proveedor_lm'       => $proveedor,
                'placa'              => $placa,
                'marchamo'           => $marchamo,
                'direccion'          => $direccion,
                'id_usuario'         => $id_usuario
            ];
        }

        $this->db->trans_start();

        $ids_pod = $this->POD_Model->insertar_pod($insert_data);

        $mapPOD = array_combine($idpreclasificaciones, $ids_pod);

        $insert_detalle = [];

        if ($_SESSION['pais'] == 1) {
            if (!empty($ids_sin_pod)) {
                $detalle = $this->POD_Model->detalle_manifiesto($ids_sin_pod);

                foreach ($detalle as $row) {
                    $insert_detalle[] = [
                        'id_pod'             => $mapPOD[$row->idpreclasificacion],
                        'idpreclasificacion' => $row->idpreclasificacion,
                        'bag_number'         => $row->bag_number,
                        'numero_cajas'       => 1,
                        'numero_paquetes'    => $row->paquetes,
                        'estado'             => 1,
                        'observacion'        => null
                    ];
                }
            }
        } else if ($_SESSION['pais'] == 3) {
            foreach ($ids_sin_pod as $idpre) {

                $refs = $this->POD_Model->obtener_referencia($idpre);

                foreach ($refs as $ref) {

                    $detalle = $this->POD_Model->detalle_manifiesto_honduras($idpre, $ref->referencia);

                    foreach ($detalle as $row) {
                        $insert_detalle[] = [
                            'id_pod'             => $mapPOD[$idpre],
                            'idpreclasificacion' => $idpre,
                            'referencia'         => $row->referencia,
                            'bag_number'         => $row->bag_number,
                            'numero_cajas'       => $row->total_bag_number,
                            'numero_paquetes'    => $row->paquetes,
                            'total_items'        => $row->total_items,
                            'estado'             => 1,
                            'observacion'        => null
                        ];
                    }
                }
            }
        }

        if (!empty($ids_con_pod)) {
            $detalle_estado_2 = $this->POD_Model->obtener_bags_estado_2($ids_con_pod);

            foreach ($detalle_estado_2 as $row) {
                $insert_detalle[] = [
                    'id_pod'             => $mapPOD[$row->idpreclasificacion],
                    'idpreclasificacion' => $row->idpreclasificacion,
                    'referencia'         => $row->referencia,
                    'bag_number'         => $row->bag_number,
                    'numero_cajas'       => $row->numero_cajas,
                    'numero_paquetes'    => $row->numero_paquetes,
                    'total_items'        => $row->total_items,
                    'estado'             => 1,
                    'observacion'        => null
                ];
            }
        }

        /*echo '<pre>';
            print_r($detalle);
            echo '</pre>';
            exit;*/

        if (!empty($insert_detalle)) {
            $this->POD_Model->insertar_detalle_pod($insert_detalle);
        }


        if (!empty($ids_con_pod)) {
            $this->POD_Model->actualizar_bags_a_estado_3($ids_con_pod);
        }

        $idPods = array_unique(array_column($insert_detalle, 'id_pod'));

        foreach ($idPods as $id_pod) {
            $this->POD_Model->actualizar_totales_encabezado($id_pod);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode([
                'status' => 'errors',
                'message' => 'Error al guardar POD'
            ]);
            return;
        }

        $master = $this->POD_Model->obtener_master_pod($ids_pod);

        $html = $this->load->view('pod/detalle_manifiesto_pdo', ['master' => $master], TRUE);

        echo json_encode(['status'  => 'success', 'ids_pod' => $ids_pod, 'html' => $html]);
    }

    public function cargarEncabezadoPOD($id_pod)
    {
        $data['encabezado'] = $this->POD_Model->obtener_encabezado_pod($id_pod);
        $this->load->view('pod/encabezado_pod', $data);
    }

    public function cargarDetallePOD($id_pod)
    {
        $data['detalle'] = $this->POD_Model->obtener_detalle_pod($id_pod);
        /*echo '<pre>';
            print_r($data['detalle']);
            echo '</pre>';
            exit;*/
        $this->load->view('pod/detalle_pod', $data);
    }

    public function desglosePOD($id_pod)
    {
        $data['desglose'] = $this->POD_Model->obtener_encabezado_pod($id_pod);
        $this->load->view('pod/desglose_pod', $data);
    }

    public function excluirCaja()
    {
        $this->load->view('pod/excluir_caja');
    }

    public function guardar_exclusion_caja()
    {
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/

        $id_detalle = $this->input->post('id_detalle');
        $observacion = $this->input->post('observacion_caja_pod');
        $id_pod      = $this->input->post('id_pod');
        $id_pre      = $this->input->post('id_pre');
        $id_pais    = $_SESSION["pais"];

        $rules = ['observacion_caja_pod'   => ['required']];

        $this->validation_form->setRules($rules);

        if (!$this->validation_form->validate($this->input->post())) {
            echo json_encode([
                'status' => 'error',
                'errors' => $this->validation_form->getErrors()
            ]);
            return;
        }

        $res = $this->POD_Model->excluir_caja($id_detalle, $observacion);

        if ($res) {
            //$this->POD_Model->bandera_caja_pendiente($id_pre);
            $detalle = $this->POD_Model->seleccionar_detalle_pod($id_detalle);
            $totales_pod = $this->POD_Model->seleccionar_pod($id_pod);

            $new_cajas = $totales_pod->numero_cajas - $detalle->numero_cajas;
            $new_paquetes = $totales_pod->numero_paquetes - $detalle->numero_paquetes;

            $referencias = '';
            if ($_SESSION['pais'] == 3) {
                $referencias = $totales_pod->total_referencias - 1;
            }

            $result = $this->POD_Model->actualizar_totales_pod($id_pod, $new_cajas, $new_paquetes, $referencias);



            if ($_SESSION['pais'] == 1) {
                $bags_manifiesto = $this->POD_Model->contar_bag_manifiesto($id_pre);
                $bags_pod        = $this->POD_Model->contar_bag_detalle_pod_activos($id_pre);
            } else if ($_SESSION['pais'] == 3) {
                $bags_manifiesto = $this->POD_Model->contar_referencias_honduras($id_pre);
                $bags_pod        = $this->POD_Model->contar_refeencias_detalle_pod_activos_honduras($id_pre);
            }

            $bags_pod = $bags_pod ?? 0;

            //var_dump($bags_manifiesto);
            //echo "br";
            //var_dump($bags_pod);

            if ($bags_manifiesto > $bags_pod) {

                $this->POD_Model->actualizar_cajas_pendientes($id_pre, 1);
            } else {

                $this->POD_Model->actualizar_cajas_pendientes($id_pre, 2);
            }

            if ($result) {
                $msg = "Bag number excluida correctamente";
                echo json_encode(['status' => 'success', 'msg' => $msg]);
            } else {
                $msg = "Ocurrió un error al excluir la bag number";
                echo json_encode(['status' => 'false', 'msg' => $msg]);
            }
        }
    }

    public function incluirCaja()
    {
        $id_detalle = $this->input->post('id');
        $id_pod = $this->input->post('id_pod');
        $id_pre = $this->input->post('id_pre');

        $res = $this->POD_Model->incluir_caja($id_detalle);

        if ($res) {
            $detalle = $this->POD_Model->seleccionar_detalle_pod($id_detalle);
            $totales_pod = $this->POD_Model->seleccionar_pod($id_pod);

            $new_cajas = $totales_pod->numero_cajas + $detalle->numero_cajas;
            $new_paquetes = $totales_pod->numero_paquetes + $detalle->numero_paquetes;

            $referencias = '';
            if ($_SESSION['pais'] == 3) {
                $referencias = $totales_pod->total_referencias + 1;
            }

            $result = $this->POD_Model->actualizar_totales_pod($id_pod, $new_cajas, $new_paquetes, $referencias);

            /*$bags_manifiesto = $this->POD_Model->contar_bag_manifiesto($id_pre);
            $bags_pod        = $this->POD_Model->contar_bag_detalle_pod_activos($id_pre);*/

            if ($_SESSION['pais'] == 1) {
                $bags_manifiesto = $this->POD_Model->contar_bag_manifiesto($id_pre);
                $bags_pod        = $this->POD_Model->contar_bag_detalle_pod_activos($id_pre);
            } else if ($_SESSION['pais'] == 3) {
                $bags_manifiesto = $this->POD_Model->contar_referencias_honduras($id_pre);
                $bags_pod        = $this->POD_Model->contar_refeencias_detalle_pod_activos_honduras($id_pre);
            }

            $bags_pod = $bags_pod ?? 0;

            //var_dump($bags_manifiesto);
            //echo "br";
            //var_dump($bags_pod);

            if ($bags_manifiesto > $bags_pod) {

                $this->POD_Model->actualizar_cajas_pendientes($id_pre, 1);
            } else {

                $this->POD_Model->actualizar_cajas_pendientes($id_pre, 2);
            }


            if ($result) {
                $msg = "Bag number incluida correctamente";
                echo json_encode(['status' => 'success', 'msg' => $msg]);
            } else {
                $msg = "Ocurrió un error al incluir la bag number";
                echo json_encode(['status' => 'false', 'msg' => $msg]);
            }
        }
    }

    public function verCaja($id)
    {
        $data['detalle'] = $this->POD_Model->ver_comentario_reproceso($id);
        $this->load->view('pod/reproceso_pod', $data);
    }

    public function generar_pod()
    {
        $ids_pod = $this->input->post('ids_pod');

        if ($_SESSION['pais'] == 2) {
            return $this->generar_pod_gt($ids_pod);
        }

        if (empty($ids_pod) || !is_array($ids_pod)) {
            echo json_encode(['status' => 'error', 'msg' => 'No se recibieron las POD']);
            return;
        }

        $ids_pre = [];

        /*foreach ($ids_pod as $id_pod) {
            $data = $this->POD_Model->obtener_encabezado_pod($id_pod);

            if ($data) {
                $ids_pre[] = $data->idpreclasificacion;
            }

            $estatus = "Generada";
            $this->POD_Model->estatus_pod($id_pod, $estatus);
        }*/

        foreach ($ids_pod as $id_pod) {
            $data = $this->POD_Model->obtener_encabezado_pod($id_pod);

            if (!$data) {
                continue;
            }

            $id_pre = $data->idpreclasificacion;

            $ids_pre[] = $id_pre;

            $estatus = "Generada";
            $this->POD_Model->estatus_pod($id_pod, $estatus);

            $this->notificacion_pod_generada($id_pod, $id_pre);
        }

        $ids_pre = array_unique($ids_pre);

        foreach ($ids_pre as $id_pre) {

            if ($_SESSION['pais'] == 1) {
                $bags_manifiesto = $this->POD_Model->contar_bag_manifiesto($id_pre);
                $bags_pod        = $this->POD_Model->contar_bag_detalle_pod_activos($id_pre);
            } else if ($_SESSION['pais'] == 3) {
                $bags_manifiesto = $this->POD_Model->contar_referencias_honduras($id_pre);
                $bags_pod        = $this->POD_Model->contar_refeencias_detalle_pod_activos_honduras($id_pre);
            }


            $bags_pod = $bags_pod ?? 0;

            //var_dump($bags_manifiesto);
            //echo "br";
            //var_dump($bags_pod);

            if ($bags_manifiesto > $bags_pod) {

                $this->POD_Model->actualizar_cajas_pendientes($id_pre, 1);
            } else {

                $this->POD_Model->actualizar_cajas_pendientes($id_pre, 2);
            }
        }

        echo json_encode(['status' => 'success', 'msg' => 'POD Generada correctamente']);
    }

    public function ver_pdf($id_pod)
    {
        //ini_set("display_errors",1);

        $encabezado     = $this->POD_Model->obtener_encabezado_pod($id_pod);
        $idpre          = $encabezado->idpreclasificacion;
        $total_paquetes = $this->POD_Model->total_paquetes_manifiesto($idpre);
        $total_cajas    = $this->POD_Model->total_bags_manifiesto($idpre);
        $entregados     = $this->POD_Model->pod_entregadas($idpre);


        $paquetes_entregados    = max(0, $entregados->total_paquetes - $encabezado->numero_paquetes);
        $cajas_entregadas       = max(0, $entregados->total_cajas - $encabezado->numero_cajas);
        $paquetes_pendientes    = max(0, $total_paquetes - ($paquetes_entregados + $encabezado->numero_paquetes));
        $cajas_pendientes       = max(0, $total_cajas - ($cajas_entregadas + $encabezado->numero_cajas));


        if ($_SESSION['pais'] == 2) {


            $qr_url         = base_url("index.php/POD_Controller/descargar_csv_gt/" . $id_pod);

        } else {

            $qr_url         = base_url("index.php/POD_Controller/descargar_csv/" . $id_pod);

        }

        $data = [
            'fecha_entrega'         => $encabezado->fecha_entrega ?? '',
            'piloto'                => $encabezado->piloto ?? '',
            'placa'                 => $encabezado->placa ?? '',
            'marchamo'              => $encabezado->marchamo ?? '',
            'direccion'             => $encabezado->direccion ?? '',
            'proveedor'             => $encabezado->proveedor ?? '',
            'master'                => $encabezado->manifiesto ?? '',
            'master_cn'             => $encabezado->manifiesto_tempo ?? 'Pendiente',
            'total_paquetes_master' => $total_paquetes ?? '',
            'total_cajas_master'    => $total_cajas ?? '',
            'paquetes_actual'       => $encabezado->numero_paquetes ?? '',
            'cajas_actual'          => $encabezado->numero_cajas ?? '',
            'paquetes_pendientes'   => $paquetes_pendientes,
            'cajas_pendientes'      => $cajas_pendientes,
            'cajas_entregadas'      => $cajas_entregadas ?? '',
            'paquetes_entregados'   => $paquetes_entregados ?? '',
            'qr_url'                => $qr_url,
            'total_p'               => $total_paquetes,
            'total_c'               => $total_cajas,
        ];


        if($_SESSION["pais"] == 2){
            $data['detalle'] = $this->POD_Model->obtener_detalle_pod_gt($id_pod,$idpre);
            $this->load->library('Pod_GT_pdf');
            $pdf = $this->pod_gt_pdf;
        }else{
            $data['detalles'] = $this->POD_Model->obtener_detalle_pod_pdf($id_pod);
            
            $this->load->library('Pod_pdf');
            $pdf = $this->pod_pdf;
        }

        $pdf->setDatos($data);
        $pdf->AddPage();
        $pdf->SetMargins(15, 15);
        $pdf->Logo();
        $pdf->QR($data['qr_url']);
        $pdf->Encabezado();
        $pdf->Detalle();

        $pdf->Output('I', 'POD_' . $encabezado->manifiesto . '_' . $encabezado->fecha_entrega . '.pdf');
    }


    public function notificacion_pod_generada($id_pod, $idpre)
    {
        $data['encabezado']           = $this->POD_Model->obtener_encabezado_pod($id_pod);
        $data['total_paquetes']   = $this->POD_Model->total_paquetes_manifiesto($idpre);
        $data['total_cajas']         = $this->POD_Model->total_bags_manifiesto($idpre);
        $data['referencias']        = $this->POD_Model->total_referencias_manifiesto($idpre);

        $html = $this->load->view('pod/correo/nueva_pod_generada', $data, TRUE);
        $id_pais = $_SESSION["pais"];

        if ($id_pais == 1) {
            $subject = "HOJA DE POD EL SALVADOR GENERADA PARA LA MÁSTER: " . $data['encabezado']->manifiesto;
            $correos = 'coordinadorOperaciones@mistarship.com,auxiliardeoperaciones1@mistarship.com';
            $correos_cc = 'opsultimamilla1@mistarship.com,servicioalcliente7@mistarship.com,opsultimamilla3@mistarship.com,jpinto@mistarship.com';
        } else if ($id_pais == 2) {
            $subject = "HOJA DE POD GUATEMALA GENERADA PARA LA MÁSTER: " . $data['encabezado']->manifiesto;
            $correos = 'aduana69@c807.com';
            $correos_cc = 'aduana83@c807.com,aduana82@c807.com,jpinto@mistarship.com,servicioalcliente7@mistarship.com,opsultimamilla4@mistarship.com,opsultimamilla1@mistarship.com,opsultimamilla3@mistarship.com';
        } else {
            $subject = "HOJA DE POD HONDURAS GENERADA PARA LA MÁSTER: " . $data['encabezado']->manifiesto;
            $correos = 'coordserinthn@c807xpress.com';
            $correos_cc = 'opsultimamilla1@mistarship.com,servicioalcliente7@mistarship.com,opsultimamilla3@mistarship.com,jpinto@mistarship.com';
        }

       $dataJson = json_encode([
            'correos' => $correos,
            'cuerpo'  => $html,
            'asunto'  => $subject,
            'copias'  => $correos_cc
        ], JSON_UNESCAPED_UNICODE);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://stargates.site/grupo_erp/ventas/ReclamosController/sendmailsapi2',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataJson,
        CURLOPT_HTTPHEADER => array(
            'Cookie: PHPSESSID=2d15ca536c4db3e80954e6b5ae1e8605',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($dataJson)
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //echo $response;

    }


    public function descargar_csv($id_pod)
    {
        $encabezado = $this->POD_Model->obtener_encabezado_pod($id_pod);
        $detalles   = $this->POD_Model->obtener_detalle_pod_pdf($id_pod);
        $master = $encabezado->manifiesto;
        $master_cn = $encabezado->manifiesto_tempo;
        $fecha = $encabezado->fecha_entrega;

        if (empty($detalles)) {
            show_error('No hay datos para este POD');
            return;
        }

        $mostrarReferencia = false;

        foreach ($detalles as $d) {
            if (!empty($d->referencia)) {
                $mostrarReferencia = true;
                break;
            }
        }

        $filename = 'POD_' . $master . '_' . $fecha . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        if ($mostrarReferencia) {
            fputcsv($output, [
                'AWB (MIA-SAP): ' . ($master ?? '')
            ]);
        } else {
            fputcsv($output, [
                'AWB (MIA-SAL): ' . ($master ?? '')
            ]);
        }

        fputcsv($output, [
            'AWB (CN-MIA): ' . ($master_cn ?? 'Pendiente')
        ]);

        fputcsv($output, []);


        $header = [];

        if ($mostrarReferencia) {
            $header[] = 'Referencia';
        }

        $header[] = 'Bag Number';
        $header[] = 'Paquetes';

        fputcsv($output, $header);

        foreach ($detalles as $row) {
            $line = [];

            if ($mostrarReferencia) {
                $line[] = $row->referencia ?? '';
            }

            $line[] = $row->bag_number ?? '';
            $line[] = $row->numero_paquetes ?? 1;


            fputcsv($output, $line);
        }

        fclose($output);
        exit;
    }


    public function editar_pod($id_pod)
    {
        $id_pais = $_SESSION["pais"];
        $data['encabezado'] = $this->POD_Model->obtener_encabezado_pod($id_pod);        
        $data['pilotos']     = $this->POD_Model->lista_pilotos($id_pais);
        $this->load->view('pod/editar_pod', $data);
    }

    public function guardar_encabezado_pdo()
    {
        $id_pod         = $this->input->post('id_pod');
        $fecha_entrega  = $this->input->post('fecha_entrega');
        $piloto         = $this->input->post('piloto');
        $placa          = $this->input->post('placa');
        $marchamo       = $this->input->post('marchamo');
        $direccion      = $this->input->post('direccion');

        $rules = [
            'fecha_entrega'     => ['required'],
            'piloto'            => ['required'],
            'placa'             => ['required'],
            'marchamo'          => ['required'],
            'direccion'         => ['required']
        ];

        $this->validation_form->setRules($rules);

        if (!$this->validation_form->validate($this->input->post())) {
            echo json_encode([
                'status' => 'error',
                'errors' => $this->validation_form->getErrors()
            ]);
            return;
        }

        $id_pais = $_SESSION["pais"];

        if (is_numeric($piloto)) {
            $id_piloto = $piloto;
        } else {
            $id_piloto = $this->POD_Model->insertar_piloto(['nombre' => trim($piloto), 'interno' => 0, 'id_pais' => $id_pais]);
        }

        $data = [
            'fecha_entrega'     => $fecha_entrega,
            'id_piloto'         => $id_piloto,
            'placa'             => $placa,
            'marchamo'          => $marchamo,
            'direccion'         => $direccion
        ];

        $res = $this->POD_Model->actualizar_encabezado_pod($id_pod, $data);

        if ($res) {
            echo json_encode(['status' => 'success', 'msg' => 'Cambios realizados correctamente']);
        }
    }

    public function anular_pod()
    {
        $this->load->view('pod/anular_pod');
    }

    public function guardar_anulacion_pod()
    {
        $id_pod = $this->input->post('id_pod');
        $id_pre = $this->input->post('id_pre');
        $comentario = $this->input->post('comentario_anulacion_pod');

        $rules = ['comentario_anulacion_pod'   => ['required']];

        $this->validation_form->setRules($rules);

        if (!$this->validation_form->validate($this->input->post())) {
            echo json_encode([
                'status' => 'error',
                'errors' => $this->validation_form->getErrors()
            ]);
            return;
        }

        $res = $this->POD_Model->anular_pod($id_pod, $comentario);

        if ($res) {

            if ($_SESSION['pais'] == 2) {
                $gt_anular = $this->POD_Model->anular_pod_gt($id_pod,$id_pre);

                if ($gt_anular > 0) {
                    $conteo = $this->POD_Model->obtener_estado_pod_gt($id_pre);

                    if ($conteo['total'] == $conteo['con_pod']) {
                        $estado = 2;
                    } else if ($conteo['total'] > $conteo['con_pod']) {
                        $estado = 1;
                    }

                    $this->POD_Model->actualizar_cajas_pendientes($id_pre, $estado);

                    echo json_encode(['status' => 'success', 'msg' => 'POD anulada correctamente.']);
                    return;
                }else{
                    echo json_encode(['status' => 'success', 'msg' => 'POD anulada correctamente.']);
                    return;
                }


            }




            $result = $this->POD_Model->anular_bag_number($id_pod);



            if ($result) {
                /*$bags_manifiesto = (int) $this->POD_Model->contar_bag_manifiesto($id_pre);
                $bags_pod        = (int) $this->POD_Model->contar_bag_detalle_pod_activos($id_pre);*/

                if ($_SESSION['pais'] == 1) {
                    $bags_manifiesto = $this->POD_Model->contar_bag_manifiesto($id_pre);
                    $bags_pod        = $this->POD_Model->contar_bag_detalle_pod_activos($id_pre);
                } else if ($_SESSION['pais'] == 3) {
                    $bags_manifiesto = $this->POD_Model->contar_referencias_honduras($id_pre);
                    $bags_pod        = $this->POD_Model->contar_refeencias_detalle_pod_activos_honduras($id_pre);
                }

                $bags_pod = $bags_pod ?? 0;

                if ($bags_pod == 0) {
                    $estado = 0;
                } elseif ($bags_pod < $bags_manifiesto) {
                    $estado = 1;
                } else {
                    $estado = 2;
                }

                $this->POD_Model->actualizar_cajas_pendientes($id_pre, $estado);

                $this->POD_Model->actualizar_bags_a_estado_5($id_pre);

                echo json_encode(['status' => 'success', 'msg' => 'POD anulada correctamente.']);
            }
        }
    }

    public function subir_comprobante_pod()
    {
        $this->load->view('pod/cargar_comprobante_pod');
    }

    public function listar_comprobantes_pod($id)
    {
        $data['comprobantes'] = $this->POD_Model->listar_comprobantes_pod($id);
        $this->load->view('pod/lista_comprobantes_pod', $data);
    }

    public function guardar_comprobantes_pod()
    {
        $id_usuario         = $_SESSION['user_id'];
        $id_pod             = $this->input->post('id_pod');

        if (empty($_FILES['comprobante_pod']['name'][0])) {
            echo json_encode(['status' => 'error', 'msg' => 'No se recibieron archivos.']);
            return;
        }

        $destino = getcwd() . "/public/uploads/comprobantes_POD/";

        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }


        $comprobante     = $_FILES['comprobante_pod'];
        $total          = count($comprobante['name']);
        $subidos        = 0;

        for ($i = 0; $i < $total; $i++) {

            $nombre_original = $comprobante['name'][$i];
            $tmp = $comprobante['tmp_name'][$i];
            $tamanio = $comprobante['size'][$i];
            $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
            $tipo_archivo = mime_content_type($tmp);
            $nombre_nuevo = uniqid('Pod_') . '.' . $extension;
            $ubicacion      = $destino . $nombre_nuevo;

            if (move_uploaded_file($tmp, $ubicacion)) {
                $url_destino = "/public/uploads/comprobantes_POD/" . $nombre_nuevo;

                $data = [
                    'usuario_id'    => $id_usuario,
                    'id_pod'        => $id_pod,
                    'nombre_archivo' => $nombre_original,
                    'nombre_nuevo'   => $nombre_nuevo,
                    'url'            => $url_destino,
                    'tipo_archivo'   => $tipo_archivo,
                    'tamanio'        => $tamanio,
                ];

                $this->POD_Model->guardar_comprobantes_pod($data);
                $subidos++;
            }
        }

        $estatus = "Finalizada";
        $this->POD_Model->estatus_pod($id_pod, $estatus);

        if ($subidos === 1) {
            echo json_encode(['status' => 'success', 'msg' => "El comprobante se subio correctamente."]);
        } else if ($subidos > 1) {
            echo json_encode(['status' => 'success', 'msg' => "Se subieron {$subidos} comprobantes correctamente."]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Error al cargar los comprobantes.']);
        }
    }

    public function eliminar_comprobante_pod($id, $id_pod)
    {
        $res = $this->POD_Model->eliminar_comprobante_pod($id);

        if ($res) {
            $total = $this->POD_Model->contar_comprobantes_activos($id_pod);
            if ($total > 0) {
                $estatus = "Finalizada";
                $this->POD_Model->estatus_pod($id_pod, $estatus);
            } else {
                $estatus = "Generada";
                $this->POD_Model->estatus_pod($id_pod, $estatus);
            }
            echo json_encode(['status' => 'success', 'msg' => 'Comprobante eliminado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Ocurrió un error al eliminar el comprobante de entrega.']);
        }
    }


    public function pod_guatemala($id_usuario, $id_piloto, $placa, $marchamo, $idpreclasificaciones, $direccion)
    {
        $dataManifiesto = $this->POD_Model->obtener_data_manifiesto($idpreclasificaciones);

        $mapManifiesto = [];
        foreach ($dataManifiesto as $row) {
            $mapManifiesto[$row->idpreclasificacion] = [
                'client_id'       => $row->client_id,
                'id_proveedor_lm' => $row->id_proveedor_lm,
                'id_pais'         => $row->id_pais
            ];
        }

        $insert_data = [];

        foreach ($idpreclasificaciones as $idpre) {

            if (isset($mapManifiesto[$idpre])) {
                $cliente   = $mapManifiesto[$idpre]['client_id'];
                $proveedor = $mapManifiesto[$idpre]['id_proveedor_lm'];
                $id_pais   = $mapManifiesto[$idpre]['id_pais'];
            } else {
                $cliente   = null;
                $proveedor = null;
                $id_pais   = null;
            }

            $insert_data[] = [
                'fecha_entrega'      => date('Y-m-d'),
                'idpreclasificacion' => $idpre,
                'id_pais'            => $id_pais,
                'cliente_id'         => $cliente,
                'id_piloto'          => $id_piloto,
                'proveedor_lm'       => $proveedor,
                'placa'              => $placa,
                'marchamo'           => $marchamo,
                'direccion'          => $direccion,
                'id_usuario'         => $id_usuario
            ];
        }

        $this->db->trans_start();

        $ids_pod = $this->POD_Model->insertar_pod($insert_data);

        $mapPOD = array_combine($idpreclasificaciones, $ids_pod);

        $this->POD_Model->actualizar_pod_manifiesto_gt($mapPOD);

        foreach ($ids_pod as $id_pod) {
            $this->POD_Model->actualizar_totales_encabezado_gt($id_pod,$idpre);
            $estatus = "Generada";
            $this->POD_Model->estatus_pod($id_pod, $estatus);
        }

        foreach ($idpreclasificaciones as $id_pre) {
            $conteo = $this->POD_Model->obtener_estado_pod_gt($id_pre);

            if ($conteo['total'] == $conteo['con_pod']) {
                $estado = 2;
            } else if ($conteo['total'] > $conteo['con_pod']) {
                $estado = 1;
            }

            $this->POD_Model->actualizar_cajas_pendientes($id_pre, $estado);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'errors', 'message' => 'Error al guardar POD']);
            return;
        }

        $master = $this->POD_Model->obtener_master_pod($ids_pod);

        $html = $this->load->view('pod/detalle_manifiesto_gt_pdo', ['master' => $master], TRUE);

        echo json_encode(['status'  => 'success', 'ids_pod' => $ids_pod, 'html' => $html]);
    }

    public function desglosePOD_gt($id_pod)
    {
        $data['desglose'] = $this->POD_Model->obtener_encabezado_pod($id_pod);
        $this->load->view('pod/desglose_pod', $data);
    }

    public function cargarDetallePOD_gt($id_pod)
    {
        $encabezado = $this->POD_Model->obtener_encabezado_pod($id_pod);
        $data['detalle'] = $this->POD_Model->obtener_detalle_pod_gt($id_pod,$encabezado->idpreclasificacion);
        $data['id_pod'] = $id_pod;

        //var_dump($data['detalle']);
        $this->load->view('pod/detalle_pod_gt', $data);
    }

    public function verPaquetesGT($id_pre, $referencia, $bandera, $id_pod)
    {
        if ($bandera == 1) {
            //Traemos tracking con id_pod
            $detalle = $this->POD_Model->paquetes_gt_incluidos($id_pre, $referencia, $id_pod);
        } else {
            //Traemos tracking con id_pod
            $detalle = $this->POD_Model->paquetes_gt_excluidos($id_pre, $referencia);
        }

        $data['detalle'] = $detalle;

        $this->load->view('pod/detalle_paquetes_gt', $data);
    }

    public function form_paquetes_gt($id_pod)
    {
        $data['id_pod'] = $id_pod;
        $this->load->view('pod/form_paquetes_gt', $data);
    }

    public function paquetes_pod_gt_todo(){
        $id_pod     = $this->input->post('id_pod');

        
        $manifiesto = $this->POD_Model->obtener_idpreclasificacion($id_pod);

        $accion = 1;

        if (!$manifiesto) {
            echo json_encode(['status' => 'error', 'msg' => 'No se encontró el manifiesto.']);
            return;
        }

        $idpreclasificacion = $manifiesto->idpreclasificacion;

        $result = $this->POD_Model->paquetes_pod_gt_todo($idpreclasificacion, $id_pod);

        $this->POD_Model->actualizar_totales_encabezado_gt($id_pod, $idpreclasificacion);

        $msg = ((int)$accion === 1) ? "Se excluyeron todos los paquetes" : "Se incluyeron todos paquetes";

        echo json_encode(['status' => 'success', 'msg' => $msg]);

        $conteo = $this->POD_Model->obtener_estado_pod_gt($idpreclasificacion);

        if ($conteo['total'] == $conteo['con_pod']) {
            $estado = 2;
        } else if ($conteo['total'] > $conteo['con_pod']) {
            $estado = 1;
        }

        $this->POD_Model->actualizar_cajas_pendientes($idpreclasificacion, $estado);


    }

    public function paquetes_pod_gt()
    {
        $id_pod     = $this->input->post('id_pod');
        $paquetes   = $this->input->post('paquetes');
        $accion     = $this->input->post('accion');

        $manifiesto = $this->POD_Model->obtener_idpreclasificacion($id_pod);

        if (!$manifiesto) {
            echo json_encode(['status' => 'error', 'msg' => 'No se encontró el manifiesto.']);
            return;
        }

        $idpreclasificacion = $manifiesto->idpreclasificacion;

        $nuevo_id_pod = ($accion == 1) ? null : $id_pod;

        $trackings = preg_split('/\r\n|\r|\n/', trim($paquetes));

        $total = 0;

        foreach ($trackings as $tracking) {
            $tracking = trim($tracking);

            if ($tracking == '') {
                continue;
            }
            $result = $this->POD_Model->paquetes_pod_gt($idpreclasificacion, $tracking, $nuevo_id_pod);

            $total += $result['affected'];
        }

        $this->POD_Model->actualizar_totales_encabezado_gt($id_pod,$idpreclasificacion);

        $msg = ((int)$accion === 1) ? "Se excluyeron $total paquetes" : "Se incluyeron $total paquetes";

        echo json_encode(['status' => 'success', 'msg' => $msg]);

        $conteo = $this->POD_Model->obtener_estado_pod_gt($idpreclasificacion);

        if ($conteo['total'] == $conteo['con_pod']) {
            $estado = 2;
        } else if ($conteo['total'] > $conteo['con_pod']) {
            $estado = 1;
        }

        $this->POD_Model->actualizar_cajas_pendientes($idpreclasificacion, $estado);
    }

    public function generar_pod_gt($ids_pod)
    {

        if (empty($ids_pod) || !is_array($ids_pod)) {
            echo json_encode(['status' => 'error', 'msg' => 'No se recibieron las POD']);
            return;
        }

        $ids_pre = [];

        foreach ($ids_pod as $id_pod) {
            $data = $this->POD_Model->obtener_encabezado_pod($id_pod);

            if (!$data) {
                continue;
            }

            $id_pre = $data->idpreclasificacion;

            $ids_pre[] = $id_pre;
        }

        $ids_pre = array_unique($ids_pre);

        foreach ($ids_pre as $id_pre) {
            $conteo = $this->POD_Model->obtener_estado_pod_gt($id_pre);

            if ($conteo['total'] == $conteo['con_pod']) {
                $estado = 2;
            } else if ($conteo['total'] > $conteo['con_pod']) {
                $estado = 1;
            }

            $this->POD_Model->actualizar_cajas_pendientes($id_pre, $estado);
        }

        echo json_encode(['status' => 'success', 'msg' => 'POD Generada correctamente']);
    }

    public function descargar_csv_gt($id_pod)
    {
        $encabezado = $this->POD_Model->obtener_encabezado_pod($id_pod);
        $detalles   = $this->POD_Model->obtener_detalle_pod_gt_pdf($id_pod,$encabezado->idpreclasificacion);
        $master = $encabezado->manifiesto;
        $master_cn = $encabezado->manifiesto_tempo;
        $fecha = $encabezado->fecha_entrega;

        if (empty($detalles)) {
            show_error('No hay datos para este POD');
            return;
        }

        $filename = 'POD_' . $master . '_' . $fecha . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        $agrupados = [];

        foreach ($detalles as $detalle) {
            $agrupados[$detalle->referencia][] = $detalle->tracking_number;
        }

        fputcsv($output, [
            'AWB (MIA-GT): ' . ($master ?? '')
        ]);



        fputcsv($output, [
            'AWB (CN-MIA): ' . ($master_cn ?? 'Pendiente')
        ]);

        fputcsv($output, []);

        foreach ($agrupados as $referencia => $trackings) {
            // Encabezado de la referencia
            fputcsv($output, ['REFERENCIA: ' . $referencia]);

            // Encabezado de la tabla
            fputcsv($output, ['Tracking Number']);

            foreach ($trackings as $tracking) {
                fputcsv($output, [$tracking]);
            }

            // Línea en blanco entre referencias
            fputcsv($output, []);
        }



        fclose($output);
        exit;
    }
}
