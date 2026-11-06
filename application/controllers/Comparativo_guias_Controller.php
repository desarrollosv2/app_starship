<?php

defined('BASEPATH') or exit('No direct script access allowed');
include getcwd()."/application/libraries/fpdf/fpdf.php";


class Comparativo_guias_Controller extends CI_Controller
{
  //Funcion Constructor en esta funcion se agregan todos los modelos que se usaran en el sitio
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->model('Comparativo_guias_Model');
  }

  public function comparativo_guias()
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $this->load->view("guias/form");
  }

  public function buscar_fecha_guia($fecha_desde_guia, $fecha_hasta_guia)
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $data['tracking'] = $this->Comparativo_guias_Model->buscar_fecha_guia($fecha_desde_guia, $fecha_hasta_guia);
    $this->load->view("guias/cuerpo", $data);
  }
  
  public function PDF_buscar_fecha_guia($fecha_desde_guia, $fecha_hasta_guia)
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

        // Inicializamos las variables que usaremos
        $stringmes = '';
        $id = 0;
        $Manifiesto  = 'LibroConsumidorFinal';
        $time        = time();
        $fecha       = date("d/m/Y");
        $dia         = date("d");
        $mes         = date("m");
        $periodo     = date("Y");
        $mora        = 0;
        $var = date('m', strtotime($fecha_desde_guia));
      //  echo $var;
        if ($var == '01')
        {
          $stringmes ='Enero';
        }
        else if($var == '02')
        {
          $stringmes ='Febrero';
        } 
        else if($var == '03')
        {
          $stringmes ='Marzo'; 
        } 
        else if($var == '04')
        {
          $stringmes ='Abril';  
        }
        else if($var == '05')
        {
          $stringmes ='Mayo';
        } 
        else if($var == '06')
        {
          $stringmes ='Junio';
        } 
        else if($var == '07')
        {
          $stringmes ='Julio';  
        }
        else if($var == '08')
        {
           $stringmes ='Agosto';
        }
        else if($var == '09')
        {
           $stringmes ='Septiembre';               
        }
        else if($var == '10')
        {
          $stringmes ='Octubre';            
        }
        else if($var == '11')
        {
          $stringmes ='Noviembre';
        }  
        else if($var == '12')
        {
          $stringmes ='Diciembre';
        }       
        setlocale(LC_ALL, 'es_ES');
        $monthNum    = $mes;
        $dateObj     = DateTime::createFromFormat('!m', $monthNum);
        $monthName   = strftime('%B', $dateObj->getTimestamp());
        $this->pdf   = new FPDF('L', 'mm', 'A4');
         
        $datos['consumidores'] = $this->Consumidores_Model->tabla_consumidores();
        foreach ($datos['consumidores'] as $fila) 
        {  
          $registro_iva         = $fila->registro_iva;
          $sucursal             = $fila->sucursal;
          $tipo_comprobante     = $fila->tipo_comprobante;
          $fecha                = $fila->fecha_comprobante;
          $comprob_minimo       = $fila->numero_minio;
          $comprob_maximo       = $fila->numero_maximo;
          $vta_exenta           = $fila->total_exento;
          $vta_int_grav         = $fila->total_gravado;
          $vta_no_suj           = $fila->total_no_sujeto;
          $exportacion          = $fila->total_exportacion;
          $retencion            = $fila->retencion_1_por_ciento;    
          $vta_propia           = $fila->venta_propia;   
          $vta_cta_terceros     = $fila->venta_cuenta_terceros;  
            
          $venta_total          = $fila->venta_total;
          $debitofiscal_propio  = $fila->debitofiscal_propio;              
          $debitofiscal_tercero = $fila->debitofiscal_tercero;

          $venta_diaria_propia  = $fila->venta_diaria_propia;

          $total_operaciones = $fila->total_operaciones;

          $exento_terceros      = $fila->exento_terceros;
          $gravado_terceros     = $fila->gravado_terceros;
          $gravado_iva_terceros = $fila->gravado_iva_terceros;
        }
        //Definiendo variables para totales 

        $svta_exenta             = 0; // Venta Exentas
        $svta_int_grav           = 0; // Ventas Internas Gravadas
        $svta_gravada            = 0; // Venta Interna Gravada Sin IVA
        $svta_grav_iva           = 0; // Venta Interna Gravada con valor neto de IVA
        $svta_int_nsuj           = 0; // Ventas Internas No Sujetas
        $svta_export             = 0; // Exportacion
        $sretencion              = 0; // Retencion 1%
        $svta_diaria             = 0; // Total de Ventas Diarias Propias
        $svta_ter                = 0; // Ventas a Cuentas de Terceros 
        $sdebitofiscal_propio    = 0;  
        $svta_total              = 0;
        $sexento_terceros        = 0; 
        $sgravado_terceros       = 0; 
        $sgravado_iva_terceros   = 0; 
        $sventa_diaria_propia    = 0; 


        $Tvta_exenta          = 0; // Total General de Venta Exentas
        $Tvta_int_grav        = 0; // Total General de Ventas Internas Gravadas
        $Tvta_gravada         = 0; // Total General de Venta Gravada Sin IVA
        $Tvta_grav_iva        = 0; // Total General de Venta Interna Gravada con valor neto de IVA
        $Tvta_int_nsuj        = 0; // Total General de Ventas Internas No Sujetas
        $Tvta_export          = 0; // Total General de Exportacion
        $Tretencion           = 0; // Total General de Retencion 1%
        $Tvta_diaria          = 0; // Total General de Total de Ventas Diarias Propias
        $Tvta_ter             = 0; // Total General de Ventas a Cuentas de Terceros 

        $Texento_terceros        = 0;
        $Tgravado_terceros       = 0;
        $Tgravado_iva_terceros   = 0;

        $Tventa_total          = 0;
        $Tdebitofiscal_propio  = 0;
        $Tdebitofiscal_tercero = 0;

        $Tventa_diaria_propia   = 0;
        
       
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetLeftMargin(5);
        $this->pdf->SetRightMargin(5);
        $this->pdf->SetFillColor(200, 200, 200);
        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->SetTextColor(46, 44, 44);
        $this->pdf->SetFont('Arial', '', 11);
   
 
        $this->pdf->SetFont('Arial', 'B', 16);
        $this->pdf->Cell(0, 16, 'LIBRO O REGISTRO DE OPERACIONES DE CONSUMIDORES FINALES', 0, 0, 'C', 0);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->Ln(10);
                           
        if ($_SESSION["empresa_id"] == 46) 
        {
          $this->pdf->SetFont('Arial', 'B', 12);
          $this->pdf->Cell(235, 16, utf8_decode('Star Mail, S.A. de C.V.'), 0, 0, 'L', 1); 
          $this->pdf->Cell(40, 16, 'Registro Fiscal:',     0, 0, 'L', 1); 
         
          $this->pdf->Cell(200, 16, $fila->registro_iva,      0, 0, 'L', 1);
          $this->pdf->Ln(5);
  
        } 
          else 
        {
          $this->pdf->SetFont('Arial', 'B', 12);
          $this->pdf->Cell(135, 16, utf8_decode('Global Cargo de El Salvador S.A. de C.V.'), 0, 0, 'L', 1);
       
          $this->pdf->Cell(40, 16, 'Registro Fiscal:',     0, 0, 'L', 1); 
         
          $this->pdf->Cell(200, 16, $fila->registro_iva,      0, 0, 'L', 1);
          $this->pdf->Ln(5);

        }
        
       $this->pdf->Ln(10);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->Cell(75, 8);
        $this->pdf->Cell(40, 8, 'MES:', 0, 0, 'C', 1);
        $this->pdf->Cell(40, 8, ($stringmes), 0, 0, 'L', 1);
        $this->pdf->Cell(30, 8, utf8_decode('AÑO:'), 0, 0, 'C', 1);
        $this->pdf->Cell(25, 8, date("Y",strtotime($fecha_desde_comprob)), 0, 0, 'L', 1);
        $this->pdf->Ln(10);
        
        // Creamos las cabeceras de la tabla con los datos que usaremos

        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->Cell(5,  6, utf8_decode(''),                              0, 0, 'C', 0);
        $this->pdf->Cell(10, 6, utf8_decode(''),                              0, 0, 'C', 0);
        $this->pdf->Cell(20, 6, utf8_decode('Fecha'),                         0, 0, 'C', 0);
        $this->pdf->Cell(15, 6, utf8_decode('Del No.'),                       0, 0, 'C', 0);
        $this->pdf->Cell(15, 6, utf8_decode('Al No.'),                        0, 0, 'C', 0);
        $this->pdf->Cell(30, 6, utf8_decode('Ventas Exentas'),                0, 0, 'C', 0);
        $this->pdf->Cell(40, 6, utf8_decode('Ventas Internas Gravadas'),      0, 0, 'C', 0);
        $this->pdf->Cell(40, 6, utf8_decode('Ventas Internas No Sujetas'),    0, 0, 'C', 0);
        $this->pdf->Cell(25, 6, utf8_decode('Exportación'),                   0, 0, 'C', 0);
        $this->pdf->Cell(20, 6, utf8_decode('Retención 1%'),                  0, 0, 'C', 0);
        $this->pdf->SetFont('Arial', '', 7);
        $this->pdf->Cell(33, 6, utf8_decode('Total Ventas Diarias Propias'),  0, 0, 'L', 0);
        $this->pdf->Cell(35, 6, utf8_decode('Ventas a Cuentas de Terceros'),  0, 0, 'L', 0);
        $this->pdf->Ln(2);
        $this->pdf->Cell(35, 6, utf8_decode('__________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________'),  7, 0, 'C', 0);
        $this->pdf->Ln(5);
        
        $datos['consumidores'] = $this->Consumidores_Model->tabla_consumidores();
        foreach ($datos['consumidores'] as $fila) 
        {  
            $sucursal             = $fila->sucursal;
            $tipo_comprobante     = $fila->tipo_comprobante;
            $fecha                = $fila->fecha_comprobante;
            $comprob_minimo       = $fila->numero_minio;
            $comprob_maximo       = $fila->numero_maximo;
            $vta_exenta           = $fila->total_exento;
            $vta_int_grav         = $fila->total_gravado;
            $vta_grav             = $fila->total_gravado_neto;
            $vta_grav_iva         = $fila->debitofiscal_propio;
            $vta_no_suj           = $fila->total_no_sujeto;
            $exportacion          = $fila->total_exportacion;
            $retencion            = $fila->retencion_1_por_ciento;    
            $vta_propia           = $fila->venta_propia;   
            $vta_cta_terceros     = $fila->venta_cuenta_terceros;  
            $debitofiscal_propio  = $fila->debitofiscal_propio;              
            $debitofiscal_tercero = $fila->debitofiscal_tercero;
            $venta_total          = $fila->venta_total;

            $exento_terceros      = $fila->exento_terceros;
            $gravado_terceros     = $fila->gravado_terceros;
            $gravado_iva_terceros = $fila->gravado_iva_terceros;

            $total_operaciones    = $fila->total_operaciones;
            $venta_diaria_propia  = $fila->venta_diaria_propia;


            //Buscamos si ya  existe el registro en la base de datos
            $datoresum =  $this->Consumidores_Model->tabla_consumidores();
     
            if (empty($datoresum)) 
            {              
              $fecha_desde_comprob =  '';
              $fecha_hasta_comprob =  '';
              $data = array(
                            'Sucursal'              => $sucursal,
                            'Tipo_Comprobante'      => $tipo_comprobante,  
                            'Fecha'                 => $fecha, 
                            'comprob_minimo'        => $comprob_minimo,        
                            'comprob_maximo'        => $comprob_maximo,
                            'vta_exenta'            => $vta_exenta, 
                            'vta_int_grav'          => $vta_int_grav,
                            'vta_no_suj'            => $vta_no_suj,
                            'exportacion'           => $exportacion,  
                            'retencion'             => $retencion,          
                            'vta_propia'            => $vta_propia,   
                            'vta_cta_terceros'      => $vta_cta_terceros,
                            'vta_grav_iva'          => $vta_grav_iva,
                            'vta_gravada'           => $vta_grav,
                            'exento_terceros'       => $exento_terceros,
                            'gravado_terceros'      => $gravado_terceros,
                            'gravado_iva_terceros'  => $gravado_iva_terceros,
                            'total_operaciones'     => $total_operaciones,
                            'venta_diaria_propia'   => $venta_diaria_propia,
                            'venta_total'           => $venta_total
                           );

              $this->Consumidores_Model->buscar_fecha_consumidores($fecha_desde_comprob, $fecha_hasta_comprob);
            } 
      } 
        //Obtenemos los datos almacenado en el resumen  
        $datos['consumidores'] = $this->Consumidores_Model->buscar_fecha_consumidores($fecha_desde_comprob, $fecha_hasta_comprob);
         foreach ($datos['consumidores'] as $fila) 
        { 
          if($fila->tipo_comprobante != 'CCF' AND $fila->tipo_comprobante != 'NCR')
         { 
          $this->pdf->SetLeftMargin(5);
          $this->pdf->SetRightMargin(5);
          $this->pdf->SetFont('Arial', '', 8);
          $this->pdf->Cell(5,  6, $fila->sucursal,                                        0, 0, 'R', 1);
          $this->pdf->Cell(10, 6, $fila->tipo_comprobante,                                0, 0, 'R', 1);
          $this->pdf->Cell(20, 6, date("d-m-Y", strtotime($fila->fecha_comprobante)),     0, 0, 'R', 1);
          $this->pdf->Cell(15, 6, $fila->numero_minio,                                    0, 0, 'R', 1);
          $this->pdf->Cell(15, 6, $fila->numero_maximo,                                   0, 0, 'R', 1);
          $this->pdf->Cell(30, 6, "$".number_format($fila->total_exento, 2),              0, 0, 'R', 1);
          $this->pdf->Cell(40, 6, "$".number_format($fila->total_gravado, 2),             0, 0, 'R', 1);
          $this->pdf->Cell(40, 6, "$".number_format($fila->total_no_sujeto, 2),           0, 0, 'R', 1);
          $this->pdf->Cell(25, 6, "$".number_format($fila->total_exportacion, 2),         0, 0, 'R', 1);
          $this->pdf->Cell(20, 6, "$".number_format($fila->retencion_1_por_ciento, 2),    0, 0, 'R', 1);
          $this->pdf->Cell(33, 6, "$".number_format($fila->venta_diaria_propia, 2),              0, 0, 'R', 1);
          $this->pdf->Cell(35, 6, "$".number_format($fila->venta_cuenta_terceros, 2),     0, 0, 'R', 1);
          $this->pdf->Ln();
    
          //Espacio para la sumatoria de totales de las ventas  
              $svta_exenta    = $svta_exenta   + $fila->total_exento;
              $svta_int_grav  = $svta_int_grav + $fila->total_gravado;
              $svta_int_nsuj  = $svta_int_nsuj + $fila->total_no_sujeto;
              $svta_export    = $svta_export   + $fila->total_exportacion;
              $sretencion     = $sretencion    + $fila->retencion_1_por_ciento;
              $svta_diaria    = $svta_diaria   + $fila->venta_propia;
              $svta_ter       = $svta_ter      + $fila->venta_cuenta_terceros;
              $venta_total    = $venta_total   + $fila->venta_total;

              $sdebitofiscal_propio   = $sdebitofiscal_propio  + $fila->debitofiscal_propio;
              $sexento_terceros       = $sexento_terceros      + $fila->exento_terceros;
              $sgravado_terceros      = $sgravado_terceros     + $fila->gravado_terceros;
              $sgravado_iva_terceros  = $sgravado_iva_terceros + $fila->gravado_iva_terceros;

              $svta_gravada           = $svta_gravada  + $fila->total_gravado_neto;
              $sventa_diaria_propia   = $sventa_diaria_propia + $fila->venta_diaria_propia;
     }
      }
        $this->pdf->SetFont('Arial', 'B', 11); 
        $this->pdf->Cell(65, 6, utf8_decode('Totales del Mes'),           0, 0, 'R', 1);
        $this->pdf->SetFont('Arial', 'B', 10); 
        $this->pdf->Cell(30, 6, "$".number_format($svta_exenta, 2),       0, 0, 'R', 1);
        $this->pdf->Cell(40, 6, "$".number_format($svta_int_grav, 2),     0, 0, 'R', 1);
        $this->pdf->Cell(40, 6, "$".number_format($svta_int_nsuj, 2),     0, 0, 'R', 1);
        $this->pdf->Cell(25, 6, "$".number_format($svta_export, 2),       0, 0, 'R', 1);
        $this->pdf->Cell(20, 6, "$".number_format($sretencion, 2),        0, 0, 'R', 1);
        $this->pdf->Cell(33, 6, "$".number_format($sventa_diaria_propia, 2),       0, 0, 'R', 1);
        $this->pdf->Cell(35, 6, "$".number_format($svta_ter, 2),          0, 0, 'R', 1);
        $this->pdf->Ln(20);
    
        $Tvta_gravada           = $svta_gravada;  + $fila->total_gravado_neto;
        $Tvta_exenta            = $svta_exenta;   + $fila->total_exento;
        $Tvta_int_nsuj          = $svta_int_nsuj; + $fila->total_no_sujeto; 
        $Tretencion             = $sretencion; +  $fila->retencion_1_por_ciento;
        $Tdebitofiscal_propio   = $sdebitofiscal_propio;  + $fila->debitofiscal_propio;
        $Tventa_total           = $Tvta_gravada + $Tvta_int_nsuj + $Tretencion + $Tvta_exenta;

        $Texento_terceros       = $sexento_terceros;      + $fila->exento_terceros;
        $Tgravado_terceros      = $sgravado_terceros;     + $fila->gravado_terceros;
        $Tgravado_iva_terceros  = $sgravado_iva_terceros; + $fila->gravado_iva_terceros;

        $this->pdf->SetFont('Arial', 'B', 11); 
        $this->pdf->Cell(108, 6, utf8_decode('Resumen de Operaciones:'),       0, 0, 'C', 1);
        $this->pdf->Cell(90,  6, utf8_decode('Propias'),                       0, 0, 'C', 1);
        $this->pdf->Cell(90,  6, utf8_decode('A Cuenta a Terceros'),           0, 0, 'C', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode(''));
        $this->pdf->Cell(45,  6, utf8_decode('Valor Total'),                   0, 0, 'C', 1);
        $this->pdf->Cell(45,  6, utf8_decode('Debito Fiscal'),                 0, 0, 'C', 1);
        $this->pdf->Cell(45,  6, utf8_decode('Valor Total'),                   0, 0, 'C', 1);
        $this->pdf->Cell(45,  6, utf8_decode('Debito Fiscal'),                 0, 0, 'C', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Ventas Internas Gravadas a Consumidores'),          0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tvta_gravada, 2),             0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tdebitofiscal_propio, 2),     0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tgravado_terceros, 2),        0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tgravado_iva_terceros, 2),    0, 0, 'R', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Ventas Internas Exentas a Consumidores'),          0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tvta_exenta, 2),              0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Texento_terceros, 2),         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Ventas Internas No Sujetas a Consumidores'),       0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tvta_int_nsuj, 2),            0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Total Retención 1%'),               0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tretencion, 2),               0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Total Operaciones a Consumidores'), 0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tventa_total, 2),             1, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tdebitofiscal_propio, 2),     1, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         1, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         1, 0, 'R', 1);
        $this->pdf->Ln(10);
        $this->pdf->Cell(108, 6, utf8_decode('Exportaciones Según Facturas de Exportación'),     0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($svta_export, 2),           1, 0, 'R', 1);
       
 
        $destino      = getcwd() . "/document/libroconsumidores/";
        $desabsoluto  =  "document/libroconsumidores/";
        if (!is_dir($destino)) 
        {
          mkdir($destino, 0777, true);
        }
          
        $nombre_archivo =  'LibroConsumidorFinal'.'.pdf';
        $this->pdf->Output("F", $destino.$nombre_archivo, true);
        $this->pdf->close();
        $datosretorno  = array(
                                'destino'        =>$desabsoluto,
                                'nombre_archivo' =>$nombre_archivo,
                              );      
        echo json_encode($datosretorno);
      }
      
  public function PDF_consumidores($fecha_desde_comprob, $fecha_hasta_comprob)
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

        // Inicializamos las variables que usaremos
        $stringmes = '';
        $id = 0;
        $Manifiesto  = 'LibroConsumidorFinal';
        $time        = time();
        $fecha       = date("d/m/Y");
        $dia         = date("d");
        $mes         = date("m");
        $periodo     = date("Y");
        $mora        = 0;
        $var = date('m', strtotime($fecha_desde_comprob));
      //  echo $var;
        if ($var == '01')
        {
          $stringmes ='Enero';
        }
        else if($var == '02')
        {
          $stringmes ='Febrero';
        } 
        else if($var == '03')
        {
          $stringmes ='Marzo'; 
        } 
        else if($var == '04')
        {
          $stringmes ='Abril';  
        }
        else if($var == '05')
        {
          $stringmes ='Mayo';
        } 
        else if($var == '06')
        {
          $stringmes ='Junio';
        } 
        else if($var == '07')
        {
          $stringmes ='Julio';  
        }
        else if($var == '08')
        {
           $stringmes ='Agosto';
        }
        else if($var == '09')
        {
           $stringmes ='Septiembre';               
        }
        else if($var == '10')
        {
          $stringmes ='Octubre';            
        }
        else if($var == '11')
        {
          $stringmes ='Noviembre';
        }  
        else if($var == '12')
        {
          $stringmes ='Diciembre';
        }       
        setlocale(LC_ALL, 'es_ES');
        $monthNum    = $mes;
        $dateObj     = DateTime::createFromFormat('!m', $monthNum);
        $monthName   = strftime('%B', $dateObj->getTimestamp());
        $this->pdf   = new FPDF('L', 'mm', 'A4');
         
        $datos_RPIVA = $this->Consumidores_Model->tabla_consumidores();        
        //Definiendo variables para totales 

        $svta_exenta             = 0; // Venta Exentas
        $svta_int_grav           = 0; // Ventas Internas Gravadas
        $svta_gravada            = 0; // Venta Interna Gravada Sin IVA
        $svta_grav_iva           = 0; // Venta Interna Gravada con valor neto de IVA
        $svta_int_nsuj           = 0; // Ventas Internas No Sujetas
        $svta_export             = 0; // Exportacion
        $sretencion              = 0; // Retencion 1%
        $svta_diaria             = 0; // Total de Ventas Diarias Propias
        $svta_ter                = 0; // Ventas a Cuentas de Terceros 
        $sdebitofiscal_propio    = 0;  
        $svta_total              = 0;
        $sexento_terceros        = 0; 
        $sgravado_terceros       = 0; 
        $sgravado_iva_terceros   = 0; 
        $sventa_diaria_propia    = 0; 
         $venta_total  = 0;


        $Tvta_exenta             = 0; // Total General de Venta Exentas
        $Tvta_int_grav           = 0; // Total General de Ventas Internas Gravadas
        $Tvta_gravada            = 0; // Total General de Venta Gravada Sin IVA
        $Tvta_grav_iva           = 0; // Total General de Venta Interna Gravada con valor neto de IVA
        $Tvta_int_nsuj           = 0; // Total General de Ventas Internas No Sujetas
        $Tvta_export             = 0; // Total General de Exportacion
        $Tretencion              = 0; // Total General de Retencion 1%
        $Tvta_diaria             = 0; // Total General de Total de Ventas Diarias Propias
        $Tvta_ter                = 0; // Total General de Ventas a Cuentas de Terceros 
        $Texento_terceros        = 0;
        $Tgravado_terceros       = 0;
        $Tgravado_iva_terceros   = 0;

        $Tventa_total            = 0;
        $Tdebitofiscal_propio    = 0;
        $Tdebitofiscal_tercero   = 0;

        $Tventa_diaria_propia   = 0;
        
       
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetLeftMargin(5);
        $this->pdf->SetRightMargin(5);
        $this->pdf->SetFillColor(200, 200, 200);
        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->SetTextColor(46, 44, 44);
        $this->pdf->SetFont('Arial', '', 11);
   
 
        $this->pdf->SetFont('Arial', 'B', 16);
        $this->pdf->Cell(0, 16, 'LIBRO O REGISTRO DE OPERACIONES DE CONSUMIDORES FINALES', 0, 0, 'C', 0);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->Ln(10);
                           
        if ($_SESSION["empresa_id"] == 46) 
        {
          $this->pdf->SetFont('Arial', 'B', 12);
          $this->pdf->Cell(225, 16, utf8_decode('Star Mail, S.A. de C.V.'), 0, 0, 'L', 1); 
          $this->pdf->Cell(40, 16, 'Registro Fiscal:',     0, 0, 'L', 1); 
         
          $this->pdf->Cell(30, 16, $datos_RPIVA[0]->registro_iva,      0, 0, 'L', 1);
          $this->pdf->Ln(5);
  
        } 
          else 
        {
          $this->pdf->SetFont('Arial', 'B', 12);
          $this->pdf->Cell(135, 16, utf8_decode('Global Cargo de El Salvador S.A. de C.V.'), 0, 0, 'L', 1);
       
          $this->pdf->Cell(40, 16, 'Registro Fiscal:',     0, 0, 'L', 1); 
         
          $this->pdf->Cell(30, 16, $datos_RPIVA[0]->registro_iva,      0, 0, 'L', 1);
          $this->pdf->Ln(5);

        }
        
       $this->pdf->Ln(10);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->Cell(75, 8);
        $this->pdf->Cell(40, 8, 'MES:', 0, 0, 'C', 1);
        $this->pdf->Cell(40, 8, ($stringmes), 0, 0, 'L', 1);
        $this->pdf->Cell(30, 8, utf8_decode('AÑO:'), 0, 0, 'C', 1);
        $this->pdf->Cell(25, 8, date("Y",strtotime($fecha_desde_comprob)), 0, 0, 'L', 1);
        $this->pdf->Ln(10);
        
        // Creamos las cabeceras de la tabla con los datos que usaremos

        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->Cell(5,  6, utf8_decode(''),                              0, 0, 'C', 0);
        $this->pdf->Cell(10, 6, utf8_decode(''),                              0, 0, 'C', 0);
        $this->pdf->Cell(20, 6, utf8_decode('Fecha'),                         0, 0, 'C', 0);
        $this->pdf->Cell(15, 6, utf8_decode('Del No.'),                       0, 0, 'C', 0);
        $this->pdf->Cell(15, 6, utf8_decode('Al No.'),                        0, 0, 'C', 0);
        $this->pdf->Cell(30, 6, utf8_decode('Ventas Exentas'),                0, 0, 'C', 0);
        $this->pdf->Cell(40, 6, utf8_decode('Ventas Internas Gravadas'),      0, 0, 'C', 0);
        $this->pdf->Cell(40, 6, utf8_decode('Ventas Internas No Sujetas'),    0, 0, 'C', 0);
        $this->pdf->Cell(25, 6, utf8_decode('Exportación'),                   0, 0, 'C', 0);
        $this->pdf->Cell(20, 6, utf8_decode('Retención 1%'),                  0, 0, 'C', 0);
        $this->pdf->SetFont('Arial', '', 7);
        $this->pdf->Cell(33, 6, utf8_decode('Total Ventas Diarias Propias'),  0, 0, 'L', 0);
        $this->pdf->Cell(35, 6, utf8_decode('Ventas a Cuentas de Terceros'),  0, 0, 'L', 0);
        $this->pdf->Ln(2);
        $this->pdf->Cell(35, 6, utf8_decode('__________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________'),  7, 0, 'C', 0);
        $this->pdf->Ln(5);
        
      
        //Obtenemos los datos almacenado en el resumen  
        $datos['consumidores'] = $this->Consumidores_Model->buscar_fecha_consumidores($fecha_desde_comprob, $fecha_hasta_comprob);

        foreach ($datos['consumidores'] as $fila) 
        { 
          if($fila->tipo_comprobante != 'CCF' AND $fila->tipo_comprobante != 'NCR')
                { 
                    $this->pdf->SetLeftMargin(5);
                    $this->pdf->SetRightMargin(5);
                    $this->pdf->SetFont('Arial', '', 8);
                    $this->pdf->Cell(5,  6, $fila->sucursal,                                        0, 0, 'R', 1);
                    $this->pdf->Cell(10, 6, $fila->tipo_comprobante,                                0, 0, 'R', 1);
                    $this->pdf->Cell(20, 6, date("d-m-Y", strtotime($fila->fecha_comprobante)),     0, 0, 'R', 1);
                    $this->pdf->Cell(15, 6, $fila->numero_minio,                                    0, 0, 'R', 1);
                    $this->pdf->Cell(15, 6, $fila->numero_maximo,                                   0, 0, 'R', 1);
                    $this->pdf->Cell(30, 6, "$".number_format($fila->total_exento, 2),              0, 0, 'R', 1);
                    $this->pdf->Cell(40, 6, "$".number_format($fila->total_gravado, 2),             0, 0, 'R', 1);
                    if($fila->tipo_comprobante == 'FEX'){ 
                        $this->pdf->Cell(40, 6, "$".number_format(0, 2),           0, 0, 'R', 1);
                        $this->pdf->Cell(25, 6, "$".number_format($fila->total_exportacion + $fila->total_no_sujeto , 2),         0, 0, 'R', 1);                        
                        $this->pdf->Cell(20, 6, "$".number_format($fila->retencion_1_por_ciento, 2), 0, 0, 'R', 1);
                        $this->pdf->Cell(33, 6, "$".number_format($fila->venta_diaria_propia, 2), 0, 0, 'R', 1);
                        $this->pdf->Cell(35, 6, "$".number_format($fila->venta_cuenta_terceros, 2), 0, 0, 'R', 1);

                    }else{                       
                         $this->pdf->Cell(40, 6, "$".number_format($fila->total_no_sujeto, 2), 0, 0, 'R', 1);
                         $this->pdf->Cell(25, 6, "$".number_format($fila->total_exportacion + $fila->total_no_sujeto, 2), 0, 0, 'R', 1);
                        $this->pdf->Cell(20, 6, "$".number_format($fila->retencion_1_por_ciento, 2), 0, 0, 'R', 1);
                        $this->pdf->Cell(33, 6, "$".number_format($fila->venta_diaria_propia, 2), 0, 0, 'R', 1);
                        $this->pdf->Cell(35, 6, "$".number_format($fila->venta_cuenta_terceros, 2), 0, 0, 'R', 1);
                    }
                $this->pdf->Ln();

                    //Espacio para la sumatoria de totales de las ventas  
                    $svta_exenta            = $svta_exenta           + $fila->total_exento;
                    $svta_int_grav          = $svta_int_grav         + $fila->total_gravado;
                    if($fila->tipo_comprobante == 'FEX'){ 
                        // ECHO 'INGRESANDO EN   FEX';  
                          $svta_int_nsuj          = 0; //$svta_int_nsuj         + $fila->total_no_sujeto;
                          $svta_export            = $svta_export           + $fila->total_exportacion + $fila->total_no_sujeto ;
                    }else{
                      //  ECHO  'OTRO TIPO DE  COMPROBANTE';
                        $svta_int_nsuj          = $svta_int_nsuj         + $fila->total_no_sujeto;
                        $svta_export            = $svta_export           + $fila->total_exportacion;
                        $sretencion             = $sretencion            + $fila->retencion_1_por_ciento;
                        $svta_diaria            = $svta_diaria           + $fila->venta_propia;
                        $svta_ter               = $svta_ter              + $fila->venta_cuenta_terceros;
                        $venta_total            = $venta_total           + $fila->venta_total;
                        $sdebitofiscal_propio   = $sdebitofiscal_propio  + $fila->debitofiscal_propio;
                        $sexento_terceros       = $sexento_terceros      + $fila->exento_terceros;
                        $sgravado_terceros      = $sgravado_terceros     + $fila->gravado_terceros;
                        $sgravado_iva_terceros  = $sgravado_iva_terceros + $fila->gravado_iva_terceros;
                        $svta_gravada           = $svta_gravada          + $fila->total_gravado_neto;
                        $sventa_diaria_propia   = $sventa_diaria_propia  + $fila->venta_diaria_propia;
                    }
                }
        }
        $this->pdf->SetFont('Arial', 'B', 11); 
        $this->pdf->Cell(65, 6, utf8_decode('Totales del Mes'),           0, 0, 'R', 1);
        $this->pdf->SetFont('Arial', 'B', 10); 
        $this->pdf->Cell(30, 6, "$".number_format($svta_exenta, 2),       0, 0, 'R', 1);
        $this->pdf->Cell(40, 6, "$".number_format($svta_int_grav, 2),     0, 0, 'R', 1);
        $this->pdf->Cell(40, 6, "$".number_format($svta_int_nsuj, 2),     0, 0, 'R', 1);
        $this->pdf->Cell(25, 6, "$".number_format($svta_export, 2),       0, 0, 'R', 1);
        $this->pdf->Cell(20, 6, "$".number_format($sretencion, 2),        0, 0, 'R', 1);
        $this->pdf->Cell(33, 6, "$".number_format($sventa_diaria_propia, 2),       0, 0, 'R', 1);
        $this->pdf->Cell(35, 6, "$".number_format($svta_ter, 2),          0, 0, 'R', 1);
        $this->pdf->Ln(20);
    
        $Tvta_gravada           += $svta_gravada;          // + $fila->total_gravado_neto;
        $Tvta_exenta            += $svta_exenta;           // + $fila->total_exento;
        $Tvta_int_nsuj          += $svta_int_nsuj;         // + $fila->total_no_sujeto; 
        $Tretencion             += $sretencion;            // + $fila->retencion_1_por_ciento;
        $Tdebitofiscal_propio   += $sdebitofiscal_propio;  // + $fila->debitofiscal_propio;
        $Tventa_total           += $Tvta_gravada;           // + $Tvta_int_nsuj + $Tretencion + $Tvta_exenta;
        $Texento_terceros       += $sexento_terceros;      // + $fila->exento_terceros;
        $Tgravado_terceros      += $sgravado_terceros;     // + $fila->gravado_terceros;
        $Tgravado_iva_terceros  += $sgravado_iva_terceros; // + $fila->gravado_iva_terceros;

        $this->pdf->SetFont('Arial', 'B', 11); 
        $this->pdf->Cell(108, 6, utf8_decode('Resumen de Operaciones:'),       0, 0, 'C', 1);
        $this->pdf->Cell(90,  6, utf8_decode('Propias'),                       0, 0, 'C', 1);
        $this->pdf->Cell(90,  6, utf8_decode('A Cuenta a Terceros'),           0, 0, 'C', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode(''));
        $this->pdf->Cell(45,  6, utf8_decode('Valor Total'),                   0, 0, 'C', 1);
        $this->pdf->Cell(45,  6, utf8_decode('Debito Fiscal'),                 0, 0, 'C', 1);
        $this->pdf->Cell(45,  6, utf8_decode('Valor Total'),                   0, 0, 'C', 1);
        $this->pdf->Cell(45,  6, utf8_decode('Debito Fiscal'),                 0, 0, 'C', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Ventas Internas Gravadas a Consumidores'),          0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tvta_gravada, 2),             0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tdebitofiscal_propio, 2),     0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tgravado_terceros, 2),        0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tgravado_iva_terceros, 2),    0, 0, 'R', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Ventas Internas Exentas a Consumidores'),          0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tvta_exenta, 2),              0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Texento_terceros, 2),         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Ventas Internas No Sujetas a Consumidores'),       0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tvta_int_nsuj, 2),            0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Total Retención 1%'),               0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tretencion, 2),               0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         0, 0, 'R', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(108, 6, utf8_decode('Total Operaciones a Consumidores'), 0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tventa_total, 2),             1, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format($Tdebitofiscal_propio, 2),     1, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         1, 0, 'R', 1);
        $this->pdf->Cell(45,  6, "$".number_format(0, 2),                         1, 0, 'R', 1);
        $this->pdf->Ln(10);
        $this->pdf->Cell(108, 6, utf8_decode('Exportaciones Según Facturas de Exportación'),     0, 0, 'L', 1);
        $this->pdf->Cell(45,  6, "$".number_format($svta_export, 2),           1, 0, 'R', 1);
       
 
        $destino      = getcwd() . "/document/libroconsumidores/";
        $desabsoluto  =  "document/libroconsumidores/";
        if (!is_dir($destino)) 
        {
          mkdir($destino, 0777, true);
        }
          
        $nombre_archivo =  'LibroConsumidorFinal'.'.pdf';
        $this->pdf->Output("F", $destino.$nombre_archivo, true);
        $this->pdf->close();
        $datosretorno  = array(
                                'destino'        =>$desabsoluto,
                                'nombre_archivo' =>$nombre_archivo,
                              );      
        echo json_encode($datosretorno);
      }      
}