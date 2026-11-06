<?php

$config = array (
       
    /**
     * login
     * */
    'login'
    => array(
        
        array('field' => 'correo','label' => 'Correo ','rules' => 'required|is_string|trim|valid_email'),
        array('field' => 'clave','label' => 'Clave','rules' => 'required|is_string|trim'),
        
    ),  
    
    //este es el final
);