<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Consulta_track_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
       // $this->load->library('PHPEXCEL/PHPExcel.php');
        $this->load->database();
        $this->load->model('Conf_model');
        $this->load->model('WarehouseModel');
        $this->load->model('SendModel');
        $this->load->model('Traspaso_model');
        $this->load->model('PreclasificacionModel');
        $this->load->model('WhModel');
        $this->load->helper('path');
        $this->load->view("verifica");
    }

    public function consulta_track()
    {
        $this->load->view("consulta_track/form");
    }

    public function buscar_tracking_numb($tracking)
    {   

        $tipo = $_GET["tipo"];

        $this->db->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");

        if($tipo == "tracking"){
            $this->datos['tracking_numb'] = $this->WhModel->buscar_tracking_numb($tracking);
        }else{
            $this->datos['tracking_numb'] = $this->WhModel->buscar_package_numb($tracking);
        }

        

        $data = [
            "id_solicitante" => $_SESSION["user_id"],
            "fecha" => date('Y-m-d h:i:s'),
            "solicitud" => $tracking . "-search",
            "respuesta" => json_encode($this->datos['tracking_numb']),
            "pagina" => "consulta-tracking"
        ];

        $this->WhModel->insert_log($data);






        $this->load->view("consulta_track/cuerpo",  $this->datos);
    }
}