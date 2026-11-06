<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Aduana_Model extends CI_Model
{
    public function listado_warehouse($args, $campo)
    {
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
            ->select('ma.*,pa.numero_partida, pa.descripcion descripcion_producto, pre.manifiesto')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->join('preclasificacion pre', 'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')
            ->where('ma.id_manifiesto', $id)
            ->get('manifiesto ma')
            ->result();

        return $query;
    }

    public function lista_warehouse_confirma($id, $master)
    {
        $query = $this->db
            ->select('ma.tracking_number,
                                ma.consignee_address1,
                                ma.departamento_name, 
                                ma.municipio_name,
                                ma.departamento, 
                                ma.municipio, 
                                ma.departamento_id, 
                                ma.municipio_id, 
                                ma.pickup_number, 
                                ma.internal_tracking, 
                                ma.tipo_entrega, 
                                ma.tipo_servicio, 
                                pa.numero_partida')
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
            ->select('ma.*,
                                pre.manifiesto, 
                                pre.fecha as fecha')
            ->join('preclasificacion pre', 'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')
            ->where('ma.id_manifiesto', $id)
            ->where('ma.referencia', $referencia)
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
        $this->db
            ->set('seleccionado', $opc)
            ->set('user_id',      $_SESSION["user_id"])
            ->where('id',         $id)
            ->update('manifiesto');
    }

    public function Alista_awb()
    {
        $query = $this->db
            ->select('pre.*, p1.nombre puerto_salida, p2.nombre puerto_salida2, ag.Nombre declarante')
           // ->join('usuarios', 'usuarios.id = pre.userid', 'inner')
            ->join('transportista', 'transportista.id = pre.id_transportista', 'left')
            ->join('puertos p1', 'p1.idPuerto = pre.puerto_salida', 'left')
            ->join('puertos p2', 'p2.idPuerto = pre.puerto_salida2', 'left')
            ->join('agentes ag', 'ag.idAgente = pre.declarante', 'left')
            ->where('pre.procesada', '0')
            ->where('pre.id_pais', $_SESSION['pais'])
            
            ->order_by('pre.fecha', 'DESC')
            ->get('preclasificacion  pre')
            ->result();
        return $query;
    }

    public function lista_awb_confirma()
    {
        $query = $this->db
            ->select('*')
            ->where('procesada', '0')
            ->where('tipo !=', 'TRA')
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

    public function consulta_warehouse($campo, $args)
    {
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

    public function asignar_guia($master, $ref)
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

    public function update_lm($id, $depto, $munic, $opc1,  $opc2,  $opc3,  $opc4,  $opc5,  $opc6, $name_d, $name_m, $code)
    {

        $status_array = array($opc1,  $opc2,  $opc3,  $opc4,  $opc5,  $opc6);


        $this->db
            ->set('departamento_id', $depto)
            ->set('municipio_id', $munic)
            ->set('flag_d', "1")
            ->set('flag_m', "1")
            ->set('departamento_name', $name_d)
            ->set('municipio_name', $name_m)
            ->set('departamento_code', $code)
            ->set('tipo_entrega', "NRML")
            ->set('tipo_servicio', "CCE")
            ->where('id_manifiesto', $id)
            ->where('agencia_destino', 0)
            ->where_in('consignee_city', $status_array)
            ->update('manifiesto');
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

    public function reporte_guias($idpreclasificacion)
    {
        $rsl =    $this->db
            ->select('preclasificacion.idpreclasificacion,
                               preclasificacion.manifiesto as awb,
                               manifiesto.tracking_number,
                               manifiesto.tracking_replace,
                               date_format(manifiesto.fecha_creacion, "%d-%m-%Y") AS fecha_creacion,
                               manifiesto.consignee,
                               manifiesto.tipo_entrega,
                               manifiesto.tipo_servicio,
                               estatus.nombre AS estatus,
                               (CASE
                                    WHEN manifiesto.facturado = 0 THEN "Pendiente"
                                    WHEN manifiesto.facturado = 1 THEN "Facturado"
                               END) AS estado,
                               manifiesto.cobro_final')
            ->JOIN('preclasificacion', 'preclasificacion.idpreclasificacion = manifiesto.id_manifiesto', 'INNER')
            ->JOIN('estatus', 'estatus.id_estatus =  manifiesto.id_estatus', 'LEFT')

            ->WHERE('preclasificacion.idpreclasificacion', $idpreclasificacion)
            ->ORDER_BY('manifiesto.tracking_number')
            ->get('manifiesto')
            ->result();

        return $rsl;
    }

    public function buscar_tracking_numb($tracking)
    {
        $query = $this->db
            ->select('ma.*,
                                 ma.tracking_number,
                                 preclasificacion.manifiesto as awb,
                                 pa.numero_partida, 
                                 pa.descripcion descripcion_producto')
            ->join('preclasificacion', 'preclasificacion.idpreclasificacion = ma.id_manifiesto', 'inner')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->where('ma.tracking_number', $tracking)

            ->group_by('ma.tracking_number')
            ->get('manifiesto ma')
            ->result();
        return $query;
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
            //->set('bag_number',                  $datas['bag_number'])
            ->insert('manifiesto');
        return ($this->db->affected_rows() > 0);
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

    public function update_num_guias($id, $cant)
    {
        $this->db
            ->set('n_guias', $cant)
            ->where('idpreclasificacion', $id)
            ->update('preclasificacion');
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

    public function preclasificacion($mawb)
    {
        //	echo $mawb."-".$cadena;
        $query = $this->db
            ->select('*')
            ->where('manifiesto', $mawb)
            ->get('preclasificacion')
            ->result();
        return $query;
    }


    public function preclasificacion_xml($mawb)
    {
        //	echo $mawb."-".$cadena;
        $query = $this->db
            ->select('pre.*,p1.Codigo as puerto1, p2.Codigo as puerto2')
            ->join('puertos p1', 'p1.idPuerto =  pre.puerto_salida', 'left')
            ->join('puertos p2', 'p2.idPuerto =  pre.puerto_salida2', 'left')
            ->where('pre.manifiesto', $mawb)
            ->get('preclasificacion pre')
            ->result();
        return $query;
    }

    public function guardar_transportista_fast($id, $transportista)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->db
            ->set('id_transportista', $transportista)
            ->where('idpreclasificacion', $id)
            ->update('preclasificacion');
    }

    /*public function editar_wh_fast($id)
    {
        $query  =   $this->db
                         ->select('manifiesto.id_manifiesto,
                                   preclasificacion.manifiesto as master,
                                   manifiesto.tracking_number,   
                                   manifiesto.fecha_creacion,    
                                   manifiesto.consignee,         
                                   manifiesto.consignee_address1,
                                   manifiesto.consignee_address2,
                                   manifiesto.gweight,           
                                   manifiesto.commodity,         
                                   manifiesto.cobro_final,       
                                   manifiesto.shipper_address1,  
                                   manifiesto.shipper_address2,  
                                   manifiesto.pieces,            
                                   manifiesto.cuenta,            
                                   manifiesto.origin,            
                                   manifiesto.destination,       
                                   manifiesto.shipper_name')
                         ->join('preclasificacion', 'manifiesto.id_manifiesto = preclasificacion.idpreclasificacion', 'INNER')
                         ->where('manifiesto.id', $id)
                         ->get('manifiesto')
                         ->row();
        return $query;
    }*/
    public function editar_wh_fast($id)
    {
        $query  =   $this->db
            ->select('manifiesto.*,preclasificacion.manifiesto as master,')
            ->join('preclasificacion', 'manifiesto.id_manifiesto = preclasificacion.idpreclasificacion', 'INNER')
            ->where('manifiesto.id', $id)
            ->get('manifiesto')
            ->row();
        return $query;
    }

    public function eliminar_wh_fast($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->db->WHERE('id', $id);
        $this->db->delete("manifiesto");
    }

    public function buscarxfecha($fechadte_desde, $fechadte_hasta)
    {
        $query = $this->db
            ->select('*')
            ->join('usuarios', 'usuarios.id = preclasificacion.userid', 'inner')
            ->join('transportista', 'transportista.id = preclasificacion.id_transportista', 'left')
            ->where('preclasificacion.procesada', '0')
            ->where('preclasificacion.tipo !=', 'TRA')
            ->WHERE("date_format(preclasificacion.fecha, '%Y-%m-%d')>=", $fechadte_desde)
            ->WHERE("date_format(preclasificacion.fecha, '%Y-%m-%d')<=", $fechadte_hasta)
            ->order_by('preclasificacion.fecha', 'DESC')
            ->get('preclasificacion')
            ->result();
        return $query;
    }

    public function paises()
    {
        $query = $this->db
            ->select('*')
            ->get('paises_exp')
            ->result();
        return $query;
    }
    public function provee()
    {
        $query = $this->db
            ->select('*')
            ->where('usar_api', 'N')
            ->get('proveedor_int')
            ->result();
        return $query;
    }

    public function xmlprovee($id)
    {
        $query = $this->db
            ->select('*')
            ->where('id', $id)
            ->get('empresa')
            ->result();
        return $query;
    }


    public function guardar_manifiesto($id, $data)
    {
        if ($id > 0) {
            $this->db->where("id", $id);
            $this->db->update("manifiesto", $data);

            return ($this->db->affected_rows() > 0);
        } else {
            $this->db->insert("manifiesto", $data);
            return $this->db->insert_id();
        }
    }
    public function preclasificacion_id($id)
    {
        $query = $this->db
            ->select('*')
            ->where('idpreclasificacion', $id)
            ->get('preclasificacion')
            ->result();
        return $query;
    }
    public function update_wh_fast($id, $data)
    {
        $this->db->where('idpreclasificacion', $id);
        $this->db->update('preclasificacion', $data);

        return ($this->db->affected_rows() > 0);
    }
    public function  list_transp()
    {
        $query = $this->db
            ->select('*')
            ->get("transportista")
            ->result();
        return $query;
    }
    public function lista_manifiesto_refer($id, $master, $ref)
    {

        $query = $this->db
            ->select('ma.*,pa.numero_partida, pa.descripcion descripcion_producto, pre.manifiesto')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->join('preclasificacion pre', 'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')
            ->where('ma.id_manifiesto', $id)
            ->where('ma.referencia', $ref)
            ->get('manifiesto ma')
            ->result();
        return $query;
    }
    public function xml_manifiesto($ref)
    {
        $query = $this->db
            ->select('manifiesto.*,preclasificacion.manifiesto')
            ->join('preclasificacion', 'manifiesto.id_manifiesto = preclasificacion.idpreclasificacion', 'inner')
            ->where('manifiesto.referencia', $ref)
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function guardar_consolidada($master, $consolidada, $id)
    {
        $this->db
            ->set('guia_consolidada', $consolidada)
            ->where('id_manifiesto', $master)
            ->where('id', $id)
            ->update('manifiesto');
    }

    public function obtener_valores($id)
    {
        $query = $this->db
            ->select('pieces, gweight, cobro_final')
            ->where('id', $id)
            ->get('manifiesto')
            ->row();
        return $query;
    }

    public function guardar_valores($master, $consolidada, $pieces, $gweight, $value)
    {
        $this->db
            ->set('pieces', $pieces)
            ->set('gweight', $gweight)
            ->set('cobro_final', $value)
            ->where('id_manifiesto', $master)
            ->where('tracking_number', $consolidada)
            ->update('manifiesto');
    }

    public function sumar_consolida($master)
    {
        $this->db->select_sum('pieces', 'piezas'); // Seleccionar la suma de 'pieces' y darle un alias 'piezas'
        $this->db->from('manifiesto');  // Especificar la tabla manifiesto
        $this->db->where('id_manifiesto =', $master);  // Filtrar registros donde id_manifiesto >= 479
        $this->db->where('guia_consolidada >', 0);  // Filtrar registros donde guia_consolidada > 0
        $query = $this->db->get();  // Ejecutar la consulta

        // Verificar si hay resultados
        if ($query->num_rows() > 0) {
            return $query->row()->piezas;  // Retornar la suma de piezas
        } else {
            return 0;  // Si no hay resultados, retornar 0
        }
    }
    public function contar_agrupar($master)
    {
        $this->db->select('COUNT(*) as agrupar'); // Seleccionar el conteo de registros y darle un alias 'piezas'
        $this->db->from('manifiesto');  // Especificar la tabla manifiesto
        $this->db->where('id_manifiesto', $master);  // Filtrar registros donde id_manifiesto coincide con $master
        $this->db->where('agrupa', 1);  // Filtrar registros donde agrupar es igual a 1
        $query = $this->db->get();  // Ejecutar la consulta

        // Verificar si hay resultados
        if ($query->num_rows() > 0) {
            return $query->row()->agrupar;  // Retornar el conteo de piezas
        } else {
            return 0;  // Si no hay resultados, retornar 0
        }
    }


    public function agentes()
    {
        $query = $this->db
            ->select('*')
            ->get("agentes")
            ->result();
        return $query;
    }

    public function t_transporte()
    {
        $query = $this->db
            ->select('*')
            ->get("transportestipos")
            ->result();
        return $query;
    }

    public function puertos()
    {
        $query = $this->db
            ->select('*')
            ->get("puertos")
            ->result();
        return $query;
    }

    public function update_FieldManif($tracking_number, $items_quantities,$items_descripction)
    {
        $this->db->set('items_quantities', $items_quantities);
        $this->db->set('commodity', $items_descripction);
        $this->db->where('tracking_number', $tracking_number);
        $this->db->update('manifiesto');

        return ($this->db->affected_rows() > 0);
    }

    public function asignar_rojos($id_master){
        $this->db->set('referencia', "ROJO");
        $this->db->where('id_manifiesto', $id_master);
        $this->db->update('manifiesto');
        return ($this->db->affected_rows() > 0);
    }


    public function asignar_verdes($id_master, $tracking)
    {
        $this->db->set('referencia', "VERDE");
        $this->db->where('id_manifiesto', $id_master);
        $this->db->where('tracking_number', $tracking);
        $this->db->update('manifiesto');

        return ($this->db->affected_rows() > 0);
    }

   

    public function asignar_verdes_temu($id_master, $tracking)
    {
        $this->db->set('referencia', "VERDE");
        $this->db->where('id_manifiesto', $id_master);
        $this->db->where('package_number', $tracking);
        $this->db->update('manifiesto');

        return ($this->db->affected_rows() > 0);
    }


    public function items_apg($manifiesto)
    {
        $query = $this->db
            ->select('id_manifiesto, tracking_number, commodity')
            ->where('id_manifiesto', $manifiesto)
            ->get("manifiesto")
            ->result();
        return $query;
    }

    public function update_items_apg($id_manifiesto, $tracking_number, $cantidad_productos)
    {
        $this->db->set('items', $cantidad_productos);
        $this->db->where('id_manifiesto', $id_manifiesto);
        $this->db->where('tracking_number', $tracking_number);
        $this->db->update('manifiesto');

        return ($this->db->affected_rows() > 0);
    }

    public function get_ReferenciaManifiesto($id_manifiesto)
    {

        $query = $this->db
            ->select('*')
            ->where('id_manifiesto', $id_manifiesto)
            ->order_by('referencia', 'ASC') // Add this line to order by 'id' in ascending order
            ->get("declaraciones")
            ->result();
        return $query;
    }

    public function cargarReferencias($manifiesto, $referencia)
    {
        // $query = $this->db
        //     ->select('*')
        //     ->where('ManifiestoItem', $manifiesto)
        //     ->where('Declaracion', $referencia)
        //     ->get("declaracionesdetalle")
        //     ->result();
        // return $query;

        $query = $this->db
            ->select('*')
            ->join('declaracionesdetalle dd', 'dd.Declaracion =  d.id', 'inner')
            ->where('dd.ManifiestoItem', $manifiesto)
            ->where('dd.Declaracion', $referencia)
            ->get("declaraciones d")
            ->result();
        return $query;
    }

    public function get_infoGuias($id, $referencia)
    {
        return $this->db
            ->select(
                'ma.*,pre.manifiesto, pre.fecha as fecha, p1.nombre puerto_salida,
                        p2.nombre puerto_salida2, ag.Nombre declarante, transportista.nombre_tra, 
                        pa1.nombre paisOrigen, pa2.nombre paisDestino, pre.viaje, pre.vuelo'
            )
            ->join('preclasificacion pre',  'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')
            ->join('transportista',  'transportista.id = pre.id_transportista', 'left')
            ->join('puertos p1',  'p1.idPuerto = pre.puerto_salida', 'left')
            ->join('puertos p2',  'p2.idPuerto = pre.puerto_salida2', 'left')
            ->join('pais pa1',  'pa1.id = ma.id_pais', 'left')
            ->join('pais pa2',  'pa2.id  = ma.pais_destino', 'left')
            ->join('agentes ag',  'ag.idAgente = pre.declarante', 'left')
            ->where('ma.id_manifiesto', $id)
            ->where('ma.referencia', $referencia)
            ->get('manifiesto ma')
            ->result();
    }

    public function clasificar_productos($master, $referencia)
    {
        $query = $this->db
            ->select('de.referencia,dd.*')
            ->join('declaracionesdetalle dd', 'dd.Declaracion =  de.id', 'inner')
            ->where('de.id', $referencia)
            ->get('declaraciones de')
            ->result();
        return $query;
    }

    public function asignarSAC($id, $HSC3, $TAR_PR1)
    {
        $this->db->set('HSC3', $HSC3);
        $this->db->set('TAR_PR1', $TAR_PR1);
        $this->db->where('idItem', $id);
        $this->db->update('declaracionesdetalle');

        // return ($this->db->affected_rows() > 0);
    }

    public function get_infoTracking($id)
    {
        $query = $this->db
            ->select('dec.*,pre.manifiesto,num_reg')
            ->join('declaracionesdetalle dec', 'dec.ManifiestoItem =  pre.idpreclasificacion', 'inner')

            ->where('dec.idItem', $id)
            ->get("preclasificacion pre")
            ->result();
        return $query;
    }

    public function ducas_make_referencia($id, $referencia)
    {
        $this->db
            ->select('de.*, pre.*,
                    po.nombre_espanol AS pais_origen,
                    pp.nombre_espanol AS pais_procedencia,
                    pa.nombre_espanol AS pais_adquisicion')
            ->from('declaracionesdetalle de')
            ->join('preclasificacion pre', 'de.ManifiestoItem = pre.idpreclasificacion', 'inner')
            ->join('manifiesto ma', 'ma.tracking_number = de.DocumentoTransporte')
            ->join('paises_exp po', 'po.id = pre.id_pais_origen', 'left')
            ->join('paises_exp pp', 'pp.id = pre.id_pais_procedencia', 'left')
            ->join('paises_exp pa', 'pa.id = pre.id_pais_adquisicion', 'left');

        if ($referencia != null) {
            $this->db->where('ma.referencia', $referencia);
        }

        $this->db->where('de.ManifiestoItem', $id);

        $query = $this->db->get()
            ->result();

        return $query;
    }

    public function updateInfoTrack($data, $item)
    {
        // Actualiza los datos en la base de datos
        $this->db->where('idItem', $item);
        return $this->db->update('declaracionesdetalle', $data);
    }

}
