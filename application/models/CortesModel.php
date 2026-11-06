<?php

    class CortesModel extends CI_Model
    {
        public function agregarCorte($data)
		{
			$this->db->insert('corte',$data);
		}
        
		public function actualizarCorte($id_corte, $data)
		{
			$this->db->where('id_corte', $id_corte);
			$this->db->update('corte', $data);
		}

        public function mostrarCortes()
        {
           
            $this->db->where('id_pais', $_SESSION['pais']);
            $query = $this->db->get('corte');
            return $query->result();
        }

        public function editarCorte($id_corte)
        {
            $this->db->where('id_corte', $id_corte);
            $this->db->from('corte');
            return $this->db->get()->row();
        }

        public function eliminarCorte($id_corte)
        {
            $this->db->where('id_corte', $id_corte);
            $this->db->delete('corte');
        }
        

        public function cambiarEstado($id_corte)
        {
            $this->db->update('corte',['estado_corte' => 0]);
            $this->db->where('id_corte', $id_corte);
            $this->db->update('corte',['estado_corte' => 1]);
        }

        
        public function obtener_activo()
        {
            $this->db->where('estado_corte', 1);
            $this->db->where('id_pais', $_SESSION['pais']);
            $query = $this->db->get('corte');
            return $query->row();
        }
    }





?>