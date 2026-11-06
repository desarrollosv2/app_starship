<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Referencia_Model extends CI_Model 
{
    public function guardar_referencia($data) 
    {
        $this->db->insert('base_manifiesto', $data);
        //return $this->db->insert_id();
    }
}