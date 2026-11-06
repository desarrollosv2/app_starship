<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ArancelController extends CI_Controller
{

  //Funcion Constructor en esta funcion se agregan todos los modelos que se usaran en el sitio
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->database();
    $this->load->model('Arancel_Model');
    $this->load->model('Conf_model');

    // $_SESSION['pais_id'] = 2; //El Salvador
    // //  $_SESSION['pais_id']=3;  //Honduras
    // if (isset($_SESSION['pais_id'])) {
    //   $this->pais = $_SESSION['pais_id'];
    // } else {
    //   $this->pais = 0;
    // }
  }

  //En esta funcion muestra todos los datos de la tabla
  public function listado_partida()
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $this->datos['catalogopermisos'] = $this->Conf_model->catalogo_permisos();

    $datos['partidas'] = $this->Arancel_Model->partidas_creadas();
    //  var_dump( $datos['partidas']);
    //var_dump( $datos['partidas'] );
    $this->load->view("arancel/lista", $datos);
  }

  //Funcion para Ingresar al boton de Agregar 
  public function agregar_partida($id)
  {

    // $datos['origen'] = $this->Conf_model->get_paises();

    // $datos['lista_estado'] = $this->Conf_model->get_estado();

    // $datos['lista_origen'] = $this->Conf_model->get_origen();

    // $datos['origen'] = $this->Conf_model->get_paises();
     $datos['datos_partida'] = $this->Arancel_Model->editar_partidas($id);
    $this->load->view('arancel/form', $datos);
  }

  // Funcion para Guardar los registros del formulario de partidas
  public function insertar_partidas()
  {

    $id              =    $_POST["id_arancel"];

    $codigo_producto =    $_POST["codigo_producto"];
    $numeroPartida   =    $_POST["numeroPartida"]; //  indica que viene valor de  formularios 
    $descripcion     =    $_POST["descripcion"];
    $marca           =    $_POST["marca"]; //Nuevos datos a solicitar
    $modelo          =    $_POST["modelo"];
    $descripcion_generica = $_POST["descripcion_generica"];
    $estado = $_POST["estado"];
    $origen = $_POST["origen"];
    $porcentaje = $_POST["porcentaje"];

    $data = array(
      'codigo_producto'  =>  $codigo_producto,
      'numero_partida'   =>  $numeroPartida,
      'descripcion'      =>  $descripcion,
      'marca'            => $marca, //agregando nuevos datos
      'modelo'           => $modelo,
      'descripcion_generica' => $descripcion_generica,
      'id_estado' => $estado,
      'id_origen' => $origen,

      'id_pais' => $_SESSION["pais"],


    );
    $this->Arancel_Model->insertar_partidas($data, $id);
  }

  //Funcion para guardar partidas editadas
  public function guardar_partida($ID)
  {
    $id              =  NULL;
    $numeroPartida   =     0;
    $descripcion     =    '';
    $origen          =    '';
    $anulado         =     0;


    $ID              =  NULL;
    $numeroPartida   =     0;
    $descripcion     =    '';
    $origen          =    '';
    $anulado         =     0;
    //Enviamos los datos por medio del metodo POST
    //Variable             Nombre segun input
    $numeroPartida   =    $_POST["numeroPartida"]; //  indica que viene valor de  formularios 
    $descripcion     =    $_POST["descripcion"];
    $marca           =    $_POST["marca"];
    $modelo          =    $_POST["modelo"];
    $descripcion_generica = $_POST["descripcion_generica"];
    $estado =             $_POST["estado"];
    $origen          =    $_POST["origen"];
    $porcentaje = $_POST["porcentaje"];
    $anulado         =    $_POST["anulado"];


    $data = array(
      'numero_partida'   =>  $numeroPartida,
      'descripcion'      =>  $descripcion,
      'marca'            =>  $marca,
      'modelo'           =>  $modelo,
      'descripcion_generica' => $descripcion_generica,
      'id_estado' => $estado,
      'id_origen'        =>  $origen,
      'porcentaje'        =>  $porcentaje,
      'anulado'          =>  $anulado

    );
  }

  //Esta funcion se encarga de eliminar el campo de las partidas
  public function eliminar_partida($id)
  {
    $this->Arancel_Model->eliminar_partida($id);
  }

  public function listado_permisos($id)
  {
    $datos['lista_p']    = $this->Arancel_Model->listado_permisos($id);
    $this->load->view('arancel/cuerpo_permisos', $datos);
  }

  public function agregar_permiso($permiso, $id)
  {
    $data = array(

      'idpermiso'           => $permiso,

      'partida'              => $id

    );

    $result = $this->Arancel_Model->verificar_permiso($permiso, $id);
    if ($result) {
    } else {
      $result = $this->Arancel_Model->agregar_permiso($data);
      echo $result;
    }
  }


  public function eliminar_permiso($id)
  {
    $this->Arancel_Model->eliminar_permiso($id);
  }


  public function asignar_codigos_arancelarios($id)
  {

    // Obtener las descripciones del manifiesto
    $this->load->model('Arancel_model');
    $this->load->library('OpenAIHelper');

    $productos = $this->Arancel_model->obtener_productos_sin_codigo($id);
    //echo "estoy aqui";
    //var_dump($productos);
    //exit;
    // Obtener productos sin código desde la base de datos
   

    // Procesar los productos con OpenAI
    $respuesta_ia = $this->openaihelper->procesar_productos($productos);

    // Validar y procesar la respuesta
    if (isset($respuesta_ia['choices'][0]['message']['content'])) {
      $resultado = json_decode($respuesta_ia['choices'][0]['message']['content'], true);

      // Actualizar los códigos en la base de datos
      foreach ($resultado as $producto) {
        $this->Arancel_model->actualizar_codigo_arancelario($producto['id'], $producto['codigo_arancelario']);
      }

      echo json_encode(['status' => 'success', 'message' => 'Códigos asignados correctamente.']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Error al procesar los productos con OpenAI.']);
    }
  }
}
