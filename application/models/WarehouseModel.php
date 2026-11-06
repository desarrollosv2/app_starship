<?php

defined('BASEPATH') or exit('No direct script access allowed');

class WarehouseModel extends CI_Model
{
    public function warehouse() {}
    public function verificar_warehouse($id)
    {
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

    public function change_clientid($plataforma, $id_manifiesto, $tracking_number, $flag = 0)
    {
        $this->db
            ->set('client_id', $plataforma);

        if ($flag == 1) {
            $this->db->where_in('tracking_number', $tracking_number);
        }

        $this->db->where_in('id_manifiesto', $id_manifiesto)
            ->update('manifiesto');

        return ($this->db->affected_rows() > 0);
    
    }

    public function obtener_guia($id)
    {
        return $this->db
            ->select('manifiesto.*, preclasificacion.manifiesto AS preclasificacion_manifiesto,preclasificacion.fecha_arribo_miami as fecha_arribo')
            ->from('manifiesto')
            ->join('preclasificacion', 'preclasificacion.idpreclasificacion = manifiesto.id_manifiesto', 'left')
            ->where('manifiesto.id', $id)
            ->get()
            ->row();
    }

    public function datosMaster($id)
    {
        return $this->db
            ->select('*')
            ->from('preclasificacion')
            ->where('idpreclasificacion', $id)
            ->get()
            ->row();
    }



    public function update_creacion($id)
    {
        // echo $id."--".$opc;
        $this->db
            ->set('fecha_creacion', date("Y-m-d H:i:s"))
            ->set('user_name', $_SESSION["nombre"])
            ->where('id', $id)
            ->update('manifiesto');
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
            ->set('user_name', $_SESSION["nombre"])
            ->where('id', $id)
            ->update('manifiesto');
        return ($this->db->affected_rows() > 0);
    }

    public function asignar_partida($id, $partida)
    {
        $this->db
            ->set('clasificado', 1)
            ->set('id_partida', $partida)
            ->where('id', $id)
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
    public function guardar_logma($data)
    {

        $this->db->insert('log_ma', $data);
        return $this->db->insert_id();
    }

    public function guardar_json($datos)
    {
        $this->db->insert('j_son', $datos);
        return $this->db->insert_id();
    }

    public function update_cobrofinal($id,$cobro_final)
    {
        $this->db
            ->set('cobro_final', $cobro_final)
            ->where('id', $id)
            ->update('manifiesto');
        return ($this->db->affected_rows() > 0);
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

    public function get_tracking($guia)
    {
        $query = $this->db->select('tracking_number, id_empresa')

            ->where('tracking_number', $guia)
            ->get('manifiesto')
            ->row();
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
        $rsl =    $this->db
            ->select('*')
            ->where('tracking_number', $tracking_number)
            ->get('reemplazo')
            ->result();
        return $rsl;
    }

    public function guardar_referencia($awb, $tracking_number, $referencia, $id_manifiesto)
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

    public function add_reem_referencia($tracking_number, $referencia)
    {
        $this->db
            ->set('referencia', $referencia)
            ->where('tracking_number', $tracking_number)
            ->update('manifiesto');
        return ($this->db->affected_rows() > 0);
    }

    public function estatus_wh($tracking_number)
    {
        $rsl =    $this->db
            ->select('estatus.nombre')
            ->join('estatus', 'estatus.id_estatus = detalle.id_estatus')
            ->where('detalle.warehouse', $tracking_number)
            ->group_by('detalle.iddetalle')
            ->order_by('detalle.iddetalle')
            ->get('detalle')
            ->result();
        return $rsl;
    }

    public function sucursal_consulta($sucursal)
    {
        $query = $this->db
            ->select('id,
                                nombre_agencia,
                                direccion')
            ->where('id', $sucursal)
            ->get('agencias')
            ->result();
        return $query;
    }

    public function guardar_envio_track($data)
    {
        $this->db->insert('envio_tracking', $data);
        return ($this->db->affected_rows() > 0);
    }

    public function buscar_fecha_envio_guia($fecha_desde_envio, $fecha_hasta_envio)
    {
        $query = $this->db
            ->select('envio_tracking.fecha_envio,
                                envio_tracking.tracking_number,
                                agencias.nombre_agencia')
            ->join('agencias', 'agencias.id = envio_tracking.id_sucursal')
            ->where('date_format(envio_tracking.fecha_envio, "%Y-%m-%d") >=', $fecha_desde_envio)
            ->where('date_format(envio_tracking.fecha_envio, "%Y-%m-%d") <=', $fecha_hasta_envio)
            ->get('envio_tracking')
            ->result();
        return $query;
    }


    public function updateCorreo($tracking_number, $consignee_email, $consignee_phone)
    {
        $query  =   $this->db
            ->set('consignee_email', $consignee_email)
            ->set('consignee_phone', $consignee_phone)
            ->where('tracking_number', $tracking_number)
            ->update('manifiesto');
        return $query;
    }


    public function  getIdEmpresa($id)
    {
        $query = $this->db
            ->select('id_empresa')
            ->where('id', $id)
            ->get('manifiesto')
            ->row();
        return $query;
    }

    public function  getClienteID($id)
    {
        $query = $this->db
            ->select('client_id')
            ->where('id', $id)
            ->get('manifiesto')
            ->row();
        return $query;
    }

    public function obtener_token($id)
    {
        $query = $this->db
            ->select('*')
            ->where('id', $id)
            ->get('empresa')
            ->row();
        return $query;
    }


    public function  obtener_paquete_por_id($id,$id_manifiesto)
    {
        $query = $this->db
            ->select('package_number')
            ->where('id', $id)
            ->where('id_manifiesto', $id_manifiesto)
            ->get('manifiesto')
            ->row();
        return $query;
    }

    public function getTemuProductSVAll($id_manifiesto)
    {
        $this->db->select("
            id_tracking,
            items_description,
            items_quantities,
            value_unit AS cif
        ", FALSE);

        $this->db->from('detalle_items');
        $this->db->where('id_manifiesto', $id_manifiesto);
        // Agrupar por tracking y arancel
        //$this->db->group_by('id_tracking');
        $query = $this->db->get();

        return $query->result();
    }

    

    

    public function obtener_url_items($track, $id_manifiesto){

        $this->db->select('items_description, url');
        $this->db->from('detalle_items');
        $this->db->where('id_tracking', $track);
        $this->db->where('id_manifiesto', $id_manifiesto);
        
        return $this->db->get()->result();

        
    }


    public function update_direccion($tracking_number, $consignee_address1)
    {
        $query  =   $this->db
            ->set('consignee_address1', $consignee_address1)

            ->where('tracking_number', $tracking_number)
            ->update('manifiesto');
        return $query;
    }

    public function getfecha_evento($tracking, $estatus)
    {
        $query = $this->db
            ->select('fecha')
            ->where('warehouse', $tracking)
            ->where('id_estatus', $estatus)
            ->get('detalle')
            ->row();
        return $query;
    }

    public function update_fechaArribo($id_master, $fecha)
    {
        $query  =   $this->db
            ->set('fecha_arribo_miami', $fecha)
            ->where('idpreclasificacion', $id_master)
            ->update('preclasificacion');
        return $query;
    }

   
   
    public function obtener_recolecta($master)
    {
        $query = $this->db
            ->select('id,tracking_number')
            ->where('id_manifiesto', $master)
            ->where('pickup_number', '0')
            ->get('manifiesto')
            ->result();
        return $query;
    }
} //fin