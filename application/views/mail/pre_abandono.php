<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACION</title>
</head>
<body>
    <img src="<?php echo base_url(); ?>public/assets/img/header.png" style="width:100%">
    <div style="padding: 0 1rem 0 1rem">
   

    <p style="font-size: 2vw"><span style="color: #878481">Star</span><span style="color: #ff5a21">Ship</span>
    te informa</p>
    <br>
    <?php   if (isset($item)) { ?>
    <ul style="font-size: 1vw;list-style: none;">
        
        <li style="margin: 0.5rem 0rem">
            Estimado(a):  <?php echo " ". $item['nombrec'];?>
        </li>
         <li style="margin: 0.5rem 0rem">
           El motivo de nuestro correo es para informarle que su paquete: <?php echo " ". $item['warehouse']. " ";?> 
           con descripción  <?php echo " ". $item['descripcion']. ", ";?> está listo para ser retirado en la sucursal seleccionada por usted desde hace más de 90 días, es decir ya se encuentra en el estado de Abandono, sin embargo queremos hacerle la atenta invitación para acercarse a retirarlo o programar su entrega a domicilio al whatsapp 72561580 o llamarnos al número 25303030.
        <span></span>
        </li>
        
        <li style="margin: 0.5rem 0rem">
           El total a cancelar es de:     <?php echo " $". $item['total'];?>
        </li>
         <li style="margin: 0.5rem 0rem">
           También disponemos de diferentes formas de pago remota para su conveniencia.
        </li>
        <li style="margin: 0.5rem 0rem">
           Nota importante: de no tener respuesta en los próximos 15 días, su paquete estará siendo catalogado como abandono definitivo sin oportunidad de recuperarlo.
        </li>
         <li style="margin: 0.5rem 0rem">
          Agradecemos de antemano su comprensión.
        </li>
    </ul>
    <br>
<?php  } ?>

 
     <div class='container'>
            <div class="row" style="text-align: center;display: block;">
                <p style="font-size: 2vw;text-align: center;">Puedes solicitar la entrega a domicilio de tu paquete totalmente gratis, contáctanos a nuestro whatsapp para ayudarte.</p>
                
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
                " target="_blank"><i class="fa fa-whatsapp" style="font-size:48px;color:Green"></i>
                <img src="<?php echo base_url(); ?>public/assets/img/what.png" style="width:15%"> 
               </a>
            </div>
        </div>
        <br>
         <p style="font-size: 2vw;text-align: center;">¡Gracias por confiar en nosotros!</p>
    
    
    </div>
    <img src="<?php echo base_url(); ?>public/assets/img/footer.png" style="width:100%">
</body>
</html>