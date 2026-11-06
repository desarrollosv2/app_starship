<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Customers_model extends CI_Model
{
    public function listado_customer($casillero)
    {
        $query = $this->db->select('*')
        ->where('cuenta', $casillero)
        ->order_by('idwarehouse','DESC')
        ->get('warehouse_detalle')
        ->result();
        return $query;

        /*
  $query = $this->db->select('w.*,d.warehouse')
        ->where('w.cuenta', $casillero)
        ->join('detalle d', 'd.warehouse = w.idwarehouse', 'inner')
        ->join('estatus e', 'e.id_estatus = d.id_estatus', 'inner')
        ->get('warehouse_detalle w')
        ->result();
        return $query;

        */

  /*
        return $this->db
        ->select('wd.*, de.casillero')
        ->where('wd.casillero', $casillero)
        ->join('detalle de', 'de.casillero = wd.cuenta', 'inner')
        ->get('warehouse_detalle wd')
        ->result();

*/
    }

    public function verificar_tracking($casillero)
    {
         $query = $this->db->select('*')
        ->where('casillero', $casillero)
        ->get('pre_alerta')
        ->result();
        return $query;
    }
    
    public function ultimo_estado($ws)
    {
        $query = $this->db->select('d.*, e.*, e.nombre nombre_estatus')
          ->where('d.warehouse', $ws)
          ->join('estatus e', 'e.id_estatus = d.id_estatus', 'inner')
          ->order_by('d.fecha', 'desc')
          ->limit('1')
          ->get('detalle d')
          ->row();
        return $query;
    }
    public function ultimo_estatus($casillero)
    {
        $query = $this->db->select('w.*, d.warehouse, e.nombre nombre_estatus')
        ->join('detalle d', 'd.warehouse = w.idwarehouse', 'left')
        ->join('estatus e', 'e.id_estatus = d.id_estatus', 'left')
        ->where('w.cuenta', $casillero)
        ->select_max('d.fecha')
        ->group_by('w.idwarehouse')
        ->get('warehouse_detalle w')
        ->result();

        return $query;
    }

    public function guardar_password($data)
    {
        $this->db
        ->set('clave', $data['clave'])
        ->where('id', $data['id'])
        ->update('usuarios');
        return ($this->db->affected_rows() > 0);
    }
    
    public function guardar_password2($data)
    {
        $this->db
        ->set('clave', $data['clave'])
        ->where('id', $data['id'])
        ->update('usuarios');
        return ($this->db->affected_rows() > 0);
    }


    public function guardar_email($data)
    {
        $this->db
        ->set('correo', $data['correo'])
        ->where('id', $data['id'])
        ->update('usuarios');
        return ($this->db->affected_rows() > 0);
    }

    
    public function guardar_sucursal($data)
    {
        $this->db
        ->set('id_retiro', $data['id_retiro'])
        ->where('casillero', $data['casillero'])
        ->update('clientes');
        return ($this->db->affected_rows() > 0);
    }

    public function lista_prealerta($casillero)
    {
        $query = $this->db->select('*')
        ->where('casillero', $casillero)
        ->get('pre_alerta')
        ->result();
        return $query;
    }

    public function listado_prealerta_all(){
        $query = $this->db
        ->select('pa.*, co.id_courier, co.descripcion nombre_courier')
        ->join('courier co', 'co.id_courier = pa.id_courier', 'inner')
        ->where('pa.estado', 0)
        ->get('pre_alerta pa')
        ->result();
        return $query;
    }
    public function listado_prealerta($casillero)
    {
        // $casillero="$casillero";
        //echo  $casillero;
     /*   $query = $this->db->select('*')
         ->where('casillero', $casillero)
         ->get('pre_alerta')
         ->result();
        return $query;*/


        $query = $this->db
        ->select('pa.*, co.id_courier, co.descripcion nombre_courier')
        ->join('courier co', 'co.id_courier = pa.id_courier', 'inner')
        ->where('pa.casillero', $casillero)
        ->get('pre_alerta pa')
        ->result();
        return $query;
        
    }
 
 
    public function consulta_prealerta($id)
    {
        $query = $this->db->select('*')
         ->where('id_prealert', $id)
         ->get('pre_alerta')
         ->result();
        return $query;
    }

    
    public function guardar_prealerta_modal($id,$data)
    {
        if ($id) {
            $this->db->where('id_prealert', $id);
            $this->db->update('pre_alerta', $data);
            return ($this->db->affected_rows() > 0);
        } else {
            $this->db->insert('pre_alerta', $data);
            return ($this->db->affected_rows() > 0);
        }
    }


    public function busqueda_prealerta($campo,$busqueda)
    {
        $query = $this->db
        ->select('pa.*, co.id_courier, co.descripcion nombre_courier')
        ->join('courier co', 'co.id_courier = pa.id_courier', 'inner')
        ->where('pa.'.$campo, $busqueda)
        ->get('pre_alerta pa')
        ->result();
        return $query;   
    }


    public function confirmar_prealerta($id){
        
            $this->db->set('estado',1);
            $this->db->where('id_prealert', $id);
            $this->db->update('pre_alerta');
            return ($this->db->affected_rows() > 0);

    }

public function consulta_estatus($id){
  /*  $query = $this->db->select('*')
         ->where('warehouse', $id)
         ->get('detalle')
         ->result();
        return $query;

*/
        
        $query = $this->db->select('d.*, e.*, e.nombre nombre_estatus')
          ->where('d.warehouse', $id)
          ->join('estatus e', 'e.id_estatus = d.id_estatus', 'inner')
          ->order_by('d.fecha', 'desc')
          ->get('detalle d')
         ->result();
        return $query;
}





}

/* End of file ModelName.php */
