<?php
    
    defined('BASEPATH') or exit('No direct script access allowed');
    
    class Traspaso extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->database();
            $this->load->model('PreclasificacionModel');
            $this->load->model('WarehouseModel');
            $this->load->model('Conf_model');
            $this->load->model('SendModel');
            $this->load->model('Traspaso_model');
            $this->datos = array();
        }
        
        public function index()
        {
        }
            
        public function pre_clasificacion()
        {
            $tipo="TRA";
            $this->datos['preclasifica'] = $this->PreclasificacionModel->pre_clasificacion($tipo);
            
            $this->data['estatus'] = $this->Conf_model->estatus();
            $this->load->view("traspaso/vista_preclasificacion", $this->data);
            $this->load->view("traspaso/lista", $this->datos);
            
        }
        
        public function traspaso_guardar()
        {
            $id = $_POST['id'];
            $data = array(
                'manifiesto' => $_POST["manifiesto"],
                'fecha' => $_POST['fecha'],
                'descripcion' => $_POST['descripcion'],
                'referencia' => 0,
                'paquetes' => 0,
                'sacos' => 0,
                'tipo' =>'TRA'
            );

            $this->PreclasificacionModel->guardar_manifiesto($id, $data);

        }


        public function guardar_poliza()
        {
            $data = array(
                'manifiesto' => $_POST["manifiesto"],
                'referencia' => $_POST['referencia'],
                'poliza' =>     $_POST['poliza'],
               
            );
            
            $this->PreclasificacionModel->guardar_poliza($data); //actualiza encabezados
            $this->PreclasificacionModel->guardar_polizas($data); //actualiza detalles
        }

        /*public function listado()
        {
            $tipo="TRA";
            $this->datos['preclasifica'] = $this->PreclasificacionModel->pre_clasificacion($tipo);
            $this->load->view('traspaso/cuerpo', $this->datos);

            //$datos['lista']    = $this->MensajeroModel->mensajeros();
            //$this->load->view('mensajero/cuerpo', $datos);
        }*/


        public function guardar_guia()
        {
            $id = $_POST['id'];
            $data = array(
                'manifiesto' => $_POST["manifiesto"],
                'referencia' => $_POST['referencia'],
                'numero_warehouse' => $_POST['guia'],
                'cajas' => $_POST['cajas'],
                'casillero' => $_POST['idcasillero']
            );

            $wh=$_POST['guia'];
            $idestatus=$_POST['estatus'];
            $idwh=$_POST['manifiesto'];


            //envio de body de correo
            $stta = $this->Traspaso_model->select_estatus($idestatus); 
            $date ="Date: ". date('Y-m-d H:i:s');
        
            $estatus="Estatus Actual es: ".$stta->nombre;
            $warehouse="Warehouse: ".$_POST['guia'];
            $casillero=$_POST['idcasillero'];

            $n_casillero = preg_replace("/[^0-9]/","", $casillero);
            $ws= $this->WarehouseModel->buscar_cliente($n_casillero);

            $nombrec="Consignatario : ".$ws->nombre;

            $correo=$ws->correo;

            $ws_detalle = $this->Traspaso_model->ws_detalle($wh);

            $detalle_ws="<br> Tracking : ".$ws_detalle->tracking."<br> Peso : ".$ws_detalle->peso." Libras <br> Bulto : ".$ws_detalle->bultos." Unidad <br> Total a pagar : $".$ws_detalle->total;

            $body='<br>'.$estatus.'<br>'.$date.'<br>'.$nombrec.'<br>'.$warehouse.' '.$detalle_ws;

            /*actualizar wh*/
            $validar_wh=$this->Traspaso_model->validar_wh($wh,$idwh);

            if($validar_wh>0){

                $this->Traspaso_model->actualizar_estatus($wh,$idestatus);
                echo $validar_wh;
                
            }else{

                $this->PreclasificacionModel->guardar_guia($data);
                $this->PreclasificacionModel->guardar_detalle($data);
                $this->Traspaso_model->actualizar_estatus($wh,$idestatus);
                echo $validar_wh;
                

            }

        /*envio de correo*/
        //$this->SendModel->sendmail($body,$correo);
    
        }
        
        
        public function consulta_guias($id)
        {
            $this->datos['guias'] = $this->Traspaso_model->consulta_guias($id);
            $this->load->view("traspaso/lista_guias", $this->datos);
        }


        public function eliminar_guia($id)
        {
            $this->PreclasificacionModel->eliminar_guia($id);
        }

        public function eliminar_referencia($id)
        {
            $this->PreclasificacionModel->eliminar_referencia($id);
        }

            
        public function consulta_guia_master($id)
        {
            $this->datos['guiamaster'] =  $this->PreclasificacionModel->consulta_guia_master($id);
            $this->load->view("traspaso/lista_clasifica", $this->datos);
        }

        public function update_guia($wh,  $estatus, $casillero, $name)
        {
            $data = array(
                'estado' => "V"
            );
            // var_dump($data);
            $this->PreclasificacionModel->update_guia($wh,  $data);

            $date = date('Y-m-d H:i:s');
          
            $detalle = array(
                'item' => 1,
                'fecha' => $date,
                'warehouse' => $wh,
                'id_estatus' => $estatus,
                'casillero' => $casillero
            );

            //var_dump($detalle);
            //guarda datos de estatus
            $this->PreclasificacionModel->guardar_detalle($detalle);
          
            $enviar=$this->sendmail($wh, $casillero, $estatus, $name);
        }

        

        public function consulta_referencia($wh)
        {
            $this->PreclasificacionModel->consulta_referencia($wh);

            $datos = $this->PreclasificacionModel->consulta_referencia($wh);
            /* if ($dato) {
                 echo json_encode($dato);
             }*/


            enviarJson(array(

            'numero_warehouse'           => $datos[0]->numero_warehouse,

            'manifiesto'       => $datos[0]->manifiesto,

            'referencia' => $datos[0]->referencia,

            'casillero' => $datos[0]->casillero

        ));
        }

     
        public function pdf_clasificado($id)
        {
            $datos['guia_master'] =  $this->PreclasificacionModel->query_guia_master($id);
            $datos['guiamaster'] =  $this->PreclasificacionModel->consulta_guia_master($id);

                  
            include getcwd() . "/application/libraries/fpdf/fpdf.php";
            include getcwd()."/" ;
            $this->pdf = new FPDF();
            $this->pdf->AddPage();
            $this->pdf->AliasNbPages();
            $this->pdf->SetTitle("Guia Master");
            $this->pdf->SetLeftMargin(10);
            $this->pdf->SetRightMargin(10);
            $this->pdf->SetFillColor(200, 200, 200);
            $this->pdf->SetFont('Arial', 'B', 7);
           

            $x = 0;
            $correla=0;
            
            $estado="";
            $manifiesto="";
            $poliza="";
            foreach ($datos['guiamaster'] as $item) {
                $manifiesto=$item->manifiesto;
                if ($item->tlc==1) {
                    $tlc="SI";
                }
                if ($x == 0) {
                    $this->pdf->SetFont('Arial', 'B', 12);
                    $this->pdf->Cell(160, 7, 'MANIFIESTO: '.' '.$manifiesto, 0, 0, 'C', '0');
                   
                    $this->pdf->SetFont('Arial', 'B', 7);
                    $this->pdf->Cell(10, 7, 'Pagina '. $this->pdf->PageNo() . '/{nb}', 0, 0, 'C', '0');
                    $this->pdf->Ln(9);

                    //   $this->pdf->Cell(160, 7, 'File Numero: '. $archivo, 0, 0, 'L', '0');
                    $this->pdf->Ln(7);
                    $this->pdf->Cell(15, 7, '', '0', 0, 'C', '0');
                    $this->pdf->Cell(12, 7, 'Item', 'TBL', 0, 'C', '1');
                    $this->pdf->Cell(30, 7, 'Referencia', 'TBL', 0, 'C', '1');
                    $this->pdf->Cell(30, 7, 'Warehouse', 'TB', 0, 'C', '1');
                    $this->pdf->Cell(30, 7, 'paquetes', 'TB', 0, 'C', '1');
                    $this->pdf->Cell(30, 7, utf8_decode('Póliza'), 'TB', 0, 'C', '1');
                    $this->pdf->Cell(20, 7, 'Estado', 'TBR', 0, 'L', '1');
               
        
                    $this->pdf->Ln(9);
                }
                $x +=1;
                $correla=$correla+1;
                $this->pdf->Cell(15, 5, '', 0, 0, 'C', 0);
                $this->pdf->Cell(12, 5, $correla, 0, 0, 'C', 0);
                $this->pdf->Cell(30, 5, $item->referencia, 0, 0, 'C', 0);
                $this->pdf->Cell(30, 5, $item->numero_warehouse, 0, 0, 'C', 0);
                $this->pdf->Cell(30, 5, $item->cajas, 0, 0, 'C', 0);
                $this->pdf->Cell(30, 5, $item->poliza, 0, 0, 'C', 0);
                if ($item->estado==="V") {
                    $estado="Recibido";
                } else {
                    $estado="Pendiente";
                }
               
                $this->pdf->Cell(20, 5, $estado, 0, 0, 'L', 0);
          
                $archivo=$item->manifiesto;
               
                $this->pdf->Ln(5);

                if ($x == 48) {
                    $x = 0;
                }
            }
            //
            $this->pdf->Ln(7);
          
            $this->pdf->Cell(30, 7, utf8_decode('Guía Master'), 'TBL', 0, 'C', '1');
            $this->pdf->Cell(30, 7, utf8_decode('Póliza'), 'TBL', 0, 'C', '1');
            $this->pdf->Cell(30, 7, 'Fecha', 'TB', 0, 'C', '1');
            $this->pdf->Cell(30, 7, 'Referencia', 'TB', 0, 'C', '1');
            $this->pdf->Cell(30, 7, 'Paquetes', 'TB', 0, 'C', '1');
            $this->pdf->Cell(30, 7, 'Sacos', 'TBR', 0, 'C', '1');
            $this->pdf->Ln(7);
            foreach ($datos['guia_master'] as $fila) {
                $ff = new DateTime($fila->fecha);
                $fecha = $ff->format('d-m-Y');
                $this->pdf->Cell(30, 5, $fila->manifiesto, 0, 0, 'C', 0);
                $this->pdf->Cell(30, 5, $fila->poliza, 0, 0, 'C', 0);
                $this->pdf->Cell(30, 5, $fecha, 0, 0, 'C', 0);
                $this->pdf->Cell(30, 5, $fila->referencia, 0, 0, 'C', 0);
                $this->pdf->Cell(30, 5, $fila->paquetes, 0, 0, 'C', 0);
                $this->pdf->Cell(30, 5, $fila->sacos, 0, 0, 'C', 0);
                $this->pdf->Ln(5);
            }
              
            $destino = getcwd()."/"."FILE.php";
       
            $this->pdf->Output($archivo.'.pdf', 'f');
        }

        public function buscar_warehouse_wd($guia)
        {
            $datos = $this->PreclasificacionModel->buscar_warehouse_wd($guia);
  
            enviarJson(array(

            'nombre_destinatario'   => $datos[0]->destinatario,

            'casillero' => $datos[0]->cuenta
            ));
        }
    }
    
    /* End of file PreclasificacionController.php */
