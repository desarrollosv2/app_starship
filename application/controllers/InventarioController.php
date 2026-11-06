<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class InventarioController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        session_start();
        $this->load->database();
        $this->load->model( 'Conf_model' );
        $this->load->model( 'PreclasificacionModel' );
        $this->load->model( 'Inventario_Model' );
        //titulo

        //Do your magic here
    }

    public function index() {
    }

    public function inventario() {
        $tipo = "INV";
        $this->datos['inventarios'] = $this->PreclasificacionModel->pre_clasificacion( $tipo );
        $this->data['estatus'] = $this->Conf_model->estatus();
        $this->data['opc_inventario'] = $this->Conf_model->opc_inventario();

        $this->load->view( "inventario/vista_inventario", $this->data );
        $this->load->view( "inventario/lista", $this->datos);
    }

    public function listado() {
        $tipo = "INV";
        $this->datos['preclasifica'] = $this->PreclasificacionModel->pre_clasificacion( $tipo );
    }

    public function guardar_inventario() {
        $id = $_POST['id'];
        $data = array(
            'manifiesto' => trim( $_POST["id_inventario"] ),
            'fecha' => $_POST['fecha'],
            'descripcion' => $_POST['descripcion'],
            'referencia' => 0,
            'paquetes' => 0,
            'sacos' => 0,
            'tipo' => "INV"
        );

        $this->PreclasificacionModel->guardar_manifiesto($id,$data);
        $estatus = $_POST['estatus'];
        $result = $this->datos['item'] = $this->Inventario_Model->selecionar_paquetes($estatus);
  
      // var_dump($result);

        $data = array();
        foreach ($result as $fila){
            $r=  $this->Inventario_Model->obtener_fecha($fila->idwarehouse,$estatus);
            //var_dump($r);
            //echo "fecha ".$r->fecha." id ".$r->warehouse."\n";
            //$id = $_POST['id'];
            if($r->warehouse == $fila->idwarehouse ){  

                $fechaActual = date('Y-m-d'); 
                $fechaEnvio =$r->fecha;
                $datetime1 = date_create($fechaEnvio);
                $datetime2 = date_create($fechaActual);
                $contador = date_diff($datetime1, $datetime2);
                $differenceFormat = '%a';
                $dias = $contador->format($differenceFormat); 

                 $rsl = array(
                    'dias'=>$dias,
                    'manifiesto' => trim($_POST["id_inventario"]),
                    'referencia' => 0,
                    'numero_warehouse' => $fila->idwarehouse,
                    'cajas' => 0,
                    'casillero' => $fila->cuenta
                );
                array_push($data,$rsl);
            }
            
            //$wh = $_POST['guia'];
            //$estatus = $_POST['estatus'];
            //$this->PreclasificacionModel->guardar_guia($data);
        }


        //var_dump($data);


        $op_in = $_POST['opc_inventario'];
        $f_l="";
        if($op_in==1){
            $f_l=15;
        }else if($op_in==2){
            $f_l=30;
        }else if($op_in==3){
            $f_l=60;
        }else if($op_in==4){
            $f_l=90;
        }else {
            $f_l=10000000;
        }

        
        foreach ($data as $row){

                

            if(($row['dias']>=1) && ($row['dias']<=$f_l)){

                echo " entraron ".$row['numero_warehouse']." f_mayor ". $f_l." <= dias ".$row['dias']."\n";
                   
                $data2 = array(
                'manifiesto' => $row['manifiesto'],
                'referencia' => 0,
                'numero_warehouse' => $row['numero_warehouse'],
                'cajas' => 0,
                'casillero' => $row['casillero']
            );                
                $this->PreclasificacionModel->guardar_guia($data2);
           }else{
                 echo " NO CUMPLE ".$row['numero_warehouse']." dias ".$row['dias']."\n";
           }
        }


    }

    public function pdf_inventario( $id, $titulo ) {
        $opc = substr( $id, 0, 3 );
        $descripcion = $datos['guia_master'] =  $this->Inventario_Model->query_guia_master( $id );
        $datos['guiamaster'] =  $this->Inventario_Model->consulta_guia_master( $id );

        include getcwd() . "/application/libraries/fpdf/fpdf.php";
        include getcwd() . "/";
        $this->pdf = new FPDF();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle( "Inventario" );
        $this->pdf->SetLeftMargin( 10 );
        $this->pdf->SetRightMargin( 10 );
        $this->pdf->SetFillColor( 200, 200, 200 );
        $this->pdf->SetFont( 'Arial', 'B', 7 );

        $x = 0;
        $correla = 0;

        $estado = "";
        $manifiesto = "";
        $poliza = "";
        $monto = 0;
        foreach ( $datos['guiamaster'] as $item ) {
            $manifiesto = $item->manifiesto;
            if ( $item->tlc == 1 ) {
                $tlc = "SI";
            }
            if ( $x == 0 ) {
                $this->pdf->SetFont( 'Arial', 'B', 12 );
                $this->pdf->Cell( 160, 7, $titulo . ': ' . ' ' . $manifiesto, 0, 0, 'C', '0' );

                $this->pdf->SetFont( 'Arial', 'B', 8 );
                $this->pdf->Cell( 10, 7, 'Pagina ' . $this->pdf->PageNo() . '/{nb}', 0, 0, 'C', '0' );
                $this->pdf->Ln( 9 );

                //   $this->pdf->Cell( 160, 7, 'File Numero: '. $archivo, 0, 0, 'L', '0' );
                $this->pdf->Cell( 160, 7, utf8_decode($descripcion->descripcion), 0, 0, 'L', '0' );
                $this->pdf->Ln( 7 );

                $this->pdf->Cell( 12, 7, 'Item', 'TBL', 0, 'C', '1' );
                $this->pdf->Cell( 30, 7, 'Warehouse', 'TB', 0, 'C', '1' );
                $this->pdf->Cell( 90, 7, 'Nombre', 'TB', 0, 'L', '1' );
                $this->pdf->Cell( 30, 7, 'Total', 'TB', 0, 'R', '1' );
                $this->pdf->Cell( 30, 7, 'Estado', 'TBR', 0, 'C', '1' );

                $this->pdf->Ln( 9 );
            }
            $this->pdf->SetFont( 'Arial', '', 8 );
            $x += 1;
            $correla = $correla + 1;

            $this->pdf->Cell( 12, 5, $correla, 0, 0, 'C', 0 );
            $this->pdf->Cell( 30, 5, $item->numero_warehouse, 0, 0, 'C', 0 );
            $this->pdf->Cell( 90, 5, $item->nombre_cliente, 0, 0, 'L', 0 );
            $this->pdf->Cell( 30, 5, $item->total, 0, 0, 'R', 0 );

            if ( $item->estado === "V" ) {
                $estado = "Recibido";
            } else {
                $estado = "Pendiente";
            }

            $this->pdf->Cell( 30, 5, $estado, 0, 0, 'C', 0 );

            $archivo = $item->manifiesto;
            $monto = $monto + $item->total;
            $this->pdf->Ln( 5 );

            if ( $x == 48 ) {
                $x = 0;
            }
        }
        // $this->pdf->Line( 10, 182, 205, 182 );
        $this->pdf->Line( $this->pdf->GetX(), $this->pdf->GetY(), 190, $this->pdf->GetY() );
        $this->pdf->Ln( 5 );
        $this->pdf->Cell( 132, 5, " ", 0, 0, 'C', 0 );
        $this->pdf->SetFont( 'Arial', 'B', 8 );
        $this->pdf->Cell( 30, 5, "TOTAL: $" . number_format( $monto, 2, '.', '' ), 0, 0, 'R', 0 );
        $this->pdf->SetFont( 'Arial', '', 8 );
        $this->pdf->Ln( 25 );

        if ($opc == 'INV') {
            $this->pdf->Cell( 20, 7, '', '', 0, 'C', '0' );
            $this->pdf->Cell( 60, 7, 'Realizado por:', 'T', 0, 'C', '0' );
            $this->pdf->Cell( 30, 7, '', '', 0, 'C', '0' );
            $this->pdf->Cell( 60, 7, 'Auditado por:', 'T', 0, 'C', '0' );
        } else {
            $this->pdf->Cell( 10, 7, '', '', 0, 'C', '0' );
            $this->pdf->Cell( 50, 7, 'Despachado por:', 'T', 0, 'C', '0' );
			$this->pdf->Cell( 10, 7, '', '', 0, 'C', '0' );
            $this->pdf->Cell( 50, 7, 'Transportado por: ', 'T', 0, 'C', '0' );
			$this->pdf->Cell( 10, 7, '', '', 0, 'C', '0' );
            $this->pdf->Cell( 50, 7, 'Recibido Por: ', 'T', 0, 'C', '0' );
        }
        $destino = getcwd() . "/" . "FILE.php";

        $this->pdf->Output( $archivo . '.pdf', 'f' );
    }

    public function buscar_warehouse_wd( $guia ) {
        $datos = $this->PreclasificacionModel->buscar_warehouse_wd( $guia );

        enviarJson( array(

            'nombre_destinatario'   => $datos[0]->destinatario,

            'casillero' => $datos[0]->cuenta
        ) );
    }

    public function consulta_guias( $id ) {
        $this->datos['guias'] = $this->Inventario_Model->consulta_guias( $id );
        $this->load->view( "inventario/listas_guias", $this->datos );
    }
}

/* End of file Controllername.php */
