<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmpresaLib {

    private $CI;

    // Constructor
    public function __construct() {
        $this->CI =& get_instance();  // Cargar la instancia de CI para acceder a modelos y helpers
    }

    // Método para obtener los datos de la empresa usando el master-id
    public function obtenerDatosDeEmpresa($master_id) {
        // Obtiene el ID de la empresa del cliente
        $request = $this->CI->Gestion_Model->get_Idempresa($master_id);

        if (empty($request)) {
            return null;  // Si no se encuentra la empresa
        }

        // Obtiene los datos de la empresa con el client_id
        $cliente = $this->CI->Gestion_Model->datos_empresa($request[0]->client_id);

        if (empty($cliente)) {
            return null;  // Si no se encuentran los datos del cliente
        }

        // Extrae los identificadores y tokens necesarios
        $id_tokencliente = $cliente[0]->id_tokencliente;
        $token_cliente = $cliente[0]->token_cliente;
        $token_lmd = $cliente[0]->token;

        // Retorna los datos de la empresa en un array
        return [
            'id_tokencliente' => $id_tokencliente,
            'token_cliente' => $token_cliente,
            'token_lmd' => $token_lmd
        ];
    }
}
