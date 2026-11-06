<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AjusteTotal
{
    /**
     * Ajusta la última fila de precios para que coincida con el total esperado
     * @param array $precios Lista de precios originales
     * @param float $totalEsperado Total correcto que se desea alcanzar
     * @return array Lista de precios con ajuste en el último elemento
     */
    public function ajustarUltimoPrecio($precios = [], $totalEsperado = 0.0)
    {
        $precios = array_map('floatval', $precios); // Convierte todo a float

        $sumaActual = array_sum($precios);
        $diferencia = $totalEsperado - $sumaActual;

        if (count($precios) > 0 && abs($diferencia) > 0.000001) {
            $ultimoIndex = count($precios) - 1;
            $precios[$ultimoIndex] += $diferencia;
        }

        // Opcional: redondear todo a 2 decimales solo si estás manejando moneda
        // foreach ($precios as &$precio) {
        //     $precio = round($precio, 2);
        // }

        return $precios;
    }
}
