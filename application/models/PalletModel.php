<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PalletModel extends CI_Model
{

    public function get_with_relations($id)
    {
        return $this->db->select('p.*, c.nombre AS cliente_nombre, t.nombre_legible AS tipo_legible, t.prefijo AS tipo_prefijo')
            ->from('pallets p')
            ->join('clientes_lm c', 'c.id = p.cliente_id')
            ->join('tipos_producto t', 't.id = p.tipo_producto_id')
            ->where('p.id', (int) $id)
            ->get()
            ->row();
    }

    /**
     * Pallet por su numero (el que va en el codigo de barras de la etiqueta PDF).
     */
    public function get_por_codigo($prefijo, $numero_pallet)
    {
        $row = $this->db->select('p.id')
            ->from('pallets p')
            ->join('tipos_producto t', 't.id = p.tipo_producto_id')
            ->where('t.prefijo', strtoupper($prefijo))
            ->where('p.numero_pallet', (int) $numero_pallet)
            ->get()
            ->row();

        return $row ? $this->get_with_relations($row->id) : NULL;
    }

    /**
     * Pallets con ese numero en cualquier tipo. Para etiquetas impresas antes del correlativo por tipo,
     * cuyo codigo de barras solo tiene el numero.
     */
    public function listar_por_numero($numero_pallet)
    {
        $filas = $this->db->select('id')
            ->where('numero_pallet', (int) $numero_pallet)
            ->where('estado !=', 'eliminado')
            ->get('pallets')
            ->result();

        $pallets = array();
        foreach ($filas as $f) {
            $pallets[] = $this->get_with_relations($f->id);
        }
        return $pallets;
    }

    /**
     * Pallet abierto para la misma combinacion cliente + tipo de producto, si existe.
     */
    public function buscar_abierto($cliente_id, $tipo_producto_id)
    {
        $row = $this->db->select('id')
            ->where('cliente_id', (int) $cliente_id)
            ->where('tipo_producto_id', (int) $tipo_producto_id)
            ->where('estado', 'abierto')
            ->limit(1)
            ->get('pallets')
            ->row();

        return $row ? $this->get_with_relations($row->id) : NULL;
    }

    /**
     * Crea un pallet con numero correlativo. Devuelve array('pallet' => obj, 'existente' => bool)
     * o FALSE si falla. Si ya hay uno abierto para la combinacion, lo devuelve sin crear otro.
     */
    
    /**
     * Bloquea la fila del pallet hasta el fin de la transaccion en curso.
     */
    public function bloquear($id)
    {
        return $this->db->query('SELECT * FROM pallets WHERE id = ? FOR UPDATE', array((int) $id))->row();
    }

    public function contar_cajas($pallet_id)
    {
        return (int) $this->db->where('pallet_id', (int) $pallet_id)->count_all_results('pallet_cajas');
    }

    public function registrar_consentimiento($pallet_id, $ip)
    {
        $this->db->where('id', (int) $pallet_id)->where('estado', 'abierto');
        $this->db->set('consentimiento_at', 'NOW()', FALSE);
        $this->db->update('pallets', array(
            'excede_limite' => 1,
            'consentimiento_ip' => substr((string) $ip, 0, 45),
        ));
        return $this->db->affected_rows() > 0;
    }

    public function cerrar($pallet_id)
    {
        $this->db->where('id', (int) $pallet_id)->where('estado', 'abierto');
        // Hora de MySQL, igual que created_at
        $this->db->set('cerrado_at', 'NOW()', FALSE);
        $this->db->update('pallets', array('estado' => 'cerrado'));
        return $this->db->affected_rows() > 0;
    }

    /**
     * TRUE si el pallet fue creado hoy (hora de MySQL, igual que created_at). Solo esos se pueden reiniciar o eliminar.
     */
    public function es_del_dia($pallet_id)
    {
        $row = $this->db->query('SELECT DATE(created_at) = CURDATE() AS hoy FROM pallets WHERE id = ?', array((int) $pallet_id))->row();
        return $row && (int) $row->hoy === 1;
    }

    /**
     * Codigos de caja del pallet (para dejarlos en el log antes de borrarlos).
     */
    public function codigos_cajas($pallet_id)
    {
        $filas = $this->db->select('codigo_barras')
            ->where('pallet_id', (int) $pallet_id)
            ->order_by('id', 'ASC')
            ->get('pallet_cajas')
            ->result();
        return array_column($filas, 'codigo_barras');
    }

    /**
     * Deja el pallet vacio y abierto, con el mismo numero. Las fotos se borran desde el controlador.
     */
    public function reiniciar($pallet_id)
    {
        $this->db->trans_begin();

        $this->bloquear($pallet_id);
        $this->db->where('pallet_id', (int) $pallet_id)->delete('pallet_cajas');
        $this->db->where('id', (int) $pallet_id)->update('pallets', array(
            'estado'            => 'abierto',
            'cerrado_at'        => NULL,
            'excede_limite'     => 0,
            'consentimiento_at' => NULL,
            'consentimiento_ip' => NULL,
        ));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }
        $this->db->trans_commit();
        return TRUE;
    }

    /**
     * Borrado logico: el pallet queda como 'eliminado' (fuera de listados y reportes).
     * Sus cajas se borran para liberar los codigos y poder escanearlos en otro pallet; las fotos se conservan.
     */
    public function eliminar($pallet_id)
    {
        $this->db->trans_begin();

        $this->bloquear($pallet_id);
        $this->db->where('pallet_id', (int) $pallet_id)->delete('pallet_cajas');
        $this->db->where('id', (int) $pallet_id);
        $this->db->set('eliminado_at', 'NOW()', FALSE);
        $this->db->update('pallets', array('estado' => 'eliminado'));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }
        $this->db->trans_commit();
        return TRUE;
    }

    /**
     * Listado de pallets con su cantidad de cajas (sin los eliminados).
     * Filtros: estado, cliente_id, tipo_producto_id, desde, hasta (Y-m-d sobre created_at).
     */
    public function listar($filtros = array(), $limite = 500)
    {
        $this->db->select('p.id, p.numero_pallet, p.estado, p.max_cajas, p.excede_limite, p.created_at, p.cerrado_at,
                c.nombre AS cliente_nombre, t.nombre_legible AS tipo_legible, COUNT(pc.id) AS total_cajas,
                (DATE(p.created_at) = CURDATE()) AS editable_hoy,
                (SELECT COUNT(*) FROM pallet_fotos pf WHERE pf.pallet_id = p.id) AS total_fotos', FALSE)
            ->from('pallets p')
            ->join('clientes_lm c', 'c.id = p.cliente_id')
            ->join('tipos_producto t', 't.id = p.tipo_producto_id')
            ->join('pallet_cajas pc', 'pc.pallet_id = p.id', 'left');

        $this->aplicar_filtros($filtros);

        return $this->db->group_by('p.id')
            ->order_by('p.numero_pallet', 'DESC')
            ->limit((int) $limite)
            ->get()
            ->result();
    }

    private function aplicar_filtros($f)
    {
        $this->db->where('p.estado !=', 'eliminado');

        if (!empty($f['estado'])) {
            $this->db->where('p.estado', $f['estado']);
        }
        if (!empty($f['cliente_id'])) {
            $this->db->where('p.cliente_id', (int) $f['cliente_id']);
        }
        if (!empty($f['tipo_producto_id'])) {
            $this->db->where('p.tipo_producto_id', (int) $f['tipo_producto_id']);
        }
        if (!empty($f['desde'])) {
            $this->db->where('p.created_at >=', $f['desde'] . ' 00:00:00');
        }
        if (!empty($f['hasta'])) {
            $this->db->where('p.created_at <=', $f['hasta'] . ' 23:59:59');
        }
    }

    /**
     * Bitacora de acciones sobre pallets (creacion, consentimiento, cierre, cajas quitadas).
     */
    public function insert_log($data)
    {
        return $this->db->insert('log_pallets', $data);
    }

    public function crear($cliente_id, $tipo_producto_id)
    {
        $this->db->trans_begin();

        // Correlativo por tipo de producto (incluye eliminados para no reutilizar numeros)
        $row = $this->db->query(
            'SELECT COALESCE(MAX(numero_pallet), 0) AS max_num FROM pallets WHERE tipo_producto_id = ? FOR UPDATE',
            array((int) $tipo_producto_id)
        )->row();

        $existente = $this->buscar_abierto($cliente_id, $tipo_producto_id);
        if ($existente) {
            $this->db->trans_rollback();
            return array('pallet' => $existente, 'existente' => TRUE);
        }

        $this->db->insert('pallets', array(
            'numero_pallet' => (int) $row->max_num + 1,
            'cliente_id' => (int) $cliente_id,
            'tipo_producto_id' => (int) $tipo_producto_id,
            'max_cajas' => (int) $this->config->item('max_cajas_default'),
            'estado' => 'abierto',
        ));
        $id = (int) $this->db->insert_id();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }
        $this->db->trans_commit();

        return array('pallet' => $this->get_with_relations($id), 'existente' => FALSE);
    }
}
