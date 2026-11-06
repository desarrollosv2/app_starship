<?php
 
 defined('BASEPATH') or exit('No direct script access allowed');
 
 class Traspaso_model extends CI_Model
 {

     public function guardar_traspaso($data)
     {
         $this->db->insert('preclasificacion', $data);
         return $this->db->insert_id();   
     }

     public function actualizar_estatus($wh,$estatus)
     {
     	$this->db
        ->set('estatus',$estatus)
        ->where('idwarehouse', $wh)
        ->update('warehouse_detalle');

        return ($this->db->affected_rows()>0);
     }

     public function consulta_guias($id)
     {
         $rsl =	$this->db
              ->select('*')
              ->where('manifiesto', $id)
              ->get('warehouse')
              ->result();
         return $rsl;     
     }
     public function validar_wh($wh,$idwh)
     {
              $this->db
              ->select('*')
              ->where('manifiesto', $idwh)
              ->where('numero_warehouse', $wh)
              ->get('warehouse')
              ->result();
        return ($this->db->affected_rows());
     }
     
     public function select_estatus($estatus)
     {
        $rsl =  $this->db
              ->select('*')
              ->where('id_estatus',$estatus)
              ->get('estatus')
              ->row();
         return $rsl; 
     }
     public function ws_detalle($id){
        $query = $this->db
        ->select('wd.idwarehouse,wd.tracking,po.total,wd.peso,wd.bultos')
        ->join('warehouse_detalle wd' , 'wd.idwarehouse = po.guia' , 'inner')
        ->where('wd.idwarehouse', $id)
        ->get('poliza po');

        return $query->row();
     }
   
   
 }



