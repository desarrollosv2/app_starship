<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiRequestimile {
// con esto cambiamos estatus atraves de nuestro webhook, configurado en sito de xpresss
    private $api_url = 'https://stargateserp.com/app_starship/ws/index.php/apimile/eventos';
    private $api_key = '4a4da5bb348e648e51d84e24c8652d1284c31156';

    public function enviarEstado($guia, $codigo, $estatus, $observaciones, $fecha, $latitud, $longitud, $pod = []) {
        $postData = json_encode([
            "guia" => $guia,
            "codigo" => $codigo,
            "estatus" => $estatus,
            "observaciones" => $observaciones,
            "fecha" => $fecha,
            "latitud" => $latitud,
            "longitud" => $longitud,
            "pod" => $pod
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                "x-api-key: {$this->api_key}",
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
