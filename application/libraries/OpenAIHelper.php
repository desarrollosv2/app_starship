<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OpenAIHelper {

    private $api_key = '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';

    public function procesar_productos($productos) {
        $endpoint = "https://api.openai.com/v1/chat/completions";

        // Preparar el prompt para el modelo
        $mensajes = [
            [
                "role" => "system",
                "content" => "Eres un asistente que asigna códigos arancelarios a productos basándote en sus descripciones. Devuelve la respuesta en formato JSON, asegurando incluir el 'id' recibido."
            ],
            [
                "role" => "user",
                "content" => json_encode($productos)
            ]
        ];

        $data = [
            "model" => "gpt-3.5-turbo",
            "messages" => $mensajes,
            "temperature" => 0.2
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->api_key,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
