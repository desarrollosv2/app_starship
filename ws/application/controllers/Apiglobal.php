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
class Apiglobal extends REST_Controller
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
		$this->load->model('GeneralOutbox_model');
		$this->load->model('Importacion_Model');
		$this->load->model('Entrega_model');
		$this->load->model('Conf_model');
	}

	public function eventos_post()
	{
		$this->load->library('TokenApg');

		try {
			//ini_set('display_errors', '1');
			//ini_set('display_startup_errors', '1');
			//error_reporting(E_ALL);

			date_default_timezone_set("America/El_Salvador");
			header('Content-Type: application/json');

			$rawData = file_get_contents('php://input');
			$row     = json_decode($rawData, true);

			if (!$row || !is_array($row)) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error'   => true,
					'mensaje' => 'Formato JSON inválido.'
				]);
				return;
			}

			$empresa           = 110;
			$entregado         = 'NO';
			$codigo_recibido   = $row['codigo'] ?? null;
			$tracking_recibido = $row['guia']   ?? null;
			$fecha_response    = $row['fecha']  ?? null; // 'Y-m-d H:i:s' esperado

			if (!$codigo_recibido || !$tracking_recibido) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error'   => true,
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
					'error'   => true,
					'mensaje' => "Tracking {$tracking_recibido} no encontrado."
				]);
				return;
			}

			// Anti-duplicados básicos que ya tenías
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

			// ===== Helpers =====

			// Inserta evento en detalle y actualiza encabezado + DTES
			$insertarEvento = function (int $id_estatus, DateTime $dt, string $problemaGestion = '', string $descripcion = '') use ($tracking_recibido) {
				$fechah  = $dt->format('Y-m-d H:i:s');
				$fechaiso = $dt->format(DateTime::ATOM);

				$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);

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
					'fechaiso'         => $fechaiso,
					'problema_gestion' => ($descripcion !== '' ? $descripcion : $problemaGestion),
				];
				$this->Manifiesto_model->guardar_estatus($datos);

				$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);
			};

			// Resuelve statusName
			$resolverStatusName = function (int $id_estatus, string $problemaGestion = '', int $empresa) {
				if ($id_estatus === 16 || $id_estatus === 19) {
					$r = $this->Manifiesto_model->buscarEstatus('descripcion', $problemaGestion, $empresa);
					return $r ? $r[0]['codigo_cliente'] : '';
				}
				$r = $this->Manifiesto_model->buscarEstatus('codigo_interno', $id_estatus, $empresa);
				return $r ? $r[0]['codigo_cliente'] : '';
			};

			// Encolar en general_outbox (APG)
			$enqueueGeneral = function (string $tracking, int $id_estatus, string $statusName, DateTime $dt, array $meta = null) use ($empresa) {
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

				$this->GeneralOutbox_model->enqueueGeneral(
					'apg',
					(int)$empresa,
					$tracking,
					(int)$id_estatus,
					$statusName,
					$payload,
					null,
					$fechaEnvio,
					$meta
				);
			};

			// Handle cURL reusado para APG (usa el endpoint correcto)
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
					CURLOPT_TIMEOUT           => 60,
					CURLOPT_CONNECTTIMEOUT    => 20,
					CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
					CURLOPT_NOSIGNAL          => true,
					CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
					CURLOPT_DNS_CACHE_TIMEOUT => 300,
					CURLOPT_FORBID_REUSE      => false,
					CURLOPT_FRESH_CONNECT     => false,
				]);
				return $ch;
			};

			// Envío a APG con reintentos/backoff + outbox si falla
			$enviarApg = function ($ch, int $id_estatus, string $tracking_recibido, string $statusName, string $fechaEnvio, string $problemaGestion) use (&$entregado, $empresa) {
				$payload = [
					'tracking' => $tracking_recibido,
					'status'   => [[
						'statusName' => $statusName,
						'location'   => 'El Salvador',
						'statusDate' => $fechaEnvio,
						'pod'        => null,
					]],
				];
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

				$maxAttempts   = 4;
				$attempt       = 0;
				$respuesta_apg = null;
				$ok            = false;

				$lastCurlNo  = null;
				$lastCurlErr = null;
				$lastHttp    = null;
				$lastBody    = null;

				$retryableCurl = [6, 7, 28, 52, 55, 56];
				$retryableHttp = [408, 429, 500, 502, 503, 504, 520];

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
						break;
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

					// 2xx
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

					$isSuccess       = $respuesta_apg['isSuccess'] ?? null;
					$respOK          = $respuesta_apg['objectResult']['response'] ?? null;
					$respStatusField = $respuesta_apg['objectResult']['status'][0]['response'] ?? null;

					if ($respOK === 'OK' || $respStatusField === 'Created' || $isSuccess === true) {
						$entregado = 'OK';
					}

					$this->_registrarEventoInterno(
						"APG OK intento {$attempt} (estatus {$id_estatus})",
						$tracking_recibido,
						'entregado',
						['http_code' => $lastHttp, 'resp' => $respuesta_apg],
						$id_estatus,
						$problemaGestion,
						$entregado
					);

					$ok = true;
				}

				if (!$ok) {
					// Encolar con meta detallado
					$this->GeneralOutbox_model->enqueueGeneral(
						'apg',
						(int)$empresa,
						$tracking_recibido,
						(int)$id_estatus,
						$statusName,
						$payload,
						null,
						$fechaEnvio,
						[
							'reason'     => 'transport_or_remote_error',
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
					// SOLO 19
					$descripcion_19     = $row['observaciones'] ?? '';
					$ProblemaGestion_19 = $descripcion_19;

					$insertarEvento(19, $dtBase, $ProblemaGestion_19, $descripcion_19);

					$sn19  = $resolverStatusName(19, $ProblemaGestion_19, $empresa);
					$token = $this->tokenapg->getToken();

					if (!$token) {
						$enqueueGeneral($tracking_recibido, 19, $sn19, $dtBase, ['reason' => 'token_unavailable']);
						$this->_registrarEventoInterno(
							'Token APG no disponible: evento 19 encolado',
							$tracking_recibido,
							'warning',
							[],
							19,
							$ProblemaGestion_19,
							$entregado
						);
						return;
					}

					$ch = $crearHandleApg($token);
					$enviarApg($ch, 19, $tracking_recibido, $sn19, $dtBase->format('Y-m-d H:i:s'), $ProblemaGestion_19);
					curl_close($ch);
					return;
				}

				// Cadena 219 (t), 10 (t+2), 19 (t+4)
				$dt219 = clone $dtBase;
				$dt10  = (clone $dtBase)->modify('+2 minutes');
				$dt19  = (clone $dtBase)->modify('+4 minutes');

				$insertarEvento(219, $dt219);
				$descripcion_19     = $row['observaciones'] ?? '';
				$ProblemaGestion_19 = $descripcion_19;
				$insertarEvento(19,  $dt19, $ProblemaGestion_19, $descripcion_19);

				$sn219 = $resolverStatusName(219, '', $empresa);
				$sn10  = $resolverStatusName(10,  '', $empresa);
				$sn19  = $resolverStatusName(19,  $ProblemaGestion_19, $empresa);

				$token = $this->tokenapg->getToken();
				if (!$token) {
					$enqueueGeneral($tracking_recibido, 219, $sn219, $dt219, ['reason' => 'token_unavailable']);
					$enqueueGeneral($tracking_recibido, 19,  $sn19,  $dt19,  ['reason' => 'token_unavailable']);
					$this->_registrarEventoInterno(
						'Token APG no disponible: cadena 219/10/19 encolada',
						$tracking_recibido,
						'warning',
						[],
						19,
						$ProblemaGestion_19,
						$entregado
					);
					return;
				}

				$ch = $crearHandleApg($token);
				$enviarApg($ch, 219, $tracking_recibido, $sn219, $dt219->format('Y-m-d H:i:s'), '');
				$enviarApg($ch, 19,  $tracking_recibido, $sn19,  $dt19->format('Y-m-d H:i:s'),  $ProblemaGestion_19);
				curl_close($ch);
				return;
			}

			// --------- FLUJO NORMAL (no es 19) ----------
			$problemaGestion = "";
			$id_estatus      = (int)$row['codigo'];
			$description     = "";

			if ($id_estatus == 16) {
				$problemaGestion = $row['razon']['descripcion'] ?? '';
				$description     = $problemaGestion;
			}
			if ($id_estatus == 19) {
				$description = ($row['observaciones'] ?? '');
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
				$resultado  = $this->Manifiesto_model->buscarPorDescripcion($description ?? '');
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			} else {
				$resultado  = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			}
			if ($devolucion) {
				$statusName = "RO";
			}

			// Encolar en general_outbox + (opcional) meta
			$this->load->model('GeneralOutbox_model');

			$payloadApg = [
				'tracking' => $tracking_recibido,
				'status'   => [[
					'statusName' => $statusName,
					'location'   => 'El Salvador',
					'statusDate' => $dtBase->format('Y-m-d H:i:s'),
					'pod'        => null,
				]],
			];

			$enqueue = $this->GeneralOutbox_model->enqueueGeneral(
				'apg',
				(int)$empresa,
				$tracking_recibido,
				(int)$id_estatus,
				$statusName,
				$payloadApg,
				null,
				$dtBase->format('Y-m-d H:i:s'),
				['source' => 'eventos_post']
			);

			$this->_responderInmediatamente([
				'error'     => false,
				'message'   => 'Recibido/Encolado correctamente.',
				'tracking'  => $tracking_recibido,
				'event_id'  => $enqueue['event_id'],
				'outbox_id' => $enqueue['id'],
			]);
			return;
		} catch (Throwable $e) {
			error_log('Error inesperado en eventos_post: ' . $e->getMessage());
			http_response_code(500);
			$this->_responderInmediatamente([
				'error'   => true,
				'mensaje' => 'Error interno del servidor.',
				'detalle' => $e->getMessage()
			]);
			return;
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



	private function _responderInmediatamente(array $response, int $httpCode = 200): void
{
    ignore_user_abort(true);

    $json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if (@ob_get_length()) { @ob_clean(); }

    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    header('Connection: close');
    header('Content-Length: ' . strlen($json));

    echo $json;

    @ob_flush(); @flush();
    if (function_exists('fastcgi_finish_request')) {
        @fastcgi_finish_request();
    }
    // no exit aquí; en el controlador: return; tras llamarla
}




	public function registrar($data)
	{
		$data['fecha'] = date('Y-m-d H:i:s');
		$this->db->insert('log_entregas', $data);
	}
}
/* End of file Controllername.php */