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
class Apg_events extends REST_Controller
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
        $this->load->model('Entrega_model');
    }


    public function estadosApg_post()
    {
        $this->load->library('TokenApg');

        try {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);

            date_default_timezone_set("America/El_Salvador");
            $date      = date('Y-m-d H:i:s');
            $fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
            $fecha_iso = $fecha_tmp->format(DateTime::ATOM);

            // Lee JSON de entrada
            $rawData = file_get_contents('php://input');
            $row = json_decode($rawData, true) ?: [];

            $codigo_recibido   = $row['codigo'] ?? null;
            $tracking_recibido = $row['guia']   ?? null;
            $fecha             = $row['fecha']  ?? $date;

            // ✅ Respuesta inmediata al primer sistema
            $this->_responderInmediatamente([
                'error'   => false,
                'message' => 'Recibido correctamente.',
                'tracking' => $tracking_recibido
            ]);

            // Validación mínima de payload
            if (!$tracking_recibido || !$codigo_recibido) {
                // registra el problema para poder verlo en tu log
                $this->Entrega_model->actualizar_respuesta(
                    ['error' => 'Payload incompleto', 'payload' => $row],
                    $tracking_recibido,
                    null,
                    $codigo_recibido
                );
                return;
            }

            // Construcción de statusName
            $id_estatus  = (int) $codigo_recibido;
            $description = '';
            if ($id_estatus === 16) {
                $description     = ($row['estatus'] ?? '') . ': ' . ($row['observaciones'] ?? '');
                $problemaGestion = $row['razon']['descripcion'] ?? '';
            }

            if ($id_estatus === 16 || $id_estatus === 19) {
                $resultado  = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
                $statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
            } else {
                $resultado  = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
                $statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
            }

            // Token APG
            $token = $this->tokenapg->getToken();
            if (!$token) {
                $this->Entrega_model->actualizar_respuesta(
                    ['error' => 'No se pudo obtener token APG'],
                    $tracking_recibido,
                    null,
                    $codigo_recibido
                );
                return;
            }

            // Llamada a APG
            $url  = 'https://apiprod.apg-global.com/api/account/save-status';
            $data = [
                'tracking' => $tracking_recibido,
                'status'   => [[
                    'statusName' => $statusName,
                    'location'   => 'El Salvador',
                    'statusDate' => $fecha,   // formato esperado por APG
                    'pod'        => null,
                ]],
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
                'Accept: application/json',
            ]);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response  = curl_exec($ch);
            $curl_err  = curl_error($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($curl_err) {
                // Guarda el error para que “se vea el registro”
                $this->Entrega_model->actualizar_respuesta(
                    ['error' => $curl_err, 'http_code' => $http_code],
                    $tracking_recibido,
                    null,
                    $codigo_recibido
                );
                return;
            }

            // Decodifica respuesta de APG
            $respuesta_apg = json_decode($response, true);
            if ($respuesta_apg === null) {
                $this->Entrega_model->actualizar_respuesta(
                    ['error' => 'Respuesta APG no JSON', 'raw' => $response, 'http_code' => $http_code],
                    $tracking_recibido,
                    null,
                    $codigo_recibido
                );
                return;
            }

            // ✅ AHORA sí, toma el valor desde la respuesta real
            $valor = $respuesta_apg['objectResult']['response'] ?? null;

            // Guarda todo para poder consultarlo
            $this->Entrega_model->actualizar_respuesta(
                $respuesta_apg,
                $tracking_recibido,
                $valor,
                $codigo_recibido
            );
            echo $tracking_recibido;
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


    private function _registrarEventoInterno($descripcion, $tracking, $estado = 'info', $respuesta = null, $id_estatus = null)
    {
        $this->Entrega_model->registrar([
            'tracking'    => $tracking,
            'id_estatus'  => $id_estatus,
            'estado'      => $estado,
            'mensaje'     => $descripcion,
            'respuesta'   => is_array($respuesta) ? json_encode($respuesta) : $respuesta
        ]);
    }
    private function _actualizarEventoInterno($descripcion, $tracking, $estado = 'info', $respuesta = null, $id_estatus = null)
    {
        $this->Entrega_model->registrar([
            'tracking'    => $tracking,
            'id_estatus'  => $id_estatus,
            'estado'      => $estado,
            'mensaje'     => $descripcion,
            'respuesta'   => is_array($respuesta) ? json_encode($respuesta) : $respuesta
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

        // ✂️ Cierra la conexión HTTP
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
}
/* End of file Controllername.php */