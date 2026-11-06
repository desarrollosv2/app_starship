<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Clasificado2Model extends CI_Model
{
    public function warehouse()
    {
    }
    public function verificar_warehouse($id)
    {
       // echo $id;
       // $pro = $this->db->where('numero_warehouse', $id)
       //     ->get('warehouse')
       //     ->row();
       // return $pro;
          $pro = $this->db->where('tracking_number', $id)
            ->get('manifiesto')
            ->row();
        return $pro;
    }
    public function buscar_warehouse($id)
    {
        $query = $this->db
            ->select("es.id_estatus,es.nombre, de.fecha,es.nombre")
            ->join('detalle de', 'de.id_estatus = es.id_estatus', 'inner')
            ->where('de.warehouse', $id)
           // ->where('de.id_pais', $_SESSION['pais'])
           ->order_by('fecha DESC')
            ->get('estatus es')
            ->result();

        return $query;
    }


    public function buscar_cliente($id)
    {
        return $this->db
            ->select('*')
            ->where('casillero', $id)
            ->get('clientes')
            ->row();
    }

    /* inserta informacion de manifiesto (archivo excel) */

    public function insertar($data)
    {

        $this->db->insert('manifiesto', $data);
    }



    public function consulta_w_detalle($idwarehouse)
    {
        $query = $this->db->select('*')
            ->where('idwarehouse', $idwarehouse)
            ->get('warehouse_detalle')
            ->result();
        return $query;
    }

    public function consulta_usuario($casillero)
    {
        $query = $this->db->select('*')
            ->where('casillero', $casillero)
            ->get('usuarios')
            ->result();
        return $query;
    }



    public function insertar_facturacion($data)
    {

        $this->db->insert('poliza', $data);
    }
    public function buscarcliente($id)
    {
        $query = $this->db
            ->select("c.casillero,c.nombre, c.correo, s.nombre nombre_sucursal")
            ->join('sucursales s', 's.idsucursal = c.id_retiro', 'inner')
            ->where('c.casillero', $id)
            ->get('clientes c')
            ->row();

        return $query;
    }

    public function buscar_correo($id)
    {
        return $this->db
            ->select('*')
            ->where('casillero', $id)
            ->get('cus')
            ->row();
    }
    public function insertar_cliente($data)
    {
        $this->db->insert('clientes', $data);
    }


    public function update_precio_paquete($guia, $total)
    {
        $this->db
            ->set('total', $total)
            ->where('idwarehouse', $guia)
            ->update('warehouse_detalle');
        return ($this->db->affected_rows() > 0);
    }


    public function tarifas()
    {
        $query = $this->db->select('*')
            ->where('id_pais', $_SESSION['pais'])
            ->get('tarifas')
            ->result();
        return $query;
    }

    public function obtener_guia($id)
    {
        return $this->db
            ->select('*')
            ->where('id', $id)
            ->get('manifiesto')
            ->row();
    }

  public function update_creacion($id){
       // echo $id."--".$opc;
        $this->db
        ->set( 'fecha_creacion', date("Y-m-d H:i:s"))
        ->where( 'id', $id)
        ->update( 'manifiesto' );
    }
    public function consulta_conversacion($id)
    {

        $query = $this->db
            ->select("ma.id, ma.tracking_number,ma.consignee,re.*")
            ->join('registro_llamada  re', 're.id_tracking = ma.id', 'inner')
            ->where('ma.id', $id)
            ->order_by('fecha', 'DESC')
            ->get('manifiesto ma')
            ->result();
        return $query;
    }

    public function obtener_guia_id($id)
    {
        return $this->db
            ->select('*')
            ->where('id', $id)
            ->get('manifiesto')
            ->row();
    }

    public function guardar_item_multi($data)
    {
        $this->db->insert('manifiesto', $data);
        return $this->db->insert_id();
    }

    public function update_item_multi($id, $peso, $precio)
    {
        $this->db
            ->set('value', $precio)
            ->where('id', $id)
            ->update('manifiesto');
        return ($this->db->affected_rows() > 0);
    }

    
    public function update_tracking_id($id, $pickup_number, $internal_tracking)
    {
        // Solución al problema de visibilidad inmediata
        $this->db->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");

        $this->db
            ->set('pickup_number', $pickup_number)
            ->set('internal_tracking', $internal_tracking)
            ->where('id', $id)
            ->update('manifiesto');
        return ($this->db->affected_rows() > 0);
    }

    
    public function asignar_partida($id,$partida)
    {
        $this->db
            ->set('clasificado', 1)
            ->set('id_partida', $partida)
            ->where('id', $id)
            ->where('id_partida', 0)
            ->update('manifiesto');
        return ($this->db->affected_rows() > 0);
    }
    
    
    
    public function buscar_estatus($id, $guia)
    {
        return $this->db
            ->select('*')
            ->where('id_estatus', $id)
            ->where('warehouse', $guia)
            ->get('detalle')
            ->row();
    }

    public function guardar_estatus($data)
    {
        $this->db->insert('detalle', $data);
        return $this->db->insert_id();
    }


    //log Mail Americas
    public function guardar_logma( $data ) {

        $this->db->insert( 'log_ma', $data );
        return $this->db->insert_id();

    }
    
    public function guardar_json($datos){
         $this->db->insert( 'j_son', $datos );
        return $this->db->insert_id();
    }
    
    public function update_info($id, $dui,  $correo, $telefono, $direccion, $agencia, $tipo_entrega,  $tipo_servicio)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        $this->db
            ->set('consignee_account', $dui)
            ->set('consignee_email', $correo)
            ->set('consignee_phone', $telefono)
            ->set('consignee_address1', $direccion)
            ->set('agencia_destino', $agencia)
            ->set('tipo_entrega', $tipo_entrega)
            ->set('tipo_servicio', $tipo_servicio)
            ->where('id', $id)
            ->update('manifiesto');
        return ($this->db->affected_rows() > 0);
    }
    
    public function get_tracking()
    {
        $query = $this->db->select('*')
          ->where('id', 439)
          ->where('tracking_number', 'RP116961176MU')
          ->get('manifiesto')
         ->result();
        return $query;
    }
    
      public function reemplazar_tracking($data)
    {
        $this->db->insert("reemplazo", $data);
    }

    public function actualizar_tracking($tracking_number, $tracking_replace)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        $this->db
             ->set('tracking_replace', $tracking_replace)
             ->where('tracking_number', $tracking_number)
             ->update('manifiesto');
        return ($this->db->affected_rows() > 0);
    }

    
    public function historial($tracking_number)
    {
        $rsl =	$this->db
                     ->select('*')
                     ->where('tracking_number', $tracking_number)
                     ->get('reemplazo')
                     ->result();
        return $rsl;     
    }
    
    public function guardar_referencia($awb, $tracking_number, $referencia, $id_manifiesto ) 
    {
        $this->db
            ->set('referencia', $referencia)
            ->WHERE('tracking_number', $tracking_number)
            ->WHERE('id_manifiesto', $id_manifiesto)
            ->update('manifiesto');
        return ($this->db->affected_rows() > 0);

        //var_dump($data);
        //$this->db->insert('base_manifiesto', $data);
        //return $this->db->insert_id();
    }
    
     public function add_reem_referencia($referencia, $tracking_number)
    {
        $this->db
             ->set('referencia', $referencia)
             ->where('tracking_number', $tracking_number)
             ->update('manifiesto');
    }
    
} //fin