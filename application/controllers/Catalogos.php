<?php



defined('BASEPATH') OR exit('No direct script access allowed');



set_time_limit(0); 



class Catalogos extends CI_Controller

{

    function __construct() {

        parent::__construct();

session_start();

        if (login()) {

            $this->data = array();

          } else {

            redirect(login_url());

          }



    }

   



    public function usuarios()

    {

        $this->load->library('Forms/Fusuarios');

        $form_usuarios = new Fusuarios();

        $this->datos['usuarios'] = $this->Catalogos_Model->buscar_usuarios();

        $this->datos['empresas'] = $this->Catalogos_Model->buscar_empresa($_SESSION["id_empresa"]);

        $this->load->view("catalogos/usuario", array_merge($this->datos, $form_usuarios->get_formulario()));

    }



    public function crear_usuario()

    {



        $datos = array(

            'nombre'           => $_POST["nombre"], 

            'correo'           => $_POST["correo"],

            'clave'            => $_POST["clave"],

            'id_empresa'       => $_POST["empresas"],

            'ver_doc_privado'  => $_POST["cb"]

        );

        $this->Catalogos_Model->mantenimiento_usuario($datos);

        $this->datos['usuarios'] = $this->Catalogos_Model->buscar_usuarios();

        $this->load->view("catalogos/vista_usuarios", $this->datos);

    }



    public function editar_usuario()

    {

         $datos = array(

            'id'              => $_POST["id_usuario".$_POST["id_Reg"]],

            'nombre'          => $_POST["nombre".$_POST["id_Reg"]], 

            'correo'          => $_POST["correo".$_POST["id_Reg"]],

            'clave'           => $_POST["clave".$_POST["id_Reg"]],

            'id_empresa'      => $_POST["empresas"],

            'ver_doc_privado' => $_POST["cb"]

        );



        $this->Catalogos_Model->mantenimiento_usuario($datos); 

    }



    public function consulta_usuario($id)

    {

        //$datos viene en un arreglo

        $datos = $this->Catalogos_Model->buscar_usuarios($id);



         enviarJson(array(

            'id'       => $datos[0]->id,  

			'nombre'   => $datos[0]->nombre,

            'correo'   => $datos[0]->correo,

            'privado'  => $datos[0]->privado

        )); 

    }

    

    public function existe_email($email, $id_usuario)

    {



        //$datos viene en un arreglo

        $datos = $this->Catalogos_Model->buscar_usuarios_email($email, $id_usuario);



        $existe = "";



        if (count($datos) > 0){

            $existe = 'S';

        }else{

            $existe = 'N';

        }



        echo $existe;



/*         enviarJson(array(

            'id'     => $datos[0]->id,  

			'nombre' => $datos[0]->nombre,

			'correo' => $datos[0]->correo

        ));

 */    }



    function empresa(){



    	$this->load->library('Forms/Fcatalogos');

        $form_catalogos= new Fcatalogos();

       // $form_catalogos->set_accion(base_url("catalogos/grabar_empresa"));

        //$this->datos['vista'] = 'catalogos/empresa';

        $this->datos['empresa'] = $this->Catalogos_Model->buscar_empresa();

    	$this->load->view("catalogos/empresa", array_merge($this->datos, $form_catalogos->get_formulario()));

    }



    function grabar_empresa(){



        //var_dump($_POST['nombre']);

        //die();

        if (isset($_POST['nombre']) && strlen($_POST['nombre']) > 0 )

            {

                $datosparm = array('nombre' => $_POST['nombre']);

    	        $this->Catalogos_Model->grabar_empresa($datosparm);

            }

            else{

                //$this->session->set_flashdata('css','success');

                //$this->session->set_flashdata('mensaje', 'Falta Ingresar Nombre de Empresa');

                //enviarJson(array('mensaje' => 'Error, falta la partida arancelaria.'));

                json_encode(array('mensaje' => 'Error, al grabar Empresa.'));

                //$this->cargar_empresa();

            }

        



    }



    function eliminar_empresa($id){



        $this->Catalogos_Model->eliminar_empresa($id);

    }



