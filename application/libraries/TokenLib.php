<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TokenLib {

    private $CI;

    // Constructor
    public function __construct() {
        $this->CI =& get_instance();  // Cargar la instancia de CI para acceder a modelos y helpers
    }

    // Método para generar el token
    public function generarToken($credentials) {
        exit;
        // Preparamos los datos para la solicitud cURL
        $data = [
            'id_tokencliente' => $credentials['id_tokencliente'],
            'token_cliente' => $credentials['token_cliente'],
            'token_xpress' => $credentials['token_xpress']
        ];

        // Iniciar cURL
        $ch = curl_init();

        // Establecer la URL y otras opciones de cURL
        curl_setopt($ch, CURLOPT_URL, "URL_DE_LA_API");  // Aquí pones la URL para generar el token
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));  // Pasamos los datos como POST

        // Ejecutar la solicitud cURL y obtener la respuesta
        $response = curl_exec($ch);

        // Comprobar si hubo algún error en la solicitud
        if(curl_errno($ch)) {
            echo 'Error de cURL: ' . curl_error($ch);
        }

        // Cerrar la sesión cURL
        curl_close($ch);

        // Retornar la respuesta
        return json_decode($response, true);  // Retorna la respuesta en formato JSON
    }
}
