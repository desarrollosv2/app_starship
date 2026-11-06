<?php
defined('BASEPATH') or exit('No direct script access allowed');
include getcwd() . "/application/libraries/fpdf/fpdf.php";

class ClasificacionController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->model('Conf_model');
        $this->load->model('ClasificadoModel');
        $this->load->model('Clasificado2Model');
        session_start();
    }

    public function index() {}

    public function archivo_facturacion()
    {

        $this->datos['lista_poliza'] = $this->ClasificadoModel->listado_polizas();
        $this->load->view("clasificacion/vista_facturacion", $this->data);
        $this->load->view("clasificacion/lista_facturacion", $this->datos);

        // $this->datos['lista'] = $this->WhModel->listado_polizas();
        //   $this->load->view( "warehouse/vista_facturacion", $this->datos );

    }

    public function manifiesto($id, $master, $correla)
    {
        $_SESSION['tnumber'] = $correla;
        $this->datos['navtext']   = "Manifiestos";
        //$this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);

        $this->datos['form']     = "clasificacion/contenido";
        $this->datos['partidas'] = $this->Conf_model->partidas();
        $this->datos['lista'] = $this->ClasificadoModel->lista_warehouse($id, $master, $correla);
        $this->datos['lst_referencia'] = $this->ClasificadoModel->reference_list($id); //obtiene las referencias asociadas la manifiesto
        $this->load->view("clasificacion/lista_detalle", $this->datos);
    }


    public function manifiesto_confirma($id, $master)
    {

        $this->datos['navtext']   = "Manifiestos";
        //   $this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);

        $this->datos['form']     = "seguimiento/contenido";
        //  $this->datos['partidas'] = $this->Conf_model->partidas();
        $this->datos['lista'] = $this->ClasificadoModel->lista_warehouse_confirma($id, $master);
        //  $this->datos['lst_referencia'] = $this->WhModel->reference_list($id); //obtiene las referencias asociadas la manifiesto
        $this->load->view("clasificacion/lista_confirma", $this->datos);
    }

    public function manifiesto_export($id, $master)
    {

        $this->datos['navtext']   = "Manifiestos";
        //$this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);

        //$this->datos['form']     = "warehouse/contenido";
        //$this->datos['partidas'] = $this->Conf_model->partidas();
        $this->datos['lista'] = $this->ClasificadoModel->manifiesto_export($id, $master);
        //$this->datos['lst_referencia'] = $this->WhModel->reference_list($id); //obtiene las referencias asociadas la manifiesto
        $this->load->view("clasificacion/lista_exportar", $this->datos);
    }


    public function lista_pendientes_confirmar()
    {

        $this->datos['lista'] = $this->ClasificadoModel->lista_pendientes_confirmar();
        $this->load->view("seguimiento/lista", $this->datos);
    }

    public function lista_confirmados()
    {
        // $this->datos['departamento'] = $this->Conf_model->get_departamentos();
        //  var_dump( $this->datos['departamento']);
        $this->datos['lista'] = $this->ClasificadoModel->lista_confirmados();
        $this->load->view("seguimiento/lista", $this->datos);
    }

    public function lista_referencia($id, $ref)
    {

        $this->datos['navtext']   = "Manifiestos";
        $this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);
        $this->datos['partidas'] = $this->Conf_model->partidas();
        $this->datos['form']     = "clasificacion/contenido";
        $this->datos['lista'] = $this->ClasificadoModel->lista_referencia($id, $ref);

        $this->load->view("clasificacion/lista_detalle", $this->datos);
    }
    public function seleccionar_item($id, $opc)
    {
        if ($opc == 1) {
            $flag = 1;
        } else {
            $flag = 0;
        }
        $this->ClasificadoModel->seleccionar_item($id, $flag);
    }

    public function lista_awb()
    {

        $this->datos['navtext']   = "AWB";
        $this->datos['form']     = "clasificacion/contenido";
        //  $this->datos['lista'] = $this->WhModel->lista_awb();
        $this->load->view("clasificacion/lista", $this->datos);
    }
    public function lista_awb_first()
    {
        $this->datos['lista'] = $this->ClasificadoModel->consulta_awb(0, 0, 3, 0);
        //$this->datos['lista'] = $this->ClasificadoModel->lista_awb();
        //   var_dump( $this->datos['lista']);
        $this->load->view("clasificacion/cuerpo_ma", $this->datos);
    }

    public function lista_awb_confirma()
    {


        $this->datos['navtext']   = "AWB";
        $this->datos['form']     = "segumiento/contenido";
        //  $this->datos['lista'] = $this->WhModel->lista_awb();
        $this->load->view("seguimiento/lista", $this->datos);
    }
    public function lista_awb_first_confirma()
    {

        $this->datos['lista'] = $this->ClasificadoModel->lista_awb_confirma();

        //  Var_dump($this->datos['lista']);

        $this->load->view("seguimiento/cuerpo_ma", $this->datos);
    }

    public function consulta_awb($inicio, $fin, $opcion, $buscar)
    {
        // $this->datos['navtext']   = "AWB";
        //$this->datos['form']     = "awb/contenido";
        $this->datos['lista'] = $this->ClasificadoModel->consulta_awb($inicio, $fin, $opcion, $buscar);
        $this->load->view("clasificacion/cuerpo_ma", $this->datos);
    }


    public function manifiesto_listado($mawb, $opc)
    {
        if ($opc == 1) {
            $campo = "tracking_number";
        } else {
            $campo = "mawb";
        }
        $this->datos['navtext']   = "Manifiestos";
        $this->datos['form']     = "clasificacion/contenido";
        $this->datos['lista'] = $this->ClasificadoModel->listado_warehouse($mawb, $campo);
        // var_dump( $this->datos['lista'] );

        $this->load->view("clasificacion/lista", $this->datos);
    }

    public function  guardar_referencia()
    {
        $id = $_POST['id'];
        $data = array(
            'id_manifiesto' => trim($_POST["id-manifiesto"]),
            'referencia' => trim($_POST['referencia']),
            'paquetes' => $_POST['paquetes'],
            'sacos' => $_POST['sacos'],

        );
        $this->ClasificadoModel->guardar_referencia($data);
    }

    public function  asignar_guia()
    {
        $ref = trim($_POST['catalogo_ref']);
        $master = $_POST['manifiesto_id'];
        echo $ref;
        // echo $master;
        $this->ClasificadoModel->asignar_guia($master, $ref);
    }




    public function consulta_conversacion($id)
    {
        $this->datos['dconversacion'] = $this->Clasificado2Model->consulta_conversacion($id);
        //  var_dump(  $this->datos['guias']);
        $this->load->view("seguimiento/lista_conversacion", $this->datos);
    }

    public function  guardar_comentario()
    {
        $id = $_POST['id-tracking'];
        $cometario = $_POST['comenta'];
        $fecha = date("Y-m-d H:i:s");
        $data = array(
            'fecha' => $fecha,
            'comentarios' => $cometario,
            'id_tracking' => $id,
            'usuario' =>   $_SESSION["nombre"]


        );
        $this->ClasificadoModel->guardar_comentario($data);
    }
    public function update_aceptado($id)
    {
        $this->ClasificadoModel->update_aceptado($id);
    }

    public function enviar_msg()
    {
        // 50373248877
        //50378271044;
        $token = "Bearer EAALCF4vf9RABAEpUnUsiWZBUGvOUPEw1qRcRh5h1j6HY0RZCFImIQ1GgK1WV1yDtCrRfhf6XimkCJq4GVR3xy9GkI9c9ZCxHvfMvzubPV4CiF8eqj1W6UhfUosMhCTXhX0NHjQHLZCcwEicULAxN4gG0MZAyZB8api1HudYnxljZCxt9E8lFXNMIyNxiNz0Ka7tpYKGx45mA3XqZBM5tytiT";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v13.0/100971466038694/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "messaging_product": "whatsapp",
            "to": "50378271044",
            "type": "template",
            "template": {
                "name": "hello_world",
                "language": {
                    "code": "en_US"
                }
            }
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization:' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function ma()
    {
        $fecha = date('Y-m-d H:i:s');
        $fecha = "2022-12-05T13:10:55-03:00";
        $header = array(
            'Host: qa.tracking.mailamericas.com',
            'Content-Type: application/json',
            'Cache-Control: no-cache'
        );

        $data = array(
            "grant_type" => "client_credentials",
            "client_id" => 42,
            "client_secret" => "1CFIlE3pXo2QdJgcnlocoRIoXsJ1Dr3veM8pWKj4",
            "scope" => "*"
        );

        $url = "https://tracking.mlasalvador.com/oauth/token";




        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
               "grant_type": "client_credentials",
                      "client_id" : 42,
                      "client_secret" : "1CFIlE3pXo2QdJgcnlocoRIoXsJ1Dr3veM8pWKj4",
                      "scope": "*"
          }',
            CURLOPT_HTTPHEADER => array(
                'Cache-Control: no-cache',
                'Content-Type: application/json',
                'Host: qa.tracking.mailamericas.com'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //   echo $response;
        $code = json_decode($response);
        $token = $code->access_token;

        //echo $token;

        //exit;
        $header = array(
            'Host: qa.tracking.mailamericas.com',
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            'Authorization: Bearer ' . $token
        );

        $data = array(
            "tracking" => "MLSV000010688RG", // m
            "date" =>  $fecha,
            "description" => "Entregado", // m
            "city" => "CCO", // m

            "received_by" => "Ricardo Herrera", //m
            "relationship" => null, //m
            "delivery_proof" => [
                "https://stargates.site/documentos_ma/638101f13d8b7.png"
            ],

            "gcs" => "18.6409321,-91.777005",
            "details" => ""
        );


        $url = "https://tracking.mlasalvador.com/api/v1/providers/events";



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
        echo $resultStr;
    }

    public function guardar_item_multi($id, $cantidad)
    {
        $detalle = $this->Clasificado2Model->obtener_guia_id($id);

        $peso = $detalle->gweight / $cantidad;
        $precio = $detalle->value / $cantidad;
        $precio = number_format($precio, 2);


        for ($i = 1; $i <= $cantidad; $i++) {
            $deta = array(
                'id_manifiesto'          => $detalle->id_manifiesto,
                'tracking_number'        => $detalle->tracking_number . "-" . $i,
                'gweight'                => $peso,
                'value'                  => $precio,
                'items'                  => $detalle->items,
                'commodity'              => $detalle->items_description,
                'consignee_account'      => $detalle->buyer_id,
                'consignee'              => $detalle->buyer,
                'buyer_company'          => $detalle->buyer_company,
                'consignee_address1'     => $detalle->buyer_address1,
                'buyer_address1_number'  => $detalle->buyer_address1_number,
                'consignee_address2'     => $detalle->buyer_address2,
                'consignee_address3'     => $detalle->buyer_address3,
                'buyer_district'         => $detalle->buyer_district,
                'consignee_city'         => $detalle->buyer_city,
                'consignee_state'        => $detalle->buyer_state,
                'consignee_country'      => $detalle->buyer_location,
                'consignee_zip'          => $detalle->buyer_zip,
                'consignee_phone'        => $detalle->buyer_phone,
                'consignee_email'        => $detalle->buyer_email,
                'hts'                    => $detalle->hts,
                'pieces'                 => $detalle->pieces,

            );
            $this->Clasificado2Model->guardar_item_multi($deta);
            $this->Clasificado2Model->update_item_multi($id, $peso, $precio);
        }
    }

    public function lista_departamentos()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.c807.com/guia.php/api/departamento',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9',
            ),
        ));

        $json = curl_exec($curl);
        curl_close($curl);
        // echo $json;
        $data = json_decode($json);

        $this->datos['departamento'] = $data;
        $this->load->view("catalogos/departamentos", $this->datos);
    }

    public function lista_municipios($id)
    {
        $code = $id;
        $curl = curl_init();
        $url = 'https://app.c807.com/guia.php/api/municipio?departamento=' . $code;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization:  91bbb9fac887f6d4723dd19842feb4f50c7dc5c9',
            ),
        ));

        $json = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($json);

        $this->datos['municipio'] = $data;
        $this->load->view("catalogos/municipios", $this->datos);
    }


    public function obtener_guia($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->Clasificado2Model->update_creacion($id);
        $rsl = $this->Clasificado2Model->obtener_guia($id);

        // var_dump($rsl);
        //    exit;

        $strfecha = date("Y-m-d") . ' 23:01:01';
        $fecha_recoleccion = $strfecha; //date("Y-m-d H:i:s"); //"2023-08-21 16:30";

        $telefono = $rsl->consignee_phone;

        $datos = array(
            "recolecta_fecha" => $fecha_recoleccion,
            "recolecta_comentario" => "MailAmericas StarShip",
            "tipo_entrega" => $rsl->tipo_entrega,
            "guias" => array(
                array(
                    "identificacion" => "100",
                    "nombre"         => $rsl->consignee,
                    "correo"         => $rsl->consignee_email,
                    "telefono"       => $telefono,
                    "direccion"      => $rsl->consignee_address1 . " " . $rsl->consignee_address2 . " " . $rsl->consignee_address3,
                    "tipo_servicio"  => $rsl->tipo_servicio,
                    "monto_cce"      => $rsl->cobro_final,
                    "agencia_destino" =>  $rsl->agencia_destino,
                    "con_seguro"     => "0",
                    "con_liquidacion_documento" => "0",
                    "departamento_id"   =>  $_POST['depto'],
                    "municipio_id"      =>  $_POST['munic'],
                    "detalle" => array(
                        array(
                            "guia"          => $rsl->tracking_number,
                            "peso"          => $rsl->gweight,
                            "contenido"     => $rsl->commodity,
                            "unidad_medida" => "LB",
                        )
                    )
                )
            )
        );



        $json = json_encode($datos);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.c807.com/guia.php/api/set_registro',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json = json_encode($datos),
            CURLOPT_HTTPHEADER => array(
                //  'Authorization: 471f1dfbfc37cc5be7aa6dc3dc374de3adc9cf82', //erick
                'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9'

            ),
            //'Authorization: 8e17b726-64ee-11eb-8d30-00505691901c', lla ve erick
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
        // var_dump($response);
        // echo json_decode( $response );

    }

    public function update_tracking_id($id, $recolecta, $guia)
    {
        $rsl =   $this->Clasificado2Model->update_tracking_id($id, $recolecta, $guia);
        echo $rsl;
    }

    public function asignar_partida($id, $partida)
    {
        $rsl =   $this->Clasificado2Model->asignar_partida($id, $partida);
        echo $rsl;
    }

    public function mail_a()
    {


        $header = array(
            'Host: qa.tracking.mailamericas.com',
            'Content-Type: application/json',
            'Cache-Control: no-cache'
        );

        $data = array(
            "grant_type" => "client_credentials",
            "client_id" => "39",
            "client_secret" => "cEWvVrkPLPcWNdF0QKCC2ZJHRJEN1CQAgQKbaH2A",

            "scope" => "*"
        );

        $url = "https://tracking.mlasalvador.com/oauth/token";


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
        curl_exec($ch);
        $rsl = curl_exec($ch);


        if (curl_exec($ch) === false) {
            echo 'Curl error: ' . curl_error($ch);
        }

        $errors = curl_error($ch);  //retorna errores                                                                                                          
        $result = curl_exec($ch);
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); //retorna el codigo de respuesta


        echo $returnCode;
        curl_close($ch);


        $code = json_decode($rsl);

        $token = $code->access_token;



        $header = array(
            'Host: qa.tracking.mailamericas.com',
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            'Authorization: Bearer ' . $token
        );

        $data = array(
            "tracking" => "ML0000149-TST", // m
            "date" => "2020-12-31T13:10:55-03:00", // m
            "description" => "Entregado", // m
            "city" => "CCO", // m

            "received_by" => "CAMILA BENÍTEZ 5981259-9", //m
            "relationship" => null, //m
            "delivery_proof" => [
                "https://app-tst.dinet.com.pe/AppApiIntegration/api/D4W/GetImage/28234",
                "https://app-tst.dinet.com.pe/AppApiIntegration/api/D4W/GetImage/28235",
                "https://app-tst.dinet.com.pe/AppApiIntegration/api/D4W/GetImage/28236",
                "https://app-tst.dinet.com.pe/AppApiIntegration/api/D4W/GetImage/28237"
            ],

            "gcs" => "18.6409321,-91.777005",
            "details" => ""
        );

        $url = "https://tracking.mlasalvador.com/api/v1/providers/events";


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
        curl_close($ch);

        echo $resultStr;
    }

    public function pdf_wh2_bk($id, $master)
    {
        $this->datos['data'] = $this->ClasificadoModel->lista_warehouse_pdf2($id);
        $this->load->view("clasificacion/pdf", $this->datos);
    }

    public function pdf_wh2($id, $master)
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);


        //$this->datos['data'] = $this->WhModel->lista_warehouse_pdf2($id);
        //$this->load->view("awb/pdf", $this->datos);
        //include getcwd()."/application/libraries/fpdf/fpdf.php";
        include getcwd() . "/application/libraries/phpqrcode/qrlib.php";
        //echo $master;
        $datos['referencia'] =  $this->ClasificadoModel->lista_warehouse_pdf2($id);
        // var_dump($datos['referencia'] );
        // exit;
        foreach ($datos['referencia'] as $item) {
            $name_qr =  $item->tracking_number;
            QRcode::png($name_qr, getcwd() . "/public/qr_ma/" . $name_qr . ".png",       5, 5, 5, 5);
        }

        $this->pdf = new FPDF('L', 'mm', array(102, 152),      1, 0, 'C', 0);

        $archivo = "";
        //var_dump($datos['referencia']);
        foreach ($datos['referencia'] as $item) {
            $this->pdf->SetMargins(1, 3, 2);
            $this->pdf->AddPage();
            $this->pdf->AliasNbPages();
            $this->pdf->SetTitle($master);
            $this->pdf->SetMargins(1, 1, 1);
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->pdf->SetLineWidth(0.5);
            //$this->pdf->Rect(7, 7, 138, 87, 5, '13', 'FD');

            $this->pdf->SetFillColor(200, 200, 200);
            $archivo = "";

            // $name_qr =  $item->tracking_number;
            $name_qr =  $item->tracking_number . ".png";

            $this->pdf->Rect(4, 4, 143, 93, 5, '1', 'DO');                // L,  D, W, 
            $this->pdf->Rect(4, 4, 36, 35, 9, '2', 'DO');
            $this->pdf->Image(getcwd() . "/public/qr_ma/" . $name_qr,          7, 6, 30, 30);
            $this->pdf->SetMargins(10, 1, 10);
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Ln(2);
            $this->pdf->Cell(48, 4, 'Principal',                                                     0, 0, 'R', 0);
            $this->pdf->Cell(80, 4, 'Fecha: ' . $item->fecha_creacion,                                 0, 0, 'R', 0);
            $this->pdf->Ln(4);
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Cell(48, 4, 'Telefono:',                                                     0, 0, 'R', 0);
            $this->pdf->Ln(4);
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Cell(49, 4, 'DESTINO',                                                       0, 0, 'R', 0);
            $this->pdf->Ln(5);
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->SetFillColor(255, 255, 255);
            $this->pdf->SetXY(41, 17);
            $this->pdf->MultiCell(100, 4, 'Nombre: ' . utf8_decode($item->consignee),                  0, 1, 'R', 0);
            $this->pdf->SetXY(41, 21);

            $this->pdf->MultiCell(100, 4, 'Contacto: ' . $item->consignee_email,                       0, 1, 'R', 0);
            $this->pdf->SetXY(41, 25);
            $this->pdf->SetFont('Arial', '', 11);
            $this->pdf->MultiCell(69, 4, 'Telefono: ' . $item->consignee_phone,                        0, 1, 'R', 0);
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->SetFillColor(255, 255, 255);
            $this->pdf->SetXY(41, 29);
            $this->pdf->MultiCell(100, 4, 'Direccion: ' . utf8_decode($item->consignee_address1),      0, 1, 'L', 0);
            $this->pdf->SetXY(41, 37);
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->MultiCell(75, 2, 'Referencia: ' . $item->referencia,                           0, 1, 'L', 0);
            $this->pdf->Ln(5);

            $this->pdf->SetLineWidth(0.8);
            $this->pdf->Line(75, 65, 75, 50);

            $this->pdf->SetFont('Arial', 'B', 16);
            $this->pdf->Cell(89, 4, $item->tracking_number,                    0, 0, 'R', 0);

            $this->pdf->SetFont('Arial', 'B', 30);
            $this->pdf->Ln(5);
            $this->pdf->SetXY(30, 54);
            $this->pdf->MultiCell(35, 4, $item->tipo_servicio,                 0, 1, 'R', 0);
            $this->pdf->SetXY(98, 54);
            $this->pdf->MultiCell(35, 4, $item->departamento_code,             0, 1, 'L', 0);
            $this->pdf->SetFont('Arial', '', 10);

            $this->pdf->SetFillColor(255, 255, 255);
            $this->pdf->SetXY(99, 60);
            $this->pdf->MultiCell(35, 4, utf8_decode($item->municipio_name),        0, 1, 'C', 0);

            $archivo = $item->tracking_number;

            $this->pdf->Ln(3);
            $this->pdf->SetFont('Arial', '', 10);

            $this->pdf->Cell(27, 2, 'A Cobrar: $' . round($item->cobro_final, 2),           0, 0, 'R', 0);
            $this->pdf->Cell(80, 2, 'Pago: Contado',                            0, 0, 'R', 0);
            $this->pdf->Ln(4);
            $this->pdf->Cell(25, 2, 'Peso: ' . $item->gweight . ' lb',              0, 0, 'L', 0);
            $this->pdf->Cell(85, 2, 'Tipo de Entrega: ',                        0, 0, 'R', 0);
            $this->pdf->Ln(4);
            $this->pdf->Cell(40, 2, 'Peso Maximo: 0.00 lb',                     0, 0, 'L', 0);
            $this->pdf->SetFont('Arial', 'B', 30);
            $this->pdf->SetXY(90, 79);
            $this->pdf->MultiCell(45, 5, $item->tipo_entrega,                   0, 1, 'R', 0);
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Ln(4);
            $this->pdf->Cell(40, 2, 'Especiales',                               0, 0, 'L', 0);
        }

        $destino      = getcwd() . "/document/qr/";
        $desabsoluto  = "document/qr/";

        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }

        $nombre_archivo =  $master . '.pdf';
        $this->pdf->Output("F", $destino . $nombre_archivo, true);
        $this->pdf->close();
        $datosretorno  = array(
            'destino'        =>  $desabsoluto,
            'nombre_archivo' =>  $nombre_archivo,
        );
        echo json_encode($datosretorno);
    }




    public function departamento_old()
    {
        $curl = curl_init();
        $url = 'https://app.c807.com/guia.php/api/departamento';

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9'
            ),
        ));
        $rsp = curl_exec($curl);
        curl_close($curl);
        $items = json_decode($rsp, true);

        $curl2 = curl_init();
        $url2 = 'https://app.c807.com/guia.php/api/municipio?departamento=2';
        curl_setopt_array($curl2, array(
            CURLOPT_URL => $url2,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9'
            ),
        ));
        $rsp2 = curl_exec($curl2);
        curl_close($curl2);
        $items2 = json_decode($rsp2, true);


        $this->datos['municipios'] = $items2;
        $this->datos['departamentos'] = $items;
        $this->load->view("clasificacion/lists_depart", $this->datos);
        //var_dump($items);
        //echo $items[0]['guia'];
        //$texto = $items[0]['guia'];
    }

    public function municipio_old($id)
    {
        $s = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $curl = curl_init();
        $url = 'https://app.c807.com/guia.php/api/municipio?departamento=' . $s;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9'
            ),
        ));
        $rsp = curl_exec($curl);
        curl_close($curl);
        $items = json_encode($rsp, true);
        echo $rsp;
        //$this->datos['departamentos'] = $items;
        //$this->load->view("awb/lists_depart", $this->datos);
        //var_dump($items);
        //echo $items[0]['guia'];
        //$texto = $items[0]['guia'];
    }

    public function update_depart()
    {

        $id_manifiesto = $_POST['id_manifiesto'];

        $departamentos = $_POST['ls_depart'];
        $municipios = $_POST['ls_municipio'];
        $dui = $_POST['dui'];
        list($id_d, $codigo_d, $nombre_d) = explode('-', $departamentos);

        $nombre_d = utf8_encode($nombre_d);


        list($id_m, $nombre_m) = explode('-', $municipios);


        $rsl =  $this->ClasificadoModel->update_dm($id_manifiesto, $id_d, $codigo_d, $nombre_d, $id_m, $nombre_m, $dui);

        echo $rsl;
    }

    // segmento  para  dibujar  pdf de los manifiestos    
    public function generar_manifiesto($id, $referencia)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $id_manifiesto = $id;

        //include getcwd()."/application/libraries/fpdf/fpdf.php";
        $Manifiesto  = 'manifiesto';
        $time        = time();
        $fecha       = date("d-m-Y (H:i:s)", $time);
        // $fecha =  date()
        $dia         = date("d");
        $mes         = date("m");
        $periodo     = date("Y");
        $mora        = 0;
        setlocale(LC_ALL, 'es_ES');
        $monthNum    = $mes;
        $dateObj     = DateTime::createFromFormat('!m', $monthNum);
        $monthName   = strftime('%B', $dateObj->getTimestamp());
        //$this->pdf   = new FPDF('L', 'mm','Legal'); 
        $this->pdf   = new FPDF();

        $dlist_manifiestos        = $this->ClasificadoModel->lista_warehouse1($id, $referencia);
        // var_dump($dlist_manifiestos);
        foreach ($dlist_manifiestos  as  $row) {
            $this->pdf->setY(0);
            $this->pdf->setX(0);
            //$SHIPPING = $row->value * 0.15;
            $SHIPPING = 0;
            $TAX      = 0;


            $this->pdf->AddPage();
            $this->pdf->AliasNbPages();
            $this->pdf->SetLeftMargin(10);
            $this->pdf->SetRightMargin(10);
            $this->pdf->SetFillColor(200, 200, 200);
            // $this->pdf->SetFont('Arial', 'B', 7);


            $this->pdf->SetFillColor(255, 255, 255);
            $this->pdf->SetTextColor(46, 44, 44);
            $this->pdf->SetFont('Arial', '', 11);
            $this->pdf->Ln(5);


            $this->pdf->Cell(15, 12, '', 0, 0, 'R', 1);
            $this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/MailAmerica.jpg', 45, 16, 50, 30), 0, 0, 'L', 0);
            //  $this->pdf->Cell(40, 16, $this->pdf->Image(getcwd() . '/public/img/logo_2.png', 17, 16, 30, 16), 0, 0, 'L', 0);


            $this->pdf->Ln(30);



            $this->pdf->Cell(200, 12, 'No. 111 Tianlin Road, Tower, Shangai ', 0, 0, 'L', 0);
            $this->pdf->Ln(10);
            $this->pdf->Cell(200, 12, 'Shangai ', 0, 0, 'L', 0);
            // $this->pdf->Ln(10);
            // $this->pdf->Cell(200, 12, 'PO Box 81226 Seattle, WA 98108-1226', 0, 0, 'L', 0);
            $this->pdf->Ln(15);
            $this->pdf->Cell(200, 12, 'ENVIADO A: ' . $row->consignee, 0, 0, 'L', 0);
            $this->pdf->Ln(10);


            $this->pdf->MultiCell(150, 5, 'DIRECCION: ' . $row->consignee_address1, 0, 'TB', 'L', false);
            $this->pdf->Ln(10);
            $this->pdf->Cell(200, 12, 'Tel: ' . $row->consignee_phone, 0, 0, 'L', 0);
            $this->pdf->Ln(10);


            $this->pdf->Cell(200, 12, 'DUI: ' . $row->consignee_account, 0, 0, 'L', 0);
            $this->pdf->Ln(15);


            $this->pdf->SetFont('Arial', 'B', 11);

            $valory = 0;
            $valorx = 0;


            $valory =  $this->pdf->getY();
            $valorx = $this->pdf->getX();
            $this->pdf->MultiCell(10, 7.5, utf8_decode(''), 0, 'TB', 'C', false);
            $this->pdf->setY($valory);
            $this->pdf->setX($valorx + 10);
            $this->pdf->MultiCell(40, 12, utf8_decode('N° de factura'), 1, 'TB', 'C', false);
            $this->pdf->setY($valory);
            $this->pdf->setX($valorx + 50);
            $this->pdf->MultiCell(35, 12, utf8_decode('Fecha de factura'), 1, 'TB', 'C', false);
            $this->pdf->setY($valory);
            $this->pdf->setX($valorx + 85);
            $this->pdf->MultiCell(30, 12, utf8_decode('Término'), 1, 'TB', 'C', false);
            $this->pdf->setY($valory);
            $this->pdf->setX($valorx + 115);
            $this->pdf->MultiCell(40, 12, utf8_decode('No de Cliente'), 1, 'TB', 'C', false);
            $this->pdf->setY($valory);
            $this->pdf->setX($valorx + 155);
            $this->pdf->MultiCell(20, 6, utf8_decode('No de Pág.'), 1, 'TB', 'C', false);
            $this->pdf->Ln(0);

            $this->pdf->SetFont('Arial', '', 9);

            $this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
            $this->pdf->Cell(40, 9, $row->tracking_number, 1, 0, 'C', 0);
            $this->pdf->Cell(35, 9, date("d-m-Y", strtotime($row->fecha)), 1, 0, 'C', 0);

            //$this->pdf->Cell(35, 12,  date("d-m-Y", strtotime($row->fecha)) , 1, 0, 'C', 0);
            $this->pdf->Cell(30, 9, 'FOB', 1, 0, 'C', 0);
            $this->pdf->Cell(40, 9, $row->consignee_account, 1, 0, 'C', 0);
            $this->pdf->Cell(20, 9, '1', 1, 0, 'C', 0);
            $this->pdf->Ln(10);
            $this->pdf->SetFont('Arial', 'B', 20);
            $this->pdf->Cell(50, 9, 'FACTURA', 0, 0, 'L', 0);
            $this->pdf->Ln(10);
            $this->pdf->SetFont('Arial', 'B', 11);

            $valory1 = 0;
            $valorx1 = 0;


            $valory1 =  $this->pdf->getY();
            $valorx1 = $this->pdf->getX();

            $this->pdf->MultiCell(10, 7.5, utf8_decode(''), 0, 'TB', 'C', false);
            $this->pdf->setY($valory1);
            $this->pdf->setX($valorx1 + 10);
            $this->pdf->MultiCell(30, 12, utf8_decode('Cantidad'), 1, 'TB', 'J', false);
            $this->pdf->setY($valory1);
            $this->pdf->setX($valorx1 + 40);
            $this->pdf->MultiCell(115, 12, utf8_decode('Cod. Artículo Descripción '), 1, 'TB', 'J', false);
            $this->pdf->setY($valory1);
            $this->pdf->setX($valorx1 + 155);
            $this->pdf->MultiCell(17, 6, utf8_decode('Precio Unitario'), 1, 'TB', 'J', false);
            $this->pdf->setY($valory1);
            $this->pdf->setX($valorx1 + 172);
            $this->pdf->MultiCell(20, 6, utf8_decode('Precio Total'), 1, 'TB', 'J', false);

            $this->pdf->Ln(0);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
            $this->pdf->Cell(30, 9, $row->pieces, 1, 0, 'C', 0);
            $this->pdf->Cell(115, 9, $row->commodity, 1, 0, 'C', 0);
            $this->pdf->Cell(17, 9, number_format($row->value, 2), 1, 0, 'C', 0);
            $this->pdf->Cell(20, 9, number_format($row->value, 2), 1, 0, 'C', 0);

            $this->pdf->Ln(10);
            $this->pdf->SetFont('Arial', 'B', 11);
            $this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
            $this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
            $this->pdf->Cell(115, 9, 'TAX', 0, 0, 'R', 0);
            $this->pdf->SetFont('Arial', '', 9);

            $this->pdf->Cell(17, 9, '', 10, 0, 'C', 0);
            $this->pdf->Cell(20, 9, number_format($TAX, 2), 0, 0, 'C', 0);
            $this->pdf->Ln(10);
            $this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
            $this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
            $this->pdf->SetFont('Arial', 'B', 11);
            $this->pdf->Cell(115, 9, 'SHIPPING', 0, 0, 'R', 0);
            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);
            $this->pdf->Cell(20, 9, number_format($SHIPPING, 2), 0, 0, 'C', 0);
            $this->pdf->Ln(10);



            $this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
            $this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
            $this->pdf->SetFont('Arial', 'B', 11);

            $this->pdf->Cell(115, 9, 'TOTAL USD', 0, 0, 'R', 0);
            $this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);
            $this->pdf->SetFont('Arial', '', 9);

            $this->pdf->Cell(20, 9, number_format(($TAX + $SHIPPING + $row->value), 2), 0, 0, 'C', 0);
        }


        $destino      = getcwd() . "/document/Manifiesto/";
        $desabsoluto  =  "document/Manifiesto/";
        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }
        $nombre_archivo =  'Manifiesto' . date("d-m-Y") . '_' . $id . '.pdf';
        $this->pdf->Output("F", $destino . $nombre_archivo, true);
        $this->pdf->close();
        $datosretorno  = array(
            'destino'        => $desabsoluto,
            'nombre_archivo' => $nombre_archivo,

        );
        // header('Content-type: appliation/json');
        echo json_encode($datosretorno);
    }


    public function aplicar_estatus($guia)
    {

        if ($guia == "undefined") {
        } else {


            //   CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/MLSV000003302RG',
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/' . $guia,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9'

                ),
            ));
            // 'Authorization: d3a22ca0cf04668a450a337a31ede0d9eeac64e3' //token anterior PRUEGCV(GLOBAL CARGO)
            //'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9' //token actual 
            $response = curl_exec($curl);

            curl_close($curl);

            $data = json_decode($response);
            //  $data = json_decode($response, true);

            $this->datos['lista'] = $data;
            // foreach ($this->datos['lista'] as $row) {
            //     if ($row->codigo == 15) {
            //         var_dump($row->pod);
            //     }
            // }


            $query =     $this->ClasificadoModel->guia_alternativa($guia);
            // var_dump( $data);
            foreach ($query as $fila) {
                $warehouse = $fila->tracking_number;
            }

            if ($warehouse) {
                $guia = $warehouse;
            } else {
                $warehouse = $guia;
            }


            foreach ($this->datos['lista'] as $row) {
                $result =  $this->Clasificado2Model->buscar_estatus($row->codigo, $warehouse);
                // echo $warehouse ."  here";
                //   var_dump($result);
                if ($result) {
                    echo 'uno';
                } else {

                    date_default_timezone_set("America/El_Salvador");
                    $date = date('Y-m-d H:i:s');
                    $fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $row->fecha);
                    $fecha_iso = $fecha_tmp->format(DateTime::ATOM);

                    $item = "";
                    $fecha = "";
                    // $warehouse = "";
                    $id_estatus = "";
                    $casillero = "";
                    $id_pais =  "";

                    /* aqui prueba */

                    //$datos = array(
                    //'json_string' => $data
                    // );
                    //   $this->Manifiesto_model->guardar_json($datos);

                    /* fin aui prueba */

                    $item = 1;
                    $fecha =  $row->fecha;
                    $warehouse =  $guia;
                    $id_estatus = $row->codigo;
                    $nombre_estatus = $row->estatus;
                    $casillero = "100";
                    $id_pais =  1;
                    $pod_nombre = "";
                    $pod_valor = "";
                    $document_name = "";
                    $ruta = "";

                    //    $this->response($row);
                    if ($id_estatus == 15) {
                        $ruta = "https://stargates.site/documentos_ma/";
                        // foreach ( $row['pod'] as $key => $detalle) {
                        foreach ($row->pod as  $detalle) {
                            $pod_nombre = $detalle->nombre;
                            $pod_valor  = $detalle->valor;
                            $img = str_replace('data:image/png;base64,', '', $pod_valor);
                            $img = str_replace('data:image/png;base64,', '', $img);
                            $img = str_replace(' ', '+', $img);
                            $data1 = base64_decode($img);
                            $document_name = uniqid() . '.png';
                            //  $file = "/home/km96wogi8pjb/public_html/documentos_ma/". uniqid() . '.jpg';
                            $file = "/home/km96wogi8pjb/public_html/documentos_ma/" . $document_name;

                            $success = file_put_contents($file, $data1);
                            //    echo "bytes fueron escritos en $file";

                        }
                    }
                    //   $this->response($row);

                    echo  $pod_nombre;
                    if ($id_estatus == 15) {
                        $ruta = $ruta . $document_name;
                    } else {
                        $ruta = null;
                    }

                    $datos = array(

                        'item' => $item,

                        'fecha' => date('Y-m-d H:i:s'),

                        'warehouse' => $warehouse,

                        'id_estatus' => $id_estatus,

                        'casillero' =>  $casillero,

                        'id_pais' =>  $id_pais,

                        'pod_nombre' =>  $pod_nombre,

                        'pod_valor' => "",

                        'json_string' => '{    "guia": ""}', //$data, data trae el json cpmpleto, utilizarlo para testing

                        'ruta_foto' => $ruta,

                        'fechaiso' => $fecha_iso

                    );


                    $this->Clasificado2Model->guardar_estatus($datos);

                    $token_ma = $this->token_ma(); //obtiene el token



                    $header = array(
                        'Host: tracking.mailamericas.com',
                        'Content-Type: application/json',
                        'Cache-Control: no-cache',
                        'Authorization: Bearer ' . $token_ma
                    );

                    $data = array(
                        "tracking" => $warehouse,
                        "date" =>  $fecha_iso,
                        "description" => $nombre_estatus,
                        "city" => "CCO",
                        "received_by" => null,
                        "relationship" => null,
                        "delivery_proof" => [
                            $ruta . $document_name
                        ],

                        "gcs" => "",
                        "details" => ""
                    );

                    $url = "https://tracking.mlasalvador.com/api/v1/providers/events";


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
                        "tracking_number" =>  $warehouse,
                        "descripcion" => $nombre_estatus, // m
                        "fecha" =>  $fecha,
                        "fechaiso" =>  $fecha_iso

                    );
                    $this->Clasificado2Model->guardar_logma($data);
                }
                //  echo $row->estatus;
            }
            //  echo $response;

        }
        $this->load->view("seguimiento/aplicar_estatus", $this->datos);
    }
    function token_ma()
    {

        $header = array(
            'Host: tracking.mailamericas.com',
            'Content-Type: application/json',
            'Cache-Control: no-cache'
        );

        $data = array(
            "grant_type" => "client_credentials",
            "client_id" => 42,
            "client_secret" => "1CFIlE3pXo2QdJgcnlocoRIoXsJ1Dr3veM8pWKj4",
            "scope" => "*"
        );

        $url = "https://tracking.mlasalvador.com/oauth/token";



        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://tracking.mailamericas.com/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                    "grant_type": "client_credentials",
                           "client_id" : 42,
                           "client_secret" : "1CFIlE3pXo2QdJgcnlocoRIoXsJ1Dr3veM8pWKj4",
                           "scope": "*"
               }',
            CURLOPT_HTTPHEADER => array(
                'Cache-Control: no-cache',
                'Content-Type: application/json',
                'Host: tracking.mailamericas.com'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //   echo $response;
        $code = json_decode($response);
        // var_dump($code);
        $token = $code->access_token;
        return $token;
    }
    public function update_info()
    {


        $id            = $_POST['id-paquete'];
        $dui           = $_POST['dui'];
        $correo        = $_POST['correo'];
        $telefono      = $_POST['telefono'];
        $direccion     = $_POST['direccion'];
        $agencia       = $_POST['agencia'];
        $tipo_entrega  = $_POST['tipo_entrega'];
        $tipo_servicio = $_POST['tipo_servicio'];

        $this->Clasificado2Model->update_info($id, $dui, $correo, $telefono, $direccion, $agencia, $tipo_entrega,  $tipo_servicio);
    }

    public function verificar_lm($id)
    {
        // $result =  $this->WhModel->verificar_lm($id); 

        //agrega departamento y municipio a la guia


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.c807.com/guia.php/catalogo/departamento',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9',
                'Content-type: text/html; charset=UTF-8'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $respuesta = json_decode($response);
        // echo $response;

        foreach ($respuesta as $row) {
            //$row->nombre;
            $depto = $row->id;
            $name_d = $row->nombre;
            $code = $row->codigo;

            $curlm = curl_init();

            curl_setopt_array($curlm, array(
                CURLOPT_URL => 'https://app.c807.com/guia.php/api/municipio?departamento=' . $row->id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9',

                ),
            ));


            $response1 = curl_exec($curlm);

            curl_close($curlm);
            $respuesta1 = json_decode($response1);
            // echo $response;


            foreach ($respuesta1 as $fila) {
                $munic = $fila->id;
                $name_m = $fila->nombre;

                // echo $depto." " .   $munic . " ".$fila->nombre;


                // echo $row->nombre;
                $cadena = $fila->nombre;
                $opc1 = $cadena;
                $opc2 = mb_strtoupper($cadena, 'UTF-8');
                $opc3 = strtolower($cadena);
                $opc4 = strtoupper($cadena);
                $string = $cadena;
                $opc5 = $this->eliminar_acentos($string);
                $opc6 = strtoLower($this->eliminar_acentos($string));


                $result =  $this->ClasificadoModel->update_lm($id, $depto, $munic, $opc1,  $opc2,  $opc3,  $opc4,  $opc5,  $opc6, $name_d, $name_m, $code);
            }
        }
    }

    function cargar_referencia()
    {

        $this->datos['lista'] =  $this->ClasificadoModel->cargar_referencia();
        // var_dump($rsl);
        foreach ($this->datos['lista'] as $fila) {
            //echo $fila->AWB;
            $this->ClasificadoModel->update_ref($fila->awb, $fila->tracking_number, $fila->referencia,  $fila->id_manifiesto);
        }

        echo "Finish";
    }



    function eliminar_acentos($cadena)
    {

        //Reemplazamos la A y a
        $cadena = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $cadena
        );

        //Reemplazamos la E y e
        $cadena = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $cadena
        );

        //Reemplazamos la I y i
        $cadena = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $cadena
        );

        //Reemplazamos la O y o
        $cadena = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $cadena
        );

        //Reemplazamos la U y u
        $cadena = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $cadena
        );

        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç'),
            array('N', 'n', 'C', 'c'),
            $cadena
        );

        return $cadena;
    }

    public function update_pod()
    {
        $result = $this->ClasificadoModel->obtener_estatus15();

        foreach ($result as $row) {
            $rsl = $this->ClasificadoModel->update_pod($row->warehouse);
        }
    }

    public function update_bag()
    {
        $result = $this->ClasificadoModel->obtener_bags();
        //   var_dump( $result);
        //   exit;
        foreach ($result as $row) {

            $this->ClasificadoModel->update_bag($row->COL2, $row->COL3);
        }
    }

    public function reporte_manifiesto($idpreclasificacion)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['lista'] = $this->ClasificadoModel->reporte_guias($idpreclasificacion);
        $this->load->view("clasificacion/vista_manifiesto", $this->datos);
    }

    public function reporte_guias($idpreclasificacion)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);



        $id            =     0;
        $Manifiesto    =    'Reporte de Guias';
        $time          =    time();
        $dia           =    date("d");
        $mes           =    date("m");
        $fecha         =    date("d-m-Y ");
        $periodo       =    date("Y");
        $mora          =    0;
        setlocale(LC_ALL, 'es_ES');
        $monthNum      =    $mes;
        $dateObj       =    DateTime::createFromFormat('!m', $monthNum);
        $monthName     =    strftime('%B', $dateObj->getTimestamp());

        $GLOBALS['fecha'] = "Reporte de Manifiesto ";
        $pdf = new PDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', '', 11);
        $datos         =    $this->ClasificadoModel->reporte_guias($idpreclasificacion);

        $pdf->reportemanifiesto($datos);

        $destino = getcwd() . "/document/reporte_ventas/";

        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }

        $archivo = 'ReporteGuias' . '.pdf';
        $pdf->Output("F", $destino . $archivo, true);
        $pdf->close();
        echo  $archivo;
    }

    public function reload_guia()
    {

        $request =  $this->ClasificadoModel->reload_guia();
        foreach ($request as $rajo) {

            //   CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/MLSV000003302RG',
            $curl = curl_init();
            $guia = $rajo->guia;
            // echo $guia;
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/' . $guia,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9'


                ),
            ));


            //'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9' token cuenta_actual
            $response = curl_exec($curl);

            curl_close($curl);

            $data = json_decode($response);
            //  $data = json_decode($response, true);

            $this->datos['lista'] = $data;

            $error = $data->errores;
            //  echo $error[0]->codigo;
            //  var_dump($error);
            if ($error[0]->codigo == 20) {
                //  echo $guia .'<br>';
                $this->ClasificadoModel->bandera($guia, 2);
            } else {




                foreach ($this->datos['lista'] as $row) {
                    $result =  $this->Clasificado2Model->buscar_estatus($row->codigo, $guia);
                    if ($result) {
                    } else {



                        date_default_timezone_set("America/El_Salvador");
                        $date = date('Y-m-d H:i:s');
                        $fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $row->fecha);
                        $fecha_iso = $fecha_tmp->format(DateTime::ATOM);

                        $item = "";
                        $fecha = "";
                        $warehouse = "";
                        $id_estatus = "";
                        $casillero = "";
                        $id_pais =  "";

                        /* aqui prueba */

                        //$datos = array(
                        //'json_string' => $data
                        // );
                        //   $this->Manifiesto_model->guardar_json($datos);

                        /* fin aui prueba */

                        $item = 1;
                        $fecha =  $row->fecha;
                        $warehouse =  $guia;
                        $id_estatus = $row->codigo;
                        $nombre_estatus = $row->estatus;
                        $casillero = "100";
                        $id_pais =  1;
                        $pod_nombre = "";
                        $pod_valor = "";
                        $document_name = "";
                        $ruta = "";

                        //    $this->response($row);
                        if ($id_estatus == 15) {
                            $ruta = "https://stargates.site/documentos_ma/";
                            // foreach ( $row['pod'] as $key => $detalle) {
                            foreach ($row->pod as  $detalle) {
                                $pod_nombre = $detalle->nombre;
                                $pod_valor  = $detalle->valor;
                                $img = str_replace('data:image/png;base64,', '', $pod_valor);
                                $img = str_replace('data:image/png;base64,', '', $img);
                                $img = str_replace(' ', '+', $img);
                                $data1 = base64_decode($img);
                                $document_name = uniqid() . '.png';
                                //  $file = "/home/km96wogi8pjb/public_html/documentos_ma/". uniqid() . '.jpg';
                                $file = "/home/km96wogi8pjb/public_html/documentos_ma/" . $document_name;

                                $success = file_put_contents($file, $data1);
                                //    echo "bytes fueron escritos en $file";

                            }
                        }
                        //   $this->response($row);

                        echo  $pod_nombre;
                        if ($id_estatus == 15) {
                            $ruta = $ruta . $document_name;
                        } else {
                            $ruta = null;
                        }

                        $datos = array(

                            'item' => $item,

                            'fecha' => date('Y-m-d H:i:s'),

                            'warehouse' => $warehouse,

                            'id_estatus' => $id_estatus,

                            'casillero' =>  $casillero,

                            'id_pais' =>  $id_pais,

                            'pod_nombre' =>  $pod_nombre,

                            'pod_valor' => "",

                            'json_string' => '{    "guia": ""}', //$data, data trae el json cpmpleto, utilizarlo para testing

                            'ruta_foto' => $ruta,

                            'fechaiso' => $fecha_iso

                        );


                        $this->Clasificado2Model->guardar_estatus($datos);

                        $token_ma = $this->token_ma(); //obtiene el token



                        $header = array(
                            'Host: tracking.mailamericas.com',
                            'Content-Type: application/json',
                            'Cache-Control: no-cache',
                            'Authorization: Bearer ' . $token_ma
                        );

                        $data = array(
                            "tracking" => $warehouse,
                            "date" =>  $fecha_iso,
                            "description" => $nombre_estatus,
                            "city" => "CCO",
                            "received_by" => null,
                            "relationship" => null,
                            "delivery_proof" => [
                                $ruta . $document_name
                            ],

                            "gcs" => "",
                            "details" => ""
                        );

                        $url = "https://tracking.mlasalvador.com/api/v1/providers/events";


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
                            "tracking_number" =>  $warehouse,
                            "descripcion" => $nombre_estatus, // m
                            "fecha" =>  $fecha,
                            "fechaiso" =>  $fecha_iso

                        );
                        $this->Clasificado2Model->guardar_logma($data);
                        $this->ClasificadoModel->bandera($guia, 1);
                    }
                    //  echo $row->estatus;
                }
                //  echo $response;
            }
        }
    }

    public function mario()
    {

        $request =  $this->ClasificadoModel->reload_guia_mario();
        foreach ($request as $rajo) {

            //   CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/MLSV000003302RG',
            $curl = curl_init();
            $guia = $rajo->guia;
            // echo $guia;
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/' . $guia,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9'


                ),
            ));


            //'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9' token cuenta_actual
            $response = curl_exec($curl);

            curl_close($curl);

            $data = json_decode($response);
            //  $data = json_decode($response, true);

            $this->datos['lista'] = $data;

            $error = $data->errores;
            //  echo $error[0]->codigo;
            //  var_dump($error);
            echo $guia . '<br>';
            if ($error[0]->codigo == 20) {
                //  echo $guia .'<br>';
                $this->ClasificadoModel->bandera_mario($guia, 2);
            } else {




                foreach ($this->datos['lista'] as $row) {
                    $result =  $this->Clasificado2Model->buscar_estatus($row->codigo, $guia);
                    if ($result) {
                    } else {



                        date_default_timezone_set("America/El_Salvador");
                        $date = date('Y-m-d H:i:s');
                        $fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $row->fecha);
                        $fecha_iso = $fecha_tmp->format(DateTime::ATOM);

                        $item = "";
                        $fecha = "";
                        $warehouse = "";
                        $id_estatus = "";
                        $casillero = "";
                        $id_pais =  "";

                        /* aqui prueba */

                        //$datos = array(
                        //'json_string' => $data
                        // );
                        //   $this->Manifiesto_model->guardar_json($datos);

                        /* fin aui prueba */

                        $item = 1;
                        $fecha =  $row->fecha;
                        $warehouse =  $guia;
                        $id_estatus = $row->codigo;
                        $nombre_estatus = $row->estatus;
                        $casillero = "100";
                        $id_pais =  1;
                        $pod_nombre = "";
                        $pod_valor = "";
                        $document_name = "";
                        $ruta = "";

                        //    $this->response($row);
                        if ($id_estatus == 15) {
                            $ruta = "https://stargates.site/documentos_ma/";
                            // foreach ( $row['pod'] as $key => $detalle) {
                            foreach ($row->pod as  $detalle) {
                                $pod_nombre = $detalle->nombre;
                                $pod_valor  = $detalle->valor;
                                $img = str_replace('data:image/png;base64,', '', $pod_valor);
                                $img = str_replace('data:image/png;base64,', '', $img);
                                $img = str_replace(' ', '+', $img);
                                $data1 = base64_decode($img);
                                $document_name = uniqid() . '.png';
                                //  $file = "/home/km96wogi8pjb/public_html/documentos_ma/". uniqid() . '.jpg';
                                $file = "/home/km96wogi8pjb/public_html/documentos_ma/" . $document_name;

                                $success = file_put_contents($file, $data1);
                                //    echo "bytes fueron escritos en $file";

                            }
                        }
                        //   $this->response($row);

                        echo  $pod_nombre;
                        if ($id_estatus == 15) {
                            $ruta = $ruta . $document_name;
                        } else {
                            $ruta = null;
                        }

                        $datos = array(

                            'item' => $item,

                            'fecha' => date('Y-m-d H:i:s'),

                            'warehouse' => $warehouse,

                            'id_estatus' => $id_estatus,

                            'casillero' =>  $casillero,

                            'id_pais' =>  $id_pais,

                            'pod_nombre' =>  $pod_nombre,

                            'pod_valor' => "",

                            'json_string' => '{    "guia": ""}', //$data, data trae el json cpmpleto, utilizarlo para testing

                            'ruta_foto' => $ruta,

                            'fechaiso' => $fecha_iso

                        );


                        $this->Clasificado2Model->guardar_estatus($datos);

                        $token_ma = $this->token_ma(); //obtiene el token



                        $header = array(
                            'Host: tracking.mailamericas.com',
                            'Content-Type: application/json',
                            'Cache-Control: no-cache',
                            'Authorization: Bearer ' . $token_ma
                        );

                        $data = array(
                            "tracking" => $warehouse,
                            "date" =>  $fecha_iso,
                            "description" => $nombre_estatus,
                            "city" => "CCO",
                            "received_by" => null,
                            "relationship" => null,
                            "delivery_proof" => [
                                $ruta . $document_name
                            ],

                            "gcs" => "",
                            "details" => ""
                        );
                        $url = "https://tracking.mlasalvador.com/api/v1/providers/events";


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
                            "tracking_number" =>  $warehouse,
                            "descripcion" => $nombre_estatus, // m
                            "fecha" =>  $fecha,
                            "fechaiso" =>  $fecha_iso

                        );
                        $this->Clasificado2Model->guardar_logma($data);
                        $this->ClasificadoModel->bandera_mario($guia, 1);
                    }
                    //  echo $row->estatus;
                }
                //  echo $response;
            }
        }
    }
}


