<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CatalogoModel extends CI_Model
{

    public function listar_clientes()
    {
        return $this->db->where('activo', 1)
            ->order_by('nombre', 'ASC')
            ->get('clientes_lm')
            ->result();
    }

    public function listar_tipos_producto()
    {
        return $this->db->where('activo', 1)
            ->order_by('nombre_legible', 'ASC')
            ->get('tipos_producto')
            ->result();
    }

    public function cliente_valido($id)
    {
        return (bool) $this->db->where('id', (int) $id)->where('activo', 1)->count_all_results('clientes_lm');
    }

    public function tipo_producto_valido($id)
    {
        return (bool) $this->db->where('id', (int) $id)->where('activo', 1)->count_all_results('tipos_producto');
    }
}
