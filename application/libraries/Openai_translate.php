<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Openai_translate
{
    private $api_key;
   // private $model = 'gpt-4'; // Puedes usar gpt-4 si prefieres
    private $model = 'gpt-4o-mini';
    public function __construct()
    {
       $this->api_key = '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'; // ← Coloca tu API Key aquí
    }

    public function traducir_batch($data)
    {
        $resultados = [];

        foreach ($data as $item) {
            $texto_original = $item['commodity'];

            $prompt = "Eres un traductor profesional. Traduce únicamente el contenido en inglés que esté dentro de corchetes []. No devuelvas el texto original en inglés, solo la traducción en español, respetando exactamente los corchetes y su orden. 
No agregues repeticiones, no expliques, y no mantengas el texto original en ningún caso.
Ejemplo:
Entrada: [Red shoes][Laptop]
Resultado esperado: [Zapatos rojos][Portátil]
Texto: " . $texto_original;

            $respuesta = $this->consultar_openai($prompt);
            $limpia = $this->limpiar_respuesta($texto_original, $respuesta);

            $resultados[] = [
                'id_manifiesto' => $item['id_manifiesto'],
                'tracking_number' => $item['tracking_number'],
                'descripcion_es' => $limpia ?: '[NO TRADUCIDO]'
            ];
        }

        return $resultados;
    }

    private function consultar_openai($prompt)
    {
        $curl = curl_init();
        $post_fields = json_encode([
            "model" => $this->model,
            "messages" => [
                ["role" => "system", "content" => "Eres un traductor profesional de inglés a español. Traduce únicamente el contenido dentro de corchetes, sin incluir el original."],
                ["role" => "user", "content" => $prompt]
            ],
            "temperature" => 0
        ]);

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->api_key,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => $post_fields
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        log_message('debug', 'Respuesta cruda OpenAI: ' . $response);

        $result = json_decode($response, true);

        if (
            isset($result['choices'][0]['message']) &&
            isset($result['choices'][0]['message']['content'])
        ) {
            return trim($result['choices'][0]['message']['content']);
        } else {
            log_message('error', 'Respuesta inesperada de OpenAI: ' . json_encode($result));
            return '[ERROR: sin respuesta]';
        }
    }

    private function limpiar_respuesta($texto_original, $respuesta)
    {
        preg_match_all('/\[[^\]]+\]/', $texto_original, $originales);
        preg_match_all('/\[[^\]]+\]/', $respuesta, $traducidos);

        $originales = $originales[0];
        $traducidos = $traducidos[0];

        // Si la respuesta contiene el doble, probablemente viene como: [inglés][español]
        if (count($traducidos) === count($originales) * 2) {
            $solo_traduccion = array_slice($traducidos, count($originales));
            return implode('', $solo_traduccion);
        }

        // Si la cantidad de traducciones es igual a la cantidad original, se acepta
        if (count($traducidos) === count($originales)) {
            return implode('', $traducidos);
        }

        // Si no hay traducción útil
        return '';
    }
}
