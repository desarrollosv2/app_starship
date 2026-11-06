<?php
defined('BASEPATH') or exit('No direct script access allowed');
include getcwd() . "/application/libraries/fpdf/fpdf.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
require_once APPPATH . 'libraries/PHPEXCEL/PHPExcel.php';
require_once APPPATH . 'libraries/PHPEXCEL/PHPExcel/IOFactory.php';


class WhController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->model('Conf_model');
		$this->load->model('WhModel');
		$this->load->model('WarehouseModel');
		$this->load->model('PreclasificacionModel');
		$this->load->model('CortesModel');
		$this->load->view("verifica");

		$this->load->library('Token');


		session_start();
	}

	public function index() {}

	public function archivo_facturacion()
	{

		$this->datos['lista_poliza'] = $this->WhModel->listado_polizas();
		$this->load->view("warehouse/vista_facturacion", $this->data);
		$this->load->view("warehouse/lista_facturacion", $this->datos);

		// $this->datos['lista'] = $this->WhModel->listado_polizas();
		//   $this->load->view( "warehouse/vista_facturacion", $this->datos );

	}

	public function manifiesto($id, $master, $correla)
	{
		ini_set('memory_limit', '256M');

		$this->datos['encabezado'] = $this->WhModel->encabezado_manifiesto($id);
		$fechaInfo = $this->procesarFechaManifiesto($this->datos['encabezado']->fecha);
		$this->datos['fecha_formateada'] = $fechaInfo['fecha_formateada'];
		$this->datos['dias_transcurridos'] = $fechaInfo['dias_transcurridos'];


		$_SESSION['tnumber'] = $correla;
		$this->datos['navtext']   = "Manifiestos";
		$this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);

		$this->datos['form']     = "warehouse/contenido";
		$this->datos['partidas'] = $this->Conf_model->partidas();
		//$this->datos['lista'] = $this->WhModel->lista_warehouse($id, $master, $correla);

		$preclasificacion = $this->WhModel->datos_preclasificacion($id);

		$empresas = [112, 113, 114];
		$tm = 0;

		if (in_array($preclasificacion->client_id, $empresas)) {

			$tm = 1;
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$id",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);

			$result = json_decode($response);

			if (isset($result->data->details)) {
				$this->datos['lista'] = $result->data->details;
				$this->datos['client_id'] = $preclasificacion->client_id;
			} else {
				$this->datos['lista'] = [];
			}

			
		} else {
			$this->datos['lista'] = $this->WhModel->lista_warehouse($id, $master, $correla);
		}

		$this->datos["tm"] = $tm;

		$this->datos['lst_referencia'] = $this->WhModel->reference_list($id); //obtiene las referencias asociadas la manifiesto
		//var_dump($this->datos['lst_referencia']);
		$this->load->view("warehouse/lista", $this->datos);
	}


	public function manifiesto_confirma($id, $master)
	{

		$this->datos['navtext']   = "Manifiestos";
		//   $this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);

		$this->datos['form']     = "seguimiento/contenido";
		//  $this->datos['partidas'] = $this->Conf_model->partidas();
		$this->datos['lista'] = $this->WhModel->lista_warehouse_confirma($id, $master);
		//  $this->datos['lst_referencia'] = $this->WhModel->reference_list($id); //obtiene las referencias asociadas la manifiesto
		$this->load->view("warehouse/lista_confirma", $this->datos);
	}

	function review_letters($value)
	{
		return str_replace(
			[
				'á',
				'é',
				'í',
				'ó',
				'ú',
				'Á',
				'É',
				'Í',
				'Ó',
				'Ú',
				'ñ',
				'Ñ'
			],
			[
				'A',
				'E',
				'I',
				'O',
				'U',
				'A',
				'E',
				'I',
				'O',
				'U',
				'N',
				'N'
			],
			$value
		);
	}

	function calcularValorFlete($pesokm)
	{
		if ($pesokm <= 0) {
			return 0;
		}

		$precioPorKg = 1.75;
		$tramos = ceil($pesokm);

		return $tramos * $precioPorKg;
	}





	public function exportar_manifiesto_x_lote_2($id, $master)
	{
		ini_set("display_errors", 1);

		$this->datos['id_master']   = $id;
		$this->datos['name_master'] = $master;

		$lista = $this->WhModel->manifiesto_export($id, $master);

		$this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);

		$preclasificacion = $this->WhModel->datos_preclasificacion($id);

		$empresas = array(112, 113, 114);

		$flag_temu = 0;

		$temu = array();

		if (in_array($preclasificacion->client_id, $empresas)) {
			$flag_temu = 1;

			$curl = curl_init();

			curl_setopt_array($curl, array(

				CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/" . $id,

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_ENCODING => '',


				CURLOPT_MAXREDIRS => 10,

				CURLOPT_TIMEOUT => 0,

				CURLOPT_FOLLOWLOCATION => true,

				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

				CURLOPT_CUSTOMREQUEST => 'GET',

				CURLOPT_HTTPHEADER => array(

					'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'

				)

			));

			$response = curl_exec($curl);

			curl_close($curl);

			$result = json_decode($response);

			if (isset($result->data->details)) {
				foreach ($result->data->details as $item) {
					$temu[$item->package_number] = $item;
				}
			}
		}

		$tmpDir = sys_get_temp_dir() . '/excel_' . uniqid();

		if (!is_dir($tmpDir)) {
			mkdir($tmpDir, 0777, true);
		}

		$registrosPorArchivo = 500;

		$contador = 0;

		$archivo = 1;

		$objPHPExcel = null;

		$sheet = null;

		$fila = 2;

		$encabezados = array(

			'TRACKING',

			'EMBARCADOR',

			'CLIENTE',

			'DIRECCION',

			'DOCUMENTO',

			'PRODUCTOS',

			'PESO',

			'CANTIDAD',

			'FLETE',

			'VALOR',

			'PARTIDA',

			'DAI',

			'OTRO',

			'CAMPO14',

			'CAMPO15'

		);

		foreach ($lista as $row) {

			if ($contador % $registrosPorArchivo == 0) {

				if ($objPHPExcel != null) {

					$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

					$writer->save($tmpDir . "/archivo_" . $archivo . ".xlsx");

					$objPHPExcel->disconnectWorksheets();

					unset($objPHPExcel);

					gc_collect_cycles();

					$archivo++;
				}

				$objPHPExcel = new PHPExcel();

				$sheet = $objPHPExcel->setActiveSheetIndex(0);

				$col = 0;

				foreach ($encabezados as $titulo) {

					$sheet->setCellValueByColumnAndRow($col, 1, $titulo);

					$col++;
				}

				$fila = 2;
			}

			$nombre_cliente = $row->consignee;

			$direccion_cliente = $row->consignee_address1;

			if ($flag_temu == 1) {

				if (isset($temu[$row->package_number])) {

					$nombre_cliente = $temu[$row->package_number]->consignee;

					$direccion_cliente = $temu[$row->package_number]->consignee_address1;
				}
			}

			$nombre_cliente = $this->review_letters($nombre_cliente);

			$pesokm = (float)$row->gweight;

			$valorFlete = $this->calcularValorFlete($pesokm);

			$embarcador = '';

			$tm = 0;

			if (in_array($row->id_empresa, array(112, 113, 114))) {

				$embarcador = 'TEMU';

				$tm = 1;
			}

			if (in_array($row->id_empresa, array(109, 108, 100, 101, 102))) {

				$embarcador = 'ALIEXPRESS';
			}

			

			$direccion_cliente = $this->review_letters($direccion_cliente);

			$direccion_cliente = iconv('UTF-8', 'ASCII//TRANSLIT', $direccion_cliente);

			$direccion_cliente = preg_replace('/[^A-Za-z0-9\-\s]/', '', $direccion_cliente);

			$direccion_cliente = strtoupper(trim($direccion_cliente));

			if ($tm == 1) {

				$listado_productos = implode(

					',',

					array_unique(

						array_map(

							'trim',

							explode(',', $row->commodity)

						)

					)

				);
			} else {

				$listado_productos = iconv('UTF-8', 'ASCII//TRANSLIT', $row->commodity);

				$listado_productos = preg_replace('/[^A-Za-z0-9\-\s]/', ' ', $listado_productos);

				$listado_productos = implode(

					' ',

					array_unique(

						array_map(

							'trim',

							explode(' ', $listado_productos)

						)

					)

				);

				$listado_productos = strtoupper(trim($listado_productos));
			}

			$listado_productos = $this->review_letters($listado_productos);



			$datos = array(

				($tm == 1) ? $row->package_number : $row->tracking_number,

				$embarcador,

				strtoupper($nombre_cliente),

				$direccion_cliente,

				'',

				strtoupper($listado_productos),

				$row->gweight,

				1,

				number_format($valorFlete, 2, '.', ''),

				$row->value,

				$row->partida,

				$row->porcentaje_dai,

				0,

				'',

				''

			);

			$col = 0;

			foreach ($datos as $valor) {

				if (is_null($valor)) {
					$valor = '';
				}

				if (is_bool($valor)) {
					$valor = $valor ? '1' : '0';
				}

				if (is_array($valor)) {
					$valor = implode(',', $valor);
				}

				$sheet->setCellValueByColumnAndRow(
					$col,
					$fila,
					(string)$valor
				);

				$col++;
			}

			$fila++;

			$contador++;
		}

		// Guardar el último archivo Excel
		if ($objPHPExcel != null) {

			$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

			$writer->save($tmpDir . "/archivo_" . $archivo . ".xlsx");

			$objPHPExcel->disconnectWorksheets();

			unset($objPHPExcel);

			gc_collect_cycles();
		}

		// Crear ZIP
		$zip = new ZipArchive();

		$zipFile = sys_get_temp_dir() . '/Exportacion_' . date('YmdHis') . '.zip';

		if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
			die('No fue posible crear el ZIP.');
		}

		foreach (glob($tmpDir . '/*.xlsx') as $excel) {
			$zip->addFile($excel, basename($excel));
		}

		$zip->close();

		if (ob_get_length()) {
			ob_end_clean();
		}

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="Exportacion.zip"');
		header('Content-Length: ' . filesize($zipFile));
		header('Pragma: public');
		header('Cache-Control: must-revalidate');

		readfile($zipFile);

		foreach (glob($tmpDir . '/*.xlsx') as $excel) {
			@unlink($excel);
		}

		@rmdir($tmpDir);

		@unlink($zipFile);

		exit;
	}


	public function exportar_manifiesto_x_lote($id, $master){

		ini_set("display_errors",1);
		$this->datos['id_master'] = $id;
		$this->datos['name_master'] = $master;

		$lista = $this->WhModel->manifiesto_export($id, $master);

		$this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);


		$preclasificacion = $this->WhModel->datos_preclasificacion($id);

		$empresas = [112, 113, 114];
		$flag_temu = 0;

		if (in_array($preclasificacion->client_id, $empresas)) {
			$flag_temu = 1;
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$id",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
				)
			));

			$response = curl_exec($curl);
			curl_close($curl);

			$result = json_decode($response);

			if (isset($result->data->details)) {
				$dlist_manifiestos  = $result->data->details;
				$decrypted = $dlist_manifiestos;
				$flag_temu = 1;
				//$this->datos['client_id'] = $preclasificacion->client_id;
			} else {
			}

			$this->load->library('zip');

			$tmpDir = sys_get_temp_dir() . '/excel_' . uniqid();

			if (!is_dir($tmpDir)) {
				mkdir($tmpDir, 0777, true);
			}

			$registrosPorArchivo = 500;
			$contador = 0;
			$archivo = 1;

			$spreadsheet = null;
			$sheet = null;
			$fila = 2;

			foreach ($lista as $row) {

				//Crear nuevo archivo cada 500 registros
				if ($contador % $registrosPorArchivo == 0) {

					if ($spreadsheet != null) {

						$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
						$writer->save($tmpDir . "/archivo_" . $archivo . ".xlsx");

						$archivo++;
					}

					$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
					$sheet = $spreadsheet->getActiveSheet();

					$encabezados = [
						'TRACKING',
						'EMBARCADOR',
						'CLIENTE',
						'DIRECCION',
						'DOCUMENTO',
						'PRODUCTOS',
						'PESO',
						'CANTIDAD',
						'FLETE',
						'VALOR',
						'PARTIDA',
						'DAI',
						'OTRO',
						'CAMPO14',
						'CAMPO15'
					];

					$col = 'A';

					foreach ($encabezados as $titulo) {
						$sheet->setCellValue($col . '1', $titulo);
						$col++;
					}

					$fila = 2;
				}

				//=========================
				// TU MISMA LÓGICA
				//=========================

				$nombre_cliente = $row->consignee;
				$direccion_cliente = $row->consignee_address1;

				if ($flag_temu == 1) {

					foreach ($decrypted as $item) {

						if ($item->package_number == $row->package_number) {

							$nombre_cliente = $item->consignee;
							$direccion_cliente = $item->consignee_address1;
							break;
						}
					}
				}

				$nombre_cliente = review_letters($nombre_cliente);

				$pesokm = (float)$row->gweight;
				$valorFlete = calcularValorFlete($pesokm);

				$embarcador = '';
				$tm = 0;

				if (in_array($row->id_empresa, [112, 113, 114])) {
					$embarcador = 'TEMU';
					$tm = 1;
				}

				if (in_array($row->id_empresa, [109, 108, 100, 101, 102])) {
					$embarcador = 'ALIEXPRESS';
				}

				$direccion_cliente = review_letters($direccion_cliente);
				$direccion_cliente = iconv('UTF-8', 'ASCII//TRANSLIT', $direccion_cliente);
				$direccion_cliente = preg_replace('/[^A-Za-z0-9\-\s]/', '', $direccion_cliente);
				$direccion_cliente = strtoupper(trim($direccion_cliente));

				if ($tm == 1) {

					$listado_productos = implode(
						',',
						array_unique(
							array_map('trim', explode(',', $row->commodity))
						)
					);
				} else {

					$listado_productos = iconv('UTF-8', 'ASCII//TRANSLIT', $row->commodity);

					$listado_productos = preg_replace('/[^A-Za-z0-9\-\s]/', ' ', $listado_productos);

					$listado_productos = implode(
						' ',
						array_unique(
							array_map('trim', explode(' ', $listado_productos))
						)
					);

					$listado_productos = strtoupper(trim($listado_productos));
				}

				$listado_productos = review_letters($listado_productos);

				//=========================
				// ESCRIBIR FILA
				//=========================

				$sheet->fromArray([
					($tm == 1) ? $row->package_number : $row->tracking_number,
					$embarcador,
					strtoupper($nombre_cliente),
					$direccion_cliente,
					'',
					strtoupper($listado_productos),
					$row->gweight,
					1,
					number_format($valorFlete, 2, '.', ''),
					$row->value,
					$row->partida,
					$row->porcentaje_dai,
					0,
					'',
					''
				], null, 'A' . $fila);

				$fila++;
				$contador++;
			}

			//Guardar último archivo

			if ($spreadsheet != null) {

				$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
				$writer->save($tmpDir . "/archivo_" . $archivo . ".xlsx");
			}

			//=========================
			// GENERAR ZIP
			//=========================

			$zip = new ZipArchive();

			$zipFile = sys_get_temp_dir() . '/Exportacion_' . date('YmdHis') . '.zip';

			$zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

			foreach (glob($tmpDir . '/*.xlsx') as $excel) {

				$zip->addFile($excel, basename($excel));
			}

			$zip->close();

			//Descargar

			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="Exportacion.zip"');
			header('Content-Length: ' . filesize($zipFile));

			readfile($zipFile);

			//Eliminar temporales

			foreach (glob($tmpDir . '/*.xlsx') as $excel) {
				unlink($excel);
			}

			rmdir($tmpDir);
			unlink($zipFile);

			exit;

		}
	}

	public function limpiarTexto($texto)
	{
		// Si detecta caracteres típicos de mala codificación
		if (preg_match('/Ã|Â|â/', $texto)) {
			$convertido = @utf8_decode($texto);

			// Solo reemplaza si la conversión no produjo caracteres inválidos
			if (strpos($convertido, '�') === false) {
				return $convertido;
			}
		}

		return $texto;
	}

	public function prueba_misa(){
		if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet'))
		{
			echo "PhpSpreadsheet instalado";
		}
	}

	public function asignar_selectivo()
	{
		$selectivo = $this->input->post('selectSelectivo');
		$idmanifiesto = $this->input->post('txtManifiesto');
		$referencia = $this->input->post('txtReferencia');


		

		if (!$selectivo) {
			echo json_encode([
				'status' => false,
				'message' => 'Debe seleccionar un selectivo.'
			]);
			return;
		}

	
		$resultado = $this->WhModel->asignar_selectivo($selectivo,$referencia,$idmanifiesto);

		if ($resultado) {

			echo json_encode([
				'status' => true,
				'message' => 'Selectivo asignado correctamente.'
			]);
		} else {

			echo json_encode([
				'status' => false,
				'message' => 'No se pudo asignar el selectivo.'
			]);
		}
	}


	public function descargar_tiempos($id_manifiesto)
	{

		require_once APPPATH . 'libraries/SimpleXLSXGen.php';

		// Obtener información
		$datos = $this->WhModel->obtener_tiempos_manifiesto($id_manifiesto);

		if (empty($datos)) {
			show_error('No se encontraron registros para el manifiesto.');
			return;
		}

		// Encabezados del Excel
		$excel = [];

		$excel[] = [
			'Master',
			'Referencia',
			'Warehouse',
			'Fecha recibido',
			'Fecha proceso',
			'Fecha demorado',
			'Fecha liberado',
			'Fecha entregado',
			'Recibido → Proceso',
			'Proceso → Demorado',
			'Demorado → Liberado',
			'Proceso → Liberado',
			'Liberado → Entregado',
			'Tiempo total'
		];

		// Datos
		foreach ($datos as $fila) {

			$excel[] = [
				$fila['manifiesto'],
				$fila['referencia'],
				$fila['warehouse'],

				$fila['fecha_recibido'],
				$fila['fecha_proceso'],
				$fila['fecha_demorado'],
				$fila['fecha_liberado'],
				$fila['fecha_entregado'],

				$fila['tiempo_recibido_proceso'],
				$fila['tiempo_proceso_demorado'],
				$fila['tiempo_demorado_liberado'],
				$fila['tiempo_proceso_liberado'],
				$fila['tiempo_liberado_entregado'],

				$fila['tiempo_total']
			];
		}

		// Nombre del archivo
		$nombre_archivo = 'tiempos_manifiesto_' . $id_manifiesto . '.xlsx';

		// Generar y descargar
		$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($excel);

		$xlsx->setDefaultFont('Arial');
		$xlsx->setDefaultFontSize(10);

		$xlsx->downloadAs($nombre_archivo);
	}

	public function exportar_manifiesto_x_lote_3($id, $master)
	{


		

		ini_set("display_errors", 1);

		

		// 1. INCLUIR LA LIBRERÍA MANUALMENTE
		// Asegúrate de subir el archivo SimpleXLSXGen.php a tu carpeta application/third_party/
		require_once APPPATH . 'libraries/SimpleXLSXGen.php';

		

		$this->datos['id_master'] = $id;
		$this->datos['name_master'] = $master;

		

		$lista = $this->WhModel->manifiesto_export($id, $master);


		
		$this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);

		
		$preclasificacion = $this->WhModel->datos_preclasificacion($id);

		


		/*NEW CODE BY BOX*/

		$boxes = $this->WhModel->qtys_by_box($id);

		
		

		$limite = 500;
		$acumulado = 0;
		$count_boxes = 1;

		$cajasCorte = [];
		$totalPaquetes = 0;

		foreach ($boxes as $caja) {

			$cantidad = (int)$caja->total;

			if (($acumulado + $cantidad) > $limite) {


				$caja->acumulado = $acumulado;

				// Esta caja será la primera del siguiente lote
				$cajasCorte[] = $caja;

				// Reiniciar el acumulado comenzando con esta caja
				$acumulado = $cantidad;
			} else {

				$acumulado += $cantidad;
			}

			$totalPaquetes += $cantidad;

			if(count($boxes) == $count_boxes){
				//echo "Ultima caja ".$acumulado;
				//echo "Total paquetes".$totalPaquetes;
			}
			$count_boxes++;
		}

		
		$empresas = [112, 113, 114];
		$flag_temu = 0;

		if (in_array($preclasificacion->client_id, $empresas)) {
			$flag_temu = 1;
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$id",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
				)
			));

			$response = curl_exec($curl);
			curl_close($curl);

			$result = json_decode($response);

			if (isset($result->data->details)) {
				$dlist_manifiestos = $result->data->details;
				$decrypted = $dlist_manifiestos;
				$flag_temu = 1;
			} else {
				$decrypted = [];
			}

			// Crear directorio temporal para almacenar los archivos excel
			$tmpDir = sys_get_temp_dir() . '/excel_' . uniqid();
			if (!is_dir($tmpDir)) {
				mkdir($tmpDir, 0777, true);
			}

			$registrosPorArchivo = 500;
			$contador = 0;
			$archivo = 1;

			// Estructura de encabezados obligatoria para cada archivo
			$encabezados = [
				'Guia',
				'Embarcador / Proveedor',
				'Consignatario',
				'Direccion Consignatario',
				'NIT Consignatario',
				'Descripcion',
				'Peso',
				'Piezas',
				'Flete',
				'Valor Mercancia',
				'Inciso Arancelario',
				'% Arancelario',
				'Gastos',
				'Ticket / Documento',
				'Dim'
			];

			// Aquí acumularemos las filas del lote actual
			$datosArchivo = [$encabezados];


			foreach ($lista as $row) {

				// PROCESAMIENTO DE TU LOGICA DE NEGOCIO
				$nombre_cliente = $row->consignee;
				$direccion_cliente = $row->consignee_address1;

				if ($flag_temu == 1) {
					foreach ($decrypted as $item) {
						if ($item->package_number == $row->package_number) {
							$nombre_cliente = $item->consignee;
							$direccion_cliente = $item->consignee_address1;
							break;
						}
					}
				}

				$nombre_cliente = $this->review_letters($this->limpiarTexto($nombre_cliente));
				$pesokm = (float)$row->gweight;

				//echo $pesokm;
				$valorFlete = $this->calcularValorFlete($pesokm);


				//echo $valorFlete;
				//die();

				$embarcador = '';
				$tm = 0;

				if (in_array($row->id_empresa, [112, 113, 114])) {
					$embarcador = 'TEMU';
					$tm = 1;
				}

				if (in_array($row->id_empresa, [109, 108, 100, 101, 102])) {
					$embarcador = 'ALIEXPRESS';
				}

				$direccion_cliente = $this->review_letters($direccion_cliente);
				$direccion_cliente = iconv('UTF-8', 'ASCII//TRANSLIT', $direccion_cliente);
				$direccion_cliente = preg_replace('/[^A-Za-z0-9\-\s]/', '', $direccion_cliente);
				$direccion_cliente = strtoupper(trim($direccion_cliente));

				if ($tm == 1) {
					$listado_productos = implode(
						',',
						array_unique(array_map('trim', explode(',', $row->commodity)))
					);
				} else {
					$listado_productos = iconv('UTF-8', 'ASCII//TRANSLIT', $row->commodity);
					$listado_productos = preg_replace('/[^A-Za-z0-9\-\s]/', ' ', $listado_productos);
					$listado_productos = implode(
						' ',
						array_unique(array_map('trim', explode(' ', $listado_productos)))
					);
					$listado_productos = strtoupper(trim($listado_productos));
				}

				$listado_productos = $this->review_letters($listado_productos);


				$listado_productos = str_replace(',', ', ', $listado_productos);


				if (!isset($cajasCorte[$archivo - 1]) || $cajasCorte[$archivo - 1]->bag_number != $row->bag_number) {
					// AGREGAR FILA AL ARRAY TEMPORAL
					$datosArchivo[] = [
						($tm == 1) ? $row->package_number : $row->tracking_number,
						$embarcador,
						strtoupper($nombre_cliente),
						$direccion_cliente,
						'',
						strtoupper(substr($listado_productos,0,70)),
						($tm == 1) ? $row->peso_prorateado_gt : $row->gweight,
						1,
						($tm == 1) ? $row->flete_gt : number_format($valorFlete, 2, '.', ''),
						($tm == 1) ? $row->fob_gt : $row->value,
						$row->partida,
						$row->porcentaje_dai,
						0,
						'',
						''
					];
				}

				$contador++;

				// Si llegamos a los 500 registros, generamos el archivo físico y reiniciamos el array
				//if ($contador % $registrosPorArchivo == 0) {

				if (
					count($lista) == $contador ||
					(isset($cajasCorte[$archivo - 1]) && $cajasCorte[$archivo - 1]->bag_number == $row->bag_number)
				) {



					$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($datosArchivo);
					$xlsx->saveAs($tmpDir . "/archivo_" . $archivo . ".xlsx");

					$archivo++;
					// Reiniciamos el array solo con los encabezados para el siguiente lote
					$datosArchivo = [$encabezados];


					if(count($lista) != $contador){
						// AGREGAR FILA NUEVA CAJA
						$datosArchivo[] = [
							($tm == 1) ? $row->package_number : $row->tracking_number,
							$embarcador,
							strtoupper($nombre_cliente),
							$direccion_cliente,
							'',
							strtoupper(substr($listado_productos,0,70)),
							($tm == 1) ? $row->peso_prorateado_gt : $row->gweight,
							1,
							($tm == 1) ? $row->flete_gt : number_format($valorFlete, 2, '.', ''),
							($tm == 1) ? $row->fob_gt : $row->value,
							$row->partida,
							$row->porcentaje_dai,
							0,
							'',
							''
						];
					}


				}
			}

			// GUARDAR EL ÚLTIMO ARCHIVO (Si quedaron registros remanentes)
			if (count($datosArchivo) > 1) {
				$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($datosArchivo);
				$xlsx->saveAs($tmpDir . "/archivo_" . $archivo . ".xlsx");
			}

			//=========================
			// GENERAR ZIP Y DESCARGA (Se mantiene igual)
			//=========================
			$zip = new ZipArchive();
			$zipFile = sys_get_temp_dir() . '/Exportacion_' . date('YmdHis') . '.zip';

			if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
				foreach (glob($tmpDir . '/*.xlsx') as $excel) {
					$zip->addFile($excel, basename($excel));
				}
				$zip->close();
			}

			// Descargar el archivo ZIP resultante
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="Exportacion.zip"');
			header('Content-Length: ' . filesize($zipFile));

			readfile($zipFile);

			// Limpieza de archivos temporales
			foreach (glob($tmpDir . '/*.xlsx') as $excel) {
				unlink($excel);
			}
			rmdir($tmpDir);
			unlink($zipFile);

			exit;
		}else{
			$lista = $this->WhModel->lista_warehouse_individual($id);

			$archivo = 1;

			$encabezados = [
				'Guia',
				'Embarcador / Proveedor',
				'Consignatario',
				'Direccion Consignatario',
				'NIT Consignatario',
				'Descripcion',
				'Peso',
				'Piezas',
				'Flete',
				'Valor Mercancia',
				'Inciso Arancelario',
				'% Arancelario',
				'Gastos',
				'Ticket / Documento',
				'Dim'
			];

			$tmpDir = sys_get_temp_dir() . '/excel_' . uniqid();
			if (!is_dir($tmpDir)) {
				mkdir($tmpDir, 0777, true);
			}

			$y = 0;
			$loteActivo = 0;
			foreach ($lista as $row) {

				//echo $row->tracking_number.' '.$y;

				if ($y == 500) { //Cambio de lote

					//echo "Aqui cerramos lote";
					//var_dump($datosArchivo);
					//die();
					//$numeroLote++;
					$loteActivo = 1;
					$y = 0;
				} else {
					$loteActivo = 0;
				}

				$y++;

				

				// PROCESAMIENTO DE TU LOGICA DE NEGOCIO
				$nombre_cliente = $row->consignee;
				$direccion_cliente = $row->consignee_address1;

				if ($flag_temu == 1) {
					foreach ($lista as $item) {
						if ($item->package_number == $row->package_number) {
							$nombre_cliente = $item->consignee;
							$direccion_cliente = $item->consignee_address1;
							break;
						}
					}
				}

				$nombre_cliente = $this->review_letters($this->limpiarTexto($nombre_cliente));
				$pesokm = (float)$row->gweight;

				//echo $pesokm;
				$valorFlete = $this->calcularValorFlete($pesokm);


				//echo $valorFlete;
				//die();

				$embarcador = '';
				$tm = 0;

				if (in_array($row->id_empresa, [112, 113, 114])) {
					$embarcador = 'TEMU';
					$tm = 1;
				}

				if (in_array($row->id_empresa, [109, 108, 100, 101, 102])) {
					$embarcador = 'ALIEXPRESS';
				}

				$direccion_cliente = $this->review_letters($direccion_cliente);
				$direccion_cliente = iconv('UTF-8', 'ASCII//TRANSLIT', $direccion_cliente);
				$direccion_cliente = preg_replace('/[^A-Za-z0-9\-\s]/', '', $direccion_cliente);
				$direccion_cliente = strtoupper(trim($direccion_cliente));

				if ($tm == 1) {
					$listado_productos = implode(
						',',
						array_unique(array_map('trim', explode(',', $row->commodity)))
					);
				} else {
					$listado_productos = iconv('UTF-8', 'ASCII//TRANSLIT', $row->commodity);
					$listado_productos = preg_replace('/[^A-Za-z0-9\-\s]/', ' ', $listado_productos);
					$listado_productos = implode(
						' ',
						array_unique(array_map('trim', explode(' ', $listado_productos)))
					);
					$listado_productos = strtoupper(trim($listado_productos));
				}

				$listado_productos = $this->review_letters($listado_productos);
				$listado_productos = str_replace(',', ', ', $listado_productos);


				if ($loteActivo == 0) {
					// AGREGAR FILA AL ARRAY TEMPORAL
					$datosArchivo[] = [
						($tm == 1) ? $row->package_number : $row->tracking_number,
						$embarcador,
						strtoupper($nombre_cliente),
						$direccion_cliente,
						'',
						strtoupper(substr($listado_productos,0,70)),
						($tm == 1) ? $row->peso_prorateado_gt : $row->gweight,
						1,
						($tm == 1) ? $row->flete_gt : number_format($valorFlete, 2, '.', ''),
						($tm == 1) ? $row->fob_gt : $row->value,
						$row->partida,
						$row->porcentaje_dai,
						0,
						'',
						''
					];
				}

				//echo count($datosArchivo)." ".$loteActivo;
				//die();
				//$contador++;

				// Si llegamos a los 500 registros, generamos el archivo físico y reiniciamos el array
				//if ($contador % $registrosPorArchivo == 0) {


				if (
					$loteActivo == 1 
				) {



					$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($datosArchivo);
					$xlsx->saveAs($tmpDir . "/archivo_" . $archivo . ".xlsx");

					$archivo++;
					// Reiniciamos el array solo con los encabezados para el siguiente lote
					$datosArchivo = [$encabezados];


					
						// AGREGAR FILA NUEVA CAJA
						$datosArchivo[] = [
							($tm == 1) ? $row->package_number : $row->tracking_number,
							$embarcador,
							strtoupper($nombre_cliente),
							$direccion_cliente,
							'',
							strtoupper(substr($listado_productos,0,70)),
							($tm == 1) ? $row->peso_prorateado_gt : $row->gweight,
							1,
							($tm == 1) ? $row->flete_gt : number_format($valorFlete, 2, '.', ''),
							($tm == 1) ? $row->fob_gt : $row->value,
							$row->partida,
							$row->porcentaje_dai,
							0,
							'',
							''
						];
					
				}

				/*if ($y == 3) {

					//var_dump($datosArchivo);
					//die();

				}*/


			}

			// GUARDAR EL ÚLTIMO ARCHIVO (Si quedaron registros remanentes)
			if (count($datosArchivo) > 1) {
				$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($datosArchivo);
				$xlsx->saveAs($tmpDir . "/archivo_" . $archivo . ".xlsx");
			}

			//=========================
			// GENERAR ZIP Y DESCARGA (Se mantiene igual)
			//=========================
			$zip = new ZipArchive();
			$zipFile = sys_get_temp_dir() . '/Exportacion_' . date('YmdHis') . '.zip';

			if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
				foreach (glob($tmpDir . '/*.xlsx') as $excel) {
					$zip->addFile($excel, basename($excel));
				}
				$zip->close();
			}

			// Descargar el archivo ZIP resultante
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="Exportacion.zip"');
			header('Content-Length: ' . filesize($zipFile));

			readfile($zipFile);

			// Limpieza de archivos temporales
			foreach (glob($tmpDir . '/*.xlsx') as $excel) {
				unlink($excel);
			}
			rmdir($tmpDir);
			unlink($zipFile);

			exit;

			

			
		}
	}

	public function manifiesto_export($id, $master)
	{


		//  $_SESSION['pais'] = 3;
		$this->datos['navtext']   = "Manifiestos";

		$this->datos['lista'] = $this->WhModel->manifiesto_export($id, $master);





		// $this->load->view("warehouse/lista_exportar", $this->datos);
		if ($_SESSION['pais'] == 1) {
			$this->load->view("warehouse/lista_exportar", $this->datos);
		} else if ($_SESSION['pais'] == 2) {

			$this->datos['id_master'] = $id;
			$this->datos['name_master'] = $master;

			$this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);


			$preclasificacion = $this->WhModel->datos_preclasificacion($id);

			$empresas = [112, 113, 114];
			$flag_temu = 0;

			if (in_array($preclasificacion->client_id, $empresas)) {
				$flag_temu = 1;
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$id",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
					)
				));

				$response = curl_exec($curl);
				curl_close($curl);

				$result = json_decode($response);

				if (isset($result->data->details)) {
					$dlist_manifiestos  = $result->data->details;
					$this->datos['decrypted'] = $dlist_manifiestos;
					$this->datos['flag_temu'] = 1;
					//$this->datos['client_id'] = $preclasificacion->client_id;
				} else {

				}
			}

			$this->load->view("warehouse/lista_exportgt", $this->datos);

		} else if ($_SESSION['pais'] == 3) {





			$this->load->view("warehouse/lista_exportarHN", $this->datos);
		}
	}


	public function lista_pendientes_confirmar()
	{

		$this->datos['lista'] = $this->WhModel->lista_pendientes_confirmar();
		$this->load->view("seguimiento/lista", $this->datos);
	}

	public function lista_confirmados()
	{
		// $this->datos['departamento'] = $this->Conf_model->get_departamentos();
		//  var_dump( $this->datos['departamento']);
		$this->datos['lista'] = $this->WhModel->lista_confirmados();
		$this->load->view("seguimiento/lista", $this->datos);
	}

	public function lista_referencia($id, $ref)
	{

		$this->datos['navtext']   = "Manifiestos";
		$this->datos['catalogo_ref'] = $this->Conf_model->get_referencia($id);
		$this->datos['form']     = "warehouse/contenido";
		$this->datos['lista'] = $this->WhModel->lista_referencia($id, $ref);

		$this->load->view("warehouse/lista", $this->datos);
	}

	public function seleccionar_item($id, $opc)
	{
		if ($opc == 1) {
			$flag = 1;
		} else {
			$flag = 0;
		}
		$this->WhModel->seleccionar_item($id, $flag);
	}

	public function lista_awb()
	{

		$this->datos['navtext']   = "AWB";
		$this->datos['form']     = "awb/contenido";
		//  $this->datos['lista'] = $this->WhModel->lista_awb();


		$this->datos["empresas"] = $this->Catalogos_Model->buscar_empresa();
		$this->datos["pais"] = $this->Conf_model->paises_ec();
		$this->datos["proveedor_lm"] = $this->Conf_model->proveedores_lm();


		$this->load->view("awb/lista", $this->datos);
	}

	public function lista_awb_first($desde, $hasta)
	{

		//$_SESSION["start_date"] = $desde;
		//$_SESSION["end_date"] = $hasta;

		

		if((!isset($desde) || $desde == "" || $desde == "undefined") || (!isset($hasta) || $hasta == "" || $hasta == "undefined")){
			// 1. Obtener la fecha del día de hoy (Hasta)
			$hasta = date('Y-m-d', strtotime('+1 day'));

			// 2. Restar exactamente 2 meses a la fecha de hoy (Desde)
			$desde = date('Y-m-d', strtotime('-3 months')); // Ejemplo: 2026-04-09
		}

		// 3. Ejecutar tu consulta con los parámetros generados
		$this->datos['lista'] = $this->WhModel->lista_awb($desde, $hasta);
		$this->load->view("awb/cuerpo_ma", $this->datos);
	}

	public function lista_awb_confirma()
	{


		$this->datos['navtext']   = "AWB";
		$this->datos['form']     = "segumiento/contenido";
		//  $this->datos['lista'] = $this->WhModel->lista_awb();
		$this->load->view("seguimiento/lista", $this->datos);
	}
	public function lista_awb_first_confirma()
	{

		$this->datos['lista'] = $this->WhModel->lista_awb_confirma();

		//  Var_dump($this->datos['lista']);

		$this->load->view("seguimiento/cuerpo_ma", $this->datos);
	}

	public function consulta_awb($inicio, $fin, $opcion, $buscar)
	{
		// $this->datos['navtext']   = "AWB";
		//$this->datos['form']     = "awb/contenido";
		$this->datos['lista'] = $this->WhModel->consulta_awb($inicio, $fin, $opcion, $buscar);
		$this->load->view("awb/cuerpo_ma", $this->datos);
	}


	public function manifiesto_listado($mawb, $opc)
	{
		if ($opc == 1) {
			$campo = "tracking_number";
		} else {
			$campo = "mawb";
		}
		$this->datos['navtext']   = "Manifiestos";
		$this->datos['form']     = "warehouse/contenido";
		$this->datos['lista'] = $this->WhModel->listado_warehouse($mawb, $campo);
		// var_dump( $this->datos['lista'] );

		$this->load->view("warehouse/lista", $this->datos);
	}

	public function  guardar_referencia()
	{
		$id = $_POST['id'];
		$data = array(
			'id_manifiesto' => trim($_POST["id-manifiesto"]),
			'referencia' => trim($_POST['referencia']),
			'paquetes' => $_POST['paquetes'],
			'sacos' => $_POST['sacos'],

		);
		$this->WhModel->guardar_referencia($data);
	}

	public function  asignar_guia()
	{
		$ref = trim($_POST['catalogo_ref']);
		$master = $_POST['manifiesto_id'];
		echo $ref;
		// echo $master;
		$this->WhModel->asignar_guia($master, $ref);
	}




	public function consulta_conversacion($id)
	{
		$this->datos['dconversacion'] = $this->WarehouseModel->consulta_conversacion($id);
		//  var_dump(  $this->datos['guias']);
		$this->load->view("seguimiento/lista_conversacion", $this->datos);
	}

	public function  guardar_comentario()
	{
		$id = $_POST['id-tracking'];
		$cometario = $_POST['comenta'];
		$fecha = date("Y-m-d H:i:s");
		$data = array(
			'fecha' => $fecha,
			'comentarios' => $cometario,
			'id_tracking' => $id,
			'usuario' =>   $_SESSION["nombre"]


		);
		$this->WhModel->guardar_comentario($data);
	}
	public function update_aceptado($id)
	{
		$this->WhModel->update_aceptado($id);
	}

	public function enviar_msg()
	{
		// 50373248877
		//50378271044;
		$token = "Bearer EAALCF4vf9RABAEpUnUsiWZBUGvOUPEw1qRcRh5h1j6HY0RZCFImIQ1GgK1WV1yDtCrRfhf6XimkCJq4GVR3xy9GkI9c9ZCxHvfMvzubPV4CiF8eqj1W6UhfUosMhCTXhX0NHjQHLZCcwEicULAxN4gG0MZAyZB8api1HudYnxljZCxt9E8lFXNMIyNxiNz0Ka7tpYKGx45mA3XqZBM5tytiT";

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://graph.facebook.com/v13.0/100971466038694/messages',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{
            "messaging_product": "whatsapp",
            "to": "50378271044",
            "type": "template",
            "template": {
                "name": "hello_world",
                "language": {
                    "code": "en_US"
                }
            }
        }',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization:' . $token
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
	}

	public function ma()
	{
		$fecha = date('Y-m-d H:i:s');
		$fecha = "2022-12-05T13:10:55-03:00";
		$header = array(
			'Host: qa.tracking.mailamericas.com',
			'Content-Type: application/json',
			'Cache-Control: no-cache'
		);

		$data = array(
			"grant_type" => "client_credentials",
			"client_id" => 42,
			"client_secret" => "1CFIlE3pXo2QdJgcnlocoRIoXsJ1Dr3veM8pWKj4",
			"scope" => "*"
		);

		$url = "https://tracking.mlasalvador.com/oauth/token";



		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{
               "grant_type": "client_credentials",
                      "client_id" : 42,
                      "client_secret" : "1CFIlE3pXo2QdJgcnlocoRIoXsJ1Dr3veM8pWKj4",
                      "scope": "*"
          }',
			CURLOPT_HTTPHEADER => array(
				'Cache-Control: no-cache',
				'Content-Type: application/json',
				'Host: qa.tracking.mailamericas.com'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//   echo $response;
		$code = json_decode($response);
		$token = $code->access_token;

		//echo $token;

		//exit;
		$header = array(
			'Host: qa.tracking.mailamericas.com',
			'Content-Type: application/json',
			'Cache-Control: no-cache',
			'Authorization: Bearer ' . $token
		);

		$data = array(
			"tracking" => "MLSV000010688RG", // m
			"date" =>  $fecha,
			"description" => "Entregado", // m
			"city" => "CCO", // m

			"received_by" => "Ricardo Herrera", //m
			"relationship" => null, //m
			"delivery_proof" => [
				"https://stargates.site/documentos_ma/638101f13d8b7.png"
			],

			"gcs" => "18.6409321,-91.777005",
			"details" => ""
		);



		$url = "https://tracking.mlasalvador.com/api/v1/providers/events";


		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$resultStr = curl_exec($ch);

		if (curl_errno($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close($ch);
		//  echo  $error_msg;
		echo $resultStr;
	}

	public function guardar_item_multi($id, $cantidad)
	{
		$detalle = $this->WarehouseModel->obtener_guia_id($id);

		$peso = $detalle->gweight / $cantidad;
		$precio = $detalle->value / $cantidad;
		$precio = number_format($precio, 2);


		for ($i = 1; $i <= $cantidad; $i++) {
			$deta = array(
				'id_manifiesto'          => $detalle->id_manifiesto,
				'tracking_number'        => $detalle->tracking_number . "-" . $i,
				'gweight'                => $peso,
				'value'                  => $precio,
				'items'                  => $detalle->items,
				'commodity'              => $detalle->items_description,
				'consignee_account'      => $detalle->buyer_id,
				'consignee'              => $detalle->buyer,
				'buyer_company'          => $detalle->buyer_company,
				'consignee_address1'     => $detalle->buyer_address1,
				'buyer_address1_number'  => $detalle->buyer_address1_number,
				'consignee_address2'     => $detalle->buyer_address2,
				'consignee_address3'     => $detalle->buyer_address3,
				'buyer_district'         => $detalle->buyer_district,
				'consignee_city'         => $detalle->buyer_city,
				'consignee_state'        => $detalle->buyer_state,
				'consignee_country'      => $detalle->buyer_location,
				'consignee_zip'          => $detalle->buyer_zip,
				'consignee_phone'        => $detalle->buyer_phone,
				'consignee_email'        => $detalle->buyer_email,
				'hts'                    => $detalle->hts,
				'pieces'                 => $detalle->pieces,

			);

			$t = $this->WarehouseModel->guardar_item_multi($deta);
			$this->WarehouseModel->update_item_multi($id, $peso, $precio);
			echo $t;
		}
	}

	public function lista_departamentos()
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://app.c807.com/guia.php/api/departamento',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9',
			),
		));

		$json = curl_exec($curl);
		curl_close($curl);
		// echo $json;
		$data = json_decode($json);

		$this->datos['departamento'] = $data;
		$this->load->view("catalogos/departamentos", $this->datos);
	}

	public function lista_municipios($id)
	{
		$code = $id;
		$curl = curl_init();
		$url = 'https://app.c807.com/guia.php/api/municipio?departamento=' . $code;

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization:  91bbb9fac887f6d4723dd19842feb4f50c7dc5c9',
			),
		));

		$json = curl_exec($curl);
		curl_close($curl);

		$data = json_decode($json);

		$this->datos['municipio'] = $data;
		$this->load->view("catalogos/municipios", $this->datos);
	}

	/**
	 * Esta funcion consume api de c807xpress, espcificamente endpoint para crear la guia
	 *
	 * @param string $id, equivalente al número de traacking para crear la guia
	 * @return bool El resultado de la función
	 */
	public function obtener_guia2025oldOK($id)
	{
		//ini_set('display_errors', 1);
		//	ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);
		$this->load->view("verifica");

		$this->WarehouseModel->update_creacion($id);
		$rsl = $this->WarehouseModel->obtener_guia($id);


		$request = $this->WarehouseModel->obtener_token($rsl->id_empresa);


		$token = $request->token;
		//	echo $token;


		if ($_SESSION['pais'] == 1) {
			/** Toma la hora activa para la recolecta */
			$corte = $this->CortesModel->obtener_activo($id);
			$strfecha = $corte->hora_corte;
		}

		if ($_SESSION['pais'] == 2  || $_SESSION['pais'] == 3) {
			$strfecha = date("Y-m-d") . '23:05:05';
		}


		//$strfecha = date("Y-m-d") . '23:05:05';
		//$strfecha = date("Y-m-d") . '17:00:02';
		//$strfecha = date("Y-m-d") . '23:07:01';
		//$strfecha = date("Y-m-d") . '13:04:01';
		// $strfecha = date("Y-m-d") . '20:04:01';


		//inicio para siguiente dia
		// Combinar fecha y hora
		//$fecha = date("Y-m-d") . '08:01:05';

		// Crear objeto DateTime
		//$dt = new DateTime($fecha);

		// Sumar un día
		//$dt->modify('+1 day');

		// Obtener la nueva fecha como string
		//$strfecha = $dt->format('Y-m-d H:i:s');
		//fin
		$fecha_recoleccion = $strfecha; //date("Y-m-d H:i:s"); //"2023-08-21 16:30";

		$telefono = $rsl->consignee_phone;

		$numero_guia = '';
		if ($rsl->tracking_replace == null) {
			$numero_guia = $rsl->tracking_number;
		} else {
			$numero_guia = $rsl->tracking_replace;
		}

		if ($rsl->tipo_servicio == "SER") {
			$montoCCE = 0;
		}
		if ($rsl->tipo_servicio == "CCE") {
			$montoCCE = $rsl->cobro_final;
		}

		if ($_SESSION['pais'] == 1) {

			$datos = array(
				"recolecta_fecha" => $fecha_recoleccion,
				"recolecta_comentario" => "StarShip",
				"tipo_entrega" => $rsl->tipo_entrega,
				"guias" => array(
					array(
						"identificacion" => "100",
						"nombre"         => $rsl->consignee,
						"correo"         => $rsl->consignee_email,
						"telefono"       => $telefono,
						"direccion"      => $rsl->consignee_address1,
						"tipo_servicio"  => $rsl->tipo_servicio,
						"monto_cce"      => $montoCCE,
						"agencia_destino" =>  $rsl->agencia_destino,
						"con_seguro"     => "0",
						"con_liquidacion_documento" => "0",
						"departamento_id"   =>  $_POST['depto'],
						"municipio_id"      =>  $_POST['munic'],
						"detalle" => array(
							array(
								"guia"          => $numero_guia,
								"peso"          => $rsl->gweight,
								"contenido"     => $rsl->commodity,
								"unidad_medida" => "LB",
							)
						)
					)
				)
			);
		}

		if ($_SESSION['pais'] == 2 || $_SESSION['pais'] == 3) {
			

			$datos = array(
				"recolecta_fecha" => $fecha_recoleccion,
				"recolecta_comentario" => "StarShip",
				"tipo_entrega" => $rsl->tipo_entrega,

				"facturacion" => array(
					//"nit" => $rsl->consignee_account,
					"nit" => null,
					"nombre" => $rsl->consignee,
					"correo" => $rsl->consignee_email,
					"direccion"      => $rsl->consignee_address1,
				),
				"guias" => array(
					array(
						"identificacion" => "100",
						"nombre"         => $rsl->consignee,
						"correo"         => $rsl->consignee_email,
						"telefono"       => $telefono,
						"direccion"      => $rsl->consignee_address1,
						"tipo_servicio"  => $rsl->tipo_servicio,
						"monto_cce"      => $rsl->cobro_final,
						"agencia_destino" =>  $rsl->agencia_destino,
						"con_seguro"     => "0",
						"con_liquidacion_documento" => "0",
						"departamento_id"   =>  $_POST['depto'],
						"municipio_id"      =>  $_POST['munic'],
						"detalle" => array(
							array(
								"guia"          => $numero_guia,
								"codigo"        => null,
								"peso"          => $rsl->gweight,
								"contenido"     => $rsl->commodity,
								"unidad_medida" => "LB",
								"cobro" => array(
									array(
										"codigo" => "IVA",
										"monto"  => $rsl->iva
									),
									array(
										"codigo" => "DAI",
										"monto"  => $rsl->dai
									),
									array(
										"codigo" => "FLETE",
										"monto"  => "0.00"
									),
									array(
										"codigo" =>  "DOCS",
										"monto"  => number_format($rsl->manejo + $rsl->total_ivag, 2, '.', '.')
									)

								)
							)

						)
					)
				)
			);
			//echo json_encode($datos);
			//	echo $token;
			//exit;
		}


		$url = "";
		$url = 'https://app.c807.com/guia.php/api/set_registro';

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => false, //TODO: quitar esto en produccion
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $json = json_encode($datos),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: ' . $token,

			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		echo $response;
	}

	public function send_info_gacela($datos, $token)
	{

		$guia = $datos["guia"];
		//echo $guia;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://app.c807.com/guia.php/api/actualizar/' . $guia,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'PUT',
			CURLOPT_POSTFIELDS => '{ 
              "telefono": "' . $datos["telefono"] . '",
              "direccion": "' . $datos["direccion"] . '",
              "correo": "' . $datos["correo"] . '",
              "departamento_id": ' . $datos["departamento_id"] . ',
              "municipio_id": ' . $datos["municipio_id"] . '                
          	}',
			CURLOPT_HTTPHEADER => array(
				'Authorization: ' . $token,
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);


		echo $response;
	}


	public function obtener_guia($id = null)
	{

		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);
		//       $id = 2278526;

		$this->load->view("verifica");

		$this->WarehouseModel->update_creacion($id);

		$rsl = $this->WarehouseModel->obtener_guia($id);




		$master = $rsl->preclasificacion_manifiesto;
		$trackingNumber = $rsl->tracking_number;
		$fecha_arribo_miami = $rsl->fecha_arribo;

		if ($fecha_arribo_miami === null || $fecha_arribo_miami === '') {

			return $this->output
				->set_content_type('application/json')
				->set_status_header(400) // Código 400: Solicitud incorrecta
				->set_output(json_encode(['error' => 'La fecha de arribo es requerida.']));
		}

		$request = $this->WarehouseModel->obtener_token($rsl->id_empresa);

		$request216 = $this->WarehouseModel->getfecha_evento($trackingNumber, 216);
		$request288 = $this->WarehouseModel->getfecha_evento($trackingNumber, 288);


		$token = $request->token;


		if ($_SESSION['pais'] == 1) {
			/** Toma la hora activa para la recolecta */
			$corte = $this->CortesModel->obtener_activo($id);
			$strfecha = $corte->hora_corte;
		}

		if ($_SESSION['pais'] == 2  || $_SESSION['pais'] == 3) {
			$strfecha = date("Y-m-d") . '23:05:05';
		}


		//$strfecha = date("Y-m-d") . '23:05:05';
		//$strfecha = date("Y-m-d") . '17:00:02';
		//$strfecha = date("Y-m-d") . '23:07:01';
		//$strfecha = date("Y-m-d") . '13:04:01';
		// $strfecha = date("Y-m-d") . '20:04:01';


		//inicio para siguiente dia
		// Combinar fecha y hora
		//$fecha = date("Y-m-d") . '08:01:05';

		// Crear objeto DateTime
		//$dt = new DateTime($fecha);

		// Sumar un día
		//$dt->modify('+1 day');

		// Obtener la nueva fecha como string
		//$strfecha = $dt->format('Y-m-d H:i:s');
		//fin
		$fecha_recoleccion = $strfecha; //date("Y-m-d H:i:s"); //"2023-08-21 16:30";

		$telefono = $rsl->consignee_phone;

		$numero_guia = '';
		if ($rsl->tracking_replace == null) {
			$numero_guia = $rsl->tracking_number;
		} else {
			$numero_guia = $rsl->tracking_replace;
		}


		if ($rsl->tipo_servicio == "SER") {
			$montoCCE = 0;
		}
		if ($rsl->tipo_servicio == "CCE") {
			$montoCCE = $rsl->cobro_final;
		}


		if ($_SESSION['pais'] == 1) {
			$datos = array(
				"recolecta_fecha" => $fecha_recoleccion,
				"recolecta_comentario" => "StarShip",
				"tipo_entrega" => $rsl->tipo_entrega,
				"guias" => array(
					array(
						"identificacion" => "100",
						"nombre"         => $rsl->consignee,
						"correo"         => $rsl->consignee_email,
						"telefono"       => $telefono,
						"direccion"      => $rsl->consignee_address1,
						"tipo_servicio"  => $rsl->tipo_servicio,
						"monto_cce"      => $montoCCE,
						"agencia_destino" =>  $rsl->agencia_destino,
						"con_seguro"     => "0",
						"con_liquidacion_documento" => "0",
						"departamento_id"   => $_POST['depto'],
						"municipio_id"      => $_POST['munic'],
						"master"            => array(
							"numero"          => $master,
							"ata_miami"       => $fecha_arribo_miami,
							"arribo_aduana"   => $request216->fecha,
							"liberado_aduana" => $request288->fecha,


						),
						"detalle" => array(
							array(
								"guia"          => $numero_guia,
								"peso"          => $rsl->gweight,
								"contenido"     => $rsl->commodity,
								"unidad_medida" => "LB",
							)
						)
					)
				)
			);
			// echo   json_encode($datos);

		}


		if ($_SESSION['pais'] == 2 || $_SESSION['pais'] == 3) {

			$datos = array(
				"recolecta_fecha" => $fecha_recoleccion,
				"recolecta_comentario" => "StarShip",
				"tipo_entrega" => $rsl->tipo_entrega,

				"facturacion" => array(
					//"nit" => $rsl->consignee_account,
					"nit" => null,
					"nombre" => $rsl->consignee,
					"correo" => $rsl->consignee_email,
					"direccion"      => $rsl->consignee_address1,
				),
				"guias" => array(
					array(
						"identificacion" => "100",
						"nombre"         => $rsl->consignee,
						"correo"         => $rsl->consignee_email,
						"telefono"       => $telefono,
						"direccion"      => $rsl->consignee_address1,
						"tipo_servicio"  => $rsl->tipo_servicio,
						"monto_cce"      => $rsl->cobro_final,
						"agencia_destino" =>  $rsl->agencia_destino,
						"con_seguro"     => "0",
						"con_liquidacion_documento" => "0",
						"departamento_id"   =>  $_POST['depto'],
						"municipio_id"      =>  $_POST['munic'],
						"master"            => array(
							"numero"          => $master,
							"ata_miami"       => $fecha_arribo_miami,
							"arribo_aduana"   => $request216->fecha,
							"liberado_aduana" => $request288->fecha,


						),
						"detalle" => array(
							array(
								"guia"          => $numero_guia,
								"codigo"        => null,
								"peso"          => $rsl->gweight,
								"contenido"     => $rsl->commodity,
								"unidad_medida" => "LB",
								"cobro" => array(
									array(
										"codigo" => "IVA",
										"monto"  => $rsl->iva
									),
									array(
										"codigo" => "DAI",
										"monto"  => $rsl->dai
									),
									array(
										"codigo" => "FLETE",
										"monto"  => "0.00"
									),
									array(
										"codigo" =>  "DOCS",
										"monto"  => number_format($rsl->manejo + $rsl->total_ivag, 2, '.', '.')
									)

								)
							)

						)
					)
				)
			);
			//echo json_encode($datos);
			//	echo $token;
			//exit;
		}


		$url = "";
		$url = 'https://app.c807.com/guia.php/api/set_registro';



		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => false, //TODO: quitar esto en produccion
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $json = json_encode($datos),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: ' . $token,

			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		echo $response;
	}

	public function update_tracking_id($id, $recolecta, $guia)
	{
		$rsl =   $this->WarehouseModel->update_tracking_id($id, $recolecta, $guia);
		echo $rsl;
	}

	public function asignar_partida($id, $partida)
	{
		$rsl =   $this->WarehouseModel->asignar_partida($id, $partida);
		echo $rsl;
	}

	public function mail_a()
	{


		$header = array(
			'Host: qa.tracking.mailamericas.com',
			'Content-Type: application/json',
			'Cache-Control: no-cache'
		);

		$data = array(
			"grant_type" => "client_credentials",
			"client_id" => "39",
			"client_secret" => "cEWvVrkPLPcWNdF0QKCC2ZJHRJEN1CQAgQKbaH2A",

			"scope" => "*"
		);

		$url = "https://tracking.mlasalvador.com/oauth/token";


		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_exec($ch);
		$rsl = curl_exec($ch);


		if (curl_exec($ch) === false) {
			echo 'Curl error: ' . curl_error($ch);
		}

		$errors = curl_error($ch);  //retorna errores                                                                                                          
		$result = curl_exec($ch);
		$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); //retorna el codigo de respuesta


		echo $returnCode;
		curl_close($ch);


		$code = json_decode($rsl);

		$token = $code->access_token;



		$header = array(
			'Host: qa.tracking.mailamericas.com',
			'Content-Type: application/json',
			'Cache-Control: no-cache',
			'Authorization: Bearer ' . $token
		);

		$data = array(
			"tracking" => "ML0000149-TST", // m
			"date" => "2020-12-31T13:10:55-03:00", // m
			"description" => "Entregado", // m
			"city" => "CCO", // m

			"received_by" => "CAMILA BENÍTEZ 5981259-9", //m
			"relationship" => null, //m
			"delivery_proof" => [
				"https://app-tst.dinet.com.pe/AppApiIntegration/api/D4W/GetImage/28234",
				"https://app-tst.dinet.com.pe/AppApiIntegration/api/D4W/GetImage/28235",
				"https://app-tst.dinet.com.pe/AppApiIntegration/api/D4W/GetImage/28236",
				"https://app-tst.dinet.com.pe/AppApiIntegration/api/D4W/GetImage/28237"
			],

			"gcs" => "18.6409321,-91.777005",
			"details" => ""
		);

		$url = "https://tracking.mlasalvador.com/api/v1/providers/events";

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$resultStr = curl_exec($ch);
		curl_close($ch);

		echo $resultStr;
	}

	public function pdf_wh2_bk($id, $master)
	{
		$this->datos['data'] = $this->WhModel->lista_warehouse_pdf2($id);
		$this->load->view("awb/pdf", $this->datos);
	}

	public function pdf_wh2($id, $master)
	{

		include getcwd() . "/application/libraries/phpqrcode/qrlib.php";

		$datos['referencia'] =  $this->WhModel->lista_warehouse_pdf2($id);

		foreach ($datos['referencia'] as $item) {
			$name_qr =  $item->tracking_number;
			QRcode::png($name_qr, getcwd() . "/public/qr_ma/" . $name_qr . ".png",       5, 5, 5, 5);
		}

		$this->pdf = new FPDF('L', 'mm', array(102, 152),      1, 0, 'C', 0);

		$archivo = "";

		foreach ($datos['referencia'] as $item) {
			$this->pdf->SetMargins(1, 3, 2);
			$this->pdf->AddPage();
			$this->pdf->AliasNbPages();
			$this->pdf->SetTitle($master);
			$this->pdf->SetMargins(1, 1, 1);
			$this->pdf->SetAutoPageBreak(true, 5);
			$this->pdf->SetLineWidth(0.5);

			$this->pdf->SetFillColor(200, 200, 200);
			$archivo = "";

			$name_qr =  $item->tracking_number . ".png";

			$this->pdf->Rect(4, 4, 143, 93, 5, '1', 'DO');                // L,  D, W, 
			$this->pdf->Rect(4, 4, 36, 35, 9, '2', 'DO');

			$this->pdf->Image(getcwd() . "/public/qr_ma/" . $name_qr,          7, 6, 30, 30);

			$this->pdf->SetMargins(10, 1, 10);
			$this->pdf->SetFont('Arial', 'B', 10);
			$this->pdf->Ln(2);
			$this->pdf->Cell(48, 4, 'Principal',                                                     0, 0, 'R', 0);
			$this->pdf->Cell(80, 4, 'Fecha: ' . $item->fecha_creacion,                                 0, 0, 'R', 0);
			$this->pdf->Ln(4);
			$this->pdf->SetFont('Arial', '', 10);
			$this->pdf->Cell(48, 4, 'Telefono:',                                                     0, 0, 'R', 0);
			$this->pdf->Ln(4);
			$this->pdf->SetFont('Arial', 'B', 10);
			$this->pdf->Cell(49, 4, 'DESTINO',                                                       0, 0, 'R', 0);
			$this->pdf->Ln(5);
			$this->pdf->SetFont('Arial', '', 10);
			$this->pdf->SetFillColor(255, 255, 255);
			$this->pdf->SetXY(41, 17);
			$this->pdf->MultiCell(100, 4, 'Nombre: ' . utf8_decode($item->consignee),                  0, 1, 'R', 0);
			$this->pdf->SetXY(41, 21);

			$this->pdf->MultiCell(100, 4, 'Contacto: ' . $item->consignee_email,                       0, 1, 'R', 0);
			$this->pdf->SetXY(41, 25);
			$this->pdf->SetFont('Arial', '', 11);
			$this->pdf->MultiCell(69, 4, 'Telefono: ' . $item->consignee_phone,                        0, 1, 'R', 0);
			$this->pdf->SetFont('Arial', '', 10);
			$this->pdf->SetFillColor(255, 255, 255);
			$this->pdf->SetXY(41, 29);
			$this->pdf->MultiCell(100, 4, 'Direccion: ' . utf8_decode($item->consignee_address1),      0, 1, 'L', 0);
			$this->pdf->SetXY(41, 37);
			$this->pdf->SetFont('Arial', '', 10);
			$this->pdf->MultiCell(75, 2, 'Referencia: ' . $item->referencia,                           0, 1, 'L', 0);
			$this->pdf->Ln(5);

			$this->pdf->SetLineWidth(0.8);
			$this->pdf->Line(75, 65, 75, 50);


			//  $_SESSION['pais']=1;
			$this->pdf->SetFont('Arial', 'B', 26);

			if ($_SESSION['pais'] == 2) {
				$this->pdf->Cell(130, 4, $item->internal_tracking,                    0, 0, 'C', 0);
			} else {
				$this->pdf->Cell(130, 4, $item->tracking_number,                    0, 0, 'C', 0);
			}



			$this->pdf->SetFont('Arial', 'B', 30);
			$this->pdf->Ln(5);
			$this->pdf->SetXY(30, 54);
			$this->pdf->MultiCell(35, 4, $item->tipo_servicio,                 0, 1, 'R', 0);
			$this->pdf->SetXY(98, 54);
			$this->pdf->MultiCell(35, 4, $item->departamento_code,             0, 1, 'L', 0);
			$this->pdf->SetFont('Arial', '', 10);

			$this->pdf->SetFillColor(255, 255, 255);
			$this->pdf->SetXY(99, 60);
			$this->pdf->MultiCell(35, 4, utf8_decode($item->municipio_name),        0, 1, 'C', 0);

			$archivo = $item->tracking_number;

			$this->pdf->Ln(3);
			$this->pdf->SetFont('Arial', '', 10);


			if ($item->tipo_servicio == "SER") {
				$this->pdf->Cell(27, 2, ' ',           0, 0, 'R', 0);
			} else {

				if ($_SESSION['pais'] == 1) {
					$this->pdf->Cell(27, 2, 'A Cobrar: $' . round($item->cobro_final, 2),           0, 0, 'R', 0);
				}
				if ($_SESSION['pais'] == 2) {
					$this->pdf->Cell(27, 2, 'A Cobrar: Q' . round($item->cobro_final, 2),           0, 0, 'R', 0);
				}

				if ($_SESSION['pais'] == 3) {
					$this->pdf->Cell(27, 2, 'A Cobrar: L' . round($item->cobro_final, 2),           0, 0, 'R', 0);
				}
				//$this->pdf->Cell(27, 2, 'A Cobrar: $' . round($item->cobro_final, 2),           0, 0, 'R', 0);
			}


			$this->pdf->Cell(80, 2, 'Pago: Contado',                            0, 0, 'R', 0);
			$this->pdf->Ln(4);
			$this->pdf->Cell(25, 2, 'Peso: ' . $item->gweight . ' lb',              0, 0, 'L', 0);
			$this->pdf->Cell(85, 2, 'Tipo de Entrega: ',                        0, 0, 'R', 0);
			$this->pdf->Ln(4);
			$this->pdf->Cell(40, 2, 'Peso Maximo: 0.00 lb',                     0, 0, 'L', 0);
			$this->pdf->SetFont('Arial', 'B', 30);
			$this->pdf->SetXY(90, 79);
			$this->pdf->MultiCell(45, 5, $item->tipo_entrega,                   0, 1, 'R', 0);
			$this->pdf->SetFont('Arial', '', 10);
			$this->pdf->Ln(4);
			$this->pdf->Cell(40, 2, 'Especiales',                               0, 0, 'L', 0);
		}

		$destino      = getcwd() . "/document/qr/";
		$desabsoluto  = "document/qr/";

		if (!is_dir($destino)) {
			mkdir($destino, 0777, true);
		}

		$nombre_archivo =  $master . '.pdf';
		$this->pdf->Output("F", $destino . $nombre_archivo, true);
		$this->pdf->close();
		$datosretorno  = array(
			'destino'        =>  $desabsoluto,
			'nombre_archivo' =>  $nombre_archivo,
		);
		echo json_encode($datosretorno);
	}




	public function departamento()
	{
		$muni = 0;


		$url = 'https://app.c807.com/guia.php/catalogo/departamento';

		$curl = curl_init();
		//	$url = 'https://app.c807.com/guia.php/catalogo/departamento';

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => false, //TODO: quitar en produccion
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: ' . $_SESSION['token']
			),
		));
		$rsp = curl_exec($curl);



		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
			echo "Error en cURL: $error_msg";
		} else {
			// Obtener el código de respuesta HTTP
			$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			if ($http_code == 200) {
			} else {
				echo "Error HTTP: $http_code. Respuesta: $rsp";
			}
		}

		curl_close($curl);
		$items = json_decode($rsp, true);



		$url2 = 'https://app.c807.com/guia.php/catalogo/municipio?departamento=' . $muni;
		$curl2 = curl_init();
		//$url2 = 'https://app.c807.com/guia.php/catalogo/municipio?departamento=' . $muni;
		curl_setopt_array($curl2, array(
			CURLOPT_URL => $url2,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => false, //TODO: quitar en produccion
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: ' . $_SESSION['token']
			),
		));
		$rsp2 = curl_exec($curl2);
		curl_close($curl2);
		$items2 = json_decode($rsp2, true);


		$this->datos['municipios'] = $items2;
		$this->datos['departamentos'] = $items;
		$this->load->view("awb/lists_depart", $this->datos);
		//var_dump($items);
		//echo $items[0]['guia'];
		//$texto = $items[0]['guia'];

	}

	public function municipio($id)
	{

		$curl = curl_init();
		$s = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);



		$url = 'https://app.c807.com/guia.php/catalogo/municipio?departamento=' . $s;
		//$url = 'https://app.c807.com/guia.php/catalogo/municipio?departamento=' . $s;
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: ' . $_SESSION['token']
			),
		));
		$rsp = curl_exec($curl);
		curl_close($curl);
		$items = json_encode($rsp, true);
		echo $rsp;
		//$this->datos['departamentos'] = $items;
		//$this->load->view("awb/lists_depart", $this->datos);
		//var_dump($items);
		//echo $items[0]['guia'];
		//$texto = $items[0]['guia'];
	}

	public function update_depart()
	{

		$id_manifiesto = $_POST['id_manifiesto'];

		$departamentos = $_POST['ls_depart'];
		$municipios = $_POST['ls_municipio'];
		$dui = $_POST['dui'];
		list($id_d, $codigo_d, $nombre_d) = explode('-', $departamentos);

		$nombre_d = utf8_encode($nombre_d);


		list($id_m, $nombre_m) = explode('-', $municipios);


		$rsl =  $this->WhModel->update_dm($id_manifiesto, $id_d, $codigo_d, $nombre_d, $id_m, $nombre_m, $dui);



		// Código PHP (fuera de CodeIgniter)
		$datos_solicitud = [

			'id_manifiesto' => $id_manifiesto,
			'dui'           => $dui,


			'departamento' => [
				'id'       => $id_d,
				'codigo'   => $codigo_d,
				'nombre'   => $nombre_d,
			],


			'municipio' => [
				'id'     => $id_m,
				'nombre' => $nombre_m,
			],


		];

		// CONVERSIÓN FINAL a JSON
		$json_data = json_encode($datos_solicitud, JSON_PRETTY_PRINT);

		$data = [
			"id_solicitante" => $_SESSION["user_id"],
			"fecha" => date('Y-m-d h:i:s'),
			"solicitud" => $id_manifiesto . "-update-department",
			"respuesta" => json_encode($json_data),
			"pagina" => "update-department-tracking"
		];

		$this->WhModel->insert_log($data);






		echo $rsl;
	}

	// segmento  para  dibujar  pdf de los manifiestos    
	public function generar_manifiesto($id, $referencia)
	{
		//echo $_SESSION['pais'];
		//die();
		if ($_SESSION['pais'] == 1) {
			$this->generar_manfiesto_pdfSV($id, $referencia);
		} else if ($_SESSION['pais'] == 2) {
		} else if ($_SESSION['pais'] == 3) {
			$this->generar_manfiesto_pdfHN($id, $referencia);
		}
	}

	public function generar_manifiesto_individual($id)
	{

		$this->generar_manifiesto_pdfGT($id);
	}




	function procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total)
	{
		// Capturar los productos entre corchetes en la primera cadena
		preg_match_all('/\[(.*?)\]/', $cadena_productos, $matches_productos);
		$productos = $matches_productos[1]; // Extraer los productos sin los corchetes

		// Capturar las cantidades entre corchetes en la segunda cadena
		preg_match_all('/\[(\d+)\]/', $cadena_cantidades, $matches_cantidades);
		$cantidades = array_map('intval', $matches_cantidades[1]); // Convertir las cantidades a enteros

		if (empty($productos) || empty($cantidades)) {
			echo "No se encontraron productos o cantidades en las cadenas.";
			return [];
		}

		//echo count($productos) ."-";


		//$cantidad_productos = $cantidades[0];


		//echo $cantidad_productos;

		//return [];


		// Asegurarnos de que las cantidades y los productos coincidan en número
		if (count($productos) != count($cantidades)) {
			var_dump($productos);
			var_dump($cantidades[0]);
			echo "La cantidad de productos y cantidades no coincide.";
			//return [];
		}

		// Total de unidades para calcular el precio por unidad
		$cantidad_total_productos = array_sum($cantidades);
		$precio_unitario = $precio_total / $cantidad_total_productos;

		$resultado = [];

		foreach ($productos as $index => $descripcion) {
			//$cantidad = $cantidades[$index];
			$cantidad = $cantidad_total_productos;

			$precio_por_producto = $precio_unitario * $cantidad; // Precio total proporcional para este producto

			$resultado[] = [
				'descripcion' => $descripcion,
				'cantidad' => $cantidad,
				'precio' => $precio_por_producto, // Precio total ya calculado
			];
		}

		return $resultado;
	}



	function agruparProductosMA($productos)
	{
		$agrupados = [];

		$concat = "";
		$i = 1;
		$cantidad = 0;
		$precio = 0;

		foreach ($productos as $producto) {
			// Convertir a minúsculas para comparar sin importar mayúsculas/minúsculas
			$nombre_base = strtolower(preg_replace('/\([^\)]*\)/', '', $producto['descripcion']));

			//Codigo agregado por Nico
			$concat = $concat . "+" . $nombre_base;

			//$agrupados[""]

			/*if (!isset($agrupados[$nombre_base])) {
				$agrupados[$nombre_base] = [
					'descripcion' => ucfirst($concat), // Mostrar con capitalización inicial
					'cantidad' => 0,
					'precio' => 0,
				];
			}*/




			/*if (!isset($agrupados[$nombre_base])) {
				$agrupados[$nombre_base] = [
					'descripcion' => ucfirst($nombre_base), // Mostrar con capitalización inicial
					'cantidad' => 0,
					'precio' => 0,
				];
			}*/

			// Sumar cantidades y precios directamente
			//$agrupados[$nombre_base]['cantidad'] = $producto['cantidad'];
			//$agrupados[$nombre_base]['precio'] += $producto['precio'];

			$cantidad = $producto['cantidad'];
			$precio = $producto['precio'];

			$i++;
		}


		$agrupados["productToShow"]["descripcion"] = $concat;
		$agrupados["productToShow"]["cantidad"] = $cantidad;
		$agrupados["productToShow"]["precio"] = $precio;


		return array_values($agrupados); // Reindexar el array
	}



	public function generar_manfiesto_pdfSV($id, $referencia)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$id_manifiesto = $id;

		//include getcwd()."/application/libraries/fpdf/fpdf.php";
		$Manifiesto  = 'manifiesto';
		$time        = time();
		$fecha       = date("d-m-Y (H:i:s)", $time);
		// $fecha =  date()
		$dia         = date("d");
		$mes         = date("m");
		$periodo     = date("Y");
		$mora        = 0;
		setlocale(LC_ALL, 'es_ES');
		$monthNum    = $mes;
		$dateObj     = DateTime::createFromFormat('!m', $monthNum);
		//  $monthName   = strftime('%B', $dateObj->getTimestamp());
		//$this->pdf   = new FPDF('L', 'mm','Legal'); 
		$this->pdf   = new FPDF();

		$dlist_manifiestos        = $this->WhModel->lista_warehouse1($id, $referencia);
		// var_dump($dlist_manifiestos);

		foreach ($dlist_manifiestos  as  $row) {
			$this->pdf->setY(0);
			$this->pdf->setX(0);
			//$SHIPPING = $row->value * 0.15;
			$SHIPPING = 0;
			$TAX      = 0;


			$this->pdf->AddPage();
			$this->pdf->AliasNbPages();
			$this->pdf->SetLeftMargin(10);
			$this->pdf->SetRightMargin(10);
			$this->pdf->SetFillColor(200, 200, 200);
			// $this->pdf->SetFont('Arial', 'B', 7);


			$this->pdf->SetFillColor(255, 255, 255);
			$this->pdf->SetTextColor(46, 44, 44);
			$this->pdf->SetFont('Arial', '', 11);
			$this->pdf->Ln(5);


			$this->pdf->Cell(15, 12, '', 0, 0, 'R', 1);
			$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/aliexpress.png', 10, 16, 85, 30), 0, 0, 'L', 0);
			//$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/MailAmerica.jpg', 45, 16, 50, 30), 0, 0, 'L', 0); 
			//  $this->pdf->Cell(40, 16, $this->pdf->Image(getcwd() . '/public/img/logo_2.png', 17, 16, 30, 16), 0, 0, 'L', 0);


			$this->pdf->Ln(30);



			$this->pdf->Cell(200, 12, 'No. 111 Tianlin Road, Tower, Shangai ', 0, 0, 'L', 0);
			$this->pdf->Ln(10);
			$this->pdf->Cell(200, 12, 'Shangai ', 0, 0, 'L', 0);
			// $this->pdf->Ln(10);
			// $this->pdf->Cell(200, 12, 'PO Box 81226 Seattle, WA 98108-1226', 0, 0, 'L', 0);
			$this->pdf->Ln(15);
			$this->pdf->Cell(200, 12, 'ENVIADO A: ' . $row->consignee, 0, 0, 'L', 0);
			$this->pdf->Ln(10);


			$this->pdf->MultiCell(150, 5, 'DIRECCION: ' . $row->consignee_address1, 0, 'TB', 'L', false);
			$this->pdf->Ln(10);
			$this->pdf->Cell(200, 12, 'Tel: ' . $row->consignee_phone, 0, 0, 'L', 0);
			$this->pdf->Ln(10);


			$this->pdf->Cell(200, 12, 'DUI: ' . $row->consignee_account, 0, 0, 'L', 0);
			$this->pdf->Ln(15);


			$this->pdf->SetFont('Arial', 'B', 11);

			$valory = 0;
			$valorx = 0;


			$valory =  $this->pdf->getY();
			$valorx = $this->pdf->getX();
			$this->pdf->MultiCell(10, 7.5, utf8_decode(''), 0, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 10);
			$this->pdf->MultiCell(40, 12, utf8_decode('N° de factura'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 50);
			$this->pdf->MultiCell(35, 12, utf8_decode('Fecha de factura'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 85);
			$this->pdf->MultiCell(30, 12, utf8_decode('Término'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 115);
			$this->pdf->MultiCell(40, 12, utf8_decode('No de Cliente'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 155);
			$this->pdf->MultiCell(20, 6, utf8_decode('No de Pág.'), 1, 'TB', 'C', false);
			$this->pdf->Ln(0);


			//bloque paa detalle de factura encabezado
			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(40, 9, $row->tracking_number, 1, 0, 'C', 0);
			$this->pdf->Cell(35, 9, date("d-m-Y", strtotime($row->fecha)), 1, 0, 'C', 0);

			//$this->pdf->Cell(35, 12,  date("d-m-Y", strtotime($row->fecha)) , 1, 0, 'C', 0);
			$this->pdf->Cell(30, 9, 'FOB', 1, 0, 'C', 0);
			$this->pdf->Cell(40, 9, $row->consignee_account, 1, 0, 'C', 0);
			$this->pdf->Cell(20, 9, '1', 1, 0, 'C', 0);
			$this->pdf->Ln(10);
			$this->pdf->SetFont('Arial', 'B', 20);
			$this->pdf->Cell(50, 9, 'FACTURA', 0, 0, 'L', 0);
			$this->pdf->Ln(10);
			$this->pdf->SetFont('Arial', 'B', 11);

			$valory1 = 0;
			$valorx1 = 0;


			$valory1 =  $this->pdf->getY();
			$valorx1 = $this->pdf->getX();

			$this->pdf->MultiCell(10, 7.5, utf8_decode(''), 0, 'TB', 'C', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 10);
			$this->pdf->MultiCell(30, 12, utf8_decode('Cantidad'), 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 40);
			$this->pdf->MultiCell(115, 12, utf8_decode('Cod. Artículo Descripción '), 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 155);
			$this->pdf->MultiCell(17, 6, utf8_decode('Precio Unitario'), 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 172);
			$this->pdf->MultiCell(20, 6, utf8_decode('Precio Total'), 1, 'TB', 'J', false);

			// fin detalle de factura encabezado

			//datos de detalle de factura 

			$cadena_descripcion = $row->commodity;


			/** [1] Bloque para tomar detalles de productos */


			$cadena_productos = $row->commodity;

			$precio_total = $row->value; // Precio total

			// validar a que empresa pertenece el manifiesto
			$verificar_empresa        = $this->WhModel->verificar_empresa($id);
			$tipo = $verificar_empresa->tipo_factura;


			if ($tipo == "MA") {


				$cadena_cantidades = $row->items_quantities;
				$resultado = $this->procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total);
				$agrupados = $this->agruparProductosMA($resultado);
			}
			if ($tipo == "IM") {



				$cadena_cantidades = $row->items;
				// Procesar los productos y cantidades

				//echo $precio_total ;


				$resultado = $this->procesarProductosConCantidadesIM($cadena_productos, $cadena_cantidades, $precio_total);

				// Agrupar productos por nombre
				$agrupados = $this->agruparProductosIM($resultado);
			}


			/** fin [1]  */

			foreach ($agrupados as $item) {

				$this->pdf->Ln(0);
				$this->pdf->SetFont('Arial', '', 9);
				$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
				$this->pdf->Cell(30, 9, $item['cantidad'], 1, 0, 'C', 0);
				$this->pdf->Cell(115, 9, $item['descripcion'], 1, 0, 'C', 0);
				$this->pdf->Cell(17, 9, number_format($item['precio'] / number_format($item['cantidad']), 2), 1, 0, 'C', 0);
				$this->pdf->Cell(20, 9, number_format($item['precio'], 2), 1, 0, 'C', 0);

				$this->pdf->Ln(10);
				$this->pdf->SetFont('Arial', 'B', 11);
				$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
				$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			}
			$this->pdf->Cell(115, 9, 'TAX', 0, 0, 'R', 0);
			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(17, 9, '', 10, 0, 'C', 0);
			$this->pdf->Cell(20, 9, number_format($TAX, 2), 0, 0, 'C', 0);
			$this->pdf->Ln(10);
			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			$this->pdf->SetFont('Arial', 'B', 11);
			$this->pdf->Cell(115, 9, 'SHIPPING', 0, 0, 'R', 0);
			$this->pdf->SetFont('Arial', '', 9);
			$this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);
			$this->pdf->Cell(20, 9, number_format($SHIPPING, 2), 0, 0, 'C', 0);
			$this->pdf->Ln(10);



			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			$this->pdf->SetFont('Arial', 'B', 11);

			$this->pdf->Cell(115, 9, 'TOTAL USD', 0, 0, 'R', 0);
			$this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);

			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(20, 9, number_format(($TAX + $SHIPPING + $row->value), 2), 0, 0, 'C', 0);

			//fin detalle de datos
		}


		$destino      = getcwd() . "/document/Manifiesto/";
		$desabsoluto  =  "document/Manifiesto/";
		if (!is_dir($destino)) {
			mkdir($destino, 0777, true);
		}
		$nombre_archivo =  'Manifiesto' . date("d-m-Y") . '_' . $id . '.pdf';
		$this->pdf->Output("F", $destino . $nombre_archivo, true);
		$this->pdf->close();
		$datosretorno  = array(
			'destino'        => $desabsoluto,
			'nombre_archivo' => $nombre_archivo,

		);
		// header('Content-type: appliation/json');
		echo json_encode($datosretorno);
	}


	public function generar_manifiesto_pdfGT($id)
	{
		//phpinfo();
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$id_manifiesto = $id;



		// Obtener los datos del manifiesto
		$dlist_manifiestos = $this->WhModel->lista_warehouse_individual($id);

		// Crear la carpeta del manifiesto
		$manifiesto = "";
		if (!empty($dlist_manifiestos)) {
			$manifiesto = $dlist_manifiestos[0]->manifiesto; // Usar el nombre del manifiesto
		}
		$destino_base = getcwd() . "/document/Manifiesto/";
		$destino_carpeta = $destino_base . "M" . $manifiesto . '/';
		if (!is_dir($destino_carpeta)) {
			mkdir($destino_carpeta, 0777, true);
		}

		foreach ($dlist_manifiestos as $row) {
			$tracking_number = $row->tracking_number;

			$this->pdf = new FPDF();
			$this->pdf->AddPage();
			$this->pdf->AliasNbPages();
			$this->pdf->SetLeftMargin(10);
			$this->pdf->SetRightMargin(10);
			$this->pdf->SetFont('Arial', '', 11);


			$this->pdf->SetFillColor(255, 255, 255);
			$this->pdf->SetTextColor(46, 44, 44);
			$this->pdf->SetFont('Arial', '', 11);
			//	$this->pdf->Ln(5);

			$origen = $row->pais_origen;
			// Encabezado y diseño inicial
			if ($row->id_empresa == 103 ||  $row->id_empresa == 104 || $row->id_empresa == 101) {
				//$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/aliexpress.png', 10, 16, 85, 30), 0, 0, 'L', 0);
				//	$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/temu.png'), 0, 0, 'L', 0);

				if ($row->client_id == "AliExpress") {
					$this->pdf->Cell(50, 40, $this->pdf->Image(getcwd() . '/public/img/aliexpress.png', $this->pdf->getX(), $this->pdf->getY(), 70, 24), 0, 0, 'L', 0);
					$address = "No. 111 Tianlin Road, Tower, Shangai, China.";
					$this->pdf->Ln(25);
					$this->pdf->Cell(200, 12, $address, 0, 0, 'L', 0);
					$this->pdf->Ln(10);
					$this->pdf->Cell(200, 12, '', 0, 0, 'L', 0);
				}else{
					$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/Ebay.jpeg', $this->pdf->getX(), $this->pdf->getY(), 50, 24), 0, 0, 'L', 0);
					$this->pdf->Ln(16);
					$this->pdf->Cell(200, 12, 'First Floor, 25 St Stephens Green ', 0, 0, 'L', 0);
					$this->pdf->Ln(10);
					$this->pdf->Cell(200, 12, 'Shangai ', 0, 0, 'L', 0);
				
				}

				
				
				
			}
			if ($row->id_empresa == 113) {

				$img = getcwd() . '/public/img/temu.png';

				$cellW = 50;
				$cellH = 24;

				$x = $this->pdf->GetX();
				$y = $this->pdf->GetY();

				// calcular proporción real
				list($iw, $ih) = getimagesize($img);

				$imgW = $cellW;
				$imgH = $imgW * ($ih / $iw);

				// si se pasa del alto de la celda, ajusta por alto
				if ($imgH > $cellH) {
					$imgH = $cellH;
					$imgW = $imgH * ($iw / $ih);
				}

				// centrar dentro de la celda
				$imgX = $x + (($cellW - $imgW) / 2);
				$imgY = $y + (($cellH - $imgH) / 2);

				// reservar celda y dibujar imagen
				$this->pdf->Cell($cellW, $cellH, '', 0, 0, 'L', 0);
				$this->pdf->Image($img, $imgX, $imgY, $imgW, $imgH);

				$this->pdf->Ln(22);
				$this->pdf->Cell(200, 12, '28th Floor, No. 533 Loushanguan Road, Changning District, Shanghai, 200051, China. ', 0, 0, 'L', 0);
				$this->pdf->Ln(6);
				$this->pdf->Cell(200, 12, 'Shangai ', 0, 0, 'L', 0);
			}
			//$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/Ebay.jpeg'), 0, 0, 'L', 0);

			$this->pdf->Ln(10);
			$this->pdf->Cell(200, 12, 'ENVIADO A: ' . utf8_decode($row->consignee), 0, 0, 'L', 0);
			$this->pdf->Ln(10);
			$this->pdf->MultiCell(150, 5, 'DIRECCION: ' . utf8_decode($row->consignee_address1), 0, 'L', false);
			$this->pdf->Ln(10);
			$this->pdf->Cell(200, 12, 'Tel: ' . $row->consignee_phone, 0, 0, 'L', 0);
			$this->pdf->Ln(10);
			$this->pdf->Cell(200, 12, 'DPI: ' . $row->consignee_account, 0, 0, 'L', 0);
			$this->pdf->Ln(15);


			$valory =  $this->pdf->getY();
			$valorx = $this->pdf->getX();

			$this->pdf->MultiCell(10, 7.5, '', 0, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 10);
			$this->pdf->MultiCell(40, 12, utf8_decode('N° de factura'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 50);
			$this->pdf->MultiCell(35, 12, 'Fecha de factura', 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 85);
			$this->pdf->MultiCell(30, 12, utf8_decode('Término'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 115);
			$this->pdf->MultiCell(40, 12, 'No de Cliente', 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 155);
			$this->pdf->MultiCell(20, 6, utf8_decode('No de Pág.'), 1, 'TB', 'C', false);
			$this->pdf->Ln(0);


			//bloque paa detalle de factura encabezado
			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(40, 9, $row->tracking_number, 1, 0, 'C', 0);
			$this->pdf->Cell(35, 9, date("d-m-Y", strtotime($row->fecha)), 1, 0, 'C', 0);

			//$this->pdf->Cell(35, 12,  date("d-m-Y", strtotime($row->fecha)) , 1, 0, 'C', 0);
			$this->pdf->Cell(30, 9, 'FOB', 1, 0, 'C', 0);
			$this->pdf->Cell(40, 9, $row->consignee_account, 1, 0, 'C', 0);
			$this->pdf->Cell(20, 9, '1', 1, 0, 'C', 0);
			$this->pdf->Ln(10);
			$this->pdf->SetFont('Arial', 'B', 20);
			$this->pdf->Cell(50, 9, 'FACTURA', 0, 0, 'L', 0);
			$this->pdf->Ln(10);
			$this->pdf->SetFont('Arial', 'B', 11);

			$valory1 = 0;
			$valorx1 = 0;


			$valory1 =  $this->pdf->getY();
			$valorx1 = $this->pdf->getX();

			$SHIPPING = 0;
			$TAX      = 0;


			$this->pdf->MultiCell(10, 7.5, '', 0, 'TB', 'C', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 10);
			$this->pdf->MultiCell(30, 12, 'Cantidad', 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 40);

			$this->pdf->MultiCell(115, 12, utf8_decode('Cod. Artículo Descripción'), 1, 'TB', 'J', false);

			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 155);
			$this->pdf->MultiCell(17, 6, 'Precio Unitario', 1, 'TB', 'R', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 172);
			$this->pdf->MultiCell(20, 6, 'Precio Total', 1, 'TB', 'R', false);

			// Detalle de productos
			//$agrupados = $this->procesarProductosConCantidadesIM($row->commodity, $row->items, $row->value); // Usando tu lógica
			//	$agrupados = $this->agruparProductosIM($agrupados); // Ajustar según tu método existente


			$cadena_descripcion = $row->commodity;


			/** [1] Bloque para tomar detalles de productos */


			$cadena_productos = $row->commodity;

			$precio_total = $row->value; // Precio total

			// validar a que empresa pertenece el manifiesto
			$verificar_empresa        = $this->WhModel->verificar_empresa($id);
			$tipo = $verificar_empresa->tipo_factura;


			if ($tipo == "MA") {


				$cadena_cantidades = $row->items_quantities;
				$resultado = $this->procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total);
				$agrupados = $this->agruparProductosMA($resultado);
			}
			if ($tipo == "IM") {



				$cadena_cantidades = $row->items;
				// Procesar los productos y cantidades

				//echo $precio_total ;


				$resultado = $this->procesarProductosConCantidadesIM($cadena_productos, $cadena_cantidades, $precio_total);

				// Agrupar productos por nombre
				$agrupados = $this->agruparProductosIM($resultado);
			}

			foreach ($agrupados as $item) {
				// Configuración del font
				$this->pdf->SetFont('Arial', '', 9);

				// Texto de la descripción
				$descripcion = utf8_decode($item['descripcion']);

				// Ancho máximo para la descripción
				$descripcionWidth = 115;

				// Altura base de línea
				$lineHeight = 5;

				// Estimamos cuántas líneas de texto necesitará la descripción
				$numLines = ceil($this->pdf->GetStringWidth($descripcion) / $descripcionWidth);

				// Si la descripción es de una sola línea, ajustamos la altura de la línea a 10
				if ($numLines <= 1) {
					$lineHeight = 10;
				}

				// La altura total de la MultiCell será el número de líneas por la altura de cada línea
				$descripcionHeight = $numLines * $lineHeight;

				// Altura común para la fila (basada en la altura de la descripción)
				$altura = max($descripcionHeight, 9); // Para que al menos sea 9 si no hay texto

				// Guardamos la posición actual (superior izquierda de la fila)
				$xInicioFila = $this->pdf->GetX();
				$yInicioFila = $this->pdf->GetY();

				// Ajuste para la columna de "Cantidad"
				$cantidadWidth = 30;
				$this->pdf->Cell(10, $altura, '', 0, 0, 'C', 0); // Espacio vacío
				$this->pdf->Cell($cantidadWidth, $altura, $item['cantidad'], 1, 0, 'C', 0);

				// Ajustamos la posición X para la columna de "Descripción"
				$this->pdf->SetXY($xInicioFila + 10 + $cantidadWidth, $yInicioFila);

				// Dibuja la descripción como MultiCell
				$this->pdf->MultiCell($descripcionWidth, $lineHeight, $descripcion, 1, 'L', 0);

				// La posición Y final de la descripción (parte inferior de la fila)
				$yFinalFila = max($this->pdf->GetY(), $yInicioFila + $altura);

				// Ajuste para la columna de "Precio Unitario"
				$precioUnitarioWidth = 17;
				$this->pdf->SetXY($xInicioFila + 10 + $cantidadWidth + $descripcionWidth, $yInicioFila);
				$this->pdf->Cell($precioUnitarioWidth, $altura, number_format($item['precio'] / $item['cantidad'], 2), 1, 0, 'R', 0);

				// Ajuste para la columna de "Precio Total"
				$precioTotalWidth = 20;
				$this->pdf->SetXY($xInicioFila + 10 + $cantidadWidth + $descripcionWidth + $precioUnitarioWidth, $yInicioFila);
				$this->pdf->Cell($precioTotalWidth, $altura, number_format($item['precio'], 2), 1, 0, 'R', 0);

				// Ajustamos la posición Y para el siguiente ciclo
				$this->pdf->SetY($yFinalFila);
			}

			$this->pdf->SetY($yFinalFila + 10);



			$this->pdf->SetFont('Arial', 'B', 11);
			$this->pdf->Cell(155, 9, 'TAX', 0, 0, 'R', 0);
			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(17, 9, '', 10, 0, 'C', 0);
			$this->pdf->Cell(20, 9, number_format($TAX, 2), 0, 0, 'R', 0);
			$this->pdf->Ln(10);
			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			$this->pdf->Cell(60, 9, utf8_decode('Método de pago: Tarjeta de crédito'), 0, 0, 'L', 0);
			$this->pdf->SetFont('Arial', 'B', 11);

			$this->pdf->Cell(55, 9, 'SHIPPING', 0, 0, 'R', 0);
			$this->pdf->SetFont('Arial', '', 9);
			$this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);
			$this->pdf->Cell(20, 9, number_format($SHIPPING, 2), 0, 0, 'R', 0);
			$this->pdf->Ln(10);



			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			$this->pdf->Cell(60, 9, 'Origen: ' . $origen, 0, 0, 'L', 0);
			$this->pdf->SetFont('Arial', 'B', 11);

			$this->pdf->Cell(55, 9, 'TOTAL USD', 0, 0, 'R', 0);
			$this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);

			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(20, 9, number_format(($TAX + $SHIPPING + $row->value), 2), 0, 0, 'R', 0);

			// Guardar el archivo PDF en la carpeta del manifiesto
			$nombre_archivo =  $tracking_number . '_2' . '.pdf';
			$this->pdf->Output("F", $destino_carpeta . $nombre_archivo, true);
			$this->pdf->close();
		}

		// Comprimir la carpeta en un archivo ZIP
		$zip_file = $destino_base . "M" . $manifiesto . '.zip';
		//echo $zip_file;

		$zip = new ZipArchive();
		if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
			$files = scandir($destino_carpeta);
			foreach ($files as $file) {
				if ($file !== '.' && $file !== '..') {
					$zip->addFile($destino_carpeta . $file, $file);
				}
			}
			$zip->close();
		}

		// Descargar automáticamente el archivo ZIP
		//header('Content-Type: application/zip');
		//header('Content-Disposition: attachment; filename="' . "M".$manifiesto . '.zip"');

		//	header('Content-Length: ' . filesize($zip_file));
		//	readfile($zip_file);

		// Eliminar el archivo ZIP después de la descarga
		//unlink($zip_file);


		$desabsoluto  =  "document/Manifiesto/";

		$nombre_archivo =   "M" . $manifiesto . ".zip";

		$datosretorno  = array(
			'destino'        => $desabsoluto,
			'nombre_archivo' => $nombre_archivo,

		);


		echo json_encode($datosretorno);
		exit;
	}


	public function generar_manfiesto_pdfHN($id, $referencia)
	{

		//die();
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$id_manifiesto = $id;

		//include getcwd()."/application/libraries/fpdf/fpdf.php";
		$Manifiesto  = 'manifiesto';
		$time        = time();
		$fecha       = date("d-m-Y (H:i:s)", $time);
		// $fecha =  date()
		$dia         = date("d");
		$mes         = date("m");
		$periodo     = date("Y");
		$mora        = 0;
		setlocale(LC_ALL, 'es_ES');
		$monthNum    = $mes;
		$dateObj     = DateTime::createFromFormat('!m', $monthNum);

		//  $monthName   = strftime('%B', $dateObj->getTimestamp());
		//$this->pdf   = new FPDF('L', 'mm','Legal'); 
		$this->pdf   = new FPDF();

		$preclasificacion = $this->WhModel->datos_preclasificacion($id);
		$empresas = [112, 113, 114];
		$logo_img = "";
		$address = "";

		if (in_array($preclasificacion->client_id, $empresas)) {
			$logo_img = "/public/img/temu.png";
			$address = "28th Floor, No. 533 Loushanguan Road, Changning District, Shanghai, 200051, China. Shangai";
		} else {
			$logo_img = "/public/img/aliexpress.png";
			$address = "No. 111 Tianlin Road, Tower, Shangai";
		}


		$dlist_manifiestos        = $this->WhModel->lista_warehouse1($id, $referencia);
		// var_dump($dlist_manifiestos);

		foreach ($dlist_manifiestos  as  $row) {



			$this->pdf->setY(0);
			$this->pdf->setX(0);
			//$SHIPPING = $row->value * 0.15;
			$SHIPPING = 0;
			$TAX      = 0;

			$this->pdf->AliasNbPages();
			$this->pdf->AddPage();

			$this->pdf->SetLeftMargin(10);
			$this->pdf->SetRightMargin(10);
			$this->pdf->SetFillColor(200, 200, 200);
			// $this->pdf->SetFont('Arial', 'B', 7);


			$this->pdf->SetFillColor(255, 255, 255);
			$this->pdf->SetTextColor(46, 44, 44);
			$this->pdf->SetFont('Arial', '', 11);
			$this->pdf->Ln(5);

			$this->pdf->SetFont('Arial', 'B', 12);
			$this->pdf->SetXY(170, 16);
			//$this->pdf->Cell(30, 10, utf8_decode('Pág. ') . $this->pdf->PageNo(), 0, 0, 'R');

			$this->pdf->SetFont('Arial', '', 11);

			$this->pdf->Cell(15, 12, '', 0, 0, 'R', 1);
			$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . $logo_img, 10, 16, 85, 30), 0, 0, 'L', 0);
			//$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/MailAmerica.jpg', 45, 16, 50, 30), 0, 0, 'L', 0); 
			//  $this->pdf->Cell(40, 16, $this->pdf->Image(getcwd() . '/public/img/logo_2.png', 17, 16, 30, 16), 0, 0, 'L', 0);


			$this->pdf->Ln(30);



			$this->pdf->Cell(200, 12, $address, 0, 0, 'L', 0);
			$this->pdf->Ln(10);
			$this->pdf->Cell(200, 12, 'Shangai ', 0, 0, 'L', 0);
			// $this->pdf->Ln(10);
			// $this->pdf->Cell(200, 12, 'PO Box 81226 Seattle, WA 98108-1226', 0, 0, 'L', 0);
			$this->pdf->Ln(15);
			$this->pdf->Cell(200, 12, 'ENVIADO A: ' . utf8_decode($row->consignee), 0, 0, 'L', 0);
			$this->pdf->Ln(10);


			$this->pdf->MultiCell(150, 5, 'DIRECCION: ' . utf8_decode($row->consignee_address1), 0, 'TB', 'L', false);
			$this->pdf->Ln(10);
			$this->pdf->Cell(200, 12, 'Tel: ' . $row->consignee_phone, 0, 0, 'L', 0);
			$this->pdf->Ln(10);


			//$this->pdf->Cell(200, 12, 'DUI: ' . $row->consignee_account, 0, 0, 'L', 0);
			//$this->pdf->Ln(15);


			$this->pdf->SetFont('Arial', 'B', 11);

			$valory = 0;
			$valorx = 0;


			$valory =  $this->pdf->getY();
			$valorx = $this->pdf->getX();
			$this->pdf->MultiCell(10, 7.5, utf8_decode(''), 0, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 10);
			$this->pdf->MultiCell(40, 12, utf8_decode('N° de factura'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 50);
			$this->pdf->MultiCell(35, 12, utf8_decode('Fecha de factura'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 85);
			$this->pdf->MultiCell(30, 12, utf8_decode('Término'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 115);
			$this->pdf->MultiCell(40, 12, utf8_decode('No de Cliente'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 155);
			$this->pdf->MultiCell(20, 6, utf8_decode('No de Pág.'), 1, 'TB', 'C', false);
			$this->pdf->Ln(0);


			//bloque paa detalle de factura encabezado
			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(40, 9, $row->tracking_number, 1, 0, 'C', 0);
			$this->pdf->Cell(35, 9, date("d-m-Y", strtotime($row->fecha)), 1, 0, 'C', 0);

			//$this->pdf->Cell(35, 12,  date("d-m-Y", strtotime($row->fecha)) , 1, 0, 'C', 0);
			$this->pdf->Cell(30, 9, 'CIP', 1, 0, 'C', 0);
			$this->pdf->Cell(40, 9, $row->consignee_account, 1, 0, 'C', 0);
			$this->pdf->Cell(20, 9, '1', 1, 0, 'C', 0);
			$this->pdf->Ln(10);
			$this->pdf->SetFont('Arial', 'B', 20);
			$this->pdf->Cell(50, 9, 'FACTURA', 0, 0, 'L', 0);
			$this->pdf->Ln(10);
			$this->pdf->SetFont('Arial', 'B', 11);

			$valory1 = 0;
			$valorx1 = 0;


			$valory1 =  $this->pdf->getY();
			$valorx1 = $this->pdf->getX();

			$this->pdf->MultiCell(10, 7.5, utf8_decode(''), 0, 'TB', 'C', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 10);
			$this->pdf->MultiCell(30, 12, utf8_decode('Cantidad'), 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 40);
			$this->pdf->MultiCell(115, 12, utf8_decode('Cod. Artículo Descripción '), 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 155);
			$this->pdf->MultiCell(17, 6, utf8_decode('Precio Unitario'), 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 172);
			$this->pdf->MultiCell(20, 6, utf8_decode('Precio Total'), 1, 'TB', 'J', false);

			// fin detalle de factura encabezado

			//datos de detalle de factura 

			$cadena_descripcion = $row->commodity;


			/** [1] Bloque para tomar detalles de productos */


			$cadena_productos = $row->commodity;

			$precio_total = $row->value; // Precio total

			// validar a que empresa pertenece el manifiesto
			$verificar_empresa        = $this->WhModel->verificar_empresa($id);
			$tipo = $verificar_empresa->tipo_factura;


			if ($tipo == "MA") {


				$cadena_cantidades = $row->items_quantities;

				//var_dump($cadena_cantidades);


				$resultado = $this->procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total);


				//var_dump($resultado);

				$agrupados = $this->agruparProductosMA($resultado);
			}

			if ($tipo == "IM") {



				$cadena_cantidades = $row->items;
				// Procesar los productos y cantidades

				//echo $precio_total ;


				$resultado = $this->procesarProductosConCantidadesIM($cadena_productos, $cadena_cantidades, $precio_total);




				// Agrupar productos por nombre
				$agrupados = $this->agruparProductosIM($resultado);
			}


			/** fin [1]  */
			$totalItems = count($agrupados);
			$index = 0;
			$sumaItem = 0;
			$precioItem = 0;
			$diferencia = 0;
			foreach ($agrupados as $item) {


				$index++; // Incrementamos índice manualmente

				// Verificamos si estamos en la última vuelta
				$esUltimaFila = ($index === $totalItems);


				$precioItem = number_format($item['precio'], 2);
				$sumaItem = $sumaItem + $precioItem;

				if ($esUltimaFila) {
					// Calculamos la diferencia que falta para llegar al total esperado
					$diferencia = $row->value - $sumaItem;

					if ($row->value > $sumaItem) {
						$precioItem =  $precioItem - $diferencia;
					}

					if ($row->value < $sumaItem) {
						$precioItem =  $precioItem + $diferencia;
					}
				}

				$this->pdf->Ln(0);
				$this->pdf->SetFont('Arial', '', 9);
				$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
				$this->pdf->Cell(30, 9, $item['cantidad'], 1, 0, 'C', 0);
				$this->pdf->Cell(115, 9, $item['descripcion'], 1, 0, 'C', 0);




				$this->pdf->Cell(17, 9, number_format($precioItem  / number_format($item['cantidad']), 2), 1, 0, 'C', 0);
				$this->pdf->Cell(20, 9, number_format($precioItem, 2), 1, 0, 'C', 0);

				$this->pdf->Ln(10);
				$this->pdf->SetFont('Arial', 'B', 11);
				$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
				$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			}

			$TAX = $row->value * 1.50 / 100;
			$SHIPPING = $row->value * 0.10;
			$this->pdf->Cell(115, 9, 'Insurance', 0, 0, 'R', 0);
			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(17, 9, '', 10, 0, 'C', 0);
			$this->pdf->Cell(20, 9, number_format($TAX, 2), 0, 0, 'C', 0);
			$this->pdf->Ln(10);
			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			$this->pdf->SetFont('Arial', 'B', 11);
			$this->pdf->Cell(115, 9, 'SHIPPING', 0, 0, 'R', 0);
			$this->pdf->SetFont('Arial', '', 9);
			$this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);
			$this->pdf->Cell(20, 9, number_format($SHIPPING, 2), 0, 0, 'C', 0);
			$this->pdf->Ln(10);



			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			$this->pdf->SetFont('Arial', 'B', 11);

			$this->pdf->Cell(115, 9, 'TOTAL USD', 0, 0, 'R', 0);
			$this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);

			$this->pdf->SetFont('Arial', '', 9);
			$totalGeneral = $row->value + $TAX + $SHIPPING;
			$this->pdf->Cell(20, 9, number_format(($totalGeneral), 2), 0, 0, 'C', 0);

			//fin detalle de datos
		}


		$destino      = getcwd() . "/document/Manifiesto/";
		$desabsoluto  =  "document/Manifiesto/";
		if (!is_dir($destino)) {
			mkdir($destino, 0777, true);
		}
		$nombre_archivo =  'Manifiesto' . date("d-m-Y") . '_' . $id . '.pdf';
		$this->pdf->Output("F", $destino . $nombre_archivo, true);
		$this->pdf->close();
		$datosretorno  = array(
			'destino'        => $desabsoluto,
			'nombre_archivo' => $nombre_archivo,

		);
		// header('Content-type: appliation/json');
		echo json_encode($datosretorno);
	}

	public function aplicar_estatus($guia)
	{

		//esto funciona solo con guias alternas
		//	ini_set('display_errors', 1);
		//	ini_set('display_startup_errors', 1);
		//		error_reporting(E_ALL);

		$rsl = $this->WarehouseModel->get_tracking($guia);


		$empresa = $this->WarehouseModel->obtener_token($rsl->id_empresa);


		



		$token_cliente = $empresa->token_cliente;
		$id_tokencliente = $empresa->id_tokencliente;
		$token_xpress = $empresa->token;




		if ($guia == "undefined") {
		} else {


			//   CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/MLSV000003302RG',
			$curl = curl_init();

			curl_setopt_array($curl, array(
				//CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/' . $guia,
				CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/' . $guia,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: ' . $token_xpress
				),
			));
			// 'Authorization: d3a22ca0cf04668a450a337a31ede0d9eeac64e3' //token anterior PRUEGCV(GLOBAL CARGO)
			//'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9' //token actual 
			$response = curl_exec($curl);

			curl_close($curl);

			$data = json_decode($response);
			//  $data = json_decode($response, true);
			//var_dump( $data);
			$this->datos['lista'] = $data;
			// foreach ($this->datos['lista'] as $row) {
			//     if ($row->codigo == 15) {
			//         var_dump($row->pod);
			//     }
			// }


			$query =     $this->WhModel->guia_alternativa($guia);

			foreach ($query as $fila) {
				$warehouse = $fila->tracking_number;
			}

			if ($warehouse) {
				$guia = $warehouse;
			} else {
				$warehouse = $guia;
			}


			foreach ($this->datos['lista'] as $row) {
				$result =  $this->WarehouseModel->buscar_estatus($row->codigo, $warehouse);
				// echo $warehouse ."  here";
				//   var_dump($result);
				if ($result) {
					echo 'uno';
				} else {

					date_default_timezone_set("America/El_Salvador");
					$date = date('Y-m-d H:i:s');
					$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $row->fecha);
					$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

					$item = "";
					$fecha = "";
					// $warehouse = "";
					$id_estatus = "";
					$casillero = "";
					$id_pais =  "";

					/* aqui prueba */

					//$datos = array(
					//'json_string' => $data
					// );
					//   $this->Manifiesto_model->guardar_json($datos);

					/* fin aui prueba */

					$item = 1;
					$fecha =  $row->fecha;
					$warehouse =  $guia;
					$id_estatus = $row->codigo;
					$nombre_estatus = $row->estatus;
					$casillero = "100";
					$id_pais =  1;
					$pod_nombre = "";
					$pod_valor = "";
					$document_name = "";
					$ruta = "";

					//    $this->response($row);
					if ($id_estatus == 15) {
						$ruta = "https://stargates.site/documentos_ma/";
						// foreach ( $row['pod'] as $key => $detalle) {
						foreach ($row->pod as  $detalle) {
							$pod_nombre = $detalle->nombre;
							$pod_valor  = $detalle->valor;
							$img = str_replace('data:image/png;base64,', '', $pod_valor);
							$img = str_replace('data:image/png;base64,', '', $img);
							$img = str_replace(' ', '+', $img);
							$data1 = base64_decode($img);
							$document_name = uniqid() . '.png';
							//  $file = "/home/km96wogi8pjb/public_html/documentos_ma/". uniqid() . '.jpg';
							$file = "/home/km96wogi8pjb/public_html/documentos_ma/" . $document_name;

							$success = file_put_contents($file, $data1);
							//    echo "bytes fueron escritos en $file";

						}
					}
					//   $this->response($row);

					//	echo  $pod_nombre;
					if ($id_estatus == 15) {
						$ruta = $ruta . $document_name;
					} else {
						$ruta = null;
					}

					$datos = array(

						'item' => $item,

						'fecha' => $row->fecha,

						'warehouse' => $warehouse,

						'id_estatus' => $id_estatus,

						'casillero' =>  $casillero,

						'id_pais' =>  $id_pais,

						'pod_nombre' =>  $pod_nombre,

						'pod_valor' => "",

						'json_string' => '{    "guia": ""}', //$data, data trae el json cpmpleto, utilizarlo para testing

						'ruta_foto' => $ruta,

						'fechaiso' => $fecha_iso

					);


					$this->WarehouseModel->guardar_estatus($datos);

					//$token_ma = $this->token_ma(1,2); //obtiene el token
					$token_ma = $this->token->getToken($token_cliente, $id_tokencliente);


					


					$header = array(
						'Host: tracking.mailamericas.com',
						'Content-Type: application/json',
						'Cache-Control: no-cache',
						'Authorization: Bearer ' . $token_ma
					);

					$data = array(
						"tracking" => $warehouse,
						"date" =>  $fecha_iso,
						"description" => $nombre_estatus,
						"city" => "CCO",
						"received_by" => null,
						"relationship" => null,
						"delivery_proof" => [
							$ruta . $document_name
						],

						"gcs" => "",
						"details" => ""
					);

					
					//$url = "https://tracking.mlasalvador.com/api/v1/providers/events";
					$url = "https://tracking.mailamericas.com/api/v1/providers/events";

					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
					curl_setopt($ch, CURLOPT_AUTOREFERER, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_VERBOSE, 1);
					$resultStr = curl_exec($ch);

					if (curl_errno($ch)) {
						$error_msg = curl_error($ch);
					}
					curl_close($ch);
					//  echo  $error_msg;
					//   echo $resultStr;

					$data = array(
						"respuesta" => $resultStr,
						"tracking_number" =>  $warehouse,
						"descripcion" => $nombre_estatus, // m
						"fecha" => $row->fecha,
						"fechaiso" =>  $fecha_iso

					);
					$this->WarehouseModel->guardar_logma($data);
				}
				//  echo $row->estatus;
			}
			//  echo $response;

		}
		$this->load->view("seguimiento/aplicar_estatus", $this->datos);
	}


	public function update_info()
	{
		//obtener registro de empresa para determinar el tipo de servicio

		$request = $this->WarehouseModel->getIdEmpresa($_POST['id-paquete']);

		$clienteId = $this->WarehouseModel->getClienteID($_POST['id-paquete']);

		$service = $_POST['tipo_servicio'];

		if ($request->id_empresa == 107 || $request->id_empresa == 103) {
			$service = "SER";
		}

		if($clienteId->client_id == 'FashionNova' || $clienteId->client_id == 'Busca Libre'){
			$service = "SER";
			$cobro_final = 0.0001;
			if($clienteId->client_id == 'FashionNova'){
				$cobro_final = 0.0000;
			}
			$this->WarehouseModel->update_cobrofinal($_POST['id-paquete'], $cobro_final);
		}




		$id            = $_POST['id-paquete'];
		$dui           = $_POST['dui'];
		$correo        = $_POST['correo'];
		$telefono      = $_POST['telefono'];
		$direccion     = $_POST['direccion'];
		$agencia       = $_POST['agencia'];
		$tipo_entrega  = $_POST['tipo_entrega'];
		$tipo_servicio = $service;

		$this->WarehouseModel->update_info($id, $dui, $correo, $telefono, $direccion, $agencia, $tipo_entrega,  $tipo_servicio);

		// Código PHP (fuera de CodeIgniter)
		$datos_form = array(
			'id_paquete'    => $_POST['id-paquete'],
			'dui'           => $_POST['dui'],
			'correo'        => $_POST['correo'],
			'telefono'      => $_POST['telefono'],
			'direccion'     => $_POST['direccion'],
			'agencia'       => $_POST['agencia'],
			'tipo_entrega'  => $_POST['tipo_entrega'],
			'tipo_servicio' => $service
		);

		// CONVERSIÓN FINAL a JSON
		$json_data = json_encode($datos_form, JSON_PRETTY_PRINT);

		$data = [
			"id_solicitante" => $_SESSION["user_id"],
			"fecha" => date('Y-m-d h:i:s'),
			"solicitud" => $id . "-update",
			"respuesta" => json_encode($json_data),
			"pagina" => "update-tracking"
		];

		$this->WhModel->insert_log($data);
	}

	public function verificar_lm($id)
	{
		// $result =  $this->WhModel->verificar_lm($id); 

		//agrega departamento y municipio a la guia


		ini_set('display_startup_errors', 1);
		ini_set('display_errors', 1);
		error_reporting(-1);

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://app.c807.com/guia.php/catalogo/departamento',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: ' . $_SESSION['token']
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$respuesta = json_decode($response);
		// echo $response;

		foreach ($respuesta as $row) {
			//$row->nombre;
			$depto = $row->id;
			$name_d = $row->nombre;
			$code = $row->codigo;

			$curlm = curl_init();

			curl_setopt_array($curlm, array(
				CURLOPT_URL => 'https://app.c807.com/guia.php/api/municipio?departamento=' . $row->id,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: ' . $_SESSION['token']

				),
			));


			$response1 = curl_exec($curlm);

			curl_close($curlm);
			$respuesta1 = json_decode($response1);
			echo $response;


			foreach ($respuesta1 as $fila) {
				$munic = $fila->id;
				$name_m = $fila->nombre;

				// echo $depto." " .   $munic . " ".$fila->nombre;


				// echo $row->nombre;
				$cadena = $fila->nombre;
				$opc1 = $cadena;
				$opc2 = mb_strtoupper($cadena, 'UTF-8');
				$opc3 = strtolower($cadena);
				$opc4 = strtoupper($cadena);
				$string = $cadena;
				$opc5 = $this->eliminar_acentos($string);
				$opc6 = strtoLower($this->eliminar_acentos($string));



				$result =  $this->WhModel->update_lm($id, $depto, $munic, $opc1,  $opc2,  $opc3,  $opc4,  $opc5,  $opc6, $name_d, $name_m, $code);
			}
		}
	}

	function cargar_referencia()
	{

		$this->datos['lista'] =  $this->WhModel->cargar_referencia();
		// var_dump($rsl);
		foreach ($this->datos['lista'] as $fila) {
			//echo $fila->AWB;
			$this->WhModel->update_ref($fila->awb, $fila->tracking_number, $fila->referencia,  $fila->id_manifiesto);
		}

		echo "Finish";
	}



	function eliminar_acentos($cadena)
	{

		//Reemplazamos la A y a
		$cadena = str_replace(
			array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
			array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
			$cadena
		);

		//Reemplazamos la E y e
		$cadena = str_replace(
			array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
			array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
			$cadena
		);

		//Reemplazamos la I y i
		$cadena = str_replace(
			array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
			array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
			$cadena
		);

		//Reemplazamos la O y o
		$cadena = str_replace(
			array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
			array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
			$cadena
		);

		//Reemplazamos la U y u
		$cadena = str_replace(
			array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
			array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
			$cadena
		);

		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
			array('Ñ', 'ñ', 'Ç', 'ç'),
			array('N', 'n', 'C', 'c'),
			$cadena
		);

		return $cadena;
	}

	public function update_pod()
	{
		$result = $this->WhModel->obtener_estatus15();

		foreach ($result as $row) {
			$rsl = $this->WhModel->update_pod($row->warehouse);
		}
	}

	public function update_bag()
	{
		$result = $this->WhModel->obtener_bags();

		foreach ($result as $row) {
			$this->WhModel->update_bag($row->COL2, $row->COL3);
		}
	}

	public function reporte_manifiesto($idpreclasificacion)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$this->datos['lista'] = $this->WhModel->reporte_guias($idpreclasificacion);

		$preclasificacion = $this->WhModel->datos_preclasificacion($idpreclasificacion);

		$empresas = [112, 113, 114];
		$flag_temu = 0;

		if (in_array($preclasificacion->client_id, $empresas)) {
			$flag_temu = 1;
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$idpreclasificacion",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
				)
			));

			$response = curl_exec($curl);
			curl_close($curl);

			$result = json_decode($response);

			if (isset($result->data->details)) {
				$this->datos['lista']  = $result->data->details;
				$this->datos['master_cn'] = $preclasificacion->manifiesto_tempo;
				//$this->datos['client_id'] = $preclasificacion->client_id;
			} else {
				$this->datos['lista'] = [];
			}
		}

		$this->datos['flag_temu'] = $flag_temu;


		$this->load->view("warehouse/vista_manifiesto", $this->datos);
	}

	public function reporte_guias($idpreclasificacion)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);



		$id            =     0;
		$Manifiesto    =    'Reporte de Guias';
		$time          =    time();
		$dia           =    date("d");
		$mes           =    date("m");
		$fecha         =    date("d-m-Y ");
		$periodo       =    date("Y");
		$mora          =    0;
		setlocale(LC_ALL, 'es_ES');
		$monthNum      =    $mes;
		$dateObj       =    DateTime::createFromFormat('!m', $monthNum);
		// $monthName     =    $dateObj->format('F'); 



		$GLOBALS['fecha'] = "Reporte de Manifiesto ";
		$pdf = new PDF('L', 'mm', 'A4');
		$pdf->AddPage();
		$pdf->AliasNbPages();
		$pdf->SetFont('Arial', '', 11);
		$datos         =    $this->WhModel->reporte_guias($idpreclasificacion);

		$pdf->reportemanifiesto($datos);

		$destino = getcwd() . "/document/reporte_ventas/";

		if (!is_dir($destino)) {
			mkdir($destino, 0777, true);
		}

		$archivo = 'ReporteGuias' . '.pdf';
		$pdf->Output("F", $destino . $archivo, true);
		$pdf->close();
		echo  $archivo;
	}

	public function reload_guia()
	{

		$request =  $this->WhModel->reload_guia();
		foreach ($request as $rajo) {

			//   CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/MLSV000003302RG',
			$curl = curl_init();
			$guia = $rajo->guia;
			// echo $guia;
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/' . $guia,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9'


				),
			));


			//'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9' token cuenta_actual
			$response = curl_exec($curl);

			curl_close($curl);

			$data = json_decode($response);
			//  $data = json_decode($response, true);

			$this->datos['lista'] = $data;

			$error = $data->errores;
			//  echo $error[0]->codigo;
			//  var_dump($error);
			if ($error[0]->codigo == 20) {
				//  echo $guia .'<br>';
				$this->WhModel->bandera($guia, 2);
			} else {




				foreach ($this->datos['lista'] as $row) {
					$result =  $this->WarehouseModel->buscar_estatus($row->codigo, $guia);
					if ($result) {
					} else {



						date_default_timezone_set("America/El_Salvador");
						$date = date('Y-m-d H:i:s');
						$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $row->fecha);
						$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

						$item = "";
						$fecha = "";
						$warehouse = "";
						$id_estatus = "";
						$casillero = "";
						$id_pais =  "";

						/* aqui prueba */

						//$datos = array(
						//'json_string' => $data
						// );
						//   $this->Manifiesto_model->guardar_json($datos);

						/* fin aui prueba */

						$item = 1;
						$fecha =  $row->fecha;
						$warehouse =  $guia;
						$id_estatus = $row->codigo;
						$nombre_estatus = $row->estatus;
						$casillero = "100";
						$id_pais =  1;
						$pod_nombre = "";
						$pod_valor = "";
						$document_name = "";
						$ruta = "";

						//    $this->response($row);
						if ($id_estatus == 15) {
							$ruta = "https://stargates.site/documentos_ma/";
							// foreach ( $row['pod'] as $key => $detalle) {
							foreach ($row->pod as  $detalle) {
								$pod_nombre = $detalle->nombre;
								$pod_valor  = $detalle->valor;
								$img = str_replace('data:image/png;base64,', '', $pod_valor);
								$img = str_replace('data:image/png;base64,', '', $img);
								$img = str_replace(' ', '+', $img);
								$data1 = base64_decode($img);
								$document_name = uniqid() . '.png';
								//  $file = "/home/km96wogi8pjb/public_html/documentos_ma/". uniqid() . '.jpg';
								$file = "/home/km96wogi8pjb/public_html/documentos_ma/" . $document_name;

								$success = file_put_contents($file, $data1);
								//    echo "bytes fueron escritos en $file";

							}
						}
						//   $this->response($row);

						echo  $pod_nombre;
						if ($id_estatus == 15) {
							$ruta = $ruta . $document_name;
						} else {
							$ruta = null;
						}

						$datos = array(

							'item' => $item,

							'fecha' => date('Y-m-d H:i:s'),

							'warehouse' => $warehouse,

							'id_estatus' => $id_estatus,

							'casillero' =>  $casillero,

							'id_pais' =>  $id_pais,

							'pod_nombre' =>  $pod_nombre,

							'pod_valor' => "",

							'json_string' => '{    "guia": ""}', //$data, data trae el json cpmpleto, utilizarlo para testing

							'ruta_foto' => $ruta,

							'fechaiso' => $fecha_iso

						);


						$this->WarehouseModel->guardar_estatus($datos);

						//	$token_ma = $this->token_ma(1, 2); //obtiene el token
						$token_ma = $this->token->getToken(1, 2);


						$header = array(
							'Host: tracking.mailamericas.com',
							'Content-Type: application/json',
							'Cache-Control: no-cache',
							'Authorization: Bearer ' . $token_ma
						);

						$data = array(
							"tracking" => $warehouse,
							"date" =>  $fecha_iso,
							"description" => $nombre_estatus,
							"city" => "CCO",
							"received_by" => null,
							"relationship" => null,
							"delivery_proof" => [
								$ruta . $document_name
							],

							"gcs" => "",
							"details" => ""
						);

						$url = "https://tracking.mlasalvador.com/api/v1/providers/events";

						$ch = curl_init($url);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
						curl_setopt($ch, CURLOPT_AUTOREFERER, true);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
						curl_setopt($ch, CURLOPT_VERBOSE, 1);
						$resultStr = curl_exec($ch);

						if (curl_errno($ch)) {
							$error_msg = curl_error($ch);
						}
						curl_close($ch);
						//  echo  $error_msg;
						//   echo $resultStr;

						$data = array(
							"respuesta" => $resultStr,
							"tracking_number" =>  $warehouse,
							"descripcion" => $nombre_estatus, // m
							"fecha" =>  $fecha,
							"fechaiso" =>  $fecha_iso

						);
						$this->WarehouseModel->guardar_logma($data);
						$this->WhModel->bandera($guia, 1);
					}
					//  echo $row->estatus;
				}
				//  echo $response;
			}
		}
	}

	public function mario()
	{
		$request =  $this->WhModel->reload_guia_mario();
		foreach ($request as $rajo) {

			//   CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/MLSV000003302RG',
			$curl = curl_init();
			$guia = $rajo->guia;
			// echo $guia;
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/' . $guia,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9'


				),
			));


			//'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9' token cuenta_actual
			$response = curl_exec($curl);

			curl_close($curl);

			$data = json_decode($response);
			//  $data = json_decode($response, true);

			$this->datos['lista'] = $data;

			$error = $data->errores;
			//  echo $error[0]->codigo;
			//  var_dump($error);
			echo $guia . '<br>';
			if ($error[0]->codigo == 20) {
				//  echo $guia .'<br>';
				$this->WhModel->bandera_mario($guia, 2);
			} else {




				foreach ($this->datos['lista'] as $row) {
					$result =  $this->WarehouseModel->buscar_estatus($row->codigo, $guia);
					if ($result) {
					} else {



						date_default_timezone_set("America/El_Salvador");
						$date = date('Y-m-d H:i:s');
						$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $row->fecha);
						$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

						$item = "";
						$fecha = "";
						$warehouse = "";
						$id_estatus = "";
						$casillero = "";
						$id_pais =  "";

						/* aqui prueba */

						//$datos = array(
						//'json_string' => $data
						// );
						//   $this->Manifiesto_model->guardar_json($datos);

						/* fin aui prueba */

						$item = 1;
						$fecha =  $row->fecha;
						$warehouse =  $guia;
						$id_estatus = $row->codigo;
						$nombre_estatus = $row->estatus;
						$casillero = "100";
						$id_pais =  1;
						$pod_nombre = "";
						$pod_valor = "";
						$document_name = "";
						$ruta = "";

						//    $this->response($row);
						if ($id_estatus == 15) {
							$ruta = "https://stargates.site/documentos_ma/";
							// foreach ( $row['pod'] as $key => $detalle) {
							foreach ($row->pod as  $detalle) {
								$pod_nombre = $detalle->nombre;
								$pod_valor  = $detalle->valor;
								$img = str_replace('data:image/png;base64,', '', $pod_valor);
								$img = str_replace('data:image/png;base64,', '', $img);
								$img = str_replace(' ', '+', $img);
								$data1 = base64_decode($img);
								$document_name = uniqid() . '.png';
								//  $file = "/home/km96wogi8pjb/public_html/documentos_ma/". uniqid() . '.jpg';
								$file = "/home/km96wogi8pjb/public_html/documentos_ma/" . $document_name;

								$success = file_put_contents($file, $data1);
								//    echo "bytes fueron escritos en $file";

							}
						}
						//   $this->response($row);

						echo  $pod_nombre;
						if ($id_estatus == 15) {
							$ruta = $ruta . $document_name;
						} else {
							$ruta = null;
						}

						$datos = array(

							'item' => $item,

							'fecha' => date('Y-m-d H:i:s'),

							'warehouse' => $warehouse,

							'id_estatus' => $id_estatus,

							'casillero' =>  $casillero,

							'id_pais' =>  $id_pais,

							'pod_nombre' =>  $pod_nombre,

							'pod_valor' => "",

							'json_string' => '{    "guia": ""}', //$data, data trae el json cpmpleto, utilizarlo para testing

							'ruta_foto' => $ruta,

							'fechaiso' => $fecha_iso

						);


						$this->WarehouseModel->guardar_estatus($datos);

						//$token_ma = $this->token_ma(1, 2); //obtiene el token
						$token_ma = $this->token->getToken(1, 2);


						$header = array(
							'Host: tracking.mailamericas.com',
							'Content-Type: application/json',
							'Cache-Control: no-cache',
							'Authorization: Bearer ' . $token_ma
						);

						$data = array(
							"tracking" => $warehouse,
							"date" =>  $fecha_iso,
							"description" => $nombre_estatus,
							"city" => "CCO",
							"received_by" => null,
							"relationship" => null,
							"delivery_proof" => [
								$ruta . $document_name
							],

							"gcs" => "",
							"details" => ""
						);

						$url = "https://tracking.mlasalvador.com/api/v1/providers/events";

						$ch = curl_init($url);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
						curl_setopt($ch, CURLOPT_AUTOREFERER, true);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
						curl_setopt($ch, CURLOPT_VERBOSE, 1);
						$resultStr = curl_exec($ch);

						if (curl_errno($ch)) {
							$error_msg = curl_error($ch);
						}
						curl_close($ch);
						//  echo  $error_msg;
						//   echo $resultStr;

						$data = array(
							"respuesta" => $resultStr,
							"tracking_number" =>  $warehouse,
							"descripcion" => $nombre_estatus, // m
							"fecha" =>  $fecha,
							"fechaiso" =>  $fecha_iso

						);
						$this->WarehouseModel->guardar_logma($data);
						$this->WhModel->bandera_mario($guia, 1);
					}
					//  echo $row->estatus;
				}
				//  echo $response;
			}
		}
	}

	public function validar_estatus()
	{
		//valida los estatus y agregar sub estados a tabla paquete 01
		$rsl = $this->WhModel->validar_estatus();

		foreach ($rsl as $fila) {
			$tracking = $fila->guia;
			//   echo $tracking;

			//    $tracking='RP124764663MU';       
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/' . $tracking,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: 91bbb9fac887f6d4723dd19842feb4f50c7dc5c9',
					'Cookie: ci_session=j4to59pq6egft61ankpm43q1otk18l9k; refresh_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE3NDUwMDg4MjYsImF1ZCI6ImEzZTU2MWMyYWM1ZTkzOTA5YTFjMTExZGFhN2Q0MmU3OGYzOWU2MzYiLCJkYXRhIjp7InB1aWQiOiIxMzE0MyIsIm5vbWJyZSI6IkNPUlJFTEFUSVZPUyBQUk9QSU9TIFhQIiwiY29ycmVvIjoidmVudGFzNUBjODA3eHByZXNzLmNvbS4uIiwic2VkZSI6IjEyNzMyIiwibm93IjoiMjAyNDA0MjMxNDQwMjYifX0.qOVlW2BrSHnqC5XOXl3WHRa0RJeW0ZvPcFO2aVyTy08'
				),
			));

			$response = curl_exec($curl);

			curl_close($curl);

			$data = json_decode($response);
			//  var_dump($data);

			$this->datos['lista'] = $data;

			$error = $data->errores;

			if ($error[0]->codigo == 20) {
				// echo $error[0]->mensaje;
				$mensaje = $error[0]->mensaje;
				$fecha = "";
				$r = $this->WhModel->buscar_estado($tracking);
				$fecha = $r[0]['fecha'];
				$mensaje = $r[0]['id_estatus'];
				//    echo "hereeee".$fecha;
				//    var_dump($r);
				// echo $r->warehouse;
				$this->WhModel->guardar_estado($tracking, $mensaje, $fecha);
			} else {

				$data = json_decode($response);
				foreach ($data as $row) {
					//  echo $data[0]->estatus;
					$mensaje = $row->estatus . ": " . $row->observaciones;
					$fecha = $row->fecha;
					$this->WhModel->guardar_estado($tracking, $mensaje, $fecha);
				}
			}

			// echo $response->errorres->codigo;

		}
	}


	public function estatus_referencias($manifiesto)
	{
		$this->datos['lista'] = $this->WhModel->estatus_referencias($manifiesto);
		$this->datos['estatus'] = $this->Conf_model->estatus();
		//  var_dump( $this->datos['lista']);
		$this->load->view("referencia/lista_manifiesto", $this->datos);
	}

	public function aplicar_estatus_referencia($id, $manifiesto, $referencia)
	{
		$_SESSION['manifest_number'] = $manifiesto . ": " . $referencia;
	}


	public function guardar_estatus_referencias($id, $manifiesto, $referencia, $id_estaus, $monmbre_estatus, $fecha_especifica=0)
	{
		//echo urldecode($monmbre_estatus);
		//  exit;

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		
		

		if($_SESSION["user_id"] != 39109){
			//die();
		}


		if ($_SESSION["user_id"] == 39109 && $fecha_especifica != 0) {

			

			$data = array(
				'tarea'      => "Aplicacion de estatus con hora especifica",
				'id_usuario' => $_SESSION["user_id"]
			);

			$this->db->insert('log_eventos', $data);
		} else {
			

			$data = array(
				'tarea'      => "Aplicacion de estatus con hora actual",
				'id_usuario' => $_SESSION["user_id"]
			);

			$this->db->insert('log_eventos', $data);
		}



		

		//	$empresa =  $this->WhModel->datos_empresa($_SESSION['id_empresa']); // obtiene las crendeciales de la api del cliente

		//	$token_cliente = $empresa->token_cliente;
		//	$id_tokencliente = $empresa->id_tokencliente;
		//  echo $id. " ". $manifiesto. " ". $referencia. " ". $id_estaus. " " . $monmbre_estatus ;


		$token_cliente = null;
		$id_tokencliente = null;

		if ($referencia == "general") {
			$request = $this->WhModel->aplicar_estatus_master($id);
		} else {
			$request = $this->WhModel->aplicar_estatus_referencia($id, $manifiesto, $referencia);
		}

		$emp = null;
		//Verifica el id de empresa del manifiesto
		foreach ($request as $fila) {
			$emp = $fila->id_empresa;
			break;
		}


		$empresa =  $this->WhModel->datos_empresa($emp); // obtiene las crendeciales de la api del cliente

		//var_dump($empresa);
		$token_cliente = $empresa->token_cliente;
		$id_tokencliente = $empresa->id_tokencliente;

		$api = $empresa->api; //verifica si cliente tiene configurada apis


		foreach ($request as $row) {


			$tracking = $row->tracking_number;
			//****************************************************
			

			if($_SESSION["user_id"] == 39109 && $fecha_especifica != 0){

				$fecha_to_set = str_replace("T", " ", $fecha_especifica);

				$fecha_to_set = $fecha_to_set.":00";

				
				$date = date($fecha_to_set);
				$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
				$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

				


			}else{
				$date = date('Y-m-d H:i:s');
				$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
				$fecha_iso = $fecha_tmp->format(DateTime::ATOM);


				
			}

			$data = array(
				'item' => 1,
				'fecha' => $date, //date('Y-m-d H:i:s'),
				'warehouse' => $tracking,
				'id_estatus' => $id_estaus,
				'casillero' => 100,
				'retirado_por' => "NA",
				'id_pais' => $_SESSION['pais'],
				'fechaiso' => $fecha_iso
			);

			$date = "Date: " .  $date; //date('Y-m-d H:i:s');


			$wh = $tracking;

			//var_dump($data);
			//die();



			//$token_ma = $this->token_ma($token_cliente, $id_tokencliente); //obtiene el token
			if ($api == "S") {
				if ($emp == 100 || $emp == 108 || $emp == 109 || $emp == 101 || $emp == 102) {
					//Mail Americas	

					/*insert */
					$this->PreclasificacionModel->guardar_detalle($data);
					$this->PreclasificacionModel->update_estatus($wh, $id_estaus);

					$token_ma = $this->token->getToken($token_cliente, $id_tokencliente);


					$header = array(
						'Host: tracking.mailamericas.com',
						'Content-Type: application/json',
						'Cache-Control: no-cache',
						'Authorization: Bearer ' . $token_ma
					);


					$data = array(
						"tracking" => $tracking,
						"date" =>  $fecha_iso,
						"description" => urldecode($monmbre_estatus),
						"city" => "CCO",
						"received_by" => "",
						"relationship" => null,
						"delivery_proof" => [
							" "
						],

						"gcs" => "",
						"details" => ""
					);


					//  var_dump($data);
					$url = "https://tracking.mailamericas.com/api/v1/providers/events";


					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
					curl_setopt($ch, CURLOPT_AUTOREFERER, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_VERBOSE, 1);

					$resultStr = curl_exec($ch);

					if (curl_errno($ch)) {
						$error_msg = curl_error($ch);
					}
					curl_close($ch);
					//  echo  $error_msg;
					//   echo $resultStr;

					if ($_SESSION["user_id"] == 39109 && $fecha_especifica != 0) {
						$fecha_to_send = $fecha_to_set;
					}else{
						$fecha_to_send = date('Y-m-d H:i:s');
					}

					$data = array(
						"respuesta" => $resultStr,
						"tracking_number" =>  $wh,
						"descripcion" =>  urldecode($monmbre_estatus),
						"fecha" => $fecha_to_send,
						//"fecha" => date('2026-05-24 18:00:00'),
						"fechaiso" =>  $fecha_iso

					);



					$this->PreclasificacionModel->guardar_logma($data);
					//****************************************************


					$tracking_number = $row->tracking_number;
					$array_ref = array(
						"id_manifiesto" => $id,
						"id_estatus" => $id_estaus,
						"tracking_number" => $tracking_number,
						"referencia" => $referencia,
					);

					
					//$this->WhModel->guardar_log_referencia($array_ref);
					//echo $row->referencia . "\n";
				} //fin MailAmericas

				//APG
				if ($emp == 107) {
					// estos son estatus por referencias o master
					$this->load->helper('estatus_helper');
					//$this->load->helper('fechas_helper'); //TODO: habiliar este helper solo para enviar fechas personalizadas (cambiar fecha en metodo fechasApg de API)

					// Datos a enviar
					$data = [
						"guia" => $wh,
						"codigo" => $id_estaus,
						"estatus" =>  urldecode($monmbre_estatus),
						"observaciones" => null,
						"fecha" => date('Y-m-d H:i:s'),
						"latitud" => null,
						"longitud" => null,
						"pod" => [] // Array vacío
					];

					//die();

					// Llamar la función y obtener la respuesta
					$response = send_api_request($data);

					// Mostrar la respuesta
					print_r($response);
				}

				//IMILE
				if ($emp == 103) {
					// estos son estatus por referencias o master
					$this->load->helper('estatus_helper');
					//$this->load->helper('fechas_helper'); //TODO: habiliar este helper solo para enviar fechas personalizadas (cambiar fecha en metodo fechasApg de API)

					// Datos a enviar
					$data = [
						"guia" => $wh,
						"codigo" => $id_estaus,
						"estatus" =>  urldecode($monmbre_estatus),
						"observaciones" => null,
						"fecha" => date('Y-m-d H:i:s'),
						"latitud" => null,
						"longitud" => null,
						"pod" => [] // Array vacío
					];

					//die();

					// Llamar la función y obtener la respuesta
					$response = imile_send_api_request($data);

					// Mostrar la respuesta
					print_r($response);
				}

				if ($emp == 112 || $emp == 113 || $emp == 114) {

					$empConfig = [
						112 => ['timezone' => 'UTC-6', 'location' => 'SV'],
						113 => ['timezone' => 'UTC-6', 'location' => 'GT'],
						114 => ['timezone' => 'UTC-6', 'location' => 'HN'],
					];

					// valores por defecto (por seguridad)
					$timezone = 'UTC-6';
					$location = 'SV';

					if (isset($empConfig[$emp])) {
						$timezone = $empConfig[$emp]['timezone'];
						$location = $empConfig[$emp]['location'];
					}

					
					$url = "https://aduana.stargateserp.com/api/tracking-event";
					$body = [
						"guia"          => $tracking,
						"codigo"        => $id_estaus,
						"estatus"       => urldecode($monmbre_estatus),
						"observaciones" => "",
						"devolucion"    => false,
						"pod"           => [],
						"timezone"      => $timezone,
						"location"      => $location,
						"id_empresa"    => $emp,
					];


					//var_dump(json_encode($body));
					//die();

					//die();


					$curl = curl_init($url);

					curl_setopt_array($curl, [
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING       => "",
						CURLOPT_MAXREDIRS      => 10,
						CURLOPT_TIMEOUT        => 30,
						CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST  => "POST",
						CURLOPT_POSTFIELDS     => json_encode($body, JSON_UNESCAPED_UNICODE),
						CURLOPT_HTTPHEADER     => [
							"Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f",
							"Content-Type: application/json",
							"Accept: application/json",
						],
					]);

					$response = curl_exec($curl);
					$err = curl_error($curl);
					$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

					curl_close($curl);

					if ($err) {
						echo "cURL Error: " . $err;
					} else {
						echo "HTTP Code: {$httpCode}\n";
						echo $response;
					}

					//die();
				}

				//echo "Finalizamos";

				//die();
			} else {
				echo "No api";
			}
		}
	}


	public function procesarProductosConCantidadesIM($cadena_productos, $cadena_cantidades, $precio_total)
	{
		// Dividir los productos por el símbolo '+'
		$productos_raw = explode('+', $cadena_productos);

		$productos = [];
		$cantidades = [];
		$cantidad_total = 0;

		foreach ($productos_raw as $producto_raw) {
			// Extraer la descripción y la cantidad usando expresiones regulares
			if (preg_match('/(.+?)\s*\*\s*(\d+)/', $producto_raw, $matches)) {
				$descripcion = trim($matches[1]);
				$cantidad = intval($matches[2]);
				$productos[] = $descripcion;
				$cantidades[] = $cantidad;
				$cantidad_total = $cadena_cantidades;
			}
		}

		// Verificar si las cantidades coinciden con la suma indicada
		if ($cantidad_total !== $cadena_cantidades) {
			//echo "La suma de las cantidades no coincide con el valor proporcionado.\n";
			var_dump($cadena_productos);
			var_dump($cadena_cantidades);
			return [];
		}

		// Calcular el precio por unidad
		$precio_unitario = $precio_total / $cantidad_total;

		$resultado = [];

		foreach ($productos as $index => $descripcion) {
			$cantidad = $cantidades[$index];
			$precio_por_producto = $precio_unitario * $cantidad; // Precio total proporcional para este producto

			$resultado[] = [
				'descripcion' => $descripcion,
				'cantidad' => $cantidad,
				'precio' => $precio_por_producto, // Precio total ya calculado
			];
		}

		return $resultado;
	}


	public function agruparProductosIM($productos)
	{
		$agrupados = [];

		foreach ($productos as $producto) {
			$nombre_base = trim($producto['descripcion']); // Usar el nombre directamente

			if (!isset($agrupados[$nombre_base])) {
				$agrupados[$nombre_base] = [
					'descripcion' => $nombre_base,
					'cantidad' => 0,
					'precio' => 0,
				];
			}

			// Sumar cantidades y precios directamente
			$agrupados[$nombre_base]['cantidad'] += $producto['cantidad'];
			$agrupados[$nombre_base]['precio'] += $producto['precio'];
		}

		return array_values($agrupados); // Reindexar el array
	}

	



	public function generar_facturas($id, $referencia)
	{

		$dlist_manifiestos        = $this->WhModel->lista_warehouse1($id, $referencia);


		$id_manifiesto = $id;

		//include getcwd()."/application/libraries/fpdf/fpdf.php";
		$Manifiesto  = 'manifiesto';
		$time        = time();
		$fecha       = date("d-m-Y (H:i:s)", $time);
		// $fecha =  date()
		$dia         = date("d");
		$mes         = date("m");
		$periodo     = date("Y");
		$mora        = 0;
		setlocale(LC_ALL, 'es_ES');
		$monthNum    = $mes;
		$dateObj     = DateTime::createFromFormat('!m', $monthNum);
		$monthName   = strftime('%B', $dateObj->getTimestamp());
		//$this->pdf   = new FPDF('L', 'mm','Legal'); 
		$this->pdf   = new FPDF();

		//$dlist_manifiestos        = $this->WhModel->lista_warehouse1($id, $referencia);

		$preclasificacion = $this->WhModel->datos_preclasificacion($id);

		$empresas = [112, 113, 114];

		$tm = 0;

		if (in_array($preclasificacion->client_id, $empresas)) {

			$busqueda = $referencia;

			$tm = 1;


			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://aduana.stargateserp.com/api/manifiesto/$id",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer 13|BrjjtzbcNDLWRBh4VHmFkk0Op0uC0iG3gkRrIfbUd75b264f'
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);

			$result = json_decode($response);
			$fecha_send = "";
			if (isset($result->data->details)) {
				$dlist_manifiestos  = $result->data->details;
				$fecha_send = $result->data->header->fecha;
				//$this->datos['client_id'] = $preclasificacion->client_id;
			} else {
				$dlist_manifiestos = [];
			}



			$encontrado = null;
			$new_array = array();

			foreach ($dlist_manifiestos as $item) {


				if ($item->referencia == $busqueda) {
					//$encontrado = $item;
					$new_array[] = $item;

					//var_dump($item);
					//break; // Detiene la búsqueda inmediatamente al encontrar el primero
				}
			}

			$dlist_manifiestos = $new_array;
		} else {
			$dlist_manifiestos        = $this->WhModel->lista_warehouse1($id, $referencia);
		}


		$customer = $dlist_manifiestos[0]->id_empresa;


		$address = "";
		$package = "";

		/*$new_array = array();
		foreach($dlist_manifiestos as $row){
			$resultado = array_filter($dlist_manifiestos, function ($item) use ($busqueda) {

				//echo $item->referencia . "\n";

				return $item->referencia === $busqueda;
			});

			// array_filter devuelve un array, si quieres solo el primer objeto:
			$row = reset($resultado);

			if ($resultado) {
				//echo $busqueda."\n";
				//var_dump($resultado);
				$new_array[] = $row;
			} else {
				echo "referencia no encontrada";
				
			}
		}*/




		//var_dump($dlist_manifiestos);		

		//die();



		foreach ($dlist_manifiestos  as  $row) {
			$this->pdf->setY(0);
			$this->pdf->setX(0);
			//$SHIPPING = $row->value * 0.15;
			$SHIPPING = 0;
			$TAX      = 0;


			$this->pdf->AddPage();
			$this->pdf->AliasNbPages();
			$this->pdf->SetLeftMargin(10);
			$this->pdf->SetRightMargin(10);
			$this->pdf->SetFillColor(200, 200, 200);
			// $this->pdf->SetFont('Arial', 'B', 7);


			$this->pdf->SetFillColor(255, 255, 255);
			$this->pdf->SetTextColor(46, 44, 44);
			$this->pdf->SetFont('Arial', '', 11);
			$this->pdf->Ln(5);


			$this->pdf->Cell(15, 12, '', 0, 0, 'R', 1);

			//$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/MailAmerica.jpg', 45, 16, 50, 30), 0, 0, 'L', 0); 
			//  $this->pdf->Cell(40, 16, $this->pdf->Image(getcwd() . '/public/img/logo_2.png', 17, 16, 30, 16), 0, 0, 'L', 0);
			$package = $row->tracking_number;
			if ($customer == 107) {
				$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/fafalux.png'), 0, 0, 'L', 0);
			}

			if ($customer == 108 || $customer == 100 || $customer == 102) {
				if ($row->client_id == "Cainiao- AliExpress") {
					$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/aliexpress.png', 10, 16, 85, 30), 0, 0, 'L', 0);
					$address = "No. 111 Tianlin Road, Tower, Shangai, China.";
				}
				if ($row->client_id == "AliExpress") {
					$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/aliexpress.png', 10, 16, 85, 30), 0, 0, 'L', 0);
					$address = "No. 111 Tianlin Road, Tower, Shangai, China.";
				}
			}

			if ($customer == 109 || $customer == 100 || $customer == 102) {

				if ($row->client_id == "SHEIN") {
					$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/shein.png', 10, 16, 85, 30), 0, 0, 'L', 0);
				}
				$address = "No. 111 Tianlin Road, Tower, Shangai, China.";
			}

			if ($customer == 103) {
				$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/temu.png', 10, 16, 85, 30), 0, 0, 'L', 0);
				$address = "No. 111 Tianlin Road, Tower, Shangai, China.";
			}

			if ($customer == 112 || $customer == 113 || $customer == 114) {
				$this->pdf->Cell(50, 24, $this->pdf->Image(getcwd() . '/public/img/temu.png', 10, 16, 50, 30), 0, 0, 'L', 0);
				$address = "28th Floor, No. 533 Loushanguan Road, Changning District, Shanghai, 200051, China.";
				$package = $row->package_number;
			}


			$this->pdf->Ln(30);



			$this->pdf->Cell(200, 12, $address, 0, 0, 'L', 0);
			$this->pdf->Ln(10);
			$this->pdf->Cell(200, 12, 'Shangai ', 0, 0, 'L', 0);

			$this->pdf->Ln(15);
			$this->pdf->Cell(200, 12, 'ENVIADO A: ' . $row->consignee, 0, 0, 'L', 0);
			$this->pdf->Ln(10);


			$this->pdf->MultiCell(150, 5, 'DIRECCION: ' . $row->consignee_address1, 0, 'TB', 'L', false);
			$this->pdf->Ln(10);
			$this->pdf->Cell(200, 12, 'Tel: ' . $row->consignee_phone, 0, 0, 'L', 0);
			$this->pdf->Ln(10);


			$this->pdf->Cell(200, 12, 'DUI: ' . $row->consignee_account, 0, 0, 'L', 0);
			$this->pdf->Ln(15);


			$this->pdf->SetFont('Arial', 'B', 11);

			$valory = 0;
			$valorx = 0;

			$valor_paquete = $row->value;
			if ($customer == 112 || $customer == 113 || $customer == 114) {
				$valor_paquete = $row->value / 1.115;
			}
			$valor_paquete = number_format($valor_paquete, 2, '.', '');

			$valory =  $this->pdf->getY();
			$valorx = $this->pdf->getX();
			$this->pdf->MultiCell(10, 7.5, utf8_decode(''), 0, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 10);
			$this->pdf->MultiCell(40, 12, utf8_decode('N° de factura'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 50);
			$this->pdf->MultiCell(35, 12, utf8_decode('Fecha de factura'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 85);
			$this->pdf->MultiCell(30, 12, utf8_decode('Término'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 115);
			$this->pdf->MultiCell(40, 12, utf8_decode('No de Cliente'), 1, 'TB', 'C', false);
			$this->pdf->setY($valory);
			$this->pdf->setX($valorx + 155);
			$this->pdf->MultiCell(20, 6, utf8_decode('No de Pág.'), 1, 'TB', 'C', false);
			$this->pdf->Ln(0);

			$this->pdf->SetFont('Arial', '', 9);

			$fecha_send = ($tm == 1) ? $fecha_send : $row->fecha;

			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(40, 9, $package, 1, 0, 'C', 0);
			//$this->pdf->Cell(35, 9, date("d-m-Y", strtotime($row->fecha)), 1, 0, 'C', 0);
			$this->pdf->Cell(35, 9, date("d-m-Y", strtotime($fecha_send)), 1, 0, 'C', 0);

			//$this->pdf->Cell(35, 12,  date("d-m-Y", strtotime($row->fecha)) , 1, 0, 'C', 0);
			$this->pdf->Cell(30, 9, 'FOB', 1, 0, 'C', 0);
			$this->pdf->Cell(40, 9, $row->consignee_account, 1, 0, 'C', 0);
			$this->pdf->Cell(20, 9, '1', 1, 0, 'C', 0);
			$this->pdf->Ln(10);
			$this->pdf->SetFont('Arial', 'B', 20);
			$this->pdf->Cell(50, 9, 'FACTURA', 0, 0, 'L', 0);
			$this->pdf->Ln(10);
			$this->pdf->SetFont('Arial', 'B', 11);

			$valory1 = 0;
			$valorx1 = 0;


			$valory1 =  $this->pdf->getY();
			$valorx1 = $this->pdf->getX();

			$this->pdf->MultiCell(10, 7.5, utf8_decode(''), 0, 'TB', 'C', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 10);
			$this->pdf->MultiCell(30, 12, utf8_decode('Cantidad'), 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 40);
			$this->pdf->MultiCell(115, 12, utf8_decode('Cod. Artículo Descripción '), 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 155);
			$this->pdf->MultiCell(17, 6, utf8_decode('Precio Unitario'), 1, 'TB', 'J', false);
			$this->pdf->setY($valory1);
			$this->pdf->setX($valorx1 + 172);
			$this->pdf->MultiCell(20, 6, utf8_decode('Precio Total'), 1, 'TB', 'J', false);

			$this->pdf->Ln(0);
			$this->pdf->SetFont('Arial', '', 9);
			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			//$this->pdf->Cell(30, 9, $row->items, 1, 0, 'C', 0);
			if ($customer == 112 || $customer == 113 || $customer == 114) {
				$this->pdf->Cell(30, 9, $row->pieces, 1, 0, 'C', 0);
			} else {
				$this->pdf->Cell(30, 9, 1, 1, 0, 'C', 0);
			}

			$this->pdf->Cell(115, 9, substr($row->commodity, 0, 30), 1, 0, 'C', 0);
			if ($customer == 112 || $customer == 113 || $customer == 114) {
				$this->pdf->Cell(17, 9, ' ', 1, 0, 'C', 0);
			} else {
				$this->pdf->Cell(17, 9, number_format($valor_paquete, 2), 1, 0, 'C', 0);
			}

			//$this->pdf->Cell(17, 9, $valor_paquete, 1, 0, 'C', 0);
			$this->pdf->Cell(20, 9, number_format($valor_paquete, 2), 1, 0, 'C', 0);

			$this->pdf->Ln(10);
			$this->pdf->SetFont('Arial', 'B', 11);
			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			$this->pdf->Cell(115, 9, 'TAX', 0, 0, 'R', 0);
			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(17, 9, '', 10, 0, 'C', 0);
			$this->pdf->Cell(20, 9, number_format($TAX, 2), 0, 0, 'C', 0);
			$this->pdf->Ln(10);
			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			$this->pdf->SetFont('Arial', 'B', 11);
			$this->pdf->Cell(115, 9, 'SHIPPING', 0, 0, 'R', 0);
			$this->pdf->SetFont('Arial', '', 9);
			$this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);
			$this->pdf->Cell(20, 9, number_format($SHIPPING, 2), 0, 0, 'C', 0);
			$this->pdf->Ln(10);



			$this->pdf->Cell(10, 12, '', 0, 0, 'C', 0);
			$this->pdf->Cell(30, 9, '', 0, 0, 'C', 0);
			$this->pdf->SetFont('Arial', 'B', 11);

			$this->pdf->Cell(115, 9, 'TOTAL USD', 0, 0, 'R', 0);
			$this->pdf->Cell(17, 9, '', 0, 0, 'C', 0);
			$this->pdf->SetFont('Arial', '', 9);

			$this->pdf->Cell(20, 9, number_format(($TAX + $SHIPPING + $valor_paquete), 2), 0, 0, 'C', 0);

			error_handler:
		}


		$destino      = getcwd() . "/document/Manifiesto/";
		$desabsoluto  =  "document/Manifiesto/";
		if (!is_dir($destino)) {
			mkdir($destino, 0777, true);
		}
		$nombre_archivo =  'Manifiesto' . date("d-m-Y") . '_' . $id . '.pdf';
		$this->pdf->Output("F", $destino . $nombre_archivo, true);
		$this->pdf->close();
		$datosretorno  = array(
			'destino'        => $desabsoluto,
			'nombre_archivo' => $nombre_archivo,

		);
		// header('Content-type: appliation/json');
		echo json_encode($datosretorno);
	}

	public function update_guia($id)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);


		$rsl = $this->WarehouseModel->obtener_guia($id);

		$numero_guia = '';
		if ($rsl->tracking_replace == null) {
			$numero_guia = $rsl->tracking_number;
		} else {
			$numero_guia = $rsl->tracking_replace;
		}

		$datos = [
			"guia" => $numero_guia,
			"telefono" => $rsl->consignee_phone,
			"correo" => $rsl->consignee_email,
			"departamento_id" => $_POST['depto'],
			"municipio_id" => $_POST['munic'],
			"direccion" => $rsl->consignee_address1 . " " . $rsl->consignee_address2 . " " . $rsl->consignee_address3


		];

		$request = $this->WarehouseModel->obtener_token($rsl->id_empresa);


		$token = $request->token;


		$this->send_info_gacela($datos, $token);
		die();





		$strfecha = date("Y-m-d") . ' 23:01:01';
		$fecha_recoleccion = $strfecha; //date("Y-m-d H:i:s"); //"2023-08-21 16:30";

		$telefono = $rsl->consignee_phone;

		$numero_guia = '';



		$datos = array(
			"recolecta_fecha" => $fecha_recoleccion,
			"recolecta_comentario" => "StarShip",
			"tipo_entrega" => $rsl->tipo_entrega,
			"guias" => array(
				array(
					"identificacion" => "100",
					"nombre"         => $rsl->consignee,
					"correo"         => $rsl->consignee_email,
					"telefono"       => $telefono,
					"direccion"      => $rsl->consignee_address1 . " " . $rsl->consignee_address2 . " " . $rsl->consignee_address3,
					"tipo_servicio"  => $rsl->tipo_servicio,

					"agencia_destino" =>  $rsl->agencia_destino,
					"con_seguro"     => "0",
					"con_liquidacion_documento" => "0",
					"departamento_id"   =>  $_POST['depto'],
					"municipio_id"      =>  $_POST['munic'],
					"detalle" => array(
						array(
							"guia"          => $numero_guia,
							"peso"          => $rsl->gweight,
							"contenido"     => $rsl->commodity,
							"unidad_medida" => "LB",
						)
					)
				)
			)
		);
	}

	public function get_recolecta($id, $tracking_number, $manifiesto)
	{

		// obtenemos todos los tracking asociados al manifiesto
		$request = $this->WarehouseModel->obtener_recolecta($manifiesto);

	//	set_time_limit(0);

		foreach ($request as $row) {

			$id = $row->id;
			$tracking_number = $row->tracking_number;

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://app.c807.com/guia.php/landing/seguimiento/' . $tracking_number,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Authorization: 1b6982b70f3d8a921162ee774cbc283282381a27'
				),
			));

			$response = curl_exec($curl);

			curl_close($curl);

			// Decodificamos el JSON
			$data = json_decode($response, true);

			if (isset($data['guia']['solicitud'])) {

				$solicitud = $data['guia']['solicitud'];

				$this->WhModel->guardar_recolecta($id, $tracking_number, $solicitud);

				echo json_encode([
					'success' => true,
					'tracking' => $tracking_number,
					'solicitud' => $solicitud
				]);
			} else {

				echo json_encode([
					'success' => false,
					'tracking' => $tracking_number,
					'message' => 'No se encontró el campo solicitud.'
				]);
			}

			// pequeña pausa para no saturar el API
		//	usleep(300000); // 0.3 segundos
		}
	}


	public function editar_descripcion($id)
	{
		$this->datos['descripcion'] = $this->WhModel->editar_descripcion($id);
		$this->load->view('warehouse/editar_descripcion', $this->datos);
	}

	public function guardar_descripcion()
	{
		$id = $this->input->post('id');
		$descripcion = $this->input->post('descripcion');

		$this->WhModel->guardar_descripcion($id, $descripcion);
	}
	private function procesarFechaManifiesto($fechaRaw)
	{
		$fecha = new DateTime($fechaRaw);
		$dia = $fecha->format('j');
		$mes_num = $fecha->format('m');
		$anio = $fecha->format('Y');

		$meses = [
			'01' => 'enero',
			'02' => 'febrero',
			'03' => 'marzo',
			'04' => 'abril',
			'05' => 'mayo',
			'06' => 'junio',
			'07' => 'julio',
			'08' => 'agosto',
			'09' => 'septiembre',
			'10' => 'octubre',
			'11' => 'noviembre',
			'12' => 'diciembre'
		];

		$mes = $meses[$mes_num];
		$fechaFormateada = "$dia de $mes de $anio";

		$hoy = new DateTime();
		$diasTranscurridos = $fecha->diff($hoy)->days;

		return [
			'fecha_formateada' => $fechaFormateada,
			'dias_transcurridos' => $diasTranscurridos
		];
	}
	public function grafica_estatus($id_manifiesto)
	{

		ini_set('display_startup_errors', 1);
		ini_set('display_errors', 1);
		error_reporting(-1);


		$rsl = $this->WhModel->get_pending_events($id_manifiesto);
		foreach ($rsl as $row) {
			//echo $row->tracking_number ."\n";
			$req = $this->WhModel->last_events($row->tracking_number);  //obtenermos todos los no entregados
			if (!empty($req->id_estatus)) {
				$this->WhModel->update_last_events($row->tracking_number, $req->id_estatus);  //actualizamos ultimo estatus en manifeisto

			}



			//	echo $req->id_estatus ."\n";

		}
		//	exit;
		$this->datos['lista'] = $this->WhModel->obtener_datos_grafica_estatus($id_manifiesto);
		//var_dump(	$this->datos['lista'] );
		$this->load->view("warehouse/grafica_estatus", $this->datos);
	}
	public function obtener_Detalles_Por_Estatus()
	{
		$id_manifiesto = (int)$this->input->get('id');
		$nombre = $this->input->get('nombre');

		if ($nombre === 'Sin movimiento') {
			$res_detalles = $this->WhModel->obtener_Detalles_Sin_Movimiento($id_manifiesto);

			$detalles = [];
			foreach ($res_detalles as $row) {
				$detalles[] = (object)[
					'tracking_number'     => $row->tracking_number,
					'nombre'              => $row->nombre,
					'fecha_formateada'    => '',
					'dias_transcurridos'  => ''
				];
			}
		} else {
			$res_detalles = $this->WhModel->obtener_Detalles_Por_Estatus($id_manifiesto, $nombre);

			$detalles = [];
			foreach ($res_detalles as $row) {
				$fechaProcesada = $this->procesarFechaManifiesto($row->fecha);
				$detalles[] = (object)[
					'tracking_number'     => $row->tracking_number,
					'nombre'              => $row->nombre,
					'fecha_formateada'    => $fechaProcesada['fecha_formateada'],
					'dias_transcurridos'  => $fechaProcesada['dias_transcurridos'] . ' Días'
				];
			}
		}

		$data['detalles'] = $detalles;
		$this->load->view('warehouse/detalle_grafica_estatus', $data);
	}

	public function exportar_excel_grafica_resumen()
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$id = $this->input->post('id_manifiesto');
		$estatus = $this->input->post('estatus');

		$res_detalles = $this->WhModel->exportar_excel_grafica_resumen($id, $estatus);

		$detalles = [];
		foreach ($res_detalles as $row) {
			if ($row->fecha) {
				$fechaProcesada = $this->procesarFechaManifiesto($row->fecha);
				$fecha_formateada = $fechaProcesada['fecha_formateada'];
				$dias_transcurridos = $fechaProcesada['dias_transcurridos'] . ' Días';
			} else {
				$fecha_formateada = '';
				$dias_transcurridos = '';
			}

			$detalles[] = (object)[
				'tracking_number'     => $row->tracking_number,
				'nombre'              => $row->nombre,
				'fecha_formateada'    => $fecha_formateada,
				'dias_transcurridos'  => $dias_transcurridos
			];
		}
		$data['detalles'] = $detalles;
		$this->load->view('warehouse/tbl_excel_resumen', $data);
	}

	public function pdf_guia_xpress($id, $tracking)
	{
		$rsl = $this->WarehouseModel->obtener_guia($id);
		$request = $this->WarehouseModel->obtener_token($rsl->id_empresa);
		$token = $request->token;
		$data = [
			"guias" => [
				$tracking
			]
		];

		$jsonData = json_encode($data);

		//echo $jsonData;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_pdf',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $jsonData,
			CURLOPT_HTTPHEADER => array(
				'Authorization: ' . $token,
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);


		$dataResponse = json_decode($response, true);

		if (isset($dataResponse['errores'])) {
			header('Content-Type: application/json');
			echo json_encode(['error' => $dataResponse['errores'][0]['mensaje']]);
			return;
		}

		if (isset($dataResponse['pdf'])) {
			$pdfData = base64_decode($dataResponse['pdf']);

			// PDF enviado directamente al navegador
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="' . $tracking . '.pdf"');
			header('Content-Length: ' . strlen($pdfData));

			echo $pdfData;
			return;
		}
		header('Content-Type: application/json');
		echo json_encode(['error' => 'Respuesta inesperada del servidor externo']);
	}

	public function cargar_documentos_manifiesto($id)
	{
		$this->load->view('awb/form_documentos_manifiesto');
	}

	public function listar_documentos_manifiesto($id)
	{
		$data['documentos'] = $this->WhModel->listar_documentos_manifiesto($id);
		$this->load->view('awb/lista_documentos_manifiesto', $data);
	}

	/*public function subir_doc_manifiesto()
	{
		$id_usuario         = $_SESSION['user_id'];
		$manifiesto         = $this->input->post('manifiesto');

		if (empty($_FILES['documentos_manifiesto']['name'][0])) {
			echo json_encode(['status' => 'error', 'msg' => 'No se recibieron archivos.']);
			return;
		}

		$destino = getcwd() . "/public/uploads/documentos_manifiesto/";

		if (!is_dir($destino)) {
			mkdir($destino, 0777, true);
		}


		$documentos     = $_FILES['documentos_manifiesto'];
		$total          = count($documentos['name']);
		$subidos        = 0;
		$correlativo = $this->WhModel->obtener_correlativo_doc_manifiesto($manifiesto);

		for ($i = 0; $i < $total; $i++) {
			$correlativo++;

			$nombre_original = $documentos['name'][$i];
			$nombre_sin_ext = pathinfo($documentos['name'][$i], PATHINFO_FILENAME);
			$correlativo_format = $correlativo;
			$tmp = $documentos['tmp_name'][$i];
			$tamanio = $documentos['size'][$i];
			$extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
			$tipo_archivo = mime_content_type($tmp);
			//$nombre_nuevo = uniqid('doc_') . '.' . $extension;
			$nombre_nuevo = "{$nombre_sin_ext} - {$manifiesto} - {$correlativo_format}.{$extension}";
			$ubicacion      = $destino . $nombre_nuevo;

			if (move_uploaded_file($tmp, $ubicacion)) {
				$url_destino = "/public/uploads/documentos_manifiesto/" . $nombre_nuevo;

				$data = [
					'usuario_id'    => $id_usuario,
					'manifiesto_id' => $manifiesto,
					'nombre_archivo' => $nombre_original,
					'nombre_nuevo'   => $nombre_nuevo,
					'url'            => $url_destino,
					'tipo_archivo'   => $tipo_archivo,
					'tamanio'        => $tamanio,
				];

				$this->WhModel->guardar_documentos_manifiesto($data);
				$subidos++;
			}
		}
		if ($subidos === 1) {
			echo json_encode(['status' => 'success', 'msg' => "El archivo se subio correctamente."]);
		} else if ($subidos > 1) {
			echo json_encode(['status' => 'success', 'msg' => "Se subieron {$subidos} archivos correctamente."]);
		} else {
			echo json_encode(['status' => 'error', 'msg' => 'Error al cargar los archivos.']);
		}
	}*/

	public function reporte_documentos($id_manifiesto)
	{

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$data['documentos'] = $this->WhModel->get_reporte_universo($id_manifiesto);
		$data['id_manifiesto'] = $id_manifiesto;

		$this->load->view('awb/reporte_documentos_view', $data);
	}

	public function subir_doc_manifiesto()
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$id_usuario         = $_SESSION['user_id'];
		$manifiesto         = $this->input->post('manifiesto');

		if (empty($_FILES['documentos_manifiesto']['name'][0])) {
			echo json_encode(['status' => 'error', 'msg' => 'No se recibieron archivos.']);
			return;
		}

		$destino = getcwd() . "/public/uploads/documentos_manifiesto/";

		if (!is_dir($destino)) {
			mkdir($destino, 0777, true);
		}


		$documentos     = $_FILES['documentos_manifiesto'];
		$total          = count($documentos['name']);
		$subidos        = 0;
		$correlativo = $this->WhModel->obtener_correlativo_doc_manifiesto($manifiesto);

		$tipos_documentos = $this->WhModel->obtener_tipos_documentos();

		$documentos_no_permitidos = array();

		$tipo_documento_seleccionado = $this->input->post('selectTipoArchivo');

		if ($tipo_documento_seleccionado == 'referencia') {
			$referencias = $this->WhModel->obtener_referencias_by_manifiesto($manifiesto);
		} else {
			$master_data = $this->WhModel->datos_preclasificacion($manifiesto);

			$object = new stdClass();

			// Dynamically add properties
			$object->referencia = $master_data->manifiesto;

			$referencias = array($object);
		}

		//var_dump($referencias);

		//echo "El tipo de archivo es: ".$tipo_documento_seleccionado;

		for ($i = 0; $i < $total; $i++) {
			$correlativo++;

			$nombre_original = $documentos['name'][$i];
			$nombre_sin_ext = pathinfo($documentos['name'][$i], PATHINFO_FILENAME);
			$correlativo_format = $correlativo;
			$tmp = $documentos['tmp_name'][$i];
			$tamanio = $documentos['size'][$i];
			$extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
			$tipo_archivo = mime_content_type($tmp);
			//$nombre_nuevo = uniqid('doc_') . '.' . $extension;
			$nombre_nuevo = "{$nombre_sin_ext} - {$manifiesto} - {$correlativo_format}.{$extension}";
			$ubicacion      = $destino . $nombre_nuevo;

			$name_to_search = explode("_", $_FILES['documentos_manifiesto']['name'][$i]);

			//var_dump($name_to_search);
			//var_dump($tipos_documentos);
			//verificamos que si dentro de la carga viene algun tipo
			//$keySearch = array_search($name_to_search[0], $tipos_documentos);
			$id_tipo = null;
			foreach ($tipos_documentos as $key) {
				if ($key->slug == strtolower($name_to_search[0])) {
					$id_tipo = $key->id;
				}
			}

			//var_dump($keySearch);
			///die();

			//echo $tipo_documento_seleccionado;

			if ($id_tipo == null) {
				$documentos_no_permitidos[] = ["status" => "error", "archivo " => $_FILES['documentos_manifiesto']['name'][$i], "razon" => "Documento no cumple una estructura permitida en el nombre"];
			} else {

				$tipo_documento_to_load = $id_tipo;


				$referenciaByFile = explode(".", $name_to_search[1]);

				$referencia_busqueda = $referenciaByFile[0];

				//print_r($referencias);

				$resultado = array_filter($referencias, function ($item) use ($referencia_busqueda) {
					return $item->referencia == $referencia_busqueda;
				});

				// Si quieres obtener el objeto directamente:
				$objetoEncontrado = !empty($resultado) ? array_values($resultado)[0] : null;



				$flag_no_load = 0;

				if ($objetoEncontrado) {
					$verifyDocument = $this->WhModel->obtener_documento_cargado($manifiesto, $referencia_busqueda, $tipo_documento_to_load);
					$countDocuments = $verifyDocument[0]->cantidad_documentos;
				} else {
					$flag_no_load = 1;
					$documentos_no_permitidos[] = ["status" => "error", "archivo " => $_FILES['documentos_manifiesto']['name'][$i], "razon" => "Referencia no encontrada en este manifiesto"];
				}

				if (move_uploaded_file($tmp, $ubicacion) && $flag_no_load == 0 && $countDocuments == 0) {
					$url_destino = "/public/uploads/documentos_manifiesto/" . $nombre_nuevo;

					$data = [
						'usuario_id'    => $id_usuario,
						'manifiesto_id' => $manifiesto,
						'nombre_archivo' => $nombre_original,
						'nombre_nuevo'   => $nombre_nuevo,
						'url'            => $url_destino,
						'tipo_archivo'   => $tipo_archivo,
						'tamanio'        => $tamanio,
						'id_tipo_documento' => $tipo_documento_to_load,
						'documento_track' => $objetoEncontrado->referencia
					];

					$this->WhModel->guardar_documentos_manifiesto($data);

					$subidos++;

					$documentos_no_permitidos[] = ["status" => "success", "archivo " => $_FILES['documentos_manifiesto']['name'][$i], "razon" => "Archivo exitosamente cargado"];
				} else {
					if ($flag_no_load == 0) {
						$documentos_no_permitidos[] = ["status" => "error", "archivo " => $_FILES['documentos_manifiesto']['name'][$i], "razon" => "Referencia y tipo de documento ya ingresado"];
					}
				}
			}
		}
		//var_dump($documentos_no_permitidos);

		//Apartado para calcular porcentaje cargado de archivos
		$archivos = $this->WhModel->obtener_archivos_para_exportar($manifiesto);
		$cantidad_cargada = count($archivos);
		//echo $cantidad_cargada."\n";
		$referencias = $this->WhModel->obtener_referencias_by_manifiesto($manifiesto);
		//echo count($referencias)."\n";

		$porcentaje_cargado = ($cantidad_cargada / ((count($referencias) * 3) + 1)) * 100; //+1 por el archivo que es unico para la master
		//echo number_format($porcentaje_cargado,2);

		$this->WhModel->modificar_porcentaje_archivos_cargados($manifiesto, $porcentaje_cargado);

		//	update encabezado manifiesto
		echo json_encode($documentos_no_permitidos);
	}

	public function downloadzip($id, $tipo_archivo = null)
	{

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$archivos = $this->WhModel->obtener_archivos_para_exportar($id);

		$preclasificacion = $this->WhModel->datos_preclasificacion($id);


		$empresas = [112, 113, 114];
		$flag_temu = 0;
		$firstNameZip = "";

		if (in_array($preclasificacion->client_id, $empresas)) {
			$firstNameZip = $preclasificacion->manifiesto_tempo;
		} else {
			$firstNameZip = $preclasificacion->manifiesto;
		}

		$misArchivos = [
			"Ducas" => [],
			"Mandamientos" => [],
			"Comprobantes" => [],
			"Facturación" => []
		];

		$name_zip = '';
		switch ($tipo_archivo) {
			case '1':
				$name_zip = "Ducas";
				break;
			case '2':
				$name_zip = "Mandamientos";
				break;
			case '3':
				$name_zip = "Comprobantes";
				break;
			case '4':
				$name_zip = "Adimex";
				break;
			default:
				$this->download_files($id, $firstNameZip);
				break;
		}

		foreach ($archivos as $key) {
			//echo $key->url;

			if (($key->id_tipo_documento == 1 && $tipo_archivo == 1) || $tipo_archivo == null) {
				$misArchivos["Ducas"][] = $key->url;
			}
			if (($key->id_tipo_documento == 2 && $tipo_archivo == 2) || $tipo_archivo == null) {
				$misArchivos["Mandamientos"][] = $key->url;
			}
			if (($key->id_tipo_documento == 3 && $tipo_archivo == 3) || $tipo_archivo == null) {
				$misArchivos["Comprobantes"][] = $key->url;
			}
			if (($key->id_tipo_documento == 4 && $tipo_archivo == 4) || $tipo_archivo == null) {
				$misArchivos["Facturación"][] = $key->url;
			}
		}

		//var_dump($misArchivos);


		// 1. Definir la estructura y rutas (como ya lo tienes)
		$nombreZip = $firstNameZip . "_" . $name_zip . ".zip";
		$zip = new ZipArchive();


		// 2. Crear el ZIP
		if ($zip->open($nombreZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

			foreach ($misArchivos as $carpeta => $archivos) {

				//echo $carpeta;
				//var_dump($archivos);

				//$zip->addEmptyDir($carpeta);
				foreach ($archivos as $ruta) {

					$ruta = getcwd() . $ruta;

					//echo $ruta;

					if (file_exists($ruta)) {


						if ($tipo_archivo == null) {
							$zip->addFile($ruta, $carpeta . "/" . basename($ruta));
						} else {
							$zip->addFile($ruta, basename($ruta));
						}

						echo "El archivo si existe";
					} else {

						echo "El archivo no existe";
					}
				}
			}


			//die();

			$zip->close();

			// 3. INICIAR DESCARGA AUTOMÁTICA
			if (file_exists($nombreZip)) {
				// Limpiar cualquier buffer de salida previo para evitar archivos corruptos
				if (ob_get_level()) {
					ob_end_clean();
				}

				// Cabeceras esenciales para forzar la descarga
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename="' . basename($nombreZip) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Content-Length: ' . filesize($nombreZip));
				header('Pragma: no-cache');
				header('Expires: 0');

				// Leer el archivo y enviarlo al navegador
				readfile($nombreZip);

				// 4. LIMPIEZA: Borrar el ZIP del servidor después de enviarlo
				unlink($nombreZip);
				exit;
			}
		} else {
			die("No se pudo crear el archivo comprimido.");
		}
	}

	public function download_files($id, $firstNameZip)
	{

		$archivos = $this->WhModel->obtener_archivos_para_exportar($id);

		$misArchivos = [
			"Ducas" => [],
			"Mandamientos" => [],
			"Comprobantes" => [],
			"Facturación" => []
		];

		foreach ($archivos as $key) {
			//echo $key->url;

			if ($key->id_tipo_documento == 1) {
				$misArchivos["Ducas"][] = $key->url;
			}
			if ($key->id_tipo_documento == 2) {
				$misArchivos["Mandamientos"][] = $key->url;
			}
			if ($key->id_tipo_documento == 3) {
				$misArchivos["Comprobantes"][] = $key->url;
			}
			if ($key->id_tipo_documento == 4) {
				$misArchivos["Facturación"][] = $key->url;
			}
		}

		//var_dump($misArchivos);
		//die();

		// 1. Definir la estructura y rutas (como ya lo tienes)
		$nombreZip = $firstNameZip . ".zip";
		$zip = new ZipArchive();

		// 2. Crear el ZIP
		if ($zip->open($nombreZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

			foreach ($misArchivos as $carpeta => $archivos) {

				//echo $carpeta;
				//var_dump($archivos);

				$zip->addEmptyDir($carpeta);
				foreach ($archivos as $ruta) {

					$ruta = getcwd() . $ruta;
					//echo $ruta;

					if (file_exists($ruta)) {
						$zip->addFile($ruta, $carpeta . "/" . basename($ruta));

						echo "El archivo si existe";
					} else {

						echo "El archivo no existe";
					}
				}
			}


			//die();

			$zip->close();

			// 3. INICIAR DESCARGA AUTOMÁTICA
			if (file_exists($nombreZip)) {
				// Limpiar cualquier buffer de salida previo para evitar archivos corruptos
				if (ob_get_level()) {
					ob_end_clean();
				}

				// Cabeceras esenciales para forzar la descarga
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename="' . basename($nombreZip) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Content-Length: ' . filesize($nombreZip));
				header('Pragma: no-cache');
				header('Expires: 0');

				// Leer el archivo y enviarlo al navegador
				readfile($nombreZip);

				// 4. LIMPIEZA: Borrar el ZIP del servidor después de enviarlo
				unlink($nombreZip);
				exit;
			}
		} else {
			die("No se pudo crear el archivo comprimido.");
		}
	}

	public function eliminar_documento_manifiesto($id)
	{
		$res = $this->WhModel->eliminar_documento_manifiesto($id);

		if ($res) {
			echo json_encode(['status' => 'ok', 'msg' => 'Documento eliminado correctamente']);
		} else {
			echo json_encode(['status' => 'false', 'msg' => 'Ocurrió un error al intentar eliminar el documento']);
		}
	}

	public function get_current_guide($id_manifiesto)
	{

		$data = $this->WhModel->get_current_guide($id_manifiesto);

		$this->load->view('awb/form_change_guide', $data);
	}

	public function cambiarGuia()
	{
		$flag = 0;

		if ($_POST["chkOnlyCurrent"] == true) {
			$flag = 1;
		}

		$this->WhModel->update_guides($_POST["txtIdPreclasificacionChangeGuide"], $_POST["guiaToChange"], $_POST["guiaEscrita"], $flag);
	}
}






class PDF extends FPDF
{
	public function Header()
	{
		$this->SetFont('Arial', '', 15);
		$this->SetFillColor(255, 255, 255);
		$this->SetFillColor(255, 255, 255);


		$this->Ln(10);

		// Mostramos la fecha en la que se genero dicho reporte
		$this->SetFont('Arial', 'B', 14);
		$this->Cell(90, 4);
		$this->Cell(100, 8, $GLOBALS['fecha'], 0, 0, 'C', 1);
		$this->Ln(10);

		// Creamos las cabeceras de la tabla con los datos que usaremos

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(10, 8,  utf8_decode('#'),                               1, 0, 'C', 1);
		$this->Cell(20, 8,  utf8_decode('AWB'),                             1, 0, 'C', 1);
		$this->Cell(30, 8,  utf8_decode('Tracking'),                        1, 0, 'C', 1);
		$this->Cell(35, 8,  utf8_decode('Tracking Replace'),                        1, 0, 'C', 1);
		//$this->Cell(15, 8,  utf8_decode('Fecha Creacion'),                  1, 0, 'C', 1);            
		$this->Cell(65, 8,  utf8_decode('Cliente'),                         1, 0, 'C', 1);
		$this->Cell(25, 8,  utf8_decode('Tipo Entrega'),                    1, 0, 'C', 1);
		$this->Cell(25, 8,  utf8_decode('Tipo Servicio'),                   1, 0, 'C', 1);
		$this->Cell(25, 8,  utf8_decode('Estatus'),                         1, 0, 'C', 1);
		$this->Cell(20, 8,  utf8_decode('Estado'),                          1, 0, 'C', 1);
		$this->Cell(25, 8,  utf8_decode('Cobro Final'),                     1, 0, 'C', 1);
		$this->Ln();
	}

	public function reportemanifiesto($datos)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		//echo 'generando los datos para el   libro diarios';

		$contador1          =  1;
		$cobro_final       =  0;

		foreach ($datos as $fila) {
			$awb                   =   $fila->awb;
			$tracking_number       =   $fila->tracking_number;
			$tracking_replace      =   $fila->tracking_replace;
			$consignee             =   $fila->consignee;
			$tipo_entrega          =   $fila->tipo_entrega;
			$tipo_servicio         =   $fila->tipo_servicio;
			$estatus               =   $fila->estatus;
			$estado                =   $fila->estado;
			$cobro_final           =   $fila->cobro_final;


			$this->SetFont('Arial', '', 7);
			$this->SetFillColor(255, 255, 255);
			$this->SetFillColor(255, 255, 255);

			$this->SetFont('Arial', '', 8);
			$this->Cell(10,  4,  $contador1,                               1, 0, 'C', 0);
			$this->Cell(20,  4,  $awb,                                     1, 0, 'C', 1);
			$this->Cell(30,  4,  $tracking_number,                         1, 0, 'C', 1);
			$this->Cell(35,  4,  $tracking_replace,                        1, 0, 'C', 1);
			$this->Cell(65,  4,  utf8_decode($consignee),                  1, 0, 'L', 1);
			$this->Cell(25,  4,  $tipo_entrega,                            1, 0, 'C', 1);
			$this->Cell(25,  4,  $tipo_servicio,                           1, 0, 'C', 1);
			$this->Cell(25,  4,  $estatus,                    1, 0, 'C', 1);
			$this->Cell(20,  4,  $estado,                                  1, 0, 'C', 1);
			$this->Cell(25,  4,  '$' . number_format($cobro_final, 2),       1, 0, 'R', 1);
			$this->Ln();

			$contador1   += 1;
			$cobro_final  = $cobro_final + $fila->cobro_final;
		}
	}

	//   segmemento para  genarar  le  nuevo detalle del   libro diario

	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', '', 10);
		$this->Cell(0, 10, '' . $this->PageNo(), 0, 0, 'C');
	}
}



/* End of file Controllername.php */


/* End of file Controllername.php */
