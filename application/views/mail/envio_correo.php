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

    <p style="font-size: 2vw"><span style="color: #878481">Star</span><span style="color: #ff5a21">Ship</span>
    te informa</p>
    <br>
    <?php   if (isset($item)) { ?>
    <ul style="font-size: 2vw;list-style: none;">
        <li style="margin: 0.5rem 0rem">
            Casillero : <span style="color: #ff5a21"><?php echo $item['casillero'];?></span></li>
        <li style="margin: 0.5rem 0rem">
        Estado de su paquete: <span style="color: #ff5a21"><?php echo utf8_decode($item['estatus']);?></span>
        </li>
        <li style="margin: 0.5rem 0rem">
            Date: <span style="color: #ff5a21"><?php echo $item['date'];?></span>
        </li>
        <li style="margin: 0.5rem 0rem">
            Consignatario:  <span style="color: #ff5a21"><?php echo $item['nombrec'];?></span>
        </li>
         <li style="margin: 0.5rem 0rem">
           Warehouse: <span style="color: #ff5a21"><?php echo $item['warehouse'];?></span>
        </li>
        <li style="margin: 0.5rem 0rem">
                    Descripción: <span style="color: #ff5a21"><?php echo utf8_decode($item['descripcion']); ?></span>
                </li>
        <li style="margin: 0.5rem 0rem">
           Tracking : <span style="color: #ff5a21"><?php echo $item['tracking'];?></span>
        </li>
         <li style="margin: 0.5rem 0rem">
           Peso : <span style="color: #ff5a21"><?php echo $item['peso'];?></span>
        </li>
        <li style="margin: 0.5rem 0rem">
           Bulto :  <span style="color: #ff5a21"><?php echo $item['bultos'];?></span>
        </li>
        <?php   if (isset($item['total'])) { ?>
        <li style="margin: 0.5rem 0rem">
           Total a Pagar:    <span style="color: #ff5a21"><?php echo $item['total'];?></span>
        </li>
        <?php  } ?>
    </ul>
    <br>
<?php  } ?>

  <div class='container'>
            <div class="row" style="text-align: center;display: block;">
                <?php //echo $item['idestatus'];?>
                <?php  if ($item['idestatus'] == '251') { ?>
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
    <img src="<?php echo base_url(); ?>public/assets/img/footer.png" style="width:100%">
</body>
</html>