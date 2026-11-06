<?php

defined('BASEPATH') or exit('No direct script access allowed');
include getcwd()."/application/libraries/fpdf/fpdf.php";

class Guias_creadas_Controller extends CI_Controller
{
  //Funcion Constructor en esta funcion se agregan todos los modelos que se usaran en el sitio
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->model('Comparativo_guias_Model');
  }

  public function guias_creadas()
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $this->load->view("guias_creadas/form");
  }

  public function buscar_fecha_creacion_guia($fecha_desde_guia, $fecha_hasta_guia)
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $data['tracking'] = $this->Comparativo_guias_Model->buscar_fecha_guia_replace($fecha_desde_guia, $fecha_hasta_guia);
    $this->load->view("guias_creadas/cuerpo", $data);
  }
}