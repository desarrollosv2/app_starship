<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EnvioController extends CI_Controller
{

    public function enviarDatos()
    {
        $this->load->model('WhModel'); // Carga el modelo
        $editables = $this->WhModel->editables(); // Obtiene los registros de la tabla editables
        // var_dump($editables);

        foreach ($editables as $row) {
            $tracking = $row->tracking_number;
            $telefono = $row->telefono;
            /** estas 2 lineas para actualizar nuestro datos */
            // $this->WhModel->updateTelefono($tracking, $telefono); // Obtiene los registros de la tabla editables
            $this->WhModel->updateEditables($tracking); // Obtiene los registros de la tabla editables

            $token = "d9e949a0b613a5b513b23dbfd29365790c0143a2"; // Almacena el token en una variable


            $postfields = [

                "telefono"        => $telefono
            ];
         
         $postFieldsJson = json_encode($postfields);
         
         // Construir la URL con el tracking
         $url = 'https://app.c807.com/guia.php/api/actualizar/' . $tracking;
         
         // Inicializar cURL
         $curl = curl_init();
         
         // Configurar las opciones de cURL
         curl_setopt_array($curl, array(
             CURLOPT_URL => $url,
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_ENCODING => '',
             CURLOPT_MAXREDIRS => 10,
             CURLOPT_TIMEOUT => 0,
             CURLOPT_FOLLOWLOCATION => true,
             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_CUSTOMREQUEST => 'PUT',
             CURLOPT_POSTFIELDS => $postFieldsJson,
             CURLOPT_HTTPHEADER => array(
                 'Authorization: ' . $token,
                 'Content-Type: application/json'
                 
             ),
         ));
         
         // Ejecutar la solicitud cURL
         $response = curl_exec($curl);
         
         // Cerrar la sesión cURL
         curl_close($curl);
         
         // Imprimir la respuesta
         echo $response;
           // break;
        }
    }
}
