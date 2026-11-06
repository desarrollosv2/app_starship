<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sincronizar_Model extends CI_Model {

    public function guardar($data)
     {
         
             $this->db->insert('usuarios', $data);
             return $this->db->insert_id();
         
     }

       
     public function crear_credencial()
     {
        $query = $this->db->select('*')
        ->get('clientes')
        ->result();
        return $query;
    
     }


}

/* End of file ModelName.php */
