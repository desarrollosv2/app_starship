<?php
defined('BASEPATH');

class ApiImileController extends CI_Controller
{

    public function __construct()
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        parent::__construct();
        $this->load->model('Gestion_Model');
    }

    public function prueba_vista(){
        echo "aqui estamos";
    }

    public function enviar_eventos($id_manifiesto)
    {

        // Aumentamos tiempo y memoria por seguridad
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        
        $limit = null; // número de registros por lote
        $offset = 0;
        $totalEnviados = 0;
        $resultados = [];

        do {
            $detalles = $this->Gestion_Model->obtener_detalle_manifiesto($id_manifiesto, $limit, $offset);


            //var_dump($detalles);
            //die();
            if (empty($detalles)) break;

            foreach ($detalles as $detalle) {

                // Determinamos observación según id_estatus
                $observaciones = $detalle->nombre;
                 

                if ((int)$detalle->id_estatus === 19 && !empty($detalle->problema_gestion)) {
                    $observaciones = $detalle->problema_gestion;
                } elseif ((int)$detalle->id_estatus === 16 && !empty($detalle->problema_gestion)) {
                    $observaciones = $detalle->problema_gestion;
                }

                // Construimos el payload
                /*$payload = [
                    "guia"         => $detalle->tracking_number,
                    "codigo"       => $detalle->id_estatus,
                    "estatus"      => $detalle->nombre,
                    "observaciones" => $observaciones,
                    "fecha"        => $detalle->fecha,
                    "latitud"      => "13.699411",
                    "longitud"     => "-89.149612",
                    "devolucion"   => false,
                    "pod"          => []

                ];*/

                $payload =[
                    "waybillNo" => $detalle->tracking_number,
                    "vendorWaybillNo" => "097823402254",
                    "timeZone" => "GMT+8",
                    "status" => $detalle->id_estatus,
                    "description" => $observaciones, 
                    "operationUserName" => "tony",
                    "driverPhone" => "xxx",
                    "ocCode" => "10001",
                    "ocName" => "网点名称",
                    "operateTime" => $detalle->fecha,
                    "pod" => array()
                ];

                //var_dump($payload);

                //die();

                

                // Enviar a API
                $response = $this->_enviar_a_api($payload, $detalle->id);

                var_dump($response);

                

            

                $resultados[] = [
                    "guia" => $detalle->tracking_number,
                    "estatus" => $detalle->id_estatus,
                    "observaciones" => $observaciones,
                    //"response" => $response
                ];

                

                $totalEnviados++;

                // Pequeña pausa para no saturar la API (0.5 segundos)
                //usleep(500000);
            }


            echo json_encode([
                "success" => true,
                "total_enviados" => $totalEnviados,
                "resultados" => $resultados
            ]);

            die();


            $offset += $limit; // Siguiente bloque

        } while (!empty($detalles));

        echo json_encode([
            "success" => true,
            "total_enviados" => $totalEnviados,
            "resultados" => $resultados
        ]);
    }

    /**
     * Función privada para enviar cada registro a la API remota
     */

    private function _enviar_a_api($payload, $id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            //CURLOPT_URL => 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjQwMzM1Ny1yZWNlaXZlVmVuZG9yVHJhY2s',
            
            CURLOPT_URL => 'https://scs.imile.com/tmsvendor/webhook/U1VQUExJRVItU1JNMjUwNDA3NS1yZWNlaXZlVmVuZG9yVHJhY2s',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
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


        //return json_encode($payload);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        
        $this->Gestion_Model->marcar_como_enviado($id, $response, $error, json_encode($payload));
        

        return $error
            ? ["error" => true, "message" => $error]
            : json_decode($response, true);
    }
    
}

?>
