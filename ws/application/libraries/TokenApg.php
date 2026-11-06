<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TokenApg
{
    private $api_url = 'https://apiprod.apg-global.com/api/account/login';
    private $token_cache_file;

    public function __construct()
    {
        // Ruta para guardar el token en cache temporal
        $this->token_cache_file = APPPATH . 'cache/apg_token.json';
    }

    public function login($username, $password)
    {
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
    }

    public function getToken()
    {
        // Revisar si ya existe token guardado y aún es válido
        if (file_exists($this->token_cache_file)) {
            $data = json_decode(file_get_contents($this->token_cache_file), true);

            if (isset($data['token']) && isset($data['expires_at']) && time() < $data['expires_at']) {
                return $data['token']; // Token aún es válido
            }
        }

        // Si no existe o está vencido, generar uno nuevo
        $username = "St4r4P1Shp";
        $password = "@1?g=]kAG>Yb";

        $response = $this->login($username, $password);

        if (isset($response['token']['token'])) {
            $token = $response['token']['token'];
            $expiresAt = time() + (60 * 60 * 23); // 23 horas de validez

            $data = [
                'token' => $token,
                'expires_at' => $expiresAt
            ];

            file_put_contents($this->token_cache_file, json_encode($data));

            return $token;
        }

        return false; // No se pudo obtener token
    }
}
