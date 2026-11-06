<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

   public function __construct()
    {
        parent::__construct();
        $this->load->database();
       
        $this->load->model('Manifiesto_model');
    }
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
	//	$this->load->view('welcome_message');
	$guia='PRUECG-4047-202311226307';
	$warehouse="";
    $query=	 $this->Manifiesto_model->guia_alternativa($guia);
    foreach( $query as $fila){
        
        $warehouse= $fila->tracking_number;
    }
    
           echo "guia".$warehouse;
           
        //	 var_dump($r);
	}
	
	public function replace(){
	  //   $this->Manifiesto_model->guia_alternativa($warehouse);
          
	}
}
