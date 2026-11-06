<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Casillero Starship Creado</title>
</head>

<body>
    <img src="<?php echo base_url(); ?>public/assets/img/header.png" style="width:100%">
    <div style="padding: 0 1rem 0 1rem">
        <!--p style="font-size: 2vw">ESTIMADO 
        <span style="color: #ff5a21">
        JOSE MIGUEL</span>  
        <span style="color: #878481">
        AVOLEVAN SACA
        </span>
    </p--><br>

        <p style="font-size: 1.5vw"><span style="color: #878481">Star</span><span style="color: #ff5a21">Ship</span>
            te informa</p>

        <br>

        <?php if (isset($item)) { ?>

            <ul style="font-size: 1.5vw;list-style: none;">
                <li style="margin: 0.5rem 0rem ;color: #ff5a21">
                    ¡Ya recibimos tu paquete en nuestra bodega!
                </li>
                <li style="margin: 0.5rem 0rem;">
                    Casillero : <span style=""><?php echo $item['casillero']; ?></span></li>
                <li style="margin: 0.5rem 0rem">
                 Estado de su paquete:<span style="color: #ff5a21"><?php echo $item['estatus']; ?></span>
                </li>
                <li style="margin: 0.5rem 0rem">
                    Date: <span style=""><?php echo $item['date']; ?></span>
                </li>
                <li style="margin: 0.5rem 0rem">
                    Consignatario: <span style=""><?php echo $item['nombrec']; ?></span>
                </li>

                <li style="margin: 0.5rem 0rem">
                    Remitente: <span style=""><?php echo $item['remitente']; ?></span>
                </li>
                <li style="margin: 0.5rem 0rem">
                    Warehouse: <span style=""><?php echo $item['warehouse']; ?></span>
                </li>
                <li style="margin: 0.5rem 0rem">
                    Descripción: <span style=""><?php echo $item['descripcion']; ?></span>
                </li>
                <li style="margin: 0.5rem 0rem">
                    Tracking : <span style=""><?php echo $item['tracking']; ?></span>
                </li>
                <li style="margin: 0.5rem 0rem">
                    Peso : <span style=""><?php echo $item['peso']; ?></span>
                </li>
                <li style="margin: 0.5rem 0rem">
                    Bulto : <span style=""><?php echo $item['bultos']; ?></span>
                </li>
               
            </ul>
            <br>
        <?php  } ?>

        <?php if ($item['banner'] == 1) { ?>

            <div class='container'>
            <div class="row text-center" style="text-align: center;">
                <p style="font-size: 1.5vw;text-align: center;">Si prealertaste tu paquete será asignado a próximo vuelo <strong style="color: #ff5a21">automáticamente, caso contrario</strong> puedes adjuntar tu factura respondiendo este correo, o en el siguiente enlace:</p>

                <a href="https://mistarship.com/starship/" style="text-decoration: blink;text-align: center;
    background-color: #FF9900;
    color: white;
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
                    Adjunta tu factura aquí
                </a>
            
    </div>
    </div>


<?php

        } ?>

<br>
<p style="font-size: 1.5vw;text-align: left;">¡Gracias por tu confianza!</p>

<p style="font-size: 1.5vw;text-align: left;">Atentamente</p>

<p style="font-size: 1.5vw;text-align: left;">StarShip El Salvador</p>



<img src="<?php echo base_url(); ?>public/assets/img/footer.png" style="width:100%">
</body>

</html>