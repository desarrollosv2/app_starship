<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');

defined('BASEPATH') or exit('No direct script access allowed');
//include getcwd() . "/libraries/RestController.php";

require(APPPATH . '/libraries/REST_Controller.php');
//use Restserver\Libraries\RestController;
class Api_pw extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Pw_model');
    }

    public function verificar_cuenta_get()
    {
        $id = $this->uri->segment(3);
        if (!isset($id)) {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'Es necesario el número de NIT'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if ($id == " ") {
        } else {
            $id = str_replace("%20", " ", $id);
        }
        $casillero = $this->Pw_model->verificar_cuenta($id);
        //var_dump($casillero);
        if (($casillero)) {
            $respuesta = array(
                'err' => false,
                'mensaje' => 'Registro cargado correctamente',
                'cliente' => $casillero
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'err' => true,
                'mensaje' => 'NIT: ' . $id . ', no está asociado a ninguna cuenta ',
                'cuenta' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }
    public function impuestos_post()
    {
        $raw = $this->input->raw_input_stream;
        $payload = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response([
                'ok'      => false,
                'mensaje' => 'JSON inválido',
                'error'   => json_last_error_msg(),
            ], 400);
        }


        // Esperamos: { "id": 582, "referencia": "...", "impuestos": [ ... ] }
        $id_manifiesto = $payload['id'] ?? null;

        $client = $this->Pw_model->verificarCliente($id_manifiesto);

      //  var_dump($client);
      //   exit;
        $items = $payload['impuestos'] ?? null;

        if ($id_manifiesto === null || !is_array($items)) {
            return $this->response([
                'ok'      => false,
                'mensaje' => 'Faltan campos requeridos: id o impuestos[]',
            ], 400);
        }

        $actualizados = 0;
        $fallidos     = 0;
        $errores      = [];
        $detalles     = [];

        $this->db->trans_start();

        foreach ($items as $i => $row) {
            $tracking = isset($row['tracking']) ? (string)$row['tracking'] : null;
            $dai      = array_key_exists('dai', $row) ? $row['dai'] : null;
            $iva      = array_key_exists('iva', $row) ? $row['iva'] : null;

            if ($tracking === null) {
                $fallidos++;
                $errores[] = ['index' => $i, 'mensaje' => 'Falta tracking'];
                continue;
            }

            // Normaliza: solo dígitos (por si viniera con espacios o guiones)
            $tracking = preg_replace('/\D+/', '', trim($tracking));

            // Normaliza: solo dígitos (por si viniera con espacios o guiones)
            $tracking = preg_replace('/\D+/', '', trim($tracking));

            // --- Nueva lógica de prefijos según cliente/pais ---
            if ($client->client_id == 107  && $client->id_pais == 1) {
                $tracking_prefijo = "S" . $tracking;
                
            } elseif ($client->client_id == 10 && $client->id_pais == 1) {
                $tracking_prefijo = "S" . $tracking;
                
            } else {
                $tracking_prefijo = $tracking;
               
            }

            // 1) Buscar coincidencia exacta por prefijo dentro de ese id_manifiesto
            $match = $this->Pw_model->find_tracking_unique_by_prefix($id_manifiesto, $tracking_prefijo);

            // 2) Si no encuentra nada y aplica el caso de client_id=10/id_pais=1, intentar con prefijo "T"
            if (!$match && $client->client_id == 107 && $client->id_pais == 1) {
                $tracking_alt = "T" . $tracking;
                $match = $this->Pw_model->find_tracking_unique_by_prefix($id_manifiesto, $tracking_alt);
            }

            if (!$match) {
                $fallidos++;
                $errores[] = [
                    'index'       => $i,
                    'tracking_in' => $tracking,
                    'mensaje'     => 'No se encontró coincidencia por prefijo'
                ];
                continue;
            }
            if (isset($match['ambiguous']) && $match['ambiguous'] === true) {
                // No debería pasar según tu dato, pero lo controlamos
                $fallidos++;
                $errores[] = [
                    'index'       => $i,
                    'tracking_in' => $tracking,
                    'mensaje'     => 'Se hallaron múltiples coincidencias (no se actualizó)'
                ];
                continue;
            }

            // 2) Actualizar SOLO esa fila exacta
            $afectadas = $this->Pw_model->update_impuesto_by_exact_tracking(
                $id_manifiesto,
                $match['tracking_number'],
                $dai,
                $iva
            );

            if ($afectadas > 0) {
                $actualizados++;
                $detalles[] = [
                    'index'          => $i,
                    'tracking_in'    => $tracking,
                    'tracking_match' => $match['tracking_number'],
                    'actualizado'    => true
                ];
            } else {
                // Sin cambios (p. ej. ya tenían esos valores)
                $detalles[] = [
                    'index'          => $i,
                    'tracking_in'    => $tracking,
                    'tracking_match' => $match['tracking_number'],
                    'actualizado'    => false,
                    'mensaje'        => 'Sin cambios (valores idénticos o no afectó filas)'
                ];
            }
        }

        $this->db->trans_complete();

        return $this->response([
            'ok'           => $fallidos === 0,
            'mensaje'      => ($fallidos === 0)
                ? 'Impuestos procesados correctamente'
                : 'Procesado con algunos errores',
            'referencia'   => $payload['referencia'] ?? null,
            'id'           => $id_manifiesto,
            'procesados'   => count($items),
            'actualizados' => $actualizados,
            'fallidos'     => $fallidos,
            'detalles'     => $detalles,
            'errores'      => $errores,
        ], 200);
    }
}

/* End of file Controllername.php */