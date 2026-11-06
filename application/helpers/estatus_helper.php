<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('send_api_request')) {
    //APG
    function send_api_request($data)
    {
        //  var_dump($data);
        // Definir la URL y el token aquí (para modificarlos en un solo lugar)
        $url = 'https://stargateserp.com/app_starship/ws/index.php/api/estadosApg'; // Cambia esto por la URL real de la API
        $token = '4a4da5bb348e648e51d84e24c8652d1284c31156'; // Cambia esto por tu token real

        // Convertir los datos a JSON
        $jsonFields = json_encode($data);

        // Inicializar cURL
        $curl = curl_init();

        // Configurar opciones de cURL
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonFields,
            CURLOPT_HTTPHEADER => [
                'x-api-key: ' . $token, // Token en el header
                'Content-Type: application/json'
            ],
        ]);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);

        // Capturar errores si los hay
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return ['error' => true, 'message' => $error_msg];
        }

        // Cerrar la conexión cURL
        curl_close($curl);

        // Retornar la respuesta decodificada
        return json_decode($response, true);
    }
    //imile
    function imile_send_api_request($data)
    {
        //  var_dump($data);
        // Definir la URL y el token aquí (para modificarlos en un solo lugar)
        $url = 'https://stargateserp.com/app_starship/ws/index.php/apimile/eventos'; // Cambia esto por la URL real de la API
        $token = '4a4da5bb348e648e51d84e24c8652d1284c31156'; // Cambia esto por tu token real

        // Convertir los datos a JSON
        $jsonFields = json_encode($data);

        // Inicializar cURL
        $curl = curl_init();

        // Configurar opciones de cURL
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonFields,
            CURLOPT_HTTPHEADER => [
                'x-api-key: ' . $token, // Token en el header
                'Content-Type: application/json'
            ],
        ]);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);

        // Capturar errores si los hay
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return ['error' => true, 'message' => $error_msg];
        }

        // Cerrar la conexión cURL
        curl_close($curl);

        // Retornar la respuesta decodificada
        return json_decode($response, true);
    }
}
