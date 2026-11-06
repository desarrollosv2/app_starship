<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Partidas_Model extends CI_Model
{
    // FUNCION PARA VER LOS TIPOS DE PARIDAS
    public function ver_partidas()
    {
     

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $DBMA = $this->load->database('contabilidad', true);
        $query =  $DBMA
                      ->SELECT('*')
                      ->WHERE('id_empresa', 47)

                      ->get('tipo_partida')
                      ->result();
        return $query;
    }

    // Funcion para obtener los datos de cabecera de la partida
    public function obtenerdatos_cabecera_partida($codigo_partida)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $DBMA = $this->load->database('contabilidad', true);
        $query =   $DBMA
                      ->SELECT('tpart.id, 
                                tpart.nombre_partida, 
                                correlapart.correlativo, 
                                correlapart.id_anio_partida,
                                correlapart.id_mes_partida')

                      ->JOIN('correlativo_tipo_partida  correlapart', 'correlapart.id_tipo_partida = tpart.id', 'inner')

                      ->WHERE('tpart.id_empresa', 47)
                      ->WHERE('tpart.codigo_partida', $codigo_partida)
                      ->WHERE('correlapart.activo', 1)

                      ->get('tipo_partida tpart')
                      ->result();
        return $query;
    }

    // Obteniendo el consolidado para el debe de los clientes
    public function obtenerdatos_detalleclientes_partida($tipogenerar, $idventa, $fecha)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $DBMA = $this->load->database('contabilidad', true);
        

        if($tipogenerar == 1) {
            $DBMA->WHERE("enc.id", $idventa);
        } else {
            $DBMA->WHERE("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha) ;
        }

        $query = $DBMA
                      ->SELECT('catconta.cuenta_contable,
                                enc.id_cliente, 
                                cl.nombre_cliente, 
                                enc.id_empresa,
                                enc.fecha_comprobante,
                                sum(enc.venta_total - enc.sub_total_cuenta_ajena) as debe')

                      ->JOIN('clientes cl', 'enc.id_cliente = cl.id', 'inner')
                      ->JOIN('catalogo_contable catconta', 'cl.id_catalogo_contable = catconta.id', 'inner')

                      ->WHERE('enc.anulado is null')
                      //->WHERE('enc.id_tipo_comprobante !=  3')
                      ->WHERE('enc.id_empresa', 47)

                      ->GROUP_BY('cl.id')
                      ->ORDER_BY('cl.id')

                      ->get('encabezados_comprob enc')
                      ->result();
        return $query;
    }

    // Obtenemos el IVA venta del comprobante facturado
    public function obtenerdatos_ivanventas_partidaind($idventa, $tipocomprobante)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //$this->db->query($query1." UNION ".$query2)->result();
        // echo  'para la suma de los iva ventas es  idventa ' . $idventa . 'El tipo de comprobante es ' .$tipocomprobante;
        /*
            if($tipocomprobante == 1)
            {
                $this->db->where('enc.id_tipo_comprobante', 1);
            }
            if($tipocomprobante == 2 || $tipocomprobante ==  7)
            {
                $this->db->where('enc.id_tipo_comprobante', 2);
                $this->db->or_where('enc.id_tipo_comprobante', 7);
            }
        */
        //echo  'la fecha en  el  modelo es '  . $fecha;
        // ->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha)
        $DBMA = $this->load->database('contabilidad', true);

        $query  =    $DBMA
                         ->SELECT("enc.id_tipo_comprobante,
                                   sum(enc.total_iva_gravado) as total_iva")
         //$this->db->from('encabezados_comprob enc');
                         ->WHERE("enc.id", $idventa)
                          //$this->db->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha);
                         ->WHERE('enc.anulado is null')
                         ->WHERE('enc.id_empresa', 47)
                         ->get('encabezados_comprob enc')
                         ->result();
        return $query;
        //$this->db->where('enc.id_tipo_comprobante',1);
        /*  $query1= $this->db->get_compiled_select();
            $this->db->select("'2' as ivconsumidores,enc.id_tipo_comprobante, coalesce(sum(enc.total_iva_gravado),0) as total_iva");
            $this->db->from('encabezados_comprob enc');
            $this->db->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha);
            $this->db->where('enc.anulado is null');
            $this->db->where('enc.id_empresa', 47);
            $this->db->where('enc.id_tipo_comprobante', 2);
            $this->db->or_where('enc.id_tipo_comprobante', 7);

        $query2= $this->db->get_compiled_select(); */
        // $this->db->query($query1." UNION ".$query2)->result();
    }

    // Obteniendo los datos del detalle de la partida de los servicios
    public function obtenerdatos_ivanventas_partida($fecha)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $DBMA = $this->load->database('contabilidad', true);


        //echo  'la fecha en  el  modelo es '  . $fecha;
        // ->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha)
         $DBMA->select("'1' as ivconsumidores,
                                enc.id_tipo_comprobante,
                                sum(enc.total_iva_gravado) as total_iva, 
                                coalesce(sum(enc.retencion_1_por_ciento),0) as total_ret");
         $DBMA->from('encabezados_comprob enc');
         $DBMA->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha);
         $DBMA->where('enc.anulado is null');
         $DBMA->where('enc.id_empresa', 47);
        //$this->db->where('enc.id_tipo_comprobante', 1);
        $query1 = $DBMA->get_compiled_select();

         $DBMA->select("'2' as ivconsumidores,
                                enc.id_tipo_comprobante, 
                                coalesce(sum(enc.total_iva_gravado),0) as total_iva,
                                coalesce(sum(enc.retencion_1_por_ciento),0) as total_ret");
         $DBMA->from('encabezados_comprob enc');
         $DBMA->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha);
         $DBMA->where('enc.anulado is null');
         $DBMA->where('enc.id_empresa', 47);
         $DBMA->where('enc.id_tipo_comprobante', 2);
         $DBMA->or_where('enc.id_tipo_comprobante', 7);

        $query2 =  $DBMA->get_compiled_select();

        return  $DBMA->query($query1 . " UNION " . $query2)->result();
    }

    // Obteniendo los datos del detalle de la partida de los servicios
    public function obtenerdatos_detalleservicios_partida($tipogenerar, $idventa, $fecha)
    {
      
        $DBMA = $this->load->database('contabilidad', true);


        if ($tipogenerar == 1) {
             $DBMA->where("enc.id", $idventa);
        } else {
             $DBMA->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha) ;
        }
        // echo  'la fecha en  el  modelo es '  . $fecha;
        $query =  $DBMA
                      ->distinct()
                      ->select('ctconta.cuenta_contable, 
                                enc.fecha_comprobante, 
                                enc.id_tipo_comprobante,
                                ctprod.id, 
                                ctprod.descripcion, 
                                (k.kdx_vta_total - k.kdx_iva_gravado) as haber')

                      ->join('kardex k', 'k.id_encabezado_comprobante =  enc.id ', 'inner')
                      ->join('catalogo_productos ctprod', 'k.id_catalogo_producto =  ctprod.id', 'inner')
                      ->join('catalogo_contable ctconta', 'ctprod.id_cuentaconta_ventas   = ctconta.id', 'inner')
                      //->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha)
                      //->where('enc.fecha_comprobante',$fecha)
                      ->where('enc.anulado is null')
                      ->where('ctprod.id_tipo_mh !=', 4)
                      ->where('ctprod.id_tipo_venta', 1)
                      ->where('(enc.id_tipo_comprobante != 3 AND enc.id_tipo_comprobante != 5 AND enc.id_tipo_comprobante != 6)')
                      //->where('ctprod.id_empresa', 47)
                      ->where('enc.id_empresa', 47)
                      ->get('encabezados_comprob enc')
                      ->result();
        return $query;
    }

    // creando el encabezado de partida
    public function guardar_partida($data, $id)
    {
        //echo  'llegando al  modeo para  insertar la partida de venta';
        $DBMA = $this->load->database('contabilidad', true);


        if($id == null) {
           // echo  'INSERTANDO PARTIDA';


            $DBMA->insert("encabezados_partida", $data);
            return $DBMA->insert_id();
        } else {
            $DBMA 
                 ->set('id_empresa', $data['id_empresa'])
                 ->set('idventa', $data['idventa'])
                 ->set('id_banco', $data['id_banco'])
                 ->set('id_usuario', $data['id_usuario'])
                 ->set('id_proveedor', $data['id_proveedor'])
                 ->set('id_tipo_partida', $data['id_tipo_partida'])
                 ->set('id_vendedor', $data['id_vendedor'])
                 ->set('tipo_partida', $data['tipo_partida'])
                 ->set('numero_partida', $data['numero_partida'])
                 ->set('fecha_ingreso', $data['fecha_ingreso'])
                 ->set('concepto', $data['concepto'])
                 ->set('total_cargos', $data['total_cargos'])
                 ->set('total_abonos', $data['total_abonos'])
                 ->set('ultima_linea', $data['ultima_linea'])
                 ->set('valor_cheque', $data['valor_cheque'])
                 ->set('nombre_cheque', $data['nombre_cheque'])
                 // ->set('fecha_creacion', $data['fecha_creacion'])

                 ->where('id', $id)
                 ->update('encabezados_partida');
            return ( $DBMA->affected_rows() > 0);
        }
    }

    // funcion para  guardar el  detalle de partida
    public function guardar_detalle_partida($data, $id)
    {
      $DBMA = $this->load->database('contabilidad', true);


        if ($id == null) {
            return $DBMA->insert("detalle_partidas", $data);
        } else {
            $DBMA
                 ->set('id_encabezado', $data['id_encabezado'])
                 ->set('id_empresa', 47)
                 ->set('id_tipo_comprobante', $data['id_tipo_comprobante'])
                 ->set('numero_partida', $data['numero_partida'])
                 ->set('fecha_ingreso', $data['fecha_ingreso'])
                 ->set('linea', $data['linea'])
                 ->set('cuenta_contable', $data['cuenta_contable'])
                 ->set('concepto', $data['concepto'])
                 ->set('valor_cargo', $data['valor_cargo'])
                 ->set('valor_abono', $data['valor_abono'])
                 ->set('orden_impresion', $data['orden_impresion'])
                 ->set('numero_cxp', $data['numero_cxp'])
                 ->set('fecha_cxp', $data['fecha_cxp'])
                 ->set('prov_cxp', $data['prov_cxp'])

                 ->where('id', $id)
                 ->update('detalle_partidas');
            return ($DBMA->affected_rows() > 0);
        }
    }

    //  funcion  para  obtener los totales  del detalle de partida
    public function sumar_detalle_partida($id)
    {
        $DBMA = $this->load->database('contabilidad', true);

        $query = $DBMA
                      ->select('coalesce(sum(detpart.valor_cargo),0) as cargo,  
                                coalesce(sum(detpart.valor_abono),0) as abono')
                      ->join('encabezados_partida  encpart', 'detpart.id_encabezado  =  encpart.id', 'inner')
                      ->where('detpart.id_encabezado', $id)
                      ->get('detalle_partidas detpart')
                      ->result();
        return $query;
    }
    // metodo para obtener los datos dela cabecera del  reporte de partida de venta
    public function get_datos_cabecerapartidavt($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $DBMA = $this->load->database('contabilidad', true);


        // echo  'el  id que se recibe como parametro en el  MODEL es ' .  $id;
        $query = $DBMA 
                      ->select('tipo_partida,  
                                numero_partida,  
                                fecha_ingreso, 
                                total_cargos, 
                                total_abonos')
                      ->where('id', $id)
                      ->where('id_empresa', 47)
                      ->get('encabezados_partida')
                      ->result();
        return $query;
    }

    // funcion  para  obtener el detalle de la partida
    public function get_datos_detallepartidavt($id_encabezado)
    {
        $DBMA = $this->load->database('contabilidad', true);


        $query = $DBMA
                      ->select('cuenta_contable, 
                                concat(cuenta_contable,substr(concepto,1,4)) as codigocliente, 
                                concepto,  
                                sum(valor_cargo) as valor_cargo, 
                                sum(valor_abono) as valor_abono, 
                                orden_impresion, 
                                cuenta_mayor')
                      ->where('id_encabezado', $id_encabezado)
                      ->group_by('codigocliente')
                      ->order_by('valor_cargo DESC, cuenta_contable')
                      ->get('detalle_partidas')
                      ->result();
        return $query;
    }


    // funcion para ver todas las partidas creadas
    public function ver_partidas_creadas()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $DBMA = $this->load->database('contabilidad', true);


        $query  =    $DBMA
                         ->select('*')
                         ->where('encabezados_partida.id_empresa', 47)
                        ->get('encabezados_partida')
                        ->result();
        return $query;
    }

    // funcion para mostrar el detalle de partidas  individuales
    public function get_detalle_partida($idventa)
    {
      
        $DBMA = $this->load->database('contabilidad', true);

        $query  =   $DBMA
                        ->select('encvent.id, 
                                  encpart.numero_partida,
                                  encpart.total_cargos, 
                                  encpart.total_abonos,
                                  detpart.cuenta_contable,
                                  detpart.concepto,
                                  detpart.valor_cargo, 
                                  detpart.valor_abono, 
                                  detpart.orden_impresion')
                        ->join('encabezados_partida encpart', 'encpart.idventa  =   encvent.id', 'inner')
                        ->join('detalle_partidas detpart', 'detpart.id_encabezado  =  encpart.id', 'inner')
                        ->where('encvent.id', $idventa)
                        ->where('encpart.id_empresa', 47)
                        ->where('encpart.anulado', 0)
                        ->get('encabezados_comprob encvent')
                        ->result();
        return $query;
    }

    //  funcion  para actualizar el correlativo de las patidas
    public function actualizar_correlativo_partida($nuevocorrelativo, $id_tipo_partida, $idaniopartida, $idmespartida)
    {
       
        $DBMA = $this->load->database('contabilidad', true);

         $DBMA
             ->set('correlativo', $nuevocorrelativo)
             ->where('id_tipo_partida', $id_tipo_partida)
             ->where('id_anio_partida', $idaniopartida)
             ->where('id_mes_partida', $idmespartida)
             ->where('id_empresa', 47)
             ->where('activo', 1)
             ->update('correlativo_tipo_partida');
        return ($DBMA->affected_rows() > 0);
    }

    // funcion para anular la  partida de venta
    public function anular_partida_venta($idventa)
    {
       
        $DBMA = $this->load->database('contabilidad', true);


        $DBMA
             ->set('anulado', 1)
             ->where('idventa', $idventa)
             ->where('anulado', 0)
             ->update('encabezados_partida');
        return ($DBMA->affected_rows() > 0);
    }

    //funcion  para obtener los datos de las partidas  por abonos a cuentas
    public function get_detalle_bancabonocuenta_cliente($id_abono)
    {
        $DBMA = $this->load->database('contabilidad', true);


        //echo  'el  id que se recibe como parametro en el  MODEL es ' .  $id;
        $query = $DBMA
                      ->select('bnc.id as  id_banco,
                                fpag.descripcion as formapago,
                                coalesce(bnc.nombre,"Sin banco") as nombre, 
                                enc_r.id_tipo_comprob, 
                                enc_r.correlativo_rec,
                                enc_r.total_abonos as debe, 
                                coalesce(cat_cont.cuenta_contable,"Sin cuenta") as cuenta_contable,
                                enc_r.numero_referencia')
                      ->join('forma_pago fpag', 'enc_r.id_forma_pago = fpag.id', 'inner')
                      ->join('bancos bnc', 'enc_r.id_banco = bnc.id', 'left')
                      ->join('ctas_bancarias  cta_bac', 'cta_bac.id  = enc_r.id_cuenta_banc', 'left')
                      ->join('catalogo_contable cat_cont', 'cat_cont.id = cta_bac.codigo_contable', 'left')
                      ->where('enc_r.id', $id_abono)
                      ->where('enc_r.abono_a_cuenta', 1) // 1- indica que es abono a cuenta
                      ->where('enc_r.id_empresa', 47)
                      ->get('encab_rec enc_r')
                      ->result();
        return $query;
    }

    // funcion para obtener el  detalle de abonos a cuenta de clientes
    public function get_detalle_abonocuenta_cliente($id_abono)
    {
       $DBMA = $this->load->database('contabilidad', true);


        //echo  'el id que se recibe como parametro en el MODEL es ' .$id;
        $query =  $DBMA
                      ->select('enc.numero_comprobante, 
                                detcxc.valor_abono as abono, 
                                catalogoc.cuenta_contable')

                      ->join('encabezados_comprob enc', 'detcxc.id_comprob_afecto = enc.id', 'inner')
                      ->join('clientes cl', 'cl.id = enc.id', 'inner')
                      ->join('catalogo_contable catalogoc', 'cl.id_catalogo_contable = catalogoc.id', 'inner')

                      ->where('detcxc.id_encabezado_rec', $id_abono)
                      ->where('detcxc.id_empresa', 47)
                      ->get('det_cxc detcxc')
                      ->result();
        return $query;
    }

    // para partidas consolidadas
    // generar partida individual
    /*public function obtenerdato_clientes_partida($idventa)
    {
       //echo  'la fecha en  el  modelo es '  . $fecha;
       $query = $this->db->
       select('catconta.cuenta_contable,enc.id_cliente, cl.nombre_cliente, enc.id_empresa,
               enc.fecha_comprobante,sum(enc.venta_total - enc.sub_total_cuenta_ajena) as  debe')
       ->join('clientes cl', 'enc.id_cliente = cl.id', 'inner')
       ->join('catalogo_contable catconta', 'cl.id_catalogo_contable = catconta.id', 'inner')
       //->join('catalogo_contable ctconta', 'ctprod.id_cuentaconta_ventas   = ctconta.id', 'left')
       //->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha)
       ->where("enc.id", $idventa)
       ->where('enc.anulado is null')
       ->where('enc.id_tipo_comprobante !=  3' )
       ->where('enc.id_empresa',  47)
       ->group_by('cl.id')
       ->order_by('cl.id')
       ->get('encabezados_comprob enc')
       ->result();
       return $query;
       //->where('enc.id_empresa', 47)
    }*/
    // generar partida individual
    /*public function obtenerdatos_ivanventa_partida($idventa)
    {
       // echo  'la fecha en  el  modelo es '  . $fecha;
       // ->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')", $fecha)
        $this->db->select("'1' as ivconsumidores,enc.id_tipo_comprobante,sum(enc.total_iva_gravado) as total_iva");
                    $this->db->from('encabezados_comprob enc');
                    $this->db->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')",$idventa);
                    $this->db->where('enc.anulado is null');
                    $this->db->where('enc.id_empresa', 47);
                    $this->db->where('enc.id_tipo_comprobante',1);
        $query1= $this->db->get_compiled_select();
        $this->db->select("'2' as ivconsumidores,enc.id_tipo_comprobante, coalesce(sum(enc.total_iva_gravado),0) as total_iva");
                    $this->db->from('encabezados_comprob enc');
                    $this->db->where("date_format(enc.fecha_comprobante,'%Y-%m-%d')",$idventa);
                    $this->db->where('enc.anulado is null');
                    $this->db->where('enc.id_empresa', 47);

                    $this->db->where('enc.id_tipo_comprobante', 2);
                    $this->db->or_where('enc.id_tipo_comprobante', 7);

        $query2= $this->db->get_compiled_select();
   return $this->db->query($query1." UNION ".$query2)->result();

    }*/

    // Obteniendo los datos de la retencion 1% de la partida
    public function obtenerdatos_retencion_partida($fecha, $idventa)
    {
       $DBMA = $this->load->database('contabilidad', true);


        $query = $DBMA 
                    ->SELECT('*')
                    ->WHERE('encabezados_comprob.id', $idventa)
                    ->WHERE("date_format(encabezados_comprob.fecha_comprobante,'%Y-%m-%d')", $fecha)
                    ->WHERE('encabezados_comprob.retencion_1_por_ciento > 0')
                    ->WHERE('encabezados_comprob.id_empresa', 47)
                    ->get('encabezados_comprob')
                    ->result();
        return $query;
    }

    // obteniendo los datos del detalle de partidas
    public function obtenerdatos_servicios_partida($fecha, $idventa)
    {
        $DBMA = $this->load->database('contabilidad', true);


        $query =  $DBMA
                    ->SELECT("encabezados_comprob.id_empresa,
                                encabezados_comprob.id_tipo_comprobante,
                                encabezados_comprob.numero_comprobante,
                                date_format(encabezados_comprob.fecha_comprobante,'%d-%m-%Y') as fecha_comprobante,
                                kardex.cantidad,
                                kardex.descripcion,
                                kardex.id_catalogo_producto,
                                catalogo_productos.codigo_anterior,
                                kardex.precio_neto,
                                kardex.kdx_exento,
                                kardex.kdx_exportacion,
                                kardex.kdx_no_sujeto,
                                kardex.kdx_gravado,
                                catalogo_productos.id_cuentaconta_ventas,
                                catalogo_contable.cuenta_contable")
                    ->JOIN('kardex', 'kardex.id_encabezado_comprobante = encabezados_comprob.id', 'INNER')
                    ->JOIN('catalogo_productos', 'catalogo_productos.id = kardex.id_catalogo_producto', 'INNER')
                    ->JOIN('catalogo_contable', 'catalogo_contable.id = catalogo_productos.id_cuentaconta_ventas', 'LEFT')
                    ->WHERE('encabezados_comprob.id', $idventa)
                    ->WHERE("date_format(encabezados_comprob.fecha_comprobante,'%Y-%m-%d')", $fecha)
                    ->WHERE('encabezados_comprob.id_empresa', 47)
                    ->WHERE('(encabezados_comprob.id_tipo_comprobante = 3 OR encabezados_comprob.id_tipo_comprobante = 5 OR encabezados_comprob.id_tipo_comprobante = 6)')
                    ->WHERE('kardex.kdx_cuenta_ajena = 0')
                    ->get('encabezados_comprob')
                    ->result();
        return $query;
    }

    //  funcion  para  obtener el detalle de la partida de NCR
    public function get_datos_detallepartidapn($id)
    {
        $DBMA = $this->load->database('contabilidad', true);


        $query = $DBMA
                    ->SELECT('cuenta_contable, 
                                concat(cuenta_contable, substr(concepto,1,4)) as codigocliente, 
                                concepto,  
                                sum(valor_cargo) as valor_cargo, 
                                sum(valor_abono) as valor_abono, 
                                orden_impresion, 
                                cuenta_mayor')
                    ->WHERE('id_encabezado', $id)
                    ->GROUP_BY('codigocliente')
                    ->ORDER_BY('valor_abono, cuenta_contable')
                    ->get('detalle_partidas')
                    ->result();
        return $query;
    }

}
