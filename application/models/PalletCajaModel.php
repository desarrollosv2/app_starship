<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PalletCajaModel extends CI_Model
{

    public function listar_por_pallet($pallet_id)
    {
        return $this->db->select('id, codigo_barras, scanned_at')
            ->where('pallet_id', (int) $pallet_id)
            ->order_by('id', 'DESC')
            ->get('pallet_cajas')
            ->result();
    }

    /**
     * Busca el codigo en cualquier pallet (abierto o cerrado): el codigo de caja es unico en el sistema.
     */
    public function buscar_por_codigo($codigo_barras)
    {
        return $this->db->select('pc.id, pc.pallet_id, p.numero_pallet, p.estado, t.prefijo AS tipo_prefijo')
            ->from('pallet_cajas pc')
            ->join('pallets p', 'p.id = pc.pallet_id')
            ->join('tipos_producto t', 't.id = p.tipo_producto_id')
            ->where('pc.codigo_barras', $codigo_barras)
            ->get()
            ->row();
    }

    /**
     * Inserta la caja. Devuelve 'ok', 'duplicado' (choca con el UNIQUE global) o 'error'.
     */
    public function agregar($pallet_id, $codigo_barras)
    {
        $debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $ok = $this->db->insert('pallet_cajas', array(
            'pallet_id' => (int) $pallet_id,
            'codigo_barras' => $codigo_barras,
        ));
        $error = $this->db->error();

        $this->db->db_debug = $debug;

        if ($ok) {
            return 'ok';
        }
        return ((int) $error['code'] === 1062) ? 'duplicado' : 'error';
    }

    public function obtener($pallet_id, $caja_id)
    {
        return $this->db->where('id', (int) $caja_id)
            ->where('pallet_id', (int) $pallet_id)
            ->get('pallet_cajas')
            ->row();
    }

    public function quitar($pallet_id, $caja_id)
    {
        $this->db->where('id', (int) $caja_id)->where('pallet_id', (int) $pallet_id);
        $this->db->delete('pallet_cajas');
        return $this->db->affected_rows() > 0;
    }
}
