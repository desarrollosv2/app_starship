<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ventas_Model extends CI_Model
{
     public function guardar_enca($data, $idVenta){
        $DBMA = $this->load->database('contabilidad', true);

        $idEncabezadoGuia = '';
        if ($idVenta == null) {
        } else {
                  if (isset( $data['id_encabezado_guia'])){
                        $idEncabezadoGuia = $data['id_encabezado_guia'];
                        //echo  'el  id del  encabezado a almacenar es' . $idEncabezadoGuia .'guia';
                  }
            //echo 'se actualizara el encabezado !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ';
            $DBMA
                    ->set('id_empresa',             $data['id_empresa'])
                    ->set('id_sucursal_empresa',    $data['id_sucursal_empresa'])
                    ->set('id_cliente',             $data['id_cliente'])
                    ->set('nombrecliente',          $data['nombrecliente'])
                    ->set('documentopresentar',     $data['documentopresentar'])
                    ->set('id_destinatario',        $data['id_destinatario'])
                    ->set('id_tipo_comprobante',    $data['id_tipo_comprobante'])
                    ->set('numero_comprobante',     $data['numero_comprobante'])
                    ->set('comprobante_afecto',     $data['comprobante_afecto'])
                    ->set('id_comprob_afecto',      $data['id_comprob_afecto'])
                    ->set('ot_numero',              $data['ot_numero'])
                    ->set('fecha_comprobante',      $data['fecha_comprobante']) //fjdkfd
                    ->set('total_exento',           $data['total_exento'])
                    ->set('total_exportacion',      $data['total_exportacion'])
                    ->set('total_no_sujeto',        $data['total_no_sujeto'])
                    ->set('total_gravado',          $data['total_gravado'])
                    ->set('total_iva_gravado',      $data['total_iva_gravado'])
                    ->set('sub_total_ventas',       $data['sub_total_ventas'])
                    ->set('retencion_1_por_ciento', $data['retencion_1_por_ciento'])
                    ->set('sub_total_cuenta_ajena', $data['sub_total_cuenta_ajena'])
                    ->set('venta_total',            $data['venta_total'])
                    ->set('id_encabezado_guia',     $idEncabezadoGuia) 
                    ->set('id_tipo_evento',         $data['id_tipo_evento']) 
                    ->set('fact_manual',            $data['fact_manual'])
                    ->set('id_usuario',             $_SESSION["user_id"] )     
                    ->where('id', $idVenta)
                    ->update('encabezados_comprob');
            return ($DBMA->affected_rows() > 0);
        }
    }

     //  funcion  para  guardar el  encabezado de la factura metodo generico  
    public function guardar_venta($data)
    {
        $DBMA = $this->load->database('contabilidad', true);

       $DBMA->insert("encabezados_comprob", $data);
        return $DBMA->insert_id();
    }

    public function guardar_detalle_venta($data, $id_detalle)    
    {        
        $DBMA = $this->load->database('contabilidad', true);

        if ($id_detalle == null) {
            echo  'Insertando en el kardex';
            return $DBMA->insert("kardex", $data);
        } else {           

            $DBMA
        ->set('no_guia', $data['no_guia'])//nwarehouse
        ->set('id_catalogo_producto',       $data['id_catalogo_producto'])
        ->set('descripcion',                $data['descripcion'])
        ->set('cantidad',                   $data['cantidad'])
        ->set('precio_neto',                $data['precio_neto'])
        ->set('venta_neta',                 $data['venta_neta'])
        ->set('id_tipo_venta',              $data['id_tipo_venta'])
        ->set('kdx_exento',                 $data['kdx_exento'])
        ->set('kdx_exportacion',            $data['kdx_exportacion'])
        ->set('kdx_no_sujeto',              $data['kdx_no_sujeto'])
        ->set('kdx_gravado',                $data['kdx_gravado'])
        ->set('kdx_iva_gravado',            $data['kdx_iva_gravado'])
        ->set('kdx_exento_a_terceros',      $data['kdx_exento_a_terceros'])
        ->set('kdx_gravado_a_terceros',     $data['kdx_gravado_a_terceros'])
        ->set('kdx_iva_gravado_a_terceros', $data['kdx_iva_gravado_a_terceros'])
        ->set('kdx_cuenta_ajena',           $data['kdx_cuenta_ajena'])
        ->set('kdx_vta_total',              $data['kdx_vta_total'])
        ->where('id', $id_detalle)
        ->update('kardex');
            return ($DBMA->affected_rows() > 0);
        }
    }

    // retorna los valores del  cardex 
    public function calcularTotalesventa($idVenta)
    {
        echo 'el id para  calcular los  totales de las  ventas es ' .  $idVenta .  'end'; 

       $DBMA = $this->load->database('contabilidad', true);

        $query = $DBMA
                        ->select('sum(venta_neta)                    as totalVentaNeta')
                        ->select('sum(kdx_exento)                    as totalKardexExento')
                        ->select('sum(kdx_exportacion)               as totalKardexExportacion')
                        ->select('sum(kdx_no_sujeto)                 as totalKardexNosujeto')
                        ->select('sum(kdx_gravado)                   as totalKardexGravado')
                        ->select('sum(kdx_iva_gravado)               as totalKardexIvaGravado')
                        ->select('sum(kdx_exento_a_terceros)         as totalKardexExentoAterceros')
                        ->select('sum(kdx_gravado_a_terceros)        as totalKardexGravadoAterceros')
                        ->select('sum(kdx_iva_gravado_a_terceros)    as totalKardexIvaGravadoAterceros')
                        ->select('sum(kdx_cuenta_ajena)              as totalKardexCuentaAjena')
                        ->select('sum(kdx_vta_total)                 as totalKardexVentaTotal')

                        ->where('id_encabezado_comprobante', $idVenta)
                        ->get('kardex')
                        ->result();
                return $query;
    }
    public function buscarIdCxC($idVenta)
    {
        $DBMA = $this->load->database('contabilidad', true);

        $query = $DBMA->select('id')
                            ->where("id_comprob_venta", $idVenta)
                            ->get("det_cxc")
                            ->row();
        return $query;
    }
    public function guardar_detalle_cxc($dataCxC, $idcxc)
    {
        $DBMA = $this->load->database('contabilidad', true);

        if ($idcxc == null) {
            echo 'Insertando la cuenta por  cobrar ';
            return $DBMA->insert("det_cxc", $dataCxC);
        } else {
            echo 'se  actualizaran los  valores  de la cuenta  por  cobrar';
            $DBMA
                  //  ->set('id_cliente',             $dataCxC['id_cliente'])
                  //  ->set('id_destinatario',        $dataCxC['id_destinatario'])
                  //  ->set('id_empresa',             $dataCxC['id_empresa'])
                  //  ->set('id_comprob_venta',       $dataCxC['id_comprob_venta']) 
                  //->set('num_comrpobante',        $dataCxC['num_comrpobante'])
                  //  ->set('id_tipo_comprobante',    $dataCxC['id_tipo_comprobante'])
                    //->set('id_comprob_afecto',      $dataCxC['id_comprob_afecto'])
                    ->set('valor_cargo',            $dataCxC['valor_cargo'])
                    //->set('valor_abono',            $dataCxC['valor_abono'])
                   // ->set('fecha_emision',          $dataCxC['fecha_emision'])

                    ->where('id', $idcxc)
                    ->update('det_cxc');
          return ( $DBMA->affected_rows() > 0);
        }
    }    
    //   filtramos  los  datos de  venta  por  id  
    public function ventasid($id)
    {
        $query = $this->db
                        ->select('ec.*, e.nombre as nombreEmpresa, se.nombre as nombreSucursalEmpresa, c.id as id_cliente,
                                    c.nombre_cliente as nombreCliente,c.id_tipo_contribuyente as id_tipo_contribuyente ,
                                    des.nombre as nombreDestinatario, tc.tipo as tipoComprobante,c.id_tipo_pago,c.codigo_cliente, c.direccion'
                                )
                        ->join('empresa e',             'ec.id_empresa = e.id','inner')
                        ->join('sucursales_empresa se', 'ec.id_sucursal_empresa = se.id','inner')
                        ->join('clientes c',            'ec.id_cliente = c.id','inner')
                        ->join('tipo_comprobante tc',   'ec.id_tipo_comprobante = tc.id','inner')
                        ->join('destinatarios des',     'ec.id_destinatario = des.id','left')
                        ->where('ec.id', $id)
                        ->where('ec.id_empresa', $_SESSION["empresa_id"])
                        ->where('ec.anulado is null')                        
                        ->where('ec.venta_total >', 0)
                        ->order_by('ec.fecha_comprobante', 'DESC')
                        ->get('encabezados_comprob ec')
                        ->row();
        return $query;
    }
    public function datoscabecera_factura($fechainicio, $fechafin)
    {
                $this->db->select('IFNULL(cl.nombre_cliente,"sin nombre") as  nombre_cliente ,tcomp.id as id_tipo_comprobante,
                       tcomp.tipo as tipo_comprobante, 
                       cl.registro_iva,
                       coalesce(encab.numero_control,0)numero_control,
                       coalesce(encab.cod_generacion,0)cod_generacion,
                       coalesce(encab.selloRecepcion,0) as selloRecepcion,                       
                       encab.numero_comprobante as numero_comprobante,
                       encab.fecha_comprobante as fecha_comprobante ,if(encab.anulado= 1,1,0) as anulado,
                       sum(k.kdx_exento) as total_exento,            
                       sum(k.kdx_no_sujeto) as total_no_sujeto,            
                       sum(k.kdx_gravado) as total_gravado,
                       sum(k.kdx_iva_gravado) as total_iva_gravado, 
                       sum(k.kdx_cuenta_ajena) as sub_total_cuenta_ajena,
                       sum(k.kdx_exento_a_terceros) as exento_a_terceros,
                       sum(k.kdx_gravado_a_terceros) as gravado_a_terceros,
                       sum(k.kdx_iva_gravado_a_terceros) as iva_gravado_a_terceros,
                       sum(k.kdx_exento_a_terceros +k.kdx_gravado_a_terceros+ k.kdx_iva_gravado_a_terceros) as vtterceros,
                       coalesce(encab.retencion_1_por_ciento,0) as retencion,
                       sum(k.kdx_exportacion) as  total_exportacion,
                       (encab.venta_total - encab.sub_total_cuenta_ajena)  as  venta_total'               
                        );
                    $this->db->From("encabezados_comprob encab")    ; 
                    $this->db->join('kardex k', 'k.id_encabezado_comprobante=encab.id', 'inner') ;   
                    $this->db->join('clientes cl', 'encab.id_cliente=cl.id', 'inner');
                    $this->db->join('tipo_comprobante tcomp', 'encab.id_tipo_comprobante=tcomp.id', 'inner');
                    $this->db->where('encab.id_empresa', $_SESSION["empresa_id"]);
                    // ->where("encab.anulado is null")  
                    $this->db->where("date_format(encab.fecha_comprobante,'%Y-%m-%d')>=", $fechainicio);
                    $this->db->where("date_format(encab.fecha_comprobante,'%Y-%m-%d')<=", $fechafin);
                    //->where("encab.id_tipo_comprobante",1)
                    $this->db->where("tcomp.id",1);
                    $this->db->where("k.id_tipo_venta",1);
                    $this->db->group_by('encab.id');
                    //$this->db->order_by("encab.numero_comprobante", "ASC");
                    //$this->db->order_by("encab.fecha_comprobante", "ASC");
                   
                  //  ->get('encabezados_comprob encab')
                   // ->result();
               // return $query;
        
        
                $queryx =$this->db->get_compiled_select() ;
                
         $this->db->select(
            'IFNULL(cl.nombre_cliente,"sin nombre") as  nombre_cliente ,tcomp.id as id_tipo_comprobante,
                       tcomp.tipo as tipo_comprobante, 
                       cl.registro_iva, 
                       coalesce(encab.numero_control,0)numero_control,
                       coalesce(encab.cod_generacion,0)cod_generacion,
                       coalesce(encab.selloRecepcion,0) as selloRecepcion, 
                       encab.numero_comprobante as numero_comprobante ,
                       encab.fecha_comprobante as fecha_comprobante,if(encab.anulado= 1,1,0) as anulado,
                       sum(k.kdx_exento) as total_exento,            
                       sum(k.kdx_no_sujeto) as total_no_sujeto,            
                       sum(k.kdx_gravado) as total_gravado,
                       sum(k.kdx_iva_gravado) as total_iva_gravado, 
                       sum(k.kdx_cuenta_ajena) as sub_total_cuenta_ajena,
                       sum(k.kdx_exento_a_terceros) as exento_a_terceros,
                       sum(k.kdx_gravado_a_terceros) as gravado_a_terceros,
                       sum(k.kdx_iva_gravado_a_terceros) as iva_gravado_a_terceros,
                       sum(k.kdx_exento_a_terceros +k.kdx_gravado_a_terceros+ k.kdx_iva_gravado_a_terceros) as vtterceros,
                       coalesce(encab.retencion_1_por_ciento,0) as retencion,
                       sum(k.kdx_exportacion) as  total_exportacion,
                       (encab.venta_total - encab.sub_total_cuenta_ajena)  as  venta_total'
        );
         $this->db->From("encabezados_comprob encab")    ;
         $this->db->join('kardex k', 'k.id_encabezado_comprobante=encab.id', 'inner') ;
         $this->db->join('clientes cl', 'encab.id_cliente=cl.id', 'inner');
         $this->db->join('tipo_comprobante tcomp', 'encab.id_tipo_comprobante=tcomp.id', 'inner');
         $this->db->where('encab.id_empresa', $_SESSION["empresa_id"]);
         // ->where("encab.anulado is null")
         $this->db->where("date_format(encab.fecha_comprobante,'%Y-%m-%d')>=", $fechainicio);
         $this->db->where("date_format(encab.fecha_comprobante,'%Y-%m-%d')<=", $fechafin);
         //->where("encab.id_tipo_comprobante",1)
         $this->db->where("tcomp.id", 3);
        $this->db->where("k.id_tipo_venta", 1);
        
         $this->db->group_by('encab.id');
           $queryz =$this->db->get_compiled_select() ;
        
        return $this->db->query($queryx." UNION ".$queryz.'ORDER BY fecha_comprobante')->result();
    }
    //   funcion   para   poner bandera  de reenviar  generacion de  factura electronica  
    public function  reenviar_gfe($idventa){    
        $this->db->set('reenviar_gfe',1)
                ->where('id', $idventa)            
                ->update('encabezados_comprob');
        return ($this->db->affected_rows() > 0);
    }

    //  funcion  para   retormar  el  evento con  el que  se genero el  comprobante
    public function retorna_tipo_evento_ventas($id)
    {
            $query = $this->db
                            ->select('enc.id_tipo_evento, enc.cod_generacion'
                                    )        
                            ->where('enc.id', $id)                           
                            ->get('encabezados_comprob enc')
                            ->row();
            return $query;
    }





    
}

/* End of file ModelName.php */