    public function cargar_empresa()

    {

        $this->datos['empresa'] = $this->Catalogos_Model->buscar_empresa();

        $this->load->view('catalogos/vista_empresa', $this->datos);

     }



    public function crear_permiso()

    {



        $datos = array(

            'id_usuario' => $_POST["user_id"],

            'id_menu'    => $_POST["permiso_id"]

        );

        $this->Catalogos_Model->mantenimiento_permiso($datos, "grabar");



    }



    public function eliminar_permiso()

    {

        $this->Catalogos_Model->mantenimiento_permiso($_POST["permiso_id"], "eliminar");

    }

    

    public function permisos_asignado($id_Reg)

     {

        $this->datos['usuario'] = $this->Catalogos_Model->buscar_usuarios($id_Reg);

        $this->datos['permiso'] = $this->Catalogos_Model->buscar_permisos_asignado($id_Reg);

    	$this->load->view("catalogos/vista_permiso", $this->datos);

     }



    public function permisos_no_asignado($id_Reg)

     {

        $this->datos['usuario'] = $this->Catalogos_Model->buscar_usuarios($id_Reg);

        $this->datos['permiso_no_asignado'] = $this->Catalogos_Model->buscar_permisos_no_asignado($id_Reg);

    	$this->load->view("catalogos/mostrar_permiso", $this->datos);

     }



    public function mante_permiso($id_Reg){

        $this->datos['usuario'] = $this->Catalogos_Model->buscar_usuarios($id_Reg);

        $this->datos['permiso'] = $this->Catalogos_Model->buscar_permisos_asignado($id_Reg);

        $this->datos['permiso_no_asignado'] = $this->Catalogos_Model->buscar_permisos_no_asignado($id_Reg);

        $this->load->view("catalogos/crear_permiso", $this->datos);

    }



    function procesos(){



    	$this->load->library('Forms/Fproceso');

        $form_proceso = new Fproceso();

        $this->datos['datos_proceso'] = $this->Catalogos_Model->buscar_proceso();



    	$this->load->view("catalogos/proceso", array_merge($this->datos, $form_proceso->get_formulario()));

    }



    function grabar_proceso(){



        if (isset($_POST['proceso']) && strlen($_POST['proceso']) > 0 )

            {

                $datosparm = array('proceso' => $_POST['proceso']);

                $this->Catalogos_Model->mantenimiento_proceso($datosparm);

                $this->datos['datos_proceso'] = $this->Catalogos_Model->buscar_proceso();

                $this->load->view("catalogos/vista_proceso", $this->datos);



            }

            else{

                json_encode(array('mensaje' => 'Error, al grabar Proceso.'));

            }  

    }

    

    public function editar_proceso()

    {

         $datos = array(

            'id'       => $_POST["id_proceso".$_POST["id_Reg"]],

            'proceso'  => $_POST["proceso".$_POST["id_Reg"]]

        );



        $this->Catalogos_Model->mantenimiento_proceso($datos); 

    }



    public function consulta_proceso($id)

    {

        //$datos viene en un arreglo

        $datos = $this->Catalogos_Model->buscar_proceso($id);



        enviarJson(array(

            'id'      => $datos[0]->id,  

			'proceso' => $datos[0]->proceso

        ));

    }





    public function empleados()

    {

        $this->load->library('Forms/Fempleado');

        $form_empleados = new Fempleado();

        $this->datos['empleados'] = $this->Catalogos_Model->buscar_empleados();

        $this->datos['procesos'] = $this->Catalogos_Model->buscar_proceso();

        $this->datos['empresas'] = $this->Catalogos_Model->buscar_empresa();

        $this->load->view("catalogos/empleado", array_merge($this->datos, $form_empleados->get_formulario()));

    }



    public function crear_empleado()

    {



        $datos = array(

            'nombre'          => $_POST["nombre"], 

            'numero_celular'  => $_POST["celular"],

            'id_proceso'      => $_POST["procesos"],

            'id_empresa'      => $_POST["empresas"]

        );

        $this->Catalogos_Model->mantenimiento_empleados($datos);

        $this->datos['empleados'] = $this->Catalogos_Model->buscar_empleados();

        $this->load->view("catalogos/vista_empleados", $this->datos);

    }



