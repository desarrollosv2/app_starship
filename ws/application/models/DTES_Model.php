<?php

defined('BASEPATH') or exit('No direct script access allowed');
class DTES_Model extends CI_Model
{
    public function guardar_DTE($data, $idVenta)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

       $_SESSION["empresa_idtmp"]  = 47;


        
        $idEncabezadoGuia = '';
        if($idVenta == null) 
        {
        
        } 
        else 
        {
            if(isset($data['id_encabezado_guia']))
            {
               $idEncabezadoGuia = $data['id_encabezado_guia'];
            }
            $DBMA = $this->load->database('contabilidad', TRUE);
                $DBMA->set('cod_generacion',    $data['cod_generacion'])  
                 ->set('numero_control',    $data['numero_control'])  
                 ->set('pdf_path',          $data['pdf_path']) 
                 ->set('dte_prod',          $data['dte_prod']) 
                 ->set('selloRecepcion',    $data['selloRecibido'])
                 ->set('create_api',        $data['create_api'])
                 
                 ->where('id', $idVenta)
                 ->update('encabezados_comprob');

            return($this->db->affected_rows() > 0);
        }
    }

    /* Funcion para la Cabecera Del Cliente */
    /* Funcion para la Cabecera Del Cliente */
    public function DTES_Cabecera($idVenta)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
      

       $DBMA  = $this->load->database('contabilidad', TRUE);
       $query =    $DBMA->SELECT("cl.id, 
                                date_format(enc.fecha_comprobante, '%Y-%m-%d') as fecha_comprobante,
                                enc.id AS id_venta,cl.dias_credito,
                                enc.id_tipo_comprobante,
                                enc.numero_comprobante, 
                                enc.comprobante_afecto,
                                enc.id_comprob_afecto,
                                enc.numero_control,
                                enc.sub_total_ventas,
                                enc.retencion_1_por_ciento, 
                                (enc.sub_total_ventas - coalesce(enc.retencion_1_por_ciento,0)) as venta_propia,
                                enc.sub_total_cuenta_ajena,
                                enc.venta_total,
                                enc.cod_generacion,
                                cl.id_tipo_empresa,
                                enc.id_sucursal_empresa,
                                cl.id as id_cliente,
                                cl.codigo_cliente as codigo_cliente, 
                                cl.nombre_cliente as nombre_cliente, 
                                cl.direccion as direccion, 
                                cl.nombre_contribuyente,
                                cl.id_clasificacion_fiscal, 
                                cl.registro_iva, 
                                cl.giro_iva, 
                                cl.cod_activ_economica,
                                cl.telefono_contacto,
                                dest.id_pais,
                                dest.id_depatamento,
                                dest.id_municipio,
                                cl.dui,
                                cl.nit, 
                                dest.email as email_contacto,
                                cl.correo_cc,
                                cl.email_contacto1,
                                cl.email_contacto2,
                                cl.email_contacto3,
                                cl.id_tipo_pago,
                                tp.descripcion,
                                cl.id_tipo_contribuyente,
                                cl.sujeto_ret_1,
                                munic.cod_departamento,
                                munic.cod_municipio,
                                distr.codigo as codDistrito, 
                                 distr.distrito, suc.esSucursal")
                               

                      ->JOIN('clientes cl ', 'cl.id =  enc.id_cliente', 'INNER')
                      ->JOIN('tipo_pago tp', ' tp.id = cl.id_tipo_pago', 'INNER') 
                      ->JOIN('destinatarios  dest ', 'dest.id_cliente =  cl.id', 'LEFT')
                      ->JOIN('municipios  munic', '(munic.id_departamento =   dest.id_depatamento   and   munic.id =  dest.id_municipio)', 'LEFT')        
                       ->jOIN('distritos distr', ' (distr.distritoID= munic.distritoID  and distr.id_departamento = munic.id_departamento)', 'LEFT')
                       ->JOIN('sucursales_empresa  suc ', 'suc.id =  enc.id_sucursal_empresa', 'LEFT')
                      ->WHERE('enc.anulado IS NULL')
                      ->WHERE('enc.id_empresa', $_SESSION["empresa_id"])  
                      ->WHERE('enc.id', $idVenta)

                      ->get('encabezados_comprob enc')
                      ->row();
        return $query;
    }
    
    public function DTES_Cabecera1($idVenta)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

     $DBMA  = $this->load->database('contabilidad', TRUE);
     $query =    $DBMA->SELECT("cl.id, 
                                date_format(enc.fecha_comprobante, '%Y-%m-%d') as fecha_comprobante,
                                enc.id AS id_venta,cl.dias_credito,
                                enc.id_tipo_comprobante,
                                enc.numero_comprobante, 
                                enc.comprobante_afecto,
                                enc.numero_control,
                                enc.sub_total_ventas, 
                                enc.retencion_1_por_ciento, 
                                enc.venta_total,
                                enc.cod_generacion,
                                cl.id_tipo_empresa,
                                enc.id_sucursal_empresa,
                                cl.id as id_cliente,
                                cl.codigo_cliente as codigo_cliente, 
                                cl.nombre_cliente as nombre_cliente, 
                                cl.direccion as direccion, 
                                cl.nombre_contribuyente,
                                cl.id_clasificacion_fiscal, 
                                cl.registro_iva, 
                                cl.giro_iva, 
                                cl.cod_activ_economica,
                                cl.telefono_contacto,
                                dest.id_pais,
                                dest.id_depatamento,                                  
                                cl.dui,
                                cl.nit, 
                                dest.email as email_contacto,
                                cl.correo_cc,
                                cl.email_contacto1,
                                cl.email_contacto2,
                                cl.email_contacto3,
                                cl.id_tipo_pago,
                                tp.descripcion,
                                cl.id_tipo_contribuyente,
                                cl.sujeto_ret_1")
                      ->JOIN('clientes cl ', 'cl.id =  enc.id_cliente', 'INNER')
                      ->JOIN('tipo_pago tp', ' tp.id = cl.id_tipo_pago', 'INNER') 
                     ->JOIN('destinatarios  dest ', 'dest.id_cliente =  cl.id', 'LEFT')        
                      ->WHERE('enc.anulado IS NULL')
                      ->WHERE('enc.id_empresa', $_SESSION["empresa_id"])  
                      ->WHERE('enc.id', $idVenta)

                      ->get(' encabezados_comprob enc ')
                      ->row();
        return $query;
    }

     /* Funcion para la Cabecera De Las Notas de Credito */
    public function Cabecera_NCR($comprobante_afecto)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //echo($comprobante_afecto);

         $DBMA  = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT("clientes.id, 
                                date_format(encabezados_comprob.fecha_comprobante, '%Y-%m-%d') as fecha_comprobante,
                                encabezados_comprob.id AS id_venta,
                                clientes.dias_credito, 
                                clientes.id_tipo_empresa,
                                encabezados_comprob.id_sucursal_empresa,
                                clientes.codigo_cliente as codigo_cliente, 
                                clientes.nombre_cliente as nombre_cliente, 
                                clientes.direccion as direccion,
                                clientes.nombre_contribuyente,  
                                clientes.id_clasificacion_fiscal, 
                                clientes.registro_iva,  
                                clientes.giro_iva, 
                                clientes.telefono_contacto,
                                (CASE
                                    WHEN clientes.dui > 0 THEN '13'
                                    WHEN clientes.nit > 0 THEN '36'
                                    ELSE 0
                                END) AS tipo_documento,
                                clientes.dui,
                                clientes.nit,
                                clientes.email_contacto,
                                clientes.correo_cc,
                                clientes.id_tipo_pago, 
                                tipo_pago.descripcion, 
                                clientes.id_tipo_contribuyente, 
                                clientes.sujeto_ret_1,
                                encabezados_comprob.id_tipo_comprobante,
                                encabezados_comprob.comprobante_afecto,
                                encabezados_comprob.numero_control,
                                encabezados_comprob.cod_generacion")
                      ->JOIN('encabezados_comprob', 'clientes.id = encabezados_comprob.id_cliente', 'INNER')
                      ->JOIN('tipo_pago', 'clientes.id_tipo_pago = tipo_pago.id', 'INNER')

                      ->WHERE('encabezados_comprob.anulado IS NULL')
                      ->WHERE('encabezados_comprob.id_empresa', $_SESSION["empresa_id"])     
                      ->WHERE('encabezados_comprob.comprobante_afecto', $comprobante_afecto)  
                    //  ->WHERE('encabezados_comprob.id', $idVenta)

                      ->get('clientes')
                      ->result();
        return $query;
    }    

    /* Funcion Para Las Cabeceras de Las Facturas de Exportacion */
    public function Cabecera_FEX($idVenta)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT("clientes.id AS id_cliente, 
                                date_format(encabezados_comprob.fecha_comprobante, '%Y-%m-%d') as fecha_comprobante,
                                clientes.dias_credito, 
                                clientes.id_tipo_empresa,
                                clientes.codigo_cliente as codigo_cliente, 
                                clientes.nombre_cliente as nombre_cliente, 
                                clientes.direccion as direccion,
                                encabezados_comprob.id_sucursal_empresa,
                                clientes.nombre_contribuyente,  
                                clientes.id_clasificacion_fiscal, 
                                clientes.registro_iva,  
                                clientes.giro_iva, 
                                clientes.telefono_contacto,
                                (CASE
                                    WHEN clientes.dui > 0 THEN '13'
                                    WHEN clientes.nit > 0 THEN '36'
                                    ELSE 0
                                END) AS tipo_documento,
                                clientes.dui,
                                clientes.nit,
                                clientes.email_contacto,
                                clientes.correo_cc,
                                clientes.telefono_contacto,
                                clientes.id_tipo_pago, 
                                tipo_pago.descripcion, 
                                clientes.id_tipo_contribuyente, 
                                clientes.sujeto_ret_1,
                                encabezados_comprob.numero_comprobante")
                      ->JOIN('encabezados_comprob', 'clientes.id = encabezados_comprob.id_cliente', 'INNER')
                      ->JOIN('tipo_pago', 'clientes.id_tipo_pago = tipo_pago.id', 'INNER')
                      ->JOIN('kardex', 'encabezados_comprob.id = kardex.id_encabezado_comprobante', 'INNER')
                      ->JOIN('catalogo_productos', 'catalogo_productos.id = kardex.id_catalogo_producto', 'INNER')

                      ->WHERE('encabezados_comprob.anulado IS NULL')
                      ->WHERE('encabezados_comprob.id_empresa', $_SESSION["empresa_id"])     
                      ->WHERE('encabezados_comprob.id', $idVenta)

                      ->get('clientes')
                      ->result();
        return $query;
    }    
      
    /*  Funcion para la Cuerpo De las DTE  */
    public function DTES_Cuerpo($idVenta)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

      $DBMA = $this->load->database('contabilidad', TRUE);
      $query =    $DBMA->SELECT("encabezados_comprob.id as id_venta,
                                kardex.id_tipo_venta, 
                                clientes.id_tipo_pago, 
                                catalogo_productos.id_centro_costos,
                                catalogo_productos.id AS id_catalogo_producto,
                                kardex.cantidad,
                                kardex.descripcion,
                                kardex.precio_neto,
                                catalogo_productos.codigo_anterior,
                                (CASE
                                     WHEN (kardex.kdx_gravado   > 0 OR kardex.kdx_gravado_a_terceros > 0) THEN '1'
                                     WHEN (kardex.kdx_no_sujeto > 0) THEN '2'
                                     WHEN (kardex.kdx_exento    > 0 OR kardex.kdx_exento_a_terceros > 0) THEN '3'
                                END) AS id_hacienda,
                                encabezados_comprob.comprobante_afecto,
                                kardex.kdx_exento, 
                                kardex.kdx_exportacion,
                                kardex.kdx_no_sujeto, 
                                kardex.kdx_gravado, 
                                kardex.descuento,
                                kardex.kdx_exento_a_terceros,
                                kardex.kdx_gravado_a_terceros, 
                                kardex.kdx_cuenta_ajena, 
                                kardex.id_catalogo_producto, 
                                encabezados_comprob.id_tipo_evento,
                                encabezados_comprob.id_tipo_comprobante,
                                encabezados_comprob.serie_factura,
                                (CASE
                                     WHEN kardex.kdx_cuenta_ajena = 0 THEN kardex.kdx_vta_total
                                     ELSE 0
                                END) AS Total,
                                encabezados_comprob.sub_total_ventas,
                                kardex.kdx_vta_total,
                                kardex.kdx_iva_gravado,
                                encabezados_comprob.total_iva_gravado,
                                kardex.kdx_vta_total,
                                encabezados_comprob.venta_total,
                                encabezados_comprob.cod_generacion,
                                encabezados_comprob.pdf_path,
                                encabezados_comprob.numero_control")

                      ->JOIN('encabezados_comprob',' kardex.id_encabezado_comprobante = encabezados_comprob.id','INNER')  
                      ->JOIN('catalogo_productos', 'catalogo_productos.id = kardex.id_catalogo_producto', 'LEFT')
                      ->JOIN('clientes', 'clientes.id = encabezados_comprob.id_cliente', 'INNER')
                      ->JOIN('tipo_contribuyente', 'tipo_contribuyente.id = clientes.id_tipo_contribuyente', 'INNER')

                      ->WHERE('encabezados_comprob.anulado is null')
                      ->WHERE('encabezados_comprob.id_empresa', $_SESSION["empresa_id"])      
                      ->WHERE('encabezados_comprob.id', $idVenta)
                      ->WHERE('kardex.kdx_cuenta_ajena = 0')       // Extraemos todos los tipos de venta a excepcion de la cuenta ajena
                      
                      ->get('kardex')
                      ->result();
        return $query;
    }

    /*  Funcion para la Cuerpo De las Notas de Credito  */
    public function Cuerpo_NCR($comprobante_afecto)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $DBMA = $this->load->database('contabilidad', TRUE);
        $query =    $DBMA->SELECT("encabezados_comprob.id as id_venta,
                                kardex.id_tipo_venta, 
                                clientes.id_tipo_pago, 
                                catalogo_productos.id_centro_costos,
                                catalogo_productos.id AS id_catalogo_producto,
                                kardex.cantidad,
                                kardex.descripcion,
                                kardex.precio_neto,
                                catalogo_productos.codigo_anterior,
                                
                                (CASE
                                    WHEN (kardex.kdx_gravado   > 0 OR kardex.kdx_gravado_a_terceros > 0) THEN '1'
                                    WHEN (kardex.kdx_no_sujeto > 0 ) THEN '2'
                                    WHEN (kardex.kdx_exento    > 0 OR kardex.kdx_exento_a_terceros > 0) THEN '3'
                                END) AS id_hacienda,
                                encabezados_comprob.comprobante_afecto,
                                kardex.kdx_exento, 
                                kardex.kdx_exportacion,
                                kardex.kdx_no_sujeto, 
                                kardex.kdx_gravado, 
                                kardex.descuento,
                                kardex.kdx_exento_a_terceros,
                                kardex.kdx_gravado_a_terceros, 
                                kardex.kdx_cuenta_ajena, 
                                kardex.id_catalogo_producto, 
                                encabezados_comprob.id_tipo_comprobante,
                                encabezados_comprob.serie_factura,

                                (CASE
                                    WHEN kardex.kdx_cuenta_ajena = 0 THEN kardex.kdx_vta_total
                                    ELSE 0
                                END) AS Total,

                                encabezados_comprob.sub_total_ventas,
                                kardex.kdx_vta_total,
                                kardex.kdx_iva_gravado,
                                encabezados_comprob.total_iva_gravado,
                                kardex.kdx_vta_total,
                                encabezados_comprob.venta_total")

                      ->JOIN('encabezados_comprob',' kardex.id_encabezado_comprobante = encabezados_comprob.id','INNER')  
                      ->JOIN('catalogo_productos', 'catalogo_productos.id = kardex.id_catalogo_producto', 'LEFT')
                      ->JOIN('clientes', 'clientes.id = encabezados_comprob.id_cliente', 'INNER')
                      ->JOIN('tipo_contribuyente', 'tipo_contribuyente.id = clientes.id_tipo_contribuyente', 'INNER')

                      ->WHERE('encabezados_comprob.anulado is null')
                      ->WHERE('encabezados_comprob.id_empresa', $_SESSION["empresa_id"])      
                      ->WHERE('encabezados_comprob.comprobante_afecto', $comprobante_afecto)  
                      ->WHERE('kardex.kdx_cuenta_ajena = 0')       // Extraemos todos los tipos de venta a excepcion de la cuenta ajena
                      
                      ->get('kardex')
                      ->result();
        return $query;
    }

    public function DTES_cuerpo_guia_imp($idVenta, $guia_no)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $DBMA  = $this->load->database('contabilidad', TRUE);
        $query =    $DBMA->SELECT("encabezados_comprob.id,
                                guias_import_ss.id_empresa,
                                encabezados_comprob.id_tipo_evento,
                                date_format(encabezados_comprob.fecha_comprobante,'%Y-%m-%d') as fecha_comprobante,
                                guias_import_ss.id_cliente, 
                                clientes.nombre_cliente,
                                encabezados_comprob.id_tipo_comprobante,
                                encabezados_comprob.numero_comprobante,
                                guias_import_ss.wh_numero,
                                kardex.descripcion,
                                kardex.cantidad,
                                (CASE
                                     WHEN (kardex.kdx_gravado   > 0 OR kardex.kdx_gravado_a_terceros > 0) THEN '1'
                                     WHEN (kardex.kdx_no_sujeto > 0) THEN '2'
                                     WHEN (kardex.kdx_exento    > 0 OR kardex.kdx_exento_a_terceros > 0) THEN '3'
                                END) AS id_hacienda,
                                kardex.precio_neto,
                                kardex.kdx_exento,
                                kardex.kdx_exportacion,
                                kardex.kdx_no_sujeto,
                                kardex.kdx_gravado,
                                kardex.kdx_iva_gravado,
                                kardex.kdx_vta_total,  
                                guias_import_ss.total_wh,
                                encabezados_comprob.venta_total")
                      ->JOIN('guias_import_ss', 'guias_import_ss.id_tipo_comprobante = encabezados_comprob.id_tipo_comprobante 
                              AND guias_import_ss.numero_comprobante = encabezados_comprob.numero_comprobante 
                              AND guias_import_ss.id_cliente = encabezados_comprob.id_cliente', 'RIGHT')
                      ->JOIN('clientes', 'clientes.id = guias_import_ss.id_cliente', 'INNER')
                      ->JOIN('kardex', 'kardex.id_encabezado_comprobante = encabezados_comprob.id', 'LEFT')  
                      
                      ->WHERE('guias_import_ss.anulado = 0')
                      ->WHERE('guias_import_ss.id_empresa', $_SESSION["empresa_id"])  
                      ->WHERE('encabezados_comprob.id', $idVenta)
                      ->WHERE('guias_import_ss.wh_numero', $guia_no)
                      ->get('kardex')
                      ->result();
        return $query;
    }

    public function DTES_Cuerpo_Guia_Exp($idVenta, $guia_no)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $DBMA = $this->load->database('contabilidad', TRUE);
        $query =    $DBMA->SELECT("encabezados_comprob.id as id_venta,
                                guia_exp_gc.id_empresa,
                                encabezados_comprob.id_tipo_evento,
                                date_format(guia_exp_gc.fecha,'%Y-%m-%d') as fecha,
                                encabezados_comprob.id_tipo_comprobante,
                                encabezados_comprob.numero_comprobante,
                                guia_exp_gc.guia_no,
                                paises_exp.nombre_espanol AS pais,
                                guia_exp_gc.peso_a_tomar_kgs AS peso,
                                kardex.id_catalogo_producto, 
                                kardex.cantidad,
                                kardex.descripcion,
                                kardex.precio_neto,
                                (CASE
                                     WHEN (kardex.kdx_gravado   > 0 OR kardex.kdx_gravado_a_terceros > 0) THEN '1'
                                     WHEN (kardex.kdx_no_sujeto > 0) THEN '2'
                                     WHEN (kardex.kdx_exento    > 0 OR kardex.kdx_exento_a_terceros > 0) THEN '3'
                                END) AS id_hacienda,
                                kardex.kdx_exento,
                                kardex.kdx_exportacion,
                                kardex.kdx_no_sujeto,
                                kardex.kdx_gravado,
                                kardex.kdx_iva_gravado,
                                kardex.kdx_exento_a_terceros,
                                kardex.kdx_gravado_a_terceros,
                                kardex.kdx_iva_gravado_a_terceros,
                                kardex.kdx_cuenta_ajena,
                                kardex.descuento,
                                kardex.kdx_vta_total,
                                encabezados_comprob.venta_total")

                      ->JOIN('guia_exp_gc', 'guia_exp_gc.id_tipo_comprobante = encabezados_comprob.id_tipo_comprobante AND guia_exp_gc.numero_comprobante = encabezados_comprob.numero_comprobante', 'RIGHT')
                      ->JOIN('paises_exp', 'paises_exp.id = guia_exp_gc.id_pais_destino', 'INNER')
                      ->JOIN('kardex', 'kardex.id_encabezado_comprobante = encabezados_comprob.id', 'LEFT')

                      ->WHERE('encabezados_comprob.anulado IS NULL')
                      ->WHERE('encabezados_comprob.id_empresa', $_SESSION["empresa_id"])   
                      ->WHERE('encabezados_comprob.id', $idVenta)   
                      ->WHERE('guia_exp_gc.guia_no', $guia_no)
                      ->WHERE('kardex.kdx_cuenta_ajena = 0')       // Extraemos todos los tipos de venta a excepcion de la cuenta ajena
                   
                      ->get('encabezados_comprob')
                      ->result();
        return $query;
    }

    /*  Funcion para agregar el valor de cuenta ajena en las DTE  */
    public function DTES_Apendice($idVenta)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

       $DBMA  = $this->load->database('contabilidad', TRUE);
       $query =    $DBMA->SELECT('encabezados_comprob.id as id_venta,
                                kardex.id_encabezado_comprobante, 
                                encabezados_comprob.serie_factura,
                                encabezados_comprob.id_tipo_comprobante,
                                clientes.id_tipo_pago,
                                tipo_comprobante.tipo,
                                encabezados_comprob.comprobante_afecto,
                                kardex.descripcion,
                                kardex.kdx_cuenta_ajena,
                                kardex.kdx_vta_total,
                                encabezados_comprob.venta_total')
                                
                      ->JOIN('encabezados_comprob', 'kardex.id_encabezado_comprobante = encabezados_comprob.id', 'INNER')
                      ->JOIN('catalogo_productos', 'catalogo_productos.id = kardex.id_catalogo_producto', 'LEFT')
                      ->JOIN('tipo_comprobante', 'tipo_comprobante.id = encabezados_comprob.id_tipo_comprobante', 'INNER')
                      ->JOIN('clientes', 'clientes.id = encabezados_comprob.id_cliente', 'INNER')
                      ->JOIN('tipo_contribuyente', 'tipo_contribuyente.id = clientes.id_tipo_contribuyente', 'INNER')

                      ->WHERE('encabezados_comprob.anulado IS NULL')   
                      ->WHERE('encabezados_comprob.id_empresa', $_SESSION["empresa_id"])     
                      ->WHERE('encabezados_comprob.id', $idVenta)
                      ->WHERE('kardex.kdx_cuenta_ajena > 0')            // Extraemos todos los datos de venta de la cuenta ajena

                      ->get('kardex')
                      ->result();
        return $query;
    }

    /* Funcion que muestra los diferentes tipos para invalidar un DTE */
    public function tipo_invalidacion()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

       $DBMA  = $this->load->database('contabilidad', TRUE);
       $query =    $DBMA->SELECT('*')
                      ->get('tipo_invalidacion')
                      ->result();
        return $query;
    }

    /* Funcion para mostrar el Formulario de Invalidacion */
    public function Invalidacion_DTE($id_encabventa)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT('encabezados_comprob.id as id_venta,
                                encabezados_comprob.id_empresa,
                                encabezados_comprob.id_cliente,
                                clientes.codigo_cliente,
                                clientes.nombre_cliente,
                                clientes.dui,
                                clientes.nit, 
                                destinatarios.email as  email_contacto,
                                clientes.correo_cc,                                
                                encabezados_comprob.id_tipo_comprobante,
                                encabezados_comprob.cod_generacion,
                                encabezados_comprob.numero_control,
                                encabezados_comprob.numero_comprobante,                                
                                usuarios.id as id_usuario,
                                usuarios.nombre as nombre_usuario,
                                usuarios.dui_user as dui_user,
                                encabezados_comprob.anulado')
                      ->JOIN('clientes', 'clientes.id = encabezados_comprob.id_cliente', 'INNER')
                      ->JOIN('destinatarios', 'destinatarios.id_cliente = clientes.id', 'INNER')
                      ->JOIN('usuarios', 'usuarios.id = encabezados_comprob.id_usuario', 'INNER')

                      ->WHERE('encabezados_comprob.id_empresa', $_SESSION["empresa_id"])
                      //->WHERE('usuarios.id', $_SESSION["user_id"])
                      ->WHERE('encabezados_comprob.anulado IS NULL')
                      ->WHERE('encabezados_comprob.id', $id_encabventa)
                     
                      ->get('encabezados_comprob')
                      ->result();
        return $query;
    }
    public function Invalidacion_DTE1($id_encabventa)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT('encabezados_comprob.id as id_venta,
                                encabezados_comprob.id_empresa,
                                encabezados_comprob.id_cliente,
                                clientes.codigo_cliente,
                                clientes.nombre_cliente, 
                                clientes.email_contacto,
                                clientes.correo_cc,
                                encabezados_comprob.id_tipo_comprobante,
                                encabezados_comprob.cod_generacion,
                                (CASE
                                    WHEN encabezados_comprob.numero_control IS NULL THEN encabezados_comprob.numero_comprobante
                                    WHEN encabezados_comprob.numero_control IS NOT NULL	THEN encabezados_comprob.numero_control
                                END) AS numero_control,
                                usuarios.id as id_usuario,
                                usuarios.nombre as nombre_usuario,
                                encabezados_comprob.anulado')
                      ->JOIN('clientes', 'clientes.id = encabezados_comprob.id_cliente', 'INNER')
                      ->JOIN('usuarios', 'usuarios.id = encabezados_comprob.id_usuario', 'INNER')

                      ->WHERE('encabezados_comprob.id_empresa', $_SESSION["empresa_id"])
                      ->WHERE('usuarios.id', $_SESSION["user_id"])
                      ->WHERE('encabezados_comprob.anulado IS NULL')
                      ->WHERE('encabezados_comprob.id', $id_encabventa)
                     
                      ->get('encabezados_comprob')
                      ->result();
        return $query;
    } 

    /* Funcion para guardar los datos del Formulario de Invalidacion */
    public function Guardar_Invalidacion($data)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $DBMA = $this->load->database('contabilidad', TRUE);
        $DBMA->insert("reg_transmision", $data);
        var_dump($data);
    } 

    /* Funcion para anular DTE en la tabla encabezado  */
    public function anular_dte($id_encabventa, $fecha)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

           $DBMA = $this->load->database('contabilidad', TRUE);
           $DBMA->set('anulado', 1)  
             ->set('fecha_anulacion', $fecha)  
             ->where('id', $id_encabventa)                
             ->update('encabezados_comprob');
        return ($this->db->affected_rows() > 0); 
    }
   
    /* Funcion para mostrar los datos del Usuario para ser enviados en el evento de invalidacion */
    public function usuarios()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

       $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT('id as id_usuario,
                                nombre as nombre_usuario,
                                dui_user,
                                id_sucursal')
                      ->WHERE("id", $_SESSION["user_id"])
                      ->get('usuarios')
                      ->result();
        return $query;
    }

     /* Funcion para mostrar los datos del Cliente para ser enviados en el evento de invalidacion */
    public function clientes($id_cliente)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT('*')
                      ->WHERE('id_empresa', $_SESSION["empresa_id"])
                      ->WHERE("id", $id_cliente)
                      ->get('usuarios')
                      ->result();
        return $query;
    }
    
