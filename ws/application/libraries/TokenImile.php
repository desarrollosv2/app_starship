<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TokenImile {
    
    private $api_url = 'https://api.apg-global.com/api/account/login';

    // Token permanente (no caduca)
    private $fixed_token = 'U1VQUExJRVItU1JNMjQwMzM1Ny1yZWNlaXZlVmVuZG9yVHJhY2s';
    
    public function login($username = null, $password = null) {
        // Si existe token fijo, simplemente retornarlo en el mismo formato esperado
        return [
            'data' => [
                'token' => $this->fixed_token
            ],
            'status' => 'success',
            'message' => 'Token estático utilizado'
        ];

        /* 
        // Código original deshabilitado (por si algún día quieres reactivarlo)
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                "userName" => $username,
                "passWord" => $password
            ]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
        */
    }
}