    public function editar_empleado()

    {

         $datos = array(

            'id'               => $_POST["id_empleado".$_POST["id_Reg"]],

            'nombre'           => $_POST["nombre".$_POST["id_Reg"]], 

            'numero_celular'   => $_POST["celular".$_POST["id_Reg"]],

            'id_empresa'       => $_POST["empresas"],

            'id_proceso'       => $_POST["procesos"]

        );



        $this->datos['procesos'] = $this->Catalogos_Model->buscar_proceso();

        $this->Catalogos_Model->mantenimiento_empleados($datos); 

    }



    public function consulta_empleado($id)

    {

        //$datos viene en un arreglo

        $datos = array(

                'id'  => $id

        );

        $datos = $this->Catalogos_Model->buscar_empleados($datos);



        enviarJson(array(

            'id'      => $datos[0]->id,  

			'nombre'  => $datos[0]->nombre,

			'celular' => $datos[0]->numero_celular

        ));

    }



    public function consulta_celular($numero_celular, $id_empleado)

    {

       //$datos viene en un arreglo

        $datos = array(

            'celular'  => $numero_celular,

            'id'       => $id_empleado

        );

        $datos = $this->Catalogos_Model->consulta_celular($datos);



        $existe = '';

        

        if (count($datos) > 0 ) {

            $existe = 'S';

        }else{

            $existe = 'N';

        }



        echo $existe;        



    }



    function proveedor(){



    	$this->load->library('Forms/Fproveedor');

        $form_proveedor = new Fproveedor();

        $this->datos['datos_proveedor'] = $this->Catalogos_Model->buscar_proveedor();



    	$this->load->view("catalogos/proveedor", array_merge($this->datos, $form_proveedor->get_formulario()));

    }

    



    function grabar_proveedor(){



        if (isset($_POST['proveedor']) && strlen($_POST['proveedor']) > 0 )

            {

                $datosparm = array('nombre' => $_POST['proveedor']);

                $this->Catalogos_Model->mantenimiento_proveedor($datosparm);

                $this->datos['datos_proveedor'] = $this->Catalogos_Model->buscar_proveedor();

                $this->load->view("catalogos/vista_proveedor", $this->datos);



            }

            else{

                json_encode(array('mensaje' => 'Error, al grabar Proveedor.'));

            }  

    }

    function companyByCountry($idCountry)
    {
        $this->datos['empresa'] = $this->Catalogos_Model->companyByCountry($idCountry);
        //var_dump($this->datos['empresa']);
        $this->load->view("catalogos/empresa", $this->datos);
    }

    

    public function editar_proveedor()

    {

         $datos = array(

            'id'      => $_POST["id_proveedor".$_POST["id_Reg"]],

            'nombre'  => $_POST["proveedor".$_POST["id_Reg"]]

        );



        $this->Catalogos_Model->mantenimiento_proveedor($datos); 

    }



    public function consulta_proveedor($id)

    {

        //$datos viene en un arreglo

        $datos = $this->Catalogos_Model->buscar_proveedor($id);



        enviarJson(array(

            'id'        => $datos[0]->id,  

			'proveedor' => $datos[0]->nombre

        ));

    }





    public function copiadora()

    {

        $this->load->library('Forms/Fcopiadora');

        $form_copiadora = new Fcopiadora();

        $this->datos['copiadora'] = $this->Catalogos_Model->buscar_copiadoras();

        $this->datos['proveedor'] = $this->Catalogos_Model->buscar_proveedor();

        $this->datos['empresas'] = $this->Catalogos_Model->buscar_empresa(); 

        $this->load->view("catalogos/copiadoras", array_merge($this->datos, $form_copiadora->get_formulario()));

 

    }



    public function crear_copiadora()