//segmento para   validar  datos de campos mandatorios para la  generacion del comprobante  
     public function DatosValidar($id_cliente)
    {
   ini_set('memory_limit','2048M');
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    
      ini_set('slave_max_allowed_packet', '9073741824');
    ini_set('innodb_log_file_size', '3000000');
    ini_set('wait_timeout', '4500000');

  $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT("cl.id,                       
                                cl.codigo_cliente,
                                cl.dias_credito,                                
                                cl.id_tipo_empresa,                                
                                cl.id as id_cliente,                                 
                                cl.nombre_cliente as nombre_cliente, 
                                cl.direccion as direccion, 
                                cl.nombre_contribuyente,
                                cl.id_clasificacion_fiscal, 
                                cl.registro_iva, 
                                cl.giro_iva, 
                                cl.cod_activ_economica,
                                cl.telefono_contacto,
                                dest.id_pais,
                                dest.id_depatamento,
                                dest.id_municipio,
                                cl.dui,
                                cl.nit, 
                                dest.email as email_contacto,
                                cl.correo_cc,                                
                                cl.id_tipo_pago,                                
                                tp.descripcion,
                                cl.id_tipo_contribuyente,cl.id_tipo_comprobante")                              

                     // ->JOIN('clientes cl', 'cl.id =  enc.id_cliente', 'INNER')
                      ->JOIN('tipo_pago tp', ' tp.id = cl.id_tipo_pago', 'INNER') 
                      ->JOIN('destinatarios  dest ', 'dest.id_cliente =  cl.id', 'INNER')  
                      ->WHERE('cl.id_empresa', $_SESSION["empresa_id"])  
                      ->WHERE('cl.id', $id_cliente)

                      ->get('clientes cl')
                      ->row();
        return $query;
    }
    
    //   funcion   para   poner bandera  de reenviar  generacion de  factura electronica 26-10-2023  
    public function  reenviar_gfe($idventa){    
      $DBMA = $this->load->database('contabilidad', TRUE);
              $DBMA->set('reenviar_gfe',1)
                ->where('id', $idventa)            
                ->update('encabezados_comprob');
        return ($this->db->affected_rows() > 0);
    }
    
    //segmento para   validar  datos de campos mandatorios para la  generacion del comprobante  
    public function DatosValidarFC($id_cliente)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
            // cl.dui,
            //cl.nit,
         $DBMA  = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT("cl.id,                       
                                cl.dias_credito,
                                cl.nombre_cliente,
                               
                                cl.sujeto_ret_1,
                                dest.email as email_contacto,
                                cl.id_tipo_pago")
    
                      ->JOIN('destinatarios  dest ', 'dest.id_cliente =  cl.id', 'INNER') 
                      ->JOIN('tipo_pago tp', ' tp.id = cl.id_tipo_pago', 'INNER')                     
                      ->WHERE('cl.id', $id_cliente)
                      ->get('clientes cl')
                      ->row();
        return $query;
    }

    public function DatosValidarCCF($id_cliente)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        // cl.dui, cl.giro_iva,

        $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT("cl.id,                       
                                cl.dias_credito,
                                cl.nombre_cliente,
                                cl.direccion,
                                cl.nit,
                                cl.cod_activ_economica,
                                cl.sujeto_ret_1,
                                dest.email as email_contacto,
                                cl.registro_iva,
                                cl.id_tipo_pago, dest.id_depatamento, dest.id_municipio
                                ")
                     // ->JOIN('clientes cl', 'cl.id =  enc.id_cliente', 'INNER')
                      ->JOIN('tipo_pago tp', ' tp.id = cl.id_tipo_pago', 'INNER') 
                      ->JOIN('destinatarios  dest ', 'dest.id_cliente =  cl.id', 'INNER') 
                      ->WHERE('cl.id', $id_cliente)

                      ->get('clientes cl')
                      ->row();
        return $query;
    }

    public function DatosValidarFEX($id_cliente)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
          // cl.dui,
          $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT("cl.id,                       
                                cl.dias_credito,
                                cl.nombre_cliente,
                                cl.direccion,
                                cl.nit,
                                cl.cod_activ_economica,
                                cl.sujeto_ret_1,
                                dest.email as email_contacto,
                                cl.registro_iva,
                                cl.giro_iva,
                                cl.id_tipo_pago
                                ")
                     // ->JOIN('clientes cl', 'cl.id =  enc.id_cliente', 'INNER')
                      ->JOIN('tipo_pago tp', ' tp.id = cl.id_tipo_pago', 'INNER') 
                      ->JOIN('destinatarios  dest ', 'dest.id_cliente =  cl.id', 'INNER') 
                      ->WHERE('cl.id', $id_cliente)
                      ->get('clientes cl')
                      ->row();
        return $query;
    }
    
    public function tracking_usps($guia_no)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
         
        // $DBMA = $this->load->database('usps', TRUE);
         $query =   $this->db->SELECT("*")
                      ->WHERE('tracking_number', $guia_no)
                      ->get('manifiesto')
                      ->row();
        return $query;
    }
    // funcion  para buscar la guia 
    public function datguia_exp($guia_no)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
         
         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT("*")
                      ->WHERE('guia_no', $guia_no)
                      ->get('guia_exp_gc')
                      ->row();
        return $query;
    }
     public function datventa($id_venta)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
         
         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->SELECT("*")
                      ->WHERE('id', $id_venta)
                      ->get('encabezados_comprob')
                      ->row();
        return $query;
    }
    //  actualizamos el   stus  de las   guias 
      public function updatetracking_usps($id_status, $guia_no)
    {
        
         //echo  'llegando al  modelo para actualizar el  STATUS';
        // $DBMA = $this->load->database('usps', TRUE);
              $this->db->set('id_estatus',$id_status)
                       ->WHERE('tracking_number', $guia_no)          
                        ->update('manifiesto');
        return ($this->db->affected_rows() > 0);
                      
      
    }



}