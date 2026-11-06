<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cotizador_model extends CI_Model
{

  public function obtner_arancel()
  {
    $otherdb = $this->load->database('sts', TRUE);

    $query = $otherdb->select('id,arancel')
      ->get('aranceles')
      ->result();
    return $query;
  }

  public function get_flete($peso)
  {
    $otherdb = $this->load->database('sts', TRUE);

    $query = $otherdb->select('*')
      ->where('de <=', $peso)
      ->where('hasta>=', $peso)
      ->get('rangos_flete')
      ->row();
    return $query;
  }


  public function get_tramite($valor)
  {
    $otherdb = $this->load->database('sts', TRUE);

    $query = $otherdb->select('*')
      ->where('de <=', $valor)
      ->where('hasta>=', $valor)
      ->get('montos_tramite')
      ->row();
    return $query;
  }

  
  public function get_seguro($valor)
  {
    $otherdb = $this->load->database('sts', TRUE);

    $query = $otherdb->select('*')
    ->where('de <=', $valor)
    ->where('hasta>=', $valor)  
    ->get('seguros')
    ->row();
    return $query;
  }

  
  public function get_arancel($id)
  {
    $otherdb = $this->load->database('sts', TRUE);

    $query = $otherdb->select('*')
    ->where('id', $id)  
    ->get('aranceles')
    ->row();
    return $query;
  }

  
  public function get_almacenaje($peso)
  {
    $otherdb = $this->load->database('sts', TRUE);

    $query = $otherdb->select('*')
      ->where('de <=', $peso)
      ->where('hasta>=', $peso)
      ->get('almacenaje')
      ->row();
    return $query;
  }

}
    
