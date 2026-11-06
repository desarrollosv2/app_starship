<?php
defined('BASEPATH') or exit('No direct script access allowed');

    /*ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/

    include_once APPPATH . 'libraries/acciones/accionesusuarios.php';

class Usuarios_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Usuarios_Model');
        $this->load->library('Validation_form');

    }

    private function obtener_permisos_menu($menu)
    {
        $info_menu = $this->Usuarios_Model->get_info_menu($menu);
        $id_menu = $info_menu->id;

        $acciones = new accionesusr();

        return $acciones->obtacciones_usuarios($id_menu);
    }

    public function usuarios_sistema()
    {
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/


        $menu = 'usuarios_sistema';
        $acciones_usuario = $this->obtener_permisos_menu($menu);
        //var_dump($acciones_usuario);
        $data['acciones_usuario'] = $acciones_usuario;

        if ($acciones_usuario['r'] != 1) {
            $respuesta = [
                'status' => 'error',
                'msg' => 'No tiene permiso para consultar el módulo de usuarios.'
            ];

            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode($respuesta));

            return;
        }
        
        $this->load->view('usuarios/usuarios_principal', $data);
    }

    public function lista_usuarios_sistema()
    {
        $menu = 'usuarios_sistema';
        $acciones_usuario = $this->obtener_permisos_menu($menu);

        if ($acciones_usuario['r'] != 1) {
            $respuesta = [
                'status' => 'error',
                'msg' => 'No tiene permiso para consultar el modulo de usuarios.'
            ];

            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode($respuesta));

            return;
        }

        $usuarios = $this->Usuarios_Model->obtener_usuarios_sistema();
        $data['usuarios'] = $usuarios;
        $data['acciones_usuario'] = $acciones_usuario;

        $this->load->view('usuarios/lista_usuario', $data);
    }

    public function agregar_nuevo_usuario_sistema()
    {
        //ini_set('display_errors', 1);
        //ini_set('display_startup_errors', 1);
        //error_reporting(E_ALL); 
    
        $menu = 'usuarios_sistema';
        $acciones_usuario = $this->obtener_permisos_menu($menu);

        if ($acciones_usuario['c'] != 1) {
            $respuesta = [
                'status' => 'error',
                'msg' => 'No tiene permiso para crear usuarios.'
            ];

            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode($respuesta));

            return;
        }

        $data['roles'] = $this->Usuarios_Model->obtener_roles();
        $data['procesos'] = $this->Usuarios_Model->obtener_procesos();
        $data['paises'] = $this->Usuarios_Model->obtener_paises();
        $data['acciones_usuario'] = $acciones_usuario;


        $this->load->view('usuarios/form', $data);
    }

    public function editar_usuario_sistema($id_usuario)
    {
        //ini_set('display_errors', 1);
        //ini_set('display_startup_errors', 1);
        //error_reporting(E_ALL); 

        $acciones_usuario = $this->obtener_permisos_menu('usuarios_sistema');

        if ($acciones_usuario['u'] != 1) {
            $respuesta = [
                'status' => 'error',
                'msg' => 'No tiene permiso para editar usuarios.'
            ];

            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode($respuesta));

            return;
        }

        $usuario = $this->Usuarios_Model->obtener_usuario_por_id($id_usuario);
        $data['usuario'] = $usuario;
        $data['roles'] = $this->Usuarios_Model->obtener_roles();
        $data['procesos'] = $this->Usuarios_Model->obtener_procesos();
        $data['paises'] = $this->Usuarios_Model->obtener_paises();
        $this->load->view('usuarios/form', $data);
        
    }

    public function guardar_usuario_sistema()
    {
        //ini_set("display_errors",1);

        $id_usuario = $this->input->post('id_usuario');
        $acciones_usuario = $this->obtener_permisos_menu('usuarios_sistema');

        $permiso_requerido = empty($id_usuario) ? 'c' : 'u';
        $mensaje_permiso = empty($id_usuario)
            ? 'No tiene permiso para crear usuarios.'
            : 'No tiene permiso para editar usuarios.';

        if ($acciones_usuario[$permiso_requerido] != 1) {
            $respuesta = [
                'status' => 'error',
                'msg' => $mensaje_permiso
            ];

            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode($respuesta));

            return;
        }

        $nombre     = $this->input->post('usuario_nombre');
        $correo     = $this->input->post('usuario_correo');
        $rol        = $this->input->post('usuario_rol');
        $password   = $this->input->post('usuario_clave');
        $proceso    = $this->input->post('usuario_proceso');
        $pais       = $this->input->post('usuario_pais');

        $rules = [];

        $rules['usuario_nombre']    = ['required'];
        $rules['usuario_correo']    = ['required', 'email'];
        $rules['usuario_rol']       = ['required'];
        $rules['usuario_proceso']   = ['required'];
        $rules['usuario_pais']   = ['required'];

        if($id_usuario == null)
        {
            $rules['usuario_clave'] = ['required'];
        } 

        $this->validation_form->setRules($rules);
        $form_data = $this->input->post();

        if(!$this->validation_form->validate($form_data))
        {
            echo json_encode([
                                'status' => 'errors',
                                'errors' => $this->validation_form->getErrors()
                            ]);
            return;
        }

        $validar_usuario = $this->Usuarios_Model->validar_usuario_correo($correo, $id_usuario);

        if($validar_usuario > 0)
        {
            echo json_encode([
                'status' => 'error',
                'msg' => 'El correo electrónico ya está registrado para otro usuario.'
            ]);
            return;
        }

        if($id_usuario)
        {
            $data = [
                'nombre'   => $nombre,
                'correo'   => $correo,
                'id_rol'   => $rol,
                'clave'    => $password,
                'id_proceso' => $proceso,
                'id_pais'   => $pais,
            ];

            $resultado = $this->Usuarios_Model->actualizar_usuario($id_usuario, $data);

            if (!$resultado)
            {
                echo json_encode([
                    'status' => 'error',
                    'msg' => 'No fue posible actualizar el usuario.'
                ]);
                return;
            }

            echo json_encode([
                'status' => 'success',
                'msg' => 'Usuario actualizado correctamente.'
            ]);
        }
        else
        {
            $data = [
                'nombre'   => $nombre,
                'correo'   => $correo,
                'id_rol'   => $rol,
                'id_proceso' => $proceso,
                'id_empresa' => $_SESSION['id_empresa'],
                'clave'    => $password,
                'id_estatus' => 1,
                //'id_sucursal' => 1,
                'id_pais' => $pais,
                'interno' => 1
            ];
            $nuevo = $this->Usuarios_Model->insertar_usuario($data);

            if(!$nuevo)
            {
                echo json_encode([
                    'status' => 'error',
                    'msg' => 'No fue posible crear el usuario.'
                ]);
                return;
            }

            echo json_encode([
                'status' => 'success',
                'msg' => 'Usuario creado correctamente.'
            ]);
        }


    }

    public function estado_usuario($id_usuario, $estado)
    {
        $acciones_usuario = $this->obtener_permisos_menu('usuarios_sistema');

        if ($acciones_usuario['u'] != 1) {
            $respuesta = [
                'status' => 'error',
                'msg' => 'No tiene permiso para cambiar el estado de usuarios.'
            ];

            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode($respuesta));

            return;
        }

        $resultado = $this->Usuarios_Model->actualizar_estatus_usuario($id_usuario, $estado);

        if(!$resultado)
        {
            echo json_encode([
                'status' => 'error',
                'msg' => 'No fue posible actualizar el estado del usuario.'
            ]);
            return;
        }
        else
        {
            if($estado == 1)
            {
                echo json_encode([
                    'status' => 'success',
                    'msg' => 'Usuario activado correctamente.'
                ]);
            }
            else
            {
                echo json_encode([
                    'status' => 'success',
                    'msg' => 'Usuario inactivado correctamente.'
                ]);
            }
        }
    }



}