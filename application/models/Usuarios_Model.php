<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usuarios_model extends CI_Model
{
    public function  get_info_menu($url)
    {
        $query = $this->db->select('*')
            ->where('url',$url)   
            ->get('dyn_menu')            
            ->row();
        return $query;

    }
    //  funcion  para determinar las accciones que puede realizar un usuario
    public function obtaccion_usuario($id_menu)
    {
        $query = $this->db->select("*")
                ->where("id_usuario",$_SESSION["user_id"])
                ->where("id_menu",$id_menu)
                ->get("permisos")
                ->row();
        return $query;                    

    }

    public function obtener_usuarios_sistema()
    {
        $this->db->select('u.*, r.nombre_rol AS rol, p.nombre_proceso AS proceso, pa.nombre AS pais');
        $this->db->from('usuarios AS u');
        $this->db->join('rol AS r', 'r.id = u.id_rol', 'left');
        $this->db->join('procesos AS p', 'p.id = u.id_proceso', 'left');
        $this->db->join('pais_ec AS pa', 'pa.id = u.id_pais', 'left');

        $this->db->where("
            (
                u.correo LIKE '%@c807.com'
                OR u.correo LIKE '%@grupoominilogix.com'
                OR u.correo LIKE '%@mistarship.com'
                OR u.correo LIKE '%@c807xpress.com'
                OR u.correo LIKE '%@8dt.com'
                OR u.correo LIKE '%@apg-global.com'
                OR u.id = 1
            )
        ", null, false);

        return $this->db->get()->result();
    }

    public function obtener_usuario_por_id($id_usuario)
    {
        $query = $this->db->select('*')
                    ->from('usuarios')
                    ->where('id', $id_usuario)
                    ->get()->row();
        return $query;
    }

    public function obtener_roles()
    {
        $query = $this->db->select('*')
                    ->from('rol')
                    ->where('id !=', 1)
                    ->get()->result();
        return $query;
    }

    public function obtener_procesos()
    {
        return $this->db->get('procesos')->result();
    }

    public function obtener_paises()
    {
        return $this->db->get('pais_ec')->result();
    }

    public function actualizar_usuario($id_usuario, $data)
    {
        $this->db->where('id', $id_usuario);
        return $this->db->update('usuarios', $data);
    }

    public function insertar_usuario($data)
    {
        $this->db->insert('usuarios', $data);
        return $this->db->insert_id();
    }

    public function actualizar_estatus_usuario($id_usuario, $estado)
    {
        $this->db->where('id', $id_usuario);
        return $this->db->update('usuarios', ['id_estatus' => $estado]);
    }

    public function validar_usuario_correo($correo, $id_usuario = null)
    {
        $this->db->where('correo', $correo);

        if (!empty($id_usuario)) {
            $this->db->where('id !=', $id_usuario);
        }

        return $this->db->count_all_results('usuarios');
 
    }

    

}