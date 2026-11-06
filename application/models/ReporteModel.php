<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReporteModel extends CI_Model
{

    /**
     * Pallets y cajas agrupados por tipo de producto o por cliente.
     * Los filtros de pallet van en el LEFT JOIN para que los items del catalogo sin pallets aparezcan con 0.
     *
     * @param string $agrupar 'tipo' | 'cliente'
     * @param array  $f       estado, cliente_id, tipo_producto_id, desde, hasta
     */
    public function resumen($agrupar, $f = array())
    {
        if ($agrupar === 'cliente') {
            $tabla = 'clientes';
            $nombre = 'nombre';
            $fk = 'cliente_id';
        } else {
            $tabla = 'tipos_producto';
            $nombre = 'nombre_legible';
            $fk = 'tipo_producto_id';
        }

        list($cond, $binds) = $this->condiciones_pallet($f);
        array_unshift($cond, 'p.' . $fk . ' = g.id');

        // Si se filtra por la misma dimension que se agrupa, solo se muestra ese item
        $where = 'g.activo = 1';
        if (!empty($f[$fk])) {
            $where .= ' AND g.id = ?';
            $binds[] = (int) $f[$fk];
        }

        $sql = "SELECT g.id, g.{$nombre} AS nombre, " . $this->columnas_conteo() . "
                  FROM {$tabla} g
             LEFT JOIN pallets p ON " . implode(' AND ', $cond) . "
             LEFT JOIN pallet_cajas pc ON pc.pallet_id = p.id
                 WHERE {$where}
              GROUP BY g.id, g.{$nombre}
              ORDER BY g.{$nombre}";

        return $this->a_enteros($this->db->query($sql, $binds)->result());
    }

    /**
     * Pallets y cajas por cada combinacion cliente + tipo de producto.
     */
    public function cliente_tipo($f = array())
    {
        list($cond, $binds) = $this->condiciones_pallet($f);
        array_unshift($cond, 'p.cliente_id = c.id', 'p.tipo_producto_id = t.id');

        $where = 'c.activo = 1 AND t.activo = 1';
        if (!empty($f['cliente_id'])) {
            $where .= ' AND c.id = ?';
            $binds[] = (int) $f['cliente_id'];
        }
        if (!empty($f['tipo_producto_id'])) {
            $where .= ' AND t.id = ?';
            $binds[] = (int) $f['tipo_producto_id'];
        }

        $sql = "SELECT c.id AS cliente_id, c.nombre AS cliente, t.id AS tipo_id, t.nombre_legible AS tipo, " . $this->columnas_conteo() . "
                  FROM clientes c
            CROSS JOIN tipos_producto t
             LEFT JOIN pallets p ON " . implode(' AND ', $cond) . "
             LEFT JOIN pallet_cajas pc ON pc.pallet_id = p.id
                 WHERE {$where}
              GROUP BY c.id, c.nombre, t.id, t.nombre_legible
              ORDER BY c.nombre, t.nombre_legible";

        return $this->a_enteros($this->db->query($sql, $binds)->result());
    }

    /**
     * Condiciones sobre la tabla pallets (alias p) para usar dentro del LEFT JOIN, con sus bindings.
     */
    private function condiciones_pallet($f)
    {
        // Los pallets eliminados no cuentan en ningun reporte
        $cond = array("p.estado <> 'eliminado'");
        $binds = array();

        if (!empty($f['estado'])) {
            $cond[] = 'p.estado = ?';
            $binds[] = $f['estado'];
        }
        if (!empty($f['cliente_id'])) {
            $cond[] = 'p.cliente_id = ?';
            $binds[] = (int) $f['cliente_id'];
        }
        if (!empty($f['tipo_producto_id'])) {
            $cond[] = 'p.tipo_producto_id = ?';
            $binds[] = (int) $f['tipo_producto_id'];
        }
        if (!empty($f['desde'])) {
            $cond[] = 'p.created_at >= ?';
            $binds[] = $f['desde'] . ' 00:00:00';
        }
        if (!empty($f['hasta'])) {
            $cond[] = 'p.created_at <= ?';
            $binds[] = $f['hasta'] . ' 23:59:59';
        }
        return array($cond, $binds);
    }

    private function columnas_conteo()
    {
        return "COUNT(DISTINCT p.id) AS pallets,
                COUNT(DISTINCT CASE WHEN p.estado = 'abierto' THEN p.id END) AS pallets_abiertos,
                COUNT(DISTINCT CASE WHEN p.estado = 'cerrado' THEN p.id END) AS pallets_cerrados,
                COUNT(pc.id) AS cajas";
    }

    private function a_enteros($filas)
    {
        foreach ($filas as $r) {
            foreach (array('pallets', 'pallets_abiertos', 'pallets_cerrados', 'cajas') as $k) {
                $r->$k = (int) $r->$k;
            }
        }
        return $filas;
    }
}
