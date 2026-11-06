<?php

class Fusuarios 
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
                'id'    => 'Form_Usuario',
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
                'id'    => 'submit_usuario',
                'class' => 'btn btn-sm btn-success bt-2',
                'onclick' => 'crear_usuario()'
            	 )
            );
            
        $this->datos['form_cierre'] = form_close();  

        $this->datos['form_cerrar'] = form_button(
            '','Cerrar',
            array(
                'id'      => 'submit_cerrar',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'cerrar_usuario()'
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
                'class'     => "{$this->cls} w-50",
                'maxlength' => "50",
                'required'  => 'required'
            )
        );
    }
    
    private function correo()
    {
        $id  = 'correo';
        $nom = 'correo';
        
        $this->datos['correo_label'] = form_label(
            'Correo',
            '',
            array('class' => 'control-label label')
            );
        
        $this->datos['correo'] = form_input(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-75",
                'maxlength' => "100"
            ));
    }

    private function clave()
    {
        $id  = 'clave';
        $nom = 'clave';
        
        $this->datos['clave_label'] = form_label(
            'Clave',
            '',
            array('class' => 'control-label label')
            );
        
        $this->datos['clave'] = form_password(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-25",
                'maxlength' => "15"
            ));
    }

    private function privado()
    {
        $id  = 'privado';
        $nom = 'privado';
        
        $this->datos['privado_label'] = form_label(
            'Ver Documentos Privado',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['privado'] = form_checkbox(
            array(
                'id'        => $id,
                'name'      => $nom
            ));
    }

    public function get_formulario()
    {
        $this->fin();
        $this->inicio();
        $this->nombre();
        $this->correo();
        $this->clave();
        $this->privado();

        
        return $this->datos;
    }

}
