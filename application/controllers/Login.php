<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Login_Model');
    }


    public function index()
    {
        $this->load->library('Forms/Flogin');

        $form_login = new Flogin();

        $form_login->set_accion("index.php/login/validar_Login");

        $this->load->view('login', $form_login->get_formulario());
    }

    public function validar_Login()
    {
        if ($this->input->post()) {
            if ($this->form_validation->run('login')) {
                $datosparm = array(

                    'correo' => $this->input->post('correo', true),

                    'clave' => $this->input->post('clave', true),

                );

                $data = $this->Usuario_Model->getLogin($datosparm);

                //var_dump($data);

                if ($data && $data->id_estatus == 1) {

                    $datos = array(
                        "user_id" => $data->id,

                        "nombre"  => $data->nombre,

                        "login"   => true
                    );

                    $time = 27000;
                    $_SESSION["tiempo"] = time();

                    $cookie = array(
                        'name'   => 'acceso',
                        'value'  => 'logueado',
                        'expire' =>  $time,
                        'secure' => false
                    );

                    $this->input->set_cookie($cookie);

                    $_SESSION["user_id"]        = $data->id;

                    $_SESSION["nombre"]         = $data->nombre;

                    $_SESSION["login"]          = true;

                    $_SESSION["id_empresa"]     = $data->id_empresa;

                    $_SESSION["nombre_empresa"] = $data->nombre_empresa;

                    $_SESSION["ver_privado"]    = $data->ver_doc_privado;

                    $_SESSION["casillero"]      = $data->casillero;

                    $_SESSION["interno"]        = $data->interno;

                    $_SESSION["pais"]           = $data->id_pais;

                    $_SESSION["token"]           = $data->token;

                    $_SESSION["token_cliente"]           = $data->token_cliente;

                    $_SESSION["id_tokencliente"]           = $data->id_tokencliente;

                    $_SESSION["client_code"] = $data->client_code;

                    

                    if ($data->interno == 0) {
                        $dato_Menu = $this->Usuario_Model->get_Parent_Menu(2);

                        $this->dato['menu'] = $this->crear_Menu($dato_Menu, 2);
                    } else {
                        $dato_Menu = $this->Usuario_Model->get_Parent_Menu($data->id);
                        //var_dump($dato_Menu);
                        //die();
                        $this->dato['menu'] = $this->crear_Menu($dato_Menu, $data->id);
                    }


                    $_SESSION["menu"] = $this->dato;

                    $this->load->view('principal', $this->dato);
                } else {
                    $this->load->view('cerrar.html');
                }
            } else {
                $this->load->view('cerrar.html');
            }
        } else {
            redirect("login");
        }
    }



    public function crear_Menu($datos, $id_Usuario)
    {
        $menu_Devolver = "";

        foreach ($datos as $dato) {
            // Depurar antes de procesar
            //  print_r($dato); // Asegúrate de que cada dato tiene las propiedades correctas
            $menu_Devolver .= $this->crear_Opcion_Padre($dato, $id_Usuario);
        }

        //echo $menu_Devolver; // Confirma que el menú completo se está generando
        return $menu_Devolver;
    }


    private function crear_Opcion_Padre($dato, $id_Usuario)
    {
        $icono = $this->obtener_Icono($dato->title); // Asigna el ícono correspondiente

        $menu_Opcion_Padre = "<li class='nav-item has-treeview menu-close'>";
        $menu_Opcion_Padre .= "<a href='#' class='nav-link active'>";
        $menu_Opcion_Padre .= "<i class='nav-icon $icono'></i>"; // Usa el ícono dinámico
        $menu_Opcion_Padre .= "<p>{$dato->title}<i class='right fa fa-angle-left'></i></p>";
        $menu_Opcion_Padre .= "</a>";

        // Obtener los hijos del menú
        $dato_Menu_Hijo = $this->Usuario_Model->get_Hijos($id_Usuario, $dato->id);

        if (!empty($dato_Menu_Hijo)) {
            $menu_Opcion_Padre .= "<ul class='nav nav-treeview'>";
            foreach ($dato_Menu_Hijo as $hijo) {
                $menu_Opcion_Padre .= $this->crear_Opcion_Hijo($hijo);
            }
            $menu_Opcion_Padre .= "</ul>";
        }

        $menu_Opcion_Padre .= "</li>";

        return $menu_Opcion_Padre;
    }



    private function crear_Opcion_Hijo($hijo)
    {
        $icono = $this->obtener_Icono($hijo->title); // Asigna el ícono dinámico

        $menu_Opcion_Hijo = "<li class='nav-item'>";
        $menu_Opcion_Hijo .= "<a href='javascript:;' class='nav-link' onclick='{$hijo->url}()'>";
        $menu_Opcion_Hijo .= "<i class='$icono nav-icon'></i>"; // Usa el ícono dinámico
        $menu_Opcion_Hijo .= "<p>{$hijo->title}</p>";
        $menu_Opcion_Hijo .= "</a>";
        $menu_Opcion_Hijo .= "</li>";

        return $menu_Opcion_Hijo;
    }


    private function obtener_Icono($nombre)
    {
        $icono = '';
        switch ($nombre) {
            case 'Clientes':
                $icono = 'fas fa-users';
                break;
            case 'Recepción Aeropuerto':
                $icono = 'fal fa-plane';
                break;
            case 'AWB':
                $icono = 'fas fa-clipboard-list';
                break;
            case 'Seguimiento':
                $icono = 'fas fa-box';
                break;
            case 'Aduana':
                $icono = 'fas fa-truck';
                break;
            case 'Consultas':
                $icono = 'fas fa-search';
                break;
            case 'Mantenimiento':
                $icono = 'fas fa-cog';
                break;

            case 'Lista AWB':
                $icono = 'fas fa-clipboard-list';
                break;

            case 'Manifiestos':
                $icono = 'fas fa-file-invoice';
                break;

            case 'Ver Estatus':
                $icono = 'fas fa-eye';
                break;

            case 'Cambiar Estatus':
                $icono = 'fas fa-exchange-alt';
                break;

            case 'Traspaso':
                $icono = ' fas fa-sync';
                break;

            case 'Verificar WH':
                $icono = 'fas fa-check-circle';
                break;

            case 'Archivo Facturación':
                $icono = 'fas fa-file-invoice-dollar';
                break;

            case 'Cargar Archivo':
                $icono = 'fas fa-upload';
                break;

            case 'Manifiesto LMD':
                $icono = 'fas fa-file-invoice';
                break;

            case 'Consulta DTE':
                $icono = 'fas fa-file-invoice';
                break;

            case 'Consulta Tracking':
                $icono = 'fas fa-search-location';
                break;

                
            case 'Estatus por referencia':
                $icono = 'fas fa-clipboard-list';
                break;





                     
            case 'Pendientes de confirmar':
                $icono = 'ffas fa-clock';
                break;

                 
            case 'Confirmados':
                    $icono = 'as fa-check-circle';
                    break;

                    
                         
            case 'Aplicar Estatus':
                $icono = 'fas fa-sync-alt';
                break;


                     
            case 'Comparativo de Guías':
                $icono = 'fas fa-balance-scale';
                break;


                     
            case 'Reporte de Tracking Replace':
                $icono = 'fas fa-file-alt';
                break;


                     
            case 'Envio Tracking':
                $icono = 'fas fa-paper-plane';
                break;





                           
            case 'Partidas Arancelarias':
                $icono = 'fas fa-list';
                break;

                           
            case 'Lista Aduana':
                $icono = 'fas fa-list-alt';
                break;

                           
            case 'Manifiesto':
                $icono = 'fas fa-file-invoice';
                break;

                           
            case 'Declaraciones':
                $icono = 'fas fa-file-text';
                break;

                           
            case 'Clasificación':
                $icono = 'fas fa-tags';
                break;



            default:
                $icono = 'fas fa-circle'; // Ícono predeterminado
                break;
        }
        // echo "Asignando ícono '$icono' para '$nombre'<br>";
        return $icono;
    }


    public function cerrar()
    {
        session_unset();
    }

    public function principal()
    {
        $this->load->view('principal');
    }

    public function credencial($correo = "")
    {
        $datos['guias2'] = $this->Login_Model->consultar_usu($correo);
        $data = array();
        foreach ($datos['guias2'] as $fila) {
            $clv = $fila->clave;
            $id = $fila->id;
            $email = $fila->correo;
        }
        if ($clv != "") {
            $data = array(
                'valido' => '<script type="text/javascript">window.location="http://localhost/app_starship/";</script>',
            );
            /* $data = array(
                    'valido' =>'ok',
                );*/
        } else {
            $data = array(
                'data2' => $id,
                'data3' => $email,
            );
        }

        $this->load->view('ps_login/log_in', $data);
    }
}
