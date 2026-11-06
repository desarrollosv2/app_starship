<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Fotografias del pallet: dos posiciones fijas (1 y 2). Los archivos viven en uploads/pallets/fotos/{pallet_id}/.
 */
class PalletFotoModel extends CI_Model
{

    /**
     * @return array posicion => fila (solo las posiciones cargadas)
     */
    public function listar_por_pallet($pallet_id)
    {
        $fotos = array();
        $filas = $this->db->where('pallet_id', (int) $pallet_id)
            ->order_by('posicion', 'ASC')
            ->get('pallet_fotos')
            ->result();
        foreach ($filas as $f) {
            $fotos[(int) $f->posicion] = $f;
        }
        return $fotos;
    }

    public function contar($pallet_id)
    {
        return (int) $this->db->where('pallet_id', (int) $pallet_id)->count_all_results('pallet_fotos');
    }

    public function obtener($pallet_id, $posicion)
    {
        return $this->db->where('pallet_id', (int) $pallet_id)
            ->where('posicion', (int) $posicion)
            ->get('pallet_fotos')
            ->row();
    }

    /**
     * Guarda (o reemplaza) la foto de una posicion. Devuelve el archivo anterior para borrarlo del disco, o NULL.
     */
    public function guardar($pallet_id, $posicion, $archivo)
    {
        $anterior = $this->obtener($pallet_id, $posicion);

        if ($anterior) {
            $this->db->where('id', (int) $anterior->id);
            $this->db->set('created_at', 'NOW()', FALSE);
            $this->db->update('pallet_fotos', array('archivo' => $archivo));
            return $anterior->archivo;
        }

        $this->db->insert('pallet_fotos', array(
            'pallet_id' => (int) $pallet_id,
            'posicion'  => (int) $posicion,
            'archivo'   => $archivo,
        ));
        return NULL;
    }

    /**
     * Borra los registros de fotos del pallet. Devuelve los archivos para borrarlos del disco.
     */
    public function eliminar_por_pallet($pallet_id)
    {
        $archivos = array();
        foreach ($this->listar_por_pallet($pallet_id) as $f) {
            $archivos[] = $f->archivo;
        }
        $this->db->where('pallet_id', (int) $pallet_id)->delete('pallet_fotos');
        return $archivos;
    }
}
