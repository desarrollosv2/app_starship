<?php

defined('BASEPATH') or exit('No direct script access allowed');

class POD_Model extends CI_Model
{

    public function lista_empresas()
    {
        $query = $this->db
            ->select('*')
            ->from('empresa')
            ->get()
            ->result();

        return $query;
    }

    public function listar_provedores_lm()
    {
        $query = $this->db
            ->select('*')
            ->from('proveedores_lm')
            ->get()
            ->result();
        return $query;
    }

    public function lista_pilotos($id_pais)
    {
        $query = $this->db
            ->select('*')
            ->from('pilotos')
            ->where('id_pais', $id_pais)
            ->get()
            ->result();

        return $query;
    }

    public function lista_pod($id_pais)
    {
        $query = $this->db
            ->select('ep.*, pre.manifiesto, emp.nombre AS nombre_empresa, pi.nombre AS piloto, pro.nombre AS proveedor')
            ->from('encabezado_pod AS ep')
            ->join('preclasificacion AS pre', 'pre.idpreclasificacion = ep.idpreclasificacion')
            ->join('empresa AS emp', 'emp.id = ep.cliente_id')
            ->join('pilotos AS pi', 'pi.id = ep.id_piloto')
            ->join('proveedores_lm AS pro', 'pro.id = ep.proveedor_lm', 'left')
            ->where('ep.estado', 1)
            ->where('ep.id_pais', $id_pais)
            ->order_by('fecha_creada', 'DESC')
            ->get()
            ->result();
        return $query;
    }

    public function buscar_manifiesto_pod($id_pais, $desde, $hasta, $empresa, $proveedor_lm)
    {
        $this->db
            ->select('pre.idpreclasificacion, pre.manifiesto, pre.client_id, pro.nombre AS proveedor_lm')
            ->from('preclasificacion AS pre')
            ->join('proveedores_lm AS pro', 'pro.id = pre.id_proveedor_lm', 'left')
            ->where('pre.id_pais', $id_pais)
            ->where('pre.fecha >=', $desde . ' 00:00:00')
            ->where('pre.fecha <=', $hasta . ' 23:59:59')
            ->where('pre.client_id', $empresa)
            ->where('pre.id_proveedor_lm', $proveedor_lm)
            ->where('estado_pod !=', 2);

            if ($empresa != 101) {
                $this->db->where('(pre.manifiesto_tempo IS NOT NULL AND pre.manifiesto_tempo != "")', null, false);
            }
            
        return $this->db->get()->result();
    }

    public function detalle_manifiesto($idpreclasificacion)
    {
        $query = $this->db
            ->select('id_manifiesto AS idpreclasificacion, bag_number, COUNT(*) AS paquetes')
            ->from('manifiesto')
            ->where_in('id_manifiesto', $idpreclasificacion)
            ->group_by(['id_manifiesto', 'bag_number'])
            ->get()
            ->result();
        return $query;
    }

    public function detalle_manifiesto_honduras($id_manifiesto, $referencia)
    {
        return $this->db
            ->select([
                'referencia',
                'GROUP_CONCAT(DISTINCT bag_number ORDER BY bag_number SEPARATOR ", ") AS bag_number',
                'COUNT(*) AS paquetes',
                'SUM(items) AS total_items',
                'COUNT(DISTINCT bag_number) AS total_bag_number'
            ])
            ->from('manifiesto')
            ->where('id_manifiesto', $id_manifiesto)
            ->where('referencia', $referencia)
            ->group_by('referencia')
            ->get()
            ->result();
    }



    public function obtener_referencia($idpreclasificacion)
    {
        return $this->db
            ->select('referencia')
            ->from('declaraciones')
            ->where('id_manifiesto', $idpreclasificacion)
            ->get()
            ->result();

        return $query;
    }

    public function total_paquetes_manifiesto($idpre)
    {
        return $this->db
            ->select('COUNT(*) AS total_paquetes')
            ->from('manifiesto')
            ->where('id_manifiesto', $idpre)
            ->get()
            ->row()
            ->total_paquetes;
    }

    public function total_referencias_manifiesto($idpre)
    {
        return $this->db
            ->select('COUNT(DISTINCT referencia) AS total_referencias_master')
            ->from('manifiesto')
            ->where('id_manifiesto', $idpre)
            ->get()
            ->row()
            ->total_referencias_master;
    }

