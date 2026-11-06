<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Filtros de busqueda (formulario "Filtros de busqueda") ya validados. Los invalidos se ignoran.
 * Se leen de POST (consultas AJAX) o de GET (exportacion CSV).
 */
function filtros_busqueda()
{
    $CI = &get_instance();
    $f = array();

    $estado = $CI->input->post_get('estado');
    if (in_array($estado, array('abierto', 'cerrado'), TRUE)) {
        $f['estado'] = $estado;
    }
    foreach (array('cliente_id', 'tipo_producto_id') as $k) {
        $v = (int) $CI->input->post_get($k);
        if ($v > 0) {
            $f[$k] = $v;
        }
    }
    foreach (array('desde', 'hasta') as $k) {
        $v = (string) $CI->input->post_get($k);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
            $f[$k] = $v;
        }
    }
    return $f;
}

/**
 * Numero de pallet con ceros a la izquierda: 12 -> 000012
 */
function numero_pallet_fmt($numero)
{
    return str_pad($numero, 6, '0', STR_PAD_LEFT);
}

function codigo_pallet($pallet)
{
    return $pallet->tipo_prefijo . '-' . numero_pallet_fmt($pallet->numero_pallet);
}

/**
 * Respuesta JSON para las llamadas AJAX: siempre {success: bool, ...}.
 */
function responder_json($data)
{
    $CI = &get_instance();
    $CI->output->set_content_type('application/json', 'utf-8')->set_output(json_encode($data));
}

/**
 * Campo oculto con el token CSRF para incluir dentro de cada <form> que se envia con FormData.
 */
function campo_csrf()
{
    $CI = &get_instance();
    return '<input type="hidden" name="' . html_escape($CI->security->get_csrf_token_name())
        . '" value="' . html_escape($CI->security->get_csrf_hash()) . '">';
}
