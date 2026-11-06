<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TokenMasterLib {

    private $idPais;
    private $tipoToken;
    private $tokenXpress;
    private $tokenCliente;
    private $idTokenCliente;
    private $tipoFacturas;
    private $CI;

    // Constructor
    public function __construct($params = null) {
        $this->CI =& get_instance();  // Cargar la instancia de CI para acceder a modelos y helpers
        if ($params) {
            $this->initialize($params);
        }
    }

    // Inicializar con parámetros
    public function initialize($params) {
        $this->idPais = isset($params['idPais']) ? $params['idPais'] : null;
        $this->tipoToken = isset($params['tipoToken']) ? $params['tipoToken'] : null;
        $this->tokenXpress = isset($params['tokenXpress']) ? $params['tokenXpress'] : null;
        $this->tokenCliente = isset($params['tokenCliente']) ? $params['tokenCliente'] : null;
        $this->idTokenCliente = isset($params['idTokenCliente']) ? $params['idTokenCliente'] : null;
        $this->tipoFacturas = isset($params['tipoFacturas']) ? $params['tipoFacturas'] : null;
    }

    // Obtener los datos de la empresa y los tokens correspondientes
    public function obtenerDatosDeEmpresa($master) {
        // Usamos el modelo de Gestión para obtener los datos de la empresa
        $request = $this->CI->Gestion_Model->get_Idempresa($master);
        if (empty($request)) {
            return null;  // Si no se encuentran datos, devolver null
        }

        $cliente = $this->CI->Gestion_Model->datos_empresa($request[0]->client_id);
        if (empty($cliente)) {
            return null;  // Si no se encuentran datos del cliente, devolver null
        }

        // Extraemos los valores necesarios de la empresa
        $this->idTokenCliente = $cliente[0]->id_tokencliente;
        $this->tokenCliente = $cliente[0]->token_cliente;
        $this->tokenXpress = $cliente[0]->token;  // Token de Xpress

        // Retornamos los datos relevantes en un array
        return [
            'id_tokencliente' => $this->idTokenCliente,
            'token_cliente' => $this->tokenCliente,
            'token_xpress' => $this->tokenXpress
        ];
    }

    // Función para obtener las credenciales de API
    public function getApiCredentials() {
        if ($this->tipoToken == 'nuestro') {
            return $this->getOurApiCredentials();
        } elseif ($this->tipoToken == 'cliente') {
            return $this->getClientApiCredentials();
        } else {
            return null;
        }
    }

    // Obtener credenciales para nuestro token
    private function getOurApiCredentials() {
        return [
            'idPais' => $this->idPais,
            'token' => $this->tokenXpress,
            'tipoFacturas' => $this->tipoFacturas
        ];
    }

    // Obtener credenciales para el token del cliente
    private function getClientApiCredentials() {
        return [
            'idPais' => $this->idPais,
            'token' => $this->tokenCliente,
            'idTokenCliente' => $this->idTokenCliente,
            'tipoFacturas' => $this->tipoFacturas
        ];
    }

    // Función para generar un token (simplificado)
    public function generateToken() {
        if ($this->tipoToken == 'nuestro') {
            return $this->generateOurToken();
        } elseif ($this->tipoToken == 'cliente') {
            return $this->generateClientToken();
        } else {
            return null;
        }
    }

    // Generar token para nuestra API
    private function generateOurToken() {
        return "token_generado_para_nuestro_servicio";
    }

    // Generar token para el cliente
    private function generateClientToken() {
        return "token_generado_para_cliente_" . $this->idTokenCliente;
    }
}
