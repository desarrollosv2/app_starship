<?php

class Fproveedor
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
                'id'    => 'Form_Proveedor',
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
                'id'      => 'submit_proveedor',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'crear_proveedor()'
            	 )
            );
            
        $this->datos['form_cierre'] = form_close();

        $this->datos['form_cerrar'] = form_button(
            '','Cerrar',
            array(
                'id'      => 'submit_cerrar',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'cerrar_proveedor()'
            )
        );
    }

    private function proveedor()
    {
        $id  = 'proveedor';
        $nom = 'proveedor';
        
        $this->datos['proveedor_label'] = form_label(
            'Proveedor',
            '',
            array('class' => 'control-label')
            );
        
        $this->datos['proveedor'] = form_input(
            array(
                'id'       => $id,
                'name'     => $nom,
                'class'    => "{$this->cls}"
            ));
    }

 	public function get_formulario()
    {
        $this->fin();
        $this->inicio();
        $this->proveedor();
       // $this->clave();
        
        return $this->datos;
    }



}


?>