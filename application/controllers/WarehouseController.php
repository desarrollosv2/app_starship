<?php

defined('BASEPATH') or exit('No direct script access allowed');

class WarehouseController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('PHPEXCEL/PHPExcel.php');
        $this->load->database();
        $this->load->model('Conf_model');
        $this->load->model('WarehouseModel');
        $this->load->model('SendModel');
        $this->load->model('Traspaso_model');
        $this->load->model('PreclasificacionModel');
        $this->load->model('Tasa_Cambio_Model');
        $this->load->library('token'); //ma token
        $this->load->model('WhModel');
        $this->load->helper('path');
        $this->load->library('ProductosLibraryMA');
        //session_start();
    }



    public function warehouse()
    {
        //  $this->datos['maste'] = $this->ClientesModel->guia_master();


        $this->load->view("warehouse/vista_warehouse", $this->datos);
    }

    public function consulta_warehouse()
    {


        $wh = $_POST['search'];
        $this->datos['wareh'] =  $this->WarehouseModel->buscar_warehouse($wh);
        //          var_dump($this->datos['wareh']);
        // $cliente = $this->datos['wareh'];
        // $nombre = $cliente[0]->consignee;
        // $array[] = array("nombre" => $nombre);
        // $this->datos['cliente'] = $array;


        $this->load->view('warehouse/consulta_wh_estatus', $this->datos);
    }


    public function clasifica()
    {
        $this->datos['estatus'] = $this->Conf_model->estatus();
        $this->load->view("warehouse/vista_clasifica", $this->datos);
    }

    public function cambiar_estatus()
    {
        $this->datos['estatus'] = $this->Conf_model->estatus();
        $this->load->view("warehouse/vista_cambiar_estatus", $this->datos);
    }

    public function guardar_estatus($warehouse, $estatus)
    {

        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        $token_cliente = null;
        $id_tokencliente = null;

        //verifica la empresa a la que pertenece la guia y verifica si usa API
        $request = $this->WhModel->get_empresa($warehouse);


        $emp = $request->id_empresa;

        $empresa =  $this->WhModel->datos_empresa($emp); // obtiene las crendeciales de la api del cliente


        $token_cliente = $empresa->token_cliente;
        $id_tokencliente = $empresa->id_tokencliente;

        $api = $empresa->api; //verifica si cliente tiene configurada apis


        $date = date('Y-m-d H:i:s');
        $fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $fecha_iso = $fecha_tmp->format(DateTime::ATOM);

        $data = array(
            'item' => 1,
            'fecha' => date('Y-m-d H:i:s'),
            'warehouse' => $warehouse,
            'id_estatus' => $estatus,
            'casillero' => 100,
            'retirado_por' =>  $_POST['n_retiro'],
            'id_pais' => $_SESSION['pais'],
            'fechaiso' => $fecha_iso
        );

        $date = "Date: " . date('Y-m-d H:i:s');

        $wh = $warehouse;
        $idestatus = $estatus;

        /*actualizar wh*/
        $this->Traspaso_model->actualizar_estatus($warehouse, $estatus);

        /*insert */
        // $this->PreclasificacionModel->guardar_detalle($data); qui originalmente
        $this->PreclasificacionModel->update_estatus($warehouse, $estatus);

        //bloque para actualizar en sistema MailAmericas

        //  $token_ma = $this->token_ma($token_cliente, $id_tokencliente); //obtiene el token
        if ($api == "S") {
            if ($emp == 100 || $emp == 101 || $emp == 102 || $emp == 108 || $emp == 109) {
                $this->PreclasificacionModel->guardar_detalle($data);
                $token_ma = $this->token->getToken($token_cliente, $id_tokencliente);

                $header = array(
                    'Host: tracking.mailamericas.com',
                    'Content-Type: application/json',
                    'Cache-Control: no-cache',
                    'Authorization: Bearer ' . $token_ma
                );


                $data = array(
                    "tracking" => $wh,
                    "date" =>  $fecha_iso,
                    "description" => urldecode($_POST['nombreestatus']),
                    "city" => "CCO",
                    "received_by" => "",
                    "relationship" => null,
                    "delivery_proof" => [
                        " "
                    ],

                    "gcs" => "",
                    "details" => ""
                );

                $url = "https://tracking.mailamericas.com/api/v1/providers/events";


                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                $resultStr = curl_exec($ch);

                if (curl_errno($ch)) {
                    $error_msg = curl_error($ch);
                }
                curl_close($ch);
                //  echo  $error_msg;
                //   echo $resultStr;

                $data = array(
                    "respuesta" => $resultStr,
                    "tracking_number" =>  $wh,
                    "descripcion" => urldecode($_POST['nombreestatus']),
                    "fecha" =>  date('Y-m-d H:i:s'),
                    "fechaiso" =>  $fecha_iso

                );

                $this->PreclasificacionModel->guardar_logma($data);
            }
        }

        if ($api == "S" && $emp == 107) {

            $this->load->library('ApiRequest');

            $response = $this->apirequest->enviarEstado(
                $warehouse,
                $estatus,
                "",
                "",
                date('Y-m-d H:i:s'),
                "",
                ""
            );
            echo $response;
        }
        if ($api == "S" && $emp == 103) {

            $this->load->library('ApiRequestImile');

            $response = $this->apirequestimile->enviarEstado(
                $warehouse,
                $estatus,
                "",
                "",
                date('Y-m-d H:i:s'),
                "",
                ""
            );
            echo $response;
        }

         if ($api == "S" && $emp == 110) {
            $this->PreclasificacionModel->guardar_detalle($data);

         }
    }


    public function guardar_estatus_RCD($tracking)
    {
        $empresa_id = $this->WhModel->get_empresa($tracking);
        $idEmpresa = $empresa_id->id_empresa;
        $empresa =  $this->WhModel->datos_empresa($idEmpresa); // obtiene las crendeciales de la api del cliente

        $token_cliente = $empresa->token_cliente;
        $id_tokencliente = $empresa->id_tokencliente;

        $date = date('Y-m-d H:i:s');
        $fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $fecha_iso = $fecha_tmp->format(DateTime::ATOM);

        $data = array(
            'item' => 1,
            'fecha' => date('Y-m-d H:i:s'),
            'warehouse' => $tracking,
            'id_estatus' => 297,
            'casillero' => 100,
            'retirado_por' => "NA",
            'id_pais' => $_SESSION['pais'],
            'fechaiso' => $fecha_iso
        );

        $date = "Date: " . date('Y-m-d H:i:s');


        $wh = $tracking;
        $idestatus = 297;
        if ($idEmpresa == 100) {
            /*actualizar wh*/
            $this->Traspaso_model->actualizar_estatus($wh, $idestatus);

            /*insert */
            $this->PreclasificacionModel->guardar_detalle($data);
            $this->PreclasificacionModel->update_estatus($wh, $idestatus);
            //  $token_ma = $this->token_ma(1,2); //obtiene el token
            $token_ma = $this->token->getToken($token_cliente, $id_tokencliente);

            $header = array(
                'Host: tracking.mailamericas.com',
                'Content-Type: application/json',
                'Cache-Control: no-cache',
                'Authorization: Bearer ' . $token_ma
            );

            $status_name = "Revisado en centro de distribución";
            $data = array(
                "tracking" => $tracking,
                "date" =>  $fecha_iso,
                "description" => $status_name,
                "city" => "CCO",
                "received_by" => "",
                "relationship" => null,
                "delivery_proof" => [
                    " "
                ],

                "gcs" => "",
                "details" => ""
            );


            //  var_dump($data);
            $url = "https://tracking.mailamericas.com/api/v1/providers/events";


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);

            $resultStr = curl_exec($ch);

            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
            }
            curl_close($ch);
            //  echo  $error_msg;
            //   echo $resultStr;

            $data = array(
                "respuesta" => $resultStr,
                "tracking_number" =>  $wh,
                "descripcion" => $status_name,
                "fecha" =>  date('Y-m-d H:i:s'),
                "fechaiso" =>  $fecha_iso

            );
            $this->PreclasificacionModel->guardar_logma($data);
        } //fin MA
        if ($idEmpresa == 107) {
            // Token (x-api-key)
            $token = '4a4da5bb348e648e51d84e24c8652d1284c31156';

            // Campos del cuerpo de la solicitud (CURLOPT_POSTFIELDS)
            $fields = [
                "guia" => $wh,
                "codigo" => "297",
                "estatus" => "Revisado en centro de distribución.",
                "observaciones" => null,
                "fecha" => date('Y-m-d H:i:s'),
                "latitud" => "13.699411",
                "longitud" => "-89.149612",
                "pod" => [] // Array vacío
            ];

            // Convertir los campos a JSON
            $jsonFields = json_encode($fields);

            // Inicializar cURL
            $curl = curl_init();

            // Configurar opciones de cURL
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://stargateserp.com/app_starship/ws/index.php/api/estadosApg', // URL de la API
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $jsonFields, // Campos en formato JSON
                CURLOPT_HTTPHEADER => [
                    'x-api-key: ' . $token, // Token en el header
                    'Content-Type: application/json'
                ],
            ]);

            // Ejecutar la solicitud y obtener la respuesta
            $response = curl_exec($curl);

            // Cerrar la conexión cURL
            curl_close($curl);

            // Mostrar la respuesta
            echo $response;
        }
    }

    public function verificar_warehouse($id)
    {
        $warehouse = $this->WarehouseModel->verificar_warehouse($id);

        if ($warehouse) {
            echo json_encode($warehouse);
        }
    }


    public function cargar_archivo()
    {
        $this->load->view("warehouse/cargar_archivo");
    }

    public function upload_file()
    {

        //  echo "nombre ".$_FILES["file"]["name"];
        if (isset($_FILES["file"]["name"])) {
            $destino = getcwd() . "/public/uploads/file/";

            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }

            $extension = explode(".", $_FILES["file"]["name"]);
            $nombre = time() . "-plantilla." . $extension[1];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destino . "/" . $nombre)) {
                $link = $destino . "/" . $nombre;
            } else {
            }

            $object = PHPExcel_IOFactory::load($link);

            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();

                for ($row = 2; $row <= $highestRow; $row++) {


                    $mawb                   = $worksheet->getCellByColumnAndRow(0,  $row)->getValue();
                    $bag_number             = $worksheet->getCellByColumnAndRow(1,  $row)->getValue();
                    $etd                    = $worksheet->getCellByColumnAndRow(2,  $row)->getValue();
                    $eta                    = $worksheet->getCellByColumnAndRow(3,  $row)->getValue();
                    $order_number           = $worksheet->getCellByColumnAndRow(4,  $row)->getValue();
                    $tracking_number        = $worksheet->getCellByColumnAndRow(5,  $row)->getValue();
                    $origin                 = $worksheet->getCellByColumnAndRow(6,  $row)->getValue();
                    $destination            = $worksheet->getCellByColumnAndRow(7,  $row)->getValue();
                    $consignee_account      = $worksheet->getCellByColumnAndRow(8,  $row)->getValue();
                    $consignee              = $worksheet->getCellByColumnAndRow(9,  $row)->getValue();
                    $consignee_address1     = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $consignee_address2     = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    $consignee_address3     = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                    $consignee_neighborhood = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    $consignee_city         = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                    $consignee_state        = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                    $consignee_zip          = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                    $consignee_country      = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    $consignee_email        = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                    $consignee_phone        = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                    $consignee_mobile       = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                    $consignee_taxid        = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
                    $pieces                 = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
                    $gweight                = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
                    $cweight                = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
                    $weight_type            = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
                    $height                 = $worksheet->getCellByColumnAndRow(26, $row)->getValue();
                    $length                 = $worksheet->getCellByColumnAndRow(27, $row)->getValue();
                    $width                  = $worksheet->getCellByColumnAndRow(28, $row)->getValue();
                    $commodity              = $worksheet->getCellByColumnAndRow(29, $row)->getValue();
                    $value                  = $worksheet->getCellByColumnAndRow(30, $row)->getValue();
                    $freight                = $worksheet->getCellByColumnAndRow(31, $row)->getValue();
                    $currency               = $worksheet->getCellByColumnAndRow(32, $row)->getValue();
                    $service_type           = $worksheet->getCellByColumnAndRow(33, $row)->getValue();
                    $service_level          = $worksheet->getCellByColumnAndRow(34, $row)->getValue();
                    $shipper_account        = $worksheet->getCellByColumnAndRow(35, $row)->getValue();
                    $shipper_name           = $worksheet->getCellByColumnAndRow(36, $row)->getValue();
                    $shipper_address1       = $worksheet->getCellByColumnAndRow(37, $row)->getValue();
                    $shipper_address2       = $worksheet->getCellByColumnAndRow(38, $row)->getValue();
                    $shipper_city           = $worksheet->getCellByColumnAndRow(39, $row)->getValue();
                    $shipper_state          = $worksheet->getCellByColumnAndRow(40, $row)->getValue();
                    $shipper_zip            = $worksheet->getCellByColumnAndRow(41, $row)->getValue();
                    $shipper_country        = $worksheet->getCellByColumnAndRow(42, $row)->getValue();
                    $shipper_email          = $worksheet->getCellByColumnAndRow(43, $row)->getValue();
                    $shipper_phone          = $worksheet->getCellByColumnAndRow(44, $row)->getValue();

                    $rate['tarifa'] = $this->WarehouseModel->tarifas(); //toma el valor de la guia para evaluacion de acuerdo al rango de tarifa (tabla tarifa)
                    //  var_dump( $rate['tarifa']);
                    $tarifa = 0;
                    foreach ($rate['tarifa'] as $f) {

                        if ($tarifa == 0) {
                            if ($value >= $f->desde && $value <= $f->hasta) {
                                $tarifa = $f->tarifa;
                            }
                        }
                    }



                    $data = array(
                        'mawb'                   => $mawb,
                        'bag_number'             => $bag_number,
                        'etd'                    => $etd,
                        'eta'                    => $eta,
                        'order_number'           => $order_number,
                        'tracking_number'        => $tracking_number,
                        'origin'                 => $origin,
                        'destination'            => $destination,
                        'consignee_account'      => $consignee_account,
                        'consignee'              => $consignee,
                        'consignee_address1'     => $consignee_address1,
                        'consignee_address2'     => $consignee_address2,
                        'consignee_address3'     => $consignee_address3,
                        'consignee_neighborhood' => $consignee_neighborhood,
                        'consignee_city'         => $consignee_city,
                        'consignee_state'        => $consignee_state,
                        'consignee_zip'          => $consignee_zip,
                        'consignee_country'      => $consignee_country,
                        'consignee_email'        => $consignee_email,
                        'consignee_phone'        => $consignee_phone,
                        'consignee_mobile'       => $consignee_mobile,
                        'consignee_taxid'        => $consignee_taxid,
                        'pieces'                 => $pieces,
                        'gweight'                => $gweight,
                        'cweight'                => $cweight,
                        'weight_type'            => $weight_type,
                        'height'                 => $height,
                        'length'                 => $length,
                        'width'                  => $width,
                        'commodity'              => $commodity,
                        'value'                  => $value,
                        'freight'                => $freight,
                        'currency'               => $currency,
                        'service_type'           => $service_type,
                        'service_level'          => $service_level,
                        'shipper_account'        => $shipper_account,
                        'shipper_name'           => $shipper_name,
                        'shipper_address1'       => $shipper_address1,
                        'shipper_address2'       => $shipper_address2,
                        'shipper_city'           => $shipper_city,
                        'shipper_state'          => $shipper_state,
                        'shipper_zip'            => $shipper_zip,
                        'shipper_country'        => $shipper_country,
                        'shipper_email'          => $shipper_email,
                        'shipper_phone'          => $shipper_phone,
                        'id_pais'                => '2',
                        'tarifa'                 => $tarifa
                    );

                    $this->WarehouseModel->insertar($data);


                    $id = null;
                    $data = array(
                        'manifiesto' => $mawb,
                        'referencia' => "1001",
                        'numero_warehouse' => $order_number,
                        'cajas' => $pieces,
                        'casillero' => $consignee_account,
                        'id_pais' => 2,

                    );


                    $this->PreclasificacionModel->guardar_guia($data);




                    $data = array(
                        'manifiesto' => $mawb,
                        'fecha' => date("Y-m-d H:i:s"),
                        'descripcion' => "Escriba una dirección aqui",
                        'referencia' => "Referencia Aqui",
                        'paquetes' => "1",
                        'sacos' => "1",
                        'tipo' => "GMA",
                        'id_pais' => 2,
                    );
                    $this->PreclasificacionModel->guardar_manifiesto($id, $data);
                }
            }
        }
    }




    public function upload_file_facturacion()
    {

        //  echo "nombre ".$_FILES["file"]["name"];
        if (isset($_FILES["file"]["name"])) {
            $destino = getcwd() . "/public/uploads/file/";

            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }

            $extension = explode(".", $_FILES["file"]["name"]);
            $nombre = time() . "-plantilla." . $extension[1];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destino . "/" . $nombre)) {
                $link = $destino . "/" . $nombre;
            } else {
            }


            $object = PHPExcel_IOFactory::load($link);
            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();


                for ($row = 1; $row <= $highestRow; $row++) {

                    $guia            = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $codcliente      = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $nombre          = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $montofactura    = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $piezas          = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $ret_en_adn      = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    $peso_neto       = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $peso_volumen    = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    $pesotomar       = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    $codtarifa       = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    $cargobasico     = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $corte           = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    $manejo_trans    = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                    $descuento       = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    $sed             = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                    $seguro          = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                    $entrega_local   = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                    $tramite_aduanal = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    $rayosx          = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                    $iva_vta_propia  = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                    $imp             = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                    $cepa            = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
                    $total           = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
                    $ot              = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
                    $poliza          = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
                    $permiso         = $worksheet->getCellByColumnAndRow(25, $row)->getValue();

                    $data = array(
                        'guia'            => $guia,
                        'codcliente'      => $codcliente,
                        'nombre'          => $nombre,
                        'montofactura'    => $montofactura,
                        'piezas'          => $piezas,
                        'ret_en_adn'      => $ret_en_adn,
                        'pesoneto'       => $peso_neto,
                        'pesovolumen'    => $peso_volumen,
                        'pesotomar'       => $pesotomar,
                        'codtarifa'       => $codtarifa,
                        'cargobasico'     => $cargobasico,
                        'corte'           => $corte,
                        'manejo_trans'    => $manejo_trans,
                        'descuento'       => $descuento,
                        'sed'             => $sed,
                        'seguro'          => $seguro,
                        'entrega_local'   => $entrega_local,
                        'tramite_aduanal' => $tramite_aduanal,
                        'rayosx'          => $rayosx,
                        'iva_vta_propia'  => $iva_vta_propia,
                        'imp'             => $imp,
                        'cepa'            => $cepa,
                        'total'           => $total,
                        'ot'              => $ot,
                        'poliza'          => $poliza,
                        'permiso'         => $permiso

                    );

                    $this->WarehouseModel->insertar_facturacion($data);
                    $this->WarehouseModel->update_precio_paquete($guia, $total);
                }
            }
        }
    }

    //  funcion  para  hace un update  de precios  en los paquetes  02-10-2023
    public function upload_fileWH($idpreclasificacion)
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        //ECHO   'vALORE  RECIBIDO N PRECLASIFICACION' . $idpreclasificacion;

        //  echo "nombre ".$_FILES["file"]["name"];
        if (isset($_FILES["file"]["name"])) {
            $destino = getcwd() . "/public/uploads/file/";

            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }

            $extension = explode(".", $_FILES["file"]["name"]);
            $nombre = time() . "-plantilla." . $extension[1];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destino . "/" . $nombre)) {
                $link = $destino . "/" . $nombre;
            } else {
            }

            $object = PHPExcel_IOFactory::load($link);

            foreach ($object->getWorksheetIterator() as $worksheet) {

                $DAI                    = 0;
                $IVA                    = 0;
                $MANEJO                    = 0;
                $comision                    = 0;
                $Cobro_final            = 0;

                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $tracking_number = '';
                for ($row = 2; $row <= $highestRow; $row++) {
                    $tracking_number        = $worksheet->getCellByColumnAndRow(0,  $row)->getValue();
                    // $Value                  = $worksheet->getCellByColumnAndRow(7,  $row)->getValue();
                    $DAI                    = $worksheet->getCellByColumnAndRow(7,  $row)->getValue();
                    $IVA                    = $worksheet->getCellByColumnAndRow(8,  $row)->getValue();
                    $total_imp              = $worksheet->getCellByColumnAndRow(9,  $row)->getValue();
                    $comision               = $worksheet->getCellByColumnAndRow(10,  $row)->getValue();
                    $MANEJO                    = $worksheet->getCellByColumnAndRow(11,  $row)->getValue();

                    $total_ivag    = $worksheet->getCellByColumnAndRow(14,  $row)->getValue();;
                    $Cobro_final            = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                    $almacenaje            = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                    $sel            = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    if ($tracking_number != '') {
                        //echo   'los valores obtenidos son ' .  $tracking_number . ' ' . $Value .   ' ' .$DAI . ' ' .$MANEJO .' '. $Cobro_final .'<br>';

                        $this->WhModel->update_dm2($idpreclasificacion, $tracking_number, $DAI, $IVA, $comision, $MANEJO, $Cobro_final, $total_imp, $total_ivag, $almacenaje, $sel);
                    } else {
                        exit;
                    }
                }
            }
        }
    }

    public function cargar_archivoMF()
    {
        $this->load->view("warehouse/cargar_archivoMF");
    }

    //  funcion para  cargar la modal  que obtendra los  datos para  actalizar el  DUI
    public function cargar_archivoDUI()
    {
        $this->load->view("warehouse/cargar_archivoDui");
    }

    //  funcion  para  hace un update  de DUI  en los paquetes  12-10-2023
    public function upload_fileDUI($idpreclasificacion)
    {
        //ECHO   'vALORE  RECIBIDO N PRECLASIFICACION' . $idpreclasificacion;

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);


        //  echo "nombre ".$_FILES["file"]["name"];
        if (isset($_FILES["file"]["name"])) {
            $destino = getcwd() . "/public/uploads/file/";

            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }

            $extension = explode(".", $_FILES["file"]["name"]);
            $nombre = time() . "-plantilla." . $extension[1];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destino . "/" . $nombre)) {
                $link = $destino . "/" . $nombre;
            } else {
            }

            $object = PHPExcel_IOFactory::load($link);

            foreach ($object->getWorksheetIterator() as $worksheet) {

                $DAI                    = 0;
                $IVA                    = 0;
                $MANEJO                    = 0;
                $Cobro_final            = 0;
                $DUI                    = '';

                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $tracking_number = '';
                for ($row = 2; $row <= $highestRow; $row++) {
                    //echo 'recorriendfo el ciclo';
                    $tracking_number        = $worksheet->getCellByColumnAndRow(1,  $row)->getValue();
                    // $DUI                  = $worksheet->getCellByColumnAndRow(17,  $row)->getValue();
                    $DUI                  = $worksheet->getCellByColumnAndRow(2,  $row)->getValue();
                    //$tracking_number1                  = trim($tracking_number);
                    //$DAI                    = $worksheet->getCellByColumnAndRow(7,  $row)->getValue();
                    //$IVA                    = $worksheet->getCellByColumnAndRow(8,  $row)->getValue();
                    //$MANEJO	                = $worksheet->getCellByColumnAndRow(9,  $row)->getValue();
                    //$Cobro_final            = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    // echo   'los valores obtenidos son ' .  $tracking_number . ' ' . $DUI  .'<br>';

                    if ($tracking_number != '') {
                        //echo   'los valores obtenidos son ' .  $tracking_number1 . ' ' . $DUI  .'<br>';

                        $this->WhModel->update_DUI($idpreclasificacion, $tracking_number, $DUI);
                    } else {
                        exit;
                    }
                }
            }
        }
    }

    public function reemplazar_tracking($id, $tracking_number, $tracking_replace)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $fecha_creacion = date('Y-m-d H:i:s');
        $data = array(
            'fecha_creacion'     =>  $fecha_creacion,
            'tracking_number'    =>  $tracking_number,
            'tracking_replace'   =>  $tracking_replace,
            'id_item'            =>  $id,

        );
        $this->WarehouseModel->reemplazar_tracking($data, $tracking_number);
        $this->WarehouseModel->actualizar_tracking($tracking_number, $tracking_replace);
    }

    public function historial($tracking_number)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);


        $this->datos['historial'] = $this->WarehouseModel->historial($tracking_number);
        $this->load->view("preclasifica/historial", $this->datos);
    }


    public function cargar_referencia()
    {
        $this->load->view("awb/cargar_referencia");
    }

    public function upload_file_referencia($idpreclasificacion)
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //  echo "nombre ".$_FILES["file"]["name"];
        if (isset($_FILES["file"]["name"])) {


            $destino = getcwd() . "/public/uploads/file/";

            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }

            $extension = explode(".", $_FILES["file"]["name"]);
            $nombre = time() . "-plantilla." . $extension[1];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destino . "/" . $nombre)) {
                $link = $destino . "/" . $nombre;
            } else {
            }

            $object = PHPExcel_IOFactory::load($link);

            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();


                for ($row = 2; $row <= $highestRow; $row++) {
                    $awb                = $worksheet->getCellByColumnAndRow(0,  $row)->getValue(); // Master
                    $tracking_number    = $worksheet->getCellByColumnAndRow(1,  $row)->getValue(); // Tracking
                    $referencia         = $worksheet->getCellByColumnAndRow(2,  $row)->getValue();
                    $invoice            = $worksheet->getCellByColumnAndRow(3,  $row)->getValue();
                    $consignee_id       = $worksheet->getCellByColumnAndRow(4,  $row)->getValue();
                    $consignee          = $worksheet->getCellByColumnAndRow(5,  $row)->getValue();
                    $phone              = $worksheet->getCellByColumnAndRow(6,  $row)->getValue();
                    $email              = $worksheet->getCellByColumnAndRow(7,  $row)->getValue(); // Referencia
                    //$referencia         = $worksheet->getCellByColumnAndRow(26,  $row)->getValue();
                    $id_manifiesto      = $idpreclasificacion;

                    $data = array(
                        'awb'               => $awb,
                        'tracking_number'   => $tracking_number,
                        'referencia'        => $referencia,
                        'id_manifiesto'     => $id_manifiesto,
                    );
                    // var_dump($data);
                    $this->WarehouseModel->guardar_referencia($awb, $tracking_number, $referencia, $id_manifiesto);
                }
            }

            if ($_SESSION['pais'] == 2) {

                $datos = array(
                    array(
                        "id_manifiesto" => $idpreclasificacion,
                        "referencia" => "ROJO",
                        "paquetes" => 0,
                        "sacos" => 0,
                    ),
                    array(
                        "id_manifiesto" => $idpreclasificacion,
                        "referencia" => "VERDE",
                        "paquetes" => 0,
                        "sacos" => 0,
                    )
                );
                $this->WhModel->crear_referencia($datos);
            }
        }
    }

    public function lista_sobrantes()
    {
        $this->load->view("awb/lista");
    }

    public function uploadUpdateManifiesto($idpreclasificacion)
    {


        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $opt_selected = $_POST["opciones"];

        if (isset($_FILES["fileUpload"]["name"])) {

            $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

            if (in_array($_FILES['fileUpload']['type'], $csvMimes)) {
                //echo "File is Allowed";
            } else {
                echo "type-error";
            }
        }

        $fila = 0;

        $results = "";


        if (($gestor = fopen($_FILES['fileUpload']['tmp_name'], "r")) !== FALSE) {


            while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {

                $numero = count($datos);

                $fila++;

                if ($fila > 1) {

                    foreach ($datos as $k => $v) {
                        $encoding = mb_detect_encoding($v, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                        if ($encoding !== 'UTF-8') {
                            $datos[$k] = mb_convert_encoding($v, 'UTF-8', $encoding);
                        }
                    }

                    $tracking_number = $datos[0];


                    //telefonos columna b
                    //correos columna c
                    //dui column d
                    //nombre columna e
                    $consignee_phone                = $datos[1];
                    $consignee_email                = $datos[2];
                    $consignee_account              = $datos[3];
                    $consignee                      = $datos[4];
                    $consignee_address1 = $datos[5];
                    $departamento = $datos[6];
                    $municipio = $datos[7];
                    $manejo = $datos[8];
                    $id_manifiesto = $idpreclasificacion; //id manifiesto

                    /*$datas = array(
                            'id_manifiesto'          =>       $id_manifiesto,
                            'tracking_number'        =>       $tracking_number,
                            'gweight'                =>       $gweight,
                            'value'                  =>       $value,
                            'items'                  =>       $items,
                            'commodity'              =>       $commodity,
                            'consignee_account'      =>       $consignee_account,
                            'consignee'              =>       $consignee,
                            'buyer_company'          =>       $buyer_company,
                            'consignee_address1'     =>       $consignee_address1,
                            'buyer_address1_number'  =>       $buyer_address1_number,
                            'consignee_address2'     =>       $consignee_address2,
                            'consignee_address3'     =>       $consignee_address3,
                            'buyer_district'         =>       $buyer_district,
                            'consignee_city'         =>       $consignee_city,
                            'consignee_state'        =>       $consignee_state,
                            'consignee_country'      =>       $consignee_country,
                            'consignee_zip'          =>       $consignee_zip,
                            'consignee_phone'        =>       $consignee_phone,
                            'consignee_email'        =>       $consignee_email,
                            'hts'                    =>       $hts,
                            'id_pais'                =>       $id_pais,
                            'pieces'                 =>       $pieces,
                            'departamento'           =>       $departamento,
                            'municipio'              =>       $municipio,
                            'sobrante'               =>       $sobrante,
                            'wpounds'                =>       $wpounds,
                        );*/

                    


                    $costValues = $this->WhModel->getCostosByTracking($tracking_number);

                    $iva_g = number_format($manejo * 0.12, 2);
                    $total_cobro = number_format($manejo + $iva_g + $costValues->almacenaje + $costValues->total_imp,2);


                    $datas = array(
                        'id_manifiesto'          =>       $id_manifiesto,
                        'tracking_number'        =>       $tracking_number,
                        'consignee_account'      =>       $consignee_account,
                        'consignee'              =>       $consignee,
                        'consignee_address1'     =>       str_replace('"', '', $consignee_address1),
                        'consignee_phone'        =>       $consignee_phone,
                        'consignee_email'        =>       $consignee_email,
                        'consignee_state' => $departamento,
                        'consignee_city' => $municipio,
                        'manejo' => $manejo, //new
                        'total_ivag' => $iva_g, //new
                        'cobro_final' => $total_cobro //new
                    );

                    
                    
                    $opt_selected[] = "manejo";
                    $opt_selected[] = "total_ivag";
                    $opt_selected[] = "cobro_final";

                    //var_dump($opt_selected);

                    //die();
                    

                    $this->WhModel->updateManifiestoByColumn($datas, $opt_selected);


                    $costValues = $this->WhModel->getCostosByTracking($tracking_number);

                    $infoDeps = $this->WhModel->getDepMunId($tracking_number);

                    //var_dump($infoDeps->departamento_id);
                    //die();
                   
                    //new process to send data to gacela

                    //if($consignee_phone != ''){

                        $phone_without_spaces = str_replace(" ", "", $consignee_phone);
                        $this->send_info_gacela($tracking_number, $phone_without_spaces, $consignee_address1, $consignee,  $consignee_email, $infoDeps->departamento_id, $infoDeps->municipio_id, $costValues->cobro_final);
                        //die();
                    //}
                    
                }
            }
        }
    }

    public function send_info_gacela($guia, $phone, $direccion, $nombre_cliente,  $correo, $departamento_id, $municipio_id, $cobro_final)
    {

        $curl = curl_init();


        $json = '{ 
              "tipo_servicio": "CCE",
                "monto_cce": '.$cobro_final.'

              
                
          }';

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.c807.com/guia.php/api/actualizar/' . $guia,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            /*CURLOPT_POSTFIELDS => '{ 
              "telefono": "' . $phone . '",
              "direccion": "'.$direccion. '",
              "nombre": "' . $nombre_cliente . '",
              "correo": "' . $correo . '",
              "departamento_id": "' . $departamento_id . '",
              "municipio_id": "' . $municipio_id . '"

              
                
          }',*/
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'Authorization: xx3ee1560327af9fc8f30e748a44d839ec3b193f91',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);


        echo $response;
    }

    public function uploadDuca($idpreclasificacion)
    {

        $pais = $_SESSION["pais"]; //Cambiar por variable de sesion
        //echo $pais;
        //die();
        if ($pais == 3) {
            $this->uploadDucaCsv($idpreclasificacion, $_POST, $_FILES);
        } elseif ($pais == 2) {
            $this->uploadDucaXml($idpreclasificacion,$_POST,$_FILES);
        } elseif ($pais == 1) {
            $this->uploadDucaXmlSv($idpreclasificacion, $_POST, $_FILES);
        }
    }

    public function uploadDucaXmlSv($idpreclasificacion, $postDuca, $fileDuca)
    {
        //new process
        $almacenaje = $postDuca["txtValorAlmacenaje"];


        if (empty($almacenaje)) {

            echo "without-almacenaje";
            die();
        }

        if (isset($fileDuca["fileUploadDuca"]["name"])) {

            // Allowed XML MIME types
            $xmlMimes = array(
                'text/xml',
                'application/xml',
                'application/xhtml+xml'
            );

            foreach ($fileDuca['fileUploadDuca']['name'] as $index => $name) {

                $type = $fileDuca['fileUploadDuca']['type'][$index];
                $tmp  = $fileDuca['fileUploadDuca']['tmp_name'][$index];

                // 1. Validate MIME type
                if (!in_array($type, $xmlMimes)) {
                    echo "type-error";
                    exit;
                }

                // 2. Validate extension
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                if ($ext !== "xml") {
                    echo "extension-error";
                    exit;
                }

                
            }

            // If everything is OK
            // echo "xml-ok";
        }

        //die();
        //cantidad Paquetes


        $cantidadPaquetes = 0;
            foreach ($fileDuca['fileUploadDuca']['name'] as $index => $name) {

                $tmp  = $fileDuca['fileUploadDuca']['tmp_name'][$index];

                // 1. Cargar el archivo XML
                $xml = simplexml_load_file($tmp);



                // 2. Iterar sobre los elementos 'producto'
                foreach ($xml->Item as $item) {

                    $cantidadPaquetes++;

                }
            } //ok

            echo "La cantidad de paquetes es".$cantidadPaquetes;

        $almacenaje = str_replace(',', '', $almacenaje);

        echo "almacenaje sin comas " . $almacenaje;

        $almacenajeXPaquete = number_format($almacenaje / $cantidadPaquetes, 2);

        $almacenajeXPaquete = str_replace(",", "", $almacenajeXPaquete);

        $id_empresa = '';

        foreach ($fileDuca['fileUploadDuca']['name'] as $index => $name) {

            $type = $fileDuca['fileUploadDuca']['type'][$index];
            $tmp  = $fileDuca['fileUploadDuca']['tmp_name'][$index];

            // 1. Cargar el archivo XML
            $xml = simplexml_load_file($tmp);

            if ($xml === false) {
                exit("Error al cargar el archivo XML.");
            }

            //echo "## Catálogo de Productos\n\n";

            $fila = 0;

            $results = "";

            $clientId = $this->WhModel->getUserIdPreclasificacion($idpreclasificacion);

            $manifiesto = $clientId->manifiesto;

            $id_empresa = $clientId->client_id;

            $id_pais = $_SESSION["pais"]; //Cambiar por variable de sesion
            //$id_pais = 1;

            $tarifa = $this->WhModel->getTarifas($id_empresa, $id_pais);
            $manejoSinIva = 0;
            $ivaManejo = 0;

            if ($tarifa->manejo_fijo > 0) {
                $manejoSinIva = number_format($tarifa->manejo_fijo / 1.13, 2);
                $ivaManejo = number_format($tarifa->manejo_fijo - $manejoSinIva, 2);
            }

            $cantidadPaquetes = 0;

            $peso_duca = 0;
            $monto_duca = 0;
            $impuestos_duca = 0;

            $peso_duca = (float)$xml->Valuation->Weight->Gross_weight;
            $monto_duca = (float)$xml->Valuation->Total_CIF;
            $reference = (float)$xml->Declarant->Reference->Number;
            //die();

            // 2. Iterar sobre los elementos 'producto'
            foreach ($xml->Item as $articulo) {


                $declaracionResumen = (string)$articulo
                                    ->Previous_doc
                                    ->Summary_declaration;

                $dai = 0;
                $iva = 0;

                echo "Declaración resumen: " . $declaracionResumen . "\n";

                /* ==============================
                   IMPUESTOS DEL ARTICULO
                ============================== */

                foreach ($articulo->Taxation->Taxation_line as $linea) {

                    $codigoImpuesto = (string)$linea->Duty_tax_code;
                    $importeArancel = (float)$linea->Duty_tax_amount;

                    if(strlen($codigoImpuesto) < 3){
                        //echo "Saltamos";
                        continue;
                    }
                    

                    echo "Codigo Arancel ".$codigoImpuesto." Importe Arancel".$importeArancel."\n";

                    if($importeArancel > 0){
                        if($codigoImpuesto == 'DAI'){
                            $dai = ($codigoImpuesto == 'DAI' && $importeArancel > 0) ? $importeArancel : 0;
                        }
                        if($codigoImpuesto == 'IVA'){
                            $iva = ($codigoImpuesto == 'IVA' && $importeArancel > 0) ? $importeArancel : 0;
                        }
                    }
                    


                    //echo "Código impuesto: " . $codigoImpuesto . "\n";
                    //echo "Importe arancelario: " . $importeArancel . "\n\n";
                }

                

                

                

                //newwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww

                // Acceder a elementos y valores:
                //$dai = (float)$item->impuestoGuia;
                //$valor = (float)$item->valorEnQuetzales;
                //$impuestoGuia = (float)$item->impuestoGuia;
                //$descripcionMercancias = explode(" ", $item->descripcionMercancias);
                $tracking_number = $declaracionResumen;

                //$iva = ($valorQuetzales + $impuestoGuia) * 0.12; //resultado del iva
                $totalImpuestos = $dai + $iva;

                $impuestos_duca = $impuestos_duca + $totalImpuestos;

                //$totalPagar = $totalImpuestos + $manejoSinIva + $ivaManejo + $almacenajeXPaquete;
                $totalPagar = $totalImpuestos + $manejoSinIva + $ivaManejo + $almacenajeXPaquete;
                $comision = 0;

                echo "Tracking number: ".$tracking_number . " Dai: " . $dai . " Iva: " . $iva . " Total Impuestos: " . $totalImpuestos . " Manejo sin iva: " . $manejoSinIva . " Iva manejo: " . $ivaManejo." Almacenaje: ".$almacenajeXPaquete." Total a pagar: ".$totalPagar." "."\n";

                $cantidadPaquetes++;

                if ($tracking_number != '') {
                    $this->WhModel->update_dm2($idpreclasificacion, $tracking_number, $dai, $iva, 0, $manejoSinIva, $totalPagar, $totalImpuestos, $ivaManejo, $almacenajeXPaquete);
                } else {
                    exit;
                }
            }

            $arrayToSend = [

                        "id_empresa" => $id_empresa,
                        "manifiesto" => $manifiesto,
                        "duca" => $reference,
                        "valor_mercancia" => $monto_duca,
                        "almacenaje" => $almacenaje,
                        "monto_impuestos" => $impuestos_duca,
                        "peso_duca" => $peso_duca,
                        "cantidad_items" => $cantidadPaquetes

                      ];

            //echo "Monto acumulado: $".$monto_duca." Monto Almacenaje $ ". $almacenaje." Monto Impuestos $ ".$impuestos_duca. " Total Peso: ". $peso_duca."\n";

            $jsonToSend = json_encode($arrayToSend);
            var_dump($jsonToSend);

            //$almacenajeXPaquete = number_format($almacenaje / $cantidadPaquetes, 2);
        }


    }


    public function procesarTrackings($idpreclasificacion)
    {
        $textarea     = $this->input->post('trackings');
        $plataforma   = $this->input->post('plataforma');
        $aplicarTodos = $this->input->post('aplicar_todos');

        if (empty($plataforma)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Debe seleccionar una plataforma'
            ]);
            return;
        }


        var_dump($textarea);
        var_dump($plataforma);
        var_dump($aplicarTodos);
        var_dump($idpreclasificacion);



        //die();

        // 🔥 SI APLICA A TODOS
        if ($aplicarTodos == 1) {

            // Llama a OTRO MODELO
            $actualizados = $this->WarehouseModel->change_clientid($plataforma, $idpreclasificacion, "");

            echo json_encode([
                'status' => 'ok',
                'tipo' => 'todos',
                'total_actualizados' => $actualizados
            ]);
            return;
        }

        // 🔹 SI ES INDIVIDUAL (POR TRACKINGS)
        if (empty($textarea)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Debe ingresar trackings'
            ]);
            return;
        }

        // Separar líneas
        $lineas = explode("\n", $textarea);
        $trackings = [];

        foreach ($lineas as $linea) {
            $tracking = trim($linea);
            if ($tracking != '') {
                $trackings[] = $tracking;
            }
        }

        if (empty($trackings)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No hay trackings válidos'
            ]);
            return;
        }

        // Modelo normal
        $actualizados = $this->WarehouseModel->change_clientid($plataforma, $idpreclasificacion, $trackings, 1);

        echo json_encode([
            'status' => 'ok',
            'tipo' => 'individual',
            'total_recibidos' => count($trackings),
            'total_actualizados' => $actualizados
        ]);
    }




    public function uploadDucaXml($idpreclasificacion, $postDuca, $fileDuca)
    {
        //new process
        $almacenaje = $postDuca["txtValorAlmacenaje"];


        if (empty($almacenaje)) {

            echo "without-almacenaje";
            die();
        }

        if (isset($fileDuca["fileUploadDuca"]["name"])) {

            // Allowed XML MIME types
            $xmlMimes = array(
                'text/xml',
                'application/xml',
                'application/xhtml+xml'
            );

            foreach ($fileDuca['fileUploadDuca']['name'] as $index => $name) {

                $type = $fileDuca['fileUploadDuca']['type'][$index];
                $tmp  = $fileDuca['fileUploadDuca']['tmp_name'][$index];

                // 1. Validate MIME type
                if (!in_array($type, $xmlMimes)) {
                    echo "type-error";
                    exit;
                }

                // 2. Validate extension
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                if ($ext !== "xml") {
                    echo "extension-error";
                    exit;
                }

                
            }

            // If everything is OK
            // echo "xml-ok";
        }

        //die();
        //cantidad Paquetes


        $cantidadPaquetes = 0;
            foreach ($fileDuca['fileUploadDuca']['name'] as $index => $name) {

                $tmp  = $fileDuca['fileUploadDuca']['tmp_name'][$index];

                // 1. Cargar el archivo XML
                $xml = simplexml_load_file($tmp);



                // 2. Iterar sobre los elementos 'producto'
                foreach ($xml->fraccion as $item) {

                    $cantidadPaquetes++;

                }
            }

            echo "La cantidad de paquetes es".$cantidadPaquetes;

        $almacenaje = str_replace(',', '', $almacenaje);

        echo "almacenaje sin comas " . $almacenaje;

        $almacenajeXPaquete = number_format($almacenaje / $cantidadPaquetes, 2);

        $almacenajeXPaquete = str_replace(",", "", $almacenajeXPaquete);


        foreach ($fileDuca['fileUploadDuca']['name'] as $index => $name) {

            $type = $fileDuca['fileUploadDuca']['type'][$index];
            $tmp  = $fileDuca['fileUploadDuca']['tmp_name'][$index];

            // 1. Cargar el archivo XML
            $xml = simplexml_load_file($tmp);

            if ($xml === false) {
                exit("Error al cargar el archivo XML.");
            }

            //echo "## Catálogo de Productos\n\n";

            $fila = 0;

            $results = "";

            $clientId = $this->WhModel->getUserIdPreclasificacion($idpreclasificacion);

            $id_empresa = $clientId->client_id;

            $id_pais = $_SESSION["pais"]; //Cambiar por variable de sesion

            $tarifa = $this->WhModel->getTarifas($id_empresa, $id_pais);
            $manejoSinIva = 0;
            $ivaManejo = 0;

            if ($tarifa->manejo_fijo > 0) {
                $manejoSinIva = number_format($tarifa->manejo_fijo / 1.12, 2);
                $ivaManejo = number_format($tarifa->manejo_fijo - $manejoSinIva, 2);
            }

            $cantidadPaquetes = 0;

            // 2. Iterar sobre los elementos 'producto'
            foreach ($xml->fraccion as $item) {

                // Acceder a elementos y valores:
                $dai = (float)$item->impuestoGuia;
                $valorQuetzales = (float)$item->valorEnQuetzales;
                $impuestoGuia = (float)$item->impuestoGuia;
                $descripcionMercancias = explode(" ", $item->descripcionMercancias);
                $tracking_number = $descripcionMercancias[0];

                $iva = ($valorQuetzales + $impuestoGuia) * 0.12; //resultado del iva
                $totalImpuestos = $dai + $iva;
                $totalPagar = $totalImpuestos + $manejoSinIva + $ivaManejo + $almacenajeXPaquete;
                $comision = 0;

                echo "Tracking number: ".$tracking_number . " Dai: " . $dai . " Iva: " . $iva . " Total Impuestos: " . $totalImpuestos . " Manejo sin iva: " . $manejoSinIva . " Iva manejo: " . $ivaManejo." Almacenaje: ".$almacenajeXPaquete." Total a pagar: ".$totalPagar." "."\n";

                $cantidadPaquetes++;

                if ($tracking_number != '') {
                    $this->WhModel->update_dm2($idpreclasificacion, $tracking_number, $dai, $iva, 0, $manejoSinIva, $totalPagar, $totalImpuestos, $ivaManejo, $almacenajeXPaquete);
                } else {
                    exit;
                }
            }

            //$almacenajeXPaquete = number_format($almacenaje / $cantidadPaquetes, 2);
        }


    }





    public function uploadDucaCsv($idpreclasificacion, $postDuca, $fileDuca)
    {
        //new process
        $almacenaje = $postDuca["txtValorAlmacenaje"];

        if(empty($almacenaje)){

            echo "without-almacenaje";
            die();

        }

        //detectar pais
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //  echo "nombre ".$_FILES["file"]["name"];
        if (isset($fileDuca["fileUploadDuca"]["name"])) {
            $destino = getcwd() . "/public/uploads/file/";

            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }

            $extension = explode(".", $fileDuca["fileUploadDuca"]["name"]);
            $nombre = time() . "-ducas." . $extension[1];

            /*if (move_uploaded_file($_FILES['fileUploadDuca']['tmp_name'], $destino . "/" . $nombre)) {
                    $link = $destino . "/" . $nombre;
                } else {
                }*/

            if (isset($fileDuca["fileUploadDuca"]["name"])) {

                $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

                if (in_array($fileDuca['fileUploadDuca']['type'], $csvMimes)) {
                    //echo "File is Allowed";
                } else {
                    echo "type-error";
                    die();
                }
            }

            $fila = 0;

            $results = "";


            $clientId = $this->WhModel->getUserIdPreclasificacion($idpreclasificacion);

            $id_pais = $_SESSION["pais"]; //Cambiar por variable de sesion

            $cantPaquetes = $this->WhModel->getCantidadPaquetes($idpreclasificacion, $id_pais);

            $almacenaje = str_replace(',', '', $almacenaje);
            
            echo "almacenaje sin comas ".$almacenaje;
            
            $almacenajeXPaquete = number_format($almacenaje / $cantPaquetes->cant_paquetes, 2);

            $almacenajeXPaquete = str_replace(",","",$almacenajeXPaquete);

            echo "Cantidad de paquetes: ".$cantPaquetes->cant_paquetes;

            $id_empresa = $clientId->client_id;

            $tarifa = $this->WhModel->getTarifas($id_empresa, 3);
            $manejoSinIva = 0;
            $ivaManejo = 0;

            if ($tarifa->manejo_fijo > 0) {
                $manejoSinIva = number_format($tarifa->manejo_fijo / 1.15, 2);
                $ivaManejo = number_format($manejoSinIva * 0.15,2);
            }

            $cantidadPaquetes = 0;

            if (($gestor = fopen($fileDuca['fileUploadDuca']['tmp_name'], "r")) !== FALSE) {


                while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {

                   

                    $numero = count($datos);

                    $fila++;

                    if ($fila > 1) {

                        foreach ($datos as $k => $v) {
                            $encoding = mb_detect_encoding($v, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                            if ($encoding !== 'UTF-8') {
                                $datos[$k] = mb_convert_encoding($v, 'UTF-8', $encoding);
                            }
                        }


                        

                        //$tracking_number = $datos[29];
                        $tracking_number = $datos[0];
                        //$dai  = str_replace("$", "", str_replace(",", ".", $datos[15]));
                        //$iva1 = str_replace("$", "", str_replace(",", ".", $datos[16]));
                        //$iva2 = str_replace("$", "", str_replace(",", ".", $datos[18]));
                        $dai  = str_replace("$", "", str_replace(",", ".", $datos[1]));
                        $isv = str_replace("$", "", str_replace(",", ".", $datos[2]));
                        $sel = str_replace("$", "", str_replace(",", ".", $datos[3]));
                        
                        
                        $id_manifiesto = $idpreclasificacion; //id manifiesto
                        $totalImpuestos = $dai + $isv;
                        $totalPagar = $totalImpuestos + $manejoSinIva + $ivaManejo + $almacenajeXPaquete;

                        echo "Tracking: ".$tracking_number . " Dai: " . $dai . " Iva: " . $isv . " Almancenaje:". $almacenajeXPaquete ." Total Impuestos:" . $totalImpuestos . " Manejo sin iva:" . $manejoSinIva . " Iva Manejo:" . $ivaManejo . " Total a pagar: " . $totalPagar . "\n";

                        if ($tracking_number != '') {
                            $this->WhModel->update_dm2($idpreclasificacion, $tracking_number, $dai, $isv, 0, $manejoSinIva, $totalPagar, $totalImpuestos, $ivaManejo, $almacenajeXPaquete, $sel);
                        } else {
                            exit;
                        }


                        $cantidadPaquetes++;
                    }
                }



            
                echo $cantidadPaquetes . " Almacenaje por paquete " . $almacenajeXPaquete;
            }
        }
    }

    public function changeOfCountry($idpreclasificacion)
    {
        //var_dump($_POST);
        //echo $idpreclasificacion;
        if (isset($_POST["pais"]) != "" && isset($_POST["empresa"]) != "" && $_POST["pais"] == 3) {

            $this->WhModel->changeOfCountry($idpreclasificacion, $_POST["pais"], $_POST["empresa"]);
            $this->WhModel->changeOfCountryTrackings($idpreclasificacion, $_POST["pais"], $_POST["empresa"], 'Cainiao- AliExpress');
            $result = array("success" => "Modificacion exitosa");
        } else {
            $result = array("error" => "Error en la modificacion, consultar a informatica.");
        }

        echo json_encode($result);
    }

    public function setProveedorLm($idpreclasificacion)
    {
        if (isset($_POST["proveedor_lm"])) {
            $this->WhModel->setProveedorLM($idpreclasificacion, $_POST["proveedor_lm"]);
            $result = array("success" => "Modificacion exitosa");
        } else {
            $result = array("error" => "Another process");
        }

        echo json_encode($result);
    }


    public function upload_file_sobrante($idpreclasificacion)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //  echo "nombre ".$_FILES["file"]["name"];
        if (isset($_FILES["file"]["name"])) {
            $destino = getcwd() . "/public/uploads/file/";

            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }

            $extension = explode(".", $_FILES["file"]["name"]);
            $nombre = time() . "-plantilla." . $extension[1];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destino . "/" . $nombre)) {
                $link = $destino . "/" . $nombre;
            } else {
            }

            $object = PHPExcel_IOFactory::load($link);

            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $tracking_number                = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $gweight                        = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $value                          = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $items                          = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $commodity                      = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $consignee_account              = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    $consignee                      = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $buyer_company                  = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    $consignee_address1             = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    $buyer_address1_number          = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    $consignee_address2             = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $consignee_address3             = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    $buyer_district                 = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                    $consignee_city                 = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    $consignee_state                = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                    $consignee_country              = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                    $consignee_zip                  = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                    $consignee_phone                = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    $consignee_email                = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                    $hts                            = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                    $id_pais                        = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                    $pieces                         = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
                    $departamento                   = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
                    $municipio                      = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
                    $wpounds                        = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
                    $itemquantities                 = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
                    $bag_number                 = $worksheet->getCellByColumnAndRow(26, $row)->getValue();

                    $id_manifiesto                  = $idpreclasificacion;
                    $sobrante                       = 1;

                    $datas = array(
                        'id_manifiesto'          =>       $id_manifiesto,
                        'tracking_number'        =>       $tracking_number,
                        'gweight'                =>       $gweight,
                        'value'                  =>       $value,
                        'items'                  =>       $items,
                        'commodity'              =>       $commodity,
                        'consignee_account'      =>       $consignee_account,
                        'consignee'              =>       $consignee,
                        'buyer_company'          =>       $buyer_company,
                        'consignee_address1'     =>       $consignee_address1,
                        'buyer_address1_number'  =>       $buyer_address1_number,
                        'consignee_address2'     =>       $consignee_address2,
                        'consignee_address3'     =>       $consignee_address3,
                        'buyer_district'         =>       $buyer_district,
                        'consignee_city'         =>       $consignee_city,
                        'consignee_state'        =>       $consignee_state,
                        'consignee_country'      =>       $consignee_country,
                        'consignee_zip'          =>       $consignee_zip,
                        'consignee_phone'        =>       $consignee_phone,
                        'consignee_email'        =>       $consignee_email,
                        'hts'                    =>       $hts,
                        'id_pais'                =>       $id_pais,
                        'pieces'                 =>       $pieces,
                        'departamento'           =>       $departamento,
                        'municipio'              =>       $municipio,
                        'sobrante'               =>       $sobrante,
                        'wpounds'                =>       $wpounds,
                        'items_quantities'       =>       $itemquantities,
                        'bag_number' =>       $bag_number
                    );
                    //$dato = json_encode($datas);
                    //var_dump($dato);
                    $this->WhModel->guardar_listsobrante($tracking_number, $datas, $id_manifiesto);
                }
            }
        }
    }
    public function referencia_upd($tracking_number)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['lista']    = $this->WhModel->buscar_tracking_numb($tracking_number);
        $this->load->view("warehouse/lista", $this->datos);
    }

    public function add_reem_referencia($tracking_number, $referencia)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->WarehouseModel->add_reem_referencia($tracking_number, $referencia);
    }

    public function asignar_ref($idpreclasificacion)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['refxbolsa']       = $this->WhModel->bolsa_ref($idpreclasificacion);
        $this->datos['manifiesto']       = $this->WhModel->crear_ref($idpreclasificacion);
        $this->load->view("catalogos/asg_ref", $this->datos);
    }

    public function listadoxsaco($bolsas)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['listadoxsaco']       = $this->WhModel->listadoxsaco($bolsas);
        $this->load->view("catalogos/no_guias", $this->datos);
    }

    public function update_ref_bolsa($referencia, $bolsas, $id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->WhModel->update_ref_bolsa($referencia, $bolsas);

        //  $verificar_ref = $this->WhModel->verificar_ref($id,$referencia);
        // var_dump($verificar_ref);

        $result = $this->WhModel->listadoxsaco($bolsas);


        $data = array(
            'id_manifiesto' => $id,
            'referencia' => $referencia,
            'paquetes' => $result[0]->no_guia
        );
        $this->WhModel->update_ref_bolsa_ma($data);
    }

    public function send_tracking()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['sucursal']       = $this->WhModel->sucursal();
        $this->load->view("consulta_track/send_tracking", $this->datos);
    }

    public function validar_warehouse($id)
    {
        $warehouse = $this->WarehouseModel->verificar_warehouse($id);

        if ($warehouse) {
            echo json_encode($warehouse);
        }
    }

    public function enviar_tracking($warehouse, $sucursal, $tipo_estatus)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        if (!isset($_SESSION)) {
            session_start();
        }

        $wh = $this->WarehouseModel->verificar_warehouse($warehouse);
        $mp = $this->WarehouseModel->sucursal_consulta($sucursal);

        $consignee          = $wh->consignee;
        $description        = $wh->commodity;
        $peso               = $wh->gweight;
        $consignee_address1 = $wh->consignee_address1;
        $total              = round($wh->cobro_final, 2);
        $warehouse          = "Tracking: " . $warehouse;
        $tracking           = $wh->tracking_number;
        $correo             = $wh->consignee_email;
        $direccion          = $mp[0]->direccion;
        $agencia            = $mp[0]->nombre_agencia;

        $this->sendmail($warehouse, $tracking, $sucursal, $tipo_estatus, $agencia, $direccion, $correo, $consignee, $peso, $consignee_address1, $description, $total);
    }

    /*Funcion  para enviar por correo */

    public function sendmail1($warehouse, $tracking, $sucursal, $tipo_estatus, $agencia, $direccion, $correo, $consignee, $peso, $consignee_address1, $description, $total)
    {
        $email     = $correo;
        //$email     = 'desarrollosv3@mistarship.com';
        $CCemal    = 'supervisorsacultimamilla@mistarship.com,desarrollosv3@mistarship.com';
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        $this->load->library('email');
        $fecha = date("d-m-Y");

        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'localhost',
            'smtp_port' => 25,
            'smtp_user' => 'adminserver@stargates.site',
            'smtp_pass'  => 'adminserver2022',
            'charset'    => 'utf-8',
            'mailtype'   => 'html'
        );
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->from('supervisorsacultimamilla@mistarship.com', 'Estatus de paquete');

        //  $this->email->to('desarrollosv@c807.com'); // se pone cuenta por defecto
        $this->email->to($email); // se obtiene el correo del  cliente
        $this->email->cc($CCemal);
        if ($tipo_estatus == 1) {
            $this->email->subject('Entrega de paquete proveniente de Aliexpress ' . $warehouse);

            $msj = '<p style="font-size: 16px">MailAmericas te informa sobre un paquete proveniente de Aliexpress y te detallamos la informaci&oacute;n...</p> <br>
            <ul style="font-size: 14px;list-style: none;">
                                <li style="margin: 0.5rem 0rem">
                                   ESTADO DE TU PAQUETE:  <span style="color: #ff5a21">' . $agencia . '</span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    DIRECCI&Oacute;N:  <span style="color: #ff5a21">' . $direccion . '</span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    CONSIGNATARIO: <span style="color: #ff5a21">' . $consignee . '</span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    DESCRIPCI&Oacute;N: <span style="color: #ff5a21">' . $description . '</span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    TRACKING: <span style="color: #ff5a21">' . $tracking . '</span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    TOTAL A PAGAR:    <span style="color: #ff5a21">' . $total . '</span>
                                </li>
                            </ul>
                            <br>
                             <div class="container">
                <div class="row" style="text-align: center;display: block;">

                <p style="font-size: 14px;text-align: center;">Para retirar tu paquete lo puedes hacer en nuestra agencia o solicitar env&iacute;o a domicilio sin costo adicional a nuestro WhatsApp; recuerda que debes brindar tu n&uacute;mero de Tracking y confirmar tu direcci&oacute;n de entrega.</p>
             
               <p style="font-size: 14px;text-align: center; font-weight: bold;">Nota: Tu paquete estar&aacute; disponible en la agencia seleccionada a partir del ' . $fecha . ' por los pr&oacute;ximos 10 d&iacute;as h&aacute;biles, despu&eacute;s sera retornado a origen</p>
            <a href="https://api.whatsapp.com/send/?phone=50360346037&text&app_absent=0" style="text-decoration: blink;
            color: black;
            display: inline-block;
            font-weight: bolder;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1.5rem;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            " target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/124/124034.png" style="width:15%"> 
           </a>
           <p style="font-size: 22px;text-align: center; font-weight: bold;">Este n&uacute;mero no est&aacute; habilitado para llamadas, solo mensajes de texto</p>
            <p style="font-size: 18px;text-align: center; font-weight: bold;">+503 6034-6037</p>
            
        </div>
    </div>
    <br>
     </div>';
        } else if ($tipo_estatus == 2) {
            $this->email->subject($warehouse . ' Retorno de paquete proveniente de Aliexpress ');

            $msj = '<p style="font-size: 16px">MailAmericas te informa que tu paquete proveniente de Aliexpress ha sido retornado a origen, por tiempo de espera alcanzado.</p> <br>
            <ul style="font-size: 14px;list-style: none;">
                                <li style="margin: 0.5rem 0rem">
                                   ESTADO DE TU PAQUETE:  <span style="color: #ff5a21"><b> RETORNADO A ORIGEN</b></span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    DIRECCI&Oacute;N:  <span style="color: #ff5a21">' . $consignee_address1 . '</span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    CONSIGNATARIO: <span style="color: #ff5a21">' . $consignee . '</span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    DESCRIPCI&Oacute;N: <span style="color: #ff5a21">' . $description . '</span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    TRACKING: <span style="color: #ff5a21">' . $tracking . '</span>
                                </li>
                                <li style="margin: 0.5rem 0rem">
                                    ESTATUS:    <span style="color: #ff5a21"><b>ABANDONO</b></span>
                                </li>
                            </ul>
                            <br>
                             <div class="container">
                <div class="row" style="text-align: center;display: block;">

                <p style="font-size: 14px;text-align: center;">El paquete ha sido retornado a origen, cualquier consulta puedes hacerla directo con la plataforma ya que se ha alcanzado el tiempo m��ximo de espera. Por este motivo, ya no se podr�� proceder con la entrega.</p>
                <p style="font-size: 14px;text-align: center;">Saludos.</p>             
             
            
        </div>
    </div>
    <br>
     </div>';
        }
        //$this->email->message(utf8_decode($msj));
        $this->email->message($msj);
        $data = array(
            'tracking_number'      => $tracking,
            'id_sucursal'          => $sucursal,
            'estatus'              => 1,
            'fecha_envio'          => date("Y-m-d H:i:s"),
            'tipo_envio'           => $tipo_estatus

        );
        //var_dump($data);
        $this->WarehouseModel->guardar_envio_track($data);

        if ($this->email->send()) {
            echo 'Your email was sent.';
        } else {
            $this->email->subject('Error en envio de correo del tracking ' . $warehouse);
            show_error($this->email->print_debugger());
            $data = array(
                'tracking_number'      => $tracking,
                'id_sucursal'          => $sucursal,
                'estatus'              => 0,
                'fecha_envio'          => date("Y-m-d H:i:s"),

            );
            //var_dump($data);
            $this->WarehouseModel->guardar_envio_track($data);
        }
        $this->email->clear(true);
    }


    public function testMail(){
        
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://stargates.site/grupo_erp/ventas/ReclamosController/sendmailsapi',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('json' => '{"correos":"mendez.manzanares@gmail.com","cuerpo":"Aqui HTML", "asunto":"Prueba de Nico","copias":"desarrollo@mistarship.com"}'),
            CURLOPT_HTTPHEADER => array(
                'Cookie: PHPSESSID=2d15ca536c4db3e80954e6b5ae1e8605'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;

    }

    public function sendmail($warehouse, $tracking, $sucursal, $tipo_estatus, $agencia, $direccion, $correo, $consignee, $peso, $consignee_address1, $description, $total)
    {
        $email     = $correo;
        //$email = "mendez.manzanares@gmail.com";

        $CCemal    = 'supervisorsacultimamilla@mistarship.com,desarrollosv3@mistarship.com,desarrollosv2@c807.com,desarrollo@mistarship.com,servicioalcliente12@mistarship.com,opsultimamilla1@mistarship.com';
        //$CCemal    = 'desarrollosv2@c807.com';
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        //$this->load->library('email');
        $fecha = date("d-m-Y");

        

        

        //  $this->email->to('desarrollosv@c807.com'); // se pone cuenta por defecto
        //$this->email->to($email); // se obtiene el correo del  cliente
        //$this->email->cc($CCemal);

        $asunto = "";

        if ($tipo_estatus == 1) {
            $asunto = "Entrega de paquete proveniente de Aliexpress " . $warehouse;

           
            $msj = "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width,initial-scale=1.0'>
    <title>Notificación de Paquete</title>
</head>
<body style='margin:0;padding:0;font-family:Segoe UI,Arial,sans-serif;background-color:#f4f7f9;'>
    <div style='max-width:600px;margin:20px auto;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.1);border:1px solid #e1e4e8;'>

        <div style='width:100%;'>
            <img src='https://www.mistarship.com/starship/public/UpdateSatrship/header-mail.png' alt='Encabezado Starship' style='width:100%;display:block;'>
        </div>

        <div style='padding:25px 20px 10px 20px;'>
            <div style='display:inline-block;background-color:#ED9B33;color:white;border-radius:50px;padding:12px 25px;font-size:18px;font-weight:bold;box-shadow:0 2px 5px rgba(237,155,51,0.3);'>
                <table border='0' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td></td>
                        <td style='color:#ffffff;font-family:Arial,sans-serif;'>Notificación de paquete</td>
                    </tr>
                </table>
            </div>
        </div>

        <div style='text-align:center;width:100%;'>
            <a href='https://api.whatsapp.com/send/?phone=50360145223&text&app_absent=0' target='_blank' style='text-decoration:none;color:black;display:inline-block;font-family:sans-serif;font-weight:bold;vertical-align:middle;'>
                <img src='https://cdn-icons-png.flaticon.com/512/124/124034.png' alt='Icono' style='border-radius:50%;width:45px;height:45px;margin-right:10px;vertical-align:middle;'>
                <span style='vertical-align:middle;font-size:20px;'>Click aquí</span>
            </a>
        </div>

        <div style='padding:20px 30px;color:#444;'>
            <div style='border-left:4px solid #f26522;padding-left:20px;background-color:#fafafa;padding-top:15px;padding-bottom:15px;border-radius:0 8px 8px 0;'>
                
                <table width='100%' border='0' cellspacing='0' cellpadding='5' style='font-size:15px;font-family:Arial,sans-serif;'>
                    <tr>
                        <td style='color:#888;width:40%;'><strong>Estado de tu paquete:</strong></td>
                        <td style='color:#007bff;font-weight:bold;'>".$agencia."</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Dirección:</strong></td>
                        <td style='color:#007bff;font-weight:bold;'>".$direccion."</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Consignatario:</strong></td>
                        <td style='color:#333;'>".$consignee."</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Descripción:</strong></td>
                        <td style='color:#333;font-weight:600;'>".$description. "</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Tracking:</strong></td>
                        <td style='color:#333;'>" . $tracking . "</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Total:</strong></td>
                        <td style='color:#f26522;font-weight:bold;font-size:13px;'>$".$total. "</td>
                    </tr>
                    
                </table>

            </div>
        </div>

        <div style='text-align:center;padding:20px 0;'>
            <p style='font-size:13px;color:#999;margin-top:5px;font-family:Arial,sans-serif;'>Para retirar tu paquete lo puedes hacer en nuestra agencia o solictar env&iacute;o a domicilio sin costo adicional a nuestro WhatsApp; recuerda que debes brindar tu n&uacute;mero de Tracking y confirmar tu direcci&oacute;n de entrega. </p>
            <p style='font-size:13px;color:#999;margin-top:5px;font-family:Arial,sans-serif;'>Nota: Tu paquete estar&aacute; disponible en la agencia seleccionada a partir del  $fecha <br> por los pr&oacute;ximos 10 d&iacute;as h&aacute;biles, despu&eacute;s sera retornado a origen</p>
        
            <p style='color:#f26522;font-weight:bold;font-size:18px;margin:0;font-family:Arial,sans-serif;'>¡GRACIAS POR CONFIAR EN NOSOTROS!</p>
            
        </div>

        <div style='width:100%;'>
            <img src='https://www.mistarship.com/starship/public/UpdateSatrship/footer-mail.png' alt='Pie Starship' style='width:100%;display:block;'>
        </div>

    </div>
</body>
</html>";

           

            //echo $msj;




        } else if ($tipo_estatus == 2) {
            $asunto = 'Retorno de paquete proveniente de Aliexpress '.$warehouse;
            //$this->email->subject($warehouse . ' Retorno de paquete proveniente de Aliexpress ');


            $msj = "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width,initial-scale=1.0'>
    <title>Notificación de Paquete</title>
</head>
<body style='margin:0;padding:0;font-family:Segoe UI,Arial,sans-serif;background-color:#f4f7f9;'>
    <div style='max-width:600px;margin:20px auto;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.1);border:1px solid #e1e4e8;'>

        <div style='width:100%;'>
            <img src='https://www.mistarship.com/starship/public/UpdateSatrship/header-mail.png' alt='Encabezado Starship' style='width:100%;display:block;'>
        </div>

        <div style='padding:25px 20px 10px 20px;'>
            <div style='display:inline-block;background-color:#ED9B33;color:white;border-radius:50px;padding:12px 25px;font-size:18px;font-weight:bold;box-shadow:0 2px 5px rgba(237,155,51,0.3);'>
                <table border='0' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td></td>
                        <td style='color:#ffffff;font-family:Arial,sans-serif;'>Notificación de Abandono</td>
                    </tr>
                </table>
            </div>
        </div>

        <div style='text-align:center;width:100%;'>
            <a href='https://api.whatsapp.com/send/?phone=50360145223&text&app_absent=0' target='_blank' style='text-decoration:none;color:black;display:inline-block;font-family:sans-serif;font-weight:bold;vertical-align:middle;'>
                <img src='https://cdn-icons-png.flaticon.com/512/124/124034.png' alt='Icono' style='border-radius:50%;width:45px;height:45px;margin-right:10px;vertical-align:middle;'>
                <span style='vertical-align:middle;font-size:20px;'>Click aquí</span>
            </a>
        </div>

        <div style='padding:20px 30px;color:#444;'>
            <div style='border-left:4px solid #f26522;padding-left:20px;background-color:#fafafa;padding-top:15px;padding-bottom:15px;border-radius:0 8px 8px 0;'>
                
                <table width='100%' border='0' cellspacing='0' cellpadding='5' style='font-size:15px;font-family:Arial,sans-serif;'>
                    <tr>
                        <td style='color:#888;width:40%;'><strong>Estado de tu paquete:</strong></td>
                        <td style='color:#007bff;font-weight:bold;'>" . $agencia . "</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Dirección:</strong></td>
                        <td style='color:#007bff;font-weight:bold;'>" . $direccion . "</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Consignatario:</strong></td>
                        <td style='color:#333;'>" . $consignee . "</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Descripción:</strong></td>
                        <td style='color:#333;font-weight:600;'>" . $description . "</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Tracking:</strong></td>
                        <td style='color:#333;'>" . $tracking . "</td>
                    </tr>
                    <tr>
                        <td style='color:#888;'><strong>Total:</strong></td>
                        <td style='color:#f26522;font-weight:bold;font-size:13px;'>$" . $total . "</td>
                    </tr>

                    <tr>
                        <td style='color:#888;'><strong>Estado:</strong></td>
                        <td style='color:#f26522;font-weight:bold;font-size:17px;color:red;'>ABANDONO</td>
                    </tr>
                    
                </table>

            </div>
        </div>

        <div style='text-align:center;padding:20px 0;'>

            <p style='font-size:13px;color:#999;margin-top:5px;font-family:Arial,sans-serif;'>El paquete ha sido retornado a origen, cualquier consulta puedes hacerla directo con la plataforma ya que se ha alcanzado el tiempo maximo de espera. <br>Por este motivo, ya no se podrá proceder con la entrega.</p>
           
            <p style='color:#f26522;font-weight:bold;font-size:18px;margin:0;font-family:Arial,sans-serif;'>¡GRACIAS POR CONFIAR EN NOSOTROS!</p>
            
        </div>

        <div style='width:100%;'>
            <img src='https://www.mistarship.com/starship/public/UpdateSatrship/footer-mail.png' alt='Pie Starship' style='width:100%;display:block;'>
        </div>

    </div>
</body>
</html>";


            
        }


        $msj = str_replace(["\r\n", "\r", "\n"], " ", $msj);
        
        $data = array(
            'tracking_number'      => $tracking,
            'id_sucursal'          => $sucursal,
            'estatus'              => 1,
            'fecha_envio'          => date("Y-m-d H:i:s"),
            'tipo_envio'           => $tipo_estatus

        );
        //var_dump($data);
        $this->WarehouseModel->guardar_envio_track($data);

        $json = '{"correos":"' . $email . '","cuerpo":"' . str_replace('"',"'",$msj). '", "asunto":"' . $asunto . '","copias":"' . $CCemal . '"}';
        //echo $json;
        //die();


        //new code Nico
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://stargates.site/grupo_erp/ventas/ReclamosController/sendmailsapi',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('json' => $json),
            CURLOPT_HTTPHEADER => array(
                'Cookie: PHPSESSID=2d15ca536c4db3e80954e6b5ae1e8605'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        //new code Nico




        /*if ($this->email->send()) {
            echo 'Your email was sent.';
        } else {
            $this->email->subject('Error en envio de correo del tracking ' . $warehouse);
            show_error($this->email->print_debugger());
            $data = array(
                'tracking_number'      => $tracking,
                'id_sucursal'          => $sucursal,
                'estatus'              => 0,
                'fecha_envio'          => date("Y-m-d H:i:s"),

            );
            //var_dump($data);
            $this->WarehouseModel->guardar_envio_track($data);
        }
        $this->email->clear(true);*/
    }

    public function report_send_tracking()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->load->view("consulta_track/form_tracking");
    }

    public function buscar_fecha_envio_guia($fecha_desde_envio, $fecha_hasta_envio)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $data['tracking_send'] = $this->WarehouseModel->buscar_fecha_envio_guia($fecha_desde_envio, $fecha_hasta_envio);
        $this->load->view("consulta_track/cuerpo_tracking", $data);
    }

    public function agregar_costosxref($idpreclasificacion)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['referencia']       = $this->Conf_model->referencias($idpreclasificacion);
        $this->datos['manifiesto']       = $this->WhModel->crear_ref($idpreclasificacion);
        $this->load->view("warehouse/vista_referencia", $this->datos);
    }

    public function guiasxref($referencia, $id_manifiesto, $full = 0)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['guiasxref']   = $this->WhModel->guiasxref($referencia, $id_manifiesto, $full);
        $this->load->view("catalogos/guiasxref", $this->datos);
    }

    public function valor_guiasxref($referencia, $tracking_number)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['valor_guiasxref']   = $this->WhModel->valor_guiasxref($referencia, $tracking_number);
        $this->load->view("catalogos/valor_guia", $this->datos);
    }

    public function valor_guiasxref_general($referencia, $id_manifiesto)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['valor_guiasxref_general']   = $this->WhModel->valor_guiasxref_general($referencia, $id_manifiesto);
        $this->load->view("catalogos/totales", $this->datos);
    }

    public function guardar_costos($id_manifiesto, $referencia, $tracking_number, $iva, $dai)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        // Obtiene la tasa de cambio para el país almacenado en la sesión actual.
        $tasa = $this->Tasa_Cambio_Model->get_tasa($_SESSION['pais']);
        $monto_tasa = 0;
        $impuesto = 0;
        $manejo              =   0;

        $monto_tasa = $tasa->tasa;
        $tax = '1.'  . $tasa->impuesto;
        $impuesto   = $tasa->impuesto / 100;
        $manejo     = $tasa->manejo;

        $iva_manejo          =   $manejo * $impuesto;

        $total_imp           =   number_format($dai, 2) + number_format($iva, 2);




        //  $manejo              =   2.00;
        //  $iva_manejo          =   0.26;


        $tax_numeric = floatval($tax);
        $manejo_total        =   $manejo + $iva_manejo;

        // echo  $impuesto . ' ' .  $manejo. ' ' .$iva_manejo . ' '. $iva_manejo . ' '. $manejo_total.'\n';
        //  $comision            =   ((number_format($total_imp, 2) + number_format($manejo_total, 2)) * (5 / 100)) / 1.13;
        $comision            =   ((number_format($total_imp, 2) + number_format($manejo_total, 2)) * (5 / 100)) / $tax_numeric;

        $iva_comision        =   ((number_format($total_imp, 2) + number_format($manejo_total, 2)) * (5 / 100)) - $comision;




        $total_iva           =   $iva_manejo + $iva_comision;

        //echo  $total_imp . ' ' .   ' ' .$comision . ' '. $total_iva ;

        // echo  $comision . ' ' .  $iva_comision. ' ' .$iva_manejo . ' '. $total_iva . ' '. $manejo_total;

        $cobro_final         =   number_format($total_imp, 2)  + number_format($comision, 2) + $manejo + number_format($total_iva, 2);


        $this->WhModel->guardar_costos($id_manifiesto, $referencia, $tracking_number, $iva, $dai, $total_imp, $comision, $total_iva, $cobro_final, $manejo);
    }

    public function tilde()
    {

        $status_name = "Revisado en centro de distribución";
        $data = array(

            "description" => $status_name,
            "city" => "CCO",
            "received_by" => "",
            "relationship" => null,
            "delivery_proof" => [
                " "
            ],

            "gcs" => "",
            "details" => ""
        );


        var_dump($data);
    }

    public static function TildesHtml($cadena)
    {
        return str_replace(
            array("��", "��", "��", "��", "��", "�0�9", "�0�9", "�0�7", "�0�1", "�0�7", "�0�3", "�0�5"),
            array(
                "&aacute;",
                "&eacute;",
                "&iacute;",
                "&oacute;",
                "&uacute;",
                "&ntilde;",
                "&Aacute;",
                "&Eacute;",
                "&Iacute;",
                "&Oacute;",
                "&Uacute;",
                "&Ntilde;"
            ),
            $cadena
        );
    }

    private function properText($str)
    {

        return ('Revisado en centro de distribuci��n');
    }

    public function cambiar_estatus_masivo()
    {
        $this->datos['estatus'] = $this->Conf_model->estatus();
        $this->load->view("warehouse/vista_cambiar_estatus_masivo", $this->datos);
    }

    public function send_status_api($json)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://stargateserp.com/app_starship/ws/index.php/api/estadosApg',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'x-api-key: 4a4da5bb348e648e51d84e24c8652d1284c31156',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function insertar_estatus_masivo()
    {


        $listW = $_POST["txtListWarehouse"];

        $statusArray = explode("-", $_POST["estatus"]);

        $decodedString = urldecode($listW);
        $array = explode("\r\n", $decodedString);

        foreach ($array as $key => $warehouse) {
            $data = array(
                "guia" => $warehouse,
                "codigo" => $statusArray[0],
                "estatus" => $statusArray[1],
                "observaciones" => "",
                "fecha" => date("Y-m-d h:i:s"),
                "latitud" => "13.699411",
                "longitud" => "-89.149612",
                "pod" => []
            );

            $json = json_encode($data);

            $this->send_status_api($json);
        }
        //  "fecha" => '2025-08-26 12:52:06', //date("Y-m-d h:i:s"),
        echo 1;
    }

    function procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total,$track="")
    {
        // Capturar los productos entre corchetes en la primera cadena
        preg_match_all('/\[(.*?)\]/', $cadena_productos, $matches_productos);
        $productos = $matches_productos[1]; // Extraer los productos sin los corchetes

        // Capturar las cantidades entre corchetes en la segunda cadena
        preg_match_all('/\[(\d+)\]/', $cadena_cantidades, $matches_cantidades);
        $cantidades = array_map('intval', $matches_cantidades[1]); // Convertir las cantidades a enteros

        if (empty($productos) || empty($cantidades)) {
            echo "No se encontraron productos o cantidades en las cadenas.";
            return [];
        }

        // Asegurarnos de que las cantidades y los productos coincidan en número
        if (count($productos) != count($cantidades)) {

            //echo $track;


            //var_dump($productos);
            //var_dump($cantidades[0]);
            //echo "La cantidad de productos y cantidades no coincide." .count($productos) ." ".count($cantidades);
            //return [];
        }

        // Total de unidades para calcular el precio por unidad
        $cantidad_total_productos = array_sum($cantidades);
        $precio_unitario = $precio_total / $cantidad_total_productos;

        $resultado = [];

        foreach ($productos as $index => $descripcion) {
            //$cantidad = $cantidades[$index];
            $cantidad = $cantidad_total_productos;

            $precio_por_producto = $precio_unitario * $cantidad; // Precio total proporcional para este producto

            $resultado[] = [
                'descripcion' => $descripcion,
                'cantidad' => $cantidad,
                'precio' => $precio_por_producto, // Precio total ya calculado
            ];
        }

        return $resultado;
    }



    function agruparProductosMA($productos)
    {
        $agrupados = [];

        $concat = "";
        $i = 1;
        $cantidad = 0;
        $precio = 0;

        foreach ($productos as $producto) {
            // Convertir a minúsculas para comparar sin importar mayúsculas/minúsculas
            $nombre_base = strtolower(preg_replace('/\([^\)]*\)/', '', $producto['descripcion']));


            $pos = strpos($concat, $nombre_base);

            if($pos === false){
                //Codigo agregado por Nico
                $concat = $concat . " " . $nombre_base;
            } //New code


            $cantidad = $producto['cantidad'];
            $precio = $producto['precio'];

            $i++;
        }

        $concat = strtoupper($concat);


        $agrupados["productToShow"]["descripcion"] = $concat;
        $agrupados["productToShow"]["cantidad"] = $cantidad;
        $agrupados["productToShow"]["precio"] = $precio;


        return array_values($agrupados); // Reindexar el array
    }

    public function procesarProductosConCantidadesIM($cadena_productos, $cadena_cantidades, $precio_total)
    {
        // Dividir los productos por el símbolo '+'
        $productos_raw = explode('+', $cadena_productos);

        $productos = [];
        $cantidades = [];
        $cantidad_total = 0;

        foreach ($productos_raw as $producto_raw) {
            // Extraer la descripción y la cantidad usando expresiones regulares
            if (preg_match('/(.+?)\s*\*\s*(\d+)/', $producto_raw, $matches)) {
                $descripcion = trim($matches[1]);
                $cantidad = intval($matches[2]);
                $productos[] = $descripcion;
                $cantidades[] = $cantidad;
                $cantidad_total = $cadena_cantidades;
            }
        }

        // Verificar si las cantidades coinciden con la suma indicada
        if ($cantidad_total !== $cadena_cantidades) {
            //echo "La suma de las cantidades no coincide con el valor proporcionado.\n";
            var_dump($cadena_productos);
            var_dump($cadena_cantidades);
            return [];
        }

        // Calcular el precio por unidad
        $precio_unitario = $precio_total / $cantidad_total;

        $resultado = [];

        foreach ($productos as $index => $descripcion) {
            $cantidad = $cantidades[$index];
            $precio_por_producto = $precio_unitario * $cantidad; // Precio total proporcional para este producto

            $resultado[] = [
                'descripcion' => $descripcion,
                'cantidad' => $cantidad,
                'precio' => $precio_por_producto, // Precio total ya calculado
            ];
        }

        return $resultado;
    }


    public function agruparProductosIM($productos)
    {
        $agrupados = [];

        foreach ($productos as $producto) {
            $nombre_base = trim($producto['descripcion']); // Usar el nombre directamente

            if (!isset($agrupados[$nombre_base])) {
                $agrupados[$nombre_base] = [
                    'descripcion' => $nombre_base,
                    'cantidad' => 0,
                    'precio' => 0,
                ];
            }

            // Sumar cantidades y precios directamente
            $agrupados[$nombre_base]['cantidad'] += $producto['cantidad'];
            $agrupados[$nombre_base]['precio'] += $producto['precio'];
        }

        return array_values($agrupados); // Reindexar el array
    }


    function review_letters($value)
    {
        return str_replace(
            [
                'á',
                'é',
                'í',
                'ó',
                'ú',
                'Á',
                'É',
                'Í',
                'Ó',
                'Ú',
                'ñ',
                'Ñ'
            ],
            [
                'A',
                'E',
                'I',
                'O',
                'U',
                'A',
                'E',
                'I',
                'O',
                'U',
                'N',
                'N'
            ],
            $value
        );
    }

    public function prorateo_manifiesto_gt($id)
    {
        $values_sum = $this->WhModel->getCifGt($id);

        $cif_sum = $values_sum["value"];
        $peso_sum = $values_sum["peso"];

        /* Calculo de totales*/

        //$flete = $_GET["flete"]; //flete
        $peso_recibido = $_GET["peso"]; //kg

        $flete = $peso_recibido * 4;

        $factor_flete = $flete / $cif_sum;

        $peso_a_proratear = $peso_recibido - $peso_sum;

        

        $fob_total = ($cif_sum - $flete) * 100 / 102.2;
        //$seguro = $fob_total * 0.022;

        //echo $fob_total." ".$seguro." ".$flete." ".$cif_sum;
        //die();



        

        echo "Fob Total $".$fob_total;

        //var_dump($cif_sum);

        $dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);


        $preclasificacion = $this->WhModel->datos_preclasificacion($id);

        //var_dump($preclasificacion);
        //die();

        $empresas = [112, 113, 114];

        $tm = 0;
        $res = 0;

        if (in_array($preclasificacion->client_id, $empresas)) {
            $tm = 1;
            $peso_a_agregar = $peso_a_proratear / count($dlist_manifiestos);
        }else{
            $cant_peso_a_agregar = $peso_a_proratear / 0.01;
            if($peso_a_proratear < 0){
                $res = 1;
            }

            $cant_peso_a_agregar = abs($cant_peso_a_agregar);
        }


       
        

        /*var_dump($values_sum);
        echo $peso_a_proratear;
        echo $peso_a_agregar;
        die();*/
        
        //echo " " . $peso_a_agregar;


        $suma_pesos = 0;
        $suma_fletes = 0;
        $suma_fob = 0; 
        $ultimo_paquete = '';


        $prorateados = 1;

        foreach ($dlist_manifiestos as $row) {

            if($tm == 1){
                $peso = number_format($peso_a_agregar + $row->gweight, 2);
            } else{


                if($prorateados <= $cant_peso_a_agregar){
                    if ($res == 0) {
                        $peso = number_format(0.01 + $row->gweight, 2);
                        $prorateados++;
                    } else {
                        echo "menos";
                        $peso = number_format(-0.01 + $row->gweight, 2);
                        $prorateados++;
                    }
                }else{
                    $peso = number_format($row->gweight, 2);
                }


                
  
                
            }   
            
            
            
            $suma_pesos = $suma_pesos + $peso;

            $ultimo_paquete = $row->tracking_number;
            $peso_ultimo_paquete = $peso;

            //$flete_x_paquete = number_format($peso * $flete / $peso_recibido, 2);

            //$fob_x_paquete = $row->value * $fob_total / $cif_sum;

            
            //echo $factor_flete." ";
            $fob_x_paquete = round((($row->value - ($row->value*$factor_flete)) / 1.022),2);
            $seguro_x_paquete = round($fob_x_paquete * 0.022,2);
            $flete_x_paquete = $row->value-$fob_x_paquete-$seguro_x_paquete;
            //echo $row->package_number . " " . $row->value . " " . $fob_x_paquete." ".$seguro_x_paquete. " ".$flete_x_paquete;            
            //die();
            //die();

            //$flete_x_paquete = number_format($peso * 4.00, 2);
            $suma_fletes = $suma_fletes + $flete_x_paquete;
            $suma_fob = $suma_fob + $fob_x_paquete;

            $this->WhModel->updateValuesGtTm(round($fob_x_paquete,2), round($flete_x_paquete,2), round($seguro_x_paquete,2), $peso, $row->tracking_number, $id);
        }

        echo "La sumatoria de pesos es : " . $suma_pesos . " Suma Fletes:  " . $suma_fletes;

        echo "La diferencia a agregarle al ultimo paquete seria: " . $ultimo_paquete . " " . number_format($peso_recibido - $suma_pesos, 2);

        $cantidad_paquetes_sumar = (int)round(($peso_recibido - $suma_pesos) / 0.01);
        $cantidad_paquetes_distribuir_flete = (int)(($suma_fletes - $flete) / 0.01);
        $cantidad_paquetes_distribuir_fob = (int)(($fob_total - $suma_fob) / 0.01);

        echo "Peso a distribuir ". ($peso_recibido - $suma_pesos);
        echo " Distribucion de fob ".($fob_total - $suma_fob);
        echo " Paquetes a distribuir flete ".$cantidad_paquetes_distribuir_flete;
        echo " Paquetes a distribuir peso " . $cantidad_paquetes_sumar;
        echo " Paquetes a distribuir fob " . $cantidad_paquetes_distribuir_fob;


        //die();

        $dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);

        $count_pk = 1;
        foreach ($dlist_manifiestos as $row) {

            if($cantidad_paquetes_sumar >= $count_pk){
                $peso_ajustado = $row->peso_prorateado_gt + 0.01;
                $this->WhModel->updatePesoPGtTm($peso_ajustado, $row->tracking_number, $id);
            }


            if ($cantidad_paquetes_distribuir_flete >= $count_pk) {
                $flete_ajustado = $row->flete_gt - 0.01;
                $this->WhModel->updateFletePGtTm($flete_ajustado, $row->tracking_number, $id);
            }


            if ($cantidad_paquetes_distribuir_flete >= $count_pk) {
                $fob_ajustado = $row->fob_gt + 0.01;
                $this->WhModel->updateFobPGtTm($fob_ajustado, $row->tracking_number, $id);
            }

            $count_pk++;
        }   


        
        //$peso_ultimo = number_format(($peso_recibido - $suma_pesos) + $peso_ultimo_paquete, 2);

        //echo "Peso ultimo " . $peso_ultimo_paquete;


        //$flete_x_paquete = $peso_ultimo * $flete / $peso_recibido;

        //$this->WhModel->updateValuesGtTm(round($fob_x_paquete,2), round($flete_x_paquete,2), round($seguro_x_paquete,2), $peso_ultimo, $ultimo_paquete, $id);


        //Process ok, modificacion de pesos

        //Calculo de flete ------------------------------

        die();

        
    }

    public function prorateo_manifiesto_gt_last($id)
    {
        $values_sum = $this->WhModel->getCifGt($id);

        $cif_sum = $values_sum["value"];
        $peso_sum = $values_sum["peso"];

        /* Calculo de totales*/

        //$flete = $_GET["flete"]; //flete
        $peso_recibido = $_GET["peso"]; //kg

        $flete = $peso_recibido * 4;

        $factor_flete = $flete / $cif_sum;

        $peso_a_proratear = $peso_recibido - $peso_sum;

        $fob_total = ($cif_sum - $flete) * 100 / 102.2;


        //echo $fob_total;

        //var_dump($cif_sum);

        $dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);

        $peso_a_agregar = $peso_a_proratear / count($dlist_manifiestos);

        /*var_dump($values_sum);
        echo $peso_a_proratear;
        echo $peso_a_agregar;
        die();*/
        
        //echo " " . $peso_a_agregar;


        $suma_pesos = 0;
        $suma_fletes = 0;
        $ultimo_paquete = '';
        foreach ($dlist_manifiestos as $row) {



            $peso = number_format($peso_a_agregar + $row->gweight, 2);
            $suma_pesos = $suma_pesos + $peso;

            $ultimo_paquete = $row->tracking_number;
            $peso_ultimo_paquete = $peso;

            //$flete_x_paquete = number_format($peso * $flete / $peso_recibido, 2);

            //$fob_x_paquete = $row->value * $fob_total / $cif_sum;

            
            echo $factor_flete." ";
            $fob_x_paquete = round((($row->value - ($row->value*$factor_flete)) / 1.022),2);
            $seguro_x_paquete = round($fob_x_paquete * 0.022,2);
            $flete_x_paquete = $row->value-$fob_x_paquete-$seguro_x_paquete;
            
            echo $row->package_number . " " . $row->value . " " . $fob_x_paquete." ".$seguro_x_paquete. " ".$flete_x_paquete;
            
            
            //die();
            //die();


            //$flete_x_paquete = number_format($peso * 4.00, 2);
            $suma_fletes = $suma_fletes + $flete_x_paquete;

            $this->WhModel->updateValuesGtTm(round($fob_x_paquete,2), round($flete_x_paquete,2), round($seguro_x_paquete,2), $peso, $row->tracking_number, $id);
        }

        echo "La sumatoria de pesos es : " . $suma_pesos . " Suma Fletes:  " . $suma_fletes;

        echo "La diferencia a agregarle al ultimo paquete seria: " . $ultimo_paquete . " " . number_format($peso_recibido - $suma_pesos, 2);

        $peso_ultimo = number_format(($peso_recibido - $suma_pesos) + $peso_ultimo_paquete, 2);

        echo "Peso ultimo " . $peso_ultimo_paquete;


        //$flete_x_paquete = $peso_ultimo * $flete / $peso_recibido;

        $this->WhModel->updateValuesGtTm(round($fob_x_paquete,2), round($flete_x_paquete,2), round($seguro_x_paquete,2), $peso_ultimo, $ultimo_paquete, $id);


        //Process ok, modificacion de pesos

        //Calculo de flete ------------------------------

        die();

        
    }


    

    public function prorateo_manifiesto_gt2($id){
            $values_sum = $this->WhModel->getCifGt($id);

            $cif_sum = $values_sum["value"];
            $peso_sum = $values_sum["peso"];


            

            /* Calculo de totales*/

            $flete = $_GET["flete"]; //flete
            $peso_recibido = $_GET["peso"]; //kg


            $peso_a_proratear = $peso_recibido - $peso_sum;
            
            $fob_total = ($cif_sum - $flete) * 100 / 102.2;


            //echo $fob_total;

            //var_dump($cif_sum);

            $dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);

            $peso_a_agregar = $peso_a_proratear / count($dlist_manifiestos);

            var_dump($values_sum);
            echo $peso_a_proratear;
            echo " ".$peso_a_agregar;


            $suma_pesos = 0;
            $suma_fletes = 0;
            $ultimo_paquete = '';
            foreach ($dlist_manifiestos as $row) {

                

                $peso = number_format($peso_a_agregar + $row->gweight,2);
                $suma_pesos = $suma_pesos + $peso;

                $ultimo_paquete = $row->tracking_number;
                $peso_ultimo_paquete = $peso;

                $flete_x_paquete = number_format($peso * $flete / $peso_recibido,2);

                $fob_x_paquete = $row->value * $fob_total / $cif_sum;
                $seguro_x_paquete = $fob_x_paquete * 0.022;


                //$flete_x_paquete = number_format($peso * 4.00, 2);
                $suma_fletes = $suma_fletes + $flete_x_paquete;

                $this->WhModel->updateValuesGtTm(number_format($fob_x_paquete, 2), number_format($flete_x_paquete, 2), number_format($seguro_x_paquete, 2), $peso, $row->tracking_number, $id);
            }

            echo "La sumatoria de pesos es : ".$suma_pesos. " Suma Fletes:  " . $suma_fletes;

            echo "La diferencia a agregarle al ultimo paquete seria: ".$ultimo_paquete." ".number_format($peso_recibido - $suma_pesos,2);

            $peso_ultimo = number_format(($peso_recibido - $suma_pesos)+$peso_ultimo_paquete, 2);

            echo "Peso ultimo ". $peso_ultimo_paquete;




            $flete_ultimo = number_format(($flete - $suma_fletes) + $flete_x_paquete,2);

            echo "Flete ultimo ".$flete_x_paquete;

            //$flete_x_paquete = $peso_ultimo * $flete / $peso_recibido;

            $this->WhModel->updateValuesGtTm(number_format($fob_x_paquete, 2), number_format($flete_ultimo, 2), number_format($seguro_x_paquete, 2), $peso_ultimo, $ultimo_paquete, $id);


            //Process ok, modificacion de pesos

            //Calculo de flete ------------------------------


            



            die();

        //var_dump($dlist_manifiestos);
        foreach ($dlist_manifiestos as $row) {

                    echo $row->value * $fob_total;


                    $fob_x_paquete = $row->value * $fob_total / $cif_sum;

                    //4 x cada kg
                    $flete_x_paquete = $row->value * $flete / $cif_sum;
                    
                    $seguro_x_paquete = $fob_x_paquete * 0.022;

                    $peso= number_format($peso_a_agregar + $row->gweight,2);
   
                    //echo $row->package_number." cif ".$row->value." fob: ".number_format($fob_x_paquete,2)." flete: ".number_format($flete_x_paquete,2). " seguro: " . number_format($seguro_x_paquete, 2);

                    
                    //die();


            
            }
            
    }

    public function verGD()
    {
        var_dump(extension_loaded('gd'));
    }

    public function verPHP(){
        phpinfo();
    }

    public function generar_copy_oea()
    {
        if (!isset($_FILES['xml'])) {
            //show_error("No se recibió ningún archivo.");
            //return;
            http_response_code(400);
            exit("No se recibió ningún archivo.");
        }



        $data_final = [];
        $total_xml = count($_FILES['xml']['name']);


        $pdfs = [];

        $xmlSinPdf = [];
        $pdfSinXml = [];

        // Validaciones básicas de extensiones
        // Revisar XMLs
        for ($i = 0; $i < $total_xml; $i++) {
            $name = isset($_FILES['xml']['name'][$i]) ? $_FILES['xml']['name'][$i] : '';
            if ($name === '') continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'xml') {

                http_response_code(400);
                exit("El archivo XML no tiene extensión .xml: {$name}");
            }
        }





        $xmls = [];

        foreach ($_FILES['xml']['tmp_name'] as $i => $tmpFile) {
            $nombreArchivo = $_FILES['xml']['name'][$i];

            if (!file_exists($tmpFile)) continue;

            $xmlContent = simplexml_load_file($tmpFile);

            //var_dump($xmlContent);
            //die();

            if (!$xmlContent) continue;

            // asegurar nodo correcto
            $declaracion = $xmlContent->declaracion ?? $xmlContent;

            // OEA
            $noDeclaracion = (string) $declaracion->noDeclaracion;
            $oea = preg_replace('/[^A-Z0-9]/', '', $noDeclaracion);

            // FECHA
            $fecha_sin_formato = (string) $declaracion->fechaValidacion;

            $fecha = DateTime::createFromFormat('j/n/Y', $fecha_sin_formato);

            $fecha = $fecha ? $fecha->format('Y/m/d') : '';

            // TIPO CAMBIO
            $tipoCambio = (string) $declaracion->tipoDeCambio;

            // fracciones
            $items = [];

            if (isset($declaracion->fraccion)) {

                foreach ($declaracion->fraccion as $f) {
                    $desc = (string) $f->descripcionMercancias;

                    preg_match('/\b(RR\d+XX|FD\d+)\b/', $desc, $m);

                    $tracking = $m[1] ?? '';

                    $items[] = [
                        'oea' => $noDeclaracion,
                        'tracking' => $tracking,
                        'descripcion' => trim(str_replace($tracking, '', $desc)),
                        'cif' => (float) $f->valorEnQuetzales,
                        'dai' => (float) $f->impuestoGuia
                    ];
                }
            }


            var_dump($items);
            die();

            $xmls[$oea] = [
                'archivo'      => $nombreArchivo,
                'oea'          => $noDeclaracion,
                'fecha'        => $fecha,
                'tipo_cambio'  => $tipoCambio,
                'items'        => $items
            ];
        }


        $data_final = [];



        foreach ($xmls as $oeaKey => $xml) {
            if (!isset($pdfs[$oeaKey])) {
                $xmlSinPdf[] = $xml['archivo'];
                continue;
            }

            foreach ($xml['items'] as $item) {
                $data_final[] = [
                    'tracking'     => $item['tracking'],
                    'cif'          => $item['cif'],
                    'dai'          => $item['dai'],
                    'cif_dai'      => $item['cif'] + $item['dai'],

                    'oea'          => $xml['oea'],
                    'fecha'        => $xml['fecha'],
                    'tipo_cambio'  => $xml['tipo_cambio']
                ];

                $impuestos = $item['cif'] + $item['dai'];
                $tracking = $item['tracking'];
            }
        }
    }

    

    public function generar_oea(){

        ini_set("display_errors",1);

        if (!isset($_FILES['xml'])) {
            //show_error("No se recibió ningún archivo.");
            //return;
            http_response_code(400);
            exit("No se recibió ningún archivo.");
        }



        $data_final = [];
        $total_xml = count($_FILES['xml']['name']);


        $pdfs = [];

        $xmlSinPdf = [];
        $pdfSinXml = [];

        // Validaciones básicas de extensiones
        // Revisar XMLs
        for ($i = 0; $i < $total_xml; $i++) {
            $name = isset($_FILES['xml']['name'][$i]) ? $_FILES['xml']['name'][$i] : '';
            if ($name === '') continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'xml') {

                http_response_code(400);
                exit("El archivo XML no tiene extensión .xml: {$name}");
            }
        }





        $xmls = [];

        foreach ($_FILES['xml']['tmp_name'] as $i => $tmpFile) {
            $nombreArchivo = $_FILES['xml']['name'][$i];

            if (!file_exists($tmpFile)) continue;

            //$xmlContent = simplexml_load_file($tmpFile);


            //$xml = simplexml_load_file($tmpFile);
            //var_dump($xml);
            //die();

            if (file_exists($tmpFile)) {
                $xml = simplexml_load_file($tmpFile);
            } else {
                // Si tienes el XML en una variable de texto, usa simplexml_load_string($xmlString);
                die("Error: No se encontró el archivo XML.");
            }

            /* 
            * NOTA: Ajusta las etiquetas del XML según la estructura exacta de tu archivo.
            * Por ejemplo, si las etiquetas son <declaracion><header><numero>...
            */


            // asegurar nodo correcto
            $declaracion = $xmlContent->declaracion ?? $xml;


            $fecha_sin_formato = (string) $declaracion->fechaValidacion;

            $clave = explode("-", $declaracion->nombreAduana);

            // Extraer encabezado (o asignar valores por defecto si vienen en nodos específicos)
            $headerData = [
                'numero_declaracion' => (string)($declaracion->noDeclaracion),
                'fecha'              => (string)($declaracion->fechaValidacion),
                'aduana'             => (string)($declaracion->nombreAduana),
                'regimen'            => (string)($declaracion->codigoRegimen),
                'pais_origen'        => (string)($declaracion->paisOrigen),
                'importador'         => (string)($declaracion->nombreEmpresaCourier),
                'nit'                => (string)($declaracion->nitImportador),
                'tipo_cambio'        => (string)($declaracion->tipoDeCambio),
                'no_factura'         => (string)($declaracion->noFactura),
                'fecha_factura'      => (string)($declaracion->fechaFactura),
                'selectivo'          => (string)($declaracion->resultadoAnalisisRiesgo),
                'guia_embarque'      => (string)($declaracion->guiaEmbarque),
                'usuario'            => (string)($declaracion->usuario),
                'fecha_declaracion'  => (string)($declaracion->fechaDeclaracion),
                'sub_total'      => (string)($declaracion->subTotal),
                'monto_iva'            => (string)($declaracion->montoIVA),
                'monto_total'  => (string)($declaracion->montoTotal),
                'clave_aduana'  => (string)($clave[0]),

            ];

            // Recorrer TODOS los ítems del XML sin límite
            $itemsHtml = '';
            $itemsHtml_pivote = '';
            $totalValor = 0;
            $totalTributos = 0;
            $itemIndex = 1;

           

            // Asumiendo estructura de nodos de ítems, por ejemplo: $xml->items->item o $xml->item
            $itemsNode = $declaracion->fraccion;


            $total_lineas = count($itemsNode);
            $cantidad_paginas = ($total_lineas - 14) / 18;
            $isDecimal = 0;
           

            if ($cantidad_paginas != floor($cantidad_paginas)) {
                $cantidad_paginas = floor($cantidad_paginas) + 2;
                $isDecimal = 1;
            } else {
                $cantidad_paginas = floor($cantidad_paginas) + 1;
            }




            $pagesInc = 0;
            $contItemsAcumulado = 0;

            $itemsHtmlMatriz = array();

            foreach ($itemsNode as $item) {


               //echo $total_lineas ." ". $contItemsAcumulado."<br>";


                $bultos      = (string)($item->numeroBultos ?? '1');
                $medida      = (string)($item->cantidadUnidadMedida ?? '1 KGM');
                $descripcion = htmlspecialchars((string)($item->descripcionMercancias ?? ''));
                $inciso      = (string)($item->incisoArancelario ?? '');
                $valor       = (float)($item->valorEnQuetzales ?? 0);
                $tasa        = (string)($item->tasaArancelaria ?? '15');
                $tributos    = (float)($item->impuestoGuia ?? 0);

                $totalValor += $valor;
                $totalTributos += $tributos;

                if($itemIndex <= 14 && $pagesInc == 0){

                        $itemsHtml .= '
                        <tr>
                            <td >' . $bultos . '</td>
                            <td >' . $medida . '</td>
                            <td style="height: 20px;">' . $descripcion . '</td>
                            <td >' . $inciso . '</td>
                            <td >' . number_format($valor, 2, '.', ',') . '</td>
                            <td >' . $tasa . '</td>
                            <td >' . number_format($tributos, 2, '.', ',') . '</td>
                        </tr>';
                        
                
                        if($itemIndex == 14){
                            $itemIndex = 0;
                            $pagesInc = $pagesInc + 2;
                        }

                    $itemIndex++;
                }elseif($pagesInc >= 1){

                    //echo $pagesInc." ".$cantidad_paginas."\n";
                  
                    $itemsHtml_pivote .= '
                        <tr>
                            <td >' . $bultos . '</td>
                            <td >' . $medida . '</td>
                            <td style="height: 20px;">' . $descripcion . '</td>
                            <td >' . $inciso . '</td>
                            <td >' . number_format($valor, 2, '.', ',') . '</td>
                            <td >' . $tasa . '</td>
                            <td >' . number_format($tributos, 2, '.', ',') . '</td>
                        </tr>';

                    
                    if ($itemIndex == 18) {
                        $itemIndex = 0;
                        
                        $itemsHtmlMatriz[$pagesInc] = $itemsHtml_pivote;
                        $itemsHtml_pivote = "";
                        $pagesInc++;
                    }

                    

                    if($isDecimal && $pagesInc == $cantidad_paginas && $total_lineas - 1 == $contItemsAcumulado){
                        $itemIndex = 0;
                        
                        $itemsHtmlMatriz[$pagesInc] = $itemsHtml_pivote;
                        $itemsHtml_pivote = "";
                        $pagesInc++;
                    }

                    $itemIndex++;
                }

                $contItemsAcumulado++;

            }

            
            //die();
            //var_dump($itemsHtmlMatriz);
            //die();
            // Formatear Totales
            $subtotalFormatted = number_format($totalValor, 2, '.', ',');
            $ivaFormatted      = number_format($totalTributos, 2, '.', ',');
            $totalFormatted    = number_format($totalValor + $totalTributos, 2, '.', ',');

            $logo = getcwd() . '/public/img/sat.png';
            $logo = base64_encode(file_get_contents($logo));
            $logo = 'data:image/png;base64,' . $logo;
            


            // 2. CONSTRUCCIÓN DEL HTML PRIMERA PAGINA PARA DOMPDF
            $html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Declaración Aduanera de Oficio ' . $headerData['numero_declaracion'] . '</title>
    
</head>
<body>
    <!-- Encabezado Institucional -->
    <table style="margin-bottom: 15px;">
        <tr>
            <td style="width: 15%; text-align: right;" >
                <img src="' . $logo . '" style="width: 70%; ">
            </td>
            <td style="padding:0px; text-align:center; font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;vertical-align:top;padding-top: 4px;">
                SUPERINTENDENCIA DE ADMINISTRACIÓN TRIBUTARIA<br>
                DECLARACIÓN ADUANERA DE OFICIO
            </td>
        
        
        </tr>
    </table>

    <!-- Cuadro de Datos Principales de la Declaración -->
    <table class="table-data">
        <tr>
            <td width="50%" style="height: 20px; vertical-align: bottom;"><span class="bold">1. Número de Declaración: </span>' . $headerData['numero_declaracion'] . '</td>
            <td width="50%" style="height: 20px; vertical-align: bottom;"><span class="bold">2. Fecha: </span>' . $headerData['fecha'] . '</td>
        </tr>
        <tr>
            <td width="50%"><span class="bold">3. Aduana: </span>' . $headerData['aduana'] . '</td>
            <td width="50%"><span class="bold"></td>
        </tr>
        <tr>
            <td width="50%"><span class="bold">4. Regimen: </span>' . $headerData['regimen'] . '</td>
            <td width="50%"><span class="bold">5. Pais de Origen: </span>' . $headerData['pais_origen'] . '</td>   
        </tr>
        <tr>
            <td width="50%"><span class="bold">6. Nombre del importador: </span>' . $headerData['importador'] . '</td>
            <td width="50%"><span class="bold"></td>
        </tr>
        <tr>
            <td><span class="bold">7. Tipo de Cambio: </span>' . $headerData['tipo_cambio'] . '</td>
            <td><span class="bold">8. NIT: </span>' . $headerData['nit'] . '</td>
        </tr>
        <tr>
            <td><span class="bold">9. No. Factura: </span>' . $headerData['no_factura'] . '</td>
            <td><span class="bold">10. Fecha de Factura: </span>' . $headerData['fecha_factura'] . '</td>
        </tr>
        <tr>
            <td style="height: 20px; vertical-align: top;"><span class="bold">11. Selectivo: </span>' . $headerData['selectivo'] . '</td>
            <td style="height: 20px; vertical-align: top;"><span class="bold">12. Guía de Embarque: </span>' . $headerData['guia_embarque'] . '</td>
            
        </tr>
    </table>

    <!-- Tabla de Ítems Completa -->
    <table class="table-items">
        <thead>
            <tr>
                <th width="7%">13. No. Bultos</th>
                <th width="10%">14. Cant y Ud Medida</th>
                <th width="43%" style="text-align:justify;">15. Descripción de Mercancías</th>
                <th width="10%">16. Inciso Arancelario</th>
                <th width="10%">17. Valor Quetzales</th>
                <th width="5%">18. Tasa</th>
                <th width="15%">19. Tributos Quetzales</th>
            </tr>
        </thead>
        <tbody>
            ' . $itemsHtml . '
        </tbody>
    </table>

    <table style="
    border: 1px solid black;
    border-collapse: collapse;
    width: 100%;
    margin: 0;
    padding: 0;
">
    <tr style="margin: 0; padding: 0;">
        
        <td style="
            width: 70%;
            height: 70px;
            text-align: center;
            vertical-align: bottom;
            padding: 0 4px 4px 4px;
            border: 1px solid black;
        ">
            20. Firma de Funcionario que Formula la Declaración.
        </td>

        <td style="
            width: 30%;
            height: 70px;
            background: #999;
            padding: 4px;
            font-size: 10px;
            border: 1px solid black;
            vertical-align: middle;
        ">
            <span>21. SUBTOTAL: Q ' . $headerData['sub_total'] . '</span><br>
            <span>22. IVA: Q ' . $headerData['monto_iva'] . '</span><br>
            <span>23. TOTAL: Q ' . $headerData['monto_total'] . '</span>
        </td>

    </tr>
</table>

    <!-- Datos de Auditoría SAT -->
    <div style="float: right; text-align: right;">
        <span class="bold">Usuario:</span> ' . $headerData['usuario'] . '<br>
        <span class="bold">Fecha de Declaración:</span> ' . $headerData['fecha_declaracion'] . '
    </div>

</body>
</html>';

$html2 = "";


$count = 1;
foreach($itemsHtmlMatriz as $items){
        
    
                //die();

                $html2 .= '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Declaración Aduanera de Oficio ' . $headerData['numero_declaracion'] . '</title>
    <style>
        @page {
            margin: 20px 20px 30px 20px;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5px;
            color: #000;
            line-height: 1.15;
        }
        
        /* Encabezado Principal */
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        /* Tablas de Estructura */
        .table-data {
            border: 1px solid black;
            width: 100%;
            border-collapse: collapse;
        }
        .table-data td {
            border: none;
            padding: 6px 10px;
            vertical-align: top;
        }
        
        /* Tabla de Ítems / Mercancías */
        .table-items {
            width: 100%;
            border-collapse: collapse;
            
        }
        .table-items thead {
            display: table-header-group; /* Repite el encabezado al saltar de página */
        }
        .table-items tr {
            page-break-inside: avoid; /* Evita que una fila se corte a la mitad */
        }
        .table-items th {
            background-color: #b9b9b9;
            border: 1px solid #000;
            padding: 2px 2px;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
        }
        .table-items td {
            border: 1px solid #000;
            padding-bottom: 5px;
            font-size: 7px;
        }
        
        /* Alineaciones de Texto */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        
                        
    

        
    </style>
</head>
<body>
    <!-- Encabezado Institucional -->
    <div class="header-title">
        
        INTENDENCIA DE ADUANAS<br>
        HOJA ADICIONAL DE DECLARACION ADUANANERA DE OFICIO
    </div>

    <!-- Cuadro de Datos Principales de la Declaración -->
    <table class="table-data">
        <tr style="border: 1px solid black;">
            <td width="50%"><span class="bold">1. Hoja: </span> '.$count.' de '.($cantidad_paginas - 1).'</td>
            <td width="50%"><span class="bold">2. No. de Declaración:</span> '. $headerData['numero_declaracion'].' </td>
            <td width="50%"><span class="bold">3. Clave de Aduana: </span>' . $headerData['clave_aduana'] . '</td>
        </tr>
        <tr>
            <td width="50%"><span class="bold">4. Nombre del importador: </span>' . $headerData['importador'] . '</td>
            <td width="50%"><span class="bold">5. NIT: </span>' . $headerData['nit'] . '</td>
            <td width="50%"><span class="bold">6. Clave de régimen: </span>' . $headerData['regimen'] . '</td>
        </tr>
    </table>

    <!-- Tabla de Ítems Completa -->
    <table class="table-items">
        <thead>
            <tr>
                <th width="7%">13. No. Bultos</th>
                <th width="10%">14. Cant y Ud Medida</th>
                <th width="43%" style="text-align:justify;">15. Descripción de Mercancías</th>
                <th width="10%">16. Inciso Arancelario</th>
                <th width="10%">17. Valor Quetzales</th>
                <th width="5%">18. Tasa</th>
                <th width="15%">19. Tributos Quetzales</th>
            </tr>
        </thead>
        <tbody>
            '.$items. '
        </tbody>
    </table>

    <table style="border: 1px solid black; width: 100%; height: 40px; padding: 0px; margin: 0px; ">
        <tr style="padding: 0px; height: 20px; margin:0px;">
            <td style="height: 20px;  padding-top: 40px;"><br>Observaciones: </td>
            
        </tr>
        <tr style="padding: 0px; height: 20px; margin:0px;">
            <td style="height: 20px; text-align:center; padding-top: 40px;">20. Firma de Funcionario que Formula la Declaración.</td>
            
        </tr>
    </table>

    

</body>
</html>';

$count++;
}


           

            $html = $html . $html2;
            // Configuración e Instancia de Dompdf
            $this->load->library('pdf');

            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            //$options->set('isPhpEnabled', true);
            $options->set('dpi', 72);


            $this->pdf = new \Dompdf\Dompdf($options);

            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'portrait');
            $this->pdf->render();

            $this->pdf->stream('reporte.pdf', ['Attachment' => 0]);
            exit;


        }
        
        
    }

    public function generar_facturas_new($id, $referencia)
    {
        //phpinfo();
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        ini_set('memory_limit', '2048M'); // 2 GB

        //include getcwd() . "/application/libraries/phpqrcode/qrlib.php";

        $this->load->library('pdf');

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('dpi', 72);


        $html = "";

        $id_manifiesto = $id;

        $preclasificacion = $this->WhModel->datos_preclasificacion($id);

        //var_dump($preclasificacion);
        //die();

        $empresas = [112, 113, 114];

        $tm = 0;

        if (in_array($preclasificacion->client_id, $empresas)) {

            //$busqueda = $referencia;


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$id/$referencia",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $result = json_decode($response);
            $fecha_send = "";
            if (isset($result->data->details)) {
                $dlist_manifiestos  = $result->data->details;
                $fecha_send = $result->data->header->fecha;
                //$this->datos['client_id'] = $preclasificacion->client_id;
            } else {
                $dlist_manifiestos = [];
            }

            $tm = 1;

            if (in_array($preclasificacion->client_id, $empresas)) {
                //Get products by tracking using ma.id
                $products = $this->WarehouseModel->getTemuProductSVAll($id);


                

                //$products = $this->Aduana_Model->getProductosByTrackingTM($row->id);
                $agrupados = [];
                foreach ($products as $key) {

                    $agrupados[$key->id_tracking][] = array(
                        'descripcion' => $key->items_description,
                        'cantidad' => $key->items_quantities,
                        'precio' => $key->cif,
                    );
                }
            }


            



            /*$encontrado = null;
            $new_array = array();

            foreach ($dlist_manifiestos as $item) {


                if ($item->referencia == $busqueda) {
                    //$encontrado = $item;
                    $new_array[] = $item;

                    //var_dump($item);
                    //break; // Detiene la búsqueda inmediatamente al encontrar el primero
                }
            }*/

            //$dlist_manifiestos = $new_array;
        } else {
            //$fecha_send = $preclasificacion->fecha;
            $dlist_manifiestos        = $this->WhModel->lista_warehouse1($id, $referencia);
        }




        // Obtener los datos del manifiesto
        //$dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);



        // Crear la carpeta del manifiesto
        $manifiesto = "";
        if (!empty($dlist_manifiestos)) {
            //$manifiesto = $dlist_manifiestos[0]->manifiesto; // Usar el nombre del manifiesto
            $manifiesto = $preclasificacion->manifiesto;
        }


        $destino_base = getcwd() . "/document/Manifiesto/";



        $destino_carpeta = $destino_base . "SV" . $manifiesto . '/';
        if (!is_dir($destino_carpeta)) {
            mkdir($destino_carpeta, 0777, true);
        }

        $zip_file = $destino_base . "M" . $manifiesto . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            foreach ($dlist_manifiestos as $row) {

                $this->pdf = new \Dompdf\Dompdf($options);

                $tracking_number = ($tm) ? $row->package_number : $row->tracking_number;
                //$origen = $row->pais_origen;

                $direccion_provee = "";

                $qr_flag = "display:none;";

                // Encabezado y diseño inicial
                if ($row->id_empresa == 103 ||  $row->id_empresa == 104 || $row->id_empresa == 101 || $row->id_empresa == 100) {


                    //if ($row->client_id == "AliExpress") {
                    $logo = getcwd() . '/public/img/Aliexpress2026.png';
                    $direccion_provee = "No. 111 Tianlin Road, Tower, Shangai, China.";
                    $color_style = "#FF2650";
                    /*} else {
                        $logo = getcwd() . '/public/img/Ebay2026.png';;
                        $direccion_provee = 'First Floor, 25 St Stephens Green, Shangai';
                        $color_style = "#116DF6";  
                    }*/

                    $fecha_send = $row->fecha;
                }
                if ($row->id_empresa == 113 || $row->id_empresa == 114 || $row->id_empresa == 112) {

                    //$logo = FCPATH."public/img/temu.png";

                    //echo $logo;
                    //die();
                    

                    $qr_flag = "display:;";
                    $logo = getcwd() . '/public/img/temu.png';
                    $direccion_provee = '28th Floor, No. 533 Loushanguan Road, Changning District, Shanghai, 200051, China. ';
                    $color_style = "#F67200";
                }




                $nombre_cliente = $this->review_letters($row->consignee);
                $direccion_cliente = $this->review_letters($row->consignee_address1);
                $telefono_cliente = $row->consignee_phone;
                $documento_cliente = $row->consignee_account;
                $fecha_factura = date("d-m-Y", strtotime($fecha_send));

                ini_set('display_errors', 1);

                $url = "https://mistarship.com/watch_details/productos/" . $row->id . "/" . $id;

                


                // Crear archivo temporal
                //$tempDir = sys_get_temp_dir();
                //$filePath = $tempDir . DIRECTORY_SEPARATOR . 'qr_' . md5($url) . '.png';

                // Generar QR local
                /*if (!file_exists($filePath)) {
                    QRcode::png(
                        $url,            // contenido
                        $filePath,       // archivo
                        QR_ECLEVEL_L,    // corrección de errores
                        4,               // tamaño
                        1                // margen
                    );
                }*/


                $cadena_descripcion = $row->commodity;

                $cadena_productos = $row->commodity;


                $precio_total = $row->value; // Precio total

                // validar a que empresa pertenece el manifiesto
                $verificar_empresa        = $this->WhModel->verificar_empresa($id);
                $tipo = $verificar_empresa->tipo_factura;

                if ($tipo == "MA") {
                    //$cadena_cantidades = $row->items_quantities;
                    
                    //$resultado = $this->procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total,$row->tracking_number);
                    //$agrupados = $this->agruparProductosMA($resultado);
                }
                if ($tipo == "IM") {

                    $cadena_cantidades = $row->items;
                    $resultado = $this->procesarProductosConCantidadesIM($cadena_productos, $cadena_cantidades, $precio_total);
                    $agrupados = $this->agruparProductosIM($resultado);
                }

                if($tipo == "TM") {

                    
                
                }


                






                //$name_qr =  "https://www.temu.com/goods.html?_bg_fs=1&goods_id=601105205757323";

                $incoterm = ($tm) ? "FOB" : "FOB";
                $puerto = "San Salvador";
                $despacho = "Courier Int.";

                $metodo_pago = "Tarjeta";
                //$id_orden = "PO-061-0472533";

                //$qr = FCPATH . "uploads/qr.png";


                if ($row->id_empresa != 113 && $row->id_empresa != 114 && $row->id_empresa != 112) {
                    //$productos = $agrupados;
                }



                //$filePath = 'C:\Users\DESARR~1\AppData\Local\Temp\qr_f4a26035b42e107c04e9310a5940cc7d.png';

                //$imageData = base64_encode(file_get_contents($filePath));

                //$qrImage = 'data:image/png;base64,' . $imageData;



                $logo = base64_encode(file_get_contents($logo));
                $logo = 'data:image/png;base64,' . $logo;


                $html .= '
            <html>
            <head>
            <meta charset="utf-8">

            <style>

            body{
                font-family: DejaVu Sans, sans-serif;
                font-size:11px;
                margin:0;
                padding:0;
                background:#ffffff;
                color:#333;
            }

            .wrapper{
                
            }

            .header{
                color:#fff;
                padding:5px;
            }

            .logo{
                font-size:42px;
                font-weight:bold;
                padding: 5%;
            }

            .subtitulo{
                font-size:11px;
                letter-spacing:2px;
                color: ' . $color_style . ';
            }

            .tracking{
                float:right;
                padding:10px 20px;
                text-align:center;
                margin-top: -100px;
                color: ' . $color_style . ';
            }

            .tracking strong{
                font-size:19px;
                
            }

            .date-div{
                width: 100%;
                text-align:center;
                padding: 10px;
            }

            .date-div strong{
                font-size: 15px;
                color: ' . $color_style . ';
                
            }

            .contenido{
                padding:15px;
            }

            .alerta{
                background:#faf4ef;
                border-left:4px solid ' . $color_style . ';
                padding:10px;
                color:#666;
                margin-bottom:20px;
            }

            .info{
                width:100%;
                border-spacing:10px;
            }

            .card{
                border:1px solid #dcdcdc;
                border-radius:5px;
                padding:10px;
                vertical-align:top;
                height:130px;
            }

            .card-title{
                color: ' . $color_style . ';
                font-weight:bold;
                margin-bottom:10px;
                border-bottom:1px solid #ddd;
                padding-bottom:5px;
            }

            .seccion{
                background:#304457;
                color:white;
                padding:10px;
                margin-top:20px;
                font-weight:bold;
            }

            .detalle{
                width:100%;
                border-collapse:collapse;
            }

            .detalle th{
                background:#f1f3f6;
                border:1px solid #ddd;
                padding:5px;
                font-size:9px;
            }

            .detalle td{
                border:1px solid #ddd;
                padding:3px;
                font-size: 8px;
                vertical-align:middle;
            }

            .descripcion{
                width:48%;
            }

            .cantidad{
                width:10%;
                text-align:center;
            }

            .precio{
                width:15%;
                text-align:right;
            }

            .total{
                width:15%;
                text-align:right;
            }

            .qr{
                width:12%;
                text-align:center;
            }

            /*.badge{
                display:inline-block;
                background:#e5e8ec;
                border-radius:20px;
                padding:4px 10px;
                font-weight:bold;
            }*/

            .subdetalle{
                color:#6d6d6d;
                font-size:10px;
                margin-top:5px;
            }

            .resumen{
                width:300px;
                float:right;
                margin-top:25px;
                border:1px solid #ddd;
                padding:15px;
            }

            .total-final{
                color: ' . $color_style . ';
                font-size:10px;
                font-weight:bold;
            }

            .footer{
                text-align:center;
                color:#888;
                font-size:10px;
                margin-top:100px;
            }

            </style>
            </head>
            <body>

            <div class="wrapper">

            <div class="header">

                <div class="logo"><img src="' . $logo . '" style="width: 20%;"/></div>
            
                <div class="tracking">
                    TRACKING NO<br>
                    <strong>' . $tracking_number . '</strong>
                </div>

            </div>

            <div class="date-div">
                    <strong> Fecha factura: ' . $fecha_factura . '</strong>
            </div>

            <div class="contenido">

           ';

            /*if($tm){
                    $html .= '<div style="text-align:center; font-weight: bold;">Enlace productos: ' . $url . '</div>';
            }*/

            $html .= '<table class="info">
            <tr>

            <td colspan="2" class="card">
                <div class="card-title">INFORMACIÓN DEL ENVÍO</div>

                <table width="100%" cellpadding="4" cellspacing="0">
                    <tr>
                        <td><b>Cliente:</b> ' . strtoupper($nombre_cliente) . '</td>
                        <td><b>DUI:</b> ' . $documento_cliente . '</td>
                        <td><b>Teléfono:</b> ' . $telefono_cliente . '</td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Dirección:</b> ' . strtoupper($direccion_cliente) . '</td>
                        <td><b>Incoterm:</b> ' . $incoterm . '</td>
                    </tr>
                    <tr>
                        <td colspan="3"><b>Despacho:</b> ' . $despacho . '</td>
                    </tr>
                </table>
            </td>

            

            </tr>
            </table>

            <table class="detalle">

            <tr>
                <th>DESCRIPCIÓN DEL PRODUCTO</th>
                <th>CANT.</th>
                <th>PRECIO UNIT.</th>
                <th>TOTAL</th>
                
            </tr>';

                $subtotal = 0;
                if ($row->id_empresa != 113 && $row->id_empresa != 114 && $row->id_empresa != 112) {

                    //foreach ($productos as $item) {

                        $total = $row->value;
                        $subtotal += $row->value;

                        $html .= '

                    <tr>

                        <td class="descripcion">
                            <strong>' . strtoupper($this->review_letters($row->commodity)) . '</strong>
                        </td>

                        <td class="cantidad">
                            <span class="badge">' . $row->pieces . '</span>
                        </td>

                        <td class="precio">
                            $-
                        </td>

                        <td class="total">
                            $' . number_format($row->value, 2) . '
                        </td>

                        

                    </tr>';
                    //}
                } else {

                    if ($tm) {
                        $agrupados_new = $agrupados[$row->id];

                        //var_dump($agrupados_new);

                        //die();

                    } else {
                        $agrupados_new = $agrupados;
                    }


                    foreach ($agrupados_new as $item) {

                        $total = number_format($item['precio'] / 1.115,2) * $item['cantidad'];
                        
                        $subtotal += $total;

                        $html .= '

                    <tr>
                    <td class="descripcion">

                            <strong>' . $this->review_letters($item['descripcion']) . '</strong>
                        </td>

                        <td class="cantidad">
                            <span class="badge">' . $item['cantidad'] . '</span>
                        </td>

                        <td class="precio">
                            $' . number_format($item['precio'] / 1.115,2). '
                        </td>

                        <td class="total">
                            $' . number_format($total, 2) . '
                        </td>

                        

                    </tr>';
                    }
                    
                }

                $html .= '

            </table>

            <div class="resumen">

                ';

                if ($tm == 0) {
                    $html .= '<table width="100%">
                    <tr>
                        <td>Subtotal de Artículos:</td>
                        <td align="right">$' . number_format($subtotal, 2) . '</td>
                    </tr>
                </table>

                <br>';
                }

                if (0) {


                    //echo $row->id." ".$id;


                    $gt_value = $this->WhModel->obtener_gt_por_id($row->id, $id);


                    $html .= '
                
                    <table width="100%">
                    <tr>
                        <td>FOB:</td>
                        <td align="right">
                            $ ' . $gt_value->fob_gt . '
                        </td>
                    </tr>
                </table>
                 <br>

                <table width="100%">
                    <tr>
                        <td>FLETE:</td>
                        <td align="right">
                            $ ' . $gt_value->flete_gt . '
                        </td>
                    </tr>
                </table>
                 <br>

                <table width="100%">
                    <tr>
                        <td>SEGURO:</td>
                        <td align="right">
                            $ ' . $gt_value->seguro_gt . '
                        </td>
                    </tr>
                </table>

                <br>
                
                ';
                }


                $html .= '

                <table width="100%">
                    <tr>
                        <td class="total-final">Monto ' . $incoterm . ' Total:</td>
                        <td align="right" class="total-final">
                            $' . number_format($subtotal, 2) . '
                        </td>
                    </tr>
                </table>

            </div>

            

            </div>
            </div>

            </body>
            </html>';





                /*$this->pdf->loadHtml($html);
                $this->pdf->setPaper('A4', 'portrait');
                $this->pdf->render();
                $this->pdf->stream('reporte.pdf', ['Attachment' => 0]);

                die();*/



                /*$this->pdf->loadHtml($html);
                $this->pdf->setPaper('A4', 'portrait');
                $this->pdf->render();

                // Guardar PDF temporal
                $pdfName = $tracking_number . "_2.pdf";
                $pdfPath = $destino_carpeta . "/". $pdfName;

                file_put_contents(
                    $pdfPath,
                    $this->pdf->output()
                );

                unset($this->pdf);*/

                


            }

            //$zip->close();
        }


        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();
        $this->pdf->stream('reporte.pdf', ['Attachment' => 0]);
        exit;


        // Comprimir la carpeta en un archivo ZIP
        //$zip_file = $destino_base . "M" . $manifiesto . '.zip';
        //echo $zip_file;

        /*$zip = new ZipArchive();
        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = scandir($destino_carpeta);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $zip->addFile($destino_carpeta . $file, $file);
                }
            }
            $zip->close();
        }*/

        // Descargar automáticamente el archivo ZIP
        //header('Content-Type: application/zip');
        //header('Content-Disposition: attachment; filename="' . "M".$manifiesto . '.zip"');

        //	header('Content-Length: ' . filesize($zip_file));
        //	readfile($zip_file);

        // Eliminar el archivo ZIP después de la descarga
        //unlink($zip_file);


        $desabsoluto  =  "document/Manifiesto/";

        $nombre_archivo =   "M" . $manifiesto . ".zip";

        $datosretorno  = array(
            'destino'        => $desabsoluto,
            'nombre_archivo' => $nombre_archivo,

        );


        echo json_encode($datosretorno);
        exit;
    }

    public function limpiarTexto($texto)
    {
        // Si detecta caracteres típicos de mala codificación
        if (preg_match('/Ã|Â|â/', $texto)) {
            $convertido = @utf8_decode($texto);

            // Solo reemplaza si la conversión no produjo caracteres inválidos
            if (strpos($convertido, '�') === false) {
                return $convertido;
            }
        }

        return $texto;
    }

    public function generar_manifiesto_pdf_x_lote($id)
    {
        //phpinfo();
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        include getcwd() . "/application/libraries/phpqrcode/qrlib.php";

        $this->load->library('pdf');

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $id_manifiesto = $id;

        $preclasificacion = $this->WhModel->datos_preclasificacion($id);

        $empresas = [112, 113, 114];

        $tm = 0;

        $boxes = $this->WhModel->qtys_by_box($id);

        //var_dump($boxes);

        $limite = 500;
        $acumulado = 0;
        $count_boxes = 1;

        $cajasCorte = [];


        $totalPaquetes = 0;

        //var_dump($boxes);
        
        foreach ($boxes as $caja) {

            $cantidad = (int)$caja->total;

            if (($acumulado + $cantidad) > $limite) {


                $caja->acumulado = $acumulado;

                // Esta caja será la primera del siguiente lote
                $cajasCorte[] = $caja;

                // Reiniciar el acumulado comenzando con esta caja
                $acumulado = $cantidad;
            } else {

                $acumulado += $cantidad;
            }

            $totalPaquetes += $cantidad;

            if (count($boxes) == $count_boxes) {
                //echo "Ultima caja ".$acumulado;
                //echo "Total paquetes".$totalPaquetes;
            }
            $count_boxes++;
        }


        //var_dump($cajasCorte);
        //die();

        if (in_array($preclasificacion->client_id, $empresas)) {

            //$busqueda = $referencia;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $result = json_decode($response);
            $fecha_send = "";
            if (isset($result->data->details)) {
                $dlist_manifiestos  = $result->data->details;
                $fecha_send = $result->data->header->fecha;
                //$this->datos['client_id'] = $preclasificacion->client_id;
            } else {
                $dlist_manifiestos = [];
            }

            $tm = 1;


            /*$encontrado = null;
            $new_array = array();

            foreach ($dlist_manifiestos as $item) {


                if ($item->referencia == $busqueda) {
                    //$encontrado = $item;
                    $new_array[] = $item;

                    //var_dump($item);
                    //break; // Detiene la búsqueda inmediatamente al encontrar el primero
                }
            }*/

            //$dlist_manifiestos = $new_array;
        } else {
            $dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);
        }


        


        // Obtener los datos del manifiesto
        //$dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);



        // Crear la carpeta del manifiesto
        $manifiesto = "";
        if (!empty($dlist_manifiestos)) {
            //$manifiesto = $dlist_manifiestos[0]->manifiesto; // Usar el nombre del manifiesto
            $manifiesto = $preclasificacion->manifiesto;
        }


        $destino_base = getcwd() . "/document/Manifiesto/";
        $destino_carpeta = $destino_base . "GT" . $manifiesto . '/';
        if (!is_dir($destino_carpeta)) {
            mkdir($destino_carpeta, 0777, true);
        }

        $zip_file = $destino_base . "M" . $manifiesto . '.zip';
        $zip = new ZipArchive();

        $numeroLote = 1;
        $loteActivo = 0;
        $cajaCorteActiva = 0;

        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            $contador = 0;
            $y = 0;


            $tracking_pro = array();

            //var_dump($cajasCorte);

            foreach ($dlist_manifiestos as $row) {

                $contador++;

                $tracking_pro[] = $row->package_number;

                
                //echo "Fuera Lote " . $numeroLote . " Activo " . $loteActivo . "\n";

                if($tm == 1){
                    if (in_array($row->bag_number, array_column($cajasCorte, 'bag_number')) && $cajaCorteActiva != $row->bag_number) {
                        $numeroLote++;
                        $loteActivo = 1;
                        $cajaCorteActiva = $row->bag_number;
                        //$itemPosition = 0;
                    } else {
                        $loteActivo = 0;
                        //$cajaCorteActiva = null;
                    }
                }else{
                    if ($y == 500) { //Cambio de lote
                        $numeroLote++;
                        $loteActivo = 1;
                        $y = 0;
                    } else {
                        $loteActivo = 0;
                    }
                }




                $y++; //Solo para MA

                //echo $tm;
                //die();

                $carpeta = 'Lote_' . str_pad($numeroLote, 3, '0', STR_PAD_LEFT);

                $this->pdf = new \Dompdf\Dompdf($options);

                $tracking_number = ($tm) ? $row->package_number : $row->tracking_number;
                //$origen = $row->pais_origen;

                $direccion_provee = "";

                $qr_flag = "display:none;";

                // Encabezado y diseño inicial
                if ($row->id_empresa == 103 ||  $row->id_empresa == 104 || $row->id_empresa == 101) {


                    //if ($row->client_id == "AliExpress") {
                    $logo = getcwd() . '/public/img/Aliexpress2026.png';
                    $direccion_provee = "No. 111 Tianlin Road, Tower, Shangai, China.";
                    $color_style = "#FF2650";
                    /*} else {
                        $logo = getcwd() . '/public/img/Ebay2026.png';;
                        $direccion_provee = 'First Floor, 25 St Stephens Green, Shangai';
                        $color_style = "#116DF6";  
                    }*/

                    $fecha_send = $row->fecha;
                }
                if ($row->id_empresa == 113 || $row->id_empresa == 114 || $row->id_empresa == 112) {

                    $qr_flag = "display:;";
                    $logo = getcwd() . '/public/img/temu.png';;
                    $direccion_provee = '28th Floor, No. 533 Loushanguan Road, Changning District, Shanghai, 200051, China. ';
                    $color_style = "#F67200";
                }



                $nombre_cliente = $this->review_letters($this->limpiarTexto($row->consignee));
                $direccion_cliente = $this->review_letters($row->consignee_address1);
                $telefono_cliente = $row->consignee_phone;
                $documento_cliente = $row->consignee_account;
                $fecha_factura = date("d-m-Y", strtotime($fecha_send));

                ini_set('display_errors', 0);

                $url = "https://mistarship.com/watch_details/productos/" . $row->id . "/" . $id;


                // Crear archivo temporal
                $tempDir = sys_get_temp_dir();
                $filePath = $tempDir . DIRECTORY_SEPARATOR . 'qr_' . md5($url) . '.png';

                // Generar QR local
                if (!file_exists($filePath)) {
                    QRcode::png(
                        $url,            // contenido
                        $filePath,       // archivo
                        QR_ECLEVEL_L,    // corrección de errores
                        4,               // tamaño
                        1                // margen
                    );
                }


                $cadena_descripcion = $row->commodity;

                $cadena_productos = $row->commodity;


                $precio_total = $row->value; // Precio total

                // validar a que empresa pertenece el manifiesto
                $verificar_empresa        = $this->WhModel->verificar_empresa($id);
                $tipo = $verificar_empresa->tipo_factura;

                if ($tipo == "MA") {
                    $cadena_cantidades = $row->items_quantities;
                    $resultado = $this->procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total, $row->tracking_number);
                    $agrupados = $this->agruparProductosMA($resultado);
                }
                if ($tipo == "IM") {

                    $cadena_cantidades = $row->items;
                    $resultado = $this->procesarProductosConCantidadesIM($cadena_productos, $cadena_cantidades, $precio_total);
                    $agrupados = $this->agruparProductosIM($resultado);
                }






                //$name_qr =  "https://www.temu.com/goods.html?_bg_fs=1&goods_id=601105205757323";

                $incoterm = ($tm) ? "CIF" : "FOB";
                $puerto = "San Salvador";
                $despacho = "Courier Int.";

                $metodo_pago = "Tarjeta";
                //$id_orden = "PO-061-0472533";

                //$qr = FCPATH . "uploads/qr.png";


                if ($row->id_empresa != 113 && $row->id_empresa != 114 && $row->id_empresa != 112) {
                    $productos = $agrupados;
                }



                //$filePath = 'C:\Users\DESARR~1\AppData\Local\Temp\qr_f4a26035b42e107c04e9310a5940cc7d.png';

                $imageData = base64_encode(file_get_contents($filePath));

                $qrImage = 'data:image/png;base64,' . $imageData;



                $logo = base64_encode(file_get_contents($logo));
                $logo = 'data:image/png;base64,' . $logo;


                $html = '
            <html>
            <head>
            <meta charset="utf-8">

            <style>

            body{
                font-family: DejaVu Sans, sans-serif;
                font-size:11px;
                margin:0;
                padding:0;
                background:#ffffff;
                color:#333;
            }

            .wrapper{
                
            }

            .header{
                color:#fff;
                padding:10px;
            }

            .logo{
                font-size:42px;
                font-weight:bold;
                padding: 5%;
            }

            .subtitulo{
                font-size:11px;
                letter-spacing:2px;
                color: ' . $color_style . ';
            }

            .tracking{
                float:right;
                border:1px dashed black;
                padding:10px 20px;
                text-align:center;
                margin-top: -100px;
                color: ' . $color_style . ';
            }

            .tracking strong{
                font-size:22px;
                
            }

            .date-div{
                width: 100%;
                text-align:center;
                padding: 10px;
            }

            .date-div strong{
                font-size: 15px;
                color: ' . $color_style . ';
                
            }

            .contenido{
                padding:25px;
            }

            .alerta{
                background:#faf4ef;
                border-left:4px solid ' . $color_style . ';
                padding:10px;
                color:#666;
                margin-bottom:20px;
            }

            .info{
                width:100%;
                border-spacing:10px;
            }

            .card{
                border:1px solid #dcdcdc;
                border-radius:5px;
                padding:10px;
                vertical-align:top;
                height:130px;
            }

            .card-title{
                color: ' . $color_style . ';
                font-weight:bold;
                margin-bottom:10px;
                border-bottom:1px solid #ddd;
                padding-bottom:5px;
            }

            .seccion{
                background:#304457;
                color:white;
                padding:10px;
                margin-top:20px;
                font-weight:bold;
            }

            .detalle{
                width:100%;
                border-collapse:collapse;
            }

            .detalle th{
                background:#f1f3f6;
                border:1px solid #ddd;
                padding:8px;
                font-size:10px;
            }

            .detalle td{
                border:1px solid #ddd;
                padding:10px;
                vertical-align:middle;
            }

            .descripcion{
                width:48%;
            }

            .cantidad{
                width:10%;
                text-align:center;
            }

            .precio{
                width:15%;
                text-align:right;
            }

            .total{
                width:15%;
                text-align:right;
            }

            .qr{
                width:12%;
                text-align:center;
            }

            .badge{
                display:inline-block;
                background:#e5e8ec;
                border-radius:20px;
                padding:4px 10px;
                font-weight:bold;
            }

            .subdetalle{
                color:#6d6d6d;
                font-size:10px;
                margin-top:5px;
            }

            .resumen{
                width:300px;
                float:right;
                margin-top:25px;
                border:1px solid #ddd;
                padding:15px;
            }

            .total-final{
                color: ' . $color_style . ';
                font-size:10px;
                font-weight:bold;
            }

            .footer{
                text-align:center;
                color:#888;
                font-size:10px;
                margin-top:100px;
            }

            </style>
            </head>
            <body>

            <div class="wrapper">

            <div class="header">

                <div class="logo"><img src="' . $logo . '" style="width: 20%;"/></div>
                <div class="subtitulo">COMPROBANTE DE CARGA</div>
            
                <div class="tracking">
                    TRACKING NO<br>
                    <strong>' . $tracking_number . '</strong>
                </div>

            </div>

            <div class="date-div">
                    <strong> Fecha factura: ' . $fecha_factura . '</strong>
            </div>

            <div class="contenido">

            <div class="alerta" style="display: block;">
            ✔ Para evitar el exceso de desperdicio en los empaques, no incluimos recibos de papel. Sin embargo, siempre puede imprimir uno para sus registros.
            </div>

            <table class="info">
            <tr>

            <td width="40%" class="card">
                <div class="card-title">DATOS DE DESTINO</div>

                <b>Cliente:</b> ' . strtoupper($nombre_cliente) . '<br><br>

                <b>DPI:</b> ' . $documento_cliente . '<br><br>

                <b>Teléfono:</b> ' . $telefono_cliente . '<br><br>


                <b>Direccion:</b> ' . strtoupper($direccion_cliente) . '<br><br>
            </td>

            <td width="30%" class="card">
                <div class="card-title">TÉRMINOS OPERATIVOS</div>

                <b>Incoterm:</b> ' . $incoterm . '<br><br>

                <b>Despacho:</b> ' . $despacho . '<br><hr><br><br>

                <div class="card-title">INFORMACIÓN DE PAGO</div>

                <b>Método:</b> Tarjeta <br><br>

            </td>

            <td width="30%" style="text-align:center; display: none;' . $qr_flag . '" >
                

                <img src="' . $qrImage . '" width="120">

                
            </td>

            </tr>
            </table>';

                if ($tm == 1) {
                    $html .= '<div style="text-align:center; font-weight: bold;">Enlace productos: ' . $url . '</div>';
                }

                $html .= '<div class="seccion">
            Detalles del artículo (Desglose de Verificación)
            </div>

            <table class="detalle">

            <tr>
                <th>DESCRIPCIÓN DEL PRODUCTO</th>
                <th>CANT.</th>
                <th>PRECIO UNIT.</th>
                <th>TOTAL</th>
                
            </tr>';

                $subtotal = 0;
                if ($row->id_empresa != 113 && $row->id_empresa != 114 && $row->id_empresa != 112) {


                    foreach ($productos as $item) {



                        $total = ($item['precio'] / $item['cantidad']) * $item['cantidad'];
                        $subtotal += $total;

                        $html .= '

                    <tr>

                        <td class="descripcion">
                            <strong>' . $this->review_letters($item['descripcion']) . '</strong>

                            
                        </td>

                        <td class="cantidad">
                            <span class="badge">' . $item['cantidad'] . '</span>
                        </td>

                        <td class="precio">
                            $' . number_format($item['precio'] / $item['cantidad'], 2) . '
                        </td>

                        <td class="total">
                            $' . number_format($total, 2) . '
                        </td>

                        

                    </tr>';
                    }
                } else {

                    $palabras_individuales = array_map('trim', explode(',', $row->commodity));
                    $conteo_palabras = array_count_values($palabras_individuales);

                    $resultado_con_conteo = [];


                    if ($tm == 1) {
                        foreach ($conteo_palabras as $palabra => $cantidad) {
                            // Formateamos como "Palabra (X)"

                            $qtys = $this->WhModel->obtener_detalle_gt_por_id($row->id, $id, $palabra);
                            //var_dump($qtys);
                            //die();


                            $resultado_con_conteo[] = "$palabra ($qtys->items_quantities)";
                        }
                    } else {
                        foreach ($conteo_palabras as $palabra => $cantidad) {
                            // Formateamos como "Palabra (X)"
                            $resultado_con_conteo[] = "$palabra ($cantidad)";
                        }
                    }


                    // Unimos el resultado final por comas
                    $producto_tm = implode(',', $resultado_con_conteo);


                    /*$producto_tm = implode(
                        ',',
                        array_unique(
                            array_map('trim', explode(',', $row->commodity))
                        )
                    );*/

                    $subtotal = $row->value;
                    $html .= '

                    <tr>

                        <td class="descripcion">
                            <strong>' . strtoupper($this->review_letters($producto_tm)) . '</strong>

                            
                        </td>

                        <td class="cantidad">
                            <span class="badge">' . $row->pieces . '</span>
                        </td>

                        <td class="precio">
                            $ - 
                        </td>

                        <td class="total">
                            $' . number_format($row->value, 2) . '
                        </td>

                        

                    </tr>';
                }

                $html .= '

            </table>

            <div class="resumen">

                ';

                if ($tm == 0) {
                    $html .= '<table width="100%">
                    <tr>
                        <td>Subtotal de Artículos:</td>
                        <td align="right">$' . number_format($subtotal, 2) . '</td>
                    </tr>
                </table>

                <br>';
                }

                if ($tm) {

                    $gt_value = $this->WhModel->obtener_gt_por_id($row->id, $id);

                    $html .= '
                
                    <table width="100%">
                    <tr>
                        <td>FOB:</td>
                        <td align="right">
                            $ ' . $gt_value->fob_gt . '
                        </td>
                    </tr>
                </table>
                 <br>

                <table width="100%">
                    <tr>
                        <td>FLETE:</td>
                        <td align="right">
                            $ ' . $gt_value->flete_gt . '
                        </td>
                    </tr>
                </table>
                 <br>

                <table width="100%">
                    <tr>
                        <td>SEGURO:</td>
                        <td align="right">
                            $ ' . $gt_value->seguro_gt . '
                        </td>
                    </tr>
                </table>
                
                ';
                }

                $html .= '<table width="100%">
                    <tr>
                        <td class="total-final">Monto ' . $incoterm . ' Total:</td>
                        <td align="right" class="total-final">
                            $' . number_format($subtotal, 2) . '
                        </td>
                    </tr>
                </table>

            </div>

            

            </div>
            </div>

            </body>
            </html>';

                // Generar PDF
                $this->pdf->loadHtml($html);
                $this->pdf->setPaper('A4', 'portrait');
                $this->pdf->render();

                $pdfName = $tracking_number . "_2.pdf";

                $rutaZip = $carpeta . '/' . $pdfName;

                $zip->addFromString(
                    $rutaZip,
                    $this->pdf->output()
                );

                unset($this->pdf);

                //die();


            }

            $zip->close();
        }


        


        $desabsoluto  =  "document/Manifiesto/";

        $nombre_archivo =   "M" . $manifiesto . ".zip";

        $datosretorno  = array(
            'destino'        => $desabsoluto,
            'nombre_archivo' => $nombre_archivo,

        );


        echo json_encode($datosretorno);
        exit;
    }

    public function generar_manifiesto_pdf_x_lote_oldd($id)
    {
        //phpinfo();
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        include getcwd() . "/application/libraries/phpqrcode/qrlib.php";

        $this->load->library('pdf');

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $id_manifiesto = $id;

        $preclasificacion = $this->WhModel->datos_preclasificacion($id);

        $empresas = [112, 113, 114];

        $tm = 0;

        if (in_array($preclasificacion->client_id, $empresas)) {

            //$busqueda = $referencia;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $result = json_decode($response);
            $fecha_send = "";
            if (isset($result->data->details)) {
                $dlist_manifiestos  = $result->data->details;
                $fecha_send = $result->data->header->fecha;
                //$this->datos['client_id'] = $preclasificacion->client_id;
            } else {
                $dlist_manifiestos = [];
            }

            $tm = 1;


            /*$encontrado = null;
            $new_array = array();

            foreach ($dlist_manifiestos as $item) {


                if ($item->referencia == $busqueda) {
                    //$encontrado = $item;
                    $new_array[] = $item;

                    //var_dump($item);
                    //break; // Detiene la búsqueda inmediatamente al encontrar el primero
                }
            }*/

            //$dlist_manifiestos = $new_array;
        } else {
            $dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);
        }





        // Obtener los datos del manifiesto
        //$dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);



        // Crear la carpeta del manifiesto
        $manifiesto = "";
        if (!empty($dlist_manifiestos)) {
            //$manifiesto = $dlist_manifiestos[0]->manifiesto; // Usar el nombre del manifiesto
            $manifiesto = $preclasificacion->manifiesto;
        }


        $destino_base = getcwd() . "/document/Manifiesto/";
        $destino_carpeta = $destino_base . "GT" . $manifiesto . '/';
        if (!is_dir($destino_carpeta)) {
            mkdir($destino_carpeta, 0777, true);
        }

        $zip_file = $destino_base . "M" . $manifiesto . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            $contador = 0;

            foreach ($dlist_manifiestos as $row) {

                $contador++;

                $numeroLote = ceil($contador / 500);

                $carpeta = 'Lote_' . str_pad($numeroLote, 3, '0', STR_PAD_LEFT);

                $this->pdf = new \Dompdf\Dompdf($options);

                $tracking_number = ($tm) ? $row->package_number : $row->tracking_number;
                //$origen = $row->pais_origen;

                $direccion_provee = "";

                $qr_flag = "display:none;";

                // Encabezado y diseño inicial
                if ($row->id_empresa == 103 ||  $row->id_empresa == 104 || $row->id_empresa == 101) {


                    //if ($row->client_id == "AliExpress") {
                    $logo = getcwd() . '/public/img/Aliexpress2026.png';
                    $direccion_provee = "No. 111 Tianlin Road, Tower, Shangai, China.";
                    $color_style = "#FF2650";
                    /*} else {
                        $logo = getcwd() . '/public/img/Ebay2026.png';;
                        $direccion_provee = 'First Floor, 25 St Stephens Green, Shangai';
                        $color_style = "#116DF6";  
                    }*/

                    $fecha_send = $row->fecha;
                }
                if ($row->id_empresa == 113 || $row->id_empresa == 114 || $row->id_empresa == 112) {

                    $qr_flag = "display:;";
                    $logo = getcwd() . '/public/img/temu.png';;
                    $direccion_provee = '28th Floor, No. 533 Loushanguan Road, Changning District, Shanghai, 200051, China. ';
                    $color_style = "#F67200";
                }

                

                $nombre_cliente = $this->review_letters($this->limpiarTexto($row->consignee));
                $direccion_cliente = $this->review_letters($row->consignee_address1);
                $telefono_cliente = $row->consignee_phone;
                $documento_cliente = $row->consignee_account;
                $fecha_factura = date("d-m-Y", strtotime($fecha_send));

                ini_set('display_errors', 0);

                $url = "https://mistarship.com/watch_details/productos/" . $row->id . "/" . $id;


                // Crear archivo temporal
                $tempDir = sys_get_temp_dir();
                $filePath = $tempDir . DIRECTORY_SEPARATOR . 'qr_' . md5($url) . '.png';

                // Generar QR local
                if (!file_exists($filePath)) {
                    QRcode::png(
                        $url,            // contenido
                        $filePath,       // archivo
                        QR_ECLEVEL_L,    // corrección de errores
                        4,               // tamaño
                        1                // margen
                    );
                }


                $cadena_descripcion = $row->commodity;

                $cadena_productos = $row->commodity;


                $precio_total = $row->value; // Precio total

                // validar a que empresa pertenece el manifiesto
                $verificar_empresa        = $this->WhModel->verificar_empresa($id);
                $tipo = $verificar_empresa->tipo_factura;

                if ($tipo == "MA") {
                    $cadena_cantidades = $row->items_quantities;
                    $resultado = $this->procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total);
                    $agrupados = $this->agruparProductosMA($resultado);
                }
                if ($tipo == "IM") {

                    $cadena_cantidades = $row->items;
                    $resultado = $this->procesarProductosConCantidadesIM($cadena_productos, $cadena_cantidades, $precio_total);
                    $agrupados = $this->agruparProductosIM($resultado);
                }






                //$name_qr =  "https://www.temu.com/goods.html?_bg_fs=1&goods_id=601105205757323";

                $incoterm = ($tm) ? "CIF" : "FOB";
                $puerto = "San Salvador";
                $despacho = "Courier Int.";

                $metodo_pago = "Tarjeta";
                //$id_orden = "PO-061-0472533";

                //$qr = FCPATH . "uploads/qr.png";


                if ($row->id_empresa != 113 && $row->id_empresa != 114 && $row->id_empresa != 112) {
                    $productos = $agrupados;
                }



                //$filePath = 'C:\Users\DESARR~1\AppData\Local\Temp\qr_f4a26035b42e107c04e9310a5940cc7d.png';

                $imageData = base64_encode(file_get_contents($filePath));

                $qrImage = 'data:image/png;base64,' . $imageData;



                $logo = base64_encode(file_get_contents($logo));
                $logo = 'data:image/png;base64,' . $logo;


                $html = '
            <html>
            <head>
            <meta charset="utf-8">

            <style>

            body{
                font-family: DejaVu Sans, sans-serif;
                font-size:11px;
                margin:0;
                padding:0;
                background:#ffffff;
                color:#333;
            }

            .wrapper{
                
            }

            .header{
                color:#fff;
                padding:10px;
            }

            .logo{
                font-size:42px;
                font-weight:bold;
                padding: 5%;
            }

            .subtitulo{
                font-size:11px;
                letter-spacing:2px;
                color: ' . $color_style . ';
            }

            .tracking{
                float:right;
                border:1px dashed black;
                padding:10px 20px;
                text-align:center;
                margin-top: -100px;
                color: ' . $color_style . ';
            }

            .tracking strong{
                font-size:22px;
                
            }

            .date-div{
                width: 100%;
                text-align:center;
                padding: 10px;
            }

            .date-div strong{
                font-size: 15px;
                color: ' . $color_style . ';
                
            }

            .contenido{
                padding:25px;
            }

            .alerta{
                background:#faf4ef;
                border-left:4px solid ' . $color_style . ';
                padding:10px;
                color:#666;
                margin-bottom:20px;
            }

            .info{
                width:100%;
                border-spacing:10px;
            }

            .card{
                border:1px solid #dcdcdc;
                border-radius:5px;
                padding:10px;
                vertical-align:top;
                height:130px;
            }

            .card-title{
                color: ' . $color_style . ';
                font-weight:bold;
                margin-bottom:10px;
                border-bottom:1px solid #ddd;
                padding-bottom:5px;
            }

            .seccion{
                background:#304457;
                color:white;
                padding:10px;
                margin-top:20px;
                font-weight:bold;
            }

            .detalle{
                width:100%;
                border-collapse:collapse;
            }

            .detalle th{
                background:#f1f3f6;
                border:1px solid #ddd;
                padding:8px;
                font-size:10px;
            }

            .detalle td{
                border:1px solid #ddd;
                padding:10px;
                vertical-align:middle;
            }

            .descripcion{
                width:48%;
            }

            .cantidad{
                width:10%;
                text-align:center;
            }

            .precio{
                width:15%;
                text-align:right;
            }

            .total{
                width:15%;
                text-align:right;
            }

            .qr{
                width:12%;
                text-align:center;
            }

            .badge{
                display:inline-block;
                background:#e5e8ec;
                border-radius:20px;
                padding:4px 10px;
                font-weight:bold;
            }

            .subdetalle{
                color:#6d6d6d;
                font-size:10px;
                margin-top:5px;
            }

            .resumen{
                width:300px;
                float:right;
                margin-top:25px;
                border:1px solid #ddd;
                padding:15px;
            }

            .total-final{
                color: ' . $color_style . ';
                font-size:10px;
                font-weight:bold;
            }

            .footer{
                text-align:center;
                color:#888;
                font-size:10px;
                margin-top:100px;
            }

            </style>
            </head>
            <body>

            <div class="wrapper">

            <div class="header">

                <div class="logo"><img src="' . $logo . '" style="width: 20%;"/></div>
                <div class="subtitulo">COMPROBANTE DE CARGA</div>
            
                <div class="tracking">
                    TRACKING NO<br>
                    <strong>' . $tracking_number . '</strong>
                </div>

            </div>

            <div class="date-div">
                    <strong> Fecha factura: ' . $fecha_factura . '</strong>
            </div>

            <div class="contenido">

            <div class="alerta" style="display: block;">
            ✔ Para evitar el exceso de desperdicio en los empaques, no incluimos recibos de papel. Sin embargo, siempre puede imprimir uno para sus registros.
            </div>

            <table class="info">
            <tr>

            <td width="40%" class="card">
                <div class="card-title">DATOS DE DESTINO</div>

                <b>Cliente:</b> ' . strtoupper($nombre_cliente) . '<br><br>

                <b>DPI:</b> ' . $documento_cliente . '<br><br>

                <b>Teléfono:</b> ' . $telefono_cliente . '<br><br>


                <b>Direccion:</b> ' . strtoupper($direccion_cliente) . '<br><br>
            </td>

            <td width="30%" class="card">
                <div class="card-title">TÉRMINOS OPERATIVOS</div>

                <b>Incoterm:</b> ' . $incoterm . '<br><br>

                <b>Despacho:</b> ' . $despacho . '<br><hr><br><br>

                <div class="card-title">INFORMACIÓN DE PAGO</div>

                <b>Método:</b> Tarjeta <br><br>

            </td>

            <td width="30%" style="text-align:center; display: none;' . $qr_flag . '" >
                

                <img src="' . $qrImage . '" width="120">

                
            </td>

            </tr>
            </table>';

            if($tm == 1){
                    $html .= '<div style="text-align:center; font-weight: bold;">Enlace productos: ' . $url . '</div>';
            }
           
            $html .= '<div class="seccion">
            Detalles del artículo (Desglose de Verificación)
            </div>

            <table class="detalle">

            <tr>
                <th>DESCRIPCIÓN DEL PRODUCTO</th>
                <th>CANT.</th>
                <th>PRECIO UNIT.</th>
                <th>TOTAL</th>
                
            </tr>';

                $subtotal = 0;
                if ($row->id_empresa != 113 && $row->id_empresa != 114 && $row->id_empresa != 112) {


                    foreach ($productos as $item) {



                        $total = ($item['precio'] / $item['cantidad']) * $item['cantidad'];
                        $subtotal += $total;

                        $html .= '

                    <tr>

                        <td class="descripcion">
                            <strong>' . $this->review_letters($item['descripcion']) . '</strong>

                            
                        </td>

                        <td class="cantidad">
                            <span class="badge">' . $item['cantidad'] . '</span>
                        </td>

                        <td class="precio">
                            $' . number_format($item['precio'] / $item['cantidad'], 2) . '
                        </td>

                        <td class="total">
                            $' . number_format($total, 2) . '
                        </td>

                        

                    </tr>';
                    }
                } else {

                    $palabras_individuales = array_map('trim', explode(',', $row->commodity));
                    $conteo_palabras = array_count_values($palabras_individuales);

                    $resultado_con_conteo = [];
                    

                    if($tm == 1){
                        foreach ($conteo_palabras as $palabra => $cantidad) {
                            // Formateamos como "Palabra (X)"

                            $qtys = $this->WhModel->obtener_detalle_gt_por_id($row->id, $id, $palabra);
                            //var_dump($qtys);
                            //die();


                            $resultado_con_conteo[] = "$palabra ($qtys->items_quantities)";
                        }
                    }else{
                        foreach ($conteo_palabras as $palabra => $cantidad) {
                            // Formateamos como "Palabra (X)"
                            $resultado_con_conteo[] = "$palabra ($cantidad)";
                        }
                    }
                    

                    // Unimos el resultado final por comas
                    $producto_tm = implode(',', $resultado_con_conteo);


                    /*$producto_tm = implode(
                        ',',
                        array_unique(
                            array_map('trim', explode(',', $row->commodity))
                        )
                    );*/

                    $subtotal = $row->value;
                    $html .= '

                    <tr>

                        <td class="descripcion">
                            <strong>' . strtoupper($this->review_letters($producto_tm)) . '</strong>

                            
                        </td>

                        <td class="cantidad">
                            <span class="badge">' . $row->pieces . '</span>
                        </td>

                        <td class="precio">
                            $ - 
                        </td>

                        <td class="total">
                            $' . number_format($row->value, 2) . '
                        </td>

                        

                    </tr>';
                }

                $html .= '

            </table>

            <div class="resumen">

                ';

                if ($tm == 0) {
                    $html .= '<table width="100%">
                    <tr>
                        <td>Subtotal de Artículos:</td>
                        <td align="right">$' . number_format($subtotal, 2) . '</td>
                    </tr>
                </table>

                <br>';
                }

                if ($tm) {

                    $gt_value = $this->WhModel->obtener_gt_por_id($row->id, $id);

                    $html .= '
                
                    <table width="100%">
                    <tr>
                        <td>FOB:</td>
                        <td align="right">
                            $ '.$gt_value->fob_gt. '
                        </td>
                    </tr>
                </table>
                 <br>

                <table width="100%">
                    <tr>
                        <td>FLETE:</td>
                        <td align="right">
                            $ ' . $gt_value->flete_gt . '
                        </td>
                    </tr>
                </table>
                 <br>

                <table width="100%">
                    <tr>
                        <td>SEGURO:</td>
                        <td align="right">
                            $ ' . $gt_value->seguro_gt . '
                        </td>
                    </tr>
                </table>
                
                ';
                }

                $html .= '<table width="100%">
                    <tr>
                        <td class="total-final">Monto '.$incoterm.' Total:</td>
                        <td align="right" class="total-final">
                            $' . number_format($subtotal, 2) . '
                        </td>
                    </tr>
                </table>

            </div>

            

            </div>
            </div>

            </body>
            </html>';

                // Generar PDF
                $this->pdf->loadHtml($html);
                $this->pdf->setPaper('A4', 'portrait');
                $this->pdf->render();

                $pdfName = $tracking_number . "_2.pdf";

                $rutaZip = $carpeta . '/' . $pdfName;

                $zip->addFromString(
                    $rutaZip,
                    $this->pdf->output()
                );

                unset($this->pdf);

                //die();


            }

            $zip->close();
        }


        // Comprimir la carpeta en un archivo ZIP
        //$zip_file = $destino_base . "M" . $manifiesto . '.zip';
        //echo $zip_file;

        /*$zip = new ZipArchive();
        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = scandir($destino_carpeta);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $zip->addFile($destino_carpeta . $file, $file);
                }
            }
            $zip->close();
        }*/

        // Descargar automáticamente el archivo ZIP
        //header('Content-Type: application/zip');
        //header('Content-Disposition: attachment; filename="' . "M".$manifiesto . '.zip"');

        //	header('Content-Length: ' . filesize($zip_file));
        //	readfile($zip_file);

        // Eliminar el archivo ZIP después de la descarga
        //unlink($zip_file);


        $desabsoluto  =  "document/Manifiesto/";

        $nombre_archivo =   "M" . $manifiesto . ".zip";

        $datosretorno  = array(
            'destino'        => $desabsoluto,
            'nombre_archivo' => $nombre_archivo,

        );


        echo json_encode($datosretorno);
        exit;
    }




    public function generar_manifiesto_pdf_new($id)
    {
        //phpinfo();
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        include getcwd() . "/application/libraries/phpqrcode/qrlib.php";

        $this->load->library('pdf');

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $id_manifiesto = $id;

        $preclasificacion = $this->WhModel->datos_preclasificacion($id);

        $empresas = [112, 113, 114];

        $tm = 0;

        if (in_array($preclasificacion->client_id, $empresas)) {

            //$busqueda = $referencia;


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $result = json_decode($response);
            $fecha_send = "";
            if (isset($result->data->details)) {
                $dlist_manifiestos  = $result->data->details;
                $fecha_send = $result->data->header->fecha;
                //$this->datos['client_id'] = $preclasificacion->client_id;
            } else {
                $dlist_manifiestos = [];
            }

            $tm = 1;


            /*$encontrado = null;
            $new_array = array();

            foreach ($dlist_manifiestos as $item) {


                if ($item->referencia == $busqueda) {
                    //$encontrado = $item;
                    $new_array[] = $item;

                    //var_dump($item);
                    //break; // Detiene la búsqueda inmediatamente al encontrar el primero
                }
            }*/

            //$dlist_manifiestos = $new_array;
        } else {
            $dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);
        }

        



        // Obtener los datos del manifiesto
        //$dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);

       

        // Crear la carpeta del manifiesto
        $manifiesto = "";
        if (!empty($dlist_manifiestos)) {
            //$manifiesto = $dlist_manifiestos[0]->manifiesto; // Usar el nombre del manifiesto
            $manifiesto = $preclasificacion->manifiesto;
        }


        $destino_base = getcwd() . "/document/Manifiesto/";
        $destino_carpeta = $destino_base . "GT" . $manifiesto . '/';
        if (!is_dir($destino_carpeta)) {
            mkdir($destino_carpeta, 0777, true);
        }

        $zip_file = $destino_base . "M" . $manifiesto . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            foreach ($dlist_manifiestos as $row) {

                $this->pdf = new \Dompdf\Dompdf($options);

                $tracking_number = ($tm) ? $row->package_number : $row->tracking_number;
                //$origen = $row->pais_origen;

                $direccion_provee = "";

                $qr_flag = "display:none;";

                // Encabezado y diseño inicial
                if ($row->id_empresa == 103 ||  $row->id_empresa == 104 || $row->id_empresa == 101 ) {

                    
                    //if ($row->client_id == "AliExpress") {
                        $logo = getcwd() . '/public/img/Aliexpress2026.png';
                        $direccion_provee = "No. 111 Tianlin Road, Tower, Shangai, China.";
                        $color_style = "#FF2650";
                    /*} else {
                        $logo = getcwd() . '/public/img/Ebay2026.png';;
                        $direccion_provee = 'First Floor, 25 St Stephens Green, Shangai';
                        $color_style = "#116DF6";  
                    }*/

                    $fecha_send = $row->fecha;
                }
                if ($row->id_empresa == 113 || $row->id_empresa == 114 || $row->id_empresa == 112) {

                    $qr_flag = "display:;";
                    $logo = getcwd() . '/public/img/temu.png';;
                    $direccion_provee = '28th Floor, No. 533 Loushanguan Road, Changning District, Shanghai, 200051, China. ';
                    $color_style = "#F67200";
                }




                $nombre_cliente = $this->review_letters($this->limpiarTexto($row->consignee));
                $direccion_cliente = $this->review_letters($row->consignee_address1);
                $telefono_cliente = $row->consignee_phone;
                $documento_cliente = $row->consignee_account;
                $fecha_factura= date("d-m-Y", strtotime($fecha_send));

                ini_set('display_errors', 1);
            
                $url = "https://mistarship.com/watch_details/productos/".$row->id."/".$id;

                

                // Crear archivo temporal
                $tempDir = sys_get_temp_dir();
                $filePath = $tempDir . DIRECTORY_SEPARATOR . 'qr_' . md5($url) . '.png';

                // Generar QR local
                if (!file_exists($filePath)) {
                    QRcode::png(
                        $url,            // contenido
                        $filePath,       // archivo
                        QR_ECLEVEL_L,    // corrección de errores
                        4,               // tamaño
                        1                // margen
                    );
                }


                $cadena_descripcion = $row->commodity;

                $cadena_productos = $row->commodity;


                $precio_total = $row->value; // Precio total

                // validar a que empresa pertenece el manifiesto
                $verificar_empresa        = $this->WhModel->verificar_empresa($id);
                $tipo = $verificar_empresa->tipo_factura;

                if ($tipo == "MA") {
                    $cadena_cantidades = $row->items_quantities;
                    $resultado = $this->procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total);
                    $agrupados = $this->agruparProductosMA($resultado);
                }
                if ($tipo == "IM") {

                    $cadena_cantidades = $row->items;
                    $resultado = $this->procesarProductosConCantidadesIM($cadena_productos, $cadena_cantidades, $precio_total);
                    $agrupados = $this->agruparProductosIM($resultado);
                }






                //$name_qr =  "https://www.temu.com/goods.html?_bg_fs=1&goods_id=601105205757323";

                $incoterm = ($tm) ? "CIF" : "FOB";
                $puerto = "San Salvador";
                $despacho = "Courier Int.";

                $metodo_pago = "Tarjeta";
                //$id_orden = "PO-061-0472533";

                //$qr = FCPATH . "uploads/qr.png";


                if ($row->id_empresa != 113 && $row->id_empresa != 114 && $row->id_empresa != 112) {
                    $productos = $agrupados;
                }

                

                //$filePath = 'C:\Users\DESARR~1\AppData\Local\Temp\qr_f4a26035b42e107c04e9310a5940cc7d.png';

                $imageData = base64_encode(file_get_contents($filePath));

                $qrImage = 'data:image/png;base64,' . $imageData;


                
                $logo = base64_encode(file_get_contents($logo));
                $logo = 'data:image/png;base64,' . $logo;


                $html = '
            <html>
            <head>
            <meta charset="utf-8">

            <style>

            body{
                font-family: DejaVu Sans, sans-serif;
                font-size:11px;
                margin:0;
                padding:0;
                background:#ffffff;
                color:#333;
            }

            .wrapper{
                
            }

            .header{
                color:#fff;
                padding:10px;
            }

            .logo{
                font-size:42px;
                font-weight:bold;
                padding: 5%;
            }

            .subtitulo{
                font-size:11px;
                letter-spacing:2px;
                color: ' . $color_style . ';
            }

            .tracking{
                float:right;
                border:1px dashed black;
                padding:10px 20px;
                text-align:center;
                margin-top: -100px;
                color: ' . $color_style . ';
            }

            .tracking strong{
                font-size:22px;
                
            }

            .date-div{
                width: 100%;
                text-align:center;
                padding: 10px;
            }

            .date-div strong{
                font-size: 15px;
                color: ' . $color_style . ';
                
            }

            .contenido{
                padding:25px;
            }

            .alerta{
                background:#faf4ef;
                border-left:4px solid ' . $color_style . ';
                padding:10px;
                color:#666;
                margin-bottom:20px;
            }

            .info{
                width:100%;
                border-spacing:10px;
            }

            .card{
                border:1px solid #dcdcdc;
                border-radius:5px;
                padding:10px;
                vertical-align:top;
                height:130px;
            }

            .card-title{
                color: ' . $color_style . ';
                font-weight:bold;
                margin-bottom:10px;
                border-bottom:1px solid #ddd;
                padding-bottom:5px;
            }

            .seccion{
                background:#304457;
                color:white;
                padding:10px;
                margin-top:20px;
                font-weight:bold;
            }

            .detalle{
                width:100%;
                border-collapse:collapse;
            }

            .detalle th{
                background:#f1f3f6;
                border:1px solid #ddd;
                padding:8px;
                font-size:10px;
            }

            .detalle td{
                border:1px solid #ddd;
                padding:10px;
                vertical-align:middle;
            }

            .descripcion{
                width:48%;
            }

            .cantidad{
                width:10%;
                text-align:center;
            }

            .precio{
                width:15%;
                text-align:right;
            }

            .total{
                width:15%;
                text-align:right;
            }

            .qr{
                width:12%;
                text-align:center;
            }

            .badge{
                display:inline-block;
                background:#e5e8ec;
                border-radius:20px;
                padding:4px 10px;
                font-weight:bold;
            }

            .subdetalle{
                color:#6d6d6d;
                font-size:10px;
                margin-top:5px;
            }

            .resumen{
                width:300px;
                float:right;
                margin-top:25px;
                border:1px solid #ddd;
                padding:15px;
            }

            .total-final{
                color: ' . $color_style . ';
                font-size:10px;
                font-weight:bold;
            }

            .footer{
                text-align:center;
                color:#888;
                font-size:10px;
                margin-top:100px;
            }

            </style>
            </head>
            <body>

            <div class="wrapper">

            <div class="header">

                <div class="logo"><img src="' . $logo . '" style="width: 20%;"/></div>
                <div class="subtitulo">COMPROBANTE DE CARGA</div>
            
                <div class="tracking">
                    TRACKING NO<br>
                    <strong>' . $tracking_number . '</strong>
                </div>

            </div>

            <div class="date-div">
                    <strong> Fecha factura: ' . $fecha_factura . '</strong>
            </div>

            <div class="contenido">

            <div class="alerta" style="display: block;">
            ✔ Para evitar el exceso de desperdicio en los empaques, no incluimos recibos de papel. Sin embargo, siempre puede imprimir uno para sus registros.
            </div>';

            if($tm == 1){
                    $html .= '<div style="text-align:center; font-weight: bold;">Enlace productos: ' . $url . '</div>';
            }
            
            $html .= '<table class="info">
            <tr>

            <td width="40%" class="card">
                <div class="card-title">DATOS DE DESTINO</div>

                <b>Cliente:</b> ' . strtoupper($nombre_cliente). '<br><br>

                <b>DPI:</b> ' . $documento_cliente . '<br><br>

                <b>Teléfono:</b> ' . $telefono_cliente . '<br><br>


                <b>Direccion:</b> ' . strtoupper($direccion_cliente) . '<br><br>
            </td>

            <td width="30%" class="card">
                <div class="card-title">TÉRMINOS OPERATIVOS</div>

                <b>Incoterm:</b> ' . $incoterm . '<br><br>

                <b>Despacho:</b> ' . $despacho . '<br><hr><br><br>

                <div class="card-title">INFORMACIÓN DE PAGO</div>

                <b>Método:</b> Tarjeta <br><br>

            </td>

            <td width="30%" style="text-align:center; display: none;'.$qr_flag.'" >
                

                <img src="' . $qrImage . '" width="120">

                
            </td>

            </tr>
            </table>

            <div class="seccion">
            Detalles del artículo (Desglose de Verificación)
            </div>

            <table class="detalle">

            <tr>
                <th>DESCRIPCIÓN DEL PRODUCTO</th>
                <th>CANT.</th>
                <th>PRECIO UNIT.</th>
                <th>TOTAL</th>
                
            </tr>';

                $subtotal = 0;
                if ($row->id_empresa != 113 && $row->id_empresa != 114 && $row->id_empresa != 112) {
                    

                    foreach ($productos as $item) {



                        $total = ($item['precio'] / $item['cantidad']) * $item['cantidad'];
                        $subtotal += $total;

                        $html .= '

                    <tr>

                        <td class="descripcion">
                            <strong>' . $this->review_letters($item['descripcion']) . '</strong>

                            
                        </td>

                        <td class="cantidad">
                            <span class="badge">' . $item['cantidad'] . '</span>
                        </td>

                        <td class="precio">
                            $' . number_format($item['precio']/ $item['cantidad'], 2) . '
                        </td>

                        <td class="total">
                            $' . number_format($total, 2) . '
                        </td>

                        

                    </tr>';
                    }

                }else{
                    
                    $palabras_individuales = array_map('trim', explode(',', $row->commodity));
                    $conteo_palabras = array_count_values($palabras_individuales);


                    
                    $resultado_con_conteo = [];
                    if ($tm == 1) {
                        foreach ($conteo_palabras as $palabra => $cantidad) {
                            // Formateamos como "Palabra (X)"

                            $qtys = $this->WhModel->obtener_detalle_gt_por_id($row->id, $id, $palabra);
                            //var_dump($qtys);
                            //die();


                            $resultado_con_conteo[] = "$palabra ($qtys->items_quantities)";
                        }
                    } else {
                        foreach ($conteo_palabras as $palabra => $cantidad) {
                            // Formateamos como "Palabra (X)"
                            $resultado_con_conteo[] = "$palabra ($cantidad)";
                        }
                    }


                    //var_dump($resultado_con_conteo);
                    //die();

                    // Unimos el resultado final por comas
                    $producto_tm = implode(',', $resultado_con_conteo);


                    /*$producto_tm = implode(
                        ',',
                        array_unique(
                            array_map('trim', explode(',', $row->commodity))
                        )
                    );*/

                    $subtotal = $row->value;
                    $html .= '

                    <tr>

                        <td class="descripcion">
                            <strong>' . strtoupper($this->review_letters($producto_tm)) . '</strong>

                            
                        </td>

                        <td class="cantidad">
                            <span class="badge">' . $row->pieces . '</span>
                        </td>

                        <td class="precio">
                            $ - 
                        </td>

                        <td class="total">
                            $' . number_format($row->value, 2) . '
                        </td>

                        

                    </tr>';
                }

                $html .= '

            </table>

            <div class="resumen">

                ';

                if($tm == 0){
                    $html .= '<table width="100%">
                    <tr>
                        <td>Subtotal de Artículos:</td>
                        <td align="right">$' . number_format($subtotal, 2) . '</td>
                    </tr>
                </table>

                <br>';
                }

                if($tm){


                    //echo $row->id." ".$id;
                    

                    $gt_value = $this->WhModel->obtener_gt_por_id($row->id, $id);

                    
                    $html .= '
                
                    <table width="100%">
                    <tr>
                        <td>FOB:</td>
                        <td align="right">
                            $ ' . $gt_value->fob_gt . '
                        </td>
                    </tr>
                </table>
                 <br>

                <table width="100%">
                    <tr>
                        <td>FLETE:</td>
                        <td align="right">
                            $ ' . $gt_value->flete_gt . '
                        </td>
                    </tr>
                </table>
                 <br>

                <table width="100%">
                    <tr>
                        <td>SEGURO:</td>
                        <td align="right">
                            $ ' . $gt_value->seguro_gt . '
                        </td>
                    </tr>
                </table>

                <br>
                
                ';
                }
                

                $html .= '

                <table width="100%">
                    <tr>
                        <td class="total-final">Monto '. $incoterm . ' Total:</td>
                        <td align="right" class="total-final">
                            $' . number_format($subtotal, 2) . '
                        </td>
                    </tr>
                </table>

            </div>

            

            </div>
            </div>

            </body>
            </html>';





                /*$this->pdf->loadHtml($html);
                $this->pdf->setPaper('A4', 'portrait');
                $this->pdf->render();
                $this->pdf->stream('reporte.pdf', ['Attachment' => 0]);
                die();*/



                /*$this->pdf->loadHtml($html);
                $this->pdf->setPaper('A4', 'portrait');
                $this->pdf->render();

                // Guardar PDF temporal
                $pdfName = $tracking_number . "_2.pdf";
                $pdfPath = $destino_carpeta . "/". $pdfName;

                file_put_contents(
                    $pdfPath,
                    $this->pdf->output()
                );

                unset($this->pdf);*/

                // Generar PDF
                $this->pdf->loadHtml($html);
                $this->pdf->setPaper('A4', 'portrait');
                $this->pdf->render();

                // Nombre dentro del ZIP
                $pdfName = $tracking_number . "_2.pdf";

                // Agregar PDF directamente al ZIP
                $zip->addFromString(
                    $pdfName,
                    $this->pdf->output()
                );

                unset($this->pdf);

                //die();


            }

            $zip->close();

        }


        // Comprimir la carpeta en un archivo ZIP
        //$zip_file = $destino_base . "M" . $manifiesto . '.zip';
        //echo $zip_file;

        /*$zip = new ZipArchive();
        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = scandir($destino_carpeta);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $zip->addFile($destino_carpeta . $file, $file);
                }
            }
            $zip->close();
        }*/

        // Descargar automáticamente el archivo ZIP
        //header('Content-Type: application/zip');
        //header('Content-Disposition: attachment; filename="' . "M".$manifiesto . '.zip"');

        //	header('Content-Length: ' . filesize($zip_file));
        //	readfile($zip_file);

        // Eliminar el archivo ZIP después de la descarga
        //unlink($zip_file);


        $desabsoluto  =  "document/Manifiesto/";

        $nombre_archivo =   "M" . $manifiesto . ".zip";

        $datosretorno  = array(
            'destino'        => $desabsoluto,
            'nombre_archivo' => $nombre_archivo,

        );


        echo json_encode($datosretorno);
        exit;
    
    
    }

    public function generarPdfNew()
    {

        ini_set('display_errors', 1);

        include getcwd() . "/application/libraries/phpqrcode/qrlib.php";

        $url = "https://www.temu.com/goods.html?_bg_fs=1&goods_id=601101740293912";

        // Crear archivo temporal
        $tempDir = sys_get_temp_dir();
        $filePath = $tempDir . DIRECTORY_SEPARATOR . 'qr_' . md5($url) . '.png';

        // Generar QR local
        if (!file_exists($filePath)) {
            QRcode::png(
                $url,            // contenido
                $filePath,       // archivo
                QR_ECLEVEL_L,    // corrección de errores
                4,               // tamaño
                1                // margen
            );
        }




        //$name_qr =  "https://www.temu.com/goods.html?_bg_fs=1&goods_id=601105205757323";



        $this->load->library('pdf');

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $this->pdf = new \Dompdf\Dompdf($options);

        $color_style = "#116DF6";


        $tracking = "IM-992385102-SV";
        $cliente = "Kevin Tolosa";
        $dui = "01234567-8";
        $telefono = "+503 6138 6659";
        $correo = "kevin.tolosa@example.com";
        $direccion = "Calle Circunvalación lote 7 zona Industrial PL, Agencia C807 plan de la laguna, Antiguo Cuscatlán, La Libertad 1502, El Salvador";

        $incoterm = "CIF";
        $puerto = "San Salvador";
        $despacho = "Courier Int.";

        $metodo_pago = "Tarjeta";
        //$id_orden = "PO-061-0472533";

        //$qr = FCPATH . "uploads/qr.png";

        $productos = [
            [
                'descripcion' => '5 piezas de película de hidrogel para Honor 400 300 200 Pro Lite',
                'detalle' => 'Modelo: Magia 5 Lite | Proveedor: ABC Phone Accessories',
                'cantidad' => 5,
                'precio' => 2.00
            ],
            [
                'descripcion' => 'Paquete de 3 películas 3D para Huawei Band 6/6Pro',
                'detalle' => 'Curva Completa | Proveedor: ALDO Protector',
                'cantidad' => 3,
                'precio' => 4.00
            ],
            [
                'descripcion' => 'Pulsera de reloj de lujo ligera para Samsung Galaxy Watch 4/5/6/7',
                'detalle' => 'Color Verde 20mm | Material Silicona/Acero',
                'cantidad' => 1,
                'precio' => 26.34
            ],
            [
                'descripcion' => 'Paquete de 3 películas 3D para Huawei Band 6/6Pro',
                'detalle' => 'Curva Completa | Proveedor: ALDO Protector',
                'cantidad' => 3,
                'precio' => 4.00
            ],
            [
                'descripcion' => 'Pulsera de reloj de lujo ligera para Samsung Galaxy Watch 4/5/6/7',
                'detalle' => 'Color Verde 20mm | Material Silicona/Acero',
                'cantidad' => 1,
                'precio' => 26.34
            ]
        ];

        //$filePath = 'C:\Users\DESARR~1\AppData\Local\Temp\qr_f4a26035b42e107c04e9310a5940cc7d.png';

        $imageData = base64_encode(file_get_contents($filePath));

        $qrImage = 'data:image/png;base64,' . $imageData;


        $logo = getcwd() . '/public/img/ebay2026.png';
        $logo = base64_encode(file_get_contents($logo));
        $logo = 'data:image/png;base64,' . $logo;


        $html = '
        <html>
        <head>
        <meta charset="utf-8">

        <style>

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:11px;
            margin:0;
            padding:0;
            background:#ffffff;
            color:#333;
        }

        .wrapper{
            
        }

        .header{
            color:#fff;
            padding:10px;
        }

        .logo{
            font-size:42px;
            font-weight:bold;
        }

        .subtitulo{
            font-size:11px;
            letter-spacing:2px;
            color: ' . $color_style . ';
        }

        .tracking{
            float:right;
            border:1px dashed black;
            padding:10px 20px;
            text-align:center;
            margin-top: -100px;
            color: ' . $color_style . ';
        }

        .tracking strong{
            font-size:22px;
            
        }

        .contenido{
            padding:25px;
        }

        .alerta{
            background:#faf4ef;
            border-left:4px solid ' . $color_style . ';
            padding:10px;
            color:#666;
            margin-bottom:20px;
        }

        .info{
            width:100%;
            border-spacing:10px;
        }

        .card{
            border:1px solid #dcdcdc;
            border-radius:5px;
            padding:10px;
            vertical-align:top;
            height:130px;
        }

        .card-title{
            color: ' . $color_style . ';
            font-weight:bold;
            margin-bottom:10px;
            border-bottom:1px solid #ddd;
            padding-bottom:5px;
        }

        .seccion{
            background:#304457;
            color:white;
            padding:10px;
            margin-top:20px;
            font-weight:bold;
        }

        .detalle{
            width:100%;
            border-collapse:collapse;
        }

        .detalle th{
            background:#f1f3f6;
            border:1px solid #ddd;
            padding:8px;
            font-size:10px;
        }

        .detalle td{
            border:1px solid #ddd;
            padding:10px;
            vertical-align:middle;
        }

        .descripcion{
            width:48%;
        }

        .cantidad{
            width:10%;
            text-align:center;
        }

        .precio{
            width:15%;
            text-align:right;
        }

        .total{
            width:15%;
            text-align:right;
        }

        .qr{
            width:12%;
            text-align:center;
        }

        .badge{
            display:inline-block;
            background:#e5e8ec;
            border-radius:20px;
            padding:4px 10px;
            font-weight:bold;
        }

        .subdetalle{
            color:#6d6d6d;
            font-size:10px;
            margin-top:5px;
        }

        .resumen{
            width:300px;
            float:right;
            margin-top:25px;
            border:1px solid #ddd;
            padding:15px;
        }

        .total-final{
            color: ' . $color_style . ';
            font-size:10px;
            font-weight:bold;
        }

        .footer{
            text-align:center;
            color:#888;
            font-size:10px;
            margin-top:100px;
        }

        </style>
        </head>
        <body>

        <div class="wrapper">

        <div class="header">

            <div class="logo"><img src="'.$logo. '" style="width: 28%;"/></div>
            <div class="subtitulo">COMPROBANTE DE CARGA</div>
        
            <div class="tracking">
                TRACKING NO<br>
                <strong>' . $tracking . '</strong>
            </div>

        </div>

        <div class="contenido">

        <div class="alerta" style="display: block;">
        ✔ Para evitar el exceso de desperdicio en los empaques, no incluimos recibos de papel. Sin embargo, siempre puede imprimir uno para sus registros.
        </div>

        <table class="info">
        <tr>

        <td width="40%" class="card">
            <div class="card-title">DATOS DE DESTINO</div>

            <b>Cliente:</b> ' . $cliente . '<br><br>

            <b>DUI:</b> ' . $dui . '<br><br>

            <b>Teléfono:</b> ' . $telefono . '<br><br>

            <b>Correo:</b> ' . $correo . '<br><br>

            <b>Direccion:</b> ' . $direccion . '<br><br>
        </td>

        <td width="30%" class="card">
            <div class="card-title">TÉRMINOS OPERATIVOS</div>

            <b>Incoterm:</b> ' . $incoterm . '<br><br>

            <b>Puerto Destino:</b> ' . $puerto . '<br><br>

            <b>Despacho:</b> ' . $despacho . '<br><hr><br><br>

            <div class="card-title">INFORMACIÓN DE PAGO</div>

            <b>Método:</b> Tarjeta <br><br>

        </td>

        <td width="30%" style="text-align:center;" >
            

            <img src="' . $qrImage . '" width="120">

            
        </td>

        </tr>
        </table>

        <div class="seccion">
        Detalles del artículo (Desglose de Verificación)
        </div>

        <table class="detalle">

        <tr>
            <th>DESCRIPCIÓN DEL PRODUCTO</th>
            <th>CANT.</th>
            <th>PRECIO UNIT.</th>
            <th>TOTAL</th>
            
        </tr>';

        $subtotal = 0;

        foreach ($productos as $item) {



            $total = $item['cantidad'] * $item['precio'];
            $subtotal += $total;

            $html .= '

            <tr>

                <td class="descripcion">
                    <strong>' . $item['descripcion'] . '</strong>

                    <div style="display:none;" class="subdetalle">
                        ' . $item['detalle'] . '
                    </div>
                </td>

                <td class="cantidad">
                    <span class="badge">' . $item['cantidad'] . '</span>
                </td>

                <td class="precio">
                    $' . number_format($item['precio'], 2) . '
                </td>

                <td class="total">
                    $' . number_format($total, 2) . '
                </td>

                

            </tr>';
        }

        $html .= '

        </table>

        <div class="resumen">

            <table width="100%">
                <tr>
                    <td>Subtotal de Artículos:</td>
                    <td align="right">$' . number_format($subtotal, 2) . '</td>
                </tr>
            </table>

            <br>

            <table width="100%">
                <tr>
                    <td class="total-final">Monto FOB Total:</td>
                    <td align="right" class="total-final">
                        $' . number_format($subtotal, 2) . '
                    </td>
                </tr>
            </table>

        </div>

        

        </div>
        </div>

        </body>
        </html>';

        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();
        $this->pdf->stream('reporte.pdf', ['Attachment' => 0]);
    }

    public function obtener_paquete()
    {

        ini_set("display_errors",1);

        // Leer payload
        $payload = json_decode(file_get_contents('php://input'), true);

        // Validar datos requeridos
        if (empty($payload['track']) || empty($payload['id_manifiesto'])) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'error' => 'track e id_manifiesto son obligatorios'
                ]));
        }

        $track = $payload['track'];
        $id_manifiesto = $payload['id_manifiesto'];
        // Obtener detalle de productos

        $data_paquete = $this->WarehouseModel->obtener_paquete_por_id($track, $id_manifiesto);
        $items = $this->WarehouseModel->obtener_url_items($track, $id_manifiesto);

        $detalle = [];

        foreach ($items as $item) {
            $detalle[] = [
                'descripcion'  => $item->items_description,
                'url_producto' => $item->url
            ];
        }

        // Estos datos puedes obtenerlos de otra consulta si existen en BD
        $respuesta = [
            'numero_paquete' => $data_paquete->package_number,
            'fecha'          => "-",
            'cliente'        => '-',
            'detalle'        => $detalle
        ];

        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    




    public function proceso_clasificacion_tm($id)
    {


        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        ini_set("display_errors", 0);


        $rows = $this->WhModel->lista_warehouse($id, "", 0);

        //unicamente cuanto sea temu
        //if (in_array($preclasificacion->client_id, $empresas)) {
        //Get products by tracking using ma.id
        $products = $this->WhModel->getTemuProducts($id);
        //$products = $this->Aduana_Model->getProductosByTrackingTM($row->id);
        $agrupados = [];
        foreach ($products as $key) {

            $agrupados[$key->id_tracking][] = array(
                'descripcion' => $key->items_description,
                'cantidad' => $key->items_quantities,
                'precio' => number_format($key->cif, 2),
                'peso_total' => $key->weight_unit,
                'partida' => substr($key->hts, 0, 4)
            );
        }
        //}



        $i = 0;

        $partidasNoDetectadas = array();

        $cant_items_no_encontrados = 0;

        foreach ($rows as $row) {
            $cadena_productos = $row->commodity;
            $precio_total = $row->value;
            $cadena_cantidades = $row->items_quantities;


            $z = 0;
            foreach ($agrupados[$row->id] as $key) {
                $desc = $key["descripcion"];

                $withPartida[$z] = $key;

                //var_dump($key);
                $datos = $this->WhModel->getDaiPartida($key["partida"]);

                if (empty($datos)) {
                    $partidaNoEncontrada = $key["descripcion"];
                    $partidasNoDetectadas[$partidaNoEncontrada] = 1;

                    $cant_items_no_encontrados++;
                } else {
                    $withPartida[$z]["partida"] = $key["partida"];
                    $withPartida[$z]["dai"] = $datos->porcentaje_dai;
                }



                $z++;
            }




            //Proceso para determinar el mayor

            $mayorPartida = null;
            $mayorDai = null;
            $equals = 0;
            $partidaToSave = '';
            $daiPToSave = '';

            foreach ($withPartida as $producto) {

                // Mayor partida
                if ($mayorPartida === null || (int)$producto['partida'] > (int)$mayorPartida['partida']) {
                    $mayorPartida = $producto;
                }

                // Mayor DAI
                if ($mayorDai === null || (float)$producto['dai'] > (float)$mayorDai['dai']) {
                    $mayorDai = $producto;
                }
            }


            if ($mayorPartida === $mayorDai) {
                //echo "Los arreglos son iguales";
                $partidaToSave = $mayorPartida["partida"];
                $daiPToSave = $mayorPartida["dai"];
            } else {
                $partidaToSave = $mayorDai["partida"];
                $daiPToSave = $mayorDai["dai"];
            }


            //echo "Partida a grabar ".$partidaToSave. " ".$daiPToSave;

            //var_dump($withPartida);

            $this->WhModel->updateTrackingTariffCode($partidaToSave, $daiPToSave, $row->tracking_number, $id);

            //echo "<br><br>";

            if ($i == 50) {

                //var_dump($partidasNoDetectadas);
                //die();
            }

            $i++;
        }

        echo count($partidasNoDetectadas);

        var_dump($partidasNoDetectadas);
        echo "Total ". $cant_items_no_encontrados;
        die();
    }


    public function proceso_clasificacion_ma($id)
    {

        ini_set('display_errors',1);
        $preclasificacion = $this->WhModel->datos_preclasificacion($id);

        $empresas = [112, 113, 114];

        if (in_array($preclasificacion->client_id, $empresas)) {

            $this->proceso_clasificacion_tm($id);

        }

        $rows = $this->WhModel->lista_warehouse($id, "", 0);

        $i = 0;

        foreach ($rows as $row) {
            $cadena_productos = $row->commodity;
            $precio_total = $row->value;
            $cadena_cantidades = $row->items_quantities;



            /*$resultado = $this->productoslibraryma->procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total, $row->gweight, $row->items);
            
            var_dump($resultado);
            
            $agrupados = $this->productoslibraryma->agruparProductosMA($resultado);*/


            // Extraer el contenido entre corchetes
            preg_match_all('/\[(.*?)\]/', $cadena_productos, $matches);

            $agrupados = array_map(function ($item) {
                return [
                    'descripcion' => trim($item)
                ];
            }, $matches[1]);

            //print_r($agrupados);
            //die();

            var_dump($agrupados);
            echo "<br><br>";

            $z = 0;
            foreach ($agrupados as $key) {
                $desc = $key["descripcion"];

                echo $desc;
                echo "<br><br>";

                $withPartida[$z] = $key;

                //var_dump($key);
                $datos = json_decode($this->send_description_to_ia($desc));

                $withPartida[$z]["partida"] = $datos->datos->sac;
                $withPartida[$z]["dai"] = $datos->datos->dai;

                $z++;
            }


            //Proceso para determinar el mayor

            $mayorPartida = null;
            $mayorDai = null;
            $equals = 0;
            $partidaToSave = '';
            $daiPToSave = '';

            foreach ($withPartida as $producto) {

                // Mayor partida
                if ($mayorPartida === null || (int)$producto['partida'] > (int)$mayorPartida['partida']) {
                    $mayorPartida = $producto;
                }

                // Mayor DAI
                if ($mayorDai === null || (float)$producto['dai'] > (float)$mayorDai['dai']) {
                    $mayorDai = $producto;
                }
            }


            if ($mayorPartida === $mayorDai) {
                echo "Los arreglos son iguales";
                $partidaToSave = $mayorPartida["partida"];
                $daiPToSave = $mayorPartida["dai"];
            } else {
                $partidaToSave = $mayorDai["partida"];
                $daiPToSave = $mayorDai["dai"];
            }


            echo "Partida a grabar " . $partidaToSave . " " . $daiPToSave;

            //var_dump($withPartida);

            $this->WhModel->updateTrackingTariffCode($partidaToSave, $daiPToSave, $row->tracking_number, $id);

            echo "<br><br>";

            if ($i == 5) {

                //die();
            }

            $i++;
        }
    }

    public function send_description_to_ia($desc)
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://gacela.c807.com/aduana.php/certeza_ia/clasificar_con_ia',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
          "DESCRIPCION": "' . $desc . '",
          "pais":502,
          "es_courier": true
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: ••••••',
                'Cookie: PHPSESSID=6uagqjg3tf3dl7aota4q8a7tr9'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
