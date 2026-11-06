<?php
class UsuarioModel  extends CI_Model {

    protected $table = 'usuarios';

    public function __construct() {
        $this->load->database();
    }

    public function get_usuarios($id = NULL) {
        if ($id === NULL) {
            $query = $this->db->get($this->table);
            return $query->result_array();
        }
        $query = $this->db->get_where($this->table, array('id' => $id));
        return $query->row_array();
    }

    public function set_usuario($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update_usuario($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_usuario($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}
