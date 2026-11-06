<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class Login_Controller extends CI_Controller {
            
        public function __construct()
        {
            parent::__construct();
            $this->load->database();
            $this->load->model('Login_Model');
            $this->load->helper('path');
        }

        public function password($correo="")
	   {
        $data = $this->Login_Model->consultar_usu($correo);
        var_dump($data);
        $this->load->view('ps_login/log_in',$this->$data);
		
	   }

}  

?>