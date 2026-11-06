<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Imile_Model extends CI_Model
{
    
    public function buscar_master($fecha_inicio, $fecha_fin)
    {
        $fecha_inicio .= ' 00:00:00';
        $fecha_fin    .= ' 23:59:59';

        $this->db->select('idpreclasificacion, manifiesto, fecha, descripcion');
        $this->db->from('preclasificacion');
        $this->db->where('fecha >=', $fecha_inicio);
        $this->db->where('fecha <=', $fecha_fin);
        $this->db->where('client_id', 103);

        $query = $this->db->get();
        return $query->result();
    }

    public function obtener_datos_manifiesto($id)
    {
        $this->db->select('idpreclasificacion, manifiesto');
        $this->db->from('preclasificacion');
        $this->db->where('idpreclasificacion', $id);
        
        $query = $this->db->get();
        return $query->row();
    }

    public function obtener_datos_trackings($id)
    {
        $this->db->select('id, tracking_number, scan_global, entregado, reingreso, devolucion');
        $this->db->from('manifiesto');
        $this->db->where('id_manifiesto', $id);
        
        $query = $this->db->get();
        return $query->result();
    }

    public function contar_trackings_existentes($trackings)
    {
        $this->db->where_in('tracking_number', $trackings);
        return $this->db->count_all_results('manifiesto');
    }

    public function actualizar_datos($trackings, $tipo)
    {
        if(!in_array($tipo, ['scan_global', 'entregado', 'reingreso', 'devolucion']))
        {
            return 0;
        }
        $this->db->where_in('tracking_number', $trackings);
        $this->db->update('manifiesto',[$tipo => 1]);

        return $this->db->affected_rows();
    }

    public function obtener_datos_grafica_general($fecha_inicio, $fecha_fin)
    {
        $this->db->select('pre.idpreclasificacion, pre.fecha, ma.tracking_number, ma.scan_global, ma.entregado, ma.reingreso, ma.devolucion');
        $this->db->from('preclasificacion AS pre');
        $this->db->join('manifiesto AS ma', 'ma.id_manifiesto = pre.idpreclasificacion', 'inner');
        $this->db->where('DATE(pre.fecha) >=', $fecha_inicio);
        $this->db->where('DATE(pre.fecha) <=', $fecha_fin);
        $this->db->where('pre.client_id', 103);
        $query = $this->db->get();

        return  $query->result();

    }

    public function obtener_datos_grafica($id)
    {
        $this->db->select('id, tracking_number, scan_global, entregado, reingreso, devolucion');
        $this->db->from('manifiesto');
        $this->db->where('id_manifiesto', $id);
        
        $query = $this->db->get();
        return $query->result();
    }
}