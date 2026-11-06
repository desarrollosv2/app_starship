<?php

class Fempleado
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
                'id'    => 'Form_Empleado',
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
                'onclick' => 'crear_empleado()'
            	 )
            );
            
        $this->datos['form_cierre'] = form_close();  

        $this->datos['form_cerrar'] = form_button(
            '','Cerrar',
            array(
                'id'      => 'submit_cerrar',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'cerrar_empleado()'
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
    
    private function celular()
    {
        $id  = 'celular';
        $nom = 'celular';
        
        $this->datos['celular_label'] = form_label(
            'celular',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['celular'] = form_input(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-25",
                'maxlength' => "9"
            ));
    }




    public function get_formulario()
    {
        $this->fin();
        $this->inicio();
        $this->nombre();
        $this->celular();
        
        return $this->datos;
    }

}
