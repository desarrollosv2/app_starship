<?php 
class Pdfticket
{
    public function crear_pdf_ticket($impreso, $id_enc_Comprobante)
    {
       $CI =  & get_instance();
       $CI ->load->model('Importacion_Model');
       $CC =  & get_instance();
       $CC ->load->model('Conf_model');
       $CV =  & get_instance();
       $CV ->load->model('Ventas_Model');


        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->pdf = new FPDF('P', 'mm', array(72.1,297));
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle("Ticket");        
        $this->pdf->SetFont('Arial', 'B', 9);
        $archivo    = 'Ticket01';
        
        
        $datos_fac     = $CI->Importacion_Model->obtener_servicios_facturados($id_enc_Comprobante);
        $id_sucursal   =  $datos_fac[0]->id_sucursal_empresa;
        
        // $idSucursal =  $_SESSION["id_sucursal"];
            if($id_sucursal ==2 ){
               
            }else{
                 $id_sucursal  = 1;

            }
            
        $datos         = $CC->Conf_model->get_info_configuracion($id_sucursal);
         
        // var_dump( $datos_fac);

        $Ntiquete   = 0; //Numero de tiquete
        $Datetime   = 0; //Fecha del ticket
        $Cajero     = 0; //Tipo de cajero
        $Ware       = 0; //Warehouse
        $wareouse   = 0;
        $anulado    = 0;
        $anulado    = $datos_fac[0]->anulado;
        /*if($anulado ==   2){
            $Ntiquete   = str_pad($datos_fac[0]->numero_comprobante, 10, "0", STR_PAD_LEFT)." (ANULACION)"; //Numero de tiquete
        }else{
        $Ntiquete   = str_pad($datos_fac[0]->numero_comprobante, 10, "0", STR_PAD_LEFT); //Numero de tiquete    
        } */
        
        $Ntiquete   = str_pad($datos_fac[0]->numero_comprobante, 10, "0", STR_PAD_LEFT); //Numero de tiquete
        //$datos_fac[0]->nombre_cliente
        $numeroticket = $datos_fac[0]->numero_comprobante;
        $id_cliente  =  $datos_fac[0]->id_cliente;
       
        $datoswarehouse =  $CI->Importacion_Model->obtener_warehouse_facturados($id_cliente, 7, $numeroticket);   //  mostramos lanzamos la vista
        if (!empty($datoswarehouse)) {
            $wareouse  = $datoswarehouse[0]->wh_numero;
        }
       $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->Image('assets/img/star.png', 11);
        $this->pdf->Ln(3);

        $this->pdf->Cell(0, 4, utf8_decode($datos->nameEmpresa), 0, 0, 'C');
        $this->pdf->Ln();
        
        $this->pdf->MultiCell(0, 4, utf8_decode($datos->direccion), 0, 'C');
      
        $this->pdf->Cell(0, 4, 'NIT: '.utf8_decode($datos->nit), 0, 0, 'C');
        $this->pdf->Ln();

        $this->pdf->Cell(0, 4, 'NCR: '.utf8_decode($datos->ncr) . '120938383-4', 0, 0, 'C');
        $this->pdf->Ln();

        $this->pdf->Cell(0, 4, utf8_decode('GIRO:'), 0, 0, 'C');
        $this->pdf->Ln();

        $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->MultiCell(0, 2,utf8_decode($datos->giro),  0, 'C',false);
        $this->pdf->Ln();

         $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->Cell(52, 5, utf8_decode('CAJA: ') .utf8_decode($datos->caja), 'B', 0, 'C');
        $this->pdf->Ln();

        $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->Cell(0, 3, utf8_decode('No. RESOLUCION:') . utf8_decode($datos->nresolucion), 0, 0, 'C');
        $this->pdf->Ln();

        $this->pdf->Cell(0, 3, utf8_decode('FECHA DE AUTORIZACION:') . utf8_decode($datos->fautorizacion), 0, 0, 'C');
        $this->pdf->Ln();

        $this->pdf->Cell(0, 3, utf8_decode('RANGO AUTORIZADO:'), 0, 0, 'C');
        $this->pdf->Ln();

        $this->pdf->Cell(0, 3, utf8_decode($datos->rango), 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Cell(0, 4, utf8_decode('NUMERO DE TIQUETE: ') . $Ntiquete, 0, 0, 'L');
        $this->pdf->Ln();

        $this->pdf->Cell(0, 4, date('d-m-Y h:i:s'), 0, 0, 'L');
        $this->pdf->Ln();

        $this->pdf->Cell(30, 4, utf8_decode('CAJERO(A): ') . utf8_decode($datos_fac[0]->cajero), 0, 0, 'L');
        $this->pdf->SetFont('Arial', 'B', '7');

        // $datos_fac =   $this->Importacion_Model->obtener_servicios_facturados($id_enc_Comprobante);

        $this->pdf->Cell(30, 4, utf8_decode('WAREHOUSE: ') . $wareouse, 0, 0, 'C');
        $this->pdf->Ln(8);

        $Cantidad   = 0; //CA
        $Producto   = 0; //Nombre del producto
        $Preciouni  = 0; //Precio Unitario
        $Total      = 0; //Total
        $Tot        = 0; //Exento, No gravado, No sujeto, Cuentas ajenas
        $TotalE     = 0;//exento
        $TotalA     = 0;//ajenas
        $TotalN     = 0;//no sujeto
        $TotalG     = 0;//gravado
        $TotalF     = 0;//Total a pagar


        $this->pdf->SetX(10);
        $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->Cell(4, 4, "CA", 'B', 0, 'C');
        $this->pdf->Cell(24, 4, "PRODUCTO", 'B', 0, 'C');
        $this->pdf->Cell(11, 4, "P/U", 'B', 0, 'C');
        $this->pdf->Cell(15, 4, "TOTAL", 'B', 0, 'C');
        $this->pdf->Ln(8);
       
        foreach ($datos_fac as $key=> $servicioFact) {
            // validando quien es el que contiene el  valor
            $this->pdf->SetFont('Arial', 'B', 6);
            if ($servicioFact->kdx_no_sujeto != 0) {
                $this->pdf->SetX(10);
                $this->pdf->Cell(4, 6, number_format($servicioFact->cantidad, 0), 0, 0, 'C');//CA
                $this->pdf->Cell(23, 6, str_replace('STAR SHIP', '', $servicioFact->descripcion), 0, 0, 'L');//Producto
                $this->pdf->Cell(13, 6, '$' .number_format($servicioFact->kdx_vta_total, 2), 0, 0, 'R');//P/U
                $this->pdf->Cell(8, 6, '$' .number_format($servicioFact->kdx_vta_total, 2), 0, 0, 'R');//Total P/U
                $this->pdf->Cell(4, 6, 'N', 0, 0, 'C');//Exento, No gravado, No sujeto, Cuentas ajenas
                $this->pdf->Ln(8);
                $TotalN = $TotalN + $servicioFact->kdx_vta_total;
            }
            if ($servicioFact->kdx_gravado != 0) {
                $this->pdf->SetX(10);
                $this->pdf->Cell(4, 6, number_format($servicioFact->cantidad, 0), 0, 0, 'C');//CA
                $this->pdf->Cell(23, 6, str_replace('STAR SHIP', '', $servicioFact->descripcion), 0, 0, 'L');//Producto
                $this->pdf->Cell(13, 6, '$' .number_format($servicioFact->kdx_vta_total, 2), 0, 0, 'R');//P/U
                $this->pdf->Cell(8, 6, '$' .number_format($servicioFact->kdx_vta_total, 2), 0, 0, 'R');//Total P/U
                $this->pdf->Cell(4, 6, 'G', 0, 0, 'C');//Exento, No gravado, No sujeto, Cuentas ajenas
                $this->pdf->Ln(8);
                $TotalG = $TotalG + $servicioFact->kdx_vta_total;
            }
            if ($servicioFact->kdx_cuenta_ajena != 0) {
                $this->pdf->SetX(10);
                $this->pdf->Cell(4, 6, number_format($servicioFact->cantidad, 0), 0, 0, 'C');//CA
                $this->pdf->Cell(23, 6, str_replace('STAR SHIP', '', $servicioFact->descripcion), 0, 0, 'L');//Producto
                $this->pdf->Cell(13, 6, '$' .number_format($servicioFact->kdx_vta_total, 2), 0, 0, 'R');//P/U
                $this->pdf->Cell(8, 6, '$' .number_format($servicioFact->kdx_vta_total, 2), 0, 0, 'R');//Total P/U
                $this->pdf->Cell(4, 6, 'A', 0, 0, 'C');//Exento, No gravado, No sujeto, Cuentas ajenas
                $this->pdf->Ln(8);
                $TotalA = $TotalA + $servicioFact->kdx_vta_total;
            }
            if ($servicioFact->kdx_exento != 0) {
                $this->pdf->SetX(10);
                $this->pdf->Cell(4, 6, number_format($servicioFact->cantidad, 0), 0, 0, 'C');//CA
                $this->pdf->Cell(23, 6, str_replace('STAR SHIP', '', $servicioFact->descripcion), 0, 0, 'L');//Producto
                $this->pdf->Cell(13, 6, '$' .number_format($servicioFact->kdx_vta_total, 2), 0, 0, 'R');//P/U
                $this->pdf->Cell(8, 6, '$' .number_format($servicioFact->kdx_vta_total, 2), 0, 0, 'R');//Total P/U
                $this->pdf->Cell(4, 6, 'E', 0, 0, 'C');//Exento, No gravado, No sujeto, Cuentas ajenas
                $this->pdf->Ln(8);
                $TotalE = $TotalE + $servicioFact->kdx_vta_total;
            }          
            $TotalF = $TotalE + $TotalG + $TotalA + $TotalN;
        }
         $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->Cell(20, 6, "VENTA GRAVADA", 'T', 0, 'L');
        $this->pdf->Cell(21, 6, "", 'T', 0, 'L');
        $this->pdf->Cell(15, 6, '$' . number_format($TotalG, 2), 'T', 0, 'R');//Total Ventas gravadas
        $this->pdf->Ln();

        $this->pdf->Cell(20, 6, "VENTA EXENTA", 0, 0, 'L');
        $this->pdf->Cell(21, 6, "", 0, 0, 'L');
        $this->pdf->Cell(15, 6, '$' . number_format($TotalE, 2), 0, 0, 'R');//Total Exenta
        $this->pdf->Ln();

        $this->pdf->Cell(20, 6, "VENTA NO SUJETA", 0, 0, 'L');
        $this->pdf->Cell(21, 6, "", 0, 0, 'L');
        $this->pdf->Cell(15, 6, '$' . number_format($TotalN, 2), 0, 0, 'R'); //Total no sujeta
        $this->pdf->Ln();

        $this->pdf->Cell(22, 6, "COBROS POR CTA. AJENA", 'B', 0, 'L');
        $this->pdf->Cell(19, 6, "", 'B', 0, 'L');
        $this->pdf->Cell(14, 6, '$' . number_format($TotalA, 2), 'B', 0, 'R');//Total Cuenta ajena
        $this->pdf->Ln(6);

           $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->Cell(20, 6, "TOTAL A PAGAR:", 0, 0, 'L');
        $this->pdf->Cell(21, 6, "", 0, 0, 'L');
        $this->pdf->Cell(14, 6, '$' . number_format($TotalF, 2), 0, 0, 'R'); //Total final
        $this->pdf->Ln();

        
        $tipoPago   = 'EFECTIVO'; //Efectivo,Tarjeta
        $recibido   = $TotalF;
        $cambio     = 0;
        $cliente    = 0;

        if ($cambio==0) {
            $cambio= '0.00';
        }
        

    $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->Cell(0, 6, "TIPO DE PAGO: " . $tipoPago, 0, 0, 'L');//EFECTIVO,TAREJTA
        $this->pdf->Ln();
        $this->pdf->Cell(29, 6, "RECIBIDO: ", 0, 0, 'R');//CANTIDAD RECIBIDA EN EFECTIVO
        $this->pdf->Cell(11, 6, "", 0, 0, 'L');
        $this->pdf->Cell(14, 6, '$'.$recibido, 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Cell(29, 6, "CAMBIO: ", 0, 0, 'R'); //CAMBIO POR RECIBIDO
        $this->pdf->Cell(11, 6, "", 0, 0, 'L');
        $this->pdf->Cell(14, 6, '$'.$cambio, 0, 0, 'L');
        $this->pdf->Ln(10);
        $this->pdf->Cell(54, 6, "E=EXENTO, G=GRAVADO, N=NO SUJETO, A=CTA.AJENA", 'B', 0, 'C');
        $this->pdf->Ln(10);
       $this->pdf->SetFont('Arial', 'B', 6);
        if ($datos_fac[0]->nombrecliente !=  null  || $datos_fac[0]->nombrecliente ='') {
            // str_replace('SAL', '', $datos_fac[0]->codigo_cliente) .' '.
               
            $this->pdf->Cell(54, 6, $datos_fac[0]->nombrecliente, 0, 0, 'C');//CLIENTE TICKET
        } else {
            $this->pdf->Cell(54, 6, str_replace('SAL', '', $datos_fac[0]->codigo_cliente) .' '. $datos_fac[0]->nombre_cliente, 0, 0, 'C');//CLIENTE TICKET
        }
        
        $this->pdf->Ln();
        $this->pdf->Cell(0, 10, "DOCUMENTO: ", 'B', 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Cell(0, 10, "NIT O DUI: ", 'B', 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Cell(0, 10, "FIRMA: ", 'B', 'B', 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->Cell(0, 4, utf8_decode('LA MANERA SMART'), 0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->Cell(0, 4, utf8_decode('PARA COMPRAR EN LINEA'), 0, 0, 'C');
         if($anulado ==   2){
            $this->pdf->Ln();
            $this->pdf->SetFont('Arial', 'B', 12);
            $this->pdf->Cell(0, 4, utf8_decode('ANULACION'), 0, 0, 'C');

        } 
        
        // $destino = getcwd() . "/" . "FILE.php";
      //  echo 'poner impreso el  comprobante ' .  $id_enc_Comprobante; 
        $CV->Ventas_Model->setimpreso($id_enc_Comprobante);

        //$this->pdf->Output($archivo  . '.pdf', 'f');
          
        //$this->pdf->Cell(52, 5, utf8_decode('CAJA: ') .utf8_decode($datos->caja),
        $nombrecaja = 'CAJA'.$datos->caja;
        
        $search         =  array('Suc.', 'Plaza Comercial las','Sucursal','Agencia Multicentro', 'C.C. Paseo Venecia',  'Agencia Express CC');
        $suc_name       =  $_SESSION["nombre_sucursal"] ;
        $sucursal1      =  trim(str_replace($search, '', $suc_name));
        $search1        =  array('_', '',' ');
        $nombre_suc     =  trim(str_replace($search1, '', $sucursal1));
                
        
        /*  if($id_sucursal  == 1){
           $sucursal ='sanfancisco';
       }else{
          $sucursal ='azaleas';

        
       }*/
       
        if($impreso ==  1){
            $destino      = getcwd() . "/document/reimpresiones/";
            $desabsoluto  =  "document/reimpresiones/";

        }else{
            $destino      = getcwd() . "/document/tickets/".$nombre_suc.'/'. $nombrecaja."/";
            $desabsoluto  =  "document/tickets/". $nombre_suc.'/'.$nombrecaja."/";

        } 
        /*if ($impreso ==  1) {
            $destino      = getcwd() . "/document/reimpresiones/";
            $desabsoluto  =  "document/reimpresiones/";
        } else {
            $destino      = getcwd() . "/document/tickets/". $nombrecaja."/";
            $desabsoluto  =  "document/tickets/". $nombrecaja."/";
        }*/
        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }
        $nombre_archivo =  'Ticket'.date("Ymd", strtotime(date("Y-m-d"))).$Ntiquete.'.pdf';
        //echo  'el nombre generado es ' . $nombre_archivo ;
        //$this->pdf->Output( $destino, $nombre_archivo , true);
        $this->pdf->Output("F", $destino. $nombre_archivo, true);
        $this->pdf->close();
       // echo 'generando banderas ';
        $datosretorno  = array('destino'        =>$desabsoluto,
                               'nombre_archivo' =>$nombre_archivo,
                               'caja'           =>strval($datos->caja),
                               'ticket'        =>intval($Ntiquete),
                               'idventa'        =>$id_enc_Comprobante,
                                );
        //echo 'llamando la funcion de recrear recibo';    
        //var_dump($datosretorno);     
        // llamar la  funcion para crear la cinta electronica 
       echo  json_encode($datosretorno);
        //echo $datosretorno;
        
      
    }
}