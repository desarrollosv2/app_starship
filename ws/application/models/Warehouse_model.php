<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse_model extends CI_Model
{
    public function get_warehouse($id)
    {
        $query = $this->db
         ->where('idwarehouse', $id)
         ->get('warehouse_detalle')
         ->row();
       // $row=$query->custom_row_object(0,'Casillero_model');
       
        return $query;
    }

    public function get_estatus($id)
    {
      

        // $query = $this->db
        // ->select('de.fecha, de.warehouse,de.id_estatus, es.nombre as descripcion, de.casillero')
        // ->join('estatus es', 'es.id_estatus = de.id_estatus', 'inner')
        // ->where('de.warehouse', $id)
        // ->get('detalle de')
        // ->result();
        // return $query;

        $query = $this->db
        ->select("es.id_estatus,es.nombre, de.fecha,es.nombre,de.problema_gestion, de.timezone, de.location")
        ->join('detalle de', 'de.id_estatus = es.id_estatus', 'inner')
        ->where('de.warehouse', $id)
       // ->where('de.id_pais', $_SESSION['pais'])
         ->order_by('fecha DESC')
        ->get('estatus es')
        ->result();

    return $query;

    }

    public function get_tracking($id)
    {
      

        // $query = $this->db
        // ->select('de.fecha, de.warehouse,de.id_estatus, es.nombre as descripcion, de.casillero')
        // ->join('estatus es', 'es.id_estatus = de.id_estatus', 'inner')
        // ->where('de.warehouse', $id)
        // ->get('detalle de')
        // ->result();
        // return $query;

        $query = $this->db
        ->select("consignee,consignee_email,consignee_phone,tracking_number, manejo, comision, total_imp,  total_ivag, cobro_final")
       
        ->where('tracking_number', $id)
        ->get('manifiesto')
        ->row();

    return $query;

    }
}

/* End of file ModelName.php */
