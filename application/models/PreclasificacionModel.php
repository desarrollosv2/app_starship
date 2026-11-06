<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class PreclasificacionModel extends CI_Model {
   

    public function pre_clasificacion( $tipo ) {
        $query = $this->db->select( '*' )
        ->where( 'tipo', $tipo )
        ->where( 'id_pais', $_SESSION['pais'] )
        ->get( 'preclasificacion' )
        ->result();
        return $query;
    }

    public function guardar_manifiesto( $id, $data ) {

        $this->db->insert( 'preclasificacion', $data );
        return $this->db->insert_id();

    }

    //guardar encabezafdos

    public function guardar_poliza( $data ) {
        $this->db
        ->set( 'poliza', $data['poliza'] )
        ->where( 'manifiesto', $data['manifiesto'] )
        ->where( 'referencia', $data['referencia'] )
		->where( 'id_pais', $_SESSION['pais'] )
        ->update( 'preclasificacion' );
    }
    //guardar detalles

    public function guardar_polizas( $data ) {
        $this->db
        ->set( 'poliza', $data['poliza'] )
        ->where( 'manifiesto', $data['manifiesto'] )
        ->where( 'referencia', $data['referencia'] )
		->where( 'id_pais', $_SESSION['pais'] )
        ->update( 'warehouse' );
    }

    public function guardar_guia( $data ) {
        $this->db->insert( 'warehouse', $data );
        return $this->db->insert_id();
    }

    public function guardar_detalle( $data ) {
        $this->db->insert( 'detalle', $data );
        return $this->db->insert_id();
    }

    public function consulta_guias( $id ) {
        return $this->db
        ->select( '*' )
        ->where( 'referencia', $id )
		->where( 'id_pais', $_SESSION['pais'])	
        ->get( 'warehouse' )
        ->result();
    }

    public function eliminar_guia( $id ) {
        $this->db->where( 'idwarehouse', $id );
		$this->db->where( 'id_pais', $_SESSION['pais'] );
        $this->db->delete( 'warehouse' );
    }

    public function eliminar_referencia( $id ) {
        $this->db->where( 'idpreclasificacion', $id );
		$this->db->where( 'id_pais', $_SESSION['pais'])	;
        $this->db->delete( 'preclasificacion' );
    }

    public function procesar_p( $id ) {
        $this->db
        ->set( 'procesada', '1' )
        ->where( 'idpreclasificacion', $id )
		->where( 'id_pais', $_SESSION['pais'])		
        ->update( 'preclasificacion' );
    }

    public function consulta_guia_master( $id ) {
      //  $id=146;
        $query = $this->db
        ->where( 'id_manifiesto', $id )
        ->order_by( 'referencia' )
        ->get( 'manifiesto' )
        ->result();
        return $query;
    }
   // ->where( 'id_pais', $_SESSION['pais'])
    public function query_guia_master( $id ) {
       $query = $this->db
         ->select('')
        ->where('idpreclasificacion', $id )
        ->get( 'preclasificacion' )
        ->row();
        return $query;
    }


    public function update_guia( $wh,  $data ) {
        $this->db->where( 'tracking_number', $wh );
        $this->db->update( 'manifiesto', $data );
    }

    public function consulta_referencia( $wh ) {
        return $this->db

        ->select( '*' )

        ->where( 'tracking_number', $wh )
		
		->where( 'id_pais', $_SESSION['pais'] )

        ->get( 'manifiesto' )

        ->result();
    }

    public function consulta_guia( $wh , $master) {
        return $this->db

        ->select( 'tracking_number' )

        ->where( 'tracking_number', $wh )
		
		->where( 'id_manifiesto', $master )

        ->get( 'manifiesto' )

        ->result();
    }

    public function buscar_warehouse_wd( $guia ) {
        return $this->db

        ->select( '*' )

        ->where( 'tracking_number', $guia )
		
		->where( 'id_pais', $_SESSION['pais'] )

        ->get( 'manifiesto' )

        ->result();
    }

    public function actualizar_estatus( $wh, $estatus ) {
        $this->db
        ->set( 'estatus', $estatus )
        ->where( 'idwarehouse', $wh )
        ->update( 'warehouse_detalle' );

        return ( $this->db->affected_rows()>0 );
    }

    public function validar_wh( $mf, $rf, $id ) {

        return  $this->db
        ->select( '*' )
        ->where( 'referencia', $rf )
        ->where( 'manifiesto', $mf )
        ->where( 'numero_warehouse', $id )
        ->get( 'warehouse' )
        ->row();

    }
//log Mail Americas
    public function guardar_logma( $data ) {

        $this->db->insert( 'log_ma', $data );
        return $this->db->insert_id();

    }
    
    public function guardar_guia_master_auto($data){
         $this->db->insert( 'manifiesto', $data );
        return $this->db->insert_id();

    }
    
      public function update_estatus($wh, $estatus) {
     	$this->db
        ->set('id_estatus', $estatus)
        ->where('tracking_number', $wh)
        ->update('manifiesto');

       
    }
    
        public function guardar_master_fast($master)
    {
        $this->db
             ->set('manifiesto',               $master['manifiesto'])
             ->insert('preclasificacion');
        return ($this->db->affected_rows() > 0);    
    }

    public function cantidad_guia($n_guias, $id_manifiesto)
    {
        $this->db
             ->set('n_guias',               $n_guias)
             ->where('idpreclasificacion', $id_manifiesto)
             ->update('preclasificacion');
    }


}

/* End of file PreclasificacionModel.php */