    public function total_bags_manifiesto($idpreclasificacion)
    {
        return $this->db
            ->select('COUNT(DISTINCT bag_number) AS total_bags')
            ->from('manifiesto')
            ->where_in('id_manifiesto', $idpreclasificacion)
            ->get()
            ->row()
            ->total_bags;
    }

    public function pod_entregadas($idpre)
    {
        return $this->db
            ->select('
            COALESCE(SUM(numero_cajas), 0) AS total_cajas,
            COALESCE(SUM(numero_paquetes), 0) AS total_paquetes')
            ->from('encabezado_pod')
            ->where('idpreclasificacion', $idpre)
            ->where('estado', 1)
            ->get()
            ->row();
    }

    public function obtener_data_manifiesto($ids)
    {
        $query = $this->db
            ->select('idpreclasificacion, id_proveedor_lm, client_id, manifiesto_tempo, id_pais')
            ->from('preclasificacion')
            ->where_in('idpreclasificacion', $ids)
            ->get()
            ->result();
        return $query;
    }

    public function insertar_pod($data)
    {
        $inserted_ids = [];


        foreach ($data as $row) {
            $this->db->insert('encabezado_pod', $row);
            $inserted_ids[] = $this->db->insert_id();
        }


        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $inserted_ids;
    }

    /*public function insertar_detalle_pod($data)
    {
        $this->db->insert_batch('detalle_pod', $data);
    }*/

    public function insertar_detalle_pod($data)
    {
        foreach ($data as $row) {

            // Normalizar tipos (defensivo)
            $row['numero_paquetes'] = (int) $row['numero_paquetes'];
            $row['numero_cajas']    = (int) $row['numero_cajas'];
            $row['estado']          = (int) $row['estado'];

            if (!$this->db->insert('detalle_pod', $row)) {
                log_message('error', 'ERROR insert detalle_pod');
                log_message('error', print_r($this->db->error(), true));
                log_message('error', json_encode($row));
                return false;
            }
        }
        return true;
    }

    public function obtener_master_pod($ids_pod)
    {
        $query = $this->db
            ->select('ep.id, pre.manifiesto')
            ->from('encabezado_pod AS ep')
            ->join('preclasificacion AS pre', 'pre.idpreclasificacion = ep.idpreclasificacion')
            ->where_in('ep.id', $ids_pod)
            ->get()
            ->result();
        return $query;
    }

    public function obtener_encabezado_pod($id_pod)
    {
        $query = $this->db
            ->select('ep.*, pre.manifiesto, pre.manifiesto_tempo, emp.nombre AS nombre_empresa, pi.nombre AS piloto, pro.nombre AS proveedor')
            ->from('encabezado_pod AS ep')
            ->join('preclasificacion AS pre', 'pre.idpreclasificacion = ep.idpreclasificacion')
            ->join('empresa AS emp', 'emp.id = ep.cliente_id')
            ->join('pilotos AS pi', 'pi.id = ep.id_piloto')
            ->join('proveedores_lm AS pro', 'pro.id = ep.proveedor_lm', 'left')
            ->where('ep.id', $id_pod)
            ->get()
            ->row();
        return $query;
    }

    public function obtener_detalle_pod($id_pod)
    {
        $query = $this->db
            ->select('dp.*, pre.manifiesto, est.nombre AS estatus')
            ->from('detalle_pod AS dp')
            ->join('preclasificacion AS pre', 'pre.idpreclasificacion = dp.idpreclasificacion')
            ->join('estatus_pod AS est', 'est.id = dp.estado')
            ->where_in('dp.id_pod', $id_pod)
            ->order_by('dp.id_pod')
            ->group_by('dp.bag_number')
            ->get()
            ->result();
        return $query;
    }

    public function actualizar_totales_encabezado($id_pod)
    {
        $totales = $this->db
            ->select('
                SUM(numero_paquetes) AS total_paquetes,
                SUM(numero_cajas) AS total_cajas,
                COUNT(referencia) AS total_referencias
            ')
            ->from('detalle_pod')
            ->where('id_pod', $id_pod)
            ->get()
            ->row();

        if ($totales) {
            $this->db
                ->where('id', $id_pod)
                ->update('encabezado_pod', [
                    'numero_paquetes' => $totales->total_paquetes ?? 0,
                    'numero_cajas'    => $totales->total_cajas ?? 0,
                    'total_referencias' => $totales->total_referencias ?? 0
                ]);
        }
    }

    public function excluir_caja($id_detalle, $observacion)
    {
        $this->db
            ->set('estado', 2)
            ->set('observacion', $observacion)
            ->where('id', $id_detalle)
            ->update('detalle_pod');
        return $this->db->affected_rows() > 0;
    }

    public function bandera_caja_pendiente($id_pre)
    {
        $this->db
            ->set('cajas_pendiente', 1)
            ->where('idpreclasificacion', $id_pre)
            ->update('preclasificacion');
        return $this->db->affected_rows() > 0;
    }

    public function seleccionar_detalle_pod($id_detalle)
    {
        $query = $this->db
            ->select('numero_cajas, numero_paquetes')
            ->from('detalle_pod')
            ->where('id', $id_detalle)
            ->get()
            ->row();
        return $query;
    }


    public function seleccionar_pod($id_pod)
    {
        $query = $this->db
            ->select('numero_cajas, numero_paquetes, total_referencias')
            ->from('encabezado_pod')
            ->where('id', $id_pod)
            ->get()
            ->row();
        return $query;
    }

    public function actualizar_totales_pod($id_pod, $new_cajas, $new_paquetes, $new_referencias)
    {
        $this->db
            ->set('numero_cajas', $new_cajas)
            ->set('numero_paquetes', $new_paquetes)
            ->set('total_referencias', $new_referencias)
            ->where('id', $id_pod)
            ->update('encabezado_pod');
        return $this->db->affected_rows() > 0;
    }

    public function incluir_caja($id_detalle)
    {
        $this->db
            ->set('estado', 1)
            ->set('observacion', null)
            ->where('id', $id_detalle)
            ->update('detalle_pod');
        return $this->db->affected_rows() > 0;
    }

    public function contar_bag_manifiesto($id_pre)
    {
        return $this->db
            ->select('COUNT(DISTINCT bag_number) AS total')
            ->from('manifiesto')
            ->where('id_manifiesto', $id_pre)
            ->get()
            ->row()
            ->total;
    }

    public function contar_referencias_honduras($id_pre)
    {
        return $this->db
            ->select('COUNT(DISTINCT referencia) AS total')
            ->from('declaraciones')
            ->where('id_manifiesto', $id_pre)
            ->get()
            ->row()
            ->total;
    }

    public function contar_refeencias_detalle_pod_activos_honduras($id_pre)
    {
        return $this->db
            ->select('COUNT(DISTINCT referencia) AS total')
            ->from('detalle_pod')
            ->where('idpreclasificacion', $id_pre)
            ->where('estado', 1)
            ->get()
            ->row()
            ->total;
    }

    public function contar_bag_detalle_pod_activos($id_pre)
    {
        return $this->db
            ->select('COUNT(DISTINCT bag_number) AS total')
            ->from('detalle_pod')
            ->where('idpreclasificacion', $id_pre)
            ->where('estado', 1)
            ->get()
            ->row()
            ->total;
    }

    public function actualizar_cajas_pendientes($id_pre, $estado)
    {
        return $this->db
            ->where('idpreclasificacion', $id_pre)
            ->update('preclasificacion', [
                'estado_pod' => $estado
            ]);
    }

    public function obtener_estado_pod($ids)
    {
        $query = $this->db
            ->select('idpreclasificacion, estado_pod')
            ->from('preclasificacion')
            ->where_in('idpreclasificacion', $ids)
            ->get()
            ->result();
        return $query;
    }

    public function obtener_bags_estado_2($ids)
    {
        $query = $this->db
            ->from('detalle_pod')
            ->where_in('idpreclasificacion', $ids)
            ->where_in('estado', [2, 5])
            ->get()
            ->result();
        return $query;
    }

    public function actualizar_bags_a_estado_3($ids)
    {
        $query = $this->db
            ->where_in('idpreclasificacion', $ids)
            ->where_in('estado', [2, 5])
            ->update('detalle_pod', ['estado' => 3]);
        return $query;
    }

    public function actualizar_bags_a_estado_5($ids)
    {
        $query = $this->db
            ->where_in('idpreclasificacion', $ids)
            ->where_in('estado', [2, 5])
            ->update('detalle_pod', ['estado' => 5]);
        return $query;
    }

    public function obtener_detalle_pod_pdf($id_pod)
    {
        $query = $this->db
            ->select('*')
            ->from('detalle_pod')
            ->where('id_pod', $id_pod)
            ->where('estado', 1)
            ->get()
            ->result();
        return $query;
    }

    public function obtener_totales_por_preclasificacion($idpreclasificacion)
    {
        return $this->db
            ->select('
                COUNT(DISTINCT bag_number) AS total_cajas,
                COUNT(bag_number) AS total_paquetes
            ')
            ->from('manifiesto')
            ->where('id_manifiesto', $idpreclasificacion)
            ->get()
            ->row();
    }

    public function obtener_estado_cajas_por_preclasificacion($idpreclasificacion)
    {
        return $this->db
            ->select('
                COUNT(DISTINCT CASE WHEN estado = 1 THEN bag_number END) AS procesadas,
                COUNT(DISTINCT CASE WHEN estado IN (2,5) THEN bag_number END) AS pendientes
            ')
            ->from('detalle_pod')
            ->where('idpreclasificacion', $idpreclasificacion)
            ->get()
            ->row();
    }

    public function actualizar_encabezado_pod($id_pod, $data)
    {
        $this->db
            ->where('id', $id_pod)
            ->update('encabezado_pod', $data);
        return $this->db->affected_rows() > 0;
    }

    public function ver_comentario_reproceso($id)
    {
        $query = $this->db
            ->select('*')
            ->from('detalle_pod')
            ->where('id', $id)
            ->get()
            ->row();
        return $query;
    }

    public function anular_pod($id_pod, $comentario)
    {
        $this->db
            ->set('motivo_anulacion', $comentario)
            ->set('estado', 0)
            ->where('id', $id_pod)
            ->update('encabezado_pod');
        return $this->db->affected_rows() > 0;
    }

    public function anular_bag_number($id_pod)
    {
        $this->db
            ->set('estado', 5)
            ->where('id_pod', $id_pod)
            ->update('detalle_pod');
        return $this->db->affected_rows() > 0;
    }

    public function insertar_piloto($data)
    {
        $this->db->insert('pilotos', $data);
        return $this->db->insert_id();
    }

    public function guardar_comprobantes_pod($data)
    {
        $this->db->insert('comprobantes_pod', $data);
    }

    public function listar_comprobantes_pod($id)
    {
        $query = $this->db
            ->select('*')
            ->from('comprobantes_pod')
            ->where('id_pod', $id)
            ->where('estado', 1)
            ->get()
            ->result();
        return $query;
    }

    public function estatus_pod($id_pod, $estatus)
    {
        $this->db
            ->set('estatus_pod', $estatus)
            ->where('id', $id_pod)
            ->update('encabezado_pod');
        return $this->db->affected_rows() > 0;
    }

    public function eliminar_comprobante_pod($id)
    {
        $this->db
            ->set('estado', 0)
            ->where('id', $id)
            ->update('comprobantes_pod');
        return $this->db->affected_rows() > 0;
    }

    public function contar_comprobantes_activos($id_pod)
    {
        $this->db->where('id_pod', $id_pod);
        $this->db->where('estado !=', 0);
        return $this->db->count_all_results('comprobantes_pod');
    }

    public function actualizar_pod_manifiesto_gt($mapPOD)
    {
        foreach ($mapPOD as $idpre => $id_pod) {
            if (empty($id_pod)) {
                continue;
            }

            $this->db
                ->where('id_manifiesto', $idpre)
                ->where('id_pod IS NULL', null, false)
                ->update('manifiesto', [
                    'id_pod' => $id_pod
                ]);
        }
    }

    public function obtener_detalle_pod_gt($idpod,$idpre)
    {
        $this->db->select("id_manifiesto, referencia, 
                   COUNT(CASE WHEN id_pod IS NOT NULL THEN 1 END) AS paquetes_agregados, 
                   COUNT(CASE WHEN id_pod IS NULL THEN 1 END) AS paquetes_excluidos", FALSE);

        $this->db->from('manifiesto');
        $this->db->where('id_manifiesto', $idpre);
        $this->db->where('id_pod', $idpod);
        $this->db->group_by(array('id_manifiesto', 'referencia'));

        $query = $this->db->get();
        $resultado = $query->result(); // Retorna un array de objetos

        return $resultado;
    }

    public function actualizar_totales_encabezado_gt($id_pod,$id_pre)
    {
        $this->db->select('
            COUNT(DISTINCT referencia) as total_referencia,
            COUNT(DISTINCT bag_number) as numero_cajas,
            COUNT(*) as total_paquetes
        ');

        $this->db->from('manifiesto');
        $this->db->where('id_pod', $id_pod);
        $this->db->where('id_manifiesto', $id_pre);

        $row = $this->db->get()->row();

        $this->db->where('id', $id_pod);
        $this->db->update('encabezado_pod', [
            'total_referencias' => $row->total_referencia,
            'numero_cajas'     => $row->numero_cajas,
            'numero_paquetes'   => $row->total_paquetes
        ]);
    }

    public function paquetes_gt_incluidos($id_pre, $referencia, $id_pod)
    {
        $query = $this->db
            ->select('tracking_number')
            ->from('manifiesto')
            ->where('id_manifiesto', $id_pre)
            ->where('referencia', $referencia)
            ->where('id_pod', $id_pod)
            ->get()
            ->result();

        // convertir a array simple
        $tracking = array_map(function ($row) {
            return $row->tracking_number;
        }, $query);

        // convertir a string separado por comas
        return implode(', ', $tracking);
    }

    public function paquetes_gt_excluidos($id_pre, $referencia)
    {
        $query = $this->db
            ->select('tracking_number')
            ->from('manifiesto')
            ->where('id_manifiesto', $id_pre)
            ->where('referencia', $referencia)
            ->where('id_pod IS NULL', null, false)
            ->get()
            ->result();

        // convertir a array simple
        $tracking = array_map(function ($row) {
            return $row->tracking_number;
        }, $query);

        // convertir a string separado por comas
        return implode(', ', $tracking);
    }

    public function obtener_idpreclasificacion($id_pod)
    {
        $query = $this->db
            ->select('idpreclasificacion')
            ->from('encabezado_pod')
            ->where('id', $id_pod)
            ->where('estado', 1)
            ->get()
            ->row();
        return $query;
    }

    public function paquetes_pod_gt($idpreclasificacion, $tracking, $nuevo_id_pod)
    {
        $tracking = trim($tracking);

        if ($tracking === '') {
            return [
                'success' => false,
                'affected' => 0
            ];
        }

        $this->db->where([
            'id_manifiesto'   => $idpreclasificacion,
            'tracking_number' => $tracking
        ]);

        $this->db->set('id_pod', $nuevo_id_pod);

        $this->db->update('manifiesto');

        $affected = $this->db->affected_rows();

        return [
            'success'  => ($affected > 0),
            'affected' => $affected
        ];
    }


    public function paquetes_pod_gt_todo($idpreclasificacion, $id_pod)
    {
        
        $this->db->where([
            'id_manifiesto'   => $idpreclasificacion,
            'id_pod' => $id_pod
        ]);

        $this->db->set('id_pod', null);

        $this->db->update('manifiesto');

        $affected = $this->db->affected_rows();

        return [
            'success'  => ($affected > 0),
            'affected' => $affected
        ];
    }

    public function obtener_estado_pod_gt($idpreclasificacion)
    {
        $this->db->from('manifiesto');
        $this->db->where('id_manifiesto', $idpreclasificacion);

        $total_registros = $this->db->count_all_results();

        $this->db->from('manifiesto');
        $this->db->where('id_manifiesto', $idpreclasificacion);
        $this->db->where('id_pod IS NOT NULL', null, false);

        $total_pod = $this->db->count_all_results();

        return [
            'total' => $total_registros,
            'con_pod' => $total_pod
        ];
    }

    public function obtener_estado_pod_gt_busqueda($idpreclasificacion)
    {
        return $this->db
            ->select("
                COUNT(*) AS total,
                SUM(CASE WHEN id_pod IS NOT NULL THEN 1 ELSE 0 END) AS procesadas,
                SUM(CASE WHEN id_pod IS NULL THEN 1 ELSE 0 END) AS pendientes
            ")
            ->from('manifiesto')
            ->where('id_manifiesto', $idpreclasificacion)
            ->get()
            ->row();
    }

    public function anular_pod_gt($id_pod,$id_pre)
    {
        $this->db
            ->set('id_pod', null)
            ->where('id_manifiesto', $id_pre)
            ->where('id_pod', $id_pod)
            ->update('manifiesto');
        return $this->db->affected_rows() > 0;
    }

    public function obtener_detalle_pod_gt_pdf($id_pod,$idpre)
    {
        return $this->db
            ->select('referencia, tracking_number')
            ->from('manifiesto')
            ->where('id_manifiesto', $idpre)
            ->where('id_pod', $id_pod)
            ->order_by('referencia', 'DESC')
            ->order_by('tracking_number', 'ASC')
            ->get()
            ->result();
    }

    
} //fin