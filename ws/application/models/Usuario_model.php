<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Usuario_model extends CI_Model
{


    function getLogin($datos)
    {


        $query = $this->db

            ->select("nombre,casillero")

            ->from("usuarios")

            ->where('correo', $datos['correo'])

            ->where('clave', $datos['clave'])

            ->get();

        if ($query->num_rows() > 0) {

            return $query->row();
        } else {

            return false;
        }
    }

    public function update_password($data)
    {
        $this->db
            ->set('clave', $data['clave'])
            ->where('casillero', $data['casillero'])
            ->update('usuarios');
        return ($this->db->affected_rows() > 0);
    }

    public function update_correo($data)
    {
        $this->db
            ->set('correo', $data['correo'])
            ->where('casillero', $data['casillero'])
            ->update('usuarios');
        return ($this->db->affected_rows() > 0);
    }
    public function update_sucursal($data)
    {
        $this->db
            ->set('id_retiro', $data['id_sucursal'])
            ->where('casillero', $data['casillero'])
            ->update('clientes');
        return ($this->db->affected_rows() > 0);
    }
}

/* End of file ModelName.php */
