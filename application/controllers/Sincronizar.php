<?php
defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Sincronizar extends CI_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->db = $this->load->database( 'default', true );

        $this->load->model( 'Sincronizar_Model' );

    }

    public function index() {

    }

    public function clientes_magic() {

        $this->datos['lista'] = $this->Sincronizar_Model->clientes_magic();

    }

    public function crear_credencial() {
        $this->datos['lista'] = $this->Sincronizar_Model->crear_credencial();
        foreach ( $this->datos['lista'] as $fila ) {

            $data = array(
                'nombre'  => $fila->nombre,
                'correo'  => strtolower($fila->correo),
                'clave'   => 'SSGC2020',
                'id_empresa' => 46,
                'ver_doc_privado' => 0,
                'interno' => 0,
                'casillero' => "SAL".$fila->casillero,

            );
            $this->Sincronizar_Model->guardar( $data );

        }
    }

    
    public function datos_clientes() {

        $result = $this->datos['cliente'] = $this->ClientesModel->clientes();

        foreach ( $result as $fila ) {

            $data = array(
                'nombre'  => $fila->nombre,
                'correo'  => strtolower($fila->correo),
                'clave'   => 'SSGC2020',
                'id_empresa' => 46,
                'ver_doc_privado' => 0,
                'interno' => 1,
                'casillero' => $fila->casillero,

            );

            $this->Sincronizar_Model->guardar( $data );

        }

    }
}

/* End of file Controllername.php */