    {



        $datos = array(

            'ubicacion'      => $_POST["ubicacion"], 

            'id_empresa'     => $_POST["empresas"],

            'plan_copia'     => $_POST["plan_copia"],

            'costo_plan'     => $_POST["costo_plan"],

            'costo_exceso'   => $_POST["costo_exceso"],

            'id_proveedor'   => $_POST["proveedor"]



        );

        $this->Catalogos_Model->mantenimiento_copiadoras($datos);

        $this->datos['copiadora'] = $this->Catalogos_Model->buscar_copiadoras();

        $this->load->view("catalogos/vista_copiadora", $this->datos);

    }



    public function editar_copiadora()

    {

         $datos = array(

            'id'             => $_POST["id_copiadora".$_POST["id_Reg"]],

            'ubicacion'      => $_POST["ubicacion".$_POST["id_Reg"]],

            'id_empresa'     => $_POST["empresas"],

            'plan_copia'     => $_POST["plan_copia".$_POST["id_Reg"]],

            'costo_plan'     => $_POST["costo_plan".$_POST["id_Reg"]],

            'costo_exceso'   => $_POST["costo_exceso".$_POST["id_Reg"]],

            'id_proveedor'   => $_POST["proveedor"]



        );



        $this->datos['procesos'] = $this->Catalogos_Model->buscar_proceso();

        $this->Catalogos_Model->mantenimiento_copiadoras($datos); 

    }



    public function consulta_copiadora($id)

    {

        //$datos viene en un arreglo

        $datos = array(

                'id'  => $id

        );

        $datos = $this->Catalogos_Model->buscar_copiadoras($datos);



        enviarJson(array(

            'id'             => $datos[0]->id,  

            'ubicacion'      => $datos[0]->ubicacion,

            'plan_copia'     => $datos[0]->plan_copia,

            'costo_plan'     => $datos[0]->costo_plan,

            'costo_exceso'   => $datos[0]->costo_exceso

        ));

    }


 


    public function cliente()

    {

        //$this->load->library('Forms/Fcliente');

       // $form_cliente = new Fcliente();
      // $this->datos['empresas']        = $this->Catalogos_Model->buscar_empresa($_SESSION["id_empresa"]);

       $this->datos['tipodoc_cliente'] = $this->Catalogos_Model->buscar_tipo_doc_cliente('',$_SESSION["id_empresa"]);

      // $this->load->view('formatos/view_formato_cliente' , $this->datos);

        $this->datos['empresas'] = $this->Catalogos_Model->buscar_empresa($_SESSION["id_empresa"]);

       $this->datos['cliente'] = $this->Catalogos_Model->buscar_cliente($_SESSION["id_empresa"]);
      

        $this->load->view("catalogos/cliente", $this->datos);

    }



    public function crear_cliente()

    {



        $datos = array(

            'codigo_cliente' => $_POST["codigo"], 

            'nombre_cliente' => $_POST["nombre"],

            'id_empresa'     => $_POST["empresas"],

            'correo'         => $_POST["correo"]

            



        );
       

        $this->Catalogos_Model->mantenimiento_cliente($datos);

        $this->datos['cliente'] = $this->Catalogos_Model->buscar_cliente();

        $this->load->view("catalogos/vista_cliente", $this->datos);

    }



    public function crear_cliente_lotes()

