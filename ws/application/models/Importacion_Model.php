<?php
/*
@desarrollo
*/
defined('BASEPATH') or exit('No direct script access allowed');

/**
* @author desarrollo
*/

class Importacion_Model extends CI_Model
{
   //   se obtiene el correlativo del  comprobante de acuerdo al tipo de comprobante del cliente
    public function obtenerCorrelativoCom($idempresa, $idSucursal, $idcomprobante)
    {
        $DBMA = $this->load->database('contabilidad', TRUE);
      //$query =    $DBMA->set('id_tipo_comprobante', null)
        $query = $DBMA
            ->select('correlativo,serie')
            ->where('id_empresa', $idempresa)
            ->where('id_sucursal', $idSucursal)
            ->where('id_tipo_comp', $idcomprobante)
            ->get("control_correlativos")
            ->row();
        return $query;
    }
    //  funcion para actualizar el correlativo
    public function actualizar_correlativos($id_empresa, $idsucursal, $id_tipo_comp, $nuevoCorrelativo)
    {
        $DBMA = $this->load->database('contabilidad', true);

        $DBMA
               ->set('correlativo', $nuevoCorrelativo)
               ->where('id_empresa', $id_empresa)
               ->where('id_sucursal', $idsucursal)
               ->where('id_tipo_comp', $id_tipo_comp)
               ->update('control_correlativos');
        return ($this->db->affected_rows() > 0);


    }    
    public function obtener_id_encabezado_venta($idEmpresa, $idSucursal, $idCliente, $idTipoComprobante, $numero_comprobante, $fecha_comprobante)
    {
        $DBMA = $this->load->database('contabilidad', true);

        $query = $DBMA
            ->select('id')
            ->select('id_empresa')
            ->select('id_sucursal_empresa')
            ->select('id_cliente')
            ->select('id_tipo_comprobante')
            ->where('id_empresa', $idEmpresa)
            ->where('id_sucursal_empresa', $idSucursal)
            ->where('id_cliente', $idCliente)
            ->where('id_tipo_comprobante', $idTipoComprobante)
            ->where('numero_comprobante', $numero_comprobante)
         ->where("date_format(fecha_comprobante,'%Y-%m-%d')", $fecha_comprobante)
            ->get("encabezados_comprob")
            ->row();
        return $query;


    }
    //  para obtener la descripcion del catalogo de servicios
    public function obtenerDatosCatalogoProductos($idproducto)
    {
        //  no se limita por empresa porque  ambas  empresas tiene  importacion
        $DBMA = $this->load->database('contabilidad', true);

         $query = $DBMA->select(
            'cproduc.id,cproduc.descripcion, cproduc.id_clasificacion_inv,
                                    cproduc.id_tipo_venta, cproduc.id_tipo_mh, 
                                    cproduc.id_presentacion, cproduc.id_tc_producto'
        )
            ->where('cproduc.id', $idproducto)
            ->get('catalogo_productos  as cproduc')
            ->result();
        return $query;


    } 
    


}
