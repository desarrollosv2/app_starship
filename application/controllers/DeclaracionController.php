<?php
    defined('BASEPATH') or exit('No direct script access allowed');
    include getcwd()."/application/libraries/fpdf/fpdf.php";
class DeclaracionController extends CI_Controller
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
        $this->load->helper('path');
        session_start();
    }

    public function index()
    {
        
    }

    public function archivo_facturacion()
    {
        $this->datos['lista_poliza'] = $this->Aduana_Model->listado_polizas();
        $this->load->view("declaracion/Avista_facturacion", $this->data);
        $this->load->view("declaracion/Alista_facturacion", $this->datos);
    }

    public function Amanifiesto($id, $master, $correla)
    {
        //var_dump($id,$master,$correla);
        $_SESSION['tnumber']                =   $correla;
        $this->datos['navtext']             =   "Manifiestos";
        $this->datos['catalogo_ref']        =   $this->Conf_model->get_referencia($id);
        $this->datos['form']                =   "declaracion/Acontenido";
        $this->datos['partidas']            =   $this->Conf_model->partidas();
        $this->datos['lista']               =   $this->Aduana_Model->lista_warehouse($id, $master,$correla);
        $this->datos['lst_referencia']      =   $this->Aduana_Model->reference_list($id); //obtiene las referencias asociadas la manifiesto
        $this->datos['preclasificacion']    =   $this->Aduana_Model->preclasificacion_id($id);
        $this->datos['transportista']       =   $this->Aduana_Model->list_transp();
        $this->datos['referencias']            = $this->Aduana_Model->get_ReferenciaManifiesto($id);
        
        $this->load->view("declaracion/Alista", $this->datos);
    }

    
    public function lista_manifiesto_refer($id, $master, $ref)
    {
        //var_dump($id,$master,$correla);
        //$_SESSION['tnumber']                =   $correla;
        $this->datos['navtext']             =   "Manifiestos";
        $this->datos['catalogo_ref']        =   $this->Conf_model->get_referencia($id);
        $this->datos['form']                =   "declaracion/Acontenido";
        $this->datos['ref']                 =   $ref;
        $this->datos['partidas']            =   $this->Conf_model->partidas();
        $this->datos['lista']               =   $this->Aduana_Model->lista_manifiesto_refer($id, $master,$ref);
        $this->datos['lst_referencia']      =   $this->Aduana_Model->reference_list($id); //obtiene las referencias asociadas la manifiesto
        $this->datos['preclasificacion']    =   $this->Aduana_Model->preclasificacion_id($id);
        $this->datos['transportista']       =   $this->Aduana_Model->list_transp();

        $this->load->view("declaracion/Alista", $this->datos);
    }


        
    public function manifiesto_confirma($id, $master)
    {
        $this->datos['navtext']   =     "Manifiestos";
        $this->datos['form']      =     "seguimiento/contenido";
        $this->datos['lista']     =     $this->Aduana_Model->lista_warehouse_confirma($id, $master);
        $this->load->view("declaracion/Alista_confirma", $this->datos);
    }
    
    public function manifiesto_export($id, $master)
    {
        $this->datos['navtext']   = "Manifiestos";
        $this->datos['lista'] = $this->Aduana_Model->manifiesto_export($id, $master);
        $this->load->view("declaracion/Alista_exportar", $this->datos);
    }

    public function lista_referencia($id, $ref)
    {
        $this->datos['navtext']         =   "Manifiestos";
        $this->datos['catalogo_ref']    =   $this->Conf_model->get_referencia($id);
        $this->datos['form']            =   "declaracion/Acontenido";
        $this->datos['lista']           =   $this->Aduana_Model ->lista_referencia($id, $ref);

        $this->load->view("declaracion/Alista", $this->datos);
    }

    public function seleccionar_item($id, $opc)
    {
        if ($opc == 1) 
        {
            $flag = 1;
        } 
        else 
        {
            $flag = 0;
        }

        $this->Aduana_Model->seleccionar_item($id, $flag);
    }

   public function lista_awb()
    {
        $this->datos['navtext']                 =   "AWB";
        $this->datos['form']                    =   "awb/contenido";
        $this->datos['transportista_fast']      =   $this->Conf_model->transportista_fast();
        $this->datos['proveedor']               =   $this->Aduana_Model->provee();
        $this->load->view("declaracion/Alistado", $this->datos);
    }


    public function lista_awb_first()
    {
        $this->datos['lista'] = $this->Aduana_Model->Alista_awb();
        $this->load->view("declaracion/Acuerpo_ma", $this->datos);
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
        if ($opc == 1) 
        {
            $campo = "tracking_number";
        } 
        else 
        {
            $campo = "mawb";
        }

        $this->datos['navtext']     =   "Manifiestos";
        $this->datos['form']        =   "declaracion/Acontenido";
        $this->datos['lista']       =   $this->Aduana_Model->listado_warehouse($mawb, $campo);
        
        $this->load->view("declaracion/Alista", $this->datos);
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
        
        //var_dump($this->datos['proveedor']);
        $this->load->view("declaracion/cargar_archivo_fast", $this->datos);
    }

    public function upload_fast()
    {
        $tarifa=0;
        $order_number=0;
        //var_dump($_POST["provee_id"]);
        if(isset($_FILES["file"]["name"])) 
        {
            $destino = getcwd() . "/public/uploads/file/";

            if (!is_dir($destino)) 
            {
                mkdir($destino, 0777, true);
            }

            $extension = explode(".", $_FILES["file"]["name"]);
            $nombre = time() . "-plantilla." . $extension[1];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destino . "/" . $nombre)) 
            {
                $link = $destino . "/" . $nombre;
            } 
            else 
            {
            
            }

            $object =   PHPExcel_IOFactory::load($link);
            $mawb   =   str_replace([' ','.xls'], '', $_FILES["file"]["name"]);
            $id     =   null;
            $data   =   array(
                                'manifiesto'      =>      $mawb,
                                'fecha'           =>      date("Y-m-d H:i:s"),
                                'descripcion'     =>      "Escriba una direccion aqui",
                                'referencia'      =>      "Referencia Aqui",
                                'paquetes'        =>      "1",
                                'sacos'           =>      "1",
                                'tipo'            =>      "GMA",
                                'client_id'       =>      $_POST["provee_id"],
                                'id_pais'         =>      2,
                                'userid'          =>      $_SESSION["user_id"]
                        );

            $this->PreclasificacionModel->guardar_manifiesto($id, $data);

            foreach ($object->getWorksheetIterator() as $worksheet) 
            {
                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $c             = 1;

                for($row = 2; $row <= $highestRow; $row++) 
                {
                    $c                      = $c + 1;
                    $tracking_number        = $worksheet->getCellByColumnAndRow(2,  $row)->getValue();
                    $consignee_account      = $worksheet->getCellByColumnAndRow(15,  $row)->getValue();
                    $consignee              = $worksheet->getCellByColumnAndRow(10,  $row)->getValue();
                    $consignee_address1     = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    $consignee_city         = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                    $consignee_state        = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    $consignee_zip          = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                    $consignee_email        = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                    $consignee_phone        = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    $consignee_mobile       = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                    $pieces                 = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                    $gweight                = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                    $cweight                = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
                    $commodity              = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
                    $shipper_account        = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $shipper_name           = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    
                    $manif['man'] = $this->Aduana_Model->preclasificacion($mawb);
                   
                    $id_manifiesto = 0;
                    foreach ($manif['man'] as $f) 
                    {
                        $id_manifiesto = $f->idpreclasificacion;
                    }
                   
                    //exit;
                    $data = array(
                        
                                    'tracking_number'        => $tracking_number,
                                    'consignee_account'      => $consignee_account,
                                    'consignee'              => $consignee,
                                    'consignee_address1'     => $consignee_address1,
                                    'consignee_city'         => $consignee_city,
                                    'consignee_state'        => $consignee_state,
                                    'consignee_zip'          => $consignee_zip,
                                    'consignee_email'        => $consignee_email,
                                    'consignee_phone'        => $consignee_phone,
                                    'consignee_mobile'       => $consignee_mobile,
                                    'pieces'                 => $pieces,
                                    'gweight'                => $gweight,
                                    'cweight'                => $cweight,
                                    'commodity'              => $commodity,
                                    'shipper_account'        => $shipper_account,
                                    'shipper_name'           => $shipper_name,
                                    'id_manifiesto'          => $id_manifiesto,
                                    'id_pais'                => '2',
                                    'tarifa'                 => $tarifa,
                                    'pakya'                  => 1,   
                    );

                    $this->WarehouseModel->insertar($data);

                    $id     =   null;
                    $data   =   array(
                                        'manifiesto'            => $mawb,
                                        'referencia'            => "1001",
                                        'numero_warehouse'      => $order_number,
                                        'cajas'                 => $pieces,
                                        'client_id'             => $_POST["provee_id"],
                                        'casillero'             => $consignee_account,
                                        'id_pais'               => 2,
                                );

                    $this->PreclasificacionModel->guardar_guia($data);

                    $n_guias =  $c - 1;

                    $this->PreclasificacionModel->cantidad_guia($n_guias, $id_manifiesto);
                }
            }
        }
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

    public function info_wh_fast($id)   
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

        $this->datos['info_master']       =   $this->Aduana_Model->editar_wh_fast($id);
        $this->load->view("aduana/Alista", $this->datos);
    } 

     public function editar_wh_fast($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);   

        if($id==null){
        }

        $datos['transportista_fast']  =   $this->Conf_model->transportista_fast();
        $datos['paises']              =   $this->Aduana_Model->paises();
        $datos['track']               =   $this->Aduana_Model->editar_wh_fast($id);
        $this->load->view("declaracion/Alista_wh", $datos);  
    }

    public function eliminar_wh_fast($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);   

        $this->Aduana_Model->editar_wh_fast($id);
    }

    public function guardar_wh_fast(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);  
        $id = 0;
        if($_POST["id"]){
            $id_manifiesto = $_POST["id_manifiesto"];
            $id = $_POST["id"];
        }
        
        echo $_POST["id_manifiesto"].' id manifiesto<br>';

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
                        'flete'               =>   $_POST["flete"]
        );
        
       //var_dump($id,$data,'data');
       $rsl = $this->Aduana_Model->guardar_manifiesto($id, $data);

       echo $rsl;
       //var_dump($datos['data'],$id,'mysql');
    }

    public function update_wh_fast(){

       
        if($_POST["id_precla"]){
            $id = $_POST["id_precla"];
        }
 
        $data  = array(
            'declarante'          =>   $_POST["declarante"],
            'tipo_transporte'     =>   $_POST["tipo_transporte"],
            'id_transportista'    =>   $_POST["id_transportista"],
            'manifiesto'          =>   $_POST["manifiesto"],
            'agent_exp'           =>   $_POST["agent_exp"],
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

       echo $datos['data'];
      //var_dump($datos['data'],$id);
    }


    public function crear_xml(){
        
        $ref = 6845;
        $ano = date("Y"); 

        


        $rsl = $this->Aduana_Model->xml_manifiesto($ref);
        //var_dump($rsl);
       
        foreach ($rsl as $row) {

            $CONSIG_NOMBRE = $row->consignee;
            $CONSIG_DIRECCION = $row->consignee_address1;
            $CONSIG_ID_TRIBUTARIA = $row->consignee_account;

            $DECL_NOMBRE = 'Global Cargo de El Salvador S.A. de C.V.';
            $DECL_DIRECCION = 'AVE Bugambilias No. 19, COL SAN FCO';
            $DECL_ID_TRIBUTARIA = 'TC263';

            $ANIO = $ano;
            $MANIFIESTO = $row->manifiesto;
            $REFERENCIA = $row->referencia;
            $PAIS_EXPORTACION = 'US';
            $MODELO_DECLARACION = 'IML';
            $TIPO_DECLARACION = '4';
            $PLACA = 'S/P';
            $TIPO = '4';
            $LOCALIZACION_MERCANCIA = '0303';
            $ADUANA_REGISTRO = '03';
            $ADUANA_ENTRADA = '03';
            $MODALIDAD_ADUANERA = '3';
            $LUGAR_DESCARGA = 'SVCPL';
            $TASAS = '';
            $INFORMACION_ADICIONAL = '';
            $VALOR_FACTURA_TOTAL = $row->cobro_final;
            $FLETE_EXTERNO = $row->flete;
            $SEGURO = '';
            
            //echo $i.' - '.$row->consignee.'<br>';
            //echo $i.' - '.$row->referencia.'<br>';

            $datos = array( 
                //buscar en manifiesto
                'CONSIGNATARIO' => array(//cliente
                    'NOMBRE' =>$CONSIG_NOMBRE,
                    'DIRECCION' => $CONSIG_DIRECCION,
                    'ID_TRIBUTARIA' => $CONSIG_ID_TRIBUTARIA     
                ),
                'DECLARANTE' => array( //empresa 
                    'NOMBRE' => $DECL_NOMBRE,
                    'DIRECCION' => $DECL_DIRECCION,
                    'ID_TRIBUTARIA' => $DECL_ID_TRIBUTARIA
                ),
                'ANIO' => $ano,
                'MANIFIESTO' => $MANIFIESTO, //buscar en preclasificacion
                'REFERENCIA' => $REFERENCIA,
                'PAIS_EXPORTACION' => $PAIS_EXPORTACION,
                'MODELO_DECLARACION' => $MODELO_DECLARACION,
                'TIPO_DECLARACION' => $TIPO_DECLARACION,
                'MEDIO_TRANSPORTE' => array(
                    'PLACA' => $PLACA,
                    'TIPO' => $TIPO
                ),
                'LOCALIZACION_MERCANCIA' => $LOCALIZACION_MERCANCIA,
                'ADUANA_REGISTRO' => $ADUANA_REGISTRO, 
                'ADUANA_ENTRADA' => $ADUANA_ENTRADA,
                'MODALIDAD_ADUANERA' => $MODALIDAD_ADUANERA,
                'LUGAR_DESCARGA' => $LUGAR_DESCARGA,
                'TASAS' => $TASAS,
                'INFORMACION_ADICIONAL' => $INFORMACION_ADICIONAL,
                'VALOR_FACTURA_TOTAL' => $VALOR_FACTURA_TOTAL,
                'FLETE_EXTERNO' => $FLETE_EXTERNO,
                'SEGURO' => $SEGURO

            );
           $d = $this->generar_xml_imc($datos);
           echo $d.'<br>';
        }
    }

    public function cargarReferencias($master, $referencia)
    {


        $datos['lista'] =  $this->Aduana_Model->cargarReferencias($master, $referencia);
        $this->load->view('aduana/cuerpo_referencia', $datos);
        // echo json_encode($request);
    }

    public function generar_xml_imc($datos){
        
        // Crear un nuevo documento XML
        $xml = new DOMDocument('1.0', 'UTF-8');
        
        // Agregar el elemento raíz <DUCA_SI>
        $duca_si = $xml->createElement('DUCA_SI');
        $xml->appendChild($duca_si);
        
        // Definir los datos estáticos
        /*$datos = array(
            'CONSIGNATARIO' => array(
                'NOMBRE' => 'Global Cargo de El Salvador S.A. de C.V.',
                'DIRECCION' => 'AVE Bugambilias No. 19, COL SAN FCO',
                'ID_TRIBUTARIA' => '06140402001036'
            ),
            'DECLARANTE' => array(
                'NOMBRE' => 'Global Cargo de El Salvador S.A. de C.V.',
                'DIRECCION' => 'AVE Bugambilias No. 19, COL SAN FCO',
                'ID_TRIBUTARIA' => 'TC263'
            ),
            'ANIO' => '2024',
            'MANIFIESTO' => '2024 2785',
            'REFERENCIA' => '92631520',
            'PAIS_EXPORTACION' => 'US',
            'MODELO_DECLARACION' => 'IML',
            'TIPO_DECLARACION' => '4',
            'MEDIO_TRANSPORTE' => array(
                'PLACA' => 'S/P',
                'TIPO' => '4'
            ),
            'LOCALIZACION_MERCANCIA' => '0303',
            'ADUANA_REGISTRO' => '03',
            'ADUANA_ENTRADA' => '03',
            'MODALIDAD_ADUANERA' => '3',
            'LUGAR_DESCARGA' => 'SVCPL',
            'TASAS' => '',
            'INFORMACION_ADICIONAL' => '',
            'VALOR_FACTURA_TOTAL' => '536.40',
            'FLETE_EXTERNO' => '53.64',
            'SEGURO' => '8.05'
        );*/
        
        // Agregar los elementos y sus datos
        foreach ($datos as $tag => $value) {
            if (is_array($value)) {
                $elemento = $xml->createElement($tag);
                foreach ($value as $subtag => $subvalue) {
                    $elemento->appendChild($xml->createElement($subtag, $subvalue));
                }
                $duca_si->appendChild($elemento);
            } else {
                $duca_si->appendChild($xml->createElement($tag, $value));
            }
        }
        
        // Agregar los elementos <ITEMS> y <DOCUMENTOS_ADJUNTOS>
        $items = $xml->createElement('ITEMS');
        $duca_si->appendChild($items);
        
        // Definir los datos de los ítems
        $items_data = array(
            array(
                'NUMERO_ITEM' => '1',
                'MARCAS_DE_EXPEDICION' => 'S/M',
                'MARCAS_DE_EXPEDICION2' => 'S/M',
                'NUMERO_BULTOS' => '1.00',
                // Agregar más datos aquí...
            ),
            array(
                'NUMERO_ITEM' => '2',
                'MARCAS_DE_EXPEDICION' => 'S/M',
                'MARCAS_DE_EXPEDICION2' => 'S/M',
                'NUMERO_BULTOS' => '1.00',
                // Agregar más datos aquí...
            )
        );
        
        // Agregar los elementos <ITEM> dentro de <ITEMS>
        foreach ($items_data as $item_data) {
            $item = $xml->createElement('ITEM');
            foreach ($item_data as $tag => $value) {
                $item->appendChild($xml->createElement($tag, $value));
            }
            $items->appendChild($item);
        }
        
        // Agregar los elementos <DOCUMENTO> dentro de <DOCUMENTOS_ADJUNTOS>
        $documentos_adjuntos = $xml->createElement('DOCUMENTOS_ADJUNTOS');
        $duca_si->appendChild($documentos_adjuntos);
        
        $documentos_data = array(
            array(
                'LINEA' => '1',
                'CODIGO_TIPO_DOC' => '001',
                'NUMERO_DOC' => 'RP126323329MU',
                'MONTO' => '536.40'
            ),
            array(
                'LINEA' => '1',
                'CODIGO_TIPO_DOC' => '024',
                'NUMERO_DOC' => 'RP126323329MU'
            )
        );
        
        foreach ($documentos_data as $documento_data) {
            $documento = $xml->createElement('DOCUMENTO');
            foreach ($documento_data as $tag => $value) {
                $documento->appendChild($xml->createElement($tag, $value));
            }
            $documentos_adjuntos->appendChild($documento);
        }
        
       // echo $xml;
        // Guardar el archivo XML
        $xml->formatOutput = true;
        $xml->save('duca_si.xml');
        echo $xml->saveXML();   
    }

    public function generarPdfGuia($id, $referencia)
    {
        if ($_SESSION["pais"] == 1) {
            //  echo "El Salvador"    ;
            $this->generarPdfGuiaSV($id, $referencia);
        }
        if ($_SESSION["pais"] == 2) {

            // $this->generarPdfGuiaGT($id, $referencia);
        }
        if ($_SESSION["pais"] == 3) {
            // echo "Honduras"    ;
            $this->generarPdfGuiaHN($id, $referencia);
        }
    }

    public function show_infoTracking($id)
    {
        // echo    'llegando al  controlador La guia que editara'. $idMnifiesto;
        // exit  ;
        $this->datos['detTraking'] =   $this->Aduana_Model->get_infoTracking($id);
        //var_dump($this->datos['list_detalleReferencia']);
        $this->load->view("declaracion/EditarTracking", $this->datos);
    }

    public function agregar_conociminto($descripcion,  $sac, $pais)
    {

        $es_courier = true;
        $postfields = array(
            "descripcion" => $descripcion,
            "sac" => $sac,
            "pais" => $_SESSION["codigo_pais"],
            "es_courier" => $es_courier
        );


        //TODO: colocar aqui metodo para obtener el token del pais
        $postfields["descripcion"] = urldecode($postfields["descripcion"]);
        // var_dump($postfields);
        //exit;

        $postfields_json = json_encode($postfields); // Important: Encode to JSON

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'gacelius_classifier.c807.com/add_knowledge',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postfields_json, // Use the JSON string here
            CURLOPT_HTTPHEADER => array(
                'Authorization: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnRlIjoiQzgwNyBPcGVyYWRvciBMb2fDrXN0aWNvIiwiYXV0b3JpemFkbyI6InRydWUifQ.gaKn0U4oMLhN-ORkvWWIgLB0MzHo23hVbHlgwhE1xxQ',
                'Content-Type: application/json' //  Crucially important: Tell the server you're sending JSON
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) { // Check for cURL errors
            echo 'Error:' . curl_error($curl);
        } else {
            echo $response;
        }


        curl_close($curl);
    }

    public function clasificar_productos($master, $referencia)
    {
        $request = $this->Aduana_Model->clasificar_productos($master, $referencia);

        // echo $master." " .$referencia;
        foreach ($request as $row) {
            // Obtener el primer producto antes del primer '+'
            $productos = explode('+', $row->ContenidoDescripcion);
            $primerProducto = trim($productos[0]); // Tomamos el primero y eliminamos espacios extra

            // Eliminar '*N' (donde N es un número del 0 al 9)
            $productoLimpio = preg_replace('/\*\d+$/', '', $primerProducto);

            echo $productoLimpio; // Para depuración

            // Llamar a la función enviarClasificar con el producto limpio
            $result = $this->enviarClasificar($productoLimpio);

            // Decodificar el JSON en un array asociativo
            $data = json_decode($result, true);

            // Acceder al primer resultado dentro de "resultados"
            if (isset($data['resultados'][0])) {
                $primerResultado = $data['resultados'][0];

                // Obtener los valores de HSC3 y TAR_PR1
                $HSC3 = substr($primerResultado['sac'], 0, 6);
                $TAR_PR1 = substr($primerResultado['sac'], 6, 2);

                // Asignar los valores en la base de datos
                $this->Aduana_Model->asignarSAC($row->idItem, $HSC3, $TAR_PR1);
            } else {
                echo "No se encontraron resultados en la clasificación.";
            }
        }
    }

    public function enviarClasificar($cadena)
    {
        $curl = curl_init();

        // Definir la cadena de datos para CURLOPT_POSTFIELDS
        $postfields = json_encode([
            "descripcion" => $cadena,
            "pais" => 503,
            "es_courier" => true
        ]);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'gacelius_classifier.c807.com/clasificar',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postfields, // Asignar la cadena creada aquí
            CURLOPT_HTTPHEADER => array(
                'Authorization: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnRlIjoiQzgwNyBPcGVyYWRvciBMb2fDrXN0aWNvIiwiYXV0b3JpemFkbyI6InRydWUifQ.gaKn0U4oMLhN-ORkvWWIgLB0MzHo23hVbHlgwhE1xxQ',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function generarPdfGuiaSV($id, $referencia)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->pdf = new FPDF('P', 'mm', 'A4');
        $datos1  =  $this->Aduana_Model->get_infoGuias($id, $referencia);

        $preclasificacion = $this->WhModel->datos_preclasificacion($id);

        $empresas = [112, 113, 114];
        $flag_temu = 0;

        if (in_array($preclasificacion->client_id, $empresas)) {
            $flag_temu = 1;
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
                )
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $result = json_decode($response);

            if (isset($result->data->details)) {
                $dlist_manifiestos  = $result->data->details;
                //$this->datos['client_id'] = $preclasificacion->client_id;
            } else {
                $this->datos['lista'] = [];
            }
        }



        //   echo $id." ". $referencia ;

        //var_dump($datos1); 
        $iteracion     = 0;
        $cuentaInterno =  0;




        foreach ($datos1 as  $datos) {

            
            if ($cuentaInterno == 0) {
                $this->pdf->AddPage();
                $this->pdf->SetMargins(30, 2,   30);
            }
            //$this->pdf->Cell(10, 10, $this->pdf->Image(getcwd() . '/public/img/logoStarship.png', 17, 16, 30, 16), 0, 0, 'L', 0);
            $this->pdf->SetFont('Arial', '', 12);
            $this->pdf->Cell(0, 10, 'GUIA AEREA', 0, 1, 'C');
            $this->pdf->Ln(3);

            //new code by Nico

            if ($flag_temu) {
                $keySearch = array_search($datos->tracking_number, $dlist_manifiestos);
                $datos->consignee = $dlist_manifiestos[$keySearch]->consignee;
            }



            if ($cuentaInterno < 2) {

                // No. de Manifiesto y Guía
                $this->pdf->Cell(0, 6, 'No. de Manifiesto: ' . $datos->manifiesto, 0, 1, 'C');
                    
                
                $this->pdf->Cell(0, 6, 'No. de Guia: ' .        $datos->tracking_number, 0, 1, 'C');
                $this->pdf->Ln(3);
                // Información de puertos y transportista
                $this->pdf->SetFont('Arial', '', 10);
                $this->pdf->Cell(95, 6, 'Puerto origen: ' . $datos->puerto_salida, 0, 0);
                $this->pdf->Cell(95, 6, 'Puerto destino: ' . $datos->puerto_salida2, 0, 1);

                $this->pdf->Cell(95, 6, 'Transportista: ' . $datos->nombre_tra, 0, 0);
                $this->pdf->Cell(95, 6, 'Cuenta: ' . '', 0, 1);

                $this->pdf->Cell(95, 6, 'Fecha vuelo: ' . $datos->fecha, 0, 0);
                $this->pdf->Cell(95, 6, 'Ruta: ' . '', 0, 1);
                $this->pdf->Ln(3);
                // Información de remitente y destinatario en columnas
                $this->pdf->SetFont('Arial', '', 10);
                $this->pdf->Cell(150, 6, 'IDENTIFICACION:',   0,   0,  'C');
                $this->pdf->Ln(4);
                $this->pdf->SetFont('Arial', '', 10);
                $this->pdf->Cell(95, 6, 'Remitente:' .  'MailAmericas', 0, 0);
                $this->pdf->Cell(95, 6, 'Destinatario:' . $datos->consignee, 0, 1);

                //$this->pdf->SetFont('Arial', '', 10);
                //$this->pdf->Cell(95, 6,$datos[0]->consignee, 0, 0);
                // $this->pdf->Cell(95, 6, $datos[0]->consignee , 0, 1);

                $this->pdf->Cell(95, 6, 'Direccion: ' . '9910 NW 77TH ST STE 4', 0, 0);
                $this->pdf->Cell(95, 6, 'Direccion: ' . 'AVE Bugambilias No. 19, COL SAN FCO', 0, 1);
                $this->pdf->Ln(2);

                $this->pdf->Cell(95, 6, 'Ciudad: ' . 'Miami', 0, 0);
                $this->pdf->Cell(95, 6, 'Ciudad: ' . 'San Salvador', 0, 1);

                $this->pdf->Cell(95, 6, 'Estado: ' . 'Florida', 0, 0);
                $this->pdf->Cell(95, 6, 'Estado: ' . 'San Salvador', 0, 1);

                $this->pdf->Cell(95, 6, 'Pais: ' . 'Estados Unidos', 0, 0);
                $this->pdf->Cell(95, 6, 'Pais: ' . 'El Salvador', 0, 1);
                $this->pdf->Ln(4);
                // Información del contenido
                $this->pdf->SetFont('Arial', 'B', 10);
                $this->pdf->Cell(95, 6, 'Contenido:', 0, 1);
                $this->pdf->SetFont('Arial', '', 10);
                $this->pdf->Cell(95, 6, utf8_decode($datos->commodity), 0, 1);
                $this->pdf->Ln(3);

                // Detalles finales
                $fleteValue = $datos->value * 0.10;
                $this->pdf->Cell(95, 6, 'Total piezas: ' . $datos->pieces, 0, 1);
                $this->pdf->Cell(95, 6, 'Peso Kg: ' . $datos->gweight, 0, 1);
                $this->pdf->Cell(95, 6, 'Flete: ' . number_format($fleteValue, 2), 0, 1);
                $this->pdf->Cell(95, 6, 'Valor: ' . number_format($datos->value, 2), 0, 1);
                if ($iteracion == 0) {
                    $this->pdf->Ln(1);
                    $this->pdf->Cell(300, 6, '- - - - - - - - - - - - - - - - - - -  - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1);
                }
                $this->pdf->Ln(2);
            }
            $iteracion     += 1;
            $cuentaInterno += 1;
            if ($iteracion == 2) {
                $cuentaInterno = 0;
                $iteracion = 0;
            }
        }

        $destino      = getcwd() . "/document/Manifiesto/";
        $desabsoluto  =  "document/Manifiesto/";
        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }
        $nombre_archivo =  'guiaAerea' . $referencia;
        $this->pdf->Output("F", $destino . $nombre_archivo, true);
        $this->pdf->close();
        $datosretorno  = array(
            'destino'        => $desabsoluto,
            'nombre_archivo' => $nombre_archivo,

        );
        // header('Content-type: appliation/json');
        echo json_encode($datosretorno);
    }
    public function generarPdfGuiaHN($id, $referencia)
    {

        $this->pdf = new FPDF('P', 'mm', 'A4');
        $datos1  =  $this->Aduana_Model->get_infoGuias($id, $referencia);
        //   echo $id." ". $referencia ;

        // var_dump($datos1); 
        $iteracion     = 0;
        $cuentaInterno =  0;
        // var_dump($datos1);
        foreach ($datos1 as  $datos) {
            // if ($cuentaInterno == 0) {
            $this->pdf->AddPage();
            //     $this->pdf->SetMargins(30, 2,   30);
            //  }
            $this->pdf->Cell(10, 10, $this->pdf->Image(getcwd() . '/public/img/logoStarship.png', 17, 16, 60, 32), 0, 0, 'L', 0);
            $this->pdf->SetFont('Arial', 'B', 12);
            $this->pdf->Ln(36);
            $this->pdf->Cell(0, 10, 'AIRWAY BILL', 0, 1, 'C');
            $this->pdf->SetFont('Arial', '', 12);

            // if ($cuentaInterno < 2) {

            // No. de Manifiesto y Guía
            // $this->pdf->Cell(0, 6, 'No. de Manifiesto: ' . $datos->manifiesto, 0, 1, 'C');
            $this->pdf->Cell(0, 6, 'No. de Guia: ' .        $datos->tracking_number, 0, 1, 'C');
            $this->pdf->Ln(3);
            // Información de puertos y transportista
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Cell(95, 6, 'Puerto origen: ' . $datos->puerto_salida, 0, 0);
            $this->pdf->Cell(95, 6, 'Puerto destino: ' . $datos->puerto_salida2, 0, 1);

            $this->pdf->Cell(95, 6, 'Transportista: ' . $datos->nombre_tra, 0, 0);
            $this->pdf->Cell(95, 6, 'Vuelo: ' . $datos->vuelo, 0, 1);

            $this->pdf->Cell(95, 6, 'Fecha vuelo: ' . $datos->fecha, 0, 1);
            //  $this->pdf->Cell(95, 6, 'Ruta: ' . '', 0, 1);
            $this->pdf->Ln(13);
            // Información de remitente y destinatario en columnas
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Cell(180, 6, 'IDENTIFICACION:',   0,   0,  'C');
            $this->pdf->Ln(8);
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Cell(95, 6, 'Remitente:' .  'MailAmericas', 0, 0);
            $this->pdf->Cell(95, 6, 'Destinatario:' . $datos->consignee, 0, 1);

            //$this->pdf->SetFont('Arial', '', 10);
            //$this->pdf->Cell(95, 6,$datos[0]->consignee, 0, 0);
            // $this->pdf->Cell(95, 6, $datos[0]->consignee , 0, 1);

            $this->pdf->Cell(95, 6, utf8_decode('Dirección: ') . '9910 NW 77TH ST STE 4', 0, 0);
            $this->pdf->Cell(95, 6, utf8_decode('Dirección: ') .  $datos->consignee_address1, 0, 1);
            $this->pdf->Ln(2);

            $this->pdf->Cell(95, 6, 'Ciudad: ' . 'Miami', 0, 0);
            $this->pdf->Cell(95, 6, 'Ciudad: ' . $datos->consignee_city, 0, 1);

            $this->pdf->Cell(95, 6, 'Estado: ' . 'Florida', 0, 0);
            $this->pdf->Cell(95, 6, 'Estado: ' . $datos->consignee_state, 0, 1);

            $this->pdf->Cell(95, 6, 'Pais: ' . 'Estados Unidos', 0, 0);
            $this->pdf->Cell(95, 6, 'Pais: ' . 'Honduras', 0, 1);
            $this->pdf->Ln(4);
            // Información del contenido

            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Cell(95, 6, 'Contenido:', 0, 1);
            $this->pdf->SetFont('Arial', '', 10);
            $cadena_sin_corchetes = trim($datos->commodity, '[]');
            $this->pdf->Cell(95, 6, utf8_decode($cadena_sin_corchetes), 0, 1);
            $this->pdf->Ln(3);

            // Detalles finales
            $fleteValue = $datos->value * 0.10;
            $seguro = ($datos->value * 1.50) / 100;
            $this->pdf->Cell(95, 6, 'Total piezas: ' . $datos->pieces, 0, 1);
            $this->pdf->Cell(95, 6, 'Peso Kg: ' . $datos->gweight, 0, 1);
            $this->pdf->Cell(95, 6, 'Flete: ' . number_format($fleteValue, 2), 0, 1);
            $this->pdf->Cell(95, 6, 'Seguro: ' . number_format($seguro, 2), 0, 1);
            //   if ($iteracion == 0) {
            //     $this->pdf->Ln(1);
            //      $this->pdf->Cell(300, 6, '- - - - - - - - - - - - - - - - - - -  - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1);
            //  }
            $this->pdf->Ln(2);
            //  }
            // $iteracion     += 1;
            // $cuentaInterno += 1;
            // if ($iteracion == 2) {
            ////     $cuentaInterno = 0;
            $iteracion = 0;
            // }
        }

        $destino      = getcwd() . "/document/Manifiesto/";
        $desabsoluto  =  "document/Manifiesto/";
        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }
        $nombre_archivo =  'guiaAerea' . $referencia;
        $this->pdf->Output("F", $destino . $nombre_archivo, true);
        $this->pdf->close();
        $datosretorno  = array(
            'destino'        => $desabsoluto,
            'nombre_archivo' => $nombre_archivo,

        );
        // header('Content-type: appliation/json');
        echo json_encode($datosretorno);
    }

    public function updateInfoTrack()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $codigoArancelario = $this->input->post('codigoArancelario');

        $HSC3 = substr($codigoArancelario, 0, 6);
        $TAR_PR1 = substr($codigoArancelario, -2);
        $data = [
            // 'id_item' => $this->input->post('id_item'),
            // 'id_precla' => $this->input->post('id_precla'),
            //  'tipo_transporte_id' => $this->input->post('tipo_transporte_id'),
            // 'transportista_id' => $this->input->post('transportista_id'),
            // 'puertosalida_id' => $this->input->post('puertosalida_id'),
            // 'puertosalida2_id' => $this->input->post('puertosalida2_id'),
            //  'declarante_id' => $this->input->post('declarante_id'),
            //  'agent_exp_id' => $this->input->post('agent_exp_id'),
            //'agent_cong_id' => $this->input->post('agent_cong_id'),

            // 'Nun_Reg' => $this->input->post('master'),
            // 'Declaracion' =>  $this->input->post('id_item'),
            'DocumentoTransporte' => $this->input->post('documento_transporte'),
            'HSC_Tipo' => 0,
            'HSC3' => $HSC3,
            'TAR_PR1' => $TAR_PR1,
            'TAR_PR2' => 0,
            'CP4_COD' => 0,
            'CP3_COD' => 0,
            'PKG_COD' => "PK",
            'Bultos' =>  $this->input->post('bultos'),
            'PesoKg' =>  $this->input->post('peso'),
            'ContenidoDescripcion' => $this->input->post('contenido_descripcion'),
            'Unidades' =>  $this->input->post('unidades'),
            'Valor' => $this->input->post('valor'),
            'Seguro' => $this->input->post('seguro'),
            'Flete' =>   $this->input->post('flete'),
            'Otros' => $this->input->post('otros'),
            'PaisOrigen' =>  $this->input->post('pais_origen'),
            'FacturaCodigo' =>  $this->input->post('factura_codigo'),
            'FacturaFecha' => $this->input->post('fecha_factura'),
            'Consignatario' => $this->input->post('consignatario'),
            'ICETCI' => 0,
            'Marcas' => 0,
            'Numeros' => 0,
            'IdConsignatario' => $this->input->post('id_consignatario'),
            'TipoId' => $this->input->post('tipoDocumento'),
            'Regimen' => $this->input->post('regimen'),
        ];



        // Llama al método del modelo para actualizar la información
        $result = $this->Aduana_Model->updateInfoTrack($data, $this->input->post('id_item'));

        // Responde según el resultado
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Datos actualizados correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Hubo un error al actualizar los datos.']);
        }
    }

}