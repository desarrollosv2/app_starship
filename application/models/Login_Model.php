<?php
    
    defined('BASEPATH') or exit('No direct script access allowed');
    
    class Login_Model extends CI_Model{
        
    	public function consultar_usu($correo){
         $rsl =	$this->db
              ->select('*')
              ->where('correo',$correo)
              ->get('usuarios')
              ->result();
         return $rsl;   

     	}
    }     

?>