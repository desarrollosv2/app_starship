<?php
    defined('BASEPATH') or exit('No direct script access allowed');
    
    class FController extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
           
            $this->load->database();
            $this->load->model('FModel');
           
        }
        
        public function index()
        {
            
                   
        }

        public function facturacion()
        {
 
        
                  
        }


    }
    
    /* End of file Controllername.php */