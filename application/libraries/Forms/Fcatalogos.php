<?php

class Fcatalogos
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
                'id'    => 'Form_Empresa',
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
                'id'      => 'submit_empresa',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'crear_empresa()'
            	 )
            );
            
        $this->datos['form_cierre'] = form_close();

        $this->datos['form_cerrar'] = form_button(
            '','Cerrar',
            array(
                'id'      => 'submit_cerrar',
                'class'   => 'btn btn-sm btn-success',
                'onclick' => 'cerrar_empresa()'
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
                'id'       => $id,
                'name'     => $nom,
                'class'    => "{$this->cls}"
            ));
    }

 	public function get_formulario()
    {
        $this->fin();
        $this->inicio();
        $this->nombre();
       // $this->clave();
        
        return $this->datos;
    }



}


?>