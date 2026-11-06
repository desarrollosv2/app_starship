<?php

class Fempleadotfijos
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
                'id'    => 'Form_Empleadotfijos',
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
                'id'    => 'submit_usuario_tfijos',
                'class' => 'btn btn-sm btn-success bt-2',
                'onclick' => 'crear_empleado_tfijos()'
            	 )
            );
            
        $this->datos['form_cierre'] = form_close();  

        $this->datos['form_cerrar'] = form_button(
            '','Cerrar',
            array(
                'id'      => 'submit_cerrar_tfijos',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'cerrar_empleado_tfijos()'
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
    
    private function tfijos()
    {
        $id  = 'tfijos';
        $nom = 'tfijos';
        
        $this->datos['tfijos_label'] = form_label(
            'Telefono',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['tfijos'] = form_input(
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
        $this->tfijos();
        
        return $this->datos;
    }

}
