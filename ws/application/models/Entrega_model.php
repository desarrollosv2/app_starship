<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Entrega_model extends CI_Model
{
    protected $tabla = 'log_entregas';

    public function __construct()
    {
        parent::__construct();
    }

    public function registrar(array $data)
    {
        $this->db->insert($this->tabla, [
            'tracking'         => $data['tracking'],
            'id_estatus'       => $data['id_estatus'] ?? null,
            'estado'           => $data['estado'],
            'mensaje'          => isset($data['mensaje']) ? $data['mensaje'] : null,
            'respuesta'        => isset($data['respuesta']) ? $data['respuesta'] : null,
            'fecha'            => date('Y-m-d H:i:s'),
            'problema_gestion' => $data['problema_gestion'],
            'entregado'        => $data['entregado'],
        ]);
    }

    public function actualizar_respuesta($response, $tracking, $valor, $id_estatus)
    {
        // Normalizar a JSON válido para la columna `respuesta`
        if (is_array($response) || is_object($response)) {
            $respuesta_json = json_encode($response, JSON_UNESCAPED_UNICODE);
        } else {
            $str = (string) $response;
            $respuesta_json = (json_decode($str, true) === null)
                ? json_encode(['error' => $str], JSON_UNESCAPED_UNICODE) // envuelvo texto plano
                : $str; // ya venía como JSON válido
        }

        // Opcional: mapear entregado (solo si viene)
        $data = ['respuesta' => $respuesta_json];
        if ($valor !== null) {
            $data['entregado'] = (is_bool($valor) ? ($valor ? 'OK' : 'NO') : (string)$valor);
        }

        $this->db->where('tracking', $tracking)
                 ->where('id_estatus', (int)$id_estatus)
                 ->update('log_entregas', $data);

        return $this->db->affected_rows(); // útil para saber si actualizó algo
    }

    public function UpdateRespuesta(array $data)
    {
        $tracking      = $data['tracking']      ?? null;
        $id_estatus    = $data['id_estatus']    ?? null;
        $respuesta     = $data['respuesta']     ?? null;
        $entregado     = $data['entregado']     ?? null;
        $mala_gestion  = $data['mala_gestion']  ?? null;
        $req           = $data['req']           ?? null; // ← nuevo

        if ($tracking === null || $id_estatus === null) {
            log_message('error', 'UpdateRespuesta: tracking o id_estatus nulos');
            return false;
        }

        // Normaliza a string JSON si viniera array/objeto
        if (is_array($respuesta) || is_object($respuesta)) {
            $respuesta = json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        if (is_array($req) || is_object($req)) {
            $req = json_encode($req, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $this->db->set('respuesta', $respuesta);
        $this->db->set('mala_gestion', $mala_gestion);
        $this->db->set('entregado', $entregado);
        $this->db->set('req', $req); // ← guarda el JSON crudo recibido

        $this->db->where('id_estatus', (int)$id_estatus);
        $this->db->where('warehouse', $tracking);
        $this->db->limit(1); // opcional

        $ok = $this->db->update('detalle');
        if (!$ok) {
            log_message('error', 'UpdateRespuesta UPDATE failed: ' . $this->db->last_query());
            return false;
        }
        return ($this->db->affected_rows() >= 0);
    }

    /* =========================
     *  AIRBAGS / HELPERS
     * ========================= */

    private function detect_carrier_from_payload(array $payload): ?string
    {
        // iMile
        if (isset($payload['waybillNo']) || isset($payload['vendorWaybillNo'])) {
            return 'imile';
        }
        // APG
        if (isset($payload['status']) && is_array($payload['status']) && isset($payload['status'][0]['statusName'])) {
            return 'apg';
        }
        // MA (ajusta a tus claves reales si difieren)
        if (isset($payload['ma_id']) || isset($payload['ma_ref']) || isset($payload['maStatus'])) {
            return 'ma';
        }
        return null;
    }

    private function trace_caller_for_outbox(string $targetTable, string $tracking, $id_estatus, array $payload, $note = '')
    {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8);
        $frames = [];
        foreach ($bt as $f) {
            $frames[] = sprintf(
                '%s:%s@%s',
                isset($f['file']) ? basename($f['file']) : '?',
                $f['line'] ?? '?',
                $f['function'] ?? '?'
            );
        }
        log_message('warning', sprintf(
            'OUTBOX INSERT [%s] tracking=%s id_estatus=%s note=%s caller=%s payload_keys=%s',
            $targetTable,
            $tracking,
            (string)$id_estatus,
            $note,
            implode(' > ', $frames),
            implode(',', array_keys($payload))
        ));
    }

    /* =========================
     *  ENQUEUE CON BLINDAJE
     * ========================= */

    // APG
    public function enqueueOutbox($tracking, $id_estatus, $statusName, $fechaEnvio, array $payload, $lastError = null)
    {
        $carrierGuess = $this->detect_carrier_from_payload($payload);
        if ($carrierGuess === 'imile') {
            // Redirige a imile_outbox y deja evidencia
            $this->trace_caller_for_outbox('apg_outbox->imile_outbox', $tracking, $id_estatus, $payload, 'redir_por_payload_imile');
            return $this->enqueueOutboxImile($tracking, $id_estatus, $statusName, $fechaEnvio, $payload, $lastError);
        }
        if ($carrierGuess === 'ma') {
            // Redirige a ma_outbox y deja evidencia
            $this->trace_caller_for_outbox('apg_outbox->ma_outbox', $tracking, $id_estatus, $payload, 'redir_por_payload_ma');
            return $this->enqueueOutboxma($tracking, $id_estatus, $statusName, $fechaEnvio, $payload, $lastError, 5);
        }

        $row = [
            'tracking'      => $tracking,
            'id_estatus'    => (int)$id_estatus,
            'status_name'   => $statusName,
            'fecha_envio'   => $fechaEnvio,
            'payload'       => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'estado'        => 'pending',
            'intentos'      => 0,
            'last_error'    => $lastError ? (is_array($lastError) ? json_encode($lastError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$lastError) : null,
            'next_retry_at' => date('Y-m-d H:i:s', time() + 300), // 5 minutos
        ];
        $this->trace_caller_for_outbox('apg_outbox', $tracking, $id_estatus, $payload);
        return $this->db->insert('apg_outbox', $row);
    }

    // iMile
    public function enqueueOutboxImile($tracking, $id_estatus, $statusName, $fechaEnvio, array $payload, $lastError = null)
    {
        $carrierGuess = $this->detect_carrier_from_payload($payload);
        if ($carrierGuess && $carrierGuess !== 'imile') {
            // Recibió algo que no parece iMile; lo guardamos igual en imile_outbox pero avisamos
            $this->trace_caller_for_outbox('imile_outbox(payload_raro)', $tracking, $id_estatus, $payload, 'payload_no_imile');
        }

        $row = [
            'tracking'      => $tracking,
            'id_estatus'    => (int)$id_estatus,
            'status_name'   => $statusName,
            'fecha_envio'   => $fechaEnvio,
            'payload'       => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'estado'        => 'pending',
            'intentos'      => 0,
            'last_error'    => $lastError ? (is_array($lastError) ? json_encode($lastError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$lastError) : null,
            'next_retry_at' => date('Y-m-d H:i:s', time() + 300), // 5 minutos
        ];
        $this->trace_caller_for_outbox('imile_outbox', $tracking, $id_estatus, $payload);
        return $this->db->insert('imile_outbox', $row);
    }

    // MA
    public function enqueueOutboxma($tracking, $id_estatus, $statusName, $fechaEnvio, array $payload, $lastError = null, $retryMinutes = 5)
    {
        $carrierGuess = $this->detect_carrier_from_payload($payload);

        if ($carrierGuess === 'imile') {
            $this->trace_caller_for_outbox('ma_outbox->imile_outbox', $tracking, $id_estatus, $payload, 'redir_por_payload_imile');
            return $this->enqueueOutboxImile($tracking, $id_estatus, $statusName, $fechaEnvio, $payload, $lastError);
        }
        if ($carrierGuess === 'apg') {
            $this->trace_caller_for_outbox('ma_outbox->apg_outbox', $tracking, $id_estatus, $payload, 'redir_por_payload_apg');
            return $this->enqueueOutbox($tracking, $id_estatus, $statusName, $fechaEnvio, $payload, $lastError);
        }

        $row = [
            'tracking'      => $tracking,
            'id_estatus'    => (int)$id_estatus,
            'status_name'   => $statusName,
            'fecha_envio'   => $fechaEnvio,
            'payload'       => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'estado'        => 'pending',
            'intentos'      => 0,
            'last_error'    => $lastError ? (is_array($lastError) ? json_encode($lastError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$lastError) : null,
            'next_retry_at' => date('Y-m-d H:i:s', time() + max(1, (int)$retryMinutes) * 60),
        ];
        $this->trace_caller_for_outbox('ma_outbox', $tracking, $id_estatus, $payload);
        $ok = $this->db->insert('ma_outbox', $row);
        return $ok ? $this->db->insert_id() : false;
    }

     public function  facturar($data)
    {
        $this->db->insert('control_facturacion', $data);
        return ($this->db->affected_rows() > 0);
    }
}
