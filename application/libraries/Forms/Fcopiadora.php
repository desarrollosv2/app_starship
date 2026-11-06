<?php

class Fcopiadora
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
                'id'    => 'Form_Copiadora',
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
                'onclick' => 'crear_copiadora()'
            	 )
            );
            
        $this->datos['form_cierre'] = form_close();  

        $this->datos['form_cerrar'] = form_button(
            '','Cerrar',
            array(
                'id'      => 'submit_cerrar',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'cerrar_copiadora()'
            )
        );        
    }
    
    private function ubicacion()
    {
        $id  = 'ubicacion';
        $nom = 'ubicacion';
        
        $this->datos['ubicacion_label'] = form_label(
            'Ubicacion',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['ubicacion'] = form_input(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-50",
                'maxlength' => "50",
                'required'  => 'required'
            )
        );
    }
    
    private function plan()
    {
        $id  = 'plan_copia';
        $nom = 'plan_copia';
        
        $this->datos['plan_copia_label'] = form_label(
            'Plan Copia',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['plan_copia'] = form_input(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-25",
                'maxlength' => "9"
            ));
    }

    private function costo()
    {
        $id  = 'costo_plan';
        $nom = 'costo_plan';
        
        $this->datos['costo_plan_label'] = form_label(
            'Costo Plan',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['costo_plan'] = form_input(
            array(
                'id'        => $id,
                'name'      => $nom,
                'class'     => "{$this->cls} w-25",
                'maxlength' => "9"
            ));
    }

    private function costo_exceso()
    {
        $id  = 'costo_exceso';
        $nom = 'costo_exceso';
        
        $this->datos['costo_exceso_label'] = form_label(
            'Costo Exceso',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['costo_exceso'] = form_input(
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
        $this->ubicacion();
        $this->plan();
        $this->costo();
        $this->costo_exceso();
        
        return $this->datos;
    }

}
