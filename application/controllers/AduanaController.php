<?php
defined('BASEPATH') or exit('No direct script access allowed');
include getcwd() . "/application/libraries/fpdf/fpdf.php";

class AduanaController extends CI_Controller
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
        $this->load->model('Aduana_Model');
        $this->load->model('WhModel');
        $this->load->view("verifica");
        $this->load->helper('path');
        session_start();
    }

    public function index() {}

    public function archivo_facturacion()
    {
        $this->datos['lista_poliza'] = $this->Aduana_Model->listado_polizas();
        $this->load->view("aduana/Avista_facturacion", $this->data);
        $this->load->view("aduana/Alista_facturacion", $this->datos);
    }

    public function Amanifiesto($id, $master, $correla)
    {

        //ini_set('display_errors', 1);
        //ini_set('display_startup_errors', 1);
        //error_reporting(E_ALL);

        $_SESSION['tnumber']                =   $correla;
        $this->datos['navtext']             =   "Manifiestos";
        $this->datos['catalogo_ref']        =   $this->Conf_model->get_referencia($id);
        $this->datos['form']                =   "aduana/Acontenido";
        $this->datos['partidas']            =   $this->Conf_model->partidas();

        $this->datos['lista']               =   $this->Aduana_Model->lista_warehouse($id, $master, $correla);
        $this->datos['lst_referencia']      =   $this->Aduana_Model->reference_list($id); //obtiene las referencias asociadas la manifiesto
        $this->datos['preclasificacion']    =   $this->Aduana_Model->preclasificacion_id($id);
        $this->datos['empresa']             =   $this->Conf_model->empresas();
        $this->datos['transportista']       =   $this->Aduana_Model->list_transp();


        $this->datos['agentes']       =   $this->Aduana_Model->agentes();
        $this->datos['t_transporte']       =   $this->Aduana_Model->t_transporte();
        $this->datos['puertos']       =   $this->Aduana_Model->puertos();


        $this->load->view("aduana/Alista", $this->datos);
    }
    public function lista_manifiesto_refer($id, $master, $ref)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        //var_dump($id,$master,$correla);
        //$_SESSION['tnumber']                =   $correla;
        $this->datos['navtext']             =   "Manifiestos";
        $this->datos['catalogo_ref']        =   $this->Conf_model->get_referencia($id);
        $this->datos['form']                =   "aduana/Acontenido";
        $this->datos['ref']                 =   $ref;
        $this->datos['partidas']            =   $this->Conf_model->partidas();
        $this->datos['lista']               =   $this->Aduana_Model->lista_manifiesto_refer($id, $master, $ref);
        $this->datos['lst_referencia']      =   $this->Aduana_Model->reference_list($id); //obtiene las referencias asociadas la manifiesto
        $this->datos['preclasificacion']    =   $this->Aduana_Model->preclasificacion_id($id);
        $this->datos['transportista']       =   $this->Aduana_Model->list_transp();

        $this->load->view("aduana/Alista", $this->datos);
    }

    public function manifiesto_confirma($id, $master)
    {
        $this->datos['navtext']   =     "Manifiestos";
        $this->datos['form']      =     "seguimiento/contenido";
        $this->datos['lista']     =     $this->Aduana_Model->lista_warehouse_confirma($id, $master);
        $this->load->view("aduana/Alista_confirma", $this->datos);
    }

    public function manifiesto_export($id, $master)
    {
        $this->datos['navtext']   = "Manifiestos";
        $this->datos['lista'] = $this->Aduana_Model->manifiesto_export($id, $master);
        $this->load->view("aduana/Alista_exportar", $this->datos);
    }

    public function lista_referencia($id, $ref)
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->datos['navtext']         =   "Manifiestos";
        $this->datos['catalogo_ref']    =   $this->Conf_model->get_referencia($id);
        $this->datos['form']            =   "aduana/Acontenido";
        $this->datos['lista']           =   $this->Aduana_Model->lista_referencia($id, $ref);

        $this->load->view("aduana/Alista", $this->datos);
    }

    public function seleccionar_item($id, $opc)
    {
        if ($opc == 1) {
            $flag = 1;
        } else {
            $flag = 0;
        }

        $this->Aduana_Model->seleccionar_item($id, $flag);
    }

    public function lista_awb()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->datos['navtext']                 =   "AWB";
        $this->datos['form']                    =   "awb/contenido";
        $this->datos['transportista_fast']      =   $this->Conf_model->transportista_fast();
        $this->datos['proveedor']               =   $this->Aduana_Model->provee();
        $this->datos['empresa']             =   $this->Conf_model->empresas();
        $this->load->view("aduana/Alistado", $this->datos);
    }



    public function lista_awb_first()
    {
        $this->load->view("verifica");
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->datos['lista'] = $this->Aduana_Model->Alista_awb();

        $this->load->view("aduana/Acuerpo_ma", $this->datos);
    }

    public function lista_awb_confirma()
    {
        $this->datos['navtext']   = "AWB";
        $this->datos['form']      = "segumiento/contenido";

        $this->load->view("seguimiento/lista", $this->datos);
    }

    public function lista_awb_first_confirma()
    {
        $this->datos['lista'] = $this->Aduana_Model->lista_awb_confirma();
        $this->load->view("seguimiento/cuerpo_ma", $this->datos);
    }

    public function consulta_awb($inicio, $fin, $opcion, $buscar)
    {
        $this->datos['lista'] = $this->Aduana_Model->consulta_awb($inicio, $fin, $opcion, $buscar);
        $this->load->view("awb/cuerpo_ma", $this->datos);
    }

    public function manifiesto_listado($mawb, $opc)
    {
        if ($opc == 1) {
            $campo = "tracking_number";
        } else {
            $campo = "mawb";
        }

        $this->datos['navtext']     =   "Manifiestos";
        $this->datos['form']        =   "aduana/Acontenido";
        $this->datos['lista']       =   $this->Aduana_Model->listado_warehouse($mawb, $campo);

        $this->load->view("aduana/Alista", $this->datos);
    }

    public function  guardar_referencia()
    {
        $id     =   $_POST['id'];
        $data   =   array(
            'id_manifiesto' => trim($_POST["id-manifiesto"]),
            'referencia'    => trim($_POST['referencia']),
            'paquetes'      => $_POST['paquetes'],
            'sacos'         => $_POST['sacos'],
        );

        $this->Aduana_Model->guardar_referencia($data);
    }

    public function asignar_guia()
    {
        $ref    = trim($_POST['catalogo_ref']);
        $master = $_POST['manifiesto_id'];
        echo $ref;

        $this->Aduana_Model->asignar_guia($master, $ref);
    }

    public function consulta_conversacion($id)
    {
        $this->datos['dconversacion'] = $this->WarehouseModel->consulta_conversacion($id);
        $this->load->view("seguimiento/lista_conversacion", $this->datos);
    }

    public function  guardar_comentario()
    {
        $id             =   $_POST['id-tracking'];
        $cometario      =   $_POST['comenta'];
        $fecha          =   date("Y-m-d H:i:s");
        $data           =   array(
            'fecha'         =>   $fecha,
            'comentarios'   =>   $cometario,
            'id_tracking'   =>   $id,
            'usuario'       =>   $_SESSION["nombre"]
        );

        $this->Aduana_Model->guardar_comentario($data);
    }

    public function guardar_transportista_fast($id, $transportista)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->Aduana_Model->guardar_transportista_fast($id, $transportista);
    }

    public function cargar_archivo_fast()
    {

        $this->load->view("aduana/cargar_archivo_fast");
    }

    public function upload_fast()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $empresa = $_POST['empresa'];


        // limitacion de proceso
        //exit("no se pudo abrir el archivo ($empresa)");

        $inf_empresa              =   $this->Aduana_Model->xmlprovee($empresa);

        //$inf_empresa[0]->id;
        $pais_destino       = $inf_empresa[0]->pais_destino;
        $pais_origen        = $inf_empresa[0]->pais_origen;
        $shipper_address1   = $inf_empresa[0]->direccion_expedidor1;
        $shipper_address2   = $inf_empresa[0]->direccion_expedidor1;

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
                die("Error al subir el archivo.");
            }

            $tarifa = 0; //TODO: Asignar valor real más adelante
            $object = PHPExcel_IOFactory::load($link);
            $mawb = str_replace([' ', '.xls'], '', $_FILES["file"]["name"]);


            if ($empresa == 106) {
                //seccion de pakya
                $data = array(
                    'manifiesto'      => $mawb,
                    'fecha'           => date("Y-m-d H:i:s"),
                    'descripcion'     => "Escriba una direccion aqui",
                    'referencia'      => "Referencia Aqui",
                    'paquetes'        => "1",
                    'sacos'           => "1",
                    'tipo'            => "ALL",
                    'puerto_salida'   => "1",
                    'puerto_salida2'  => "2",
                    'agent_exp'       => $empresa,
                    'client_id'       => $_POST["empresa"],
                    'id_pais'         => $_SESSION["pais"],
                    'userid'          => $_SESSION["user_id"],
                    'linea_master'    => 14,
                    'tipo_factura'    => "MA",
                );

                // insercion en el encabezado de preclasificacion 
                $id_manifiesto = $this->PreclasificacionModel->guardar_manifiesto(null, $data);

                //Iterar sobre todas las hojas del archivo Excel paykya
                $i = 1;
                foreach ($object->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();

                    for ($row = 2; $row <= $highestRow; $row++) {
                        // Obtener los valores de las celdas, puedes ajustar los índices si es necesario
                        $tracking_number    = $worksheet->getCellByColumnAndRow(2,  $row)->getValue();
                        $consignee_account  = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                        $consignee          = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                        $consignee_address1 = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                        $consignee_city     = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                        $consignee_state    = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                        $consignee_zip      = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                        $consignee_email    = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                        $consignee_phone    = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                        $consignee_mobile   = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                        $pieces             = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                        $gweight            = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                        $cweight            = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
                        $commodity          = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
                        $shipper_account    = $worksheet->getCellByColumnAndRow(3,  $row)->getValue();
                        $shipper_name       = $worksheet->getCellByColumnAndRow(4,  $row)->getValue();
                        $charges            = $worksheet->getCellByColumnAndRow(27,  $row)->getValue();

                        // Insertar los datos del manifiesto
                        $fecha_cc =  date('Y-m-d');
                        $data = array(
                            'tracking_number'    => $tracking_number,
                            'consignee_account'  => $consignee_account,
                            'consignee'          => $consignee,
                            'consignee_address1' => $consignee_address1,
                            'consignee_city'     => $consignee_city,
                            'consignee_state'    => $consignee_state,
                            'consignee_zip'      => $consignee_zip,
                            'consignee_email'    => $consignee_email,
                            'consignee_phone'    => $consignee_phone,
                            'consignee_mobile'   => $consignee_mobile,
                            'pieces'             => $pieces,
                            'gweight'            => $gweight,
                            'cweight'            => $cweight,
                            'commodity'          => $commodity,
                            'shipper_account'    => $shipper_account,
                            'shipper_name'       => $shipper_name,
                            'id_manifiesto'      => $id_manifiesto,
                            'id_pais'            => $_SESSION["pais"],
                            'tarifa'             => $tarifa,
                            'cobro_final'        => $charges,
                            'fecha'              => $fecha_cc,
                            'pais_destino'       => $pais_destino,
                            'pais_origen'        => $pais_origen,
                            'shipper_address1'   => $shipper_address1,
                            'shipper_address2'   => $shipper_address2,
                            'pakya'              => 'IMILE',
                            'Bol_type_code'      => 'HCO',
                            'natcod'            => 23,
                            'id_empresa'        => $empresa,
                            'agencia_destino'  => 0,

                        );
                        $i = $i + 1;
                        $this->WarehouseModel->insertar($data);
                    }
                }
                //guarda cantidad de guias del manifiesto
                $this->Aduana_Model->update_num_guias($id_manifiesto, $i);
            } //fin 106 pakya
            $idpais = 0;
            if ($empresa == 103) {
                $idpais = 1;
            }
            if ($empresa ==  104) {
                $idpais = 2;
            }
            if ($empresa ==  105) {
                $idpais = 3;
            }

            if ($empresa == 103  || $empresa == 104 || $empresa == 105) {
                //seccion IMILE sv 103
                $data = array(
                    'manifiesto'      => $mawb,
                    'fecha'           => date("Y-m-d H:i:s"),
                    'descripcion'     => "Escriba una direccion aqui",
                    'referencia'      => "Referencia Aqui",
                    'paquetes'        => "1",
                    'sacos'           => "1",
                    'tipo'            => "GMA",
                    'puerto_salida'   => "3",
                    'puerto_salida2'  => "1",
                    'agent_exp'       => $empresa,
                    'client_id'       => $_POST["empresa"],
                    'id_pais'         => $idpais,
                    'userid'          => $_SESSION["user_id"],
                    'linea_master'    => 14,
                    'tipo_factura'          => "IM",
                );

                //insercion en el encabezado de preclasificacion 
                $id_manifiesto = $this->PreclasificacionModel->guardar_manifiesto(null, $data);
                $i = 0;
                /*seccion destinada a IMILE */
                foreach ($object->getWorksheetIterator() as $worksheet) {
                    $highestRow    = $worksheet->getHighestRow();
                    //$highestColumn = $worksheet->getHighestColumn();
                    $c             = 1;

                    $fecha_cc =  date('Y-m-d');
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $c                      = $c + 1;

                        $tracking_number        = $worksheet->getCellByColumnAndRow(0, $row)->getValue();

                        $pais_origen            = $worksheet->getCellByColumnAndRow(3, $row)->getValue();

                        $consignee              = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

                        $consignee_country     = $worksheet->getCellByColumnAndRow(5, $row)->getValue();

                        $consignee_address1     = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

                        $consignee_city         = $worksheet->getCellByColumnAndRow(8, $row)->getValue();

                        $consignee_state        = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

                        $consignee_zip          = $worksheet->getCellByColumnAndRow(9, $row)->getValue();

                        $consignee_email        = $worksheet->getCellByColumnAndRow(11, $row)->getValue();

                        $consignee_phone        = $worksheet->getCellByColumnAndRow(10, $row)->getValue();

                        $items                 = $worksheet->getCellByColumnAndRow(14, $row)->getValue();

                        $pieces                 = $worksheet->getCellByColumnAndRow(14, $row)->getValue();

                        $gweight                = $worksheet->getCellByColumnAndRow(15, $row)->getValue();

                        $commodity              = $worksheet->getCellByColumnAndRow(12, $row)->getValue();

                        $value                   = $worksheet->getCellByColumnAndRow(16, $row)->getValue();

                        $currency                  = $worksheet->getCellByColumnAndRow(17, $row)->getValue();

                        $bag                  = $worksheet->getCellByColumnAndRow(19, $row)->getValue();

                        $consignee_account      = $worksheet->getCellByColumnAndRow(20, $row)->getValue();

                        //exit;
                        $data = array(
                            'tracking_number'        => $tracking_number,
                            'consignee_account'      => $consignee_account,
                            'consignee_country'      => $consignee_country,
                            'consignee'              => $consignee,
                            'consignee_city'         => $consignee_city,
                            'consignee_state'        => $consignee_state,
                            'consignee_zip'          => $consignee_zip,
                            'consignee_email'        => $consignee_email,
                            'consignee_phone'        => $consignee_phone,
                            //'consignee_mobile'       => $consignee_mobile,
                            'pieces'                 => 1,
                            'gweight'                => $gweight,
                            //'cweight'                => $cweight,
                            'commodity'              => $commodity,
                            'currency'               => $currency,
                            //'shipper_account'        => $shipper_account,
                            //'shipper_name'           => $shipper_name,
                            'id_manifiesto'          => $id_manifiesto,
                            //'fecha'                  => $fecha,
                            'id_pais'                => $idpais,
                            'tarifa'                 => 0,
                            'pakya'                  => 1,
                            'bag_number'             => $bag,
                            'fecha'                  => $fecha_cc,
                            'pais_destino'           => $pais_destino,
                            'pais_origen'            => $pais_origen,
                            'shipper_address1'       => $shipper_address1,
                            'shipper_address2'       => $shipper_address2,
                            'consignee_address1'     => $consignee_address1,
                            'tipo_entrega'           => "NRML",
                            'tipo_servicio'          => "SER",
                            'items'                  => $items,
                            'value'                  => $value,
                            'Bol_type_code'          => 'HCO',
                            'natcod'                => 23,
                            'consignee_address2'    => '',
                            'id_empresa'            => $empresa,
                            'agencia_destino'  => 0,
                        );

                        $i = $i + 1;
                        //var_dump($data,'IMILE SV');
                        //insercion a la tabla manifiesto
                        $this->WarehouseModel->insertar($data);
                        // $this->WarehouseModel->updateCorreo($tracking_number, $consignee_email, $consignee_phone);
                    }
                }
                //guarda cantidad de guias del manifiesto
                $this->Aduana_Model->update_num_guias($id_manifiesto, $i);
            }

            if ($empresa == 107) {
                //  CREACION DE LA CABECERA 
                $data = array(
                    'manifiesto'      => $mawb,
                    'fecha'           => date("Y-m-d H:i:s"),
                    'descripcion'     => "Apg Ecommerce",
                    'referencia'      => "Pendiente",
                    'paquetes'        => "1",
                    'sacos'           => "1",
                    'tipo'            => "GMA",
                    'puerto_salida'   => "1",
                    'puerto_salida2'  => "2",
                    'agent_exp'       => $empresa,
                    'client_id'       => $_POST["empresa"],
                    'id_pais'         => $_SESSION["pais"],
                    'userid'          => $_SESSION["user_id"],
                    'linea_master'    => 0,
                    'tipo_factura'    => "AP"
                );

                $id_manifiesto = $this->PreclasificacionModel->guardar_manifiesto(null, $data); //TODO:descomentar esto
                //  $id_manifiesto=null;
                // se tiene que cargar los datos para apg 
                foreach ($object->getWorksheetIterator() as $worksheet) {
                    $highestRow    = $worksheet->getHighestRow();
                    //$highestColumn = $worksheet->getHighestColumn();
                    $c             = 1;

                    $fecha_cc =  date('Y-m-d');
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $c                      = $c + 1;
                        //HAWB
                        // $tracking_number   
                        $tracking_number        = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        //Name of Consignee
                        $consignee              = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                        //Consignee Country
                        $consignee_country      = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                        //Consignee Address
                        $consignee_address1     = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                        //Municipality / Municipio 
                        $consignee_city         = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                        //State / Departamento  
                        $consignee_state        = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                        //ZiP Code
                        $consignee_zip          = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                        //Email
                        $consignee_email        =   $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                        //Contact Number
                        $consignee_phone        =  $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                        //No of Item
                        $items                  = $worksheet->getCellByColumnAndRow(20, $row)->getValue();

                        $pieces                 = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                        //Weight(kg)
                        $gweight                = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
                        //Goods Desc 1
                        $commodity              = $worksheet->getCellByColumnAndRow(27, $row)->getValue();

                        $value                  = $worksheet->getCellByColumnAndRow(29, $row)->getValue();

                        $currency               = null; //  $worksheet->getCellByColumnAndRow(17, $row)->getValue(); TODO:VERIFICAR

                        $bag                    = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        //$shipper_account        = $worksheet->getCellByColumnAndRow(3,$row)->getValue();
                        //$shipper_name           = $worksheet->getCellByColumnAndRow(4,$row)->getValue();
                        $consignee_account      = $worksheet->getCellByColumnAndRow(19, $row)->getValue();

                        $cadena_menor50 = $worksheet->getCellByColumnAndRow(28, $row)->getValue();;

                        // Usamos la función strpos para encontrar la posición de la primera coma
                        $posicionComa = strpos($cadena_menor50, ",");

                        // Si se encuentra una coma
                        if ($posicionComa !== false) {
                            // Usamos la función substr para extraer la subcadena desde el inicio hasta la posición de la coma
                            $primerDato = substr($cadena_menor50, 0, $posicionComa);
                        } else {
                            // Si no hay coma, el primer dato es toda la cadena
                            $primerDato = $cadena_menor50;
                        }

                        //echo 'el traking  number es  ' . $tracking_number. '<br>';
                        $data = array(
                            'tracking_number'    => $tracking_number,
                            'consignee_account'  => $consignee_account,
                            'consignee'          => $consignee,
                            'consignee_address1' => $consignee_address1,
                            'consignee_city'     => $consignee_city,
                            'consignee_state'    => $consignee_state,
                            'consignee_zip'      => $consignee_zip,
                            'consignee_email'    => $consignee_email,
                            'consignee_phone'    => $consignee_phone,
                            'consignee_mobile'   => null, // TODO:VERIFICAR
                            'pieces'             => $pieces,
                            'items'              => $items,
                            'gweight'            => $gweight,
                            //'cweight'            => $cweight,
                            'commodity'          => $commodity,
                            //'shipper_account'    => $shipper_account,
                            //'shipper_name'       => $shipper_name,
                            'id_manifiesto'      => $id_manifiesto,
                            'id_pais'            => $_SESSION["pais"],
                            'tarifa'             => $tarifa,
                            'value'              => $value,
                            'fecha'              => $fecha_cc,
                            'pais_destino'       => $pais_destino,
                            'pais_origen'        => $pais_origen,
                            //'shipper_address1'   => $shipper_address1,
                            // 'shipper_address2'   => $shipper_address2,
                            'pakya'              => 'APG',
                            //'tipoclient'          => $tipoclient
                            'Bol_type_code'      => 'HCO',
                            'natcod'            => 23,
                            'bag_number'          => $bag,
                            'id_empresa'        => $empresa,
                            'hscode'            => $primerDato,
                            'tipo_entrega'           => "NRML",
                            'tipo_servicio'          => "SER",
                            'agencia_destino'  => 0,

                        );

                        //  echo $tracking_number. " ". $consignee_address1."\n";
                        $this->WarehouseModel->insertar($data);
                        //      $this->WarehouseModel->updateCorreo($tracking_number, $consignee_email, $consignee_phone);
                        // $this->WarehouseModel->update_direccion($tracking_number, $consignee_address1);

                    }
                }
            }
        }
    }


    public function descargarArchivoDucaXls($id, $referencia = null, $tmp = null)
    {
        // 1. Cargar la librería (Asegúrate de que la ruta sea correcta según tu proyecto)
        // require_once APPPATH . 'third_party/PHPExcel.php'; 
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $objPHPExcel = new PHPExcel();

        // 2. Obtener datos
        $lista = $this->Aduana_Model->ducas_make_referencia($id, $referencia);

        // 3. Configuración de Propiedades del Documento
        $objPHPExcel->getProperties()->setCreator("Sistema Aduana")
            ->setTitle("Archivo DUCA");

        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        // 4. Cabeceras (Fila 1)
        $headers = [
            'Partida',
            'Descripción',
            'Comentario',
            'Cantidad Comercial',
            'Unidad Comercial',
            'Cantidad Estadistica',
            'FOB',
            'Flete',
            'Seguro',
            'Otros',
            'Peso B',
            'Peso N',
            'Cantidad de Bultos',
            'Origen',
            'Procedencia',
            'Adquisición',
            'Estado',
            'Titulo de Manifiesto Courier',
            'No. Serie',
            'Tipo item',
            'Factura'
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            // Opcional: Poner cabeceras en negrita
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $column++;
        }

        // 5. Llenado de Datos (A partir de la fila 2)
        $fila = 2;
        foreach ($lista as $row) {
            $sheet->setCellValue('A' . $fila, $row->HSC3 . $row->TAR_PR1);
            $sheet->setCellValue('B' . $fila, $row->ContenidoDescripcion);
            $sheet->setCellValue('C' . $fila, "Comentario");
            $sheet->setCellValue('D' . $fila, $row->Bultos);
            $sheet->setCellValue('E' . $fila, "UNIDAD");
            $sheet->setCellValue('F' . $fila, $row->Bultos);
            $sheet->setCellValue('G' . $fila, number_format($row->Valor, 2, '.', ''));
            $sheet->setCellValue('H' . $fila, number_format($row->Flete, 2, '.', ''));
            $sheet->setCellValue('I' . $fila, number_format($row->Seguro, 2, '.', ''));
            $sheet->setCellValue('J' . $fila, number_format($row->Otros, 2, '.', ''));
            $sheet->setCellValue('K' . $fila, $row->PesoKg);
            $sheet->setCellValue('L' . $fila, $row->PesoKg);
            $sheet->setCellValue('M' . $fila, $row->Bultos);
            $sheet->setCellValue('N' . $fila, $row->pais_origen);
            $sheet->setCellValue('O' . $fila, $row->pais_procedencia);
            $sheet->setCellValue('P' . $fila, $row->pais_adquisicion);
            $sheet->setCellValue('Q' . $fila, "NUEVAS");
            $sheet->setCellValue('R' . $fila, $row->linea_master . $row->DocumentoTransporte);
            $sheet->setCellValue('S' . $fila, $row->Nun_Reg);
            $sheet->setCellValue('T' . $fila, "Normal");
            $sheet->setCellValue('U' . $fila, $row->FacturaCodigo);

            $fila++;
        }

        // 6. Ajuste automático de columnas (opcional para mejor visualización)
        foreach (range('A', 'U') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // 7. Preparar descarga
        $filename = "archivo_duca_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        // Si necesitas guardarlo en el servidor usa: $objWriter->save($tmp);
        // Para descarga directa al navegador:
        $objWriter->save('php://output');
        exit;
    }

    public function descargarExcelHTML($id, $referencia = null)
    {
        $lista = $this->Aduana_Model->ducas_make_referencia($id, $referencia);
        $filename = "archivo_duca.xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        echo "<table border='1'>";
        echo "<tr><th>Partida</th><th>Descripcion</th><th>FOB</th></tr>";

        foreach ($lista as $row) {
            echo "<tr>";
            echo "<td>" . $row->HSC3 . $row->TAR_PR1 . "</td>";
            echo "<td>" . $row->ContenidoDescripcion . "</td>";
            echo "<td>" . $row->Valor . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }


    public function guardar_master_fast($master, $transportista)
    {
        $id                  =   null;
        $manifiesto          =   $master;
        $id_transportista    =   $transportista;

        $data  = array(
            'manifiesto'            =>   $manifiesto,
            'id_transportista'      =>   intval($id_transportista),
            'fecha'                 =>   date("Y-m-d H:i:s"),
            'descripcion'           =>   "PAKYA",
            'referencia'            =>   "Referencia Aqui",
            'paquetes'              =>   "1",
            'sacos'                 =>   "1",
            'tipo'                  =>   "GMA",
            'id_pais'               =>   2,
            'userid'                =>   $_SESSION["user_id"]
        );

        $this->PreclasificacionModel->guardar_manifiesto($id, $data);
    }
    public function guardar_wh_fast()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $id = 0;
        if ($_POST["id"]) {
            //$id_manifiesto = $_POST["id_manifiesto"];
            $id = $_POST["id"];
        }

        $data  = array(
            'id_manifiesto'       =>   $_POST["id_manifiesto"],
            'tracking_number'     =>   $_POST["tracking_number"],
            'fecha'               =>   $_POST["fecha"],
            'consignee_account'   =>   $_POST["consignee_account"],
            'iden_of'             =>   $_POST["iden_of"],
            'shipper_name'        =>   $_POST["shipper_name"],
            'consignee'           =>   $_POST["consignee"],
            'shipper_address1'    =>   $_POST["shipper_address1"],
            'consignee_address1'  =>   $_POST["consignee_address1"],
            'shipper_address2'    =>   $_POST["shipper_address2"],
            'consignee_address2'  =>   $_POST["consignee_address2"],
            'pais_origen'         =>   $_POST["pais_origen"],
            'pais_destino'        =>   $_POST["pais_destino"],
            'commodity'           =>   $_POST["commodity"],
            'bultos'              =>   $_POST["bultos"],
            'gweight'             =>   $_POST["gweight"],
            'contenido'           =>   $_POST["contenido"],
            'cobro_final'         =>   $_POST["cobro_final"],
            'flete'               =>   $_POST["flete"],
            'natcod'              =>   $_POST["natcod"],
            'bol_type_code'       =>   $_POST["bol_type_code"]
        );

        //var_dump($id,$data,'data');
        $datos['data'] = $this->Aduana_Model->guardar_manifiesto($id, $data);
        //  echo $datos['data'];
        //var_dump($datos['data'],$id,'mysql');
    }

    public function update_wh_fast()
    {

        if ($_POST["id_precla"]) {
            $id = $_POST["id_precla"];
        }

        $data  = array(
            'declarante'          =>   $_POST["declarante"],
            'tipo_transporte'     =>   $_POST["tipo_transporte"],
            'id_transportista'    =>   $_POST["id_transportista"],
            'manifiesto'          =>   $_POST["manifiesto"],
            'agent_exp'           =>   $_POST["empresa"],
            'puerto_salida'       =>   $_POST["puerto_salida"],
            'agent_cong'          =>   $_POST["agent_cong"],
            'puerto_salida2'      =>   $_POST["puerto_salida2"],
            'fechasalida'         =>   $_POST["fechasalida"],
            'aduana'              =>   $_POST["aduana"],
            'viaje'               =>   $_POST["viaje"],
            'linea_master'        =>   $_POST["linea_master"],
            'num_reg'               =>   $_POST["num_reg"]
        );

        //var_dump($id,$data);
        $datos['data'] = $this->Aduana_Model->update_wh_fast($id, $data);

        //  echo $datos['data'];
        //var_dump($datos['data'],$id);
    }

    public function info_wh_fast($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->datos['info_expedidor']       =   $this->Aduana_Model->expedidor($id);
        $this->datos['info_master']       =   $this->Aduana_Model->editar_wh_fast($id);

        $this->load->view("aduana/Alista", $this->datos);
    }

    public function editar_wh_fast($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $datos['transportista_fast']      =   $this->Conf_model->transportista_fast();
        $datos['paises']                  =   $this->Aduana_Model->paises();
        $datos['track']                   =   $this->Aduana_Model->editar_wh_fast($id);
        $this->load->view("aduana/Alista_wh", $datos);
    }

    public function eliminar_wh_fast($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->Aduana_Model->editar_wh_fast($id);
    }
    public function XFM($ref)
    {

        //j
        $ano = date("Y");
        $rsl = $this->Aduana_Model->xml_manifiesto($ref);

        // Crear un nuevo documento XML
        $xml = new DOMDocument('1.0', 'UTF-8');

        // Agregar el elemento raíz <DUCA_SI>
        $duca_si = $xml->createElement('DUCA_SI');
        $xml->appendChild($duca_si);

        // Agregar elementos hijos dentro de <DUCA_SI>
        $consignatario = $xml->createElement('CONSIGNATARIO');
        $consignatario->appendChild($xml->createElement('NOMBRE', 'Global Cargo de El Salvador S.A. de C.V.'));
        $consignatario->appendChild($xml->createElement('DIRECCION', 'AVE Bugambilias No. 19, COL SAN FCO'));
        $consignatario->appendChild($xml->createElement('ID_TRIBUTARIA', '06140402001036'));
        $duca_si->appendChild($consignatario);

        $declarante = $xml->createElement('DECLARANTE');
        $declarante->appendChild($xml->createElement('NOMBRE', 'Global Cargo de El Salvador S.A. de C.V.'));
        $declarante->appendChild($xml->createElement('DIRECCION', 'AVE Bugambilias No. 19, COL SAN FCO'));
        $declarante->appendChild($xml->createElement('ID_TRIBUTARIA', 'TC263'));
        $duca_si->appendChild($declarante);

        $duca_si->appendChild($xml->createElement('ANIO', '2024'));
        $duca_si->appendChild($xml->createElement('MANIFIESTO', '2024 2785'));
        $duca_si->appendChild($xml->createElement('REFERENCIA', '92631520'));
        $duca_si->appendChild($xml->createElement('PAIS_EXPORTACION', 'US'));
        $duca_si->appendChild($xml->createElement('MODELO_DECLARACION', 'IML'));
        $duca_si->appendChild($xml->createElement('TIPO_DECLARACION', '4'));

        // Agregar elementos hijos dentro de <MEDIO_TRANSPORTE>
        $medio_transporte = $xml->createElement('MEDIO_TRANSPORTE');
        $medio_transporte->appendChild($xml->createElement('PLACA', 'S/P'));
        $medio_transporte->appendChild($xml->createElement('TIPO', '4'));
        $duca_si->appendChild($medio_transporte);

        $duca_si->appendChild($xml->createElement('LOCALIZACION_MERCANCIA', '0303'));
        $duca_si->appendChild($xml->createElement('ADUANA_REGISTRO', '03'));
        $duca_si->appendChild($xml->createElement('ADUANA_ENTRADA', '03'));
        $duca_si->appendChild($xml->createElement('MODALIDAD_ADUANERA', '3'));
        $duca_si->appendChild($xml->createElement('LUGAR_DESCARGA', 'SVCPL'));
        $duca_si->appendChild($xml->createElement('TASAS', 'NO'));
        $duca_si->appendChild($xml->createElement('MONTO_TASAS', '2.0'));
        $duca_si->appendChild($xml->createElement('INFORMACION_ADICIONAL', ''));
        $duca_si->appendChild($xml->createElement('VALOR_FACTURA_TOTAL', '536.40'));
        $duca_si->appendChild($xml->createElement('FLETE_EXTERNO', '53.64'));
        $duca_si->appendChild($xml->createElement('SEGURO', '8.05'));

        // Agregar elementos <ITEMS> y <ITEM> con foreach
        $items = $xml->createElement('ITEMS');
        $i = 1;
        foreach ($rsl as $row) {
            //foreach (range(1, 2) as $i) {
            $item = $xml->createElement('ITEM');
            $item->appendChild($xml->createElement('NUMERO_ITEM', $i));
            $item->appendChild($xml->createElement('MARCAS_DE_EXPEDICION', 'S/M'));
            $item->appendChild($xml->createElement('MARCAS_DE_EXPEDICION2', 'S/M'));
            $item->appendChild($xml->createElement('NUMERO_BULTOS', '1.00'));
            $item->appendChild($xml->createElement('CLASE_BULTO', 'PK'));
            $item->appendChild($xml->createElement('CODIGO_ARANCELARIO', ($i == 1) ? '95030049' : '83059010'));
            $item->appendChild($xml->createElement('PRECISION_ARANCELARIA', '000'));
            $item->appendChild($xml->createElement('DESCRIPCION_COMERCIAL', ($i == 1) ? '[model toy]' : '[clips]'));
            $item->appendChild($xml->createElement('GUIA_AEREA', ($i == 1) ? 'RP126323329MU' : 'RP126350015MU'));
            $item->appendChild($xml->createElement('PESO_BRUTO', ($i == 1) ? '0.47' : '0.10'));
            $item->appendChild($xml->createElement('PESO_NETO', ($i == 1) ? '0.47' : '0.10'));
            $item->appendChild($xml->createElement('PAIS_ORIGEN', 'US'));
            $item->appendChild($xml->createElement('REGIMEN', '4400'));
            $item->appendChild($xml->createElement('REGIMEN_EXTENDIDO', '000'));
            $item->appendChild($xml->createElement('UNIDAD_MEDIDA', '15'));
            $item->appendChild($xml->createElement('CANTIDAD_UNIDADES', '1.00'));
            $item->appendChild($xml->createElement('VALOR_FACTURA', ($i == 1) ? '5.00' : '3.00'));
            $items->appendChild($item);
            $i = $i + 1;
        }
        $duca_si->appendChild($items);

        // Agregar elementos hijos dentro de <DOCUMENTOS_ADJUNTOS>
        $documentos_adjuntos = $xml->createElement('DOCUMENTOS_ADJUNTOS');

        $x = 1;
        foreach ($rsl as $row) {
            $documento = $xml->createElement('DOCUMENTO');
            $documento->appendChild($xml->createElement('LINEA', $x));
            $documento->appendChild($xml->createElement('CODIGO_TIPO_DOC', ($x % 2 != 0) ? '001' : '024'));
            $documento->appendChild($xml->createElement('NUMERO_DOC', $row->tracking_number));
            $documento->appendChild($xml->createElement('FECHA_EMISION', 'RP126323329MU'));
            $documento->appendChild($xml->createElement('MONTO', number_format($row->cobro_final, 2)));
            $documentos_adjuntos->appendChild($documento);
            $x = $x + 1;
        }
        $duca_si->appendChild($documentos_adjuntos);

        /*$documento2 = $xml->createElement('DOCUMENTO');
        $documento2->appendChild($xml->createElement('LINEA', '1'));
        $documento2->appendChild($xml->createElement('CODIGO_TIPO_DOC', '024'));
        $documento2->appendChild($xml->createElement('NUMERO_DOC', 'RP126323329MU'));
        $documentos_adjuntos->appendChild($documento2);*/



        $duca_si->appendChild($xml->createElement('PAIS_ULTIMA_PROCEDENCIA', 'US'));

        //Mostrar el XML en la consola
        //echo $xml->saveXML();
        $archivo = $xml->saveXML();

        //Guardar el archivo XML
        //$xml->formatOutput = true;
        //$archivo = $xml->save('duca_si.xml');
        //echo $xml->saveXML();  

        $rsl = $this->aduna_xml($archivo);
        echo $rsl . '<br>';
    }
    public function aduna_xml($json)
    {
        ini_set('memory_limit', '2048M');
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ini_set('max_execution_time', '300');
        ini_set('post_max_size', '8192');
        ini_set('upload_max_filesize   ', '8192');

        //$json = file_get_contents("php://input"); 
        $api_key = "WSTC.06140402001036:IceBreakers2024"; //prueba
        //$api_key = "WSAA.08071107520015:RFi1esgCqjHFkQG"; //produccion
        $key = base64_encode($api_key);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://swtest.aduana.gob.sv/WSSimDec/WSSimDec?wsdl"); //servidor  pruebas
        //curl_setopt($ch, CURLOPT_URL, "https://siduneaworld.aduana.gob.sv/WSWebInterface/REST/encodeAttachedDocuments"); // servidor producccion
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 780);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $api_key);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Accept: application/json',
                'Content-Type: application/json',
            )
            // 'Authorization: '.  $key
        );

        if (curl_exec($ch) === false) {
            echo 'Curl error: ' . curl_error($ch);
        }

        $errors = curl_error($ch);  //retorna errores                                                                                                          
        $result = curl_exec($ch);
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); //retorna el codigo de respuesta
        $rsl = json_decode($result);
        curl_close($ch);
        //var_dump($rsl);
        echo $rsl;
        /*$doc_scaneado = $rsl->ENCODED_ATTACHED_DOCUMENTS;
        echo $doc_scaneado;*/
    }



    public function degroup_xml($id, $master)
    {
        //var_dump($id, $master);





        $datos['header']             =   $this->Aduana_Model->preclasificacion_xml($master);
        $datos['body']               =   $this->Aduana_Model->lista_warehouse($id, $master, 0);

        //var_dump($datos['header']);

        $fechaxml       = date("Y-m-d", strtotime($datos['header'][0]->fecha));
        $ref_number     = $datos['header'][0]->manifiesto;
        $Line_number    = $datos['header'][0]->linea_master;




        // Variables generales
        $customs_office_code = "03";
        $voyage_number = $datos['header'][0]->viaje;
        $date_of_departure = $fechaxml;
        $reference_number = $ref_number;



        $place_of_loading_code = $datos['header'][0]->puerto1;
        $place_of_unloading_code =  $datos['header'][0]->puerto2;
        $exporter_address = '7950 NW 77TH ST STE 4';

        // Crear el documento DOM
        // $dom = new DOMDocument('1.0', 'UTF-8');
        //$dom->formatOutput = true;
        //$dom->xmlStandalone = true;  // Agrega standalone="yes"

        $dom = new DOMDocument();
        // Crear el nodo raíz <Awbolds>
        $awbolds = $dom->createElement('Awbolds');
        $dom->appendChild($awbolds);

        // Master_bol segment
        $master_bol = $dom->createElement('Master_bol');
        $awbolds->appendChild($master_bol);

        $master_bol->appendChild($dom->createElement('Customs_office_code', $customs_office_code));
        $master_bol->appendChild($dom->createElement('Voyage_number', $voyage_number));
        $master_bol->appendChild($dom->createElement('Date_of_departure', $date_of_departure));
        $master_bol->appendChild($dom->createElement('Reference_number', $reference_number));

        // Variables
        $Sub_Line_Number = 1;
        $shipper_address1 = "9910 NW 77TH ST STE 4";
        $consignee_address1 = "AVE Bugambilias No. 19, COL SAN FCO";
        $Freight_currency = "USD";
        // Iterar sobre los datos
        foreach ($datos['body'] as $row) {
            if ($row->guia_consolidada == null) {
                // Crear Bol_segment
                $bol_segment = $dom->createElement('Bol_segment');
                $awbolds->appendChild($bol_segment);

                // Bol_id segment
                $bol_id = $dom->createElement('Bol_id');
                $bol_segment->appendChild($bol_id);

                $bol_id->appendChild($dom->createElement('Bol_reference', $row->tracking_number));
                $bol_id->appendChild($dom->createElement('Line_number', $Line_number));
                $bol_id->appendChild($dom->createElement('Sub_Line_Number', $Sub_Line_Number));
                $bol_id->appendChild($dom->createElement('Bol_nature', $row->natcod));
                $bol_id->appendChild($dom->createElement('Bol_type_code', $row->bol_type_code));

                // Load_unload_place segment
                $load_unload_place = $dom->createElement('Load_unload_place');
                $bol_segment->appendChild($load_unload_place);

                $load_unload_place->appendChild($dom->createElement('Place_of_loading_code', $place_of_loading_code));
                $load_unload_place->appendChild($dom->createElement('Place_of_unloading_code', $place_of_unloading_code));

                // Traders_segment
                $traders_segment = $dom->createElement('Traders_segment');
                $bol_segment->appendChild($traders_segment);

                // Exporter
                $exporter = $dom->createElement('Exporter');
                $traders_segment->appendChild($exporter);

                $exporter->appendChild($dom->createElement('Exporter_name', $row->shipper_name));
                $exporter->appendChild($dom->createElement('Exporter_address', $shipper_address1));

                // Consignee
                $consignee = $dom->createElement('Consignee');
                $traders_segment->appendChild($consignee);

                // Crear Consignee_code vacío con apertura y cierre
                $consignee_code = $dom->createElement('Consignee_code');
                $empty_text = $dom->createTextNode('');
                $consignee_code->appendChild($empty_text);
                $consignee->appendChild($consignee_code);

                $consignee->appendChild($dom->createElement('Consignee_name', $row->consignee));
                // $consignee->appendChild($dom->createElement('Consignee_address', $row->consignee_address1));
                $consignee->appendChild($dom->createElement('Consignee_address', $consignee_address1));

                // Goods_segment
                $goods_segment = $dom->createElement('Goods_segment');
                $bol_segment->appendChild($goods_segment);

                $goods_segment->appendChild($dom->createElement('Number_of_packages', $row->pieces));
                $goods_segment->appendChild($dom->createElement('Package_type_code', 'PK'));
                $goods_segment->appendChild($dom->createElement('Gross_mass', $row->gweight));
                $goods_segment->appendChild($dom->createElement('Shipping_marks', 'S/M'));
                $goods_segment->appendChild($dom->createElement('Goods_description', $row->commodity));
                $goods_segment->appendChild($dom->createElement('Num_of_ctn_for_this_bol', 0));

                // Value_segment
                $value_segment = $dom->createElement('Value_segment');
                $bol_segment->appendChild($value_segment);

                $freight_segment = $dom->createElement('Freight_segment');
                $value_segment->appendChild($freight_segment);

                // $freight_segment->appendChild($dom->createElement('Freight_value', $row->gweight));
                $freight_segment->appendChild($dom->createElement('Freight_value', number_format($row->cobro_final, 2)));
                $freight_segment->appendChild($dom->createElement('Freight_currency', $Freight_currency));

                // Location
                $location = $dom->createElement('Location');
                $bol_segment->appendChild($location);
                $location->appendChild($dom->createElement('Location_code', '0303'));

                $Sub_Line_Number += 1;
            }
        }


        // Guardar como archivo
        $xml_string = $dom->saveXML();
        file_put_contents('generated_awbolds.xml', $xml_string);

        // Enviar como respuesta
        header('Content-Type: application/xml');
        //echo $xml_string;

        $xml = $xml_string;

        // Eliminar la declaración de versión
        $new_xml_string = str_replace('<?xml version="1.0"?>', '', $xml);
        $this->header_degroupXML($new_xml_string);
    }
    public function header_degroupXML($xml_string)
    {

        // Crear nuevo documento XML
        $doc = new DOMDocument('1.0', 'UTF-8');

        $doc->formatOutput = true;

        // Crear el elemento Envelope con sus atributos
        $envelope = $doc->createElement('soapenv:Envelope');
        $envelope->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
        $envelope->setAttribute('xmlns:ws', 'ws.asyman.aw2.aduana.mh.gob.sv');

        // Crear el Header y añadirlo al Envelope
        $header = $doc->createElement('soapenv:Header');
        $envelope->appendChild($header);

        // Crear el Body y añadirlo al Envelope
        $body = $doc->createElement('soapenv:Body');
        $envelope->appendChild($body);

        // Crear el elemento ws:degroup
        $degroup = $doc->createElement('ws:degroup');
        $body->appendChild($degroup);

        // Crear el elemento ws:ManifestInfo
        $manifestInfo = $doc->createElement('ws:ManifestInfo');
        $degroup->appendChild($manifestInfo);

        // Crear el documento DOM para el contenido CDATA
        $innerDom = new DOMDocument('1.0', 'UTF-8');
        $innerDom->formatOutput = true;
        $innerDom->xmlStandalone = true;







        //   $xml_string = $innerDom->saveXML($awbolds);
        $cdata = $doc->createCDATASection($xml_string);
        $manifestXml = $doc->createElement('manifestXml');
        $manifestXml->appendChild($cdata);
        $manifestInfo->appendChild($manifestXml);

        $doc->appendChild($envelope);

        $xml = $doc->saveXML();
        //   echo $xml;

        $this->desconsolidar_xml($xml);
    }

    public function desconsolidar_xml($xml)
    {
        $this->aduna_xml($xml);
        exit;
        // Configuración de PHP
        ini_set('memory_limit', '2048M');
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        ini_set('max_execution_time', '300');
        ini_set('post_max_size', '8192M');
        ini_set('upload_max_filesize', '8192M');

        // Validar la entrada
        if (empty($xml)) {
            return "Error: No se proporcionó un XML válido.";
        }

        // Configuración de API Key
        $api_key = "WSTC.06140402001036:IceBreakers2024"; //prueba
        $key = base64_encode($api_key);

        // Inicializar cURL
        $ch = curl_init();
        $url = "https://swtest.aduana.gob.sv/WSManifest/WSManifest"; // URL del servidor de pruebas

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 780);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); // Enviar XML
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $key); // Autenticación
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/xml',
            'Content-Type: application/xml',
        ]);

        // Ejecutar cURL
        $result = curl_exec($ch);
        $errors = curl_error($ch);
        $returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Código de respuesta HTTP

        curl_close($ch);

        // Manejar errores de cURL
        if ($errors) {
            echo "Error en cURL: " . $errors . PHP_EOL;
            return;
        }

        // Depuración del resultado y código de respuesta
        echo "Código HTTP: {$returnCode}" . PHP_EOL;
        echo "Respuesta cruda: " . print_r($result, true) . PHP_EOL;

        // Validar si la respuesta es XML
        libxml_use_internal_errors(true);
        $response = simplexml_load_string($result);

        if ($response === false) {
            echo "Error al procesar la respuesta XML:" . PHP_EOL;
            foreach (libxml_get_errors() as $error) {
                echo $error->message . PHP_EOL;
            }
            libxml_clear_errors();
            return;
        }

        // Devolver respuesta XML
        echo "Respuesta procesada: " . $response->asXML() . PHP_EOL;
    }


    public function update_agrupar($id_master, $consolidada)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        if (isset($_POST['ids'])) {
            $ids = $_POST['ids'];  // Recibir el arreglo de IDs

            // Verifica si es un array
            $pieces = 0;
            $gweight = 0;
            $value = 0;
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    // Procesa cada ID. Por ejemplo, puedes guardarlos en una base de datos.
                    // Supongamos que tienes una función que guarda los IDs:
                    $result = $this->Aduana_Model->obtener_valores($id);
                    $pieces += $result->pieces;
                    $gweight += $result->gweight;
                    $value += $result->cobro_final;

                    $this->guardar_consolidada($id_master, $consolidada, $id);
                }

                $this->guardar_valores($id_master, $consolidada, $pieces, $gweight, $value);
                // Responder al cliente con éxito
                echo $value; //json_encode(['status' => 'success', 'message' => 'Datos guardados correctamente']);
            } else {
                // Si no es un array, devolver un error
                echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos válidos']);
            }
        } else {
            // Si no se enviaron datos, devolver un error
            echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos']);
        }
    }
    function guardar_consolidada($master, $consolidada, $id)
    {

        $this->Aduana_Model->guardar_consolidada($master, $consolidada, $id);
    }

    function guardar_valores($id_master, $consolidada, $pieces, $gweight, $value)
    {

        $this->Aduana_Model->guardar_valores($id_master, $consolidada, $pieces, $gweight, $value);
    }

    function sumar_consolida($master)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $sum = 0;
        $count = 0;
        $count_agrupar = $this->Aduana_Model->contar_agrupar($master); //obtiene las guias creadas para grupar
        if ($count_agrupar) {
            $count = $count_agrupar;
        }

        $result = $this->Aduana_Model->sumar_consolida($master); //obtiene las guias que han sido consolidadas
        if ($result) {
            $sum = $result;
        }


        $datos = array(
            "sum" => $sum,
            "count" => $count
        );

        //var_dump($result);
        echo json_encode($datos);
    }

    public function quiantities_add()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //$empresa = $_POST['empresa'];

        // limitacion de proceso
        //exit("no se pudo abrir el archivo ($empresa)");      

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
                die("Error al subir el archivo.");
            }

            $tarifa = 0; //TODO: Asignar valor real más adelante
            $object = PHPExcel_IOFactory::load($link);
            $mawb = str_replace([' ', '.xls'], '', $_FILES["file"]["name"]);
            //  caputamos valores de la columna  
            $id_manifiesto = "";
            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    // Obtener los valores de las celdas, puedes ajustar los índices si es necesario
                    $tracking_number    = $worksheet->getCellByColumnAndRow(1,  $row)->getValue();
                    $items_descripction    = $worksheet->getCellByColumnAndRow(7,  $row)->getValue();
                    $items_quantities    = $worksheet->getCellByColumnAndRow(8,  $row)->getValue();
                    //echo "Los valores capturados son " .  $tracking_number  .  " ".    $Items_Quantities. '<br>';
                    $this->Aduana_Model->update_FieldManif($tracking_number, $items_quantities, $items_descripction);
                    // $tracking_number, $items_quantities

                }
            }
        }
    }

    public function asignar_verdes($id_master)
    {
        // Recuperar la cadena desde $_POST
        $textVerdes = $this->input->post('text-verdes');

        // Verifica que no esté vacía
        if (!empty($textVerdes)) {


            // Divide la cadena en un array eliminando espacios extras
            $valores = preg_split('/\s+/', trim($textVerdes));



            //new code
            $this->Aduana_Model->asignar_rojos($id_master);

            $preclasificacion = $this->WhModel->datos_preclasificacion($id_master);

            $empresas = [112, 113, 114];

            $tm = 0;

            if (in_array($preclasificacion->client_id, $empresas)) {
                $tm = 1;    
            }


                // Iterar sobre el array y guardar los valores
                foreach ($valores as $valor) {
                // Por ejemplo, guardar en la base de datos

                $tracking = $valor;

                if($tm == 1){

                    $this->Aduana_Model->asignar_verdes_temu($id_master, $tracking);
                }else{
                    $this->Aduana_Model->asignar_verdes($id_master, $tracking);
                }
                
            
            
            }

            echo "Valores guardados correctamente.";
        } else {
            echo "No se recibió ningún dato.";
        }
    }

    public function items_apg()
    {

        $request = $this->Aduana_Model->items_apg(1905);
        foreach ($request as $row) {

            $productos = $row->commodity;
            // Convertir la cadena en un array usando la coma como separador
            $array_productos = explode(",", $productos);

            // Contar la cantidad de elementos en el array
            $cantidad_productos = count($array_productos);
            $update = $this->Aduana_Model->update_items_apg($row->id_manifiesto, $row->tracking_number, $cantidad_productos);

            //   echo $row->tracking_number ." ". $cantidad_productos ."\n";
        }
    }
}
