<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');

defined('BASEPATH') or exit('No direct script access allowed');
//include getcwd() . "/libraries/RestController.php";

require(APPPATH . '/libraries/REST_Controller.php');
include getcwd() . "/application/libraries/conversor/facturaelectronica.php";
include getcwd() . "/application/libraries/conversor/generarpartidas.php";
//use Restserver\Libraries\RestController;
class Apitest extends REST_Controller
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
		$this->load->model('DTES_Model');
		$this->load->model('Exportacion_Model');
		$this->load->model('Ventas_Model');
		$this->load->model('Importacion_Model');
		$this->load->model('Entrega_model');
		$this->load->model('Conf_model');
	}

	public function jsoncompras_post()
	{
		// 1. Leer el stream de entrada
		$data = file_get_contents('php://input');

		// 2. Decodificar el JSON a array
		$row = json_decode($data, true);

		// 3. Validar que el JSON no venga vacío o corrupto
		if (empty($row)) {
			header('Content-Type: application/json');
			http_response_code(400);
			echo json_encode([
				'status'  => false,
				'message' => 'El cuerpo de la petición no contiene un JSON válido.'
			]);
			return;
		}

		// 4. Pasar los datos al modelo
	//	$result = $this->Manifiesto_model->json_compras($row);
$result="proceso correctamente";
		// 5. Responder a n8n con éxito (HTTP 200)
		header('Content-Type: application/json');
		http_response_code(200);
		echo json_encode([
			'status'  => true,
			'message' => 'JSON recibido y procesado correctamente',
			'data'    => $result
		]);
	}


	public function estados_post()
	{

		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
		date_default_timezone_set("America/El_Salvador");
		$date = date('Y-m-d H:i:s');
		$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
		$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

		$item = "";
		$fecha = "";
		$warehouse = "";
		$id_estatus = "";
		$casillero = "";
		$id_pais =  "";



		$data = $this->post(); //contiene los datos

		$data =  file_get_contents('php://input', true);
		//  echo $data ;
		$row = json_decode($data, true);




		$item = 1;
		$fecha = date('Y-m-d H:i:s');
		$warehouse =  $row['guia'];

		/** Recibe la guia de xpreess (internal_tracking) y luego busca la guia de Mailamericas(tracking_number) */
		$query =	 $this->Manifiesto_model->get_tracking($warehouse);
		//var_dump($query);

		$cliente = $this->Manifiesto_model->datos_empresa($query[0]->id_empresa);

		$codigoEmpresa = $query[0]->id_empresa; //se utiliza para determinar si se crea FEL o no

		$id_tokencliente = $cliente[0]->id_tokencliente;
		$token_cliente = $cliente[0]->token_cliente;


		if ($query[0]->metodo == "E") {

			$warehouse = $query[0]->tracking_number;
			// echo "estoy en correlativos de xpress";
		} else {
			//echo "estoy en correlativos de MA";
		}

		//	echo $cliente. " ". $codigoEmpresa. " ". $id_tokencliente. " ".$token_cliente;
		//	exit;

		$id_estatus = $row['codigo'];
		$nombre_estatus = $row['estatus'];

		$description = "";
		if ($id_estatus == 16) {
			$description    =  $row['estatus'] . ": " .  $row['observaciones'];
		} else {
			$description    =  $row['estatus'];
		}

		$casillero = "100";
		$id_pais =  1;
		$pod_nombre = "";
		$pod_valor = "";
		$document_name = "";
		$ruta = "";
		//    $this->response($row);
		if ($id_estatus == 15) {
			$ruta = "https://stargateserp.com/documentos_ma/";
			// foreach ( $row['pod'] as $key => $detalle) {


			foreach ($row['pod'] as  $detalle) {
				$pod_nombre = $detalle['nombre'];
				$pod_valor  = $detalle['valor'];
				$img = str_replace('data:image/png;base64,', '', $pod_valor);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$data1 = base64_decode($img);
				$document_name = uniqid() . '.png';
				//  $file = "/home/km96wogi8pjb/public_html/documentos_ma/". uniqid() . '.jpg';
				// $file = "/home/km96wogi8pjb/public_html/documentos_ma/" . $document_name;
				$file = "/var/www/stargateserp/documentos_ma/" . $document_name;


				$success = file_put_contents($file, $data1);
				//    echo "bytes fueron escritos en $file";     

			}
		}
		//   $this->response($row);

		if ($id_estatus == 15) {
			$ruta = $ruta . $document_name;
		} else {
			$ruta = null;
		}



		if ($id_estatus == 15 && ($row['devolucion'] ?? 0) == 1) {
			$id_estatus = "FP-2";
			$description = "Entregado en dirección de retorno";
		}
		$this->Manifiesto_model->update_estatus($warehouse, $id_estatus); //guardar estas en tablas locales

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


		$this->Manifiesto_model->guardar_estatus($datos);

		$token_ma = $this->token_ma($id_tokencliente, $token_cliente); //obtiene el token



		$header = array(
			'Host: tracking.mailamericas.com',
			'Content-Type: application/json',
			'Cache-Control: no-cache',
			'Authorization: Bearer ' . $token_ma
		);

		$data = array(
			"tracking" => $warehouse,
			"date" =>  $fecha_iso,
			"description" =>   $description,
			"city" => "CCO",
			"received_by" => null,
			"relationship" => null,
			"delivery_proof" => ($id_estatus == 15 && !empty($ruta)) ? [$ruta] : [],

			"gcs" => "",
			"details" => ""
		);

		$headers = [
			'Authorization: Bearer ' . $token_ma,   // usa el MISMO token que en Postman para probar
			'Content-Type: application/json',
			'Expect:' // evita 100-continue
		];

		$url = "https://tracking.mailamericas.com/api/v1/providers/events";

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_POSTFIELDS     => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_CONNECTTIMEOUT => 20,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
		]);
		$resultStr = curl_exec($ch);
		$curl_err  = curl_error($ch);
		$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// log local (ya lo haces)
		$this->Manifiesto_model->guardar_logma([
			"respuesta"       => $resultStr ?: $curl_err,
			"tracking_number" => $warehouse,
			"descripcion"     => $description,
			"fecha"           => $fecha,
			"fechaiso"        => $fecha_iso
		]);

		$data = array(
			"respuesta" => $resultStr,
			"tracking_number" =>  $warehouse,
			"descripcion" =>  $description, // m
			"fecha" =>  $fecha,
			"fechaiso" =>  $fecha_iso

		);

		$this->Manifiesto_model->guardar_logma($data);

		$this->DTES_Model->updatetracking_usps($id_estatus, $warehouse);
		// ==== NUEVO: armar payload para outbox y encolar si falló el envío ====
		$payload = [
			"tracking"       => $warehouse, // asegúrate que sea el MISMO que usaste en Postman
			"date"           => $fecha_iso, // ej: 2025-10-10T16:54:17-06:00 (DateTime::ATOM te da ese formato)
			"description"    => $description,
			"city"           => "CCO",
			"received_by"    => null,
			"relationship"   => null,
			// IMPORTANTE: si no hay POD, va un arreglo vacío, NO [$ruta.$document_name]
			"delivery_proof" => ($id_estatus == 15 && !empty($ruta)) ? [$ruta] : [],
			"gcs"            => "",
			"details"        => ""
		];

		// Falla si hubo error de transporte o HTTP no-2xx
		if (!empty($curl_err) || $httpCode < 200 || $httpCode >= 300) {
			$this->Entrega_model->enqueueOutboxma(
				$warehouse,
				(int)$id_estatus,
				$nombre_estatus,
				date('Y-m-d H:i:s'),
				$payload,
				// lastError:
				!empty($curl_err)
					? $curl_err
					: ('HTTP ' . (int)$httpCode . ' - ' . substr((string)$resultStr, 0, 500)),
				// retryMinutes opcional:
				5
			);
		}

		if ($codigoEmpresa == 108 || $codigoEmpresa == 109 || $codigoEmpresa == 100) {
			//bloque para facturar MA-FEL
			if ($id_estatus == 15) {
				$guiaFel = $this->Manifiesto_model->get_guiaFel($warehouse);
				$json_data = json_encode($guiaFel);

				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://stargates.site/app_starship/ws/index.php/api/guiaaws',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_POST => true,  // Forzar solicitud POST
					CURLOPT_POSTFIELDS => $json_data,  // Enviar el JSON como datos POST
					CURLOPT_HTTPHEADER => array(
						'X-API-KEY: 4a4da5bb348e648e51d84e24c8652d1284c31156',
						'Content-Type: application/json',
						'Content-Length: ' . strlen($json_data)  // Longitud del contenido
					),
				));

				$response = curl_exec($curl);
				$error_no = curl_errno($curl);
				$error_msg = curl_error($curl);

				curl_close($curl);

				// Manejo de errores
				if ($error_no) {
					echo "cURL Error ($error_no): " . $error_msg . PHP_EOL;
				} else {
					echo $response;
				}
			}
		}





		$warehouse = '';
		# SEGMENTO PARA LA FACTURACION ELECTRONICA
		if ($warehouse != '') {
			if ($id_estatus == 15) {
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);
				$ejecuta    = false;
				if ($ejecuta == true) {
					$_SESSION["user_id"] = date("his");
					$seleccionado        = 1;
					$ma                  = 1;
					$guiaMAupdate        = 0;
					$idEncaComprobante   = 0;
					$datoupcabecera      = 0;
					$datosdfacturacion   = 0;
					$guiaSeleccionada    = 0;
					$datosguiaMA         = 0;
					$datosguiaMA         =  $this->getItemsForGma($warehouse); // DESCARGAMOS LA GUIA
					//echo  'la guia  maRetorno'. $datosguiaMA;
					# SEGMENTO PARA LA EJECUCION  DE  SELECCION Y OTROS PROCESOS  SI SE  DESCARGA LA GUIA
					if ($datosguiaMA != 0) {
						$guiaSeleccionada = $this->seleccionar_guia($warehouse, $seleccionado, $ma);
						if ($guiaSeleccionada == 0) { // 0  indica que la guia  no  tuvo ninguna incosistencia y se selecciono
							# SEGMENTO  PARA LA  GENERACION  DE LA  VENTA
							// echo  'Ingresando al  proceso de facturacion';
							$clasificacioFiscal     = 0;   // 1- grande  2-mediano  3- otros
							$tipoContribuyente      = 0;   // 1- gravado 2- excento 3- tasa cero 4-sujeto excluido
							$sujetoRetencion        = 0;   // solo si la   si la venta gravada es >=100  y es 1- grande ciontribuyente
							$idTipoComprobante      = 0;   // 1-CCF 2-FCf 3- NCR(nota credito) 4- fex(factura exportacion) 5- ANF (anulacion factura consumidor final)
							$numero_comprobante     = 0;
							$fecha_comprobante      = date('Y-m-d'); // cambiar por la indicada
							$id_tipo_transac        = '';
							$idEmpresa              = 0;
							$idSucursal             = 0;
							$idCliente              = 0;
							$idDestinatario         = 0;
							$idCatalogoProducto     = 0;
							$ot_numero              = 0; // orden de trabajo
							// Capturando valriables de session
							$idEmpresa              = 47; //$_SESSION["empresa_id"];// se cambia para variable de inicion de sesion
							$idSucursal             = '1'; // factura para  global cargo, se direcciona todo para la sucursal san francisco
							$idEncaComprobante      = 0;
							// para calculos
							$cantidad               = 1; //  evaluar
							$precio_neto            = 0; //  es el valor del  servicio
							$venta_neta             = 0;
							$id_tipo_evento         = 2; // 2- exportacion
							$fact_manual            =  0;
							$id_comprob_afecto      = null;
							$DatosClientes        = $this->Exportacion_Model->obtenerClienteAgrupadosMA(); // se obtien los datos de la guias(el detalle)
							if ($DatosClientes ==  null) {
								echo 0; // variable que indica si existen datos a facturar
								echo 'NO HAY DATOS A FACTURAR '; // variable que indica si existen datos a facturar
							} else {
								// echo 1;
								foreach ($DatosClientes as $datosClientes) { //  recorremos el numero  de  clientes  pendientes  a facturar
									$idCliente   = $datosClientes->id;
									//var_dump( 'el Cliente a procesar !!!!!!!!!!!!!!!!!!!!!!! '.$idCliente) ;
									$destinatarios = $this->Conf_model->get_destinatario($idCliente);
									if (!empty($destinatarios)) {
										$id_destinatario = $destinatarios[0]->id;
									} else {
										$id_destinatario =  1033;
									}
									$clasificacioFiscal = $datosClientes->id_clasificacion_fiscal;
									// 1 GRANDE 2- MEDIANO 3- OTRO
									$id_tipo_pago =  $datosClientes->id_tipo_pago;
									$tipoContribuyente  = $datosClientes->id_tipo_contribuyente;
									if ($tipoContribuyente == '1') { // GRAVADO
										$tasaCero = 'NO';
										$exento   = 'NO'; // SI ES NO INDICA QUE SE LE APLICA IVA
									} elseif ($tipoContribuyente == '2') { // EXCENTO
										$tasaCero = 'NO';
										$exento   = 'SI'; // NO SE SE LE APLICA IVA
									} elseif ($tipoContribuyente == '3') { // TASA CERO
										$tasaCero = 'SI';
										$exento   = 'NO';
									}

									$sujetoRetencion    = $datosClientes->sujeto_ret_1;
									if ($sujetoRetencion == 'S'  || $sujetoRetencion == 's') { // EL  CLIENTE ESTA SUJETO A RETENSION
										$percep1 = 'SI';
									} else {
										$percep1 = 'NO';
									}
									$idTipoComprobante  = $datosClientes->id_tipo_comprobante;
									//  var_dump( 'el tipo de contribuyecte es'.$tipoContribuyente.'el tipo de comprobante a crear es' . $idTipoComprobante . 'valor de tasa cero'.$tasaCero.'el valor de excento' .$exento);
									$correlativoFactura =  $this->Importacion_Model->obtenerCorrelativoCom($idEmpresa, $idSucursal, $idTipoComprobante);
									// var_dump($correlativoFactura);
									$numero_comprobante =  $correlativoFactura->correlativo;
									// se incrementa el correlativo de factura
									$serie_factura      =  $correlativoFactura->serie;
									$nuevoCorrelativo   = strval($numero_comprobante + 1);
									// var_dump('el nuevo correlativo sera '.$nuevoCorrelativo);
									// 1- generar un encabezado con el  idautomatico
									$data = array(
										'id_empresa' 		  => $idEmpresa,
										'id_sucursal_empresa' => $idSucursal,
										'id_cliente'		  => $idCliente,
										'id_destinatario'	  => $id_destinatario,
										'id_tipo_comprobante' => $idTipoComprobante,
										'numero_comprobante'  => $numero_comprobante,
										'comprobante_afecto'  => $numero_comprobante,
										'serie_factura'		  => $serie_factura,
										'fecha_comprobante'   => $fecha_comprobante,
									);
									// 1- llamar la funcion de almacenar encabezado generar el espacio en la tabla
									$idEncaComprobante  = $this->Ventas_Model->guardar_venta($data);
									//$datos_encabezado  = $this->Importacion_Model->obtener_id_encabezado_venta($idEmpresa, $idSucursal, $idCliente, $idTipoComprobante, $numero_comprobante, $fecha_comprobante);
									//$idEncaComprobante = $datos_encabezado->id;
									$seleccionado      = 1;
									$Catalogos_a_Facturar =  $this->Exportacion_Model->obtner_valores_detalle_GuiaMA($warehouse);
									if (!empty($Catalogos_a_Facturar)) {

										foreach ($Catalogos_a_Facturar as $catalogos_a_Facturar) {
											// obtener la descripcion del servicio por catalago de producto
											// catalogos a facturar  cargo basico, inspeccion no intrusiva, fuel, seguro


											//$cargo_corte_guia = $catalogos_a_Facturar->D15;

											if ($catalogos_a_Facturar->D15 > 0) {
												$precio_neto  = $catalogos_a_Facturar->D15;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '31';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												// var_dump($DatosKardex);
												// $this->Exportacion_Controller->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//$argo_manejo_y_transp = $catalogos_a_Facturar->argo_manejo_y_transp;
											if ($catalogos_a_Facturar->N15 > 0) {
												$precio_neto  = $catalogos_a_Facturar->N15;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '32';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												// var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//$cargo_seguro = $catalogos_a_Facturar->cargo_seguro;
											if ($catalogos_a_Facturar->N48 > 0) {
												$precio_neto  = $catalogos_a_Facturar->N48;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '33';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												//var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//$cargo_entrega_local        = $catalogos_a_Facturar->insp_no_intrusiva;
											if ($catalogos_a_Facturar->cargo_seguro > 0) {
												$precio_neto  = $catalogos_a_Facturar->cargo_seguro;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '82';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												//var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}

											// se ingresa a kardex  como  tramite de visado
											//$cargo_permiso = $catalogos_a_Facturar->insp_no_intrusiva;
											if ($catalogos_a_Facturar->insp_no_intrusiva > 0) {
												$precio_neto  = $catalogos_a_Facturar->insp_no_intrusiva;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '84';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												// var_dump('los datos de inspeccioj no intrusiva son #################');
												//var_dump($DatosCatalogoProducto);

												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta' 		=> $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh' 			=> $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);
												// var_dump('************************ guardando inspeccion no instrusica******************');
												//  var_dump($DatosKardex);
												//$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}



											// $cargo_tramite_aduanal = $catalogos_a_Facturar->fuel;
											if ($catalogos_a_Facturar->fuel > 0) {
												$precio_neto  = $catalogos_a_Facturar->fuel;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '123';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);
												// var_dump('Datos  a facturar en kardex');

												// var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//para  facturar el  nuevo servicio de cuenta  ajena;
											if ($catalogos_a_Facturar->iva_adn > 0) {
												$precio_neto  = $catalogos_a_Facturar->iva_adn;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '145';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);
												// var_dump('Datos  a facturar en kardex');

												// var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}



											// Generando los montos totales de los encabezados del comprobante
											$DatossumKardex = $this->Ventas_Model->calcularTotalesventa($idEncaComprobante);
											// pasando valores a variables para realizar calculos
											$totalVentaNetaAjena        = $DatossumKardex[0]->totalKardexCuentaAjena;
											$totalKardexGravado         = $DatossumKardex[0]->totalKardexGravado;
											$truncar = 10 ** 2;
											// $totalKardexIvaGravado      =   intval(($DatossumKardex[0]->totalKardexGravado *0.13) * $truncar) / $truncar;
											$totalKardexIvaGravado      = $DatossumKardex[0]->totalKardexIvaGravado;
											// $totalKardexIvaGravado = parseFloat(data[0].totalKardexIvaGravado;
											$totalKardexExento          = $DatossumKardex[0]->totalKardexExento;
											$totalKardexExportacion     = $DatossumKardex[0]->totalKardexExportacion;
											$totalKardexNosujeto        = $DatossumKardex[0]->totalKardexNosujeto;
											//   almacena el   subtotal de la  venta
											$subTotal = round(($totalKardexGravado + $totalKardexIvaGravado + $totalKardexExento + $totalKardexExportacion + $totalKardexNosujeto), 2);
											//  calculando si para el  cliente se le hace retencion
											if ($percep1 == 'SI') {
												if ($totalKardexGravado >= 100) {
													$retencionUnoPorCiento = round(($totalKardexGravado * 0.01), 2);
												}
											} else {
												$retencionUnoPorCiento = 0.00;
											}
											$totalLiquidocobrar1PorCiento = ($subTotal - $retencionUnoPorCiento);
											//  almacena el   total  de l
											$totalKardexVentaTotal = number_format(($totalLiquidocobrar1PorCiento + $totalVentaNetaAjena), 2);
											$totalKardexVentaTotal =   str_replace(',', '', $totalKardexVentaTotal);
											$nombrecliente         = '';
											$documentopresentar    = '';
											// generando el array para actualizar el encabezado del  comprobante
											$data = array(
												'id_empresa'				=> $idEmpresa,
												'id_sucursal_empresa'		=> $idSucursal,
												'id_cliente'				=> $idCliente,
												'nombrecliente'            => $nombrecliente,
												'documentopresentar'        => $documentopresentar,
												'id_destinatario'			=> $id_destinatario,
												'id_tipo_comprobante'		=> $idTipoComprobante,
												'numero_comprobante'		=> $numero_comprobante,
												'comprobante_afecto'		=> $numero_comprobante,
												'id_comprob_afecto'         => $id_comprob_afecto,
												'serie_factura'		        => $serie_factura,
												'ot_numero'					=> $ot_numero,
												'fecha_comprobante'			=> $fecha_comprobante,
												'total_exento'				=> $totalKardexExento,
												'total_exportacion'			=> $totalKardexExportacion,
												'total_no_sujeto'			=> $totalKardexNosujeto,
												'total_gravado'				=> $totalKardexGravado,
												'total_iva_gravado'			=> $totalKardexIvaGravado,
												'sub_total_ventas'			=> $subTotal,
												'retencion_1_por_ciento'	=> $retencionUnoPorCiento,
												'sub_total_cuenta_ajena'	=> $totalVentaNetaAjena,
												'venta_total'				=> $totalKardexVentaTotal,
												'id_tipo_evento'            => $id_tipo_evento,
												'fact_manual'               => $fact_manual,
											);

											//var_dump('totales de factura............................................');
											if ($totalKardexVentaTotal > 0) {
												$datoupcabecera  =  $this->Ventas_Model->guardar_enca($data, $idEncaComprobante);
												if ($datoupcabecera !=  0) {
													// segmento para actualizar en  guias_import_ss  los warehouse que ya se procesaron
													//actualizar el control de correlativos
													$this->Importacion_Model->actualizar_correlativos($idEmpresa, $idSucursal, $idTipoComprobante, $nuevoCorrelativo);

													$procesado = 1; // variable que indicara que los warehouse ya fueron  procesados
													//  funcion  para  obtener las  guias a actualizar
													//  arrar  de datos  de facturacion
													$data =  array(
														'id_tipo_comprobante'    => $idTipoComprobante,
														'numero_comprobante'     => $numero_comprobante,
														'fecha_comprobante' 	 => $fecha_comprobante,
														'facturacion_pendiente'  => 1,
														'seleccionado'           => 0
													);
													// echo  'modificar los datos de las  guias' se  ponen como facturado;
													$datosdfacturacion = $this->Exportacion_Model->asigna_datosdfacturacion($idEncaComprobante, $idCliente, $warehouse, $data);
													//  obtenermos  las  guias  facturadas para  casos  MA
													if ($datosdfacturacion != 0) {
														$guias_g  =   $this->Exportacion_Model->guias_expfacturadasFE($idEncaComprobante);
														if (!empty($guias_g)) {
															foreach ($guias_g as $gma) {
																if ($gma->ma   == 1) {
																	$tracking_number = $gma->guia_no;
																	// echo 'actualizando para  MA' . '<br>';
																	$guiaMAupdate = $this->Exportacion_Model->update_facturadoUSPS($tracking_number);
																}
															}
														}
													}
													# generamos la partida de venta
													$codigopartida   = '008';
													//$generarpartida  = new Generarpartidas();
													//$generarpartida->generarpartida_venta($idTipoComprobante, $idEncaComprobante, $codigopartida, $fecha_comprobante);
													# fin de la generacion de partida de venta

													$id_tipo_pago    = 2;
													if ($guia_ncancelada = 0) { // indica que ya se cancelaron  todas las guias de la factura
														if ($id_tipo_pago == 1) { //  el cliente es de contado
															//  quitar la bandera de incluir cobro
															$incluir_cobro = 0; //  quitamos la marca de cobrado /
															$this->Ventas_Model->incluir_cobro($incluir_cobro, $idEncaComprobante);
														}
													}
													$valor_abono        = 0.00;
													$valor_cargo        = $totalKardexVentaTotal;
													$comprobante_afecto = $numero_comprobante;
													$tipo_transac       =  'c';                                    //}

													echo 'ingresando al  proceso de crear cuenta por cobrar!!!!!!!!!!';

													if ($id_tipo_pago ==   2) {
														echo 'ingresando al  proceso de crear cuenta por cobrar';
														$dataCxC = array(
															'id_cliente'             => $idCliente,
															'id_destinatario'        => $idCliente,
															'id_empresa'             => $idEmpresa,
															'id_comprob_venta'       => $idEncaComprobante,
															'num_comrpobante'        => $numero_comprobante,
															'id_tipo_comprobante'    => $idTipoComprobante,
															'id_comprob_afecto'      => $idEncaComprobante,
															'valor_cargo'            => $valor_cargo,
															'valor_abono'            => $valor_abono,
															'fecha_emision'          => $fecha_comprobante,
															'tipo_transac'          => $tipo_transac
														);
														//var_dump($dataCxC);
														//var_dump('proceso  de almacenado en  det_cxc');
														$idcxc = $this->Ventas_Model->buscarIdCxC($idEncaComprobante);
														// siempre se genera  ya que crea la cuenta por cobrar despues del  proceso  de facturacion
														$this->Ventas_Model->guardar_detalle_cxc($dataCxC, $idcxc->id);
													}
												}
											}
										}
									}
									# FIN DEL SEGMENTO  DE LA  GENERACION  DE LA  VENTA
								}
							}
							//  recorriendo  los datos del detalle de la guia para obtener a que cliente se le va a facturar
						}
						if ($guiaMAupdate != 0) {
							$id_venta = 0;
							$datos    = $this->DTES_Model->datguia_exp($warehouse);
							if (!empty($datos)) {
								$id_venta = $datos->id_venta;
								//   con ese  id  obtenemos los datos de la  venta
								if ($id_venta !=  0) {
									$datosv =    $this->DTES_Model->datventa($id_venta);
									if (!empty($datosv)) {
										// var_dump($datosv);
										$id = $datosv->id;
										$id_tipo_evento = $datosv->id_tipo_evento;
										$cod_generacion = $datosv->cod_generacion;
										$resultadoERR   = '';
										$id_cliente     =  12165;
										//echo   'eñ  codigo  de  generacion  es ' .  $cod_generacion;
										if ($cod_generacion ==   null || $cod_generacion ==   null  || $cod_generacion == '') {
											$proceso  = 1;
											$facturaelectronica  = new Facturaelectronica();
											$resulgeneracio      =   $facturaelectronica->generarfacturaelectronica($id_tipo_evento, $id);
											$resultadoERR =   $facturaelectronica->CtrlError($resulgeneracio, $id_cliente, $proceso, $id);
										} else {
											echo  'ya se  genero facura  electronica';
										}
									}
								}
							} else {
								echo   'Esta guia  no se encuentra descargada en  el  sistema de facturacion';
							}
						}
					} else {
						echo  'esta guia ya se  proceso!!!!!!!!!!!!!!!!!!!!';
					}
				}
			}
		}
		# FIN DEL SEGMENTO PARA LA FACTURACION  ELECTRONICA

		//  actualizar  estatus  de  manifiesto 
		//echo 'los  daos para  actualizar son  ' .  $id_estatus .  ' hw'. $warehouse; 

	}


	function token_ma($id_tokencliente, $token_cliente)
	{

		$header = array(
			'Host: tracking.mailamericas.com',
			'Content-Type: application/json',
			'Cache-Control: no-cache'
		);

		$data = array(
			"grant_type" => "client_credentials",
			"client_id" => $id_tokencliente, //42,
			"client_secret" => $token_cliente, //"1CFIlE3pXo2QdJgcnlocoRIoXsJ1Dr3veM8pWKj4",
			"scope" => "*"
		);


		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://tracking.mailamericas.com/oauth/token',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode(array(
				"grant_type" => "client_credentials",
				"client_id" => $id_tokencliente,
				"client_secret" => $token_cliente,
				"scope" => "*"
			)),
			CURLOPT_HTTPHEADER => array(
				'Cache-Control: no-cache',
				'Content-Type: application/json',
				'Host: tracking.mailamericas.com'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//   echo $response;
		$code = json_decode($response);

		$token = $code->access_token;
		return $token;
	}


	/* Espacio para metodos d valores, vienen desde c807xpress/servicios*/
	public function tikets_post()
	{
		$data = $this->post();

		//    var_dump($data);
		//echo $data['descripcion'];

		$datos = array(
			"empresa_id"     => $data['empresa_id'],
			"proceso_id"     => $data['proceso_id'],
			"sistema_id"     => $data['sistema_id'],
			"estado_id"      => $data['estado_id'],
			"colaborador_id" => $data['colaborador_id'],
			"categoria_id"   => $data['categoria_id'],
			"asunto"         => $data['asunto'],
			"descripcion"    => $data['descripcion'],
			"prioridad_id"   => $data['prioridad_id'],
			"fecha"          => $data['fecha'],
			"created_at"     => $data['created_at'],
			"updated_at"     => $data['updated_at'],
		);
		$rsl = $this->Manifiesto_model->guardar_ticket($datos);
		echo $rsl;
	}

	public function consulta_tikets_get()
	{
		$result = $this->Manifiesto_model->consulta_tikets();
		echo json_encode($result, JSON_PRETTY_PRINT);
	}

	public function getItemsForGma($tracking_number)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$DrEtorno = 0;
		$fila  = $this->Exportacion_Model->getItemsFormaG($tracking_number);
		if ($fila != null) {   //  determina que el  json contenga valores para contiuar la operacion
			#region //  declaracion de variables
			foreach ($fila as $row) {
				$guia_no       = '';
				$guia_no       = $row->guia_no;
				// echo  'buscando la guia ' .  $guia_no   .  '<br>' ;
				$DatosNguia    = $this->Exportacion_Model->buscar_guia_tbguias($guia_no);
				//var_dump($DatosNguia);
				$id_empresa                     = 47;
				$id_prov_exp                 = 6; //  ups
				$id_cliente                     = 12165;
				$facturacion_pendiente         = 0;
				$fecha                         = date("Y-m-d"); //  fecha de la guia
				$fecha_ingreso                  = date("Y-m-d");
				$hora_ingreso                = null;
				$id_tipo_pago                = 5;
				$id_pais_destino             = 211;
				$id_region_destino           = 1;
				$id_servicio                 = 2;
				$largo_cms                   = 0;
				$alto_cms                    = 0;
				$ancho                       = 0;
				$ma                          = 1;
				$porcetajefuel               =  0;
				$unidad_m                    = 'lbs';
				$cargo_recargo_no_doc         = 0.0;
				$aplicar_seguro                 = 0; // 0 indica que no aplica  1  aplica
				$id_tarifa_seguro             = 0;
				$cargo_seguro                 = 0.0;
				$insp_no_intrusiva             = 0.0;
				$cargo_adicional             = 0.0;
				$concepto_cargo_adicional     = '';
				$valor_monto_declarado       = 0;
				$no_piezas                   = 1;
				$seleccionado                 = 0;
				$id_usuario_ingreso          =  $_SESSION["user_id"];
				$ciudad_destino              =  '';
				$subtotal                    = 0;
				$total_descuentos            = 0;
				$fuel                        = 0;
				$id_servexp                  = 2;
				$id = 0;
				$cargo_basico                = $row->cargo_basico;
				$cargo_peso_extra            = $row->cargo_peso_extra;
				$iva                         = $row->iva;
				$iva_adn                     = $row->iva_adn;
				$total_guia                  = $row->total_guia;
				if ($DatosNguia == null) {
					// indica que no encontro la guia en base de datos
					//echo  'proceso de  insertar  datos' ;
					$guia_no                     = $row->guia_no;
					$guia_ibc_no                 = $row->guia_no;
					$descripcion                 = $row->descripcion;
					$peso_neto_kgs               = $row->peso_neto_kgs;
					$peso_neto_lbs               = $row->peso_neto_lbs;
					$peso_a_tomar_kgs            = $row->peso_neto_kgs;
					$peso_a_tomar_lbs            = $row->peso_neto_lbs;

					$data = array(
						'id_empresa'                         => 47, //$_SESSION["empresa_id"]),
						'id_prov_exp'                         => $id_prov_exp,
						'id_cliente'                         => $id_cliente,
						'guia_no'                             => $guia_no,
						'guia_ibc_no'                         => $guia_ibc_no,
						'fecha'                             => $fecha, //  fecha de la guia
						'fecha_ingreso'                     => $fecha_ingreso, // fecha en  que el usuario ingresa la guia
						'hora_ingreso'                         => $hora_ingreso,
						'id_usuario_ingreso'                 => $id_usuario_ingreso,
						'id_tipo_pago'                         => $id_tipo_pago,
						'facturacion_pendiente'             => $facturacion_pendiente,
						'ciudad_destino'                     => $ciudad_destino,
						'id_pais_destino'                     => $id_pais_destino,
						'id_region_destino'                 => $id_region_destino,
						'id_servicio'                         => $id_servexp, // 1,2 documento  no  documento
						'descripcion'                       => $descripcion, // almacena que es lo que se envia
						'valor_monto_declarado'             => $valor_monto_declarado,
						'no_piezas'                         => $no_piezas,
						'unidad_m'                             => $unidad_m, //  lbs o kgs
						'peso_neto_kgs'                         => $peso_neto_kgs, // cambiar  peso_a_tomar_kgs
						'peso_neto_lbs'                         => number_format($peso_neto_lbs, 3), // cambiar  peso_a_tomar_kgs
						'peso_a_tomar_lbs'                     => number_format($peso_a_tomar_lbs, 3),
						'peso_a_tomar_kgs'                  => number_format(floatval($peso_a_tomar_kgs), 2),
						'cargo_basico'                         => $cargo_basico,
						'cargo_peso_extra'                     => $cargo_peso_extra,
						'cargo_recargo_no_doc'                 => $cargo_recargo_no_doc,
						'aplicar_seguro'                    => $aplicar_seguro,
						'id_tarifa_seguro'                     => $id_tarifa_seguro,
						'cargo_seguro'                         => $cargo_seguro,
						'insp_no_intrusiva'                     => $insp_no_intrusiva,
						'subtotal'                             => $subtotal,
						'total_descuentos'                     => $total_descuentos,
						'fuel'                                 => $fuel,
						'iva'                                => $iva,
						'total_guia'                         => $total_guia,
						'seleccionado'                      => $seleccionado,
						'porc_fuel'                         => $porcetajefuel,
						'iva_adn'                           => $iva_adn,
						'ma'                                => $ma,
					);
					$DrEtorno = $this->Exportacion_Model->guardar_guiaexp($id, $data);
				} else {
					// echo  'actualizando paquetes';
					//funcion  de  actualizacion
					$dataGMA = array(
						'cargo_basico'                         => $cargo_basico,
						'cargo_peso_extra'                     => $cargo_peso_extra,
						'iva_adn'                           => $iva_adn,
						'iva'                                => $iva,
						'total_guia'                         => $total_guia,
					);
					$DrEtorno = $this->Exportacion_Model->update_guiaMA($guia_no, $dataGMA);
				}
			}
		}
		return  $DrEtorno;
	}

	// funcion para  validad los  datos de las  guias
	public function seleccionar_guia($guia_no, $seleccionado, $ma)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$nError = 0;
		if ($ma ==  1) {
			// echo    'ingresando    a validacion  ma  ' ;
			// lanzamos la  validacion  de los  datos de las  guia x
			$datos_gEXP  =  $this->Exportacion_Model->getdatosGuiaEXP($guia_no);
			//var_dump($datos_gEXP);
			//echo 'datos MAjghjg' . '<br>';

			if (!empty($datos_gEXP)) {
				$datos_gMA  = $this->Exportacion_Model->getdatosGuiaMA($guia_no);
				//echo 'datos MA' . '<br>';
				//var_dump($datos_gMA);
				if (!empty($datos_gMA)) {
					//  comparamos el cargo basico
					if ($datos_gEXP->cargo_basico != $datos_gMA->manejo) {
						$nError += 1;
					}
					//  comparamos el cargo basico
					if ($datos_gEXP->cargo_peso_extra != $datos_gMA->comision) {
						$nError += 1;
					}
					//  comparamos el cargo basico
					if ($datos_gEXP->iva_adn != $datos_gMA->total_imp) {
						$nError += 1;
					}

					if ($datos_gEXP->iva != $datos_gMA->total_ivag) {
						$nError += 1;
					}
					if ($datos_gEXP->total_guia != $datos_gMA->cobro_final) {
						$nError += 1;
					}
				}
			}

			echo  'errror  en  la  guia ' . $nError . ' error  de seleccion ';
			if ($nError > 0) {

				$this->Exportacion_Model->seleccionar_guia($guia_no, $seleccionado = 0);
			} else {
				$this->Exportacion_Model->seleccionar_guia($guia_no, $seleccionado);
			}
		} else {
			$this->Exportacion_Model->seleccionar_guia($guia_no, $seleccionado);
		}
		echo $nError;
	}

	//  FUNCION PARA  ALMACENAR EL DETALLE DE LAS  VENTAS
	public function guardar_detalle_venta($idVenta, $exento, $tasaCero, $percep1, $DatosKardex)
	{
		//  determinar si los productos vienen de ingreso manual o de  un llenado automatico -- observacion  oscar
		//  se necesita pasar la informacio del cliente si esta excento , tasa cero o si se le persive al cliente
		// generacion  de variables
		$kdx_exento                 = 0.00;
		$kdx_exportacion             = 0.00;
		$kdx_no_sujeto                 = 0.00;
		$kdx_gravado                 = 0.00;
		$kdx_iva_gravado             = 0.00;
		$kdx_exento_a_terceros         = 0.00;
		$kdx_gravado_a_terceros     = 0.00;
		$kdx_iva_gravado_a_terceros = 0.00;
		$kdx_cuenta_ajena             = 0.00;
		$retencion_1_por_ciento     = 0.00;
		$kdx_vta_total                 = 0.00;
		$id_encabezado_comprobante  = $idVenta;
		$id_detalle                 = '';
		$id_catalogo_producto         = 0;
		$descripcion                 = '';
		$cantidad                     = 0.00;
		$precio_neto                 = 0.00;
		$venta_neta                 = 0.00;
		$tasaCero                     = $tasaCero;
		$exento                     = $exento;
		$perUnoPorCiento             = $percep1;
		$id_tc_producto             = '';
		$id_clasificacion_inv         = '';
		$id_tipo_venta                 = '';
		$id_tipo_mh                 = '';
		$id_presentacion             = '';
		$id_tipo_comprobante         = '';

		if (isset($DatosKardex)) {
			// var_dump('se ha encontrados datos genereados de forma automatica');
			//  var_dump($DatosKardex);

			$id_catalogo_producto         = $DatosKardex['id_catalogo_producto'];
			$descripcion                 = $DatosKardex['descripcion'];
			$cantidad                     = $DatosKardex['cantidad'];
			$precio_neto                 = $DatosKardex['precio_neto'];
			$venta_neta                 = $DatosKardex['venta_neta'];
			$id_tipo_venta                 = intval($DatosKardex['id_tipo_venta']); //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
			$id_tipo_mh                 = $DatosKardex['id_tipo_mh']; //afecto o gravado, excento, no sujeto
			$id_tipo_comprobante         = $DatosKardex['id_tipo_comprobante']; //1.CCF, 2.FCF, 3.NCR, 4.FEX
		} else {
			echo  'los datos del kardex no llegaron  ';
		}
		//echo  'el catalo del producto despues de asignar es ' . $id_catalogo_producto;




		// nueva codificacio
		if ($id_tipo_venta == 1) { //1.ventas propias
			if ($id_tipo_mh == 1) { //afecto o gravado
				if ($id_tipo_comprobante == 4) { // factura exportacion
					if ($tasaCero == 'NO' && $exento   = 'NO') { //si no es exento o tasa cero pasa a ser gravado por defecto
						var_dump("en exportacion es gravado!!!!!!!!!!!!!!!!!!");
						$kdx_gravado = $venta_neta;
						$kdx_iva_gravado = $venta_neta * 0.13;
						$kdx_vta_total   = round(($kdx_gravado + $kdx_iva_gravado), 2);
					} elseif ($tasaCero == 'NO' && $exento == 'SI') {
						$kdx_exento = $venta_neta;
						$kdx_vta_total = $kdx_exento;
					} elseif ($tasaCero == 'SI') {
						$kdx_exportacion = $venta_neta;
						$kdx_vta_total = $kdx_exportacion;
					}
				} elseif ($id_tipo_comprobante == 2) { // consumidor final
					if ($exento == 'SI') { //si cliente es exento
						$kdx_exento = $venta_neta;
						$kdx_vta_total = $kdx_exento;
					} else {
						$kdx_gravado = $venta_neta;
						$kdx_iva_gravado = $venta_neta * 0.13;
						$kdx_vta_total = round(($kdx_gravado + $kdx_iva_gravado), 2);
					}
				} else { // para credito fiscal

					if ($exento == 'SI') { //si cliente es exento
						$kdx_exento = $venta_neta;
						$kdx_vta_total = $kdx_exento;
					} else { //si no es exento o tasa cero pasa a ser gravado por defecto
						var_dump("gravado");
						$kdx_gravado = $venta_neta;
						$kdx_iva_gravado = $venta_neta * 0.13;
						$kdx_vta_total = round(($kdx_gravado + $kdx_iva_gravado), 2);
					}
				}
			} elseif ($id_tipo_mh == 2) { //excento tipo  de movimiento  del producto


				if ($id_tipo_comprobante == 4) { // factura exportacion
					$kdx_exportacion = $venta_neta;
					$kdx_vta_total = $kdx_exportacion;
				} else {
					$kdx_exento = $venta_neta;
					$kdx_vta_total = $venta_neta;
				}
			} else { //no sujeto

				$kdx_no_sujeto = $venta_neta;
				$kdx_vta_total = $venta_neta;
			}
		} elseif ($id_tipo_venta == 2) { //2.ventas a terceros
			if ($id_tipo_mh == 1) { //afecto o gravado
				if ($exento == 'SI') { //si cliente es exento
					$kdx_exento_a_terceros = $venta_neta;
					$kdx_vta_total = $kdx_exento;
				} else { //si no es exento o tasa cero pasa a ser gravado por defecto
					$kdx_gravado_a_terceros = $venta_neta;
					$kdx_iva_gravado_a_terceros = $venta_neta * 0.13;
					$kdx_vta_total = round(($kdx_gravado_a_terceros + $kdx_iva_gravado_a_terceros), 2);
				}
			} elseif ($id_tipo_mh == 2) { //excento
				$kdx_exento_a_terceros = $venta_neta;
				$kdx_vta_total = $venta_neta;
			} else { //no sujeto
				//$kdx_no_sujeto = $venta_neta;
			}
		} else { //3.ventas_ajenas
			$kdx_cuenta_ajena = $venta_neta;
			$kdx_vta_total = $venta_neta;
		}

		if ($perUnoPorCiento == 'SI' && $kdx_gravado > 100) {
			$retencion_1_por_ciento = ($kdx_gravado + $kdx_iva_gravado) * 0.01;
		}
		$id_detalle = null;


		$data = array(
			'id_encabezado_comprobante' => $id_encabezado_comprobante,
			'no_guia'                   => 0, //nwarehouse
			'id_catalogo_producto'      => $id_catalogo_producto,
			'descripcion'               => $descripcion,
			'cantidad'                  => $cantidad,
			'precio_neto'               => $precio_neto,
			'venta_neta'                => $venta_neta,
			'id_tipo_venta'             => $id_tipo_venta,
			'kdx_exento'                => $kdx_exento,
			'kdx_exportacion'           => $kdx_exportacion,
			'kdx_no_sujeto'             => $kdx_no_sujeto,
			'kdx_gravado'               => $kdx_gravado,
			'kdx_iva_gravado'           => $kdx_iva_gravado,
			'kdx_exento_a_terceros'     => $kdx_exento_a_terceros,
			'kdx_gravado_a_terceros'    => $kdx_gravado_a_terceros,
			'kdx_iva_gravado_a_terceros' => $kdx_iva_gravado_a_terceros,
			'kdx_cuenta_ajena'          => $kdx_cuenta_ajena,
			'kdx_vta_total'             => $kdx_vta_total

		);
		//var_dump('Antes de guardar en el kardex');

		// var_dump($data);
		$this->Ventas_Model->guardar_detalle_venta($data, $id_detalle);
	}

	//Recibe la información de cliente
	public function prealerta_post()
	{

		$data = $this->post();
		//var_dump($data);

		$datos = array(
			'nombre' => $data["nombre"],
			'tracking_number' => $data['tracking_number'],
			'correo' => $data['correo'],
			'telefono' => $data['telefono'],
			'dui' => $data['dui'],
			'commodity' => $data['commodity'],
			'direccion' => $data['direccion']
		);

		$dui = $this->Manifiesto_model->verificar_dui($data['dui']);
		// update, departamento, dui, municipio y agencia 

		if (count($dui) == 0) {
			$this->Manifiesto_model->prealerta($datos);
			$this->sendEmail($datos);
			echo "No existe el dui";
		} else {
			echo "Dui encontrado!";
		}
	}
	//recibe valores de dui y nombre de cliente
	public function identidad_personal_post()
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$data = $this->post();

		$datos = array(
			'nombre' => $data['nombre'],
			'dui' => $data['dui'],
		);

		$this->Manifiesto_model->identidad_personal($datos);

		$idDepartamento = 0;
		$idMunicipio =  0;
		$id_sucursal = 0;

		//si sucursal > 0 realizar una consulta a la tabla agencias el valor que traiga la sucursal
		if ($data["sucursal"] > 0) {
			$result = $this->Manifiesto_model->buscar_sucursal($data["sucursal"]);

			$idDepartamento =  $result->id_departamento;

			$idMunicipio =  $result->id_municipio;

			$id_sucursal = $data["sucursal"];
		} else {
			$idDepartamento = $data['departamento'];
			$idMunicipio = $data['municipio'];
		}


		$this->Manifiesto_model->update_prealerta($data['dui'], $idDepartamento, $idMunicipio, $id_sucursal, $data['tracking']);
	}




	public function sendEmail($datos)
	{

		$nombre = $datos["nombre"];
		$correo = $datos["correo"];
		$tracking_number = $datos["tracking_number"];
		$commodity = $datos["commodity"];
		$telefono = $datos["telefono"];
		$dui = $datos["dui"];
		$direccion = $datos["direccion"];


		$msj = '<!DOCTYPE html>
        
                                <html lang="en">
                                <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Document</title>
                                </head>
                                <body>
                                <p>Estimado ' . $nombre . '</p>
                                <p>Te notificamos que hemos recibido un paquete de una compra procedente de AliExpress.</p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Tracking number : ' . $tracking_number . '</span></p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Descripcion de la compra :' . $commodity . ' </span></p>
                                <p>* Puedes restrear tu envio desde AliExpress o en el siguiente enlace <a href="https://mailamericas.com/ ">https://mailamericas.com/</a> usando tu número de tracking</p>
                                <p>Para completar el proceso de la liberación de tu(s) compra(s) necesitamos que nos ayudes llenado el <a href="">formulario</a> para efectos del tramite aduanal y poder aplicar al beneficio de la Ley de "Facilitación de Compras en Linea" que por compras personales menores a $300.00 no pagas DAI (Derecho Arancelario a la Importación) ni permisos especiales*. <br>
                                Una vez liberado, puedes recogerlo en cualquier de nuestras oficinas o para tu comodidad, enviártelo hasta tu domicilio o lugar de trabajo.
                                </p><p>NOTA IMPORTANTE:</p>
                                <p>- Por tu seguridad, el pago será únicamente al momento de la entrega de tu paquete y deberá ser en efectivo. Nunca te pediremos que deposites anticipadamente en una cuenta bancaria o que nos brindes datos de alguna tarjeta de crédito o débito.</p>
                                <br>
                                <br>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">PBX:</span> (503) 2537-7200</p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">WhatsApp:</span> (503) 7150-9535 o al (503) 6034-6037</p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Web:</span> <a href="https://mistarship.com/agencias/">mistarship.com</a></p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Facebook:</span> /StarshipSV</p>
                                <br>
                                <a href="https://stargates.site/form/?tracking_get=' . $tracking_number . '&dui=' . $dui . '&correo=' . $correo . '&tel=' . $telefono . '&commodity=' . $commodity . '&nombre=' . $nombre . '&direccion=' . $direccion . '" style="text-decoration: dashed;background-color: #ff6a40;width: 260px;color: #ffffff !important;margin-top: 10px;border: 1px solid transparent;border-radius: 4px;cursor: pointer;display: inline-block;font-size: 16px;font-weight: 400;line-height: 1.5;padding: 6px 12px;position: relative;text-align: center;transition: background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;-webkit-user-select: none;-moz-user-select: none;user-select: none;vertical-align: middle;white-space: nowrap;">Llenar formulario acá</a>
                                </body>
                                </html>';

		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		$this->load->library('email');
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'localhost',
			'smtp_port' => 25,
			'smtp_user' => 'adminserver@stargates.site',
			'smtp_pass' => 'adminserver2022',
			'mailtype' => 'html',
			'charset' => 'utf-8',
		);
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
		$this->email->from('servicioalcliente7@mistarship.com', 'StarShip');
		$this->email->to($correo);
		$this->email->subject('Notificacion de StarShip');
		$this->email->message($msj);
		if ($this->email->send()) {
			echo 'Your email was sent.';
		} else {
			echo $correo . " - NO ENVIADO<br>";
		}
		$this->email->clear(true);
	}

	public function upatetracking_post()
	{

		$data = $this->post(); //contiene los datos

		$data =  file_get_contents('php://input', true);
		//  echo $data ;
		$row = json_decode($data);
		$tracking_number = $row->tracking_number;


		$rsl = $this->Exportacion_Model->update_facturadoUSPS($tracking_number);
		echo $rsl;
	}


	public function consignee_get()
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
		$warehouse = $this->Warehouse_model->get_tracking($id);

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


	public function comparar_post()
	{
		$data = $this->post();
		$tracking = $data['tracking'];

		$result =    $this->Exportacion_Model->comparar($tracking);
		echo json_encode($result);
	}

	public function estadosApg_post()
	{
		$this->load->model('Entrega_model');
		$this->load->model('Manifiesto_model');
		$this->load->model('DTES_Model');
		$this->load->library('TokenApg');

		try {
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);

			date_default_timezone_set("America/El_Salvador");
			header('Content-Type: application/json');

			$rawData = file_get_contents('php://input');
			$row     = json_decode($rawData, true);

			if (!$row || !is_array($row)) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Formato JSON inválido.'
				]);
				return;
			}

			$entregado         = 'NO';
			$codigo_recibido   = $row['codigo'] ?? null;
			$tracking_recibido = $row['guia']   ?? null;
			$fecha_response    = $row['fecha']  ?? null; // 'Y-m-d H:i:s' esperado

			if (!$codigo_recibido || !$tracking_recibido) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Faltan datos requeridos: codigo o guia.'
				]);
				return;
			}

			// Normaliza/valida fecha
			$dtBase = DateTime::createFromFormat('Y-m-d H:i:s', (string)$fecha_response);
			if (!$dtBase) {
				$dtBase = new DateTime((string)$fecha_response); // fallback ISO u otros
			}
			$fecha_iso = $dtBase->format(DateTime::ATOM);

			// Verifica tracking
			$request_tracking = $this->Manifiesto_model->obtener_tracking($tracking_recibido);
			if (!$request_tracking) {
				http_response_code(404);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => "Tracking {$tracking_recibido} no encontrado."
				]);
				return;
			}

			// Respuesta inmediata
			$this->_responderInmediatamente([
				'error' => false,
				'message' => 'Recibido correctamente.',
				'tracking' => $tracking_recibido
			]);

			// Anti-duplicados
			$existe = $this->Manifiesto_model->verificar_pod($tracking_recibido, $codigo_recibido);
			if ($existe) {
				$info = [
					"tracking_number" => $tracking_recibido,
					"id_estatus"      => $codigo_recibido,
					"fecha"           => date("Y-m-d H:i:s")
				];
				$this->Manifiesto_model->guardar_alerta($info);
				$this->_registrarEventoInterno(
					'Ya existe un registro con estatus 15 para ese tracking.',
					$tracking_recibido,
					'repetido',
					null,
					$codigo_recibido,
					"",
					$entregado
				);
				return;
			}

			$existe_ro = $this->Manifiesto_model->verificar_Ro($tracking_recibido, $codigo_recibido);
			if ($existe_ro) {
				$this->_registrarEventoInterno(
					'Ya existe un registro con estatus 311 para ese tracking.',
					$tracking_recibido,
					'Repetido',
					null,
					$codigo_recibido,
					"",
					$entregado
				);
				return;
			}

			// ======================
			// HELPERS (closures y utilitarios)
			// ======================

			// Inserta evento en detalle y actualiza encabezado + DTES
			$insertarEvento = function (int $id_estatus, DateTime $dt, string $problemaGestion = '', string $descripcion = '') use ($tracking_recibido) {
				$fechah  = $dt->format('Y-m-d H:i:s');
				$fechais = $dt->format(DateTime::ATOM);

				// Actualiza encabezado con el estatus actual
				$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);

				// Inserta detalle
				$datos = [
					'item'             => 1,
					'fecha'            => $fechah,
					'warehouse'        => $tracking_recibido,
					'id_estatus'       => $id_estatus,
					'casillero'        => '100',
					'id_pais'          => 1,
					'pod_nombre'       => '',
					'pod_valor'        => '',
					'json_string'      => json_encode(['guia' => '']),
					'ruta_foto'        => '',
					'fechaiso'         => $fechais,
					'problema_gestion' => ($descripcion !== '' ? $descripcion : $problemaGestion),
				];
				$this->Manifiesto_model->guardar_estatus($datos);

				// Sincroniza a tu otro sistema interno
				$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);
			};

			// Resuelve statusName según tu regla (por código o por descripción)
			$resolverStatusName = function (int $id_estatus, string $problemaGestion = '') {
				if ($id_estatus === 16 || $id_estatus === 19) {
					$r = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
					return $r ? $r[0]['codigo_cliente'] : '';
				} else {
					$r = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
					return $r ? $r[0]['codigo_cliente'] : '';
				}
			};

			// ====== Helper: Encolar en APG Outbox cuando no se pudo enviar ======
			$enqueueOutbox = function (string $tracking, int $id_estatus, string $statusName, DateTime $dt, array $extra = []) {
				$fechaEnvio = $dt->format('Y-m-d H:i:s');
				$payload = [
					'tracking' => $tracking,
					'status'   => [[
						'statusName' => $statusName,
						'location'   => 'El Salvador',
						'statusDate' => $fechaEnvio,
						'pod'        => null,
					]],
				];
				$this->Entrega_model->enqueueOutbox(
					$tracking,
					$id_estatus,
					$statusName,
					$fechaEnvio,
					$payload,
					$extra
				);
			};

			// ====== CLOSURE 1: Handle cURL reusado para APG ======
			$apgUrl = 'https://apiprod.apg-global.com/api/account/save-status';
			$crearHandleApg = function (string $token) use ($apgUrl) {
				$authHeader = (stripos($token, 'bearer ') === 0) ? $token : ('Bearer ' . $token);
				$ch = curl_init($apgUrl);
				curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER    => true,
					CURLOPT_POST              => true,
					CURLOPT_HTTPHEADER        => [
						'Authorization: ' . $authHeader,
						'Content-Type: application/json',
						'Accept: application/json',
						'Expect:',
						'Connection: keep-alive',
						'Keep-Alive: timeout=5, max=1000',
					],
					CURLOPT_TIMEOUT           => 60,   // total
					CURLOPT_CONNECTTIMEOUT    => 20,   // conexión
					CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
					CURLOPT_NOSIGNAL          => true,
					CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
					CURLOPT_DNS_CACHE_TIMEOUT => 300,
					CURLOPT_FORBID_REUSE      => false,
					CURLOPT_FRESH_CONNECT     => false,
				]);
				return $ch;
			};

			// ====== CLOSURE 2: Envío a APG con reintentos/backoff + outbox si falla ======
			$enviarApg = function ($ch, int $id_estatus, string $tracking_recibido, string $statusName, string $fechaEnvio, string $problemaGestion) use (&$entregado) {
				$payload = [
					'tracking' => $tracking_recibido,
					'status'   => [[
						'statusName' => $statusName,
						'location'   => 'El Salvador',
						'statusDate' => $fechaEnvio, // 'Y-m-d H:i:s'
						'pod'        => null,
					]],
				];
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

				$maxAttempts     = 4; // 1 intento + 3 reintentos
				$attempt         = 0;
				$respuesta_apg   = null;
				$ok              = false;

				// Para auditar y encolar si todo falla
				$lastCurlNo      = null;
				$lastCurlErr     = null;
				$lastHttp        = null;
				$lastBody        = null;

				$retryableCurl = [6, 7, 28, 52, 55, 56];               // resolve/connect/timeout/got nothing/send/recv
				$retryableHttp = [408, 429, 500, 502, 503, 504, 520];   // timeouts, rate limit, 5xx

				while ($attempt < $maxAttempts && !$ok) {
					$attempt++;

					$resp        = curl_exec($ch);
					$lastCurlErr = curl_error($ch);
					$lastCurlNo  = curl_errno($ch);
					$lastHttp    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					$lastBody    = $resp;

					if ($lastCurlErr) {
						$esRetry = in_array($lastCurlNo, $retryableCurl, true);
						$this->_registrarEventoInterno(
							"APG cURL intento {$attempt}" . ($esRetry ? " (retry)" : ""),
							$tracking_recibido,
							$esRetry ? 'warning' : 'fallido',
							['curl_errno' => $lastCurlNo, 'curl_error' => $lastCurlErr],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						if ($esRetry && $attempt < $maxAttempts) {
							$ms = (int) (500 * pow(2, $attempt - 1) + random_int(0, 250));
							usleep($ms * 1000);
							continue;
						}
						break; // no retryable o agotado
					}

					if ($lastHttp < 200 || $lastHttp >= 300) {
						$esRetry = in_array($lastHttp, $retryableHttp, true);
						$this->_registrarEventoInterno(
							"APG HTTP {$lastHttp} intento {$attempt}" . ($esRetry ? " (retry)" : ""),
							$tracking_recibido,
							$esRetry ? 'warning' : 'fallido',
							['http_code' => $lastHttp, 'body' => $lastBody],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						if ($esRetry && $attempt < $maxAttempts) {
							$ms = (int) (600 * pow(2, $attempt - 1) + random_int(0, 300));
							usleep($ms * 1000);
							continue;
						}
						break;
					}

					// 2xx: parseamos
					$respuesta_apg = json_decode($resp, true);
					if (json_last_error() !== JSON_ERROR_NONE) {
						$this->_registrarEventoInterno(
							"APG JSON inválido intento {$attempt}",
							$tracking_recibido,
							'fallido',
							['http_code' => $lastHttp, 'body' => $lastBody, 'json_error' => json_last_error_msg()],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						break;
					}

					// Éxito lógico
					$isSuccess       = $respuesta_apg['isSuccess'] ?? null;
					$respOK          = $respuesta_apg['objectResult']['response'] ?? null;
					$respStatusField = $respuesta_apg['objectResult']['status'][0]['response'] ?? null;

					if ($respOK === 'OK' || $respStatusField === 'Created' || $isSuccess === true) {
						$entregado = 'OK';
					}

					// Auditoría del intento exitoso
					$this->_registrarEventoInterno(
						"APG OK intento {$attempt} (estatus {$id_estatus})",
						$tracking_recibido,
						'entregado',
						['http_code' => $lastHttp, 'resp' => $respuesta_apg],
						$id_estatus,
						$problemaGestion,
						$entregado
					);

					// Si deseas guardar la respuesta en detalle, descomenta:
					// $this->_updateRespuesta($tracking_recibido, $respuesta_apg, $id_estatus, $entregado, $problemaGestion, null);

					$ok = true;
				} // fin while

				// Si agotó intentos, encola para reintento por worker
				if (!$ok) {
					$this->Entrega_model->enqueueOutbox(
						$tracking_recibido,
						$id_estatus,
						$statusName,
						$fechaEnvio,
						$payload,
						[
							'curl_errno' => $lastCurlNo,
							'curl_error' => $lastCurlErr,
							'http_code'  => $lastHttp,
							'body'       => $lastBody
						]
					);
				}

				return $ok ? $respuesta_apg : null;
			};

			// ======================
			// LÓGICA PRINCIPAL
			// ======================
			$id_estatus_original = (int)$codigo_recibido;

			// *** VALIDACIÓN NUEVA ***
			// Si llega 19: revisa si YA existe algún 19 para este tracking.
			$yaTiene19 = false;
			if ($id_estatus_original === 19) {
				$yaTiene19 = $this->db
					->select('1')
					->from('detalle')
					->where('warehouse', $tracking_recibido)
					->where('id_estatus', 19)
					->limit(1)
					->get()
					->num_rows() > 0;
			}

			// FLUJO ESPECIAL para 19
			if ($id_estatus_original === 19) {

				if ($yaTiene19) {
					// ---- SOLO 19 (sin 219 ni 10) ----
					$descripcion_19     = $row['observaciones'] ?? '';
					$problemaGestion_19 = $descripcion_19;

					// Insertar evento 19 con fecha base (sin offset)
					$insertarEvento(19, $dtBase, $problemaGestion_19, $descripcion_19);

					// Token + envío APG SOLO 19 (si no hay token, encola)
					$sn19  = $resolverStatusName(19, $problemaGestion_19);
					$token = $this->tokenapg->getToken();
					if (!$token) {
						$enqueueOutbox($tracking_recibido, 19, $sn19, $dtBase, ['reason' => 'token_unavailable']);
						$this->_registrarEventoInterno(
							'Token APG no disponible: evento 19 encolado',
							$tracking_recibido,
							'warning',
							[],
							19,
							$problemaGestion_19,
							$entregado
						);
						return;
					}

					$ch = $crearHandleApg($token);
					$enviarApg($ch, 19, $tracking_recibido, $sn19, $dtBase->format('Y-m-d H:i:s'), $problemaGestion_19);
					curl_close($ch);
					return;
				}

				// ---- CADENA COMPLETA: 219 (t), 10 (t+2), 19 (t+4) ----
				$dt219 = clone $dtBase;                         // 219 fecha base
				$dt10  = (clone $dtBase)->modify('+2 minutes'); // 10  +2
				$dt19  = (clone $dtBase)->modify('+4 minutes'); // 19  +4

				// Inserciones locales
				$insertarEvento(219, $dt219);
				$insertarEvento(10,  $dt10);
				$descripcion_19     = $row['observaciones'] ?? '';
				$problemaGestion_19 = $descripcion_19;          // para 19 usas observaciones
				$insertarEvento(19,  $dt19, $problemaGestion_19, $descripcion_19);

				// Resolver statusName ANTES del token
				$sn219 = $resolverStatusName(219, '');
				$sn10  = $resolverStatusName(10,  '');
				$sn19  = $resolverStatusName(19,  $problemaGestion_19);

				// Token y handle reusado (si no hay token, encola los tres)
				$token = $this->tokenapg->getToken();
				if (!$token) {
					$enqueueOutbox($tracking_recibido, 219, $sn219, $dt219, ['reason' => 'token_unavailable']);
					$enqueueOutbox($tracking_recibido, 10,  $sn10,  $dt10,  ['reason' => 'token_unavailable']);
					$enqueueOutbox($tracking_recibido, 19,  $sn19,  $dt19,  ['reason' => 'token_unavailable']);

					$this->_registrarEventoInterno(
						'Token APG no disponible: cadena 219/10/19 encolada',
						$tracking_recibido,
						'warning',
						[],
						19,
						$problemaGestion_19,
						$entregado
					);
					return;
				}

				$ch = $crearHandleApg($token);

				// Envíos granulares
				$enviarApg($ch, 219, $tracking_recibido, $sn219, $dt219->format('Y-m-d H:i:s'), '');
				$enviarApg($ch, 10,  $tracking_recibido, $sn10,  $dt10->format('Y-m-d H:i:s'),  '');
				$enviarApg($ch, 19,  $tracking_recibido, $sn19,  $dt19->format('Y-m-d H:i:s'),  $problemaGestion_19);

				curl_close($ch);
				return;
			}

			// --------- FLUJO NORMAL (no es 19) ----------
			$problemaGestion = "";
			$id_estatus      = (int)$row['codigo'];
			$description     = "";

			if ($id_estatus == 16 || $id_estatus == 19) {
				$description     = ($row['observaciones'] ?? '');
				$problemaGestion = $row['razon']['descripcion'] ?? '';
			}

			$devolucion = false;
			if ($id_estatus == 15 && ($row['devolucion'] ?? 0) == 1) {
				$id_estatus = 311;
				$devolucion = true;
			}

			if ($id_estatus == 19) {
				$problemaGestion = ($row['observaciones'] ?? '');
			}

			// Encabezado + detalle
			$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);
			$datos = [
				'item'             => 1,
				'fecha'            => $dtBase->format('Y-m-d H:i:s'),
				'warehouse'        => $tracking_recibido,
				'id_estatus'       => $id_estatus,
				'casillero'        => '100',
				'id_pais'          => 1,
				'pod_nombre'       => '',
				'pod_valor'        => "",
				'json_string'      => json_encode(['guia' => '']),
				'ruta_foto'        => '',
				'fechaiso'         => $fecha_iso,
				'problema_gestion' => $description
			];
			$this->Manifiesto_model->guardar_estatus($datos);
			$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);

			// statusName
			if ($id_estatus == 16 || $id_estatus == 19) {
				$resultado  = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			} else {
				$resultado  = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			}
			if ($devolucion) {
				$statusName = "RO";
			}

			// Enviar a APG (flujo normal) con cliente reusado; si no hay token, encola
			$token = $this->tokenapg->getToken();
			if ($token) {
				$ch = $crearHandleApg($token);
				$enviarApg($ch, $id_estatus, $tracking_recibido, $statusName, $dtBase->format('Y-m-d H:i:s'), $problemaGestion);
				curl_close($ch);
			} else {
				$enqueueOutbox($tracking_recibido, $id_estatus, $statusName, $dtBase, ['reason' => 'token_unavailable']);
				$this->_registrarEventoInterno(
					'Token APG no disponible: evento encolado',
					$tracking_recibido,
					'warning',
					[],
					$id_estatus,
					$problemaGestion,
					$entregado
				);
			}
		} catch (Throwable $e) {
			error_log('Error inesperado en estadosApg_post: ' . $e->getMessage());
			http_response_code(500);
			$this->_responderInmediatamente([
				'error'   => true,
				'mensaje' => 'Error interno del servidor.',
				'detalle' => $e->getMessage()
			]);
		}
	}


	public function estadosApg_bueno_post()
	{
		$this->load->model('Entrega_model');
		$this->load->model('Manifiesto_model');
		$this->load->model('DTES_Model');
		$this->load->library('TokenApg');

		try {
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);

			date_default_timezone_set("America/El_Salvador");
			header('Content-Type: application/json');

			$rawData = file_get_contents('php://input');
			$row     = json_decode($rawData, true);

			if (!$row || !is_array($row)) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Formato JSON inválido.'
				]);
				return;
			}

			$entregado         = 'NO';
			$codigo_recibido   = $row['codigo'] ?? null;
			$tracking_recibido = $row['guia']   ?? null;
			$fecha_response    = $row['fecha']  ?? null; // 'Y-m-d H:i:s' esperado

			if (!$codigo_recibido || !$tracking_recibido) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Faltan datos requeridos: codigo o guia.'
				]);
				return;
			}

			// Normaliza/valida fecha
			$dtBase = DateTime::createFromFormat('Y-m-d H:i:s', (string)$fecha_response);
			if (!$dtBase) {
				$dtBase = new DateTime((string)$fecha_response); // fallback ISO u otros
			}
			$fecha_iso = $dtBase->format(DateTime::ATOM);

			// Verifica tracking
			$request_tracking = $this->Manifiesto_model->obtener_tracking($tracking_recibido);
			if (!$request_tracking) {
				http_response_code(404);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => "Tracking {$tracking_recibido} no encontrado."
				]);
				return;
			}

			// Respuesta inmediata
			$this->_responderInmediatamente([
				'error' => false,
				'message' => 'Recibido correctamente.',
				'tracking' => $tracking_recibido
			]);

			// Anti-duplicados
			$existe = $this->Manifiesto_model->verificar_pod($tracking_recibido, $codigo_recibido);
			if ($existe) {
				$info = [
					"tracking_number" => $tracking_recibido,
					"id_estatus"      => $codigo_recibido,
					"fecha"           => date("Y-m-d H:i:s")
				];
				$this->Manifiesto_model->guardar_alerta($info);
				$this->_registrarEventoInterno(
					'Ya existe un registro con estatus 15 para ese tracking.',
					$tracking_recibido,
					'repetido',
					null,
					$codigo_recibido,
					"",
					$entregado
				);
				return;
			}

			$existe_ro = $this->Manifiesto_model->verificar_Ro($tracking_recibido, $codigo_recibido);
			if ($existe_ro) {
				$this->_registrarEventoInterno(
					'Ya existe un registro con estatus 311 para ese tracking.',
					$tracking_recibido,
					'Repetido',
					null,
					$codigo_recibido,
					"",
					$entregado
				);
				return;
			}

			// ======================
			// HELPERS (aquí van tus closures y utilitarios)
			// ======================

			// Inserta evento en detalle y actualiza encabezado + DTES
			$insertarEvento = function (int $id_estatus, DateTime $dt, string $problemaGestion = '', string $descripcion = '') use ($tracking_recibido) {
				$fechah  = $dt->format('Y-m-d H:i:s');
				$fechais = $dt->format(DateTime::ATOM);

				// Actualiza encabezado con el estatus actual
				$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);

				// Inserta detalle
				$datos = [
					'item'             => 1,
					'fecha'            => $fechah,
					'warehouse'        => $tracking_recibido,
					'id_estatus'       => $id_estatus,
					'casillero'        => '100',
					'id_pais'          => 1,
					'pod_nombre'       => '',
					'pod_valor'        => '',
					'json_string'      => json_encode(['guia' => '']),
					'ruta_foto'        => '',
					'fechaiso'         => $fechais,
					'problema_gestion' => ($descripcion !== '' ? $descripcion : $problemaGestion),
				];
				$this->Manifiesto_model->guardar_estatus($datos);

				// Sincroniza a tu otro sistema interno
				$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);
			};

			// Resuelve statusName según tu regla (por código o por descripción)
			$resolverStatusName = function (int $id_estatus, string $problemaGestion = '') {
				if ($id_estatus === 16 || $id_estatus === 19) {
					$r = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
					return $r ? $r[0]['codigo_cliente'] : '';
				} else {
					$r = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
					return $r ? $r[0]['codigo_cliente'] : '';
				}
			};

			// ====== CLOSURE 1: Crea un handle cURL para APG (cliente reusado) ======
			$apgUrl = 'https://apiprod.apg-global.com/api/account/save-status';
			$crearHandleApg = function (string $token) use ($apgUrl) {
				$authHeader = (stripos($token, 'bearer ') === 0) ? $token : ('Bearer ' . $token);
				$ch = curl_init($apgUrl);
				curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER    => true,
					CURLOPT_POST              => true,
					CURLOPT_HTTPHEADER        => [
						'Authorization: ' . $authHeader,
						'Content-Type: application/json',
						'Accept: application/json',
						'Expect:',
						'Connection: keep-alive',
						'Keep-Alive: timeout=5, max=1000',
					],
					CURLOPT_TIMEOUT           => 60,   // total
					CURLOPT_CONNECTTIMEOUT    => 20,   // conexión
					CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
					CURLOPT_NOSIGNAL          => true,
					CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
					CURLOPT_DNS_CACHE_TIMEOUT => 300,
					CURLOPT_FORBID_REUSE      => false,
					CURLOPT_FRESH_CONNECT     => false,
					// SSL verificación ON por seguridad. Para pruebas extremas:
					// CURLOPT_SSL_VERIFYPEER     => true,
					// CURLOPT_SSL_VERIFYHOST     => 2,
				]);
				return $ch;
			};

			// ====== CLOSURE 2: Enviar a APG con reintentos/backoff, usando el handle reusado ======
			$enviarApg = function ($ch, int $id_estatus, string $tracking_recibido, string $statusName, string $fechaEnvio, string $problemaGestion) use (&$entregado) {
				$payload = [
					'tracking' => $tracking_recibido,
					'status'   => [[
						'statusName' => $statusName,
						'location'   => 'El Salvador',
						'statusDate' => $fechaEnvio, // 'Y-m-d H:i:s'
						'pod'        => null,
					]],
				];
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

				// Política de reintentos
				$maxAttempts   = 4; // 1 intento + 3 reintentos
				$attempt       = 0;
				$respuesta_apg = null;
				$ok            = false;

				$retryableCurl = [6, 7, 28, 52, 55, 56];               // resolve/connect/timeout/got nothing/send/recv
				$retryableHttp = [408, 429, 500, 502, 503, 504, 520];   // timeouts, rate limit, 5xx

				while ($attempt < $maxAttempts && !$ok) {
					$attempt++;

					$resp    = curl_exec($ch);
					$curlErr = curl_error($ch);
					$curlNo  = curl_errno($ch);
					$http    = curl_getinfo($ch, CURLINFO_HTTP_CODE);

					if ($curlErr) {
						$esRetry = in_array($curlNo, $retryableCurl, true);
						$this->_registrarEventoInterno(
							"APG cURL intento {$attempt}" . ($esRetry ? " (retry)" : ""),
							$tracking_recibido,
							$esRetry ? 'warning' : 'fallido',
							['curl_errno' => $curlNo, 'curl_error' => $curlErr],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						if ($esRetry && $attempt < $maxAttempts) {
							$ms = (int) (500 * pow(2, $attempt - 1) + random_int(0, 250));
							usleep($ms * 1000);
							continue;
						}
						break; // no retryable o agotado
					}

					if ($http < 200 || $http >= 300) {
						$esRetry = in_array($http, $retryableHttp, true);
						$this->_registrarEventoInterno(
							"APG HTTP {$http} intento {$attempt}" . ($esRetry ? " (retry)" : ""),
							$tracking_recibido,
							$esRetry ? 'warning' : 'fallido',
							['http_code' => $http, 'body' => $resp],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						if ($esRetry && $attempt < $maxAttempts) {
							$ms = (int) (600 * pow(2, $attempt - 1) + random_int(0, 300));
							usleep($ms * 1000);
							continue;
						}
						break;
					}

					// 2xx: parseamos
					$respuesta_apg = json_decode($resp, true);
					if (json_last_error() !== JSON_ERROR_NONE) {
						$this->_registrarEventoInterno(
							"APG JSON inválido intento {$attempt}",
							$tracking_recibido,
							'fallido',
							['http_code' => $http, 'body' => $resp, 'json_error' => json_last_error_msg()],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						break;
					}

					// Éxito lógico
					$isSuccess       = $respuesta_apg['isSuccess'] ?? null;
					$respOK          = $respuesta_apg['objectResult']['response'] ?? null;
					$respStatusField = $respuesta_apg['objectResult']['status'][0]['response'] ?? null;

					if ($respOK === 'OK' || $respStatusField === 'Created' || $isSuccess === true) {
						$entregado = 'OK';
					}

					// Auditoría del intento exitoso
					$this->_registrarEventoInterno(
						"APG OK intento {$attempt} (estatus {$id_estatus})",
						$tracking_recibido,
						'entregado',
						['http_code' => $http, 'resp' => $respuesta_apg],
						$id_estatus,
						$problemaGestion,
						$entregado
					);

					// Persistir respuesta por estatus
					// $this->_updateRespuesta(
					// 	$tracking_recibido,
					// 	$respuesta_apg,
					// 	$id_estatus,
					// 	$entregado,
					// 	$problemaGestion,
					// 	null // opcional: puedes pasar json_encode($row) si quieres
					// );

					$ok = true;
				}

				return $ok ? $respuesta_apg : null;
			};

			// ======================
			// LÓGICA PRINCIPAL
			// ======================
			$id_estatus_original = (int)$codigo_recibido;

			// FLUJO ESPECIAL: 19 => 219 (t), 10 (t+2), 19 (t+4) + push granular a APG
			if ($id_estatus_original === 19) {
				$dt219 = clone $dtBase;                        // 219 fecha base
				$dt10  = (clone $dtBase)->modify('+2 minutes'); // 10  +2
				$dt19  = (clone $dtBase)->modify('+4 minutes'); // 19  +4

				// Inserciones locales
				$insertarEvento(219, $dt219);
				$insertarEvento(10,  $dt10);
				$descripcion_19     = $row['observaciones'] ?? '';
				$problemaGestion_19 = $descripcion_19;        // para 19 usas observaciones
				$insertarEvento(19,  $dt19, $problemaGestion_19, $descripcion_19);

				// Token y handle reusado
				$token = $this->tokenapg->getToken();
				if (!$token) {
					$this->_registrarEventoInterno(
						'No se pudo obtener el token de APG.',
						$tracking_recibido,
						'error',
						[],
						19,
						$problemaGestion_19,
						$entregado
					);
					return;
				}
				$ch = $crearHandleApg($token);

				// 219 -> statusName por código
				$sn219 = $resolverStatusName(219, '');
				$enviarApg($ch, 219, $tracking_recibido, $sn219, $dt219->format('Y-m-d H:i:s'), '');

				// 10  -> statusName por código
				$sn10 = $resolverStatusName(10, '');
				$enviarApg($ch, 10, $tracking_recibido, $sn10, $dt10->format('Y-m-d H:i:s'), '');

				// 19  -> statusName por descripción (observaciones)
				$sn19 = $resolverStatusName(19, $problemaGestion_19);
				$enviarApg($ch, 19, $tracking_recibido, $sn19, $dt19->format('Y-m-d H:i:s'), $problemaGestion_19);

				curl_close($ch);
				return;
			}

			// --------- FLUJO NORMAL (no es 19) ----------
			$problemaGestion = "";
			$id_estatus      = (int)$row['codigo'];
			$description     = "";

			if ($id_estatus == 16 || $id_estatus == 19) {
				$description     = ($row['observaciones'] ?? '');
				$problemaGestion = $row['razon']['descripcion'] ?? '';
			}

			$devolucion = false;
			if ($id_estatus == 15 && ($row['devolucion'] ?? 0) == 1) {
				$id_estatus = 311;
				$devolucion = true;
			}

			if ($id_estatus == 19) {
				$problemaGestion = ($row['observaciones'] ?? '');
			}

			// Encabezado + detalle
			$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);
			$datos = [
				'item'             => 1,
				'fecha'            => $dtBase->format('Y-m-d H:i:s'),
				'warehouse'        => $tracking_recibido,
				'id_estatus'       => $id_estatus,
				'casillero'        => '100',
				'id_pais'          => 1,
				'pod_nombre'       => '',
				'pod_valor'        => "",
				'json_string'      => json_encode(['guia' => '']),
				'ruta_foto'        => '',
				'fechaiso'         => $fecha_iso,
				'problema_gestion' => $description
			];
			$this->Manifiesto_model->guardar_estatus($datos);
			$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);

			// statusName
			if ($id_estatus == 16 || $id_estatus == 19) {
				$resultado  = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			} else {
				$resultado  = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			}
			if ($devolucion) {
				$statusName = "RO";
			}

			// Enviar a APG (flujo normal) con cliente reusado
			$token = $this->tokenapg->getToken();
			if ($token) {
				$ch = $crearHandleApg($token);
				$enviarApg($ch, $id_estatus, $tracking_recibido, $statusName, $dtBase->format('Y-m-d H:i:s'), $problemaGestion);
				curl_close($ch);
			} else {
				$this->_registrarEventoInterno(
					'No se pudo obtener el token de APG.',
					$tracking_recibido,
					'error',
					[],
					$id_estatus,
					$problemaGestion,
					$entregado
				);
			}
		} catch (Throwable $e) {
			error_log('Error inesperado en estadosApg_post: ' . $e->getMessage());
			http_response_code(500);
			$this->_responderInmediatamente([
				'error'   => true,
				'mensaje' => 'Error interno del servidor.',
				'detalle' => $e->getMessage()
			]);
		}
	}



	private function _registrarEventoInterno($descripcion, $tracking, $estado = 'info', $respuesta = null, $id_estatus = null, $problemaGestion, $entregado)
	{
		$this->Entrega_model->registrar([
			'tracking'         => $tracking,
			'id_estatus'       => $id_estatus,
			'estado'           => $estado,
			'mensaje'          => $descripcion,
			'respuesta'        => is_array($respuesta) ? json_encode($respuesta) : $respuesta,
			'problema_gestion' => $problemaGestion,
			'entregado'   => $entregado

		]);
	}


	private function _updateRespuesta($tracking, $respuesta = null, $id_estatus = null, $entregado = null, $mala_gestion = null, $req = null)
	{
		$this->Entrega_model->UpdateRespuesta([
			'tracking'      => $tracking,
			'id_estatus'    => $id_estatus,
			'respuesta'     => is_array($respuesta) ? json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $respuesta,
			'entregado'     => $entregado,
			'mala_gestion'  => $mala_gestion,
			'req'           => $req, // ← nuevo
		]);
	}



	private function _responderInmediatamente($response)
	{
		ignore_user_abort(true); // el script sigue si el cliente cierra
		if (ob_get_length()) {
			ob_clean();
		}

		$json = json_encode($response);

		header('Content-Type: application/json');
		header('Connection: close');
		header('Content-Length: ' . strlen($json));

		echo $json;


		ob_flush();
		flush();
		if (function_exists('fastcgi_finish_request')) {
			fastcgi_finish_request(); // ✨ para FPM
		}
	}


	public function registrar($data)
	{
		$data['fecha'] = date('Y-m-d H:i:s');
		$this->db->insert('log_entregas', $data);
	}


	public function procesarOutbox_post()
	{
		$this->load->model('Entrega_model');
		$this->load->library('TokenApg');

		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
		header('Content-Type: application/json');
		set_time_limit(120);

		// --- Configurables rápidos ---
		$MAX_TRIES = 8;                        // máximo de reintentos por evento
		$BATCH_LIMIT = (int)($this->input->get_post('limit') ?? 500);
		if ($BATCH_LIMIT < 1) $BATCH_LIMIT = 1;
		if ($BATCH_LIMIT > 500) $BATCH_LIMIT = 500;

		// --- Helper: calcula siguiente next_retry_at con backoff + jitter ---
		$computeNextRetry = function (int $intentos) {
			// minutos base (escalado exponencial suave)
			$schedule = [5, 10, 20, 40, 80, 160, 320, 640]; // tope ~10.6h
			$idx = min(max($intentos - 1, 0), count($schedule) - 1);
			$mins = $schedule[$idx];
			$jitterSec = random_int(0, 30);
			return date('Y-m-d H:i:s', time() + ($mins * 60) + $jitterSec);
		};

		// --- 1) Leer pendientes de la outbox ---
		$rows = $this->db->select('id, tracking, id_estatus, status_name, fecha_envio, payload, intentos, last_error, next_retry_at, estado')
			->from('apg_outbox')
			->where('estado', 'pending')
			->group_start()
			->where('next_retry_at IS NULL', null, false)
			->or_where('next_retry_at <=', date('Y-m-d H:i:s'))
			->group_end()
			->order_by('id', 'ASC')
			->limit($BATCH_LIMIT)
			->get()
			->result();

		if (!$rows) {
			echo json_encode([
				'ok' => true,
				'message' => 'Outbox vacía o nada elegible por next_retry_at.',
				'processed' => 0,
				'delivered' => 0,
				'rescheduled' => 0,
				'dead' => 0,
			], JSON_UNESCAPED_UNICODE);
			return;
		}

		// --- 2) Intentar obtener token ---
		$token = $this->tokenapg->getToken();
		$tokenWasRefreshed = false;

		// Factory del handle cURL (mismo que usas en estadosApg_post)
		$apgUrl = 'https://apiprod.apg-global.com/api/account/save-status';
		$crearHandleApg = function (string $token) use ($apgUrl) {
			$authHeader = (stripos($token, 'bearer ') === 0) ? $token : ('Bearer ' . $token);
			$ch = curl_init($apgUrl);
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER    => true,
				CURLOPT_POST              => true,
				CURLOPT_HTTPHEADER        => [
					'Authorization: ' . $authHeader,
					'Content-Type: application/json',
					'Accept: application/json',
					'Expect:',
					'Connection: keep-alive',
					'Keep-Alive: timeout=5, max=1000',
				],
				CURLOPT_TIMEOUT           => 90,
				CURLOPT_CONNECTTIMEOUT    => 30,
				CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
				CURLOPT_NOSIGNAL          => true,
				CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
				CURLOPT_DNS_CACHE_TIMEOUT => 300,
				CURLOPT_FORBID_REUSE      => false,
				CURLOPT_FRESH_CONNECT     => false,
			]);
			return $ch;
		};

		$retryableCurl = [6, 7, 28, 52, 55, 56];
		$retryableHttp = [408, 429, 500, 502, 503, 504, 520];

		$delivered = 0;
		$rescheduled = 0;
		$dead = 0;
		$processed = 0;

		// Si no hay token: no abortamos; reprogramamos TODOS para luego
		if (!$token) {
			foreach ($rows as $r) {
				$next = $computeNextRetry($r->intentos + 1);
				$this->db->where('id', $r->id)->update('apg_outbox', [
					'intentos'      => $r->intentos + 1,
					'last_error'    => json_encode(['reason' => 'token_unavailable']),
					'next_retry_at' => $next,
					'estado'        => ($r->intentos + 1 >= $MAX_TRIES) ? 'dead' : 'pending',
				]);
				if ($r->intentos + 1 >= $MAX_TRIES) $dead++;
				else $rescheduled++;
				$processed++;
			}
			echo json_encode([
				'ok' => true,
				'message' => 'Sin token: eventos reprogramados.',
				'processed' => $processed,
				'delivered' => $delivered,
				'rescheduled' => $rescheduled,
				'dead' => $dead,
			], JSON_UNESCAPED_UNICODE);
			return;
		}

		$ch = $crearHandleApg($token);

		foreach ($rows as $r) {
			$processed++;

			// Payload: si está en texto, úsalo; si necesitas reconstruir, hazlo desde columnas
			$payload = $r->payload;
			if (!is_string($payload) || $payload === '') {
				$payload = json_encode([
					'tracking' => $r->tracking,
					'status'   => [[
						'statusName' => $r->status_name,
						'location'   => 'El Salvador',
						'statusDate' => $r->fecha_envio,
						'pod'        => null,
					]],
				], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			}

			// Reintentos por registro
			$maxAttempts = 4;
			$attempt = 0;
			$ok = false;
			$lastCurlNo = null;
			$lastCurlErr = null;
			$lastHttp = null;
			$lastBody = null;

			while ($attempt < $maxAttempts && !$ok) {
				$attempt++;

				curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
				$resp = curl_exec($ch);
				$lastCurlErr = curl_error($ch);
				$lastCurlNo  = curl_errno($ch);
				$lastHttp    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$lastBody    = $resp;

				// Transporte fallido
				if ($lastCurlErr) {
					$isRetry = in_array($lastCurlNo, $retryableCurl, true);
					if ($isRetry && $attempt < $maxAttempts) {
						usleep((int)(500 * pow(2, $attempt - 1) + random_int(0, 250)) * 1000);
						continue;
					}
					break;
				}

				// 401 → intenta refrescar token una sola vez
				if ($lastHttp === 401 && !$tokenWasRefreshed) {
					$newToken = $this->tokenapg->getToken(true); // fuerza refresh si tu lib lo soporta
					if ($newToken) {
						$tokenWasRefreshed = true;
						$authHeader = (stripos($newToken, 'bearer ') === 0) ? $newToken : ('Bearer ' . $newToken);
						curl_setopt($ch, CURLOPT_HTTPHEADER, [
							'Authorization: ' . $authHeader,
							'Content-Type: application/json',
							'Accept: application/json',
							'Expect:',
							'Connection: keep-alive',
							'Keep-Alive: timeout=5, max=1000',
						]);
						// reintenta el mismo intento (no consumes uno extra)
						usleep(200 * 1000);
						continue;
					}
				}

				// HTTP no-2xx
				if ($lastHttp < 200 || $lastHttp >= 300) {
					$isRetry = in_array($lastHttp, $retryableHttp, true);
					if ($isRetry && $attempt < $maxAttempts) {
						usleep((int)(600 * pow(2, $attempt - 1) + random_int(0, 300)) * 1000);
						continue;
					}
					break;
				}

				// 2xx: valida cuerpo
				$parsed = json_decode($resp, true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					// Body inválido; no retry
					break;
				}

				$respOK          = $parsed['objectResult']['response'] ?? null;     // "OK"
				$respStatusField = $parsed['objectResult']['status'][0]['response'] ?? null; // "Created"
				$isSuccess       = $parsed['isSuccess'] ?? null;

				if ($respOK === 'OK' || $respStatusField === 'Created' || $isSuccess === true) {
					$ok = true;
					break;
				}

				// Si no marcó éxito lógico pero fue 2xx, no seguimos reintentando en este run
				break;
			} // fin while intentos

			if ($ok) {
				// Marcar OK
				$this->db->where('id', $r->id)->update('apg_outbox', [
					'estado'        => 'ok',
					'last_error'    => null,
					'next_retry_at' => null,
				]);
				$delivered++;
			} else {
				// Reprogramar o pasar a dead
				$newTries = $r->intentos + 1;
				$update = [
					'intentos'      => $newTries,
					'last_error'    => json_encode([
						'curl_errno' => $lastCurlNo,
						'curl_error' => $lastCurlErr,
						'http_code'  => $lastHttp,
						'body'       => $lastBody,
					], JSON_UNESCAPED_UNICODE),
					'estado'        => 'pending',
					'next_retry_at' => $computeNextRetry($newTries),
				];
				if ($newTries >= $MAX_TRIES) {
					$update['estado'] = 'dead';
					$dead++;
				} else {
					$rescheduled++;
				}
				$this->db->where('id', $r->id)->update('apg_outbox', $update);
			}

			// Pequeña pausa para no saturar
			usleep(50 * 1000); // 50ms
		} // foreach row

		if (isset($ch) && is_resource($ch)) {
			curl_close($ch);
		}

		echo json_encode([
			'ok'          => true,
			'processed'   => $processed,
			'delivered'   => $delivered,
			'rescheduled' => $rescheduled,
			'dead'        => $dead,
			'token_refreshed' => $tokenWasRefreshed,
		], JSON_UNESCAPED_UNICODE);
	}

	public function procesarOutboxMA_post()
	{

		$this->load->model('Entrega_model');


		header('Content-Type: application/json');
		set_time_limit(120);

		// --- Configurables rápidos (igual APG) ---
		$MAX_TRIES   = 8;
		$BATCH_LIMIT = (int)($this->input->get_post('limit') ?? 500);
		if ($BATCH_LIMIT < 1)   $BATCH_LIMIT = 1;
		if ($BATCH_LIMIT > 500) $BATCH_LIMIT = 500;

		// --- Helper: calcula next_retry_at con backoff + jitter (igual APG) ---
		$computeNextRetry = function (int $intentos) {
			$schedule = [5, 10, 20, 40, 80, 160, 320, 640]; // minutos
			$idx = min(max($intentos - 1, 0), count($schedule) - 1);
			$mins = $schedule[$idx];
			$jitterSec = random_int(0, 30);
			return date('Y-m-d H:i:s', time() + ($mins * 60) + $jitterSec);
		};

		// --- 1) Leer pendientes de la outbox de MA ---
		$rows = $this->db->select('id, tracking, id_estatus, status_name, fecha_envio, payload, intentos, last_error, next_retry_at, estado')
			->from('ma_outbox')
			->where('estado', 'pending')
			->where('id_estatus<>', 316)
			->group_start()
			->where('next_retry_at IS NULL', null, false)
			->or_where('next_retry_at <=', date('Y-m-d H:i:s'))
			->group_end()
			->order_by('id', 'ASC')
			->limit($BATCH_LIMIT)
			->get()
			->result();

		if (!$rows) {
			echo json_encode([
				'ok'          => true,
				'message'     => 'Outbox MA vacía o nada elegible por next_retry_at.',
				'processed'   => 0,
				'delivered'   => 0,
				'rescheduled' => 0,
				'dead'        => 0,
			], JSON_UNESCAPED_UNICODE);
			return;
		}

		// Endpoint MA
		$maUrl = 'https://tracking.mailamericas.com/api/v1/providers/events';

		// Códigos retryables (igual APG)
		$retryableCurl = [6, 7, 28, 52, 55, 56];
		$retryableHttp = [408, 429, 500, 502, 503, 504, 520];

		$delivered = 0;
		$rescheduled = 0;
		$dead = 0;
		$processed = 0;

		foreach ($rows as $r) {
			$processed++;

			// --- 2) Resolver token EXACTAMENTE como en estados_post() ---
			$token_ma = null;
			try {
				// 2.a) intento por tracking tal como lo usas en estados_post
				$q = $this->Manifiesto_model->get_tracking($r->tracking);
				if (!empty($q)) {
					$empresa = $this->Manifiesto_model->datos_empresa($q[0]->id_empresa);

					if (!empty($empresa)) {
						$id_tokencliente = $empresa[0]->id_tokencliente ?? null;
						$token_cliente   = $empresa[0]->token_cliente   ?? null;
						if ($id_tokencliente && $token_cliente) {
							// mismo método que usas en estados_post
							$token_ma = $this->token_ma($id_tokencliente, $token_cliente);
						}
					}
				}
			} catch (Throwable $e) {
				// si falla, token_ma queda null y se reprograma abajo
			}

			// Si no hay token: reprogramar (igual patrón APG)
			if (!$token_ma) {
				$next = $computeNextRetry($r->intentos + 1);
				$this->db->where('id', $r->id)->update('ma_outbox', [
					'intentos'      => $r->intentos + 1,

					'last_error' => json_encode([
						'reason' => 'token_unavailable',
						'token'  => $token_ma
					], JSON_UNESCAPED_UNICODE),

					'next_retry_at' => $next,
					'estado'        => ($r->intentos + 1 >= $MAX_TRIES) ? 'dead' : 'pending',
				]);
				if ($r->intentos + 1 >= $MAX_TRIES) $dead++;
				else $rescheduled++;
				usleep(50 * 1000);
				continue;
			}

			// --- 3) Headers idénticos a estados_post (Bearer + Content-Type, sin Host) ---
			$headers = [
				'Content-Type: application/json',
				'Cache-Control: no-cache',
				'Authorization: Bearer ' . trim((string)$token_ma),
				'Expect:',
				'Connection: keep-alive',
				'Keep-Alive: timeout=5, max=1000',
			];

			//formateo de fecha iso
			$fecha     = $r->fecha_envio;
			$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
			$fecha_iso = $fecha_tmp->format(DateTime::ATOM);
			// --- 4) Payload: usar el guardado; si no hay, mínimo viable MA ---
			$payload = $r->payload;

			// Si viene vacío, crear payload base
			if (empty($payload)) {

				$payload = [
					"tracking"       => $r->tracking,
					"date"           => $fecha_iso,
					"description"    => $r->status_name,
					"city"           => "CCO",
					"received_by"    => null,
					"relationship"   => null,
					"delivery_proof" => [],
					"gcs"            => "",
					"details"        => ""
				];
			} else {

				// Convertir JSON a array
				if (is_string($payload)) {
					$payload = json_decode($payload, true);
				}

				// Frase a buscar
				$frase = 'Problemas en la gestión:';

				// Validar description
				if (
					isset($payload['description']) &&
					strpos($payload['description'], $frase) !== false
				) {

					// Separar texto
					$partes = explode($frase, $payload['description'], 2);

					// Description limpia
					$payload['description'] = 'Problemas en la gestión';

					// Motivo en details
					$payload['details'] = trim($partes[1]);
				}
			}

			// Convertir nuevamente a JSON para CURL
			$payload = json_encode(
				$payload,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			);


			$ch = curl_init($maUrl);
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER    => true,
				CURLOPT_POST              => true,
				CURLOPT_HTTPHEADER        => $headers,
				CURLOPT_POSTFIELDS        => $payload,
				CURLOPT_TIMEOUT           => 90,
				CURLOPT_CONNECTTIMEOUT    => 30,
				CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
				CURLOPT_NOSIGNAL          => true,
				CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
				CURLOPT_DNS_CACHE_TIMEOUT => 300,
				CURLOPT_FORBID_REUSE      => false,
				CURLOPT_FRESH_CONNECT     => false,
				CURLOPT_SSL_VERIFYHOST    => false,
				CURLOPT_SSL_VERIFYPEER    => false,
			]);

			$maxAttempts = 4;
			$attempt = 0;
			$ok = false;
			$lastCurlNo = null;
			$lastCurlErr = null;
			$lastHttp = null;
			$lastBody = null;

			while ($attempt < $maxAttempts && !$ok) {
				$attempt++;

				$resp = curl_exec($ch);
				$lastCurlErr = curl_error($ch);
				$lastCurlNo  = curl_errno($ch);
				$lastHttp    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$lastBody    = $resp;

				// Transporte fallido
				if ($lastCurlErr) {
					$isRetry = in_array($lastCurlNo, $retryableCurl, true);
					if ($isRetry && $attempt < $maxAttempts) {
						usleep((int)(500 * pow(2, $attempt - 1) + random_int(0, 250)) * 1000);
						continue;
					}
					break;
				}

				// HTTP no-2xx
				if ($lastHttp < 200 || $lastHttp >= 300) {
					$isRetry = in_array($lastHttp, $retryableHttp, true);
					if ($isRetry && $attempt < $maxAttempts) {
						usleep((int)(600 * pow(2, $attempt - 1) + random_int(0, 300)) * 1000);
						continue;
					}
					break;
				}

				// 2xx → lo damos por OK (MA puede devolver JSON o texto)
				$ok = true;
				break;
			} // while

			curl_close($ch);

			if ($ok) {
				// --- 6) Marcar OK ---
				$this->db->where('id', $r->id)->update('ma_outbox', [
					'estado'        => 'ok',
					'last_error'    => null,
					'next_retry_at' => null,
				]);
				$delivered++;
			} else {
				// --- 7) Reprogramar o DEAD ---
				$newTries = $r->intentos + 1;
				$update = [
					'intentos'      => $newTries,
					'last_error'    => json_encode([
						'curl_errno' => $lastCurlNo,
						'curl_error' => $lastCurlErr,
						'http_code'  => $lastHttp,
						'body'       => $lastBody,
					], JSON_UNESCAPED_UNICODE),
					'estado'        => 'pending',
					'next_retry_at' => $computeNextRetry($newTries),
				];
				if ($newTries >= $MAX_TRIES) {
					$update['estado'] = 'dead';
					$dead++;
				} else {
					$rescheduled++;
				}
				$this->db->where('id', $r->id)->update('ma_outbox', $update);
			}

			// Pausa breve para no saturar
			usleep(50 * 1000);
		} // foreach

		echo json_encode([
			'ok'          => true,
			'processed'   => $processed,
			'delivered'   => $delivered,
			'rescheduled' => $rescheduled,
			'dead'        => $dead,
		], JSON_UNESCAPED_UNICODE);
	}
}
/* End of file Controllername.php */