    {

        $contador = 0;

          

        $destino = getcwd()."/public/uploads/";



        if (!is_dir($destino)) {

            mkdir($destino, 0777, true);

        }



        if (file_exists((string)$_FILES['file']['tmp_name'])) {

             $extension = explode(".", $_FILES["file"]["name"]);

             $nombre = time()."-plantilla.".$extension[1];





             if (move_uploaded_file($_FILES['file']['tmp_name'], $destino."/".$nombre)) {

                 $link = $destino."/".$nombre;

             } else {

                 $_SESSION["no_clasificado"] = "Error al subir el archivo";

                 //redirect("movimientos/celular");

                 }

         } 

   



        $this->load->library('PHPEXCEL/PHPExcel.php');

        $object = PHPExcel_IOFactory::load($link);

        

        foreach($object->getWorksheetIterator() as $worksheet) {

            $highestRow    = $worksheet->getHighestRow();

            $highestColumn = $worksheet->getHighestColumn();

           

            foreach($object->getWorksheetIterator() as $worksheet) {

                $highestRow    = $worksheet->getHighestRow();

                $highestColumn = $worksheet->getHighestColumn();

                       

                for($row=2; $row<=$highestRow; $row++) {



                    //Buscar Codigo Cliente

                    $datos = array(

                        'codigo_cliente' => $worksheet->getCellByColumnAndRow(0, $row)->getValue(),

                        'id_empresa'     => $_POST["empresas"]

                    );

                    $datos = $this->Catalogos_Model->buscar_cliente($datos);



                    if (count($datos) == 0 ) {



                        //var_dump($proveedor);

                         //die();



                        $data = array(

                            'codigo_cliente' => trim($worksheet->getCellByColumnAndRow(0, $row)->getValue()),

                            'nombre_cliente' => trim($worksheet->getCellByColumnAndRow(1, $row)->getValue()),

                            'id_empresa'     => $_POST["empresas"],

                            'correo'         => trim($worksheet->getCellByColumnAndRow(2, $row)->getValue())

                        );



                        $this->Catalogos_Model->mantenimiento_cliente($data);

                        $contador += 1;

                    }   

                }

            }



                unlink($link);

                $data = array('id_empresa' => $_POST["empresas"]);

                $this->datos['cliente'] = $this->Catalogos_Model->buscar_cliente($data);

                $this->load->view("catalogos/vista_cliente", $this->datos);



        }



    }



    public function editar_cliente()

    {

         $datos = array(

            'id'              => $_POST["id_cliente".$_POST["id_Reg"]],

            'codigo_cliente'  => $_POST["codigo".$_POST["id_Reg"]],

            'nombre_cliente'  => $_POST["nombre".$_POST["id_Reg"]],

            'correo'          => $_POST["correo".$_POST["id_Reg"]],

            'id_empresa'      => $_POST["empresas"]

        );



        $this->datos['procesos'] = $this->Catalogos_Model->buscar_proceso();

        $this->Catalogos_Model->mantenimiento_cliente($datos); 

    }



    public function actualizar_cliente()

    {

         $datos = array(

            'id'              => $_POST["id_cliente"],

            'codigo_cliente'  => $_POST["codigo"],

            'nombre_cliente'  => $_POST["nombre_cliente"],

            'correo'          => $_POST["correo"],

            'id_empresa'      => $_POST["empresas"]

        );



        $this->datos['procesos'] = $this->Catalogos_Model->buscar_proceso();

        $this->Catalogos_Model->mantenimiento_cliente($datos); 

    }



    public function consulta_cliente($id)

    {

        //$datos viene en un arreglo

        $datos = array(

                'id'  => $id

        );

        $datos = $this->Catalogos_Model->buscar_cliente($datos);



        enviarJson(array(

            'id'         => $datos[0]->id,  

            'codigo'     => $datos[0]->codigo_cliente,

            'nombre'     => $datos[0]->nombre_cliente,

            'correo'     => $datos[0]->correo,

        ));

    }



    public function consulta_cliente_empresa()

    {

        $this->datos['empresas'] = $this->Catalogos_Model->buscar_empresa($_SESSION["id_empresa"]);

       //$this->datos['cliente'] = $this->Catalogos_Model->buscar_cliente();

       $this->load->view("consultas/consulta_cliente", $this->datos);

    }



    public function buscar_cliente_empresa()

    {

        $data = array(

            'id'              => $_POST["id_cliente"],

            'id_empresa'      => $_POST["empresas"]

        );

        

       $this->datos['doc_clientes'] = $this->Movimiento_Model->buscar_doc_clientes($data);

      /*  $this->datos['editar']  = "N";

       $this->datos['ver_doc'] = "S"; */

       //$this->load->view("catalogos/vista_cliente", $this->datos);



       $this->load->view("consultas/v_formatos_cliente", $this->datos);

    }



    public function buscar_cliente_listado()

