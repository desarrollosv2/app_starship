<?php

defined('BASEPATH') or exit('No direct script access allowed');
include getcwd()."/application/libraries/fpdf/fpdf.php";


class ListadoLMD_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('ListadoLMD_Model');
    }

    public function listado_lmd()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $datos['listado_lmd']    =    $this->ListadoLMD_Model->encabezado_manif();
        $this->load->view("lista_lmd/form", $datos);
        //var_dump($datos);
    }


  //Funcion para Ingresar al boton de Agregar 
  public function agregar_manifiesto($id)
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $datos['listado_lmd'] = $this->ListadoLMD_Model->crear_encabezado($id);
    $this->load->view('lista_lmd/modal_form', $datos);
  }

  // Funcion para Guardar los registros del formulario de partidas
  public function guardar_manifiesto()
  {
    //Muestra Error en el Codigo
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
        // Variable            Nombre segun input
        $id                 =  $_POST["id"]; //Enviamos los datos por medio del metodo POST
        $fecha_manifiesto   =  $_POST["fecha_manifiesto"];
        //$tipo_entrega       =  $_POST["tipo_entrega"];
        $observaciones      =  $_POST["observaciones"];
       
   
        //los datos son enviados por una cadena de datos
        //Los datos deben llamarse segun tabla 
        $data = array(
                            'id'                => $id,        
                            'fecha_manifiesto'  => $fecha_manifiesto,   
            
                            'observaciones'     => $observaciones, 
                              
        );  
   
        $this->ListadoLMD_Model->guardar_manifiesto($id, $data);
    }


    public function ver_manifiesto($id)
    {
        $datos['listado_man']     =    $this->ListadoLMD_Model->vista_manif($id);
        $datos['ver_manifiesto']  =    $this->ListadoLMD_Model->detalle_manif($id);

        $this->load->view("lista_lmd/form_man", $datos);
    }

    public function guardar_guia_man($id, $no_guia)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //$no_guia      =    $_POST["no_guia"];

        //Los datos deben llamarse segun tabla 
        $data = array(
                      'id_manifiesto'  =>   $id,   
                      'no_guia'        =>   $no_guia
                    );  

        //var_dump($data);
        $this->ListadoLMD_Model->guardar_guia_man($data);      
    }

    public function editar_manifiesto($id)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //echo("Llegando al id: ".$id);
        $datos['listado_lmd'] = $this->ListadoLMD_Model->ver_encabezado_manif($id);
        //$this->load->view("lista_lmd/form", $datos);

    }


    public function eliminar_manifiesto($id_guia)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->ListadoLMD_Model->eliminar_manifiesto($id_guia);     
    }

    public function eliminar_guia_manifiesto($id_guia)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->ListadoLMD_Model->eliminar_guia_manifiesto($id_guia);     
    }


    public function generar_guia_pdf($id, $fecha_manifiesto, $observaciones)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //$id            =     0;
        $Manifiesto    =    '';
        $time          =    time();
        $dia           =    date("d");
        $mes           =    date("m");
        $fecha         =    date("d-m-Y ");
        $periodo       =    date("Y");
        $mora          =    0;
        setlocale(LC_ALL, 'es_ES');
        $monthNum      =    $mes;
        $dateObj       =    DateTime::createFromFormat('!m', $monthNum);
        $monthName     =    strftime('%B', $dateObj->getTimestamp());



        
     
        $GLOBALS['tipo']  =  trim(str_replace([' ',' ','%'], ' ', $observaciones));
        $GLOBALS['fecha'] = "Fecha " .$fecha_manifiesto; 
       
        $pdf = new PDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', '', 11);
        $datos         =    $this->ListadoLMD_Model->detalle_manif($id);
        $pdf->cuerpoguias($datos);

        $destino = getcwd() . "/document/reporte_ventas/";

        if (!is_dir($destino)) 
        {
            mkdir($destino, 0777, true);
        }
      
        $archivo = 'Manifiesto'.'.pdf';
        $pdf->Output("F", $destino . $archivo, true);
        $pdf->close();
        echo  $archivo;
    }    
}
class PDF extends FPDF
{
    public function Header()
    {
        $this->SetFont('Arial', '', 15);
        $this->SetFillColor(255, 255, 255);
        $this->SetFillColor(255, 255, 255);

        // Mostramos la fecha en la que se genero dicho reporte
        $this->SetFont('Arial', '', 12);
        $this->Cell(80, 8);
        $this->Cell(90, 8, $GLOBALS['tipo'], 0, 0, 'L', 1);
        $this->Ln(10);
        $this->Cell(90, 8, $GLOBALS['fecha'], 0, 0, 'L', 1);
        $this->Ln(10);

        // Creamos las cabeceras de la tabla con los datos que usaremos
        $this->SetFont('Arial', '', 10);
        $this->Cell(10, 8,   utf8_decode('#'),                      1, 0, 'C', 1);
        $this->Cell(25, 8,   utf8_decode('Master'),                 1, 0, 'C', 1);  
        $this->Cell(20, 8,   utf8_decode('Referencia'),             1, 0, 'C', 1);  
        $this->Cell(30, 8,   utf8_decode('Guias'),                  1, 0, 'C', 1);          
        $this->Cell(65, 8,   utf8_decode('Cliente'),                1, 0, 'C', 1);
        $this->Cell(60, 8,   utf8_decode('Descripcion'),            1, 0, 'C', 1);
        $this->Cell(15, 8,   utf8_decode('Tipo'),                   1, 0, 'C', 1);
        $this->Cell(15, 8,   utf8_decode('Valor'),                  1, 0, 'C', 1);
        $this->Cell(15, 8,   utf8_decode('IVA'),                    1, 0, 'C', 1);
        $this->Cell(15, 8,   utf8_decode('DAI'),                    1, 0, 'C', 1);
        $this->Cell(15, 8,   utf8_decode('Total'),                  1, 0, 'C', 1);
        $this->Ln();
    }

