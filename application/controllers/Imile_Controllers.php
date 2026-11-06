<?php
defined('BASEPATH') or exit('No direct script access allowed');
include getcwd() . "/application/libraries/fpdf/fpdf.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

class Imile_Controllers extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Imile_Model');
        $this->load->helper('path');
        session_start();
    }

    public function imile_index()
    {
        $this->load->view('imile/imile');
    }

    public function buscar_master()
    {
        $fecha_inicio   = $this->input->post('fecha_inicio_imile');
        $fecha_fin      = $this->input->post('fecha_fin_imile');

        $data['master'] = $this->Imile_Model->buscar_master($fecha_inicio, $fecha_fin);
        $this->load->view('imile/tbl_master', $data);
    }

    public function cargar_vista_trackings($id)
    {
        $data['manifiesto'] = $this->Imile_Model->obtener_datos_manifiesto($id);
        $this->load->view('imile/ver_trackings_imile', $data);
    }

    public function obtener_trackings($id)
    {
        $data['trackings'] = $this->Imile_Model->obtener_datos_trackings($id);
        $this->load->view('imile/tbl_trackings_imile', $data);
    }

    public function verificar_excel()
    {
        if (isset($_FILES['archivo']['tmp_name'])) {
            $tipo = $this->input->post('tipo');
            $archivo = $_FILES['archivo']['tmp_name'];

            $spreadsheet = IOFactory::load($archivo);
            $hoja = $spreadsheet->getActiveSheet();
            $datos = $hoja->rangesToArray('A2:A' . $hoja->getHighestRow(), NULL, TRUE, FALSE);

            $trackings = array_filter(array_map('trim', array_column($datos, 0)));
            $cantidad = $this->Imile_Model->contar_trackings_existentes($trackings);

            echo json_encode(['status' => 'ok', 'cantidad' => $cantidad]);
        } else {
            echo json_encode(['status' => 'error', 'messege' => 'Archivo no recibido']);
        }
    }

    public function actualizar_datos()
    {
        if (isset($_FILES['archivo']['tmp_name'])) {
            $tipo = $this->input->post('tipo');
            $archivo = $_FILES['archivo']['tmp_name'];

            $spreadsheet = IOFactory::load($archivo);
            $hoja = $spreadsheet->getActiveSheet();
            $datos = $hoja->rangesToArray('A2:A' . $hoja->getHighestRow(), NULL, TRUE, FALSE);

            $trackings = array_filter(array_map('trim', array_column($datos, 0)));
            $actualizados = $this->Imile_Model->actualizar_datos($trackings, $tipo);

            echo json_encode(['status' => 'ok', 'messege' => "Se actualizaron {$actualizados} registros en la columna '{$tipo}' "]);
        } else {
            echo json_encode(['status' => 'error', 'messege' => 'Archivo no procesado']);
        }
    }


    public function generar_grafica_general()
    {
        ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
        $fecha_inicio = $this->input->post('fecha_inicio_imile');
        $fecha_fin    = $this->input->post('fecha_fin_imile');
        $_SESSION['fi']= $fecha_inicio;
          $_SESSION['ff']= $fecha_fin;

        $datos = $this->Imile_Model->obtener_datos_grafica_general($fecha_inicio, $fecha_fin);

        if (empty($datos)) {
            echo json_encode(['status' => 'empty']);
            return;
        }

        // Carga la vista como string
        $html = $this->load->view('imile/ver_grafica_general', ['datos' => $datos], true);

        echo json_encode([
            'status' => 'ok',
            'html'   => $html
        ]);
    }
    public function ver_grafica($id)
    {
        $data['datos'] = $this->Imile_Model->obtener_datos_grafica($id);
        $this->load->view('imile/ver_grafica', $data);
    }
}
