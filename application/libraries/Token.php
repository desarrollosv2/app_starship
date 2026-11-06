<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Token {
    public function getToken($token_cliente, $id_tokencliente)
    {
        // Configuración de headers y datos
        $header = array(
            'Host: tracking.com',
            'Content-Type: application/json',
            'Cache-Control: no-cache'
        );

        $data = array(
            "grant_type" => "client_credentials",
            "client_id" => $id_tokencliente,
            "client_secret" => $token_cliente,
            "scope" => "*"
        );

		$url = "https://tracking.mailamericas.com/oauth/token"; 

        // Inicializa cURL
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $code = json_decode($response);
        return isset($code->access_token) ? $code->access_token : null;
    }
}