    public function cuerpoguias($datos)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //echo 'generando los datos para el   libro diarios';

        $contador1          =  1;
        $sventa_total       =  0;

        foreach ($datos as $fila) 
        { 
            $manifiesto         =   $fila->manifiesto;
            $referencia         =   $fila->referencia;
            $no_guia            =   $fila->no_guia;
            $consignee          =   $fila->consignee;
            $tipo_entrega       =   $fila->tipo_entrega;
            $description        =   $fila->commodity;
            $value              =   $fila->value;
            $iva                =   $fila->iva;
            $dai                =   $fila->dai;
            $cobro_final        =   $fila->cobro_final;

            $this->SetFont('Arial', '', 8);
            $this->SetFillColor(255, 255, 255);
            $this->SetFillColor(255, 255, 255);

            $this->Cell(10,  6,   $contador1,                              1, 0, 'C', 1);
            
            $this->Cell(25,  6,   $manifiesto,                             1, 0, 'L', 1);
            $this->Cell(20,  6,   $referencia,                             1, 0, 'L', 1);
            $this->Cell(30,  6,   $no_guia,                                1, 0, 'L', 1);
            $this->Cell(65,  6,   utf8_decode($consignee),                 1, 0, 'L', 1);
            $this->Cell(60,  6,   utf8_decode($description),               1, 0, 'L', 1);
            $this->Cell(15,  6,   utf8_decode($tipo_entrega),              1, 0, 'C', 1);
            $this->Cell(15,  6,   '$'.number_format($value,2),             1, 0, 'R', 1);
            $this->Cell(15,  6,   '$'.number_format($iva,2),               1, 0, 'R', 1);
            $this->Cell(15,  6,   '$'.number_format($dai,2),               1, 0, 'R', 1);
            $this->Cell(15,  6,   '$'.number_format($cobro_final,2),       1, 0, 'R', 1);
            $this->Ln();

            $contador1   += 1;
        }
    }
      
    //   segmemento para  genarar  le  nuevo detalle del   libro diario

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, ''.$this->PageNo(), 0, 0, 'C');
    }
}