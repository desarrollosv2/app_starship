<?php
    
defined('BASEPATH') OR exit('No direct script access allowed');    
class ClientesController extends CI_Controller 
{            
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('ClientesModel');
    }

    public function clientes()
    {
        //$this->datos['cliente'] = $this->ClientesModel->clientes();
        $this->load->view("clientes/vista_clientes");
    }

    public function busqueda_clientes($consignee_email)
    {
        $this->datos['cliente'] = $this->ClientesModel->busqueda_clientes($consignee_email);
        $this->load->view("clientes/cuerpo",$this->datos);
    }


    public function consulta_cliente() 
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

        $this->datos['lista_clientes']   = $this->ClientesModel->consulta_cliente();
        $this->load->view("clientes/vista_clientes", $this->datos);
    }

    public function buscar_cliente($dui) 
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

       
    }

    public function guardar_comentario($correo, $comentario_cliente) 
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

        $comentario_cliente = str_replace('%20', ' ', $comentario_cliente); 

        $data  = array(
                        'no_doc'            =>   $correo,
                        'comentario'        =>   urldecode($comentario_cliente),
                        'validar'           =>   1
        );        

        $data1 = array(
                        'no_documento'      =>   $correo,
                        'comentario'        =>   urldecode($comentario_cliente),
                        'fecha'             =>   date("Y-m-d H:i:s"),
        );
        
        $this->ClientesModel->agregar_cliente($data, $correo);
        $this->ClientesModel->guardar_comentario($data1);
    }


    public function modal_comentario_cliente($consignee_email)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

        $this->datos['cliente'] = $this->ClientesModel->modal_comentario_cliente($consignee_email);
        $this->load->view("clientes/cuerpo", $this->datos);
    }

    public function actualizar_comentario($comentario, $consignee_account)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

        $comentario = str_replace('%20', ' ', $comentario); 
        

        $data1 = array(
                    'no_documento'      =>   $consignee_account,
                    'comentario'        =>   $comentario,
                    'fecha'             =>   date("Y-m-d H:i:s"),
        );

        $this->ClientesModel->actualizar_comentario_cliente($comentario, $consignee_account);
        $this->ClientesModel->guardar_comentario($data1);

    }

    public function historial_cliente($consignee_account)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

        $this->datos['historial'] = $this->ClientesModel->historial_cliente($consignee_account);
        $this->load->view("clientes/historial_cliente", $this->datos);
    }
}       

?>