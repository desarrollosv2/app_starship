<?php
class Usuarios extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('UsuarioModel');
        $this->load->helper('url_helper');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['usuarios'] = $this->UsuarioModel->get_usuarios();
        $this->load->view('usuarios/index', $data);
    }

    public function view($id) {
        $data['usuario'] = $this->UsuarioModel->get_usuarios($id);
        if (empty($data['usuario'])) {
            show_404();
        }
        $this->load->view('usuarios/view', $data);
    }

    public function create() {
        $this->load->view('usuarios/create');
    }

    public function store() {
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'correo' => $this->input->post('correo'),
            'clave' => $this->input->post('clave'),
            'id_empresa' => $this->input->post('id_empresa'),
            'ver_doc_privado' => $this->input->post('ver_doc_privado'),
            'id_estatus' => $this->input->post('id_estatus'),
            'casillero' => $this->input->post('casillero'),
            'interno' => $this->input->post('interno') ? 1 : 0,
            'id_pais' => $this->input->post('id_pais'),
            'perfil' => $this->input->post('perfil')
        );
        $this->UsuarioModel->set_usuario($data);
        redirect('usuarios');
    }

    public function edit($id) {
        $data['usuario'] = $this->UsuarioModel->get_usuarios($id);
        if (empty($data['usuario'])) {
            show_404();
        }
        $this->load->view('usuarios/edit', $data);
    }

    public function update($id) {
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'correo' => $this->input->post('correo'),
            'clave' => $this->input->post('clave'),
            'id_empresa' => $this->input->post('id_empresa'),
            'ver_doc_privado' => $this->input->post('ver_doc_privado'),
            'id_estatus' => $this->input->post('id_estatus'),
            'casillero' => $this->input->post('casillero'),
            'interno' => $this->input->post('interno') ? 1 : 0,
            'id_pais' => $this->input->post('id_pais'),
            'perfil' => $this->input->post('perfil')
        );
        $this->UsuarioModel->update_usuario($id, $data);
        redirect('usuarios');
    }

    public function delete($id) {
        $this->UsuarioModel->delete_usuario($id);
        redirect('usuarios');
    }
}
