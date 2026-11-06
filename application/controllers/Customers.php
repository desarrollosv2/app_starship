<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Customers extends CI_Controller {
    public function __construct() {
        parent::__construct();
        session_start();
        $this->load->database();
        $this->load->model( 'Conf_model' );
        $this->load->model( 'Customers_model' );
        $this->load->model( 'WhModel' );
        $this->load->model( 'WarehouseModel' );
        $this->load->model( 'SendModel' );
        //titulo

        //Do your magic here
    }

    public function index() {
        // $this->load->view( "customers/menu_customers" ) ;
        // $this->customers();
    }

    public function customers() {
        $this->datos['navtext']   = "Nombre: " . $_SESSION["nombre"] . " / " . " Casillero: " . $_SESSION["casillero"];
        $this->datos['form']     = "customers/contenido";
        $this->load->view( "customers/lista", $this->datos );
    }

    public function listado_customer() {
        $casillero = $_SESSION["casillero"];

        $track['tracking'] = $this->Customers_model->verificar_tracking( $casillero );

        $datos['lista'] = $this->Customers_model->listado_customer( $casillero );
        //  $datos['servicios'] = $this->Conf_model->get_servicios();

        $c = 0;

        foreach ( $datos['lista'] as $item ) {
            $last['estatus'] = $this->Customers_model->ultimo_estado( $item->idwarehouse );

            $datos['lista'][$c]->estado = $last['estatus']->nombre_estatus;

            $cadena =  substr( $item->tracking, -12 );

            $datos['lista'][$c]->pre_alertada = "NO";
            foreach ( $track['tracking'] as $fila ) {

                if ( $cadena == $fila->ntracking ) {

                    $datos['lista'][$c]->pre_alertada = "SI";

                }

            }

            $c = $c + 1;
        }

        $this->load->view( "customers/cuerpo", $datos );
    }

    public function contactanos() {
        $this->load->view( "customers/form_contactanos" );
    }

    public function cambiar_clave() {
        $this->load->view( "customers/customers_modal" );
    }

    public function cambiar_email() {
        $this->load->view( "customers/customers_modal" );
    }

    public function suc_entrega() {
        $this->datos['sucursales'] = $this->Conf_model->sucursales();
        $this->load->view( "customers/customers_modal", $this->datos );
    }

    public function crear_prealerta() {
        $this->load->view( "customers/customers_modal" );
    }

    public function guardar_password() {
        $data = array(
            'id'        =>  $_SESSION["user_id"],
            'clave'     =>  $_POST['password']
        );
        $result = $this->Customers_model->guardar_password( $data );
        echo $result;
    }

    public function guardar_password2() {
        $correo = $_POST["user_email"];
        $data = array(
            'id'        =>  $_POST["user_id"],
            'clave'     =>  $_POST['password']
        );

        $result = $this->Customers_model->guardar_password2( $data );
        echo $result;
        /**/

        if ( $result > 0 ) {
            $body = "Esta es su contraseña " . $_POST['password'];
            /*envio de correo*/
            $this->SendModel->sendmail( $body, $correo );
        }
    }

    public function guardar_email() {
        $data = array(
            'id'        =>  $_SESSION["user_id"],
            'correo'     =>  $_POST['correo']
        );
        $result = $this->Customers_model->guardar_email( $data );
        echo $result;
    }

    public function guardar_sucursal() {

        $data = array(
            'casillero'        =>  $_SESSION["casillero"],
            'id_retiro'     =>  $_POST['sucursal']
        );
        $result = $this->Customers_model->guardar_sucursal( $data );
        echo $result;
    }

    public function info_cuenta() {

        $cuenta = $_SESSION["casillero"];
        ///echo $cuenta;
        //  $resultado = intval( preg_replace( '/[^0-9]+/', '', $cuenta ), 10 );
        //   $resultado = preg_replace( '', '', $cuenta );
        // echo $resultado;
        $this->datos['info_cliente'] = $this->WarehouseModel->buscarcliente( $cuenta );
        //var_dump( $this->datos['info_cliente'] );
        $this->datos['sucursales'] = $this->Conf_model->sucursales();
        $this->datos['tipo_cuenta'] = $this->Conf_model->tipo_cuenta();
        $this->datos['navtext']   = "";
        $this->datos['form']     = "customers/contenido_cuenta";
        $this->load->view( "customers/informacion_cuenta", $this->datos );
    }

    public function consulta_prealerta( $id ) {

        //$listar = array();
        $rsl = $this->Customers_model->consulta_prealerta( $id );
        echo json_encode( $rsl );
    }

    public function prealertas() {
        $this->datos['courier']  = $this->Conf_model->courier();
        $this->datos['navtext']  = "PRE ALERTAS";

        if ( $_SESSION['interno'] == 1 ) {
            $casillero = 0;
            $this->datos['lista'] = $this->Customers_model->listado_prealerta_all();
        } else {
            $casillero = $_SESSION["casillero"];
            $this->datos['lista'] = $this->Customers_model->listado_prealerta( $casillero );
        }

        if ( $_SESSION["interno"] == 0 ) {
            $this->datos['form']     = "customers/contenido_prealerta";
        } else {
            $this->datos['form']     = "customers/contenido_prealerta_interna";
        }

        $this->load->view( "customers/listado_prealerta", $this->datos );
        //$this->load->view( "customers/cuerpo_prealerta", $this->datos );
        //  $rsl = $this->Customers_model->consulta_prealerta( $id );
        //echo json_encode( $rsl );

    }

    public function guardar_prealerta() {
        $hex_string = "";
        $data = "";
        if ( $_FILES['upload_file']['tmp_name'] != null ) {
            $nombre = $_FILES['upload_file']['name'];
            $tmp = $_FILES['upload_file']['tmp_name'];
            $limpiar = str_replace( " ", "", basename( $_FILES["upload_file"]["name"] ) );
            $temp = explode( ".", $limpiar );
            $nombre = 'NombreArchivo' . round( microtime( true ) ) . '.' . end( $temp );
            $folder = 'public/uploads';
            move_uploaded_file( $tmp, $folder . '/' . $nombre );
            $img = $folder . '/' . $nombre;

            $data = array(
                'ntracking'     =>  $_POST["numero_tracking"],
                'id_courier'    =>  $_POST['comp_courier'],
                'tcompraste'    =>  $_POST['tienda_compra'],
                'vpaquete'      =>  $_POST['valor_paquete'],
                'desc_paquete'  =>  $_POST['comment'],
                'imgfactura'    =>  $img,
                'estado'        =>  0,
                'casillero'     =>  $_SESSION["casillero"]
            );
        } else {

            $data = array(
                'ntracking'     =>  $_POST["numero_tracking"],
                'id_courier'    =>  $_POST['comp_courier'],
                'tcompraste'    =>  $_POST['tienda_compra'],
                'vpaquete'      =>  $_POST['valor_paquete'],
                'desc_paquete'  =>  $_POST['comment'],
                'estado'        =>  0,
                'casillero'     =>  $_SESSION["casillero"]
            );
        }
        $id = $_POST["id_prealerts"];
        $result = $this->Customers_model->guardar_prealerta_modal( $id, $data );

        $casillero  = $_SESSION["casillero"];
        $nombre     = $_SESSION["nombre"];
        $trackign   = $_POST["numero_tracking"];

        echo $result;

        $enviar = $this->sendmail_nt( $nombre, $casillero, $trackign );
    }
    /*envio de correo;
    casillero, trackign, nombre del cliente   */

    public function sendmail_nt( $nombre, $casillero, $trackign ) {

        $this->load->library( 'email' );

        $config = array(
            'protocol' => 'mail',
            'smtp_host' => 'smtp.mistarship.com',
            'smtp_port' => 465,
            'smtp_user' => 'bug@mistarship.com',
            'smtp_pass' => 'd3sarr@110star<(0)',
            'mailtype' => 'html',
            'validate'  => TRUE,
            'charset' => 'utf-8',
        );

        //$correo = 'desarrollosv@c807.com';
        $correo = 'juanobisp@gmail.com';
        $this->email->initialize( $config );
        $this->email->set_newline( "\r\n" );
        $this->email->from( 'elmer.guardado@gmail.com', 'Starship Shopping' );
        $this->email->to( $correo );
        // $this->email->to( 'mavolevan@mistarship.com' );
        $this->email->subject( 'Estatus' );
        $filename = 'public/imagenes/logo.png';
        $this->email->attach( $filename );
        $cid = $this->email->attachment_cid( $filename );
        //   $this->email->message( '<img src="cid:'. $cid .'" alt="photo1" />' );
        $this->email->message( '<img src="cid:' . $cid . '" alt="photo1" <br><br> ' . $nombre . '<br><br> ' . $casillero . ' <br> ' . $trackign . '<br><br> <br><h2>CORREO DE PRUEBA</h2> ' );

        if ( $this->email->send() ) {
            echo 'Your email was sent.';
        } else {
            echo $this->email->print_debugger();
        }
    }

    public function lista_prealerta() {
        $casillero = $_SESSION["casillero"];
        $this->datos['lista'] = $this->Customers_model->lista_prealerta( $casillero );
        $this->load->view( "customers/lista_prealerta", $this->datos );
        // $this->load->view( "customers/customers_modal" );
    }

    public function mostrar_lista_prealerta() {
        if ( $_SESSION['interno'] == 1 ) {
            $casillero = 0;
            $this->datos['lista'] = $this->Customers_model->listado_prealerta_all();
        } else {
            $casillero = $_SESSION["casillero"];
            $this->datos['lista'] = $this->Customers_model->listado_prealerta( $casillero );
        }
        //$this->load->view( "customers/lista_prealerta", $this->datos );
        $rsl = $this->load->view( "customers/cuerpo_prealerta", $this->datos );
        echo $rsl;
    }

    public function busqueda_prealerta() {
        $busqueda = $_POST["busqueda"];
        $campo = $_POST["campo"];

        if ( $campo == 1 ) {
            $campo = "ntracking";
        } else if ( $campo == 2 ) {
            $campo = "casillero";
        }

        $this->datos['lista'] = $this->Customers_model->busqueda_prealerta( $campo, $busqueda );
        $this->load->view( "customers/cuerpo_prealerta", $this->datos );
    }

    public function confirmar_prealerta( $id ) {
        //$listar = array();
        $rsl = $this->Customers_model->confirmar_prealerta( $id );
        echo $rsl;
    }

    public function consulta_estatus( $id ) {

        $url = utf8_encode( file_get_contents( 'http://pakya.cargotrack.net/appl2.0/auto/api.asp?api_key=j7c56la49n&api_action=track&class=warehouse&reference='.$id ) );
        $xml = simplexml_load_string( $url );

        $this->datos['whouse'] = $xml;

        $this->datos['l_estatus'] = $this->Customers_model->consulta_estatus( $id );

        $this->load->view( "customers/lista_estatus", $this->datos );
    }

    public function lista_servicios() {
        $this->datos['servicios'] = $this->Conf_model->get_servicios();
        $this->load->view( "catalogos/servicios", $this->datos );

    }

    public function lista_departamentos() {

        $this->datos['departamento'] = $this->Conf_model->get_departamentos();
        $this->load->view( "catalogos/departamentos", $this->datos );

    }

    public function lista_municipios( $id ) {

        $this->datos['municipio'] = $this->Conf_model->get_municipios( $id );
        $this->load->view( "catalogos/municipios", $this->datos );

    }

    public function tempo() {
        $wh = $_POST['search'];
        $url = utf8_encode( file_get_contents( 'http://pakya.cargotrack.net/appl2.0/auto/api.asp?api_key=j7c56la49n&api_action=track&class=warehouse&reference='.$wh ) );
        $xml = simplexml_load_string( $url );

        $id_cliente = "";

        $this->datos['wareh'] =  $this->WarehouseModel->buscar_warehouse( $wh );
        foreach ( $this->datos['wareh'] as $f ) {
            $id_cliente = $f->casillero;
            break;
        }
        $cliente = $this->WarehouseModel->buscar_cliente( $id_cliente );
        $nombre = $cliente->nombres. " ". $cliente->apellidos;
        // var_dump( $cliente );
        $array [] = array( "nombre" => $nombre );
        $this->datos['cliente'] = $array;
        $this->datos['whouse'] = $xml;

        $this->load->view( 'warehouse/consulta_wh_estatus', $this->datos);
    }

}