<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tasa_cambioController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Tasa_Cambio_Model');
        $this->load->model('Conf_model');
    }

    public function listar_tasa_cambio()
    {

        $this->datos['tasa_cambio'] = $this->Tasa_Cambio_Model->tasa_de_cambio();

        $this->load->view("tasa_de_cambio/vista_tasa_cambio", $this->datos);
    }

    public function mostrar_tasa_cambio($id_tasa)
    {

        $datos['pais'] = $this->Conf_model->paises_ec();

        if ($id_tasa == 0) {
            $this->load->view("tasa_de_cambio/form", $datos);
        } else {
          
            $datos['tasa_cambio'] = $this->Tasa_Cambio_Model->mostrar_tasa_cambio($id_tasa);
          
           $this->load->view("tasa_de_cambio/form", $datos);
        }
    }

    
    public function guardar()
    {
        
        $id_tasa                = $_POST["id_tasa1"];
        $id_pais                = $_POST["id_pais"];
        $nombre_moneda          = $_POST["nombre_moneda"];
        $tasa_de_cambio         = $_POST["tasa_de_cambio"];



        $data = array(
            //'id_tasa'               => $id_tasa,
            'id_pais'               => $id_pais,
            'nombre_moneda'         => $nombre_moneda,
            'tasa'        => $tasa_de_cambio,

        );

        $date = $this->Tasa_Cambio_Model->guardar($data, $id_tasa);
    }

    public function eliminar_tasa_cambio($id_tasa)
    {
        $this->Tasa_Cambio_Model->eliminar_tasa_cambio($id_tasa);
    }
}   /* End of file Controllername.php */
