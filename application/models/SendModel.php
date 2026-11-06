<?php
 
 defined('BASEPATH') or exit('No direct script access allowed');
 
 class SendModel extends CI_Model
 {
    public function sendmail($body,$correo, $warehouse)
    {
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
        $correo='desarrollosv3@mistarship.com';
        //$correo='juanobisp@gmail.com';

        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->from('elmer.guardado@gmail.com', 'Starship Shopping');
        $this->email->to($correo);
        // $this->email->to('mavolevan@mistarship.com');
        $this->email->subject('Estatus'.$$warehouse);
        $filename = 'public/imagenes/logo.png';
        $this->email->attach($filename);
        $cid = $this->email->attachment_cid($filename);
        //   $this->email->message('<img src="cid:'. $cid .'" alt="photo1" />');
        $b =rawurldecode($body);
        $this->email->message('
                <img src="<?php echo base_url(); ?>public/assets/img/header.png" style="width:100%">
    <div style="padding: 0 1rem 0 1rem">
    <br>

    <p style="font-size: 2vw"><span style="color: #878481">Star</span><span style="color: #ff5a21">Ship</span>te informa</p>
    <br>
    <?php   if (isset($item)) { ?>
    <ul style="font-size: 2vw;list-style: none;">
        <li style="margin: 0.5rem 0rem">
            Casillero : <span style="color: #ff5a21"></span></li>
        <li style="margin: 0.5rem 0rem">
        Estado de su paquete: <span style="color: #ff5a21"></span>
        </li>
        <li style="margin: 0.5rem 0rem">
            Date: <span style="color: #ff5a21"></span>
        </li>
        <li style="margin: 0.5rem 0rem">
            Consignatario:  <span style="color: #ff5a21"></span>
        </li>
         <li style="margin: 0.5rem 0rem">
           Warehouse: <span style="color: #ff5a21"></span>
        </li>
        <li style="margin: 0.5rem 0rem">
                    Descripción: <span style="color: #ff5a21"></span>
                </li>
        
         <li style="margin: 0.5rem 0rem">
           Peso : <span style="color: #ff5a21"></span>
        </li>
    
       
        <li style="margin: 0.5rem 0rem">
           Total a Pagar:    <span style="color: #ff5a21"></span>
        </li>
       
    </ul>
    <br>

  <div class="container">
            <div class="row" style="text-align: center;display: block;">
                    <p style="font-size: 2vw;text-align: center;">Para coordinar entrega en la sucursal de c807 Xpress o a tu domicilio sin costo adicional, solicitamos de tu confirmación de pago, el cual puedes realizarlo en transferencia bancaria a </p>
                    <p style="font-size: 2vw;text-align: center;">Cuenta Corriente a nombre de: <br> Star Mail S.A. de C.V. <br> Cuscatlan 010301000053285 <br>  Banco Agricola: 511-001119-1</p>
                    <p style="font-size: 2vw;text-align: center;">o link de pago en tarjeta de crédito o débito. <br> Solicítalo directamente a nuestro whatsapp +503 7256 1580</p>
                <?php } else { ?>
                    <p style="font-size: 2vw;text-align: center;">Puedes solicitar la entrega a domicilio de tu paquete totalmente gratis, contáctanos a nuestro whatsapp para ayudarte.</p>
                    <p style="font-size: 2vw;text-align: center;">Confirma tu entrega a domicilio Paga aquí. </p>
                <?php  } ?>
                <a href="https://api.whatsapp.com/send/?phone=50372561580&text&app_absent=0" style="text-decoration: blink;
                color: black;
                display: inline-block;
                font-weight: bolder;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                border: 1px solid transparent;
                padding: .375rem .75rem;
                font-size: 1.5rem;
                line-height: 1.5;
                border-radius: .25rem;
                transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
                " target="_blank">
                <img src="<?php echo base_url(); ?>public/assets/img/what.png" style="width:15%"> 
               </a>
            </div>
        </div>
        <br>
        <p style="font-size: 2vw;text-align: center;">¡Gracias por confiar en nosotros!</p>
    
    </div>
    <img src="<?php echo base_url(); ?>public/assets/img/footer.png" style="width:100%">');

        if ($this->email->send()) {
            //echo 'Your email was sent.';
        } else {
            echo $this->email->print_debugger();
        }
    }


 }