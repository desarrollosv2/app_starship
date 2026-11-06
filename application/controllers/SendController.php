<?php
    
    defined('BASEPATH') or exit('No direct script access allowed');
    
    class SendController extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->database();
             $this->load->model('WarehouseModel');
           //  $this->load->model('Customers_model');
            $this->datos = array();
        }
        
        public function index()
        {
        }

        public function test_mail(){

            /*$ws= $this->WarehouseModel->buscar_cliente($csll);
            $nombre="Consignee: ".$ws->nombres." ".$ws->apellidos;
            $correo=$ws->correo;
            */

            //$path = set_realpath('application');
            //echo $path;
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
            $this->load->library('email');

            $config = array(
                'protocol' => 'mail',
                'smtp_host' => 'smtp.office365.com',
                'smtp_port' => 587,
                'smtp_user' => 'info@mistarship.com',
                'smtp_pass' => '*A0t]#kj3Vs0',
                'mailtype' => 'html',
                'charset' => 'utf-8',
                
                 );
              //   oscaranibal988@hgmail.com
            // $correo='desarrollosv@c807.com';
            $correo='majrpor010@gmail.com';
            // $correo='juan.carlosvigil@gmail.com';
            // $correo='mavolevan@mistarship.com';

            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $this->email->from('info@mistarship.com', 'Starship');
            $this->email->to($correo);
            // $this->email->to('mavolevan@mistarship.com');
            $this->email->subject('Estatus');
            $filename = 'public/imagenes/logo.png';
            $this->email->attach($filename);
            $cid = $this->email->attachment_cid($filename);
            //   $this->email->message('<img src="cid:'. $cid .'" alt="photo1" />');
            // $b =rawurldecode($body);
            $this->email->message('<h2>CORREO DE PRUEBA 24</h2>');
    
            if ($this->email->send()) {
               echo 'Your email was sent.';
              
            } else {
                echo $this->email->print_debugger();
            }
            
}
        public function sendmail($body,$mail)
        {
            /*$ws= $this->WarehouseModel->buscar_cliente($csll);
            $nombre="Consignee: ".$ws->nombres." ".$ws->apellidos;
            $correo=$ws->correo;
            */

            //$path = set_realpath('application');
            //echo $path;
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
            $this->load->library('email');

            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.gmail.com',
                'smtp_port' => 465,
                'smtp_user' => 'desarrollo@mistarship.com',
                'smtp_pass' => 'jJ2020$.005',
                'mailtype' => 'html',
                'charset' => 'utf-8',
                
                 );
                 
            //$correo='desarrollosv@c807.com';
            $correo='desarrollo@mistarship.com';

            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $this->email->from('elmer.guardado@gmail.com', 'Starship Shopping');
            $this->email->to($correo);
            // $this->email->to('mavolevan@mistarship.com');
            $this->email->subject('Estatus');
            $filename = 'public/imagenes/logo.png';
            $this->email->attach($filename);
            $cid = $this->email->attachment_cid($filename);
            //   $this->email->message('<img src="cid:'. $cid .'" alt="photo1" />');
            $b =rawurldecode($body);
            $this->email->message('
                <img src="cid:'. $cid .'" alt="photo1"/>
                <br></br>
                 '.$b.'
                <br></br>   
                <h2>CORREO DE PRUEBA 14</h2>');
    
            if ($this->email->send()) {
                echo 'Your email was sent.';
            } else {
                echo $this->email->print_debugger();
            }
        }

        public function tracking()
        {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL); 
            
            $datos = $this->WarehouseModel->get_tracking();
             
            $i = 0;
            foreach ($datos as $item) 
            {
            $correo = 'desarrollosv3@mistarship.com'; 
            $msj    = '<!DOCTYPE html>
                           <html lang="en">
                           <head>
                           <meta charset="UTF-8">
                           <meta name="viewport" content="width=device-width, initial-scale=1.0">
                           <title>Mensaje de Confirmacion</title>
                           </head>
                           <body>
                           <p>Estimado '.$item->consignee.'</p>
                           <p>Te notificamos que hemos recibido un paquete de una compra procedente de <span style="font-weight: bold;">AliExpress</span>.</p>
                           <p style="font-weight: bold;font-style: italic;margin-left: 2rem;">Tracking number: '.$item->tracking_number.'</p>
                           <p style="font-weight: bold;font-style: italic;margin-left: 2rem;">Descripción de la compra: '.$item->commodity.'</p>
                           <p>*Puedes rastrear tu envío desde AliExpress o en el siguiente enlace  <a href="https://mailamericas.com/">https://mailamericas.com/</a> usando tu  '.$item->tracking_number.'</p>
                           <p>Para completar el proceso de la liberación de tu(s) compra(s) necesitamos que nos ayudes llenando el <a href="https://mistarship.com/formulario-starship/"> formulario </a> para efectos del trámite aduanal y poder aplicar al beneficio de la Ley de “Facilitación de Compras en Línea” que por compras personales menores a $300.00 no pagas DAI (Derecho Arancelario a la Importación) ni permisos especiales*.</p>
                           <p>Una vez liberado, puedes recogerlo en cualquiera de nuestras oficinas o para tu comodidad, enviártelo hasta tu domicilio o lugar de trabajo.</p>
                           <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;"><a href="https://mistarship.com/formulario-starship/">LLENAR FORMULARIO AQUI</a></span> </p>
                           <!--p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Nuestras Agencias:</span> <a href="https://mistarship.com/agencias/">mistarship.com/contacto/</a></p-->
                           <br>
                           <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Web:</span> <a href="https://mistarship.com">mistarship.com</a></p>
                           <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">PBX:</span> (503) 2537-7200</p>
                           <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">WhatsApp:</span> (503) 7150-9535 o al (503) 6034-6037</p>
                           <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Facebook:</span> /StarshipSV</p>
                           <br>
                           <a href="https://mistarship.com/formulario-starship/" style="text-decoration: dashed;background-color: #ff6a40;width: 260px;color: #ffffff !important;margin-top: 10px;border: 1px solid transparent;border-radius: 4px;cursor: pointer;display: inline-block;font-size: 16px;font-weight: 400;line-height: 1.5;padding: 6px 12px;position: relative;text-align: center;transition: background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;-webkit-user-select: none;-moz-user-select: none;user-select: none;vertical-align: middle;white-space: nowrap;display:none;">Llenar formulario</a>
                           </body>
                           </html>';
                    
            echo $msj."<br>";
            echo $msj."<br>"; 
             
            $this->load->library('email');
            $config = array(
                            'protocol'  => 'smtp',
                'smtp_host' => 'localhost',
                'smtp_port' => 25,
                'smtp_user' => 'adminserver@stargates.site',
                'smtp_pass' => 'adminserver2022',
                'charset'   => 'utf-8',
                'mailtype' => 'html',
                        );
                                    
            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $this->email->from('notificaciones@mistarship.com', 'StarShip');
            $this->email->to($correo);
            //$this->email->cc('juanobisp@gmail.com');
            $this->email->subject('Prueba Tracking');
            $this->email->message($msj);
                
            if ($this->email->send()) 
            {
                //$r=$this->Customers_model->data_delete($correo);
                echo 'Enviado';
            } 
            else 
            {
               echo $correo." - NO ENVIADO<br>";
            }
                
            $i++;    
            }
        }        
    }

?>        