<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ProductosLibraryMA
{
    public function procesarProductosConCantidadesMA($cadena_productos, $cadena_cantidades, $precio_total, $peso_total, $items)
    {
        // Calcular el peso por ítem, distribuyéndolo equitativamente
        $peso_por_item = $peso_total / $items;

        // Capturar los productos entre corchetes en la primera cadena
        preg_match_all('/\[(.*?)\]/', $cadena_productos, $matches_productos);
        $productos = $matches_productos[1]; // Extraer los productos sin los corchetes

        // Capturar las cantidades entre corchetes en la segunda cadena
        preg_match_all('/\[(\d+)\]/', $cadena_cantidades, $matches_cantidades);
        $cantidades = array_map('intval', $matches_cantidades[1]); // Convertir las cantidades a enteros

        if (empty($productos) || empty($cantidades)) {
            log_message('error', "No se encontraron productos o cantidades en las cadenas.");
            return ['error' => 'No se encontraron productos o cantidades'];
        }

        // Asegurarnos de que las cantidades y los productos coincidan en número
        if (count($productos) !== count($cantidades)) {
            log_message('error', "La cantidad de productos y cantidades no coincide.");
            return ['error' => 'La cantidad de productos y cantidades no coincide '.count($productos).' '.count($cantidades)];
        }

        // Total de unidades para calcular el precio por unidad
        $cantidad_total_productos = array_sum($cantidades);
        $precio_unitario = $precio_total / $cantidad_total_productos;

        $resultado = [];
        $peso_acumulado = 0;

        // Procesar cada producto
        $i = 0;
        foreach ($productos as $index => $descripcion) {
            $cantidad = $cantidades[$index];
            $precio_por_producto = round($precio_unitario * $cantidad, 2);

            // Asignar el peso por ítem para cada fila
            $peso_por_producto = round($peso_por_item, 2);

            // Acumulamos el peso de cada fila
            $peso_acumulado += $peso_por_producto;
            $i = $i + 1;
            // Añadir el producto procesado al resultado
            $resultado[] = [
                'descripcion' => $descripcion,
                'cantidad' => $cantidad,
                'precio' => $precio_por_producto,
                'peso' => $peso_por_producto,  // Asignamos el peso fijo
                'peso_total' => $peso_total,  // Asignamos el peso fijo
                'items' => $items,  // Asignamos el peso fijo
            ];
        }

        // Ajuste final en caso de que haya diferencia por el redondeo
        $diferencia_peso = round($peso_total - $peso_acumulado, 2);

        // Ajustamos el peso de la última fila para corregir la diferencia
        if (count($resultado) > 0) {
            $ultima_posicion = count($resultado) - 1;
            $resultado[$ultima_posicion]['peso'] += $diferencia_peso;
        }

        return $resultado;
    }



    public function agruparProductosMA($productos)
    {
        $agrupados = [];
        $i = 0;
        $c = 0;



        foreach ($productos as $producto) {

            // Convertir a minúsculas para comparar sin importar mayúsculas/minúsculas
            $nombre_base = strtolower(preg_replace('/\([^\)]*\)/', '', $producto['descripcion']));

            if (!isset($agrupados[$nombre_base])) {
                $agrupados[$nombre_base] = [
                    'descripcion' => ucfirst($nombre_base), // Mostrar con capitalización inicial
                    'cantidad' => 0,
                    'precio' => 0,
                    'peso' => 0,
                    'peso_total' => 0,
                    'items' => 0,
                ];
                $c = $c + 1;
            }

            // Sumar cantidades y precios directamente
            $agrupados[$nombre_base]['cantidad'] += $producto['cantidad'];
            $agrupados[$nombre_base]['precio'] += $producto['precio'];
            $agrupados[$nombre_base]['peso'] += $producto['peso'];
            $agrupados[$nombre_base]['peso_total'] = $producto['peso_total'];
            // Sumar pesos en lugar de sobrescribirlos
            //  $agrupados[$nombre_base]['peso'] = $i;

            // $calculo =   $producto['valor'] / $i;
            // $agrupados[$nombre_base]['peso'] =  $i;
        }
        // $calculo =   $producto['valor'] / $i;
        // $agrupados[$nombre_base]['peso'] = $calculo;
        return array_values($agrupados); // Reindexar el array
    }
}