    {

        $data = array(

            'codigo_cliente'  => $_POST["codigo_cliente"],

            'nombre_cliente'  => $_POST["nombre_cliente"],

            'correo'          => $_POST["correo_cliente"],

            'id_empresa'      => $_POST["empresas"]

        );

        

       $this->datos['cliente'] = $this->Catalogos_Model->buscar_cliente($data);

      /*  $this->datos['editar']  = "N";

       $this->datos['ver_doc'] = "S"; */

       //$this->load->view("catalogos/vista_cliente", $this->datos);



       //var_dump ($this->datos['clientes']);die();



       $this->datos['editar']  = "S";

       $this->datos['empresas'] = $this->Catalogos_Model->buscar_empresa($_SESSION["id_empresa"]);

       $this->load->view("catalogos/vista_cliente", $this->datos);

    }





    public function buscar_cliente()

    {



        $data = array(

            'codigo_cliente'  => $_GET["codigo_cliente"],

            'id_empresa'      => $_GET["empresas"]

        );

        $clientes  = $this->Catalogos_Model->buscar_cliente($data);



        if (count($clientes) > 0 )

        {

            enviarJson(array(

                'id'             => $clientes[0]->id,  

                'nombre_cliente' => $clientes[0]->nombre_cliente,

                'codigo'         => $clientes[0]->codigo_cliente,

                'correo'         => $clientes[0]->correo

            ));

        }else{

            enviarJson(array(

                'id'             => 0,

                'nombre_cliente' => "Codigo no Existe"

            ));

        }

    }



    public function mostrar_ventana()

    {

        //esta pendiente ver el video

    }



    public function consulta_doc_cliente($empresa, $id_empleado)

    {



        $data = array(

            'id'           => $id_empleado,

            'id_empresa'   => $empresa

            );

        $this->datos['doc_clientes']  = $this->Movimiento_Model->buscar_doc_clientes($data);



        $this->load->view("consultas/v_doc_cliente", $this->datos);

    }



    public function empleados_tfijos()

    {

        $this->load->library('Forms/Fempleadotfijos');

        $form_empleados = new Fempleadotfijos();

        $this->datos['empleados'] = $this->Catalogos_Model->buscar_empleados_tfijos();

        $this->datos['procesos'] = $this->Catalogos_Model->buscar_proceso();

        $this->datos['empresas'] = $this->Catalogos_Model->buscar_empresa();



        $this->load->view("catalogos/empleado_tfijos", array_merge($this->datos, $form_empleados->get_formulario()));

    }

   

    public function crear_empleado_tfijos()

    {



        $datos = array(

            'nombre'      => $_POST["nombre"], 

            'numero_tel'  => $_POST["tfijos"],

            'id_proceso'  => $_POST["procesos"],

            'id_empresa'  => $_POST["empresas"]

        );

        $this->Catalogos_Model->mantenimiento_empleados_tfijos($datos);

        $this->datos['empleados'] = $this->Catalogos_Model->buscar_empleados_tfijos();

        $this->load->view("catalogos/vista_empleados_tfijos", $this->datos);

    }



    public function editar_empleado_tfijos()

    {

         $datos = array(

            'id'           => $_POST["id_empleado".$_POST["id_Reg"]],

            'nombre'       => $_POST["nombre".$_POST["id_Reg"]], 

            'numero_tel'   => $_POST["tfijos".$_POST["id_Reg"]],

            'id_empresa'   => $_POST["empresas"],

            'id_proceso'   => $_POST["procesos"]

        );



        $this->datos['procesos'] = $this->Catalogos_Model->buscar_proceso();

        $this->Catalogos_Model->mantenimiento_empleados_tfijos($datos); 

    }



    public function consulta_empleado_tfijos($id)

    {

        //$datos viene en un arreglo

        $datos = array(

            'id'  => $id

        );

        $datos = $this->Catalogos_Model->buscar_empleados_tfijos($datos);


       
       
        enviarJson(array(

            'id'      => $datos[0]->id,  

			'nombre'  => $datos[0]->nombre,

            'tfijos'  => $datos[0]->numero_tel

            



            
            

        ));

    }



    public function consulta_tfijos($id)

