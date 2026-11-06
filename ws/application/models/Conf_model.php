<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Conf_model extends CI_Model
{
    public function get_destinatario($id)
    {
        
    $DBMA = $this->load->database('contabilidad', TRUE);
         $query =    $DBMA->select('*')
               ->where('id_cliente', $id)
               ->get('destinatarios')
               ->result();
        return $query;
    }
}
/* End of file ModelName.php */