<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChatGPT extends CI_Controller {

    // Método para consumir la API
    public function index() {
        // Configuración de la API
        $api_url = "https://api.openai.com/v1/chat/completions";
        $api_key = "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";
$direccion='final 6A calle poniente pasaje#1 casa#2 barrio el calvario cojutepeque';
        // Datos del cuerpo de la solicitud
        $data = [
            "model" => "gpt-3.5-turbo", // Modelo
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Eres un asistente que clasifica direcciones de El Salvador en Departamento y Municipio."
                ],
                [
                    "role" => "user",
                    "content" => "Por favor, analiza esta dirección y dame el Departamento y Municipio: " . $direccion
                ]
            ],
            "max_tokens" => 100, // Máximo de tokens en la respuesta
            "temperature" => 0.7 // Controla la creatividad
        ];

        // Configuración de cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $api_key",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Ejecutar la solicitud
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Manejar la respuesta
        if ($http_code == 200) {
            $result = json_decode($response, true);
            $message = $result["choices"][0]["message"]["content"];
            echo "Respuesta de ChatGPT: " . $message;
        } else {
            echo "Error: Código HTTP $http_code, Respuesta: $response";
        }
    }
}
