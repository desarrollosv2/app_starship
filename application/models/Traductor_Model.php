<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Traductor_Model extends CI_Model
{
    public function lista_tracking($manifiesto)
    {
        $query = $this->db
            ->select('pr.traduccion, ma.id_manifiesto, ma.tracking_number, ma.commodity')
            ->join('manifiesto ma', 'ma.id_manifiesto = pr.idpreclasificacion', 'inner')
            ->where('id_manifiesto', $manifiesto)
            ->get('preclasificacion pr')
            ->result();
        return $query;
    }

    public function actualizar_descripcion($id_manifiesto, $tracking_number, $descripcion_es)
    {
        $this->db->set('commodity', $descripcion_es);
        $this->db->where('id_manifiesto', $id_manifiesto);
        $this->db->where('tracking_number', $tracking_number);
        return $this->db->update('manifiesto');
    }
}
