<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ClientesModel extends CI_Model
{
    public function consulta_cliente()
    {
        $query = $this->db
            ->select('consignee')
            ->group_by('consignee')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function busqueda_clientes($consignee_email)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $query = $this->db
            ->select('manifiesto.tracking_number,
                  manifiesto.tracking_replace,
                  manifiesto.consignee,
                  manifiesto.consignee_address1,
                  manifiesto.consignee_phone,
                  manifiesto.consignee_email,
                  consignee.comentario,
                  es.nombre')
            ->join('preclasificacion', 'preclasificacion.idpreclasificacion = manifiesto.id_manifiesto')
            ->join('consignee', 'consignee.no_doc = manifiesto.consignee_email', 'left')
            ->join('estatus es', 'es.id_estatus = manifiesto.id_estatus', 'left')
            ->where('consignee_email', $consignee_email)
            ->where('preclasificacion.tipo !=', 'TRA')
            ->group_by('manifiesto.tracking_number')
            ->order_by('manifiesto.tracking_number')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function modal_comentario_cliente($consignee_email)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $query = $this->db
            ->select('*')
            ->join('preclasificacion', 'preclasificacion.idpreclasificacion = manifiesto.id_manifiesto')

            ->where('manifiesto.consignee_email', $consignee_email)
            ->where('preclasificacion.tipo !=', 'TRA')
            ->group_by('manifiesto.consignee_email', $consignee_email)
            ->order_by('manifiesto.consignee')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function agregar_cliente($data, $correo)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);


        if ($correo) {
            $this->db->insert('consignee', $data);
            return $this->db->insert_id();
        } else {
            $this->db->where('no_doc', $correo);
            $this->db->update('consignee', $data);
            return ($this->db->affected_rows() > 0);
        }
    }

    public function guardar_comentario($data1)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->db->insert('historial', $data1);
        return $this->db->insert_id();
    }

    public function historial_cliente($consignee_account)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $query = $this->db
            ->select('*')
            ->where('no_documento', $consignee_account)
            ->get('historial')
            ->result();
        return $query;
    }

    public function actualizar_comentario_cliente($comentario, $consignee_account)
    {
        $this->db
            ->set('comentario', $comentario)
            ->where('no_doc', $consignee_account)
            ->update('consignee');
        return ($this->db->affected_rows() > 0);
    }
}