    {

       //$datos viene en un arreglo

        $datos = array(

            'numero_tel'  => $id

        );

        $datos = $this->Catalogos_Model->buscar_empleados_tfijos($datos);



        $existe = '';



        if (count($datos) > 1 ) {

            $existe = 'S';

        }else{

            $existe = 'N';

        }



        echo $existe;        



    }



    public function tipo_documento()

    {

        $this->load->library('Forms/Ftipodocumento');

        $form_tipodocumento = new Ftipodocumento();

        $this->datos['tipodocumento'] = $this->Catalogos_Model->buscar_tipo_documento('',$_SESSION["id_empresa"]);

        $this->datos['empresas'] = $this->Catalogos_Model->buscar_empresa($_SESSION["id_empresa"]);

        $this->load->view("catalogos/tipo_documento", array_merge($this->datos, $form_tipodocumento->get_formulario()));

    }



    public function crear_tipo_documento()

    {



        $datos = array(

            'nombre'     => $_POST["nombre"], 

            'privado'    => $_POST["cb"],

            'id_empresa' => $_POST["empresas"]

        );

        $this->Catalogos_Model->mantenimiento_tipo_documento($datos);

        $this->datos['tipodocumento'] = $this->Catalogos_Model->buscar_tipo_documento('',$_SESSION["id_empresa"]);

        $this->load->view("catalogos/tipo_documento", $this->datos);

    }



    public function editar_tipo_documento()

    {

         $datos = array(

            'id'         => $_POST["id_tipo_documento".$_POST["id_Reg"]],

            'nombre'     => $_POST["nombre".$_POST["id_Reg"]], 

            'privado'    => $_POST["cb"],

            'id_empresa' => $_POST["empresas"]

        );



        $this->Catalogos_Model->mantenimiento_tipo_documento($datos); 

    }



    public function consulta_tipo_documento($id)

    {

        //$datos viene en un arreglo

        $datos = $this->Catalogos_Model->buscar_tipo_documento($id,$_SESSION["id_empresa"]);



         enviarJson(array(

            'id'       => $datos[0]->id,  

			'nombre'   => $datos[0]->nombre,

			'privado'  => $datos[0]->privado

        )); 

    }

    

    public function tipo_doc_cliente()

    {

        $this->load->library('Forms/Ftipodoccliente');

        $form_tipodocumento = new Ftipodoccliente();

        $this->datos['tipodocumento'] = $this->Catalogos_Model->buscar_tipo_doc_cliente('',$_SESSION["id_empresa"]);

        $this->datos['empresas'] = $this->Catalogos_Model->buscar_empresa($_SESSION["id_empresa"]);

        $this->load->view("catalogos/tipo_doc_cliente", array_merge($this->datos, $form_tipodocumento->get_formulario()));

    }



    public function crear_tipo_doc_cliente()

    {



        $datos = array(

            'nombre'     => $_POST["nombre"], 

            'privado'    => $_POST["cb"],

            'id_empresa' => $_POST["empresas"]

        );

        $this->Catalogos_Model->mantenimiento_tipo_doc_cliente($datos);

        $this->datos['tipodocumento'] = $this->Catalogos_Model->buscar_tipo_doc_cliente('',$_SESSION["id_empresa"]);

        $this->load->view("catalogos/tipo_doc_cliente", $this->datos);

    }



    public function editar_tipo_doc_cliente()

    {

         $datos = array(

            'id'         => $_POST["id_tipo_doc_cliente".$_POST["id_Reg"]],

            'nombre'     => $_POST["nombre".$_POST["id_Reg"]], 

            'privado'    => $_POST["cb"],

            'id_empresa' => $_POST["empresas"]

        );



        $this->Catalogos_Model->mantenimiento_tipo_doc_cliente($datos); 

    }



    public function consulta_tipo_doc_cliente($id)

    {

        //$datos viene en un arreglo

        $datos = $this->Catalogos_Model->buscar_tipo_doc_cliente($id,$_SESSION["id_empresa"]);



         enviarJson(array(

            'id'       => $datos[0]->id,  

			'nombre'   => $datos[0]->nombre,

			'privado'  => $datos[0]->privado

        )); 

    }

    





}

