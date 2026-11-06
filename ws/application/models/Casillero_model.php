<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Casillero_model extends CI_Model
{
    public function get_casillero($id)
    {
        $query = $this->db
         ->select("wd.*, es.nombre nombre_estatus")
         ->join('estatus  as es', 'es.id_estatus = wd.estatus', 'left')
         ->where('wd.cuenta', $id)
         ->order_by('wd.fecha_ingreso', 'DESC')
         ->get('warehouse_detalle wd')
         ->result();
       // $row=$query->custom_row_object(0,'Casillero_model');
       
        return $query;
    }

     public function obtner_sucursales()
    {
        $query = $this->db->select('*')
         ->get('sucursales')
         ->result();
         return $query;
    }

    
}

/* End of file ModelName.php */
