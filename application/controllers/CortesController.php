<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CortesController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('CortesModel');
	}

	public function index()
	{

		$data['cortes'] = $this->CortesModel->mostrarCortes();
		$this->load->view('cortes/cortesView', $data);
	}



	public function cargarFrm()
	{
		$this->load->view('cortes/frmCortes');
	}

	public function agregarCorte()
	{
		$id_corte = $_POST['id_corte'];
		$hora = $_POST['hora_corte'];
		$dia_adicional = $_POST['dia_adicional'];

		$data = [
			'hora_corte' => $hora,
			'id_pais' => $_SESSION['pais'], //remplazarlo por la variable de session
			'dia_adicional' => $dia_adicional
		];

		if (empty($id_corte)) {
			$this->CortesModel->agregarCorte($data);

			echo json_encode([
				"mensaje" => "Su corte ha sido creado",
				"status" => "success"
			]);
		} else {
			$this->CortesModel->actualizarCorte($id_corte, $data);
			echo json_encode([
				"mensaje" => "Su corte ha sido actualizado",
				"status" => "success"
			]);
		}
	}

	public function editarCorte($id_corte)
	{
		$data['info'] = $this->CortesModel->editarCorte($id_corte);
		$this->load->view('cortes/frmCortes', $data);
	}

	public function eliminarCorte($id_corte)
	{
		$this->CortesModel->eliminarCorte($id_corte);
		echo json_encode(['success' => true]);
	}

	public function cambiarEstado($id_corte)
	{
		$this->CortesModel->cambiarEstado($id_corte);
		echo json_encode(['success' => true]);
	}
}
