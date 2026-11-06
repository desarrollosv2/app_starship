<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pw_model extends CI_Model
{

    public function verificar_cuenta($nit)
    {
        $query = $this->db
            ->select('casillero,nit')
            ->where('nit', $nit)
            ->get('clientes')
            ->row();
        // $row=$query->custom_row_object(0,'Casillero_model');

        return $query;
    }

    public function find_tracking_unique_by_prefix($id_manifiesto, $tracking_prefix)
    {
        $q = $this->db->select('tracking_number')
            ->from('manifiesto')
            ->where('id_manifiesto', $id_manifiesto)
            ->like('tracking_number', $tracking_prefix, 'after')
            ->limit(2)
            ->get();

        $rows = $q->result_array();

        if (count($rows) === 0)  return null;
        if (count($rows) > 1)    return ['ambiguous' => true];

        return ['tracking_number' => $rows[0]['tracking_number']];
    }

    public function update_impuesto_by_exact_tracking($id_manifiesto, $tracking_exact, $dai = null, $iva = null)
    {
        // Setea solo si viene la clave (permite null si lo envías explícito)
        if (func_num_args() >= 3) $this->db->set('dai', $dai);
        if (func_num_args() >= 4) $this->db->set('iva', $iva);

        $this->db->where('id_manifiesto', $id_manifiesto)
            ->where('tracking_number', $tracking_exact)
            ->update('manifiesto');

        return $this->db->affected_rows();
    }

    public function verificarCliente($id)
    {
        $query = $this->db
            ->select('client_id,id_pais')
            ->where('idpreclasificacion', $id)
            ->get('preclasificacion')
            ->row();
        // $row=$query->custom_row_object(0,'Casillero_model');

        return $query;
    }
}

/* End of file ModelName.php */
