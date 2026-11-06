<?php defined('BASEPATH') or exit('No direct script access allowed');

class GeneralOutbox_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Genera un event_id único e idempotente.
     * Formato: carrier:tracking:id_estatus:YYYYMMDDHHMMSS (sin espacios)
     */
    private function make_event_id(string $carrier, string $tracking, int $id_estatus, ?string $fechaEnvio): string
    {
        $carrier  = strtolower(trim($carrier));
        $tracking = trim($tracking);
        $tsSrc    = $fechaEnvio ? trim($fechaEnvio) : date('Y-m-d H:i:s');
        $ts       = preg_replace('/\D/', '', $tsSrc); // solo dígitos
        return sprintf('%s:%s:%d:%s', $carrier, $tracking, (int)$id_estatus, $ts);
    }

    /**
     * Encola un evento general e idempotente.
     * Si el mismo event_id ya existe, NO falla: devuelve el mismo id.
     */
    public function enqueueGeneral(
    string $carrier,
    int $company_id,
    string $tracking,
    int $id_estatus,
    string $status_name,
    array $payload,
    ?string $event_id = null,
    ?string $fechaEnvio = null,
    $meta = null
): array {
    $now        = date('Y-m-d H:i:s');
    $fechaEnvio = $fechaEnvio ?: $now;

    // Normaliza
    $carrier     = strtolower(trim($carrier));
    $tracking    = trim($tracking);
    $status_name = (string)$status_name;
    $event_id    = $event_id ? trim($event_id) : $this->make_event_id($carrier, $tracking, (int)$id_estatus, $fechaEnvio);

    $row = [
        'carrier'       => $carrier,
        'company_id'    => (int)$company_id,
        'tracking'      => $tracking,
        'id_estatus'    => (int)$id_estatus,
        'status_name'   => $status_name,
        'payload'       => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'event_id'      => $event_id,
        'fecha_envio'   => $fechaEnvio,
        'estado'        => 'pending',
        'intentos'      => 0,
        'next_retry_at' => date('Y-m-d H:i:s', time() + 300),
        'created_at'    => $now,
        'updated_at'    => $now,
        'meta'          => $meta !== null
            ? ((is_array($meta) || is_object($meta))
                ? json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : (string)$meta)
            : null,
    ];

    // --- Desactiva db_debug SOLO durante el insert para evitar HTML de error ---
    $prevDebug = $this->db->db_debug;
    $this->db->db_debug = false;

    try {
        $ok = $this->db->insert('general_outbox', $row);

        if ($ok) {
            $id = $this->db->insert_id();

            // Publica en Redis si está disponible
            if (isset($this->redis)) {
                $this->redis->xAdd('general_outbox_stream', '*', [
                    'id'          => (string)$id,
                    'carrier'     => $row['carrier'],
                    'company_id'  => (string)$row['company_id'],
                    'tracking'    => $row['tracking'],
                    'id_estatus'  => (string)$row['id_estatus'],
                    'status_name' => $row['status_name'],
                    'payload'     => $row['payload'],
                    'meta'        => $row['meta'] ?? '',
                ]);
            }

            return ['id' => $id, 'event_id' => $event_id, 'was_duplicate' => false];
        }

        // Lee el error sin romper la ejecución
        if (method_exists($this->db, 'error')) {
            $err    = $this->db->error();
            $errno  = (int)($err['code'] ?? 0);
            $errmsg = (string)($err['message'] ?? 'Unknown DB error');
        } else {
            // Compatibilidad CI2 (solo si existen)
            $errno  = method_exists($this->db, '_error_number')  ? (int)$this->db->_error_number()   : 0;
            $errmsg = method_exists($this->db, '_error_message') ? (string)$this->db->_error_message(): 'Unknown DB error';
        }

        // Idempotencia: si es duplicado por ux_event, devuelve el existente
        if ($errno === 1062) {
            $existing = $this->db->select('id')
                ->from('general_outbox')
                ->where('event_id', $event_id)
                ->limit(1)
                ->get()
                ->row();
            if ($existing) {
                return ['id' => (int)$existing->id, 'event_id' => $event_id, 'was_duplicate' => true];
            }
            // Caso raro: duplicado pero no se puede leer
            return ['id' => 0, 'event_id' => $event_id, 'was_duplicate' => true];
        }

        // Otros errores
        throw new RuntimeException('DB insert error en general_outbox (' . $errno . '): ' . $errmsg);

    } finally {
        // Restaura db_debug pase lo que pase
        $this->db->db_debug = $prevDebug;
    }
}


    public function markOk(int $id): bool
    {
        return (bool)$this->db->where('id', $id)->update('general_outbox', [
            'estado'        => 'ok',
            'last_error'    => null,
            'next_retry_at' => null,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);
    }

    public function markRetry(int $id, array $lastError, int $intentos): bool
    {
        $schedule = [5, 10, 20, 40, 80, 160, 320, 640]; // minutos
        $idx  = min(max($intentos - 1, 0), count($schedule) - 1);
        $mins = $schedule[$idx];
        $jsec = random_int(0, 30);
        $next = date('Y-m-d H:i:s', time() + $mins * 60 + $jsec);

        return (bool)$this->db->where('id', $id)->update('general_outbox', [
            'estado'        => 'pending',
            'intentos'      => $intentos,
            'last_error'    => json_encode($lastError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'next_retry_at' => $next,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);
    }

    public function markDead(int $id, array $lastError): bool
    {
        return (bool)$this->db->where('id', $id)->update('general_outbox', [
            'estado'        => 'dead',
            'last_error'    => json_encode($lastError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at'    => date('Y-m-d H:i:s')
        ]);
    }
}
