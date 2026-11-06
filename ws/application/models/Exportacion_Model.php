<?php

defined('BASEPATH') or  exit('No direct script access allowed');
class Exportacion_Model extends CI_Model
{

    public function getItemsFormaG($tracking_number)
    {
        //echo  'los  datos  en  el  modelo  son  '.  $manifiesto . '---'. $referencia . '<br>';
       // $DBMA = $this->load->database('mailamerica', true);
        $query =    $this->db->select(
                                       "manif.tracking_number as  guia_no,
                                       manif.tracking_number as  guia_ibc_no,
                                       manif.commodity as  descripcion,     
                                       manif.gweight  as  peso_neto_lbs, 
                                       (manif.gweight / 2.2046) as  peso_neto_kgs,
                                       (manif.manejo) as  cargo_basico,
                                        coalesce((manif.comision),0)  as  cargo_peso_extra,  
                                        manif.total_ivag as iva,  
                                       (manif.total_imp )  as  iva_adn, 
                                        manif.cobro_final as total_guia"
                                    )
                     ->join('manifiesto as  manif', 'manif.id_manifiesto   =  precla.idpreclasificacion', 'inner')                 
                     ->where('manif.id_estatus', '15')
                    // ->where('manif.tipo_servicio !=', 'SER')
                     ->where('manif.cobro_final>', 0)
                      ->where('manif.facturado', 0)
                     ->where('precla.tipo!=', 'TRA')
                     ->where('manif.tracking_number', $tracking_number)
                     ->get('preclasificacion precla')
                     ->result();
        return $query;
    }    
    //  funcion que valida si la guia ya se encuentra descargada
    public function buscar_guia_tbguias($guia_no)
    {
        //echo 'en el  modelo La guia es '.$guia_no;
        $DBMA = $this->load->database('contabilidad', TRUE);
        $query =    $DBMA->select('*')
                        ->where('guia_no', $guia_no)
                         ->get('guia_exp_gc')
                        ->row();
        return $query;

    }
    // funcion para insertar datos de guia exportacion
    public function guardar_guiaexp($id, $data)
    {
        $DBMA = $this->load->database('contabilidad', true);
        if($id == 0) {  
           $DBMA->insert('guia_exp_gc', $data);   
          return $DBMA->insert_id();
      
        } else {
            $this-> $DBMA
                ->set('id_empresa', $data['id_empresa'])
                ->set('id_prov_exp', $data['id_prov_exp'])
                ->set('id_cliente', $data['id_cliente'])
                ->set('id_cliente_exp', $data['id_cliente_exp'])
                ->set('guia_no', $data['guia_no'])
                ->set('guia_ibc_no', $data['guia_ibc_no'])
                ->set('fecha', $data['fecha'])
                ->set('fecha_ingreso', $data['fecha_ingreso'])
                ->set('hora_ingreso', $data['hora_ingreso'])
                ->set('id_usuario_ingreso', $data['id_usuario_ingreso'])
                ->set('id_tipo_pago', $data['id_tipo_pago'])
                ->set('facturacion_pendiente', $data['facturacion_pendiente'])
                ->set('ciudad_destino', $data['ciudad_destino'])
                ->set('id_pais_destino', $data['id_pais_destino'])
                ->set('id_region_destino', $data['id_region_destino'])
                ->set('id_servicio', $data['id_servicio'])
                ->set('descripcion', $data['descripcion'])
                ->set('valor_monto_declarado', $data['valor_monto_declarado'])
                ->set('no_piezas', $data['no_piezas'])
                ->set('unidad_m', $data['unidad_m'])
                ->set('peso_neto_kgs', $data['peso_neto_kgs'])
                ->set('peso_neto_lbs', $data['peso_neto_lbs'])
                ->set('largo_cms', $data['largo_cms'])
                ->set('alto_cms', $data['alto_cms'])
                ->set('ancho', $data['ancho'])
                ->set('peso_volumen_kgs', $data['peso_volumen_kgs'])
                ->set('peso_volumen_lbs', $data['peso_volumen_lbs'])
                ->set('peso_a_tomar_kgs', $data['peso_a_tomar_kgs'])
                ->set('peso_a_tomar_lbs', $data['peso_a_tomar_lbs'])
                ->set('cargo_basico', $data['cargo_basico'])
                ->set('cargo_peso_extra', $data['cargo_peso_extra'])
                ->set('cargo_recargo_no_doc', $data['cargo_recargo_no_doc'])
                ->set('aplicar_seguro', $data['aplicar_seguro'])
                ->set('id_tarifa_seguro', $data['id_tarifa_seguro'])
                ->set('cargo_seguro', $data['cargo_seguro'])
                ->set('insp_no_intrusiva', $data['insp_no_intrusiva'])
                ->set('cargo_adicional', $data['cargo_adicional'])
                ->set('concepto_cargo_adicional', $data['concepto_cargo_adicional'])
                ->set('subtotal', $data['subtotal'])
                ->set('total_descuentos', $data['total_descuentos'])
                ->set('fuel', $data['fuel'])
                ->set('iva', $data['iva'])
                ->set('iva_adn', $data['iva_adn'])
                ->set('total_guia', $data['total_guia'])
                ->set('id_tipo_comprobante', $data['id_tipo_comprobante'])
                ->set('numero_comprobante', $data['numero_comprobante'])
                ->set('fecha_comprobante', $data['fecha_comprobante'])
                ->set('anulado', $data['anulado'])
                ->set('id_tipo_comprobante_anulado', $data['id_tipo_comprobante_anulado'])
                ->set('numero_comprobante_anulado', $data['numero_comprobante_anulado'])
                ->set('fecha_comprobante_anulado', $data['fecha_comprobante_anulado'])
                ->set('seleccionado', $data['seleccionado'])
                ->set('porc_fuel', $data['porc_fuel'])
                ->where('id', $id)
                ->update('guia_exp_gc');
           return $DBMA->affected_rows();
        }
    }
    // fncion para poner seleccionado  un paquebte para su facturacion
    public function seleccionar_guia($guia_no, $seleccionado)
    {
        echo  'llegando al  modelo' . $guia_no .   ' '. $seleccionado . 'user'. $_SESSION["user_id"]. ' ';
        $DBMA = $this->load->database('contabilidad', true);

        if($seleccionado == 1) {
           $DBMA->set('id_usuario', $_SESSION["user_id"]);
        } else {
            $DBMA->set('id_usuario', null);
        }
        $DBMA->set('seleccionado', $seleccionado)


        //->where('id_empresa', $_SESSION["empresa_id"])
        ->where('guia_no', $guia_no)
        ->update('guia_exp_gc');
        return ($this->db->affected_rows() > 0);

    }
    //  funcion para actualizar  los  datos de los paquetes MA
    public function update_guiaMA($guia_no, $dataGM)
    {
         $DBMA = $this->load->database('contabilidad', true);

        $DBMA->set('cargo_basico', $dataGM['cargo_basico'])
            ->set('cargo_peso_extra', $dataGM['cargo_peso_extra'])
            ->set('iva_adn', $dataGM['iva_adn'])
            ->set('iva', $dataGM['iva'])
            ->set('total_guia', $dataGM['total_guia'])
            ->where('guia_no', $guia_no)
            ->where('facturacion_pendiente', 0)
            ->update('guia_exp_gc');
        return($DBMA->affected_rows());

    }
    //  fincion  para   obtener   datos  de   guia  de mail americas
    public function getdatosGuiaMA($tracking_number)
    {
        //$DBMA = $this->load->database('mailamerica', true);
        $query = $this->db->select('tracking_number, manejo, comision, total_imp,  total_ivag, cobro_final')
                       ->join('usps.preclasificacion  as  precla', 'precla.idpreclasificacion  = manif.id_manifiesto', 'inner')
                      ->where('manif.tracking_number', $tracking_number)
                      ->where('precla.tipo!=', 'TRA')
                      ->get("usps.manifiesto as  manif")
                      ->row();
        return $query;
    }
    //  obtener  datos  de la   guia de exportacion
    public function getdatosGuiaEXP($guia_no)
    {

         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->select("gexp.guia_no,	gexp.cargo_basico,	gexp.cargo_peso_extra,	gexp.iva_adn,	gexp.iva,	gexp.total_guia")
                                ->where('gexp.guia_no', $guia_no)
                                ->get('guia_exp_gc as  gexp')
                                ->row();
        return $query;

    }
    // funcion para  obtener los clientes  agrupados de las  guias  MA 
    public function obtenerClienteAgrupadosMA()
    {
        //echo  'obteniendo  cliente  ma  ' . '<br>';
         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->select('cl.id, SUBSTRING(cl.codigo_cliente,4,12) as codigo_cliente,  
                                    cl.nombre_cliente, cl.direccion, cl.nombre_contribuyente, 
                                    cl.id_clasificacion_fiscal, cl.registro_iva, cl.nit, cl.id_tipo_pago, tp.descripcion,    
		                            cl.id_tipo_contribuyente, cl.sujeto_ret_1, cl.id_tipo_comprobante')
              ->join('clientes cl', 'gexp.id_cliente =  cl.id', 'inner')
              ->join('tipo_pago tp', 'cl.id_tipo_pago = tp.id', 'inner')
             // ->where('gimpss.id_encab_guia_ss', $idEncaImport)
              ->where('gexp.facturacion_pendiente', 0)  // esto  indicara que hay warehouse pendientes a facturar
              ->where('gexp.id_cliente', 12165)
              ->where('gexp.id_usuario', $_SESSION["user_id"])
              //->where('gimpss.id_encab_guia_ss', $idEncaImport)
              //->where('cl.id_empresa', $_SESSION["empresa_id"]) //  variable  de sesion
              ->where('cl.id_empresa', 47) //  variable  de sesion
              //->where('cl.id', '91')  // se realiza prueba solo con un cliente
             // ->or_where('cl.id', '2142')
              ->group_by('cl.id')
              ->get('guia_exp_gc  as gexp')
              ->result();
        return $query;


    }
    // funciones para  proceso de  facturacion   11-12-2023
    public function obtner_valores_detalle_GuiaMA($guia_no)
    {
        //echo 'ingresando al modelo para  obtener las   guias ';
         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->select('case when gexp.id_servicio = 1  then  (gexp.cargo_basico + gexp.cargo_peso_extra+ gexp.cargo_recargo_no_doc + coalesce(gexp.cargo_adicional,0)) else 0 end   as D15')
                  ->select('case when gexp.id_servicio = 2  then  (gexp.cargo_basico + gexp.cargo_peso_extra+ gexp.cargo_recargo_no_doc + coalesce(gexp.cargo_adicional,0)) else 0 end   as N15')
                  ->select('case when gexp.id_servicio = 3  then  (gexp.cargo_basico + gexp.cargo_peso_extra+ gexp.cargo_recargo_no_doc + coalesce(gexp.cargo_adicional,0)) else 0 end   as N48')
                  ->select('gexp.cargo_seguro                        as cargo_seguro')
                  ->select('gexp.insp_no_intrusiva                    as insp_no_intrusiva')
                  ->select('gexp.fuel                                 as fuel')
                  ->select('gexp.iva_adn                              as iva_adn')
                  ->select('gexp.subtotal                             as subtotal')
                  ->select('gexp.total_descuentos                     as total_descuentos')
                  ->select('gexp.iva                                  as iva')
                  ->select('gexp.total_guia                           as total_guia')
                  ->select('gexp.guia_no                              as guia_no')

                  ->where('gexp.seleccionado', 1)
                  ->where('gexp.id_usuario', $_SESSION["user_id"])
                  ->where('gexp.guia_no', $guia_no)
                  ->order_by('gexp.id')
                  ->where('gexp.facturacion_pendiente', 0)
                  ->where('gexp.ma', 1)
                  ->limit('10')

                  ->get('guia_exp_gc gexp')
                  ->result();
        return $query;
    }
    //  actualiza en manifiesto  el estado de  facturado
    public function update_facturadoUSPS($tracking_number)
    {
        //echo  'en el  modelo de  actualizacion  usp ' . $tracking_number  .  '<br>';
        //$DBMA = $this->load->database('mailamerica', true);
        $this->db->set('facturado', 1)
        ->where('tracking_number', $tracking_number)
        ->update('manifiesto');
        
       
        return ($this->db->affected_rows() > 0);

    }
    // obtener los  datos del  kardex para la facturacion electronica
    public function obtener_servicios_facturadosFE($id_enc_Comprobante)
    {
        //var_dump('en el  modelo servicios facturados el id_encab_compo es: '.$id_enc_Comprobante ); concat('$',k.venta_neta)as 'kdx_gravado',
         $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->select(
            " k.id_tipo_venta, k.cantidad,concat(k.descripcion,':') as 'descripcion',
                                         k.precio_neto,
                                         k.kdx_exento,
                                         k.kdx_exportacion,
                                         kdx_no_sujeto,
                                         k.kdx_gravado,
                                         k.kdx_iva_gravado,
                                         k.kdx_exento_a_terceros,
                                         k.kdx_gravado_a_terceros,
                                         k.kdx_iva_gravado_a_terceros,
                                         k.kdx_cuenta_ajena,
                                         k.kdx_vta_total,
                                         k.descuento,
                                         k.id_catalogo_producto,
                                         encab.id_tipo_comprobante,
                                         (CASE
                                            WHEN (k.kdx_gravado   > 0 OR k.kdx_gravado_a_terceros > 0) THEN '1'
                                            WHEN (k.kdx_no_sujeto > 0 OR k.kdx_exportacion > 0) THEN '2'
                                            WHEN (k.kdx_exento    > 0 OR k.kdx_exento_a_terceros > 0) THEN '3'
									                          WHEN (k.kdx_cuenta_ajena > 0) THEN '0'
                                        END) AS id_hacienda"
        )
                    ->join('encabezados_comprob encab', 'k.id_encabezado_comprobante = encab.id ', 'inner')
                    ->where('k.id_encabezado_comprobante ', $id_enc_Comprobante)
                    ->get('kardex  k')
                    ->result();
        return $query;


    }
    // funcion  para asignar el  tipo  de comprobante, el numero  y la fecha en la que el paquete se facturo
    public function asigna_datosdfacturacion($id_venta, $id_cliente, $guia_no, $data)
    {
        $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->set('id_venta', $id_venta)
        ->set('id_tipo_comprobante', $data['id_tipo_comprobante'])
        ->set('numero_comprobante', $data['numero_comprobante'])
        ->set('fecha_comprobante', $data['fecha_comprobante'])
        ->set('facturacion_pendiente', $data['facturacion_pendiente'])
        ->set('seleccionado', $data['seleccionado'])
        ->where('id_empresa', 47)
        ->where('id_cliente', $id_cliente)
        ->where('guia_no', $guia_no)
        ->update('guia_exp_gc');
        return ($this->db->affected_rows() > 0);

    }
     // obtener las  guias  exportacion  facturadas para  el proceso  de  facturacion electronica 08-09-2023
    public function guias_expfacturadasFE($id_venta)
    {
        // echo 'en el modelo los datos recibidos   '.  $id_tipo_comprobante.  ' '.$numero_comprobante. '   '.$id_cliente;
         $DBMA  = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->select("gexp.fecha, gexp.ma,  gexp.guia_no, pexp.cod_pais, pexp.nombre_espanol as destino, gexp.iva,
                                            rexp.id  as z,  ( case 
                                                                when tsexp.descripcion  ='D' then 'D'
                                                                when tsexp.descripcion  ='S' then 'N'
                                                                else  '' 
                                                            end
                                                ) as d_n,  gexp.peso_a_tomar_kgs as peso, (gexp.cargo_basico + gexp.cargo_peso_extra + gexp.cargo_recargo_no_doc+ coalesce(gexp.cargo_adicional,0)) as  cargo_basico,gexp.subtotal,coalesce(gexp.concepto_cargo_adicional,0) as concepto_cargo_adicional, cl.id_tipo_contribuyente")
                    ->join('clientes cl', 'cl.id = gexp.id_cliente', 'inner')
                    ->join('paises_exp pexp', 'gexp.id_pais_destino = pexp.id', 'inner')
                    ->join('regiones_exp rexp', 'gexp.id_region_destino = rexp.id', 'inner')
                    ->join('servicios_exp  sexp', 'gexp.id_servicio = sexp.id', 'inner')
                    ->join('tipo_servicio_exp tsexp', 'sexp.id_tipo_servicio = tsexp.id', 'inner')
                    ->where('gexp.id_venta', $id_venta)
                    ->limit('10')
                    ->get('guia_exp_gc gexp')
                    ->result();
        return $query;


    }

    public function comparar($guia)
    {
      
        $valor = 'usps.manifiesto.tracking_number = "' . $guia . '"  OR  usps.manifiesto.tracking_replace= "' . $guia . '"';
        $query  =   $this->db->select('(CASE
                                            WHEN usps.manifiesto.tracking_replace != "" THEN usps.manifiesto.tracking_replace
                                            WHEN usps.manifiesto.tracking_replace = "" THEN usps.manifiesto.tracking_number
                                        END) AS tracking,                                
                                        
                                        usps.preclasificacion.manifiesto,
                                        usps.manifiesto.referencia')
        ->JOIN('usps.preclasificacion', 'usps.preclasificacion.idpreclasificacion = usps.manifiesto.id_manifiesto', 'INNER')
        ->WHERE('usps.preclasificacion.tipo !=', 'TRA')          
            ->WHERE($valor)
            ->get('usps.manifiesto')
            ->result();
        return  $query;
    }
}
