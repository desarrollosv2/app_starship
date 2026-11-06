<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CrearGuia
{

    private $CI;

    // Constructor
    public function __construct()
    {
        $this->CI = &get_instance();  // Cargar la instancia de CI para acceder a modelos y helpers
    }

    /**
     * Función para crear la guía utilizando cURL
     * 
     * @param string $token El token para la autenticación
     * @param array $datos El arreglo con los datos a enviar a la API
     * 
     * @return string Respuesta de la API
     */
    public function crearGuia($token, $datos)
    {
        // Determinar la URL dependiendo del país
        //var_dump( $datos);
        $url = 'https://app.c807.com/guia.php/api/set_registro';
        // Iniciar cURL
        $curl = curl_init();

        // Configuración de cURL
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false, //TODO: quitar esto en producción
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($datos),  // Convertir datos en JSON
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: ' . $token,  // Pasar el token recibido como parámetro
            ),
        ));
        
        $response = curl_exec($curl);
		curl_close($curl);
		
        // Retornar la respuesta decodificada
        return $response; // Devuelve los datos procesados
    }
}
