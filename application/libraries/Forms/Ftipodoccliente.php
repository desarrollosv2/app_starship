<?php

class Ftipodoccliente
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
                'id'    => 'Form_tipo_documento',
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
                'id'    => 'submit_tipo_doc_cliente',
                'class' => 'btn btn-sm btn-success bt-2',
                'onclick' => 'crear_tipo_doc_cliente()'
            	 )
            );
            
        $this->datos['form_cierre'] = form_close();  

        $this->datos['form_cerrar'] = form_button(
            '','Cerrar',
            array(
                'id'      => 'submit_cerrar',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'cerrar_tipo_doc_cliente()'
            )
        );        
    }
    
    private function nombre()
    {
        $id  = 'nombre';
        $nom = 'nombre';
        
        $this->datos['nombre_label'] = form_label(
            'Nombre Documento',
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
    
    private function privado()
    {
        $id  = 'privado';
        $nom = 'privado';
        
        $this->datos['privado_label'] = form_label(
            'Privado',
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
        $this->privado();

        
        return $this->datos;
    }

}
