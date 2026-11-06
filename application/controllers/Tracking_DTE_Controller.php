<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tracking_DTE_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Tracking_DTE_Model');
    }

    public function tracking()
    {
        $this->load->view("vista_dte/vista_form", $this->datos);
    }

    public function consulta_tracking()
    {
        $wh = $_POST['search'];
        $this->datos['wareh'] =  $this->Tracking_DTE_Model->buscar_tracking($wh);
       
        $this->load->view('vista_dte/consulta_dte', $this->datos);
    }
}