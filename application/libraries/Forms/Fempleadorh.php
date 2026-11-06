<?php

class Fempleadorh
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
                'id'    => 'Form_Empleado_rh',
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
                'id'    => 'submit_empleado_rh',
                'class' => 'btn btn-sm btn-success bt-2',
                'onclick' => 'crear_empleado_rh()'
            	 )
            );
            
        $this->datos['form_cierre'] = form_close();  

        $this->datos['form_cerrar'] = form_button(
            '','Cerrar',
            array(
                'id'      => 'submit_cerrar',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'cerrar_empleado_rh()'
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

    private function apellido()
    {
        $id  = 'apellido';
        $nom = 'apellido';
        
        $this->datos['apellido_label'] = form_label(
            'Apellido',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['apellido'] = form_input(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-25",
                'maxlength' => "50"
            ));
    }

   


    public function get_formulario()
    {
        $this->fin();
        $this->inicio();
        $this->nombre();
        $this->apellido();
  
        
        return $this->datos;
    }

}
