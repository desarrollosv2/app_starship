<?php

class Fcliente
{

    protected $cls;
    protected $datos;

    function __construct()
    {
        $this->cls = 'form-control';
        $this->datos = array();   
    }

 
    private function inicio()
    {
        $this->datos['form'] = form_open(
            '',
            array(
                'id'    => 'Form_Cliente',
                'class' => 'form-horizontal'
            )
            );
    }
    
  

    private function fin()
    {
        $this->datos['submit'] = form_button(
            '',
            'Guardar',
            array(
                'id'    => 'submit_copiadora',
                'class' => 'btn btn-sm btn-success bt-2',
                'onclick' => 'crear_cliente()'
            	 )
            );
            
        $this->datos['form_cierre'] = form_close();  

        $this->datos['form_cerrar'] = form_button(
            '','Cerrar',
            array(
                'id'      => 'submit_cerrar',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'cerrar_cliente()'
            )
        );        
    }
    
    private function codigo()
    {
        $id  = 'codigo';
        $nom = 'codigo';
        
        $this->datos['codigo_label'] = form_label(
            'Codigo',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['codigo'] = form_input(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-25",
                'maxlength' => "10",
                'required'  => 'required'
            )
        );
    }
    
    private function nombre()
    {
        $id  = 'nombre';
        $nom = 'nombre';
        
        $this->datos['nombre_label'] = form_label(
            'Nombre',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['nombre'] = form_input(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-25",
                'maxlength' => "50"
            ));
    }

    private function correo()
    {
        $id  = 'correo';
        $nom = 'correo';
        
        $this->datos['correo_label'] = form_label(
            'Correo',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['correo'] = form_input(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-75",
                'maxlength' => "100"
            ));
    }

   


    public function get_formulario()
    {
        $this->fin();
        $this->inicio();
        $this->codigo();
        $this->nombre();
        $this->correo();
  
        
        return $this->datos;
    }

}
