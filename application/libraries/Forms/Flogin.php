
<style>
    /* Estilos para las etiquetas */
    label {
        font-size: 14px;
        color: #aaa;
        margin-bottom: 5px;
        display: block;
        font-weight: normal;
    }

    /* Estilos para los inputs */
    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        color: #000;
        background-color: #fff;
        /* Fondo blanco por defecto */
        border: none;
        border-bottom: 1px solid #ccc;
        margin-bottom: 20px;
        outline: none;
        transition: border-bottom-color 0.3s ease, background-color 0.3s ease;
    }

    /* Efecto hover/focus para inputs */
    input[type="text"]:focus,
    input[type="password"]:focus {
        border-bottom: 1px solid #000;
        background-color: #fff;
        /* Mantener fondo blanco al enfocar */
        border-color: #FE4F00;
    }

    /* Estilo para evitar autofill gris en Chrome */
    input:-webkit-autofill {
        -webkit-box-shadow: 0 0 0px 1000px white inset !important;
        /* Forzar fondo blanco */
        box-shadow: 0 0 0px 1000px white inset !important;
        /* Aplicar también en otros navegadores */
        background-color: white !important;
    }

    /* Mantener el color del texto del input después de que el usuario lo llene */
    input[type="text"]:not(:placeholder-shown),
    input[type="password"]:not(:placeholder-shown) {
        color: #000;
        /* Asegurar que el texto dentro del input permanezca negro */
        background-color: #fff;
        /* Mantener el fondo blanco si tiene texto */
    }

    /* Placeholder estilo para gris claro */
    input::placeholder {
        color: #aaa;
        font-size: 14px;
    }
</style>
<?php



class Flogin

{



    protected $cls;

    protected $datos;

    protected $accion;



    function __construct()

    {

        $this->cls = 'form-control';

        $this->datos = array();
    }



    public function set_accion($valor)

    {

        $this->accion = $valor;
    }





    private function inicio1()

    {

        $this->datos['form'] = form_open(

            $this->accion,

            array(

                'id'    => 'Form_Login',

                'class' => 'form-horizontal'

            )

        );
    }



    private function inicio()

    {

        $this->datos['form'] = form_open(

            $this->accion,

            array(

                'id'    => 'Form_Login',

                'class' => 'form-horizontal'

            )

        );
    }



    private function fin()

    {

        $this->datos['submit'] = form_submit(

            '',

            'Ingresar',

            array(

                'class' => 'btn'



            )

        );



        $this->datos['cancelar'] = form_submit(

            '',

            'Cancelar',

            array('class' => 'btn btn-default')

        );





        $this->datos['form_cierre'] = form_close();
    }



    private function user()

    {

        $id  = 'correo';

        $nom = 'correo';




        $this->datos['correo'] = form_input(

            array(

                'id'       => $id,

                'name'     => $nom,

                'value'    => set_value_input(array(), 'correo', 'correo'),



                'placeholder' => "Digitar Correo"

            )
        );
    }



    private function clave()

    {

        $id  = 'clave';

        $nom = 'clave';






        $this->datos['clave'] = form_input(

            array(

                'id'       => $id,

                'name'     => $nom,

                'type'     => 'password',



                'placeholder' => 'Digitar Clave.'

            )
        );
    }





    public function get_formulario()

    {

        $this->fin();

        $this->inicio();

        $this->user();

        $this->clave();



        return $this->datos;
    }
}