class PDF extends FPDF
{
    public function Header()
    {
        $this->SetFont('Arial', '', 15);
        $this->SetFillColor(255, 255, 255);
        $this->SetFillColor(255, 255, 255);


        $this->Ln(10);

        // Mostramos la fecha en la que se genero dicho reporte
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(90, 4);
        $this->Cell(100, 8, $GLOBALS['fecha'], 0, 0, 'C', 1);
        $this->Ln(10);

        // Creamos las cabeceras de la tabla con los datos que usaremos

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 8,  utf8_decode('#'),                               1, 0, 'C', 1);
        $this->Cell(20, 8,  utf8_decode('AWB'),                             1, 0, 'C', 1);
        $this->Cell(30, 8,  utf8_decode('Tracking'),                        1, 0, 'C', 1);
        $this->Cell(35, 8,  utf8_decode('Tracking Replace'),                        1, 0, 'C', 1);
        //$this->Cell(15, 8,  utf8_decode('Fecha Creacion'),                  1, 0, 'C', 1);            
        $this->Cell(65, 8,  utf8_decode('Cliente'),                         1, 0, 'C', 1);
        $this->Cell(25, 8,  utf8_decode('Tipo Entrega'),                    1, 0, 'C', 1);
        $this->Cell(25, 8,  utf8_decode('Tipo Servicio'),                   1, 0, 'C', 1);
        $this->Cell(25, 8,  utf8_decode('Estatus'),                         1, 0, 'C', 1);
        $this->Cell(20, 8,  utf8_decode('Estado'),                          1, 0, 'C', 1);
        $this->Cell(25, 8,  utf8_decode('Cobro Final'),                     1, 0, 'C', 1);
        $this->Ln();
    }

    public function reportemanifiesto($datos)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //echo 'generando los datos para el   libro diarios';

        $contador1          =  1;
        $cobro_final       =  0;

        foreach ($datos as $fila) {
            $awb                   =   $fila->awb;
            $tracking_number       =   $fila->tracking_number;
            $tracking_replace      =   $fila->tracking_replace;
            $consignee             =   $fila->consignee;
            $tipo_entrega          =   $fila->tipo_entrega;
            $tipo_servicio         =   $fila->tipo_servicio;
            $estatus               =   $fila->estatus;
            $estado                =   $fila->estado;
            $cobro_final           =   $fila->cobro_final;


            $this->SetFont('Arial', '', 7);
            $this->SetFillColor(255, 255, 255);
            $this->SetFillColor(255, 255, 255);

            $this->SetFont('Arial', '', 8);
            $this->Cell(10,  4,  $contador1,                               1, 0, 'C', 0);
            $this->Cell(20,  4,  $awb,                                     1, 0, 'C', 1);
            $this->Cell(30,  4,  $tracking_number,                         1, 0, 'C', 1);
            $this->Cell(35,  4,  $tracking_replace,                        1, 0, 'C', 1);
            $this->Cell(65,  4,  utf8_decode($consignee),                  1, 0, 'L', 1);
            $this->Cell(25,  4,  $tipo_entrega,                            1, 0, 'C', 1);
            $this->Cell(25,  4,  $tipo_servicio,                           1, 0, 'C', 1);
            $this->Cell(25,  4,  utf8_decode($estatus),                    1, 0, 'C', 1);
            $this->Cell(20,  4,  $estado,                                  1, 0, 'C', 1);
            $this->Cell(25,  4,  '$' . number_format($cobro_final, 2),       1, 0, 'R', 1);
            $this->Ln();

            $contador1   += 1;
            $cobro_final  = $cobro_final + $fila->cobro_final;
        }
    }

    //   segmemento para  genarar  le  nuevo detalle del   libro diario

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, '' . $this->PageNo(), 0, 0, 'C');
    }
}



/* End of file Controllername.php */


/* End of file Controllername.php */
