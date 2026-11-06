<?php

defined('BASEPATH') or exit('No direct script access allowed');
//include getcwd() . "/libraries/RestController.php";

require(APPPATH.'/libraries/REST_Controller.php');
//use Restserver\Libraries\RestController;
class Clientes extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Cliente_model');
    }
    

    public function cliente_get()
    {
        $id=$this->uri->segment(3);
        if (!isset($id)) {
            $respuesta =array(
                'err' => TRUE,
                'mensaje'=> 'Es necesario el ID del cliente'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $cliente=$this->Cliente_model->get_cliente($id);
        if (isset($cliente)){
            $respuesta =array(
                'err' => FALSE,
                'mensaje'=> 'Registro cargado correctamente',
                'cliente' =>$cliente
            );
            $this->response($respuesta);
        }else{
            $respuesta =array(
                'err' => TRUE,
                'mensaje'=> 'El registro con el ID '.$id. ', no existe ',
                'cliente' =>null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
       // $this->response($cliente);
    }
}

/* End of file Controllername.php */
