<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PreclasificacionController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('PreclasificacionModel');
        $this->load->model('WarehouseModel');
        $this->load->model('Conf_model');
        $this->load->model('SendModel');
        $this->load->model('Traspaso_model');
        $this->datos = array();
    }

    public function index() {}

    public function pre_clasificacion()
    {
        //$tipo="GMA";

        //$this->data['clientes'] = $this->Conf_model->clientes();

        // $this->datos['preclasifica'] = $this->PreclasificacionModel->pre_clasificacion($tipo);
        //$this->load->view('preclasifica/cuerpo', $this->datos);
        //$this->load->view('preclasifica/lista');
        //$this->load->view("preclasifica/lista", $this->datos);
        //  $this->listado();


        $tipo = "GMA";
        $this->datos['preclasifica'] = $this->PreclasificacionModel->pre_clasificacion($tipo);


        $this->load->view("preclasifica/vista_preclasificacion");
        $this->load->view('preclasifica/lista', $this->datos);
    }


    public function guardar_manifiesto()
    {
        $id = $_POST['id'];
        $data = array(
            'manifiesto' => trim($_POST["manifiesto"]),
            'fecha' => $_POST['fecha'],
            'descripcion' => $_POST['descripcion'],
            'referencia' => trim($_POST['referencia']),
            'paquetes' => $_POST['paquetes'],
            'sacos' => $_POST['sacos'],
            'tipo' => "GMA",
            'id_pais' => $_SESSION['pais']
        );
        $this->PreclasificacionModel->guardar_manifiesto($id, $data);
    }


    public function guardar_poliza()
    {
        $data = array(
            'manifiesto' => $_POST["manifiesto"],
            'referencia' => $_POST['referencia'],
            'poliza' =>     $_POST['poliza'],

        );

        $this->PreclasificacionModel->guardar_poliza($data); //actualiza encabezados
        $this->PreclasificacionModel->guardar_polizas($data); //actualiza detalles
    }

    public function listado()
    {
        $tipo = "GMA";
        $this->datos['preclasifica'] = $this->PreclasificacionModel->pre_clasificacion($tipo);

        $this->load->view('preclasifica/cuerpo', $this->datos);

        //$datos['lista']    = $this->MensajeroModel->mensajeros();
        //$this->load->view('mensajero/cuerpo', $datos);
    }

    public function guardar_guia()
    {
        $id = $_POST['id'];
        $data = array(
            'manifiesto' => trim($_POST["manifiesto"]),
            'referencia' => trim($_POST['referencia']),
            'numero_warehouse' => trim($_POST['guia']),
            'cajas' => $_POST['cajas'],
            'casillero' => $_POST['idcasillero'],
            'id_pais' => $_SESSION['pais']

        );

        $mf = trim($_POST["manifiesto"]);
        $rf = trim($_POST['referencia']);

        $validar_wh = $this->PreclasificacionModel->validar_wh($mf, $rf, trim($_POST['guia']));
        // echo $validar_wh;
        if ($validar_wh) {
            echo 1; //$validar_wh;

        } else {
            $this->PreclasificacionModel->guardar_guia($data);
            echo $validar_wh;
        }

        //  $this->PreclasificacionModel->guardar_guia($data);

    }



    public function consulta_guias($id)
    {
        $this->datos['guias'] = $this->PreclasificacionModel->consulta_guias($id);
        $this->load->view("preclasifica/lista_guias", $this->datos);
    }

    public function eliminar_guia($id)
    {
        $this->PreclasificacionModel->eliminar_guia($id);
    }

    public function eliminar_referencia($id)
    {
        $this->PreclasificacionModel->eliminar_referencia($id);
    }

    public function procesar_p($id)
    {
        $this->PreclasificacionModel->procesar_p($id);
    }


    public function consulta_guia_master($id)
    {

        $this->datos['guiamaster'] =  $this->PreclasificacionModel->consulta_guia_master($id);


        $this->load->view("preclasifica/lista_clasifica", $this->datos);
    }

    public function update_guia($wh,  $master)
    {

        $data = array(
            'estado' => "V"
        );

        $this->PreclasificacionModel->update_guia($wh, $data);
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

        $url = "http://tracking.mailamericas.com/oauth/token";



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



    public function consulta_referencia($wh)
    {

        $datos = $this->PreclasificacionModel->consulta_referencia($wh);
        //  var_dump($datos);
        enviarJson(array(
            'numero_warehouse' => $datos[0]->tracking_number,
            'manifiesto' => $datos[0]->id_manifiesto,
            'referencia' => $datos[0]->referencia,
            'casillero' => "0"
        ));
    }

    
    public function consulta_guia($wh,$master)
    {

        $datos = $this->PreclasificacionModel->consulta_guia($wh,$master);
         // var_dump($datos);
        enviarJson(array(
            'numero_warehouse' => $datos[0]->tracking_number,
           
        ));
    }



    public function pdf_clasificado($id, $manifiesto)
    {
        //    $datos['guia_master'] =  $this->PreclasificacionModel->query_guia_master($id);
        $datos['guia_master'] =  $this->PreclasificacionModel->consulta_guia_master($id);


        include getcwd() . "/application/libraries/fpdf/fpdf.php";
        //   include getcwd() . "/";
        $this->pdf = new FPDF();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle("Guia Master");
        $this->pdf->SetLeftMargin(10);
        $this->pdf->SetRightMargin(10);
        $this->pdf->SetFillColor(200, 200, 200);
        $this->pdf->SetFont('Arial', 'B', 7);


        $x = 0;
        $correla = 0;

        $estado = "";

        $poliza = "";
        //  echo "estoy en 335";
        foreach ($datos['guia_master'] as $item) {


            if ($x == 0) {
                $this->pdf->SetFont('Arial', 'B', 12);
                $this->pdf->Cell(160, 7, 'MANIFIESTO: ' . ' ' . $manifiesto, 0, 0, 'C', '0');

                $this->pdf->SetFont('Arial', 'B', 7);
                $this->pdf->Cell(10, 7, 'Pagina ' . $this->pdf->PageNo() . '/{nb}', 0, 0, 'C', '0');
                $this->pdf->Ln(9);

                //   $this->pdf->Cell(160, 7, 'File Numero: '. $archivo, 0, 0, 'L', '0');
                $this->pdf->Ln(7);
                $this->pdf->Cell(15, 7, '', '0', 0, 'C', '0');
                $this->pdf->Cell(12, 7, 'Item', 'TBL', 0, 'C', '1');
                $this->pdf->Cell(30, 7, 'Referencia', 'TBL', 0, 'C', '1');
                $this->pdf->Cell(30, 7, 'Tracking', 'TB', 0, 'C', '1');
                $this->pdf->Cell(80, 7, 'Nombre', 'TB', 0, 'L', '1');
                //    $this->pdf->Cell(30, 7, utf8_decode('Póliza'), 'TB', 0, 'C', '1');
                $this->pdf->Cell(20, 7, 'Estado', 'TBR', 0, 'L', '1');


                $this->pdf->Ln(9);
            }
            $x += 1;
            $correla = $correla + 1;
            $this->pdf->Cell(15, 5, '', 0, 0, 'C', 0);
            $this->pdf->Cell(12, 5, $correla, 0, 0, 'C', 0);
            $this->pdf->Cell(30, 5, $item->referencia, 0, 0, 'C', 0);
            $this->pdf->Cell(30, 5, $item->tracking_number, 0, 0, 'C', 0);
            $this->pdf->Cell(80, 5, utf8_decode($item->consignee), 0, 0, 'L', 0);
            // $this->pdf->Cell(30, 5, $item->poliza, 0, 0, 'C', 0);
            if ($item->estado === "V") {
                $estado = "Recibido";
            } else {
                $estado = "Pendiente";
            }

            $this->pdf->Cell(20, 5, $estado, 0, 0, 'L', 0);

            $archivo = $item->id_manifiesto;

            $this->pdf->Ln(5);

            if ($x == 48) {
                $x = 0;
            }
        }
        //
        $this->pdf->Ln(7);


        $destino = getcwd() . "/" . "FILE.php";

        $this->pdf->Output($archivo . '.pdf', 'f');
    }

    public function buscar_warehouse_wd($guia)
    {
        $datos = $this->PreclasificacionModel->buscar_warehouse_wd($guia);

        enviarJson(array(

            'nombre_destinatario'   => $datos[0]->consignee,

            'casillero' => $datos[0]->consignee_account,

            'id_pais' => $_SESSION['pais']

        ));
    }

    public function fecha()
    {
        $ruta = "https://localhost/";
        $document_name = "firma.png";
        $data = array(
            "tracking" => "123456",
            "date" =>  "hoy",
            "description" => "producto",
            "city" => "CCO",
            "received_by" => "",
            "relationship" => null,
            "delivery_proof" => [
                //    "https://stargates.site/documentos_ma/638101f13d8b7.png"
                $ruta . $document_name

            ],

            "gcs" => "",
            "details" => ""
        );
        var_dump($data);

        exit;
        $fecha = date('Y-m-d H:i:s');
        $fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        $fecha_str = $fecha_tmp->format(DateTime::ATOM);
        echo $fecha_str;
    }

    /* crea traspaso automatico de manifiesto recibio en aeropuerto para enviarlo a san francisco */
    public function consulta_guia_master_auto($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $enca = $this->PreclasificacionModel->query_guia_master($id);
        $enca->manifiesto;
        $id_tra = null;
        $data = array(
            'manifiesto' => "TRA-" . $enca->manifiesto,
            'fecha' => $enca->fecha,
            'descripcion' => "TRASPASO",
            'id_pais' => $enca->id_pais,
            'client_id' => $enca->client_id,
            'sacos' => 0,
            'tipo' => "TRA"
        );
        $manifiesto = $this->PreclasificacionModel->guardar_manifiesto($id_tra, $data);


        $datos['guiamaster'] =  $this->PreclasificacionModel->consulta_guia_master($id);

        foreach ($datos['guiamaster'] as $item) {

            $data = array(
                'referencia' =>  $item->referencia,
                'tracking_number' =>  $item->tracking_number,
                'consignee' => $item->consignee,
                'id_manifiesto' => $manifiesto,

            );

            if ($item->estado == "V") {
                $this->PreclasificacionModel->guardar_guia_master_auto($data);
            } else {
                //  $this->PreclasificacionModel->update_guia_master_auto($mani, $item->referencia);
            }
        }
    }
}
    
    /* End of file PreclasificacionController.php */
