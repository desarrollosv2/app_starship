<?php

defined('BASEPATH') or exit('No direct script access allowed');
class WhModel extends CI_Model
{
    public function listado_warehouse($args, $campo)
    {
        //	echo $mawb."-".$cadena;
        $query = $this->db
            ->select('*')
            ->where($campo, $args)
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function lista_warehouse($id, $master, $correla)
    {

        if (!$correla == 0) {
            $this->db->where('tracking_number', $correla);
        }
        $query = $this->db
            ->select('ma.*,pa.numero_partida, pa.descripcion descripcion_producto')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->where('ma.id_manifiesto', $id)
            ->get('manifiesto ma')
            ->result();
        return $query;
    }

    public function lista_warehouse_confirma($id, $master)
    {
        $query = $this->db
            ->select('ma.tracking_number,ma.consignee_address1,ma.departamento_name, ma.municipio_name,
                      ma.departamento, ma.municipio, ma.departamento_id, ma.municipio_id, ma.pickup_number, ma.internal_tracking, ma.tipo_entrega, ma.tipo_servicio, pa.numero_partida')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->where('ma.id_manifiesto', $id)
            ->get('manifiesto ma')
            ->result();
        return $query;
    }
    //  funcion creada para  mostrar  pdf  creado para  detalle de  manifiesto 
    public function lista_warehouse1($id, $referencia)
    {
        $query = $this->db
            ->select('ma.*,pre.manifiesto, pre.fecha as fecha, pre.client_id as id_empresa')
            ->join('preclasificacion pre', 'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')
            ->where('ma.id_manifiesto', $id)
            ->where('ma.referencia', $referencia)
            ->get('manifiesto ma')
            ->result();
        return $query;
    }
    public function lista_warehouse_individual($id)
    {
        $query = $this->db
            ->select('ma.*,pre.manifiesto, pre.fecha as fecha')
            ->join('preclasificacion pre', 'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')
            ->where('ma.id_manifiesto', $id)

            ->get('manifiesto ma')
            ->result();
        return $query;
    }

    public function manifiesto_export($id, $referencia)
    {
        $query = $this->db
            ->select('ma.*,pre.manifiesto, pre.fecha as fecha')
            ->join('preclasificacion pre', 'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')
            ->where('ma.id_manifiesto', $id)
            ->get('manifiesto ma')
            ->result();
        return $query;

        // $query = $this->db

        //     ->select('ma.*,pre.manifiesto, pre.fecha as fecha, pa.numero_partida, pa.marca, pa.modelo, pa.id_estado, pa.descripcion, pa.descripcion_generica, pa.id_origen, pa.porcentaje')
        //     ->join('preclasificacion pre', 'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')

        //     ->join('partida_arancel pa', 'pa.id =  ma.id_partida', 'inner')
        //     ->where('ma.id_manifiesto', $id)
        //     ->get('manifiesto ma')
        //     ->result();


        // return $query;
    }

    public function getTemuProducts($id_manifiesto)
    {
        $this->db->select("
            id_tracking,
            RPAD(hts, 12, '0') AS hts, 
            GROUP_CONCAT(items_description SEPARATOR ' | ') AS items_description,
            SUM(items_quantities) AS items_quantities,
            SUM(value_unit * items_quantities) AS cif,
            SUM(weight_unit * items_quantities) AS weight_unit
        ", FALSE);

        $this->db->from('detalle_items');
        $this->db->where('id_manifiesto', $id_manifiesto);

        // Agrupar por tracking y arancel
        $this->db->group_by('id_tracking');
        $this->db->group_by("RPAD(hts, 12, '0')", FALSE);

        // Orden
        $this->db->order_by('id_tracking', 'ASC');
        $this->db->order_by('SUM(weight_unit * items_quantities)', 'ASC', FALSE);
        $this->db->order_by("RPAD(hts, 12, '0')", 'ASC', FALSE);

        $query = $this->db->get();

        return $query->result();
    }

    public function getDaiPartida($partida)
    {

        $result = $this->db
            ->select('porcentaje_dai')
            ->where('partida', $partida)
            ->get('porcentajes_dai')
            ->row();

        return $result;
    }

    public function updateTrackingTariffCode($partida, $porcentaje_dai, $tracking, $manifiesto)
    {
        $this->db->set('partida', $partida)
            ->set('porcentaje_dai', $porcentaje_dai)
            ->where('tracking_number', $tracking)
            ->where('id_manifiesto', $manifiesto)
            ->update('manifiesto');
    }

    public function updateValuesGtTm($fob, $flete, $seguro, $peso, $tracking, $manifiesto)
    {
        $this->db->set('fob_gt', $fob)
            ->set('flete_gt', $flete)
            ->set('seguro_gt', $seguro)
            ->set('peso_prorateado_gt', $peso)
            ->where('tracking_number', $tracking)
            ->where('id_manifiesto', $manifiesto)
            ->update('manifiesto');
    }


    public function updatePesoPGtTm($peso, $tracking, $manifiesto)
    {
        $this->db->set('peso_prorateado_gt', $peso)
            ->where('tracking_number', $tracking)
            ->where('id_manifiesto', $manifiesto)
            ->update('manifiesto');
    }
    public function updateFletePGtTm($flete, $tracking, $manifiesto)
    {
        $this->db->set('flete_gt', $flete)
            ->where('tracking_number', $tracking)
            ->where('id_manifiesto', $manifiesto)
            ->update('manifiesto');
    }

    public function updateFobPGtTm($fob, $tracking, $manifiesto)
    {
        $this->db->set('fob_gt', $fob)
            ->where('tracking_number', $tracking)
            ->where('id_manifiesto', $manifiesto)
            ->update('manifiesto');
    }

    public function lista_pendientes_confirmar()
    {
        $query = $this->db
            ->select('*')
            ->where('confirmada', '0')
            ->where('facturado', '0')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function lista_confirmados()
    {

        $query = $this->db
            ->select('*')
            ->where('confirmada', 1)
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function lista_referencia($id, $ref)
    {

        $query = $this->db
            ->select('*')
            ->where('id_manifiesto', $id)
            ->where('referencia', $ref)
            ->order_by('tracking_number')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function reference_list($id)
    {

        $query = $this->db
            ->select('*')
            ->where('id_manifiesto', $id)
            ->get('referencia')
            ->result();
        return $query;
    }

    public function seleccionar_item($id, $opc)
    {
        // echo $id."--".$opc;
        $this->db
            ->set('seleccionado', $opc)
            ->set('user_id', $_SESSION["user_id"])
            ->where('id', $id)
            ->update('manifiesto');
    }

    public function getCifGt($id)
    {
        $row = $this->db->select_sum('value', 'total_value')
            ->select_sum('gweight', 'total_peso') // Reemplaza 'peso' por el nombre real de tu columna
            ->where('id_manifiesto', $id)
            ->get('manifiesto')
            ->row();

        // Retornamos un array estructurado con los valores ya convertidos a números
        return [
            'value' => $row ? (float) $row->total_value : 0.0,
            'peso'  => $row ? (float) $row->total_peso : 0.0
        ];
    }

    public function lista_awb($desde, $hasta)
    {
        $client_code = (isset($_SESSION["client_code"]) ? $_SESSION["client_code"] : '');

        if (!empty($client_code)) {
            $this->db->where("client_id", $client_code);
        }

        /*$query = $this->db
            ->select('*')
            ->where('procesada', '0')
            ->where('id_pais', $_SESSION["pais"])
            ->where('fecha >=', $desde)  // Fecha desde
            ->where('fecha <=', $hasta)  // Fecha hasta
            ->order_by('fecha', 'DESC')
            ->get('preclasificacion')
            ->result();*/

        $query = $this->db
            ->select('preclasificacion.*, proveedores_lm.*') // Select columns from both tables
            ->from('preclasificacion') // Explicitly define the main table
            ->join('proveedores_lm', 'proveedores_lm.id = preclasificacion.id_proveedor_lm', 'left')
            ->where('preclasificacion.procesada', '0')
            ->where('preclasificacion.id_pais', $_SESSION["pais"])
            ->where('preclasificacion.fecha >=', $desde)
            ->where('preclasificacion.fecha <=', $hasta)
            ->order_by('preclasificacion.fecha', 'DESC')
            ->get()
            ->result();

        return $query;
    }

    public function obtener_gt_por_id($id, $id_manifiesto)
    {
        $query = $this->db
            ->select('flete_gt,fob_gt,seguro_gt,peso_prorateado_gt')
            ->where('id', $id)
            ->where('id_manifiesto', $id_manifiesto)
            ->get('manifiesto')
            ->row();
        return $query;
    }

    public function obtener_detalle_gt_por_id($id, $id_manifiesto, $frase)
    {
        /*$query = $this->db
            ->select('items_quantities')
            ->where('id_tracking', $id)
            ->where('id_manifiesto', $id_manifiesto)
            ->where('items_description', $frase)
            ->get('detalle_items')
            ->row();*/
        $query = $this->db
            ->select_sum('items_quantities', 'items_quantities')
            ->where('id_tracking', $id)
            ->where('id_manifiesto', $id_manifiesto)
            ->where('items_description', $frase)
            ->get('detalle_items')
            ->row();
        return $query;
    }

    public function lista_awb_confirma()
    {
        //	echo $mawb."-".$cadena;
        $query = $this->db
            ->select('*')
            ->where('procesada', '0')
            ->order_by('fecha', 'DESC')
            ->get('preclasificacion')
            ->result();
        return $query;
    }
    public function consulta_awb($desde, $hasta, $opcion, $param)
    {
        /* param: determinar si trae manifiesto o referencia*/
        if ($opcion == 3 || $opcion == 4 || $opcion == 5) {
            $this->db->where("fecha BETWEEN '{$desde}' AND '{$hasta}'");
        }
        if ($opcion == 1) {
            $this->db->where('manifiesto', $param);
        }
        if ($opcion == 2) {
            $this->db->where('referencia', $param);
        }
        if ($opcion == 3) {
            $this->db->where('procesada', '0');
        }
        if ($opcion == 4) {
            $this->db->where('procesada', 1);
        }

        $query = $this->db
            ->select('*')
            ->get('preclasificacion')
            ->result();
        return $query;
    }

    public function  consulta_warehouse($campo, $args)
    {
        //$args="SAL 50005";
        $query = $this->db
            ->select('*')
            ->where($campo, $args)
            ->get("warehouse_detalle")
            ->result();
        return $query;
    }

    public function  listado_polizas()
    {
        $query = $this->db
            ->select('*')
            ->get("poliza")
            ->result();
        return $query;
    }
    public function  guardar_referencia($data)
    {

        $this->db->insert('referencia', $data);
        return ($this->db->affected_rows() > 0);
    }

    public function  asignar_guia($master, $ref)
    {

        $this->db
            ->set('referencia', $ref)
            ->where('id_manifiesto', $master)
            ->where('seleccciona', $_SESSION["user_id"])
            ->where('seleccionado', 1)
            ->where('referencia', 'Pendiente')
            ->update('manifiesto');
    }

    public function  guardar_comentario($data)
    {

        $this->db->insert('registro_llamada', $data);
        return ($this->db->affected_rows() > 0);
    }

    public function update_aceptado($id)
    {
        $this->db
            ->set('confirmada', 1)
            ->where('id', $id)
            ->update('manifiesto');
    }
    public function lista_warehouse_pdf2($id)
    {
        $query = $this->db
            ->select('*')
            //->where('user_id', $_SESSION["user_id"])
            // ->where('seleccionado',1)
            ->where('id', $id)
            ->get('manifiesto')
            ->result();
        return $query;
    }
    public function update_dm($id_manifiesto, $id_d, $codigo_d, $nombre_d, $id_m, $nombre_m, $dui)
    {
        $query = $this->db
            ->set('departamento_id', $id_d)
            ->set('departamento_code', $codigo_d)
            ->set('departamento_name', $nombre_d)
            ->set('municipio_id', $id_m)
            ->set('municipio_name', $nombre_m)
            ->set('departamento', $nombre_d)
            ->set('municipio', $nombre_m)
            ->where('id', $id_manifiesto)
            ->update('manifiesto');
        return $query;
    }

    //  funcion para actualizar  valores del  manifiesto desde la carga de archivos  02-10-2023
    public function update_dm1($idpreclasificacion, $tracking_number, $DAI, $IVA, $comision, $MANEJO, $Cobro_final, $total_imp, $total_ivag)
    {
        //  echo   'HECHO  LLEGANDO AL  MODELO DE ACTUALIZACION' ;
        $query = $this->db
            ->set('dai', $DAI)
            ->set('iva', $IVA)
            ->set('comision', $comision)
            ->set('manejo', $MANEJO)
            ->set('cobro_final', $Cobro_final)
            ->set('total_imp', $total_imp)
            ->set('total_ivag', $total_ivag)
            ->where('id_manifiesto', $idpreclasificacion)
            ->where('tracking_number', $tracking_number)
            ->where('facturado', 0)
            ->update('manifiesto');
        return $query;
    }

    public function update_dm2($idpreclasificacion, $tracking_number, $DAI, $IVA, $comision, $MANEJO, $Cobro_final, $total_imp, $total_ivag, $almacenaje = 0, $sel = 0)
    {
        //  echo   'HECHO  LLEGANDO AL  MODELO DE ACTUALIZACION' ;
        $query = $this->db
            ->set('dai', $DAI)
            ->set('iva', $IVA)
            ->set('almacenaje', $almacenaje)
            ->set('sel', $sel)
            ->set('comision', $comision)
            ->set('manejo', $MANEJO)
            ->set('cobro_final', $Cobro_final)
            ->set('total_imp', $total_imp)
            ->set('total_ivag', $total_ivag)
            ->where('id_manifiesto', $idpreclasificacion)
            ->where('tracking_number', $tracking_number)
            ->where('facturado', 0)
            ->update('manifiesto');
        return $query;
    }

    //  funcion para actualizar  valores del  manifiesto desde la carga de archivos  02-10-2023
    public function update_DUI($idpreclasificacion, $tracking_number, $DUI)
    {
        //echo   'HECHO  LLEGANDO AL  MODELO DE ACTUALIZACION   '.  $idpreclasificacion. '#'.$tracking_number .'#'. $DUI.'<br>';
        $query =  $this->db
            ->set('consignee_account', $DUI)
            ->where('id_manifiesto', $idpreclasificacion)
            ->where('tracking_number', $tracking_number)

            ->update('manifiesto');
        return $query;
    }

    public function cargar_referencia()
    {
        $query = $this->db
            ->select('*')
            ->get('base_manifiesto')
            ->result();
        return $query;
    }

    public function listadoxsaco($bolsas)
    {
        $query = $this->db
            ->select('manifiesto.id_manifiesto,
                                preclasificacion.manifiesto, 
                                manifiesto.referencia,
                                manifiesto.bag_number,
                                COUNT(tracking_number) AS no_guia,
                                SUM(manifiesto.value) AS total')
            ->join('preclasificacion', 'manifiesto.id_manifiesto = preclasificacion.idpreclasificacion', 'inner')
            ->where('manifiesto.bag_number', $bolsas)
            ->group_by('bag_number')
            ->get('manifiesto')
            ->result();
        return $query;
    }


    public function update_ref($awb, $tracking_number, $referencia,  $id_manifiesto)
    {
        //Actualiza las referencias


        $query = $this->db
            ->set('referencia', $referencia)
            ->where('tracking_number', $tracking_number)
            ->where('id_manifiesto', $id_manifiesto)
            ->update('manifiesto');
        return $query;
    }

    public function verificar_lm($id)
    {
        $query = $this->db
            ->select('id, consignee_city, consignee_state')
            ->where('id_manifiesto', $id)
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function update_lmbk($id, $depto, $munic, $opc1,  $opc2,  $opc3,  $opc4,  $opc5,  $opc6, $name_d, $name_m, $code)
    {

        $status_array = array($opc1,  $opc2,  $opc3,  $opc4,  $opc5,  $opc6);


        $this->db
            ->set('departamento_id', $depto)
            ->set('municipio_id', $munic)
            ->set('flag_d', "1")
            ->set('flag_m', "1")
            ->set('departamento_name', $name_d)
            ->set('municipio_name', $name_m)
            ->set('departamento', $name_d)
            ->set('municipio', $name_m)
            ->set('departamento_code', $code)
            ->where('id_manifiesto', $id)
            ->where('agencia_destino', 0)
            ->where_in('consignee_city', $status_array)
            ->update('manifiesto');
    }
    public function update_lm($id, $depto, $munic, $opc1, $opc2, $opc3, $opc4, $opc5, $opc6, $name_d, $name_m, $code)
    {
        // Variantes del municipio
        $status_array = array($opc1, $opc2, $opc3, $opc4, $opc5, $opc6);

        // Variantes del departamento
        $depto_array = array(
            $name_d,
            mb_strtoupper($name_d, 'UTF-8'),
            strtolower($name_d),
            strtoupper($name_d),
            $this->eliminar_acentos($name_d),
            strtolower($this->eliminar_acentos($name_d))
        );

        $this->db
            ->set('departamento_id', $depto)
            ->set('municipio_id', $munic)
            ->set('flag_d', "1")
            ->set('flag_m', "1")
            ->set('departamento_name', $name_d)
            ->set('municipio_name', $name_m)
            ->set('departamento', $name_d)
            ->set('municipio', $name_m)
            ->set('departamento_code', $code)
            ->where('id_manifiesto', $id)
            ->where('agencia_destino', 0)
            ->where('flag_m', 0) // 🔥 evita sobreescribir registros ya asignados
            ->where_in('consignee_city', $status_array)
            ->where_in('consignee_state', $depto_array) // 🔥 valida también el departamento
            ->update('manifiesto');
    }
    public function eliminar_acentos($cadena)
    {
        $acentos = array(
            'á',
            'à',
            'ä',
            'â',
            'Á',
            'À',
            'Ä',
            'Â',
            'é',
            'è',
            'ë',
            'ê',
            'É',
            'È',
            'Ë',
            'Ê',
            'í',
            'ì',
            'ï',
            'î',
            'Í',
            'Ì',
            'Ï',
            'Î',
            'ó',
            'ò',
            'ö',
            'ô',
            'Ó',
            'Ò',
            'Ö',
            'Ô',
            'ú',
            'ù',
            'ü',
            'û',
            'Ú',
            'Ù',
            'Ü',
            'Û',
            'ñ',
            'Ñ',
            'ç',
            'Ç'
        );

        $sin_acentos = array(
            'a',
            'a',
            'a',
            'a',
            'A',
            'A',
            'A',
            'A',
            'e',
            'e',
            'e',
            'e',
            'E',
            'E',
            'E',
            'E',
            'i',
            'i',
            'i',
            'i',
            'I',
            'I',
            'I',
            'I',
            'o',
            'o',
            'o',
            'o',
            'O',
            'O',
            'O',
            'O',
            'u',
            'u',
            'u',
            'u',
            'U',
            'U',
            'U',
            'U',
            'n',
            'N',
            'c',
            'C'
        );

        return str_replace($acentos, $sin_acentos, $cadena);
    }
    public function obtener_estatus15()
    {
        $query = $this->db
            ->select('warehouse,id_estatus')
            ->where('id_estatus', 15)
            ->get('detalle')
            ->result();
        return $query;
    }

    public function update_pod($tracking)
    {

        $this->db
            ->set('id_estatus', '15')
            ->where('tracking_number', $tracking)
            ->update('manifiesto');
    }

    public function update_bag($bag, $tracking)
    {

        $this->db
            ->set('bag_number', $bag)
            ->where('tracking_number', $tracking)
            ->update('manifiesto');
    }


    public function obtener_bags()
    {
        $query = $this->db
            ->select('*')
            ->get('bag')
            ->result();
        return $query;
    }

    public function getUserIdPreclasificacion($idpreclasificacion)
    {
        $query = $this->db
            ->select('client_id, manifiesto')
            ->where('idpreclasificacion', $idpreclasificacion)
            ->get('preclasificacion')
            ->row();
        return $query;
    }

    public function getCantidadPaquetes($idpreclasificacion, $id_pais)
    {
        $query = $this->db
            ->select('count(tracking_number) as cant_paquetes')
            ->where('id_manifiesto', $idpreclasificacion)
            ->get('manifiesto')
            ->row();
        return $query;
    }

    public function getTarifas($id_empresa, $id_pais)
    {
        $query = $this->db
            ->select('manejo_fijo')
            ->where('id_empresa', $id_empresa)
            ->where('id_pais', $id_pais)
            ->get('tarifas_manejo')
            ->row();
        return $query;
    }

    public function getDepMunId($tracking)
    {
        $query = $this->db
            ->select('departamento_id, municipio_id')
            ->where('tracking_number', $tracking)
            ->where('departamento_id >', 0)
            ->get('manifiesto')
            ->row();
        return $query;
    }


    public function getCostosByTracking($tracking)
    {
        $query = $this->db
            ->select('almacenaje, total_imp, cobro_final')
            ->where('tracking_number', $tracking)
            ->get('manifiesto')
            ->row();
        return $query;
    }




    public function updateManifiestoByColumn($datas, $seleccionados)
    {

        if (!empty($datas['tracking_number'])) {

            foreach ($seleccionados as $col) {
                if (isset($datas[$col])) {
                    $this->db->set($col, $datas[$col]);
                    echo $col . " " . $datas[$col];
                }
            }

            $this->db->where('tracking_number', $datas['tracking_number'])
                ->where('id_manifiesto', $datas['id_manifiesto'])
                ->update('manifiesto');
        }
    }

    public function reporte_guias($idpreclasificacion)
    {
        $rsl =    $this->db
            ->select('preclasificacion.idpreclasificacion,
                               preclasificacion.manifiesto as awb,
                               preclasificacion.manifiesto_tempo as awb_cn,
                               manifiesto.tracking_number,
                               manifiesto.tracking_replace,
                               manifiesto.referencia,
                               manifiesto.bag_number,
                               manifiesto.consignee_account,
                               manifiesto.value,
                               manifiesto.commodity,
                               manifiesto.gweight,
                               manifiesto.almacenaje,
                               manifiesto.consignee_address1,
                               date_format(manifiesto.fecha_creacion, "%d-%m-%Y") AS fecha_creacion,
                               manifiesto.consignee,
                               manifiesto.tipo_entrega,
                               manifiesto.tipo_servicio,
                               manifiesto.pickup_number,
                               manifiesto.internal_tracking,
                               estatus.nombre AS estatus,
                               manifiesto.user_name,
                               (CASE
                                    WHEN manifiesto.facturado = 0 THEN "Pendiente"
                                    WHEN manifiesto.facturado = 1 THEN "Facturado"
                               END) AS estado,
                               manifiesto.dai,
                               manifiesto.iva,
                               (manifiesto.manejo + manifiesto.total_ivag) as manejo,
                               manifiesto.cobro_final,   manifiesto.sobrante, manifiesto.departamento_id')
            ->JOIN('preclasificacion', 'preclasificacion.idpreclasificacion = manifiesto.id_manifiesto', 'INNER')
            // ->JOIN('detalle', 'detalle.warehouse = manifiesto.tracking_number')
            ->JOIN('estatus', 'estatus.id_estatus = manifiesto.id_estatus', 'LEFT')

            ->WHERE('preclasificacion.idpreclasificacion', $idpreclasificacion)
            ->GROUP_BY('manifiesto.tracking_number')
            // ->ORDER_BY('manifiesto.tracking_number')
            ->get('manifiesto')
            ->result();

        return $rsl;
    }

    public function buscar_tracking_numb($tracking)
    {
        $query = $this->db
            ->select('ma.*,
                                 preclasificacion.manifiesto as awb,
                                 pa.numero_partida, 
                                 pa.descripcion descripcion_producto,
                                 consignee.comentario')
            ->join('preclasificacion', 'preclasificacion.idpreclasificacion = ma.id_manifiesto', 'inner')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->join('consignee', 'consignee.no_doc = ma.consignee_email', 'left')
            ->where('ma.tracking_number', $tracking)

            ->order_by('consignee.comentario', 'ASC')
            ->group_by('ma.tracking_number')

            ->get('manifiesto ma')
            ->result();
        return $query;
    }


    public function buscar_package_numb($tracking)
    {
        $query = $this->db
            ->select('ma.*,
                                 preclasificacion.manifiesto as awb,
                                 pa.numero_partida, 
                                 pa.descripcion descripcion_producto,
                                 consignee.comentario')
            ->join('preclasificacion', 'preclasificacion.idpreclasificacion = ma.id_manifiesto', 'inner')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->join('consignee', 'consignee.no_doc = ma.consignee_email', 'left')
            ->where('ma.package_number', $tracking)

            ->order_by('consignee.comentario', 'ASC')
            ->group_by('ma.tracking_number')

            ->get('manifiesto ma')
            ->result();
        return $query;
    }

    public function qtys_by_box($id){
        $this->db->select('bag_number, COUNT(tracking_number) AS total');
        $this->db->from('manifiesto');
        $this->db->where('id_manifiesto', $id);
        $this->db->group_by('bag_number');

        $query = $this->db->get();
        $resultado = $query->result();
        return $resultado;
    }

    public function guardar_listsobrante($id_manifiesto, $datas, $tracking_number)
    {
        $this->db
            ->set('tracking_number',           $datas['tracking_number'])
            ->set('gweight',                   $datas['gweight'])
            ->set('value',                     $datas['value'])
            ->set('items',                     $datas['items'])
            ->set('commodity',                 $datas['commodity'])
            ->set('consignee_account',         $datas['consignee_account'])
            ->set('consignee',                 $datas['consignee'])
            ->set('buyer_company',             $datas['buyer_company'])
            ->set('consignee_address1',        $datas['consignee_address1'])
            ->set('buyer_address1_number',     $datas['buyer_address1_number'])
            ->set('consignee_address2',        $datas['consignee_address2'])
            ->set('consignee_address3',        $datas['consignee_address3'])
            ->set('buyer_district',            $datas['buyer_district'])
            ->set('consignee_city',            $datas['consignee_city'])
            ->set('consignee_state',           $datas['consignee_state'])
            ->set('consignee_country',         $datas['consignee_country'])
            ->set('consignee_zip',             $datas['consignee_zip'])
            ->set('consignee_phone',           $datas['consignee_phone'])
            ->set('consignee_email',           $datas['consignee_email'])
            ->set('hts',                       $datas['hts'])
            ->set('id_pais',                   $datas['id_pais'])
            ->set('pieces',                    $datas['pieces'])
            ->set('departamento',              $datas['departamento'])
            ->set('municipio',                 $datas['municipio'])
            ->set('wpounds',                   $datas['wpounds'])
            ->set('id_manifiesto',             $datas['id_manifiesto'])
            ->set('sobrante',                  $datas['sobrante'])
            ->set('items_quantities',          $datas['items_quantities'])
            ->set('bag_number',                  $datas['bag_number'])
            ->insert('manifiesto');
        return ($this->db->affected_rows() > 0);

        /*if($datas['tracking_number'] != ""){
            $this->db
                ->set('gweight',                   $datas['gweight'])
                ->set('items',                     $datas['items'])
                ->set('commodity',                 $datas['commodity'])
                ->set('consignee_account',         $datas['consignee_account'])
                ->set('consignee',                 $datas['consignee'])
                ->set('buyer_c
                ompany',             $datas['buyer_company'])
                ->set('consignee_address1',        $datas['consignee_address1'])
                ->set('buyer_address1_number',     $datas['buyer_address1_number'])
                ->set('consignee_address2',        $datas['consignee_address2'])
                ->set('consignee_address3',        $datas['consignee_address3'])
                ->set('buyer_district',            $datas['buyer_district'])
                ->set('consignee_city',            $datas['consignee_city'])
                ->set('consignee_state',           $datas['consignee_state'])
                ->set('consignee_country',         $datas['consignee_country'])
                ->set('consignee_zip',             $datas['consignee_zip'])
                ->set('consignee_phone',           $datas['consignee_phone'])
                ->set('consignee_email',           $datas['consignee_email'])
                ->set('hts',                       $datas['hts'])
                ->set('id_pais',                   $datas['id_pais'])
                ->set('pieces',                    $datas['pieces'])
                ->set('departamento',              $datas['departamento'])
                ->set('municipio',                 $datas['municipio'])
                ->set('wpounds',                   $datas['wpounds'])
                ->set('id_manifiesto',             $datas['id_manifiesto'])
                ->set('sobrante',                  $datas['sobrante'])
                ->set('items_quantities',          $datas['items_quantities'])
                ->where('tracking_number', $datas['tracking_number'])
                ->update('manifiesto');
        }*/
    }





    public function bolsa_ref($idpreclasificacion)
    {
        $query = $this->db
            ->select('manifiesto.id_manifiesto,
                                preclasificacion.manifiesto, 
                                manifiesto.referencia,
                                manifiesto.bag_number')
            ->join('preclasificacion', 'manifiesto.id_manifiesto = preclasificacion.idpreclasificacion', 'inner')
            ->where('id_manifiesto', $idpreclasificacion)
            ->group_by('bag_number')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function update_ref_bolsa($referencia, $bolsas)
    {
        $this->db
            ->set('referencia', $referencia)
            ->where('bag_number', $bolsas)
            ->update('manifiesto');
    }

    public function  update_ref_bolsa_ma($data)
    {
        // Comprobar si ya existe el registro
        $this->db->where('referencia', $data['referencia']);
        $this->db->where('id_manifiesto', $data['id_manifiesto']);
        $query = $this->db->get('referencia');

        if ($query->num_rows() == 0) {
            // Si no existe, insertar
            $this->db->insert('referencia', $data);
            return $this->db->insert_id();  // Devuelve el ID del nuevo registro
        } else {
            return false;  // Ya existe el registro
        }
    }

    public function reasignar_referencia($referencia, $tracking_number)
    {
        $this->db
            ->set('referencia', $referencia)
            ->where('tracking_number', $tracking_number)
            ->update('manifiesto');
    }


    public function crear_ref($id)
    {
        $query = $this->db
            ->select('manifiesto.id_manifiesto,
                                preclasificacion.manifiesto, 
                                manifiesto.referencia,
                                manifiesto.bag_number')
            ->join('preclasificacion', 'manifiesto.id_manifiesto = preclasificacion.idpreclasificacion', 'inner')
            ->where('manifiesto.id_manifiesto', $id)
            ->group_by('manifiesto.id_manifiesto')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    // Mario termino 1:11 pm 
    public function reload_guia()
    {
        $query = $this->db
            ->select('*')
            ->where('aplicado', 0)
            ->where('id >=', 1200)
            //->where('id=', 1)
            ->get('paquete')
            ->result();
        return $query;
    }

    public function bandera($id)
    {

        $this->db
            ->set('aplicado', 1)
            ->where('guia', $id)
            ->update('paquete');
    }

    public function reload_guia_mario()
    {
        $query = $this->db
            ->select('*')
            ->where('aplicado', 0)
            ->where('id >=', 929)
            //->where('id=', 1)
            ->get('paquete_01')
            ->result();
        return $query;
    }

    public function bandera_mario($id)
    {

        $this->db
            ->set('aplicado', 1)
            ->where('guia', $id)
            ->update('paquete_01');
    }

    public function guia_alternativa($guia)
    {
        $query = $this->db->select('*')
            ->where('tracking_replace', $guia)
            ->get('reemplazo')
            ->result();
        return $query;
    }

    public function sucursal()
    {
        $query = $this->db
            ->select('*')
            ->get('agencias')
            ->result();
        return $query;
    }


    public function validar_estatus()
    {
        $query = $this->db
            ->select('*')
            ->where('id >=', 1046)
            ->get('paquete_01')
            ->result();
        return $query;
    }


    public function guardar_estado($id, $mensaje, $fecha)
    {

        $this->db
            ->set('estatus', $mensaje)
            ->set('fecha', $fecha)
            ->where('guia', $id)
            ->update('paquete_01');
    }

    public function buscar_estado($id)
    {
        $this->db->select('*');
        $this->db->where('warehouse', $id);
        $this->db->order_by('id_estatus', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('detalle');

        return   $query->result_array();
    }

    public function guiasxref($referencia, $id_manifiesto, $full)
    {
        if ($full == 0) {
            //$this->db->where('(a = 1 or a = 2)');
            $this->db->where("manifiesto.iva", 0);
            $this->db->where("manifiesto.dai", 0);
        }
        $query = $this->db
            ->select('manifiesto.id_manifiesto,
                                preclasificacion.manifiesto, 
                                manifiesto.referencia,
                                manifiesto.tracking_number,
                                manifiesto.iva,
                                manifiesto.dai,
                                SUM(manifiesto.iva) AS Total_IVA,
                                SUM(manifiesto.dai) AS Total_DAI,
                                manifiesto.cobro_final')
            ->join('preclasificacion', 'manifiesto.id_manifiesto = preclasificacion.idpreclasificacion', 'inner')
            ->where('manifiesto.referencia', $referencia)
            ->where('manifiesto.id_manifiesto', $id_manifiesto)
            ->group_by('tracking_number')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function valor_guiasxref($referencia, $tracking_number)
    {
        $query = $this->db
            ->select('manifiesto.id_manifiesto,
                                preclasificacion.manifiesto, 
                                manifiesto.referencia,
                                manifiesto.tracking_number,
                                manifiesto.iva,
                                manifiesto.dai,
                                manifiesto.cobro_final')
            ->join('preclasificacion', 'manifiesto.id_manifiesto = preclasificacion.idpreclasificacion', 'inner')
            ->where('manifiesto.referencia', $referencia)
            ->where('manifiesto.tracking_number', $tracking_number)
            ->group_by('tracking_number')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function valor_guiasxref_general($referencia, $id_manifiesto)
    {
        $query = $this->db
            ->select('manifiesto.id_manifiesto,
                                SUM(manifiesto.iva) AS Total_IVA,
                                SUM(manifiesto.dai) AS Total_DAI')
            ->where('manifiesto.referencia', $referencia)
            ->where('manifiesto.id_manifiesto', $id_manifiesto)
            ->order_by('manifiesto.tracking_number', 'ASC')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function guardar_costos($id_manifiesto, $referencia, $tracking_number, $iva, $dai, $total_imp, $comision, $total_iva, $cobro_final, $manejo)
    {
        $query  =   $this->db
            ->set('iva', round($iva, 2))
            ->set('dai', round($dai, 2))
            ->set('cobro_final', round($cobro_final, 2))
            ->set('manejo', round($manejo, 2))
            ->set('total_imp', round($total_imp, 2))
            ->set('comision', round($comision, 2))
            ->set('total_ivag', round($total_iva, 2))

            ->where('id_manifiesto', $id_manifiesto)
            ->where('referencia', $referencia)
            ->where('tracking_number', $tracking_number)
            ->update('manifiesto');
        return $query;
    }


    public function estatus_referencias($manifiesto)
    {
        $query = $this->db
            ->select('pr.idpreclasificacion, pr.manifiesto, pr.fecha, pr.descripcion, pr.client_id, re.referencia nombre_referencia, re.selectivo')
            ->join('referencia re', 're.id_manifiesto = pr.idpreclasificacion', 'inner')
            ->where('pr.manifiesto', $manifiesto)
            ->where('pr.id_pais', $_SESSION["pais"])
            ->get("preclasificacion pr")
            ->result();
        return $query;
    }

    public function asignar_selectivo($selectivo,$referencia,$idmanifiesto)
    {
        $data = [
            'selectivo' => $selectivo
        ];

        return $this->db
            ->where('id_manifiesto', $idmanifiesto)
            ->where('referencia', $referencia)
            ->update('referencia', $data);
    }


    public function obtener_tiempos_manifiesto($id_manifiesto)
    {
        $sql = "
            SELECT
                t.manifiesto,
                t.referencia,
                t.warehouse,

                t.fecha_recibido,
                t.fecha_proceso,
                t.fecha_demorado,
                t.fecha_liberado,
                t.fecha_entregado,

                 /* Recibido -> Proceso */
    CONCAT(
        TIMESTAMPDIFF(DAY, t.fecha_recibido, t.fecha_proceso),
        ' días ',
        TIME_FORMAT(
            SEC_TO_TIME(
                TIMESTAMPDIFF(SECOND, t.fecha_recibido, t.fecha_proceso)
                - TIMESTAMPDIFF(DAY, t.fecha_recibido, t.fecha_proceso) * 86400
            ),
            '%H:%i:%s'
        )
    ) AS tiempo_recibido_proceso,

    /* Proceso -> Demorado */
    CONCAT(
        TIMESTAMPDIFF(DAY, t.fecha_proceso, t.fecha_demorado),
        ' días ',
        TIME_FORMAT(
            SEC_TO_TIME(
                TIMESTAMPDIFF(SECOND, t.fecha_proceso, t.fecha_demorado)
                - TIMESTAMPDIFF(DAY, t.fecha_proceso, t.fecha_demorado) * 86400
            ),
            '%H:%i:%s'
        )
    ) AS tiempo_proceso_demorado,

    /* Demorado -> Liberado */
    CONCAT(
        TIMESTAMPDIFF(DAY, t.fecha_demorado, t.fecha_liberado),
        ' días ',
        TIME_FORMAT(
            SEC_TO_TIME(
                TIMESTAMPDIFF(SECOND, t.fecha_demorado, t.fecha_liberado)
                - TIMESTAMPDIFF(DAY, t.fecha_demorado, t.fecha_liberado) * 86400
            ),
            '%H:%i:%s'
        )
    ) AS tiempo_demorado_liberado,

    /* Proceso -> Liberado */
    CONCAT(
        TIMESTAMPDIFF(DAY, t.fecha_proceso, t.fecha_liberado),
        ' días ',
        TIME_FORMAT(
            SEC_TO_TIME(
                TIMESTAMPDIFF(SECOND, t.fecha_proceso, t.fecha_liberado)
                - TIMESTAMPDIFF(DAY, t.fecha_proceso, t.fecha_liberado) * 86400
            ),
            '%H:%i:%s'
        )
    ) AS tiempo_proceso_liberado,

    /* Liberado -> Entregado */
    CONCAT(
        TIMESTAMPDIFF(DAY, t.fecha_liberado, t.fecha_entregado),
        ' días ',
        TIME_FORMAT(
            SEC_TO_TIME(
                TIMESTAMPDIFF(SECOND, t.fecha_liberado, t.fecha_entregado)
                - TIMESTAMPDIFF(DAY, t.fecha_liberado, t.fecha_entregado) * 86400
            ),
            '%H:%i:%s'
        )
    ) AS tiempo_liberado_entregado,

    /* Tiempo total */
    CONCAT(
        TIMESTAMPDIFF(DAY, t.fecha_recibido, t.fecha_entregado),
        ' días ',
        TIME_FORMAT(
            SEC_TO_TIME(
                TIMESTAMPDIFF(SECOND, t.fecha_recibido, t.fecha_entregado)
                - TIMESTAMPDIFF(DAY, t.fecha_recibido, t.fecha_entregado) * 86400
            ),
            '%H:%i:%s'
        )
    ) AS tiempo_total

            FROM (
                SELECT
                    pre.manifiesto,
                    m.referencia,
                    m.tracking_number AS warehouse,

                    MIN(CASE WHEN d.id_estatus = 216 THEN d.fecha END) AS fecha_recibido,
                    MIN(CASE WHEN d.id_estatus = 319 THEN d.fecha END) AS fecha_proceso,
                    MIN(CASE WHEN d.id_estatus = 287 THEN d.fecha END) AS fecha_demorado,
                    MIN(CASE WHEN d.id_estatus = 288 THEN d.fecha END) AS fecha_liberado,
                    MIN(CASE WHEN d.id_estatus = 316 THEN d.fecha END) AS fecha_entregado

                FROM manifiesto m
                INNER JOIN preclasificacion pre ON pre.idpreclasificacion = m.id_manifiesto
                LEFT JOIN detalle d
                    ON d.warehouse = m.tracking_number
                   AND d.id_estatus IN (216,287,288,316,319)

                WHERE m.id_manifiesto = ?

                GROUP BY
                    m.referencia,
                    m.tracking_number
            ) t

            ORDER BY
                t.referencia,
                t.warehouse
        ";

        return $this->db->query($sql, [$id_manifiesto])->result_array();
    }


    public function aplicar_estatus_referencia($id, $manifiesto, $referencia)
    {
        $query = $this->db
            ->select('ma.id_manifiesto,  ma.referencia, ma.tracking_number, ma.id_empresa')
            ->where('ma.id_manifiesto', $id)
            ->where('ma.referencia', $referencia)
            ->get("manifiesto ma")
            ->result();
        return $query;
    }


    public function aplicar_estatus_master($id)
    {
        $query = $this->db
            ->select('ma.id_manifiesto,  ma.referencia, ma.tracking_number, ma.id_empresa')
            ->where('ma.id_manifiesto', $id)
            ->get("manifiesto ma")
            ->result();
        return $query;
    }


    public function  guardar_log_referencia($data)
    {
        $this->db->insert('log_estatus', $data);
        return ($this->db->affected_rows() > 0);
    }

    public function datos_empresa($id)
    {
        return $this->db
            ->select('*')
            ->where('id', $id)
            ->get('empresa')
            ->row();
    }


    public function get_id($id)
    {
        return $this->db
            ->select('idpreclasificacion,client_id')
            ->where('id', $id)
            ->get('preclasificacion')
            ->row();
    }


    public function verificar_empresa($id)
    {
        $query = $this->db
            ->select('tipo_factura')
            ->where('idpreclasificacion',  $id)
            ->get('preclasificacion')
            ->row();

        return $query;
    }

    public function crear_referencia($data)
    {
        $this->db->insert_batch('referencia', $data);
        return ($this->db->affected_rows() > 0);
    }

    public function get_empresa($id)
    {
        $query = $this->db
            ->select('id_empresa')
            ->where('tracking_number',  $id)
            ->get('manifiesto')
            ->row();

        return $query;
    }
    //bloque para editables
    public function editables()
    {
        $query = $this->db
            ->select('*')
            ->where('enviado',  "N")
            ->get('editables')
            ->result();

        return $query;
    }
    public function updateTelefono($tracking, $telefono)
    {
        $this->db
            ->set('consignee_phone', $telefono)
            ->where('tracking_number', $tracking)
            ->update('manifiesto');
    }
    public function updateEditables($tracking)
    {
        $this->db
            ->set('enviado', "S")
            ->where('tracking_number', $tracking)
            ->update('editables');
    }
    //fin



    public function editar_descripcion($id)
    {
        $query = $this->db->select('commodity, id')
            ->from('manifiesto')
            ->where('id', $id)
            ->get()
            ->row();
        return $query;
    }

    public function guardar_descripcion($id, $descripcion)
    {
        $this->db->set('commodity', $descripcion)
            ->where('id', $id)
            ->update('manifiesto');
    }

    public function encabezado_manifiesto($id)
    {
        $query = $this->db
            ->select('idpreclasificacion,manifiesto, fecha')
            ->from('preclasificacion')
            ->where('idpreclasificacion', $id)
            ->get()
            ->row();
        return $query;
    }
    public function obtener_datos_grafica_estatus($id_manifiesto)
    {
        $query = $this->db
            ->select("ma.tracking_number,
                      IFNULL(es.nombre, 'Sin movimiento') AS nombre,
                      IFNULL(NULLIF(ma.id_estatus, ''), 0) AS id_estatus,
                      ma.id_manifiesto", false)
            ->from('manifiesto ma')
            ->join('estatus es', 'es.id_estatus = ma.id_estatus', 'left')
            ->where('ma.id_manifiesto', $id_manifiesto)
            ->get()
            ->result();
        return $query;
    }

    public function obtener_Detalles_Por_Estatus($id_manifiesto, $nombre)
    {
        $subquery = $this->db->select('warehouse, MAX(fecha) as fecha')
            ->from('detalle')
            ->group_by('warehouse')
            ->get_compiled_select();

        $query =  $this->db->select('ma.tracking_number, ult.fecha, es.nombre')
            ->from('manifiesto ma')
            ->join("($subquery) ult", 'ma.tracking_number = ult.warehouse')
            ->join('detalle de', 'de.warehouse = ult.warehouse AND de.fecha = ult.fecha')
            ->join('estatus es', 'es.id_estatus = de.id_estatus')
            ->where('ma.id_manifiesto', $id_manifiesto)
            ->where('es.nombre', $nombre)
            ->get()
            ->result();
        return $query;
    }
    public function obtener_Detalles_Sin_Movimiento($id_manifiesto)
    {
        $query = $this->db->select('ma.tracking_number, NULL AS fecha, "Sin movimiento" AS nombre', false)
            ->from('manifiesto ma')
            ->join('detalle de', 'ma.tracking_number = de.warehouse', 'left')
            ->where('de.warehouse IS NULL') // No existe detalle
            ->where('ma.id_manifiesto', $id_manifiesto)
            ->group_by('ma.tracking_number')
            ->get()
            ->result();
        return $query;
    }

    public function exportar_excel_grafica_resumen($id, $estatus)
    {
        $subquery = $this->db->select('warehouse, MAX(fecha) as fecha')
            ->from('detalle')
            ->group_by('warehouse')
            ->get_compiled_select();

        $this->db->select('ma.tracking_number, ult.fecha, es.nombre')
            ->from('manifiesto ma')
            ->join("($subquery) ult", 'ma.tracking_number = ult.warehouse', 'left')
            ->join('detalle de', 'de.warehouse = ult.warehouse AND de.fecha = ult.fecha', 'left')
            ->join('estatus es', 'es.id_estatus = de.id_estatus', 'left')
            ->where('ma.id_manifiesto', $id);

        if ($estatus === 'pendientes') {
            $this->db->group_start();
            $this->db->where_not_in('es.id_estatus', [15, 311]);
            $this->db->or_where('es.nombre IS NULL');
            $this->db->group_end();
        } elseif ($estatus === 'entregados') {
            $this->db->where_in('es.id_estatus', [15, 311]);
        }

        $query = $this->db->get();
        return $query->result();
    }


    public function get_pending_events($master)
    {
        $query = $this->db
            ->select('tracking_number, id_empresa')
            ->where('id_estatus !=', 15)
            ->where('id_estatus !=', 311)
            ->where('id_estatus !=', 312)
            ->where('id_manifiesto', $master)
            ->get('manifiesto')
            ->result();

        return $query;
    }

    public function last_events($tracking)
    {
        $query = $this->db
            ->select('warehouse,id_estatus')
            ->where('warehouse', $tracking)
            ->get('detalle_ultimo_estatus')
            ->row();

        return $query;
    }

    public function update_last_events($tracking, $estatus)
    {

        $this->db
            ->set('id_estatus', $estatus)
            ->where('tracking_number', $tracking)
            ->update('manifiesto');
    }

    public function guardar_recolecta($id, $tracking, $solicitud)
    {
        $this->db
            ->set('pickup_number', $solicitud)
            ->set('internal_tracking', $tracking)
            ->where('id', $id)
            ->update('manifiesto');
    }

    public function datos_preclasificacion($id)
    {
        $query = $this->db->select('*')
            ->from('preclasificacion')
            ->where('idpreclasificacion', $id)
            ->get()
            ->row();
        return $query;
    }

    public function insert_log($data)
    {
        $this->db->insert('action_logs', $data);
        return ($this->db->affected_rows() > 0);
    }

    public function listar_documentos_manifiesto($id)
    {
        $query = $this->db
            ->select('*')
            ->from('doc_manifiesto')
            ->where('manifiesto_id', $id)
            ->where('estado', 1)
            ->get()
            ->result();
        return $query;
    }

    public function guardar_documentos_manifiesto($data)
    {
        $this->db->insert('doc_manifiesto', $data);
    }

    public function obtener_correlativo_doc_manifiesto($id)
    {
        $this->db->where('manifiesto_id', $id);
        $this->db->from('doc_manifiesto');
        $count = $this->db->count_all_results();

        return $count + 1;
    }

    public function eliminar_documento_manifiesto($id)
    {
        $this->db
            ->set('estado', 0)
            ->where('id', $id)
            ->update('doc_manifiesto');

        return $this->db->affected_rows() > 0;
    }

    public function changeOfCountry($idManifiesto, $idCountry, $idCompany)
    {
        $this->db->set('id_pais', $idCountry)
            ->set('client_id', $idCompany)
            ->where('idpreclasificacion', $idManifiesto)
            ->update('preclasificacion');
    }


    public function changeOfCountryTrackings($idManifiesto, $idCountry, $idCompany, $clientText)
    {
        $this->db->set('id_empresa', $idCompany)
            ->set('id_pais', $idCountry)
            ->set('client_id', $clientText)
            ->where('id_manifiesto', $idManifiesto)
            ->update('manifiesto');
    }

    public function setProveedorLM($idManifiesto, $idProveedorLM)
    {
        $this->db->set('id_proveedor_lm', $idProveedorLM)
            ->where('idpreclasificacion', $idManifiesto)
            ->update('preclasificacion');
    }

    public function obtener_tipos_documentos()
    {
        $query = $this->db
            ->select('id, slug, nivel')
            ->from('cat_tipos_documentos')
            ->get()
            ->result();
        return $query;
    }

    public function obtener_referencias_by_manifiesto($id)
    {
        $query = $this->db
            ->select('referencia')
            ->from('manifiesto')
            ->where('id_manifiesto', $id)
            ->group_by('referencia')
            ->get()
            ->result();
        return $query;
    }

    public function obtener_documento_cargado($id, $referencia, $tipo_documento)
    {

        $query = $this->db
            ->select('count(id) as cantidad_documentos')
            ->from('doc_manifiesto')
            ->where('manifiesto_id', $id)
            ->where('documento_track', $referencia)
            ->where('id_tipo_documento', $tipo_documento)
            ->get()
            ->result();
        return $query;
    }

    public function obtener_archivos_para_exportar($id)
    {
        $query = $this->db
            ->select('url, id_tipo_documento')
            ->from('doc_manifiesto')
            ->where('manifiesto_id', $id)
            ->where('estado', 1)
            ->get()
            ->result();
        return $query;
    }

    public function modificar_porcentaje_archivos_cargados($id, $porcentaje)
    {
        $query = $this->db
            ->set('porcentaje_docs', $porcentaje)
            ->where('idpreclasificacion', $id)
            ->update('preclasificacion');
        return $query;
    }

    public function get_reporte_universo($id_manifiesto)
    {
        $sql = "
        SELECT 
            universo.referencia, 
            universo.slug AS tipo_nombre, 
            COUNT(d.id) AS cantidad
        FROM (
            SELECT DISTINCT 
                m.referencia, 
                td.id AS tipo_id, 
                td.slug
            FROM manifiesto m
            INNER JOIN cat_tipos_documentos td ON 1=1
            WHERE m.id_manifiesto = ?
        ) AS universo
        LEFT JOIN doc_manifiesto d ON 
            d.documento_track = universo.referencia 
            AND d.id_tipo_documento = universo.tipo_id
        GROUP BY universo.referencia, universo.slug
        ORDER BY universo.referencia ASC, universo.slug ASC
    ";

        // Pasamos el ID en el array de bindings para mayor seguridad
        $query = $this->db->query($sql, array($id_manifiesto));

        return $query->result();
    }

    public function get_current_guide($id_manifiesto)
    {

        $query = $this->db
            ->select('manifiesto, manifiesto_tempo')
            ->from('preclasificacion')
            ->where('idpreclasificacion', $id_manifiesto)
            ->get()
            ->row();
        return $query;
    }

    public function update_guides($id_manifiesto, $guia_actual, $guia_to_set, $flag)
    {
        if ($flag == 1) {
            $this->db
                ->set('manifiesto', $guia_to_set)
                ->where('idpreclasificacion', $id_manifiesto)
                ->update('preclasificacion');

            return $this->db->affected_rows() > 0;
        } else {
            $this->db
                ->set('manifiesto_tempo', $guia_actual)
                ->set('manifiesto', $guia_to_set)
                ->where('idpreclasificacion', $id_manifiesto)
                ->update('preclasificacion');
            return $this->db->affected_rows() > 0;
        }
    }
}
