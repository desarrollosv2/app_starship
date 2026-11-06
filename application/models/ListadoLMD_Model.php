<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ListadoLMD_Model extends CI_Model
{
    public function crear_encabezado($id)
    {
        $query = $this->db
                      ->select('*')
                      ->where("id", $id)
                      ->get('encab_manifiesto')
                      ->row();
        return $query;
    }






      //Funcion para guardar las partidas editadas
  public function guardar_manifiesto($id, $data)
  {
    if ($id == null) 
    {
      return $this->db->insert("encab_manifiesto", $data);
    } 
    else 
    {
      $this->db
           ->set('fecha_manifiesto',        $data['fecha_manifiesto'])
           ->set('tipo_entrega',            $data['tipo_entrega'])
           ->set('observaciones',           $data['observaciones'])   
           ->WHERE('id', $id)
           ->update('encab_manifiesto');
      return ($this->db->affected_rows() > 0);
    }
  }

    public function encabezado_manif()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $query = $this->db
                      ->SELECT('*')
                      ->get('encab_manifiesto')
                      ->result();
        return $query;  
    }

    public function ver_encabezado_manif($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //echo("Llegando al Model id : ".$id);
        $query = $this->db
                      ->SELECT('*')
                      ->WHERE('id', $id)
                      ->get('encab_manifiesto')
                      ->result();
        return $query;  

        var_dump($query);
    }

    public function vista_manif($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $query = $this->db
                      ->SELECT('*')
                      ->WHERE('id', $id)
                      ->get('encab_manifiesto')
                      ->result();
        return $query;  
    }

    public function detalle_manif($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $query = $this->db
                      ->SELECT('detalle_manifiesto.id,
                                manifiesto.tipo_servicio,
                                preclasificacion.manifiesto,
                                detalle_manifiesto.id_manifiesto,
                                manifiesto.referencia,
                                detalle_manifiesto.no_guia, 
                                manifiesto.tracking_number, 
                                manifiesto.tracking_replace,
                                manifiesto.consignee, 
                                manifiesto.value,
                                manifiesto.dai,
                                manifiesto.iva,
                                manifiesto.commodity,
                                manifiesto.tipo_entrega,
                                manifiesto.departamento,
                                manifiesto.municipio,
                                manifiesto.cobro_final')
                      ->JOIN('manifiesto', 'manifiesto.tracking_number = detalle_manifiesto.no_guia', 'INNER')  
                      ->JOIN('encab_manifiesto', 'encab_manifiesto.id = detalle_manifiesto.id_manifiesto', 'INNER')
                      ->JOIN('preclasificacion', 'preclasificacion.idpreclasificacion = manifiesto.id_manifiesto', 'INNER')
                      
                      ->WHERE('detalle_manifiesto.id_manifiesto', $id)
                      ->ORDER_BY('detalle_manifiesto.id', 'DESC')
                      ->GROUP_BY('manifiesto.tracking_number')
                      ->get('detalle_manifiesto')
                      ->result();
        return $query;  
    }

    public function guardar_guia_man($data)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->db->insert("detalle_manifiesto", $data);
    }

    public function eliminar_manifiesto($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->db->WHERE('id', $id);  
        $this->db->delete("encab_manifiesto");
    }

    public function eliminar_guia_manifiesto($id_guia)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->db->WHERE('id', $id_guia);  
        $this->db->delete("detalle_manifiesto");
    }
}