<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class Inventario_Model extends CI_Model {
    
        /*
        public function listado_prealerta($casillero)
        {
            $query = $this->db
            ->select('*')
            ->get('inventario')
            ->result();
            return $query;
            
        }
        */

           public function selecionar_paquetes($estatus)
        {
           
            $query = $this->db
            ->select('*')
            ->where('estatus', $estatus)
            ->get('warehouse_detalle')
            ->result();
            return $query;
            
        }

        public function guardar_inventario($data){
            
             $this->db->insert('warehouse', $data);
             return $this->db->insert_id();
        }

        public function obtener_fecha($id,$estatus)
        {
            $rsl =	$this->db
                 ->select('warehouse')
                 ->select_min('fecha')
                 ->where('warehouse', $id)
                 ->where('id_estatus', $estatus)
                 ->get('detalle')
                 ->row();
            return $rsl;
        }

          public function consulta_guias($id)
        {
            $rsl =	$this->db
                 ->select('*')
                 ->where('manifiesto', $id)
                 ->get('warehouse')
                 ->result();
            return $rsl;     
        }
     
     
      
     public function consulta_guia_master($id)
     {
     
          $query = $this->db
                            ->select("w.*, p.guia, c.nombre nombre_cliente,  p.total")
                            ->join('poliza  p', 'p.guia = w.numero_warehouse', 'left')
                            ->join('clientes  c', 'c.casillero = w.casillero', 'left')
                            ->where('w.manifiesto', $id)
                            ->get('warehouse w')
                            ->result();
      
            return $query;
          
     }

       
     public function query_guia_master($id)
     {
         $query = $this->db->where('manifiesto', $id)
        ->get('preclasificacion')
        ->row();
         return $query;
     }
     

    }
    
    /* End of file ModelName.php */