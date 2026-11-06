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
class Apimile extends REST_Controller
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
        $this->load->model('Conf_model');
    }



    public function eventosbueno_post()
    {
        //este estaba antes de enviar imagnes en pod
        $this->load->model('Entrega_model');
        $this->load->model('Manifiesto_model');
        $this->load->model('DTES_Model');
        $this->load->library('TokenImile'); // propiedad: $this->tokenimile

        try {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);

            date_default_timezone_set("America/El_Salvador");
            header('Content-Type: application/json');

            // ---------- Leer JSON ----------
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

            $entregado         = 'NO';
            $codigo_recibido   = $row['codigo'] ?? null;
            $tracking_recibido = $row['guia']   ?? null;
            $fecha_response    = $row['fecha']  ?? date('Y-m-d H:i:s'); // si no viene, usa ahora

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
                $dtBase = new DateTime((string)$fecha_response); // fallback ISO/otros
            }
            $fecha_iso = $dtBase->format(DateTime::ATOM);

            // Verifica tracking (IMPORTANTE: validar antes de usar ->id_manifiesto)
            $request_tracking = $this->Manifiesto_model->obtener_tracking($tracking_recibido);
            if (!$request_tracking) {
                http_response_code(404);
                $this->_responderInmediatamente([
                    'error'   => true,
                    'mensaje' => "Tracking {$tracking_recibido} no encontrado."
                ]);
                return;
            }

            $masterId = $request_tracking->id_manifiesto;

            // País / Vendor / URL por país
            $id_pais = (int) $this->Manifiesto_model->obtener_id_pais($masterId);

            $vendor   = '';
            $url_imile = '';

            if ($id_pais === 1) {
                // SV
                $url_imile = 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjQwMzM1Ny1yZWNlaXZlVmVuZG9yVHJhY2s';
                $vendor    = '097823402254';
            } elseif ($id_pais === 2) {
                // GT (pendiente)
                $url_imile = '';     // <- cuando tengas el webhook GT lo pones aquí
                $vendor    = '';     // <- vendor GT
            } elseif ($id_pais === 3) {
                // HN
                $url_imile = 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjUwNDA3NS1yZWNlaXZlVmVuZG9yVHJhY2s';
                $vendor    = 'SRM2504075';
            }

            // ---------- Respuesta inmediata ----------
            $this->_responderInmediatamente([
                'error'    => false,
                'message'  => 'Recibido correctamente.',
                'tracking' => $tracking_recibido
            ]);

            // ---------- Anti-duplicados 15 / 311 ----------
            $existe15 = $this->Manifiesto_model->verificar_pod($tracking_recibido, $codigo_recibido);
            if ($existe15) {
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
                    (int)$codigo_recibido,
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
                    (int)$codigo_recibido,
                    "",
                    $entregado
                );
                return;
            }

            // ======================
            // HELPERS
            // ======================

            // Inserta evento en detalle y actualiza encabezado + DTES
            // (IMPORTANTE: aquí usamos $id_pais real, antes estaba fijo en 1)
            $insertarEvento = function (int $id_estatus, DateTime $dt, string $problemaGestion = '', string $descripcion = '')
            use ($tracking_recibido, $fecha_iso, $id_pais) {

                $fechah  = $dt->format('Y-m-d H:i:s');
                $fechais = $dt->format(DateTime::ATOM);

                // Encabezado
                $this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);

                // Detalle
                $datos = [
                    'item'             => 1,
                    'fecha'            => $fechah,
                    'warehouse'        => $tracking_recibido,
                    'id_estatus'       => $id_estatus,
                    'casillero'        => '100',
                    'id_pais'          => $id_pais,
                    'pod_nombre'       => '',
                    'pod_valor'        => '',
                    'json_string'      => json_encode(['guia' => '']),
                    'ruta_foto'        => '',
                    'fechaiso'         => $fechais ?: $fecha_iso,
                    'problema_gestion' => ($descripcion !== '' ? $descripcion : $problemaGestion),
                ];
                $this->Manifiesto_model->guardar_estatus($datos);

                // Sincroniza a tu otro sistema interno
                $this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);
            };

            // Resolver mapeo iMile (statusName/descripcion) y posible remapeo de id_estatus (16/20)
            $resolverStatusImile = function (int &$id_estatus, string $problemaGestion = '') {
                $statusName = '';
                if ($id_estatus === 16 || $id_estatus === 20) {
                    $resultado = $this->Manifiesto_model->buscarPorDescripcionImile($problemaGestion ?? '');
                    if ($resultado) {
                        $id_estatus = (int)$resultado[0]['codigo_cliente'];
                        if ($resultado[0]['codigo_cliente'] == 315) {
                            $id_estatus = 315;
                        }
                        $statusName = $resultado[0]['descripcion'];
                    }
                } else {
                    $resultado = $this->Manifiesto_model->buscarPorCodigoImile($id_estatus);
                    if ($resultado) {
                        $statusName = $resultado[0]['descripcion'];
                    }
                }
                return $statusName;
            };

            // -------- Helper: resolver token/URL de iMile por país con fallbacks seguros --------
            // NOTA: En tu caso, lo más estable para PROD es regresar directamente el URL por país.
            //       Si mañana quieres manejarlo por ENV/CONFIG por país, ya está listo.
            $resolverTokenImile = function (int $id_pais) use ($url_imile): ?string {

                // 1) si por algún motivo ya calculaste $url_imile arriba y viene lleno, úsalo
                if ($url_imile && trim($url_imile) !== '') {
                    return trim($url_imile);
                }

                // 2) librería (si la quieres usar)
                try {
                    if (!isset($this->tokenimile)) {
                        $this->load->library('TokenImile');
                    }

                    // Si algún día implementas getTokenByPais en tu librería, esto te sirve
                    if (isset($this->tokenimile) && method_exists($this->tokenimile, 'getTokenByPais')) {
                        $val = $this->tokenimile->getTokenByPais($id_pais);
                        if ($val && trim($val) !== '') return trim($val);
                    }

                    // fallback general si solo existe getToken()
                    if (isset($this->tokenimile) && method_exists($this->tokenimile, 'getToken')) {
                        $val = $this->tokenimile->getToken();
                        if ($val && trim($val) !== '') return trim($val);
                    }
                } catch (Throwable $e) {
                    log_message('error', 'TokenImile error: ' . $e->getMessage());
                }

                // 3) ENV por país (opcional)
                $envKey = match ($id_pais) {
                    1 => 'IMILE_WEBHOOK_SV',
                    2 => 'IMILE_WEBHOOK_GT',
                    3 => 'IMILE_WEBHOOK_HN',
                    default => null,
                };
                if ($envKey) {
                    $env = getenv($envKey);
                    if ($env && trim($env) !== '') return trim($env);
                }

                // 4) CONFIG por país (opcional)
                $cfgKey = match ($id_pais) {
                    1 => 'imile_webhook_sv',
                    2 => 'imile_webhook_gt',
                    3 => 'imile_webhook_hn',
                    default => null,
                };
                if ($cfgKey) {
                    $cfg = $this->config->item($cfgKey);
                    if ($cfg && trim($cfg) !== '') return trim($cfg);
                }

                // 5) fallback duro por país (PROD SAFE)
                return match ($id_pais) {
                    1 => 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjQwMzM1Ny1yZWNlaXZlVmVuZG9yVHJhY2s', // SV
                    3 => 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjUwNDA3NS1yZWNlaXZlVmVuZG9yVHJhY2s', // HN
                    2 => null, // GT pendiente
                    default => null,
                };
            };

            // Outbox: guarda para reintentar luego (tabla imile_outbox)
            $enqueueOutboxImile = function (string $tracking, int $id_estatus, string $statusName, DateTime $dt, array $extra = [], ?array $payloadOverride = null, $vendor) {
                $fechaEnvio = $dt->format('Y-m-d H:i:s');
                $payload = $payloadOverride ?: [
                    'waybillNo'         => $tracking,
                    'vendorWaybillNo'   => $vendor,
                    'timeZone'          => 'GMT+8',
                    'status'            => $id_estatus,
                    'description'       => $statusName,
                    'operationUserName' => 'Starship',
                    'driverPhone'       => 'xxx',
                    'ocCode'            => '10001',
                    'ocName'            => 'PLL',
                    'operateTime'       => $fechaEnvio,
                ];

                $this->Entrega_model->enqueueOutboxImile(
                    $tracking,
                    $id_estatus,
                    $statusName,
                    $fechaEnvio,
                    $payload,
                    $extra
                );
            };

            // Crea handle cURL para iMile (token en URL o URL completa)
            $crearHandleImile = function (string $tokenOrUrl) {
                $url = stripos($tokenOrUrl, 'http') === 0
                    ? $tokenOrUrl
                    : ('https://scs.imile.com/tmsvendor/webhook/' . $tokenOrUrl);

                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER    => true,
                    CURLOPT_POST              => true,
                    CURLOPT_HTTPHEADER        => [
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
                    CURLOPT_FOLLOWLOCATION    => true,
                    CURLOPT_MAXREDIRS         => 5,
                ]);
                return $ch;
            };

            // Enviar a iMile con reintentos/backoff y outbox si falla
            $enviarImile = function ($ch, int $id_estatus_envio, string $tracking, string $statusName, string $fechaEnvio, array $payload, string $problemaGestion = '') use (&$entregado) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                $maxAttempts     = 4;
                $attempt         = 0;
                $ok              = false;
                $respuesta_imile = null;

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
                        $isRetry = in_array($lastCurlNo, $retryableCurl, true);
                        $this->_registrarEventoInterno(
                            "iMile cURL intento {$attempt}" . ($isRetry ? " (retry)" : ""),
                            $tracking,
                            $isRetry ? 'warning' : 'fallido',
                            ['curl_errno' => $lastCurlNo, 'curl_error' => $lastCurlErr],
                            $id_estatus_envio,
                            $problemaGestion,
                            $entregado
                        );
                        if ($isRetry && $attempt < $maxAttempts) {
                            usleep((int)(500 * pow(2, $attempt - 1) + random_int(0, 250)) * 1000);
                            continue;
                        }
                        break;
                    }

                    if ($lastHttp < 200 || $lastHttp >= 300) {
                        $isRetry = in_array($lastHttp, $retryableHttp, true);
                        $this->_registrarEventoInterno(
                            "iMile HTTP {$lastHttp} intento {$attempt}" . ($isRetry ? " (retry)" : ""),
                            $tracking,
                            $isRetry ? 'warning' : 'fallido',
                            ['http_code' => $lastHttp, 'body' => $lastBody],
                            $id_estatus_envio,
                            $problemaGestion,
                            $entregado
                        );
                        if ($isRetry && $attempt < $maxAttempts) {
                            usleep((int)(600 * pow(2, $attempt - 1) + random_int(0, 300)) * 1000);
                            continue;
                        }
                        break;
                    }

                    $parsed = json_decode($resp, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                        $respuesta_imile = $parsed;
                    } else {
                        $respuesta_imile = $resp;
                    }

                    $entregado = 'OK';
                    $this->_registrarEventoInterno(
                        "iMile OK intento {$attempt} (estatus {$id_estatus_envio})",
                        $tracking,
                        'entregado',
                        ['http_code' => $lastHttp, 'resp' => $respuesta_imile],
                        $id_estatus_envio,
                        $problemaGestion,
                        $entregado
                    );

                    $ok = true;
                }

                if (!$ok) {
                    $this->Entrega_model->enqueueOutboxImile(
                        $tracking,
                        $id_estatus_envio,
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

                return $ok ? $respuesta_imile : null;
            };

            // ======================
            // LÓGICA PRINCIPAL iMile
            // ======================
            $id_estatus_original = (int)$codigo_recibido;
            $observaciones       = $row['observaciones'] ?? '';
            $razon_desc          = $row['razon']['descripcion'] ?? '';
            $devolucion_flag     = (int)($row['devolucion'] ?? 0);

            // ¿Existe ya un 19?
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

            // ------ FLUJO ESPECIAL (19) ------
            if ($id_estatus_original === 19) {

                if ($yaTiene19) {
                    // SOLO 19
                    $insertarEvento(19, $dtBase, $observaciones, $observaciones);

                    $id_envio = 19;
                    $statusName19 = $resolverStatusImile($id_envio, $observaciones);

                    if ($id_envio == 216 || $id_envio == 288) {
                        return;
                    }

                    $payload19 = [
                        'waybillNo'         => $tracking_recibido,
                        'vendorWaybillNo'   => $vendor,  // <- YA NO hardcode
                        'timeZone'          => 'GMT+8',
                        'status'            => $id_envio,
                        'description'       => $statusName19,
                        'operationUserName' => 'Starship',
                        'driverPhone'       => 'xxx',
                        'ocCode'            => '10001',
                        'ocName'            => 'PLL',
                        'operateTime'       => $dtBase->format('Y-m-d H:i:s'),
                    ];

                    $tokenOrUrl = $resolverTokenImile((int)$id_pais);
                    if (!$tokenOrUrl) {
                        $enqueueOutboxImile($tracking_recibido, $id_envio, $statusName19, $dtBase, ['reason' => 'token_unavailable'], $payload19, $vendor);
                        $this->_registrarEventoInterno(
                            'Token/URL iMile no disponible: evento 19 encolado',
                            $tracking_recibido,
                            'warning',
                            [],
                            19,
                            $observaciones,
                            $entregado
                        );
                        return;
                    }

                    $ch = $crearHandleImile($tokenOrUrl);
                    $enviarImile($ch, $id_envio, $tracking_recibido, $statusName19, $dtBase->format('Y-m-d H:i:s'), $payload19, $observaciones);
                    curl_close($ch);
                    return;
                }

                // Cadena 219 (t), 10 (t+2), 19 (t+4)
                $dt219 = clone $dtBase;
                $dt10  = (clone $dtBase)->modify('+2 minutes');
                $dt19  = (clone $dtBase)->modify('+4 minutes');

                $insertarEvento(219, $dt219);
                $insertarEvento(10,  $dt10);
                $insertarEvento(19,  $dt19, $observaciones, $observaciones);

                $id219 = 219;
                $name219 = $resolverStatusImile($id219, '');
                $id10  = 10;
                $name10  = $resolverStatusImile($id10,  '');
                $id19  = 19;
                $name19  = $resolverStatusImile($id19,  $observaciones);

                $skip219 = ($id219 == 216 || $id219 == 288);
                $skip10  = ($id10  == 216 || $id10  == 288);
                $skip19  = ($id19  == 216 || $id19  == 288);

                $tokenOrUrl = $resolverTokenImile((int)$id_pais);
                if (!$tokenOrUrl) {
                    if (!$skip219) $enqueueOutboxImile($tracking_recibido, $id219, $name219, $dt219, ['reason' => 'token_unavailable'], null, $vendor);
                    if (!$skip10)  $enqueueOutboxImile($tracking_recibido, $id10,  $name10,  $dt10,  ['reason' => 'token_unavailable'], null, $vendor);
                    if (!$skip19)  $enqueueOutboxImile($tracking_recibido, $id19,  $name19,  $dt19,  ['reason' => 'token_unavailable'], null, $vendor);

                    $this->_registrarEventoInterno(
                        'Token/URL iMile no disponible: cadena 219/10/19 encolada',
                        $tracking_recibido,
                        'warning',
                        [],
                        19,
                        $observaciones,
                        $entregado
                    );
                    return;
                }

                $ch = $crearHandleImile($tokenOrUrl);

                if (!$skip219) {
                    $enviarImile($ch, $id219, $tracking_recibido, $name219, $dt219->format('Y-m-d H:i:s'), [
                        'waybillNo'         => $tracking_recibido,
                        'vendorWaybillNo'   => $vendor, // <- YA NO hardcode
                        'timeZone'          => 'GMT+8',
                        'status'            => $id219,
                        'description'       => $name219,
                        'operationUserName' => 'Starship',
                        'driverPhone'       => 'xxx',
                        'ocCode'            => '10001',
                        'ocName'            => 'PLL',
                        'operateTime'       => $dt219->format('Y-m-d H:i:s'),
                    ]);
                }

                // (Tus envíos de 10 y 19 están comentados, los dejo igual)
                curl_close($ch);
                return;
            }

            // ------ FLUJO NORMAL (no es 19) ------
            $id_estatus = (int)$row['codigo'];

            // Devolución: 15 + devolucion=1 => 311
            $devolucion = false;
            if ($id_estatus == 15 && $devolucion_flag === 1) {
                $id_estatus = 311;
                $devolucion = true;
            }

            $descripcion_for_detalle = '';
            if ($id_estatus == 16) {
                $descripcion_for_detalle = ($row['estatus'] ?? '') . ': ' . ($observaciones ?? '');
            }

            $insertarEvento($id_estatus, $dtBase, $razon_desc ?: $observaciones, $descripcion_for_detalle);

            $statusName = $resolverStatusImile($id_estatus, $razon_desc ?: $observaciones);
            if ($devolucion) {
                $statusName = 'RO';
            }

            if ($id_estatus == 216 || $id_estatus == 288) {
                return;
            }

            $payload = [
                'waybillNo'         => $tracking_recibido,
                'vendorWaybillNo'   => $vendor,  // <- YA NO hardcode
                'timeZone'          => 'GMT+8',
                'status'            => $id_estatus,
                'description'       => $statusName,
                'operationUserName' => 'Starship',
                'driverPhone'       => 'xxx',
                'ocCode'            => '10001',
                'ocName'            => 'PLL',
                'operateTime'       => $dtBase->format('Y-m-d H:i:s'),
            ];

            $tokenOrUrl = $resolverTokenImile((int)$id_pais);
            if ($tokenOrUrl) {
                $ch = $crearHandleImile($tokenOrUrl);
                $enviarImile($ch, $id_estatus, $tracking_recibido, $statusName, $dtBase->format('Y-m-d H:i:s'), $payload, $razon_desc ?: $observaciones);
                curl_close($ch);
            } else {
                $enqueueOutboxImile($tracking_recibido, $id_estatus, $statusName, $dtBase, ['reason' => 'token_unavailable'], $payload, $vendor);
                $this->_registrarEventoInterno(
                    'Token/URL iMile no disponible: evento encolado',
                    $tracking_recibido,
                    'warning',
                    [],
                    $id_estatus,
                    $razon_desc ?: $observaciones,
                    $entregado
                );
            }
        } catch (Throwable $e) {
            error_log('Error inesperado en eventos_post (iMile): ' . $e->getMessage());
            http_response_code(500);
            $this->_responderInmediatamente([
                'error'   => true,
                'mensaje' => 'Error interno del servidor.',
                'detalle' => $e->getMessage()
            ]);
        }
    }



    public function procesarOutboxImile_post()
    {
        $this->load->model('Entrega_model');
        $this->load->library('TokenImile');

        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
        header('Content-Type: application/json');
        set_time_limit(180);

        // --- Config ---
        $MAX_TRIES   = 8;
        $BATCH_LIMIT = (int)($this->input->get_post('limit') ?? 500);
        if ($BATCH_LIMIT < 1) $BATCH_LIMIT = 1;
        if ($BATCH_LIMIT > 500) $BATCH_LIMIT = 500;

        // --- Backoff con jitter ---
        $computeNextRetry = function (int $intentos) {
            $schedule = [5, 10, 20, 40, 80, 160, 320, 640]; // minutos
            $idx  = min(max($intentos - 1, 0), count($schedule) - 1);
            $mins = $schedule[$idx];
            $jsec = random_int(0, 30);
            return date('Y-m-d H:i:s', time() + ($mins * 60) + $jsec);
        };

        // --- 1) Leer pendientes de la outbox ---
        $rows = $this->db->select('id, tracking, id_estatus, status_name, fecha_envio, payload, intentos, last_error, next_retry_at, estado')
            ->from('imile_outbox')
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
                'ok'          => true,
                'message'     => 'Outbox iMile vacía o nada elegible por next_retry_at.',
                'processed'   => 0,
                'delivered'   => 0,
                'rescheduled' => 0,
                'dead'        => 0,
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // --- 2) Obtener token/URL iMile (si no hay, reprograma todos) ---
        //$tokenOrUrl = $this->tokenimile->getToken();
        $tokenOrUrl  =   'U1VQUExJRVItU1JNMjQwMzM1Ny1yZWNlaXZlVmVuZG9yVHJhY2s';
        $delivered = 0;
        $rescheduled = 0;
        $dead = 0;
        $processed = 0;

        if (!$tokenOrUrl) {
            foreach ($rows as $r) {
                $next = $computeNextRetry($r->intentos + 1);
                $this->db->where('id', $r->id)->update('imile_outbox', [
                    'intentos'      => $r->intentos + 1,
                    'last_error'    => json_encode(['reason' => 'token_unavailable'], JSON_UNESCAPED_UNICODE),
                    'next_retry_at' => $next,
                    'estado'        => ($r->intentos + 1 >= $MAX_TRIES) ? 'dead' : 'pending',
                ]);
                if ($r->intentos + 1 >= $MAX_TRIES) $dead++;
                else $rescheduled++;
                $processed++;
            }
            echo json_encode([
                'ok'          => true,
                'message'     => 'Sin token/URL iMile: eventos reprogramados.',
                'processed'   => $processed,
                'delivered'   => $delivered,
                'rescheduled' => $rescheduled,
                'dead'        => $dead,
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // --- 3) Crear handle cURL reusado ---
        $crearHandleImile = function (string $tokenOrUrl) {
            $url = stripos($tokenOrUrl, 'http') === 0
                ? $tokenOrUrl
                : ('https://scs.imile.com/tmsvendor/webhook/' . $tokenOrUrl);
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER    => true,
                CURLOPT_POST              => true,
                CURLOPT_HTTPHEADER        => [
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
                CURLOPT_FOLLOWLOCATION    => true,
                CURLOPT_MAXREDIRS         => 5,
            ]);
            return $ch;
        };

        $ch = $crearHandleImile($tokenOrUrl);

        $retryableCurl = [6, 7, 28, 52, 55, 56];
        $retryableHttp = [408, 429, 500, 502, 503, 504, 520];

        foreach ($rows as $r) {
            $processed++;

            // Si por regla de negocio no se envían 216/288, márcalos OK y sigue.
            if ((int)$r->id_estatus === 216 || (int)$r->id_estatus === 288) {
                $this->db->where('id', $r->id)->update('imile_outbox', [
                    'estado'        => 'ok',
                    'last_error'    => null,
                    'next_retry_at' => null,
                ]);
                $delivered++;
                continue;
            }

            // Payload: usa el guardado; si faltara, lo reconstruye.
            $payload = $r->payload;
            if (!is_string($payload) || $payload === '') {
                $payload = json_encode([
                    'waybillNo'         => $r->tracking,
                    'vendorWaybillNo'   => '097823402254',
                    'timeZone'          => 'GMT+8',
                    'status'            => (int)$r->id_estatus,
                    'description'       => $r->status_name,
                    'operationUserName' => 'Starship',
                    'driverPhone'       => 'xxx',
                    'ocCode'            => '10001',
                    'ocName'            => 'PLL',
                    'operateTime'       => $r->fecha_envio,
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

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

                if ($lastCurlErr) {
                    $isRetry = in_array($lastCurlNo, $retryableCurl, true);
                    if ($isRetry && $attempt < $maxAttempts) {
                        usleep((int)(500 * pow(2, $attempt - 1) + random_int(0, 250)) * 1000);
                        continue;
                    }
                    break;
                }

                // Si iMile devolviera 401 por URL/Token inválido, intentamos refrescar 1 vez
                if ($lastHttp === 401) {
                    $newTokenOrUrl = $this->tokenimile->getToken(true); // si tu lib soporta refresh
                    if ($newTokenOrUrl && $newTokenOrUrl !== $tokenOrUrl) {
                        $tokenOrUrl = $newTokenOrUrl;
                        if (is_resource($ch)) curl_close($ch);
                        $ch = $crearHandleImile($tokenOrUrl);
                        // reintenta el mismo intento (no consumas uno extra)
                        usleep(200 * 1000);
                        continue;
                    }
                }

                if ($lastHttp < 200 || $lastHttp >= 300) {
                    $isRetry = in_array($lastHttp, $retryableHttp, true);
                    if ($isRetry && $attempt < $maxAttempts) {
                        usleep((int)(600 * pow(2, $attempt - 1) + random_int(0, 300)) * 1000);
                        continue;
                    }
                    break;
                }

                // 2xx: intenta parsear, si no es JSON igual lo damos por bueno
                $parsed = json_decode($resp, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                    // puedes validar campos si iMile define alguno
                }

                $ok = true;
            } // while

            if ($ok) {
                $this->db->where('id', $r->id)->update('imile_outbox', [
                    'estado'        => 'ok',
                    'last_error'    => null,
                    'next_retry_at' => null,
                ]);
                $delivered++;
            } else {
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
                $this->db->where('id', $r->id)->update('imile_outbox', $update);
            }

            // Pequeña pausa para no saturar
            usleep(50 * 1000);
        } // foreach

        if (isset($ch) && is_resource($ch)) {
            curl_close($ch);
        }

        echo json_encode([
            'ok'          => true,
            'processed'   => $processed,
            'delivered'   => $delivered,
            'rescheduled' => $rescheduled,
            'dead'        => $dead,
        ], JSON_UNESCAPED_UNICODE);
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

public function eventos_post()
{
    $this->load->model('Entrega_model');
    $this->load->model('Manifiesto_model');
    $this->load->model('DTES_Model');
    $this->load->library('TokenImile');

    try {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        date_default_timezone_set("America/El_Salvador");
        header('Content-Type: application/json');

        // ---------- Leer JSON ----------
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

        $pod = $row['pod'];
        $urls = array_column($pod, 'url');
        $pod_final = json_encode(['pod' => $urls], JSON_UNESCAPED_SLASHES);

        $entregado         = 'NO';
        $codigo_recibido   = $row['codigo'] ?? null;
        $tracking_recibido = $row['guia']   ?? null;
        $fecha_response    = $row['fecha']  ?? date('Y-m-d H:i:s');

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
            $dtBase = new DateTime((string)$fecha_response);
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

        $masterId = $request_tracking->id_manifiesto;
        $id_pais = (int) $this->Manifiesto_model->obtener_id_pais($masterId);

        $vendor = '';
        $url_imile = '';

        if ($id_pais === 1) {
            $url_imile = 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjQwMzM1Ny1yZWNlaXZlVmVuZG9yVHJhY2s';
            $vendor    = '097823402254';
        } elseif ($id_pais === 2) {
            $url_imile = '';
            $vendor    = '';
        } elseif ($id_pais === 3) {
            $url_imile = 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjUwNDA3NS1yZWNlaXZlVmVuZG9yVHJhY2s';
            $vendor    = 'SRM2504075';
        }

        // ---------- Respuesta inmediata al webhook ----------
        //  Ya no hacemos exit aquí, solo enviamos respuesta y continuamos
        $this->_responderInmediatamente([
            'error'    => false,
            'message'  => 'Recibido correctamente. Procesando...',
            'tracking' => $tracking_recibido
        ]);

        // Continuamos con el procesamiento en segundo plano
        // (idealmente esto debería ir a una cola, pero por ahora sigue)

        // ---------- Anti-duplicados ----------
        $existe15 = $this->Manifiesto_model->verificar_pod($tracking_recibido, $codigo_recibido);
        if ($existe15) {
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
                (int)$codigo_recibido,
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
                (int)$codigo_recibido,
                "",
                $entregado
            );
            return;
        }

        // ======================
        // HELPERS
        // ======================

        $insertarEvento = function (int $id_estatus, DateTime $dt, string $problemaGestion = '', string $descripcion = '')
        use ($tracking_recibido, $fecha_iso, $id_pais) {
            $fechah  = $dt->format('Y-m-d H:i:s');
            $fechais = $dt->format(DateTime::ATOM);

            $this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);

            $datos = [
                'item'             => 1,
                'fecha'            => $fechah,
                'warehouse'        => $tracking_recibido,
                'id_estatus'       => $id_estatus,
                'casillero'        => '100',
                'id_pais'          => $id_pais,
                'pod_nombre'       => '',
                'pod_valor'        => '',
                'json_string'      => json_encode(['guia' => '']),
                'ruta_foto'        => '',
                'fechaiso'         => $fechais ?: $fecha_iso,
                'problema_gestion' => ($descripcion !== '' ? $descripcion : $problemaGestion),
            ];
            $this->Manifiesto_model->guardar_estatus($datos);
            $this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);
        };

        $resolverStatusImile = function (int &$id_estatus, string $problemaGestion = '') use (&$resolverStatusImile) {
            $statusName = '';
            if ($id_estatus === 16 || $id_estatus === 20) {
                $resultado = $this->Manifiesto_model->buscarPorDescripcionImile($problemaGestion ?? '');
                if ($resultado) {
                    $id_estatus = (int)$resultado[0]['codigo_cliente'];
                    if ($resultado[0]['codigo_cliente'] == 315) {
                        $id_estatus = 315;
                    }
                    $statusName = $resultado[0]['descripcion'];
                }
            } else {
                $resultado = $this->Manifiesto_model->buscarPorCodigoImile($id_estatus);
                if ($resultado) {
                    $statusName = $resultado[0]['descripcion'];
                }
            }
            return $statusName;
        };

        $resolverTokenImile = function (int $id_pais) use ($url_imile) {
            if ($url_imile && trim($url_imile) !== '') {
                return trim($url_imile);
            }

            try {
                if (!isset($this->tokenimile)) {
                    $this->load->library('TokenImile');
                }

                if (isset($this->tokenimile) && method_exists($this->tokenimile, 'getTokenByPais')) {
                    $val = $this->tokenimile->getTokenByPais($id_pais);
                    if ($val && trim($val) !== '') return trim($val);
                }

                if (isset($this->tokenimile) && method_exists($this->tokenimile, 'getToken')) {
                    $val = $this->tokenimile->getToken();
                    if ($val && trim($val) !== '') return trim($val);
                }
            } catch (Throwable $e) {
                log_message('error', 'TokenImile error: ' . $e->getMessage());
            }

            $envKey = match ($id_pais) {
                1 => 'IMILE_WEBHOOK_SV',
                2 => 'IMILE_WEBHOOK_GT',
                3 => 'IMILE_WEBHOOK_HN',
                default => null,
            };
            if ($envKey) {
                $env = getenv($envKey);
                if ($env && trim($env) !== '') return trim($env);
            }

            $cfgKey = match ($id_pais) {
                1 => 'imile_webhook_sv',
                2 => 'imile_webhook_gt',
                3 => 'imile_webhook_hn',
                default => null,
            };
            if ($cfgKey) {
                $cfg = $this->config->item($cfgKey);
                if ($cfg && trim($cfg) !== '') return trim($cfg);
            }

            return match ($id_pais) {
                1 => 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjQwMzM1Ny1yZWNlaXZlVmVuZG9yVHJhY2s',
                3 => 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjUwNDA3NS1yZWNlaXZlVmVuZG9yVHJhY2s',
                2 => null,
                default => null,
            };
        };

        $enqueueOutboxImile = function (string $tracking, int $id_estatus, string $statusName, DateTime $dt, array $extra = [], ?array $payloadOverride = null, $vendor) use ($pod_final) {
            $fechaEnvio = $dt->format('Y-m-d H:i:s');
            $payload = $payloadOverride ?: [
                'waybillNo'         => $tracking,
                'vendorWaybillNo'   => $vendor,
                'timeZone'          => 'GMT+8',
                'status'            => $id_estatus,
                'description'       => $statusName,
                'operationUserName' => 'Starship',
                'driverPhone'       => 'xxx',
                'ocCode'            => '10001',
                'ocName'            => 'PLL',
                'operateTime'       => $fechaEnvio,
                'pod'               => json_decode($pod_final, true)['pod'],
            ];

            $this->Entrega_model->enqueueOutboxImile(
                $tracking,
                $id_estatus,
                $statusName,
                $fechaEnvio,
                $payload,
                $extra
            );
        };

        $crearHandleImile = function (string $tokenOrUrl) {
            $url = stripos($tokenOrUrl, 'http') === 0
                ? $tokenOrUrl
                : ('https://scs.imile.com/tmsvendor/webhook/' . $tokenOrUrl);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER    => true,
                CURLOPT_POST              => true,
                CURLOPT_HTTPHEADER        => [
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
                CURLOPT_FOLLOWLOCATION    => true,
                CURLOPT_MAXREDIRS         => 5,
            ]);
            return $ch;
        };

        $enviarImile = function ($ch, int $id_estatus_envio, string $tracking, string $statusName, string $fechaEnvio, array $payload, string $problemaGestion = '') use (&$entregado, $enqueueOutboxImile) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            $maxAttempts     = 4;
            $attempt         = 0;
            $ok              = false;
            $respuesta_imile = null;

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
                    $isRetry = in_array($lastCurlNo, $retryableCurl, true);
                    $this->_registrarEventoInterno(
                        "iMile cURL intento {$attempt}" . ($isRetry ? " (retry)" : ""),
                        $tracking,
                        $isRetry ? 'warning' : 'fallido',
                        ['curl_errno' => $lastCurlNo, 'curl_error' => $lastCurlErr],
                        $id_estatus_envio,
                        $problemaGestion,
                        $entregado
                    );
                    if ($isRetry && $attempt < $maxAttempts) {
                        usleep((int)(500 * pow(2, $attempt - 1) + random_int(0, 250)) * 1000);
                        continue;
                    }
                    break;
                }

                if ($lastHttp < 200 || $lastHttp >= 300) {
                    $isRetry = in_array($lastHttp, $retryableHttp, true);
                    $this->_registrarEventoInterno(
                        "iMile HTTP {$lastHttp} intento {$attempt}" . ($isRetry ? " (retry)" : ""),
                        $tracking,
                        $isRetry ? 'warning' : 'fallido',
                        ['http_code' => $lastHttp, 'body' => $lastBody],
                        $id_estatus_envio,
                        $problemaGestion,
                        $entregado
                    );
                    if ($isRetry && $attempt < $maxAttempts) {
                        usleep((int)(600 * pow(2, $attempt - 1) + random_int(0, 300)) * 1000);
                        continue;
                    }
                    break;
                }

                $parsed = json_decode($resp, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                    $respuesta_imile = $parsed;
                } else {
                    $respuesta_imile = $resp;
                }

                $entregado = 'OK';
                $this->_registrarEventoInterno(
                    "iMile OK intento {$attempt} (estatus {$id_estatus_envio})",
                    $tracking,
                    'entregado',
                    ['http_code' => $lastHttp, 'resp' => $respuesta_imile],
                    $id_estatus_envio,
                    $problemaGestion,
                    $entregado
                );

                $ok = true;
            }

            if (!$ok) {
                $enqueueOutboxImile(
                    $tracking,
                    $id_estatus_envio,
                    $statusName,
                    new DateTime($fechaEnvio),
                    [
                        'curl_errno' => $lastCurlNo,
                        'curl_error' => $lastCurlErr,
                        'http_code'  => $lastHttp,
                        'body'       => $lastBody
                    ],
                    $payload,
                    $payload['vendorWaybillNo'] ?? ''
                );
            }

            return $ok ? $respuesta_imile : null;
        };

        // ======================
        // LÓGICA PRINCIPAL iMile
        // ======================
        $id_estatus_original = (int)$codigo_recibido;
        $observaciones       = $row['observaciones'] ?? '';
        $razon_desc          = $row['razon']['descripcion'] ?? '';
        $devolucion_flag     = (int)($row['devolucion'] ?? 0);

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

        // ------ FLUJO ESPECIAL (19) ------
        if ($id_estatus_original === 19) {
            if ($yaTiene19) {
                $insertarEvento(19, $dtBase, $observaciones, $observaciones);

                $id_envio = 19;
                $statusName19 = $resolverStatusImile($id_envio, $observaciones);

                if ($id_envio == 216 || $id_envio == 288) {
                    return;
                }

                $payload19 = [
                    'waybillNo'         => $tracking_recibido,
                    'vendorWaybillNo'   => $vendor,
                    'timeZone'          => 'GMT+8',
                    'status'            => $id_envio,
                    'description'       => $statusName19,
                    'operationUserName' => 'Starship',
                    'driverPhone'       => 'xxx',
                    'ocCode'            => '10001',
                    'ocName'            => 'PLL',
                    'operateTime'       => $dtBase->format('Y-m-d H:i:s'),
                ];

                $tokenOrUrl = $resolverTokenImile((int)$id_pais);
                if (!$tokenOrUrl) {
                    $enqueueOutboxImile($tracking_recibido, $id_envio, $statusName19, $dtBase, ['reason' => 'token_unavailable'], $payload19, $vendor);
                    $this->_registrarEventoInterno(
                        'Token/URL iMile no disponible: evento 19 encolado',
                        $tracking_recibido,
                        'warning',
                        [],
                        19,
                        $observaciones,
                        $entregado
                    );
                    return;
                }

                $ch = $crearHandleImile($tokenOrUrl);
                $enviarImile($ch, $id_envio, $tracking_recibido, $statusName19, $dtBase->format('Y-m-d H:i:s'), $payload19, $observaciones);
                curl_close($ch);
                return;
            }

            $dt219 = clone $dtBase;
            $dt10  = (clone $dtBase)->modify('+2 minutes');
            $dt19  = (clone $dtBase)->modify('+4 minutes');

            $insertarEvento(219, $dt219);
            $insertarEvento(10,  $dt10);
            $insertarEvento(19,  $dt19, $observaciones, $observaciones);

            $id219 = 219;
            $name219 = $resolverStatusImile($id219, '');
            $id10  = 10;
            $name10  = $resolverStatusImile($id10,  '');
            $id19  = 19;
            $name19  = $resolverStatusImile($id19,  $observaciones);

            $skip219 = ($id219 == 216 || $id219 == 288);
            $skip10  = ($id10  == 216 || $id10  == 288);
            $skip19  = ($id19  == 216 || $id19  == 288);

            $tokenOrUrl = $resolverTokenImile((int)$id_pais);
            if (!$tokenOrUrl) {
                if (!$skip219) $enqueueOutboxImile($tracking_recibido, $id219, $name219, $dt219, ['reason' => 'token_unavailable'], null, $vendor);
                if (!$skip10)  $enqueueOutboxImile($tracking_recibido, $id10,  $name10,  $dt10,  ['reason' => 'token_unavailable'], null, $vendor);
                if (!$skip19)  $enqueueOutboxImile($tracking_recibido, $id19,  $name19,  $dt19,  ['reason' => 'token_unavailable'], null, $vendor);

                $this->_registrarEventoInterno(
                    'Token/URL iMile no disponible: cadena 219/10/19 encolada',
                    $tracking_recibido,
                    'warning',
                    [],
                    19,
                    $observaciones,
                    $entregado
                );
                return;
            }

            $ch = $crearHandleImile($tokenOrUrl);

            if (!$skip219) {
                $enviarImile($ch, $id219, $tracking_recibido, $name219, $dt219->format('Y-m-d H:i:s'), [
                    'waybillNo'         => $tracking_recibido,
                    'vendorWaybillNo'   => $vendor,
                    'timeZone'          => 'GMT+8',
                    'status'            => $id219,
                    'description'       => $name219,
                    'operationUserName' => 'Starship',
                    'driverPhone'       => 'xxx',
                    'ocCode'            => '10001',
                    'ocName'            => 'PLL',
                    'operateTime'       => $dt219->format('Y-m-d H:i:s'),
                    
                ]);
            }

            curl_close($ch);
            return;
        }

        // ------ FLUJO NORMAL (no es 19) ------
        $id_estatus = (int)$row['codigo'];

        $devolucion = false;
        if ($id_estatus == 15 && $devolucion_flag === 1) {
            $id_estatus = 311;
            $devolucion = true;
        }

        $descripcion_for_detalle = '';
        if ($id_estatus == 16) {
            $descripcion_for_detalle = ($row['estatus'] ?? '') . ': ' . ($observaciones ?? '');
        }

        $insertarEvento($id_estatus, $dtBase, $razon_desc ?: $observaciones, $descripcion_for_detalle);

        $statusName = $resolverStatusImile($id_estatus, $razon_desc ?: $observaciones);
        if ($devolucion) {
            $statusName = 'RO';
        }

        if ($id_estatus == 216 || $id_estatus == 288) {
            return;
        }

        $payload = [
            'waybillNo'         => $tracking_recibido,
            'vendorWaybillNo'   => $vendor,
            'timeZone'          => 'GMT+8',
            'status'            => $id_estatus,
            'description'       => $statusName,
            'operationUserName' => 'Starship',
            'driverPhone'       => 'xxx',
            'ocCode'            => '10001',
            'ocName'            => 'PLL',
            'operateTime'       => $dtBase->format('Y-m-d H:i:s'),
            'pod'               => json_decode($pod_final, true)['pod']
        ];

       // echo json_encode($payload);
       // exit;
        
        $tokenOrUrl = $resolverTokenImile((int)$id_pais);
        if ($tokenOrUrl) {
            $ch = $crearHandleImile($tokenOrUrl);
            $enviarImile($ch, $id_estatus, $tracking_recibido, $statusName, $dtBase->format('Y-m-d H:i:s'), $payload, $razon_desc ?: $observaciones);
            curl_close($ch);
        } else {
            $enqueueOutboxImile($tracking_recibido, $id_estatus, $statusName, $dtBase, ['reason' => 'token_unavailable'], $payload, $vendor);
            $this->_registrarEventoInterno(
                'Token/URL iMile no disponible: evento encolado',
                $tracking_recibido,
                'warning',
                [],
                $id_estatus,
                $razon_desc ?: $observaciones,
                $entregado
            );
        }
        
    } catch (Throwable $e) {
        error_log('Error inesperado en eventos_post (iMile): ' . $e->getMessage());
        http_response_code(500);
        $this->_responderInmediatamente([
            'error'   => true,
            'mensaje' => 'Error interno del servidor.',
            'detalle' => $e->getMessage()
        ]);
    }
}

    
}
/* End of file Controllername.php */