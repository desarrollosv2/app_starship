
<?php
defined('BASEPATH') or exit('No direct script access allowed');


class ZplController extends CI_Controller
{
    public function __construct()
    {
         parent::__construct();
        $this->load->library('Zpl_library');
        
    }

   public function imprimir_guia()
    {
        $orden = 'GD0088063';
        $token = 'f4b64fdd2a0f04640cf50a6c79257c38d4f1771c';

        $guias = $this->zpl_library->obtener_guias($orden, $token);
        if (!$guias) {
            echo "No se pudieron obtener las guías.";
            return;
        }

        $zpl = $this->zpl_library->generar_zpl($guias);
        echo $zpl; // cadena a enviar

       
    }

}

