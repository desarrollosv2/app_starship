<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tasa_Cambio_Model extends CI_Model
{
    public function tasa_de_cambio()
    {
        $query = $this->db
        ->select('tdc.*,ps.nombre nombre_pais')
        ->join('pais_ec ps', 'tdc.id_pais = ps.id')
        ->get('tasa_cambio tdc')
        ->result();
        return $query;
        
    }
    
    public function guardar($data, $id_tasa)
    {
     //   var_dump($data);
        if($id_tasa){
            $this->db
                
            ->set('id_pais'                 , $data['id_pais'])
            ->set('nombre_moneda'           , $data['nombre_moneda'])
            ->set('tasa'          , $data['tasa'])
            ->where('id', $id_tasa)
            ->update('tasa_cambio');
        return ($this->db->affected_rows()>0);

          
        }else{
            return $this->db->insert("tasa_cambio", $data);
        }
    }

    public function mostrar_tasa_cambio($id_tasa)
    {
        
        $query = $this->db->select('*')
            ->where("id", $id_tasa)
            ->get('tasa_cambio')
            ->row();
        return $query;
    }
          
    
    public function eliminar_tasa_cambio($id_tasa)
    {
        $this->db->where("id", $id_tasa);
        $this->db->delete("tasa_cambio");
    }

    public function eliminar($id_tasa)
    {
        $this->db->where("id_tasa", $id_tasa);
        
    }

    public function paises()
    {
        $query = $this->db
        ->select('*')
        ->get('pais')
        ->result();
        return $query;
        
    }

    public function mostrar_pais($id)
    {
        $query = $this->db->select('*')->where("id", $id)->get('pais')->row();
        return $query;
    }

    public function get_tasa($id_pais)
    {
        $query = $this->db->select('*')
            ->where("id_pais", $id_pais)
            ->get('tasa_cambio')
            ->row();
        return $query;
    }

}

/* End of file ModelName.php */
