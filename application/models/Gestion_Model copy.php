<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Gestion_Model extends CI_Model
{
    public function buscar_trackings($master, $bag_number)
    {
        $this->db->select('bag_number, tracking_number, pickup_number, internal_tracking');
        $this->db->from('manifiesto');
        $this->db->where('id_manifiesto', $master);
        $this->db->where('bag_number', $bag_number);

        $this->db->order_by('pickup_number', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }

    public function obtener_bolsa($master, $bag_number,)
    {
        $query = $this->db
            ->select('id, bag_number, tracking_number, pickup_number, internal_tracking,
            tipo_entrega, 
            consignee_account, 
            consignee, 
            consignee_email, 
            consignee_address1,
            consignee_address2, 
            consignee_address3,
            consignee_phone,
            tipo_servicio,
            cobro_final,
            agencia_destino,
            departamento_id,
            municipio_id,
            gweight,
            commodity,
            iva,
            dai,estado')
            ->where('bag_number', $bag_number)
            ->where('id_manifiesto',  $master)
            ->where('estado',  "V")
            ->order_by('fecha', 'DESC')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    //toma el id de empresa y token para gestionar apis
    public function get_Idempresa($master)
    {
        $query = $this->db->select('client_id,tipo_factura')
            ->where('idpreclasificacion', $master)
            ->get('preclasificacion')
            ->result();
        return $query;
    }

    public function datos_empresa($id)
    {
        $query = $this->db->select('id, token_cliente, id_tokencliente, token')
            ->where('id', $id)
            ->get('empresa')
            ->result();
        return $query;
    }

    public function push_events($empresa)
    {
        $query = $this->db->select('id,tracking_number')
            ->where('id_empresa', $empresa)
            ->where('id >', 1425574)  // ✅ CORRECTO

            ->get('manifiesto')
            ->result();
        return $query;
    }
    public function get_events($tracking)
    {
        $query = $this->db->select('detalle.*, estatus.nombre as nombre_estatus')
            ->from('detalle')
            ->join('estatus', 'estatus.id_estatus = detalle.id_estatus', 'left')
            ->where('detalle.warehouse', $tracking)

            ->get()
            ->result();

        return $query;
    }
    public function get_events_error()
    {

        $this->db->select('log_entregas.*, estatus.nombre');

        $this->db->from('log_entregas');


        $this->db->join('estatus', 'log_entregas.id_estatus = estatus.id_estatus');


        $this->db->where('log_entregas.estado', 'error');
        $this->db->where('log_entregas.response', null);
        $this->db->where('entregado', "NO");


        $query = $this->db->get();


        return $query->result();
    }

    public function actualizar_respuesta($response, $tracking)
    {
        $this->db
            ->set('response', $response)
            ->where('tracking', $tracking)
            ->update('log_entregas');
    }

    public function validate_events()
    {

        $this->db->select('*');

        $this->db->from('log_entregas');

        $this->db->where('log_entregas.estado', 'error');

        $query = $this->db->get();

        return $query->result();
    }

    public function get_validate_events($tracking, $estatus)
    {

        $this->db->select('*');

        $this->db->from('log_entregas');

        $this->db->where('tracking', $tracking);

        $this->db->where('id_estatus', $estatus);

        $this->db->where('entregado', "NO");


        $query = $this->db->get();

        return $query->result();
    }

    public function ok_events($tracking, $estatus)
    {
        $this->db
            ->set('entregado', "OK")
            ->where('tracking', $tracking)
            ->where('id_estatus', $estatus)
            ->update('log_entregas');
    }
}
