<?php

defined('BASEPATH') or exit('No direct script access allowed');
//include getcwd() . "/libraries/RestController.php";

require(APPPATH . '/libraries/REST_Controller.php');
//use Restserver\Libraries\RestController;
class ManifiestoApi extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Manifiesto_model');
    }


    public function departamentos_post($tracking)
    {

        //$id = $this->uri->segment(3);
        if (!isset($tracking)) {
            $respuesta = array(
                'err' => TRUE,
                'mensaje' => 'Es necesario el numero de tracking'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $query = $this->Manifiesto_model->get_guiaFel($tracking);

        $municipio_id = $query->municipio_id;
        $departamento_id = $query->departamento_id;

        $array = array("municipio"=>$municipio_id,"departamento" =>$departamento_id);

        echo json_encode($array);

        
    }
}

/* End of file Controllername.php */
