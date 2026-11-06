<?php

defined('BASEPATH') or exit('No direct script access allowed');
class ClasificadoModel extends CI_Model
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

    public function lista_warehouse($id,$master,$correla)
    {
       
        if (!$correla==0){
             $this->db->where('tracking_number',$correla);
             
        }
        $query = $this->db
            ->select('ma.*,pa.numero_partida, pa.descripcion descripcion_producto')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->where('ma.id_manifiesto',$id)
            ->where('ma.clasificado',0)
            //->where('ma.id_partida',0)
            ->get('manifiesto ma')
            ->result();
        return $query;
    }
    
    public function lista_warehouse_confirma($id,$master)
    {
        $query = $this->db
            ->select('ma.tracking_number,ma.consignee_address1,ma.departamento_name, ma.municipio_name,
            ma.departamento, ma.municipio, ma.departamento_id, ma.municipio_id, ma.pickup_number, ma.internal_tracking, ma.tipo_entrega, ma.tipo_servicio, pa.numero_partida')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->where('ma.id_manifiesto',$id)
            ->get('manifiesto ma')
            ->result();
        return $query;
    }
    //  funcion creada para  mostrar  pdf  creado para  detalle de  manifiesto 
       public function lista_warehouse1($id,$referencia)
    {
        $query = $this->db
            ->select('ma.*,pre.manifiesto, pre.fecha as fecha')
            ->join('preclasificacion pre', 'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')
            ->where('ma.id_manifiesto',$id)
            ->where('ma.referencia',$referencia)
            ->get('manifiesto ma')
            ->result();
        return $query;
    }
    
        public function manifiesto_export($id,$referencia)
    {
        $query = $this->db
            ->select('ma.*,pre.manifiesto, pre.fecha as fecha')
            ->join('preclasificacion pre', 'ma.id_manifiesto =  pre.idpreclasificacion', 'inner')
            ->where('ma.id_manifiesto',$id)
            ->get('manifiesto ma')
            ->result();
        return $query;
    }

    public function lista_pendientes_confirmar()
    {
        $query = $this->db
            ->select('*')
            ->where('confirmada','0')
            ->where('facturado','0')
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function lista_confirmados()
    {
       
          $query = $this->db
            ->select('*')
            ->where('confirmada',1)
            ->get('manifiesto')
            ->result();
        return $query;
    }

    public function lista_referencia($id,$ref)
    {
        if ($ref != 'Pendiente'){
             $query = $this->db
            ->select('ma.*,pa.numero_partida, pa.descripcion descripcion_producto')
            ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
            ->where('ma.id_manifiesto',$id)
            //->where('referencia',$ref)
            ->where('ma.clasificado',1)
            ->get('manifiesto ma')
            ->result();
             
        }else{
            $query = $this->db
            ->select('*')
            ->where('id_manifiesto',$id)
            ->where('referencia',$ref)
            ->get('manifiesto')
            ->result();
        }
      
        
        return $query;
    }

    public function reference_list($id)
    {
      
        $query = $this->db
            ->select('*')
            ->where('id_manifiesto',$id)
            ->get('referencia')
            ->result();
        return $query;
    }

    public function seleccionar_item($id,$opc){
       // echo $id."--".$opc;
        $this->db
        ->set( 'seleccionado', $opc)
        ->set( 'user_id',$_SESSION["user_id"] )
        ->where( 'id', $id)
        ->update( 'manifiesto' );
    }

    public function lista_awb()
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
    public function consulta_awb($desde, $hasta, $opcion,$param)
    {
        /* param: determinar si trae manifiesto o referencia*/
        if ($opcion == 3 || $opcion == 4 || $opcion == 5) {
            $this->db->where("fecha BETWEEN '{$desde}' AND '{$hasta}'");
        }
        // if ($opcion == 1) {
        //     $this->db->where('manifiesto',$param);
        // }
        // if ($opcion == 2) {
        //     $this->db->where('referencia',$param);
        // }
        // if ($opcion == 3) {
        //     $this->db->where('procesada','0');
        // }
        // if ($opcion == 4) {
        //     $this->db->where('procesada',1);
        // }

        $query = $this->db
            ->select('*')
            ->where('id_pais',$_SESSION['pais'])
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
    public function  guardar_referencia($data){
        
        $this->db->insert('referencia', $data);
         return ($this->db->affected_rows() > 0);
   
    }

    public function  asignar_guia($master,$ref){
       
        $this->db
        ->set( 'referencia',$ref )
        ->where('id_manifiesto', $master)
        ->where('seleccciona', $_SESSION["user_id"])
        ->where('seleccionado',1)
        ->where( 'referencia','Pendiente')
        ->update( 'manifiesto' );
   
    }

    public function  guardar_comentario($data){
        
        $this->db->insert('registro_llamada', $data);
         return ($this->db->affected_rows() > 0);
   
    }

    public function update_aceptado($id){
         $this->db
         ->set( 'confirmada',1 )
         ->where( 'id', $id)
         ->update( 'manifiesto' );
     }
    public function lista_warehouse_pdf2($id){
        $query = $this->db
            ->select('*')
            //->where('user_id', $_SESSION["user_id"])
           // ->where('seleccionado',1)
            ->where('id',$id)
            ->get('manifiesto')
            ->result();
        return $query;
    }
    public function update_dm($id_manifiesto,$id_d,$codigo_d,$nombre_d,$id_m,$nombre_m,$dui){
        $query =$this->db
        ->set('departamento_id',$id_d)
        ->set('departamento_code',$codigo_d)
        ->set('departamento_name',$nombre_d)
        ->set('municipio_id',$id_m)
        ->set('municipio_name',$nombre_m)
        ->set('departamento',$nombre_d)
        ->set('municipio',$nombre_m)
        ->where('id',$id_manifiesto)
        ->update('manifiesto');
        return $query;
    }
    
    //  funcion para actualizar  valores del  manifiesto desde la carga de archivos  02-10-2023
    public function update_dm1($idpreclasificacion,$tracking_number, $DAI, $IVA, $comision, $MANEJO,$Cobro_final, $total_imp, $total_ivag){
      //  echo   'HECHO  LLEGANDO AL  MODELO DE ACTUALIZACION' ;
        $query =$this->db
        ->set('dai',$DAI)
        ->set('iva',$IVA)
         ->set('comision',$comision)
        ->set('manejo',$MANEJO)
        ->set('cobro_final',$Cobro_final)
        ->set('total_imp',$total_imp)
        ->set('total_ivag',$total_ivag)
        ->where('id_manifiesto',$idpreclasificacion)
        ->where('tracking_number',$tracking_number)
        ->where('facturado', 0)
        ->update('manifiesto');
        return $query;
    }
    
    //  funcion para actualizar  valores del  manifiesto desde la carga de archivos  02-10-2023
    public function update_DUI($idpreclasificacion,$tracking_number, $DUI){
       //echo   'HECHO  LLEGANDO AL  MODELO DE ACTUALIZACION   '.  $idpreclasificacion. '#'.$tracking_number .'#'. $DUI.'<br>';
        $query =  $this->db        
        ->set('consignee_account',$DUI)        
        ->where('id_manifiesto',$idpreclasificacion)
        ->where('tracking_number',$tracking_number)
        
        ->update('manifiesto');
        return $query;
    }
    
    public function cargar_referencia(){
      $query = $this->db
            ->select('*')
            ->get('base_manifiesto')
            ->result();
        return $query;
    }  
 
 
  public function update_ref($awb, $tracking_number, $referencia,  $id_manifiesto){
      //Actualiza las referencias
    

        $query =$this->db
        ->set('referencia', $referencia)
        ->where('tracking_number',$tracking_number)
        ->where('id_manifiesto',$id_manifiesto)
        ->update('manifiesto');
        return $query;
    }
    
     public function verificar_lm($id){
       $query = $this->db
            ->select('id, consignee_city, consignee_state')
            ->where('id_manifiesto',$id)
            ->get('manifiesto')
            ->result();
        return $query;
    }
    
    public function update_lm($id,$depto, $munic, $opc1,  $opc2,  $opc3,  $opc4,  $opc5,  $opc6, $name_d, $name_m, $code  ){
        
        $status_array = array( $opc1,  $opc2,  $opc3,  $opc4,  $opc5,  $opc6);
       

          $this->db
        ->set('departamento_id', $depto)
        ->set('municipio_id',$munic )
        ->set('flag_d',"1" )
        ->set('flag_m',"1" )
        ->set('departamento_name',$name_d )
        ->set('municipio_name',$name_m )
        ->set('departamento_code',$code )
        ->set('tipo_entrega',"NRML")
        ->set('tipo_servicio',"CCE" )
        ->where('id_manifiesto', $id)
         ->where('agencia_destino', 0)
        ->where_in('consignee_city', $status_array)
        ->update( 'manifiesto' );
    }
    
	public function obtener_estatus15(){
	     $query = $this->db
            ->select('warehouse,id_estatus')
            ->where('id_estatus',15)
            ->get('detalle')
            ->result();
        return $query; 
	}
	
	public function update_pod($tracking){
	    
          $this->db
        ->set('id_estatus', '15')
        ->where('tracking_number', $tracking)
        ->update('manifiesto');
	    
	}
	
		public function update_bag($bag, $tracking){
	    
          $this->db
        ->set('bag_number', $bag)
        ->where('tracking_number', $tracking)
        ->update('manifiesto');
	    
	}
	
	
		public function obtener_bags(){
	     $query = $this->db
            ->select('*')
            ->get('bag')
            ->result();
        return $query; 
	}
	
	public function reporte_guias($idpreclasificacion)
    {
        $rsl =	$this->db
                     ->select('preclasificacion.idpreclasificacion,
                               preclasificacion.manifiesto as awb,
                               manifiesto.tracking_number,
                               manifiesto.tracking_replace,
                               manifiesto.referencia,
                               manifiesto.bag_number,
                               manifiesto.consignee_account,
                               manifiesto.value,
                               manifiesto.commodity,
                               manifiesto.gweight,
                               manifiesto.consignee_address1,
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
                                 preclasificacion.manifiesto as awb,
                                 pa.numero_partida, 
                                 pa.descripcion descripcion_producto')
                       ->join('preclasificacion', 'preclasificacion.idpreclasificacion = ma.id_manifiesto', 'inner')
                       ->join('partida_arancel pa', 'pa.id = ma.id_partida', 'left')
                       ->where('ma.tracking_number',$tracking)
                       
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
    
    public function reload_guia(){
         $query = $this->db
            ->select('*')
            ->where('aplicado', 0)
            ->where('id>=', 338)
            //->where('id=', 1)
            ->get('paquete')
            ->result();
        return $query;
    }
    
     public function bandera($id){
    
        $this->db
        ->set( 'aplicado',1 )
        ->where( 'guia', $id)
        ->update( 'paquete' );
    }
    
     public function reload_guia_mario(){
         $query = $this->db
            ->select('*')
            ->where('aplicado', 0)
            ->where('id>=', 5596)
            //->where('id=', 1)
            ->get('paquete_01')
            ->result();
        return $query;
    }
    
     public function bandera_mario($id){
    
        $this->db
        ->set( 'aplicado',1 )
        ->where( 'guia', $id)
        ->update( 'paquete_01' );
    }
    
    public function guia_alternativa($guia){
          $query = $this->db->select('*')
        ->where('tracking_replace',$guia)
        ->get('reemplazo')
        ->result();
        return $query;
    }
    
}
