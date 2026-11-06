<?php defined('BASEPATH') or exit('No direct script access allowed');

class GeneralWorker extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if (!$this->input->is_cli_request()) { show_404(); }
        $this->load->library('RedisClient');
        $this->load->model('GeneralOutbox_model');
        $this->load->library('TokenApg'); // para APG
    }

    public function run() {
        $stream   = 'stream:general';
        $group    = 'group:general:workers';
        $consumer = gethostname().':'.getmypid();

        // Crear grupo si no existe
        try { $this->redisclient->redis->xgroup('CREATE', $stream, $group, '$', true); } catch (Throwable $e) {}

        while (true) {
            $messages = $this->redisclient->redis->xreadgroup($group, $consumer, [$stream => '>'], 100, 5000);
            if (!$messages) { continue; }

            foreach ($messages as $sName => $entries) {
                foreach ($entries as $msgId => $fields) {
                    $event_id   = $fields['event_id']   ?? null;
                    $outbox_id  = (int)($fields['outbox_id'] ?? 0);
                    $carrier    = $fields['carrier']    ?? '';
                    $payloadStr = $fields['payload']    ?? '{}';

                    // Idempotencia
                    if ($this->already_done($event_id)) {
                        $this->redisclient->redis->xack($stream, $group, [$msgId]);
                        continue;
                    }

                    $ok = false;
                    try {
                        if ($carrier === 'apg') {
                            $ok = $this->send_to_apg($payloadStr);
                        } else {
                            // TODO: otros carriers (imile, ma, newco...) con sus adapters
                            $ok = false;
                        }
                    } catch (Throwable $e) {
                        $ok = false;
                    }

                    if ($ok) {
                        $this->mark_done($event_id);
                        if ($outbox_id) {
                            $this->db->where('id', $outbox_id)->update('general_outbox', [
                                'estado'        => 'ok',
                                'last_error'    => null,
                                'next_retry_at' => null,
                                'updated_at'    => date('Y-m-d H:i:s'),
                            ]);
                        }
                        $this->redisclient->redis->xack($stream, $group, [$msgId]);
                    } else {
                        // No ACK => queda en PEL. Opcional: actualizar intentos/next_retry_at en MySQL
                        if ($outbox_id) {
                            $this->db->set('intentos', 'intentos+1', false)
                                     ->set('updated_at', date('Y-m-d H:i:s'));
                            // backoff simple 5,10,20,40...
                            $schedule = [5,10,20,40,80,160,320,640];
                            $r = $this->db->select('intentos')->from('general_outbox')->where('id',$outbox_id)->get()->row();
                            $tries = ($r && isset($r->intentos)) ? (int)$r->intentos + 1 : 1;
                            $mins  = $schedule[min(max($tries-1,0), count($schedule)-1)];
                            $this->db->set('next_retry_at', date('Y-m-d H:i:s', time()+$mins*60));
                            if ($tries >= 8) { $this->db->set('estado', 'dead'); }
                            $this->db->where('id', $outbox_id)->update('general_outbox');
                        }
                        // Tip: puedes mover a DLQ con XADD si supera N intentos y luego hacer XACK
                    }
                }
            }
        }
    }

    private function already_done(?string $event_id): bool {
        if (!$event_id) return false;
        $key = "dedupe:general:$event_id";
        $ok  = $this->redisclient->redis->setnx($key, 1);
        if ($ok) { $this->redisclient->redis->expire($key, 3*24*3600); } // 3 días
        return !$ok;
    }

    private function mark_done(string $event_id): void {
        // con SETNX + TTL ya alcanza; opcional guardar otro estado
    }

    private function send_to_apg(string $payloadStr): bool {
        $apgUrl = 'https://apiprod.apg-global.com/api/account/save-status';
        $token  = $this->tokenapg->getToken();
        if (!$token) return false;

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
            CURLOPT_POSTFIELDS        => $payloadStr,
            CURLOPT_TIMEOUT           => 60,
            CURLOPT_CONNECTTIMEOUT    => 20,
            CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
            CURLOPT_NOSIGNAL          => true,
            CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
            CURLOPT_DNS_CACHE_TIMEOUT => 300,
            CURLOPT_FORBID_REUSE      => false,
            CURLOPT_FRESH_CONNECT     => false,
        ]);

        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http < 200 || $http >= 300) return false;

        $parsed = json_decode($resp, true);
        if (json_last_error() !== JSON_ERROR_NONE) return false;

        $respOK          = $parsed['objectResult']['response'] ?? null;
        $respStatusField = $parsed['objectResult']['status'][0]['response'] ?? null;
        $isSuccess       = $parsed['isSuccess'] ?? null;

        return ($respOK === 'OK' || $respStatusField === 'Created' || $isSuccess === true);
    }
}
