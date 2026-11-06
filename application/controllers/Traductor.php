<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Traductor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Openai_translate');
        $this->load->model('Traductor_Model');
    }

    public function traducir($manifiesto)
    {
        $resultado_crudo = $this->Traductor_Model->lista_tracking($manifiesto);

        if (!empty($resultado_crudo) && $resultado_crudo[0]->traduccion == 1) {
            echo json_encode(['mensaje' => 'Traducción ya fue aplicada, no puede continuar con este proceso', 'items_actualizados' => 0]);
            exit;
        }

        $lista = array_map(function ($item) {
            return [
                'id_manifiesto' => $item->id_manifiesto,
                'tracking_number' => $item->tracking_number,
                'commodity' => $item->commodity,
            ];
        }, $resultado_crudo);

        if (empty($lista)) {
            echo json_encode(['mensaje' => 'No se encontraron datos para traducir', 'items_actualizados' => 0]);
            exit;
        }

        $resultado = $this->openai_translate->traducir_batch($lista);

        foreach ($resultado as $item) {
            if (!empty($item['descripcion_es']) && $item['descripcion_es'] !== '[NO TRADUCIDO]') {
                $this->Traductor_Model->actualizar_descripcion(
                    $item['id_manifiesto'],
                    $item['tracking_number'],
                    $item['descripcion_es']
                );
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['mensaje' => 'Actualización completa', 'items_actualizados' => count($resultado)]);
    }

    public function actualizar_descripcion($id_manifiesto, $tracking_number, $descripcion_es)
    {
        $this->db->where('id_manifiesto', $id_manifiesto);
        $this->db->where('tracking_number', $tracking_number);
        return $this->db->update('tu_tabla_destino', [
            'descripcion_es' => $descripcion_es
        ]);
    }

    public function test_traduccion_openai()
    {
        $this->load->library('openai_translate');

        $lista = [
            [
                'id_manifiesto' => 1,
                'tracking_number' => 'TEST123',
                'commodity' => '[Red shoes][Sunglasses][USB cable]'
            ]
        ];

        $resultado = $this->openai_translate->traducir_batch($lista);

        echo "<pre>";
        print_r($resultado);
        echo "</pre>";
    }
}
