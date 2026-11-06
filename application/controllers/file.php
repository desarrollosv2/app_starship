<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');

defined('BASEPATH') or exit('No direct script access allowed');
//include getcwd() . "/libraries/RestController.php";

require(APPPATH . '/libraries/REST_Controller.php');
//use Restserver\Libraries\RestController;
class Api extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Casillero_model');
        $this->load->model('Warehouse_model');
        $this->load->model('Usuario_model');
        $this->load->model('Manifiesto_model');
        $this->load->model('Cotizador_model');
    }

    public function casillero_get()
    {
        $id = $this->uri->segment(3);

        if (!isset($id)) {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'Es necesario el número de casillero'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if ($id == " ") {
        } else {
            $id = str_replace("%20", " ", $id);
        }
        $casillero = $this->Casillero_model->get_casillero($id);
        if (($casillero)) {
            $respuesta = array(
                'err' => false,
                'mensaje' => 'Registro cargado correctamente',
                'cliente' => $casillero
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'El casillero con el número ' . $id . ', no existe ',
                'casillero' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }


    public function warehouse_get()
    {
        $id = $this->uri->segment(3);

        if (!isset($id)) {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'Es necesario el número de warehouse'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if ($id == " ") {
        } else {
            $id = str_replace("%20", " ", $id);
        }
        $warehouse = $this->Warehouse_model->get_warehouse($id);

        if (($warehouse)) {
            $respuesta = array(
                'err' => false,
                'mensaje' => 'Registro cargado correctamente',
                'cliente' => $warehouse
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'El warehouse ' . $id . ', no existe ',
                'warehouse' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }


    public function estatus_get()
    {
        $id = $this->uri->segment(3);

        if (!isset($id)) {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'Es necesario el número de warehouse'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if ($id == " ") {
        } else {
            $id = str_replace("%20", " ", $id);
        }
        $warehouse = $this->Warehouse_model->get_estatus($id);

        if (($warehouse)) {
            $respuesta = array(
                'err' => false,
                'mensaje' => 'Registro cargado correctamente',
                'cliente' => $warehouse
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'El warehouse ' . $id . ', no existe ',
                'warehouse' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }



    public function correo_get()
    {
        $id = $this->uri->segment(3);

        if (!isset($id)) {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'Es necesario el número de warehouse'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if ($id == " ") {
        } else {
            $id = str_replace("%20", " ", $id);
        }
        $warehouse = $this->Warehouse_model->get_estatus($id);

        if (($warehouse)) {
            $respuesta = array(
                'err' => false,
                'mensaje' => 'Registro cargado correctamente',
                'cliente' => $warehouse
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'El warehouse ' . $id . ', no existe ',
                'warehouse' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function login_get()
    {
        $email = $this->uri->segment(3);
        $pass = $this->uri->segment(4);


        $datosparm = array(

            'correo' => $email,

            'clave'   =>  $pass,

        );


        $credenciales = $this->Usuario_model->getLogin($datosparm);

        if (($credenciales)) {
            $respuesta = array(
                'err' => false,
                'mensaje' => 'las credenciales son correctas',
                'login' => $credenciales
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'Credenciales incorrectas ',
                'login' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function password_post()
    {
        $data = $this->post();

        $this->load->library('form_validation');

        $this->form_validation->set_data($data);


        if ($this->form_validation->run('password_post')) {

            $hecho = $this->Usuario_model->update_password($data);

            if ($hecho == true) {
                $respuesta = array(
                    'err' => FALSE,
                    'mensaje' => 'Registro actualizado correctamente',
                );

                $this->response($respuesta);
            } else {
                $respuesta = array(
                    'err' => TRUE,
                    'mensaje' => 'Error al actualizar',
                );

                $this->response($respuesta);
            }
        } else {
            $respuesta = array(
                'err' => TRUE,
                'mensaje' => 'Hay errores en el envio de informacion',
                'errores' => $this->form_validation->get_errores_arreglo()
            );

            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function mail_post()
    {
        $data = $this->post();

        $this->load->library('form_validation');

        $this->form_validation->set_data($data);
        // $casillero = $this->uri->segment(3);
        //$casillero = 'SAL 50005';
        //$data['casillero'] = $casillero;

        if ($this->form_validation->run('mail_post')) {

            $hecho = $this->Usuario_model->update_correo($data);

            if ($hecho == true) {
                $respuesta = array(
                    'err' => FALSE,
                    'mensaje' => 'Registro actualizado correctamente',
                );

                $this->response($respuesta);
            } else {
                $respuesta = array(
                    'err' => TRUE,
                    'mensaje' => 'Error al actualizar',
                );

                $this->response($respuesta);
            }
        } else {
            $respuesta = array(
                'err' => TRUE,
                'mensaje' => 'Hay errores en el envio de informacion',
                'errores' => $this->form_validation->get_errores_arreglo()
            );

            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function sucursal_post()
    {
        $data = $this->post();

        $this->load->library('form_validation');

        $this->form_validation->set_data($data);


        if ($this->form_validation->run('sucursal_post')) {

            $hecho = $this->Usuario_model->update_sucursal($data);

            if ($hecho == true) {
                $respuesta = array(
                    'err' => FALSE,
                    'mensaje' => 'Registro actualizado correctamente',
                );

                $this->response($respuesta);
            } else {
                $respuesta = array(
                    'err' => TRUE,
                    'mensaje' => 'Error al actualizar',
                );

                $this->response($respuesta);
            }
        } else {
            $respuesta = array(
                'err' => TRUE,
                'mensaje' => 'Hay errores en el envio de informacion',
                'errores' => $this->form_validation->get_errores_arreglo()
            );

            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function registro_put()
    {

        $data = $this->put();

        $this->load->library('form_validation');

        $this->form_validation->set_data($data);
        if ($this->form_validation->run('registro_put')) {

            $registro = array(
                'nombre' => $data['nombre'],
                'correo' => $data['correo'],
                'telefono' => $data['telefono'],


            );

            $hecho = $this->Manifiesto_model->guardar_ios($registro);
            if ($hecho) {
                $respuesta = array(
                    'err' => FALSE,
                    'mensaje' => 'Registro insertado correctamente',
                    'registro_id' => $this->db->insert_id()
                );
                $this->response($respuesta);
            } else {
            }
        } else {
            $respuesta = array(
                'err' => TRUE,
                'mensaje' => 'Hay errores en el envio de informacion',
                'errores' => $this->form_validation->get_errores_arreglo()
            );

            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function obtner_sucursales_get()
    {

        $sucursal = $this->Casillero_model->obtner_sucursales();


        if (($sucursal)) {
            $respuesta = array(
                'err' => false,
                'mensaje' => 'Consulta realizada correctamente',
                'sucursal' => $sucursal
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'No ha sipo posile obtener registros ',
                'sucursal' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function obtener_arancel_get()
    {

        $arancel = $this->Cotizador_model->obtner_arancel();

        //  var_dump( $arancel);
        if (($arancel)) {
            $respuesta = array(
                'err' => false,
                'mensaje' => 'Consulta realizada correctamente',
                'arancel' => $arancel
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'No ha sipo posile obtener registros ',
                'arancel' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function calcular_get()
    {
        $peso = $this->uri->segment(3);
        $valor = $this->uri->segment(4);
        $tipo_menrcancia = $this->uri->segment(5);

        $arancelcot = $this->Cotizador_model->get_arancel($tipo_menrcancia);
        $flete = $this->Cotizador_model->get_flete($peso);
        $tramite = $this->Cotizador_model->get_tramite($valor);
        $seguro = $this->Cotizador_model->get_seguro($valor);
        // $almacenaje = $this->Cotizador_model->get_almacenaje($peso);
        //var_dump($almacenaje);
        $tarifa_flete = $flete->valor_libra;
        $tarifa_tramite = $tramite->valor_tramite;
        $tarifa_seguro = $seguro->valor_seguro;
        $rango_almacenaje = 0; //$almacenaje->valor_almacenaje;
        $peso_cotizar = $peso;


        $costolibras = $tarifa_flete * $peso_cotizar;
        $costotramite = $tarifa_tramite;
        $costoseguro = $tarifa_seguro;
        $primaseguro = $valor * 0.015;

        $costototal = $costolibras + 0 + $valor;
        $totdai = ($costototal  + $costoseguro) * ($arancelcot->porcentaje / 100);
        $totiva = ($costototal + $costoseguro) * 0.13;
        $impuesto = 0;

        if ($valor < 200) {

            $impuesto = number_format(($totiva), 2);
        } else {
            $impuesto = number_format(($totdai + $totiva), 2);
        }
        $corteguia = 0;
        $transferenciaymanejo = 0;
        $cesc = 0;
        $inspeccionointrusiva = 0;
        $totalsts = 0;
        //Inspecci¨®n no intrusiva
        $inspeccionointrusiva = 0;
        //CESC
        if ($arancelcot->cesc == 1) {
            $cesc = ($costototal + $totdai) * ($arancelcot->porcentaje_cesc / 100);
        } else {
            $cesc = 0;
        }
        $totalsts = number_format($impuesto, 2) + number_format($costotramite, 2) + number_format($costolibras, 2) + number_format($corteguia, 2) + number_format($transferenciaymanejo, 2) + number_format($inspeccionointrusiva, 2)  + number_format($rango_almacenaje, 2);
        // +   +  + number_format($almacenaje,2)
        $total = 0;
        //Total
        //  echo $valor+$totalsts;
        $total = $totalsts + $valor + $costoseguro - $rango_almacenaje;
        // $total=number_format($total,2);

        if (($total)) {
            $respuesta = array(
                'err' => false,
                'mensaje' => 'Cálculo realizado correctamente',
                'calculo_estimado' => $total,
                'valor_articulo' => $valor,
                'envio' => $costolibras,
                'seguro' => $costoseguro,
                'impuesto' => $impuesto,
                'costo_tramite' => $costotramite


            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'No ha sido posible calcular el producto ',
                'total' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function manifiestoap_post()
    {
        $_SESSION['pais']=2; 	
        $data = $this->post();
        $country = 2;
        $awb = $data['mawb'];
        $etd=$data['etd'];
        $eta=$data['eta'];
      //  $pais=$data['country'];
        $enca = array(
            'manifiesto' => $data['mawb'],
            'fecha' => $data['etd'],
            'descripcion' => "Pruebas API",
            'referencia' => "Pendiente",
            'paquetes' => "0",
            'sacos' => $data['bag_number'],
            'tipo' => "ALL",
            'client_id' => $data['client_id'],
            'userid' => $data['client_id']

        );
        // 'id_pais' => $country,
        $id_manifiesto =  $this->Manifiesto_model->guardar_manifiesto($enca);
       
       
      
        if ($id_manifiesto) {

            foreach ($data['guias'] as $key => $detalle) {
                $rate['tarifa'] = $this->Manifiesto_model->tarifas(); //toma el valor de la guia para evaluacion de acuerdo al rango de tarifa (tabla tarifa)
             //     var_dump( $rate['tarifa']);
                $tarifa = 0;
                foreach ($rate['tarifa'] as $f) {

                    if ($tarifa == 0) {
                        if ($detalle['value']  >= $f->desde &&  $detalle['value']  <= $f->hasta) {
                            $tarifa = $f->tarifa;
                        }
                    }
                }
               /* 'mawb'                   => $detalle['mawb'],
                'bag_number'             => $detalle['bag_number'],
                'etd'                    => $detalle['etd'],
                'eta'                    => $detalle['eta'], */
                $deta = array(
                    'etd'                    => $etd,
                    'eta'                    => $eta,
                    'order_number'           => $detalle['order_number'], 
                    'tracking_number'        => $detalle['tracking_number'], 
                    'origin'                 => $detalle['origin'], 
                    'destination'            => $detalle['destination'], 
                    'consignee_account'      => $detalle['consignee_account'],
                    'consignee'              => $detalle['consignee'],
                    'consignee_address1'     => $detalle['consignee_address1'],
                    'consignee_address2'     => $detalle['consignee_address2'], 
                    'consignee_address3'     => $detalle['consignee_address3'],
                    'consignee_neighborhood' => $detalle['consignee_neighborhood'],
                    'consignee_city'         => $detalle['consignee_city'],
                    'consignee_state'        => $detalle['consignee_state'], 
                    'consignee_zip'          => $detalle['consignee_zip'], 
                    'consignee_country'      => $detalle['consignee_country'], 
                    'consignee_email'        => $detalle['consignee_email'],
                    'consignee_phone'        => $detalle['consignee_phone'], 
                    'consignee_mobile'       => $detalle['consignee_mobile'], 
                    'consignee_taxid'        => $detalle['consignee_taxid'], 
                    'pieces'                 => $detalle['pieces'],  
                    'gweight'                => $detalle['gweight'], 
                    'cweight'                => $detalle['cweight'],   
                    'weight_type'            => $detalle['weight_type'],   
                    'height'                 => $detalle['height'], 
                    'length'                 => $detalle['length'],  
                    'width'                  => $detalle['width'], 
                    'commodity'              => $detalle['commodity'],  
                    'value'                  => $detalle['value'], 
                    'freight'                => $detalle['freight'], 
                    'currency'               => $detalle['currency'],
                    'service_type'           => $detalle['service_type'],
                    'service_level'          => $detalle['service_level'],
                    'shipper_account'        => $detalle['shipper_account'], 
                    'shipper_name'           => $detalle['shipper_name'], 
                    'shipper_address1'       => $detalle['shipper_address1'], 
                    'shipper_address2'       => $detalle['shipper_address2'],
                    'shipper_city'           => $detalle['shipper_city'],   
                    'shipper_state'          => $detalle['shipper_state'], 
                    'shipper_zip'            => $detalle['shipper_zip'],  
                    'shipper_country'        => $detalle['shipper_country'],
                    'shipper_email'          => $detalle['shipper_email'], 
                    'shipper_phone'          => $detalle['shipper_phone'],
                    'id_pais'                => $_SESSION["pais"], 	
                    'id_manifiesto'          => $id_manifiesto,
                    'tarifa'                 => $tarifa		
                     );  

                    
                $hecho = $this->Manifiesto_model->guardar_manifiesto_detalle($deta);
            }
           
            if ($hecho) {
                $respuesta = array(
                    'err' => FALSE,
                    'mensaje' => 'Registro insertado correctamente',
                    
                );
                $this->response($respuesta);
            }
        }
    }

    public function manifiestoma_post()
    {

        $data = $this->post();
        $country = 2;
        $awb = $data['awb'];
        $pais=$data['country'];
        $enca = array(
            'manifiesto' => $data['awb'],
            'fecha' => $data['fecha'],
            'descripcion' => "Pruebas API",
            'referencia' => "Pendiente",
            'paquetes' => "0",
            'sacos' => $data['bag'],
            'tipo' => "ALL",
            'client_id' => $data['client_id'],
            'id_pais' => $country,
            'userid' => $data['client_id']

        );
        $id_manifiesto =  $this->Manifiesto_model->guardar_manifiesto($enca);
       
       
      
        if ($id_manifiesto) {

            foreach ($data['guias'] as $key => $detalle) {
             
               $deta = array(
                    'id_manifiesto'          => $id_manifiesto,
                    'tracking_number'        => $detalle['tracking_number'],
                    'gweight'                => $detalle['weight'],
                    'value'                  => $detalle['value'],
                    'items'                  => $detalle['items'],
                    'commodity'              => $detalle['items_description'],
                    'consignee_account'      => $detalle['buyer_id'],
                    'consignee'              => $detalle['buyer'],
                    'buyer_company'          => $detalle['buyer_company'],
                    'consignee_address1'     => $detalle['buyer_address1'],
                    'buyer_address1_number'  => $detalle['buyer_address1_number'],
                    'consignee_address2'     => $detalle['buyer_address2'],
                    'consignee_address3'     => $detalle['buyer_address3'],
                    'buyer_district'         => $detalle['buyer_district'],
                    'consignee_city'         => $detalle['buyer_city'],
                    'consignee_state'        => $detalle['buyer_state'],
                    'consignee_country'      => $detalle['buyer_location'],
                    'consignee_zip'          => $detalle['buyer_zip'],
                    'consignee_phone'        => $detalle['buyer_phone'],
                    'consignee_email'        => $detalle['buyer_email'],
                    'hts'                    => $detalle['hts'],
                    'pieces'                 => $detalle['pieces'],  
                    'departamento_id'        => $detalle['departamento_id'],  
                    'municipio_id'           => $detalle['municipio_id']  

                );
                $hecho = $this->Manifiesto_model->guardar_manifiesto_detalle($deta);
            }
           
            if ($hecho) {
                $respuesta = array(
                    'err' => FALSE,
                    'mensaje' => 'Registro insertado correctamente',
                    
                );
                $this->response($respuesta);
            }
        }
    }
   
    /* public function addmanifiesto_post()
    {

        $data = $this->post();

        $this->load->library('form_validation');

        $this->form_validation->set_data($data);
        if ($this->form_validation->run('addmanifiesto_post')) {



            $hecho = $this->Manifiesto_model->guardar_manifiesto($data);
            if ($hecho) {
                $respuesta = array(
                    'err' => FALSE,
                    'mensaje' => 'Registro insertado correctamente',
                    'registro_id' => $this->db->insert_id()
                );
                $this->response($respuesta);
            } else {
            }
        } else {
            $respuesta = array(
                'err' => TRUE,
                'mensaje' => 'Hay errores en el envio de informacion',
                'errores' => $this->form_validation->get_errores_arreglo()
            );

            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }*/
    
    //integracion con xpress
     public function addestatus_post()

    {
        $data = $this->post(); //contiene los datos
        
        $item="";
        $fecha="";
        $warehouse= "";
        $id_estatus = "";
        $casillero = "";
        $id_pais =  "";
        
        
        // ******
        
        //$gg = "kkkk";
        //$r2 = json_decode($data);
        //$this->response($data);
      
        // *********
        
        
        
        //echo $data[0]->guia;
        
        //$this->response($data);
        
        //$result = json_decode($data);
        $result = json_decode($data);
        //$this->response($result); 
        
        //var_dump($result);
        
        foreach ($result as $fila) {
            $id_estatus = $fila->codigo;
            $id_estatus = $fila['codigo'];
        }
        
        //
        //$this->response$id_estatus);
        
        $item=1;
        $fecha=date('Y-m-d H:i:s');
        //$warehouse= $data['guia'];
        $warehouse= $data->guia;
        $id_estatus = $id_estatus;
        $casillero = "100";
        $id_pais =  1;
        
        //foreach ($data['pod'] as $key => $detalle) {
        //echo $detalle['nombre'];
        //echo $detalle['valor'];
        //}
        //$this->response($data);
        
        $datos = array(
            'item' => $item,
            'fecha' => $fecha,
            'warehouse' => $warehouse,
            'id_estatus' =>$id_estatus,
            'casillero' =>$casillero,
            'id_pais' =>  $id_pais,
        );
        
        //$hecho = $this->Manifiesto_model->guardar_estatus($datos);

      
    }
    
     public function elmer_post(){
    

       
            $item = "";
            $fecha = "";
            $warehouse = "";
            $id_estatus = "";
            $casillero = "";
            $id_pais =  "";
            
     
     
            $data = $this->post(); //contiene los datos
       
            $data=  file_get_contents('php://input',true);
          //  echo $data ;
            $row = json_decode($data, true);
           
          //  echo $row['guia'];
          //  echo $row['codigo'];

 
  
            $item = 1;
            $fecha = date('Y-m-d H:i:s');
            $warehouse =  $row['guia'];
            $id_estatus = $row['codigo'];
            $casillero = "100";
            $id_pais =  1;
            $pod_nombre="";
            $pod_valor="";
            if ( $id_estatus==15){
                foreach ($data['pod'] as $key => $detalle) {
                  $pod_nombre = $detalle['nombre'];
                  $pod_valor  = $detalle['valor'];
            }

            // $this->response($data);
    
    
    
            $datos = array(
    
                'item' => $item,
    
                'fecha' => $fecha,
    
                'warehouse' => $warehouse,
    
                'id_estatus' => $id_estatus,
    
                'casillero' =>  $casillero,
    
                'id_pais' =>  $id_pais,
                
                '$pod_nombre' =>  $pod_nombre,
                 
                '$pod_nombre' =>  $id_pais,
    
            );
    
    
            $hecho = $this->Manifiesto_model->guardar_estatus($datos);
      
    
    }
}
}
/* End of file Controllername.php */