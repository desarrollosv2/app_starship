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
            Estatus Actual es: <span style="color: #ff5a21"><?php echo $item['estatus'];?></span>
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
           Tracking : <span style="color: #ff5a21"><?php echo $item['tracking'];?></span>
        </li>
         <li style="margin: 0.5rem 0rem">
           Peso : <span style="color: #ff5a21"><?php echo $item['peso'];?></span>
        </li>
        <li style="margin: 0.5rem 0rem">
           Bulto :  <span style="color: #ff5a21"><?php echo $item['bultos'];?></span>
        </li>
        <li style="margin: 0.5rem 0rem">
           Total a Pagar:    <span style="color: #ff5a21"><?php echo $item['total'];?></span>
        </li>
    </ul>
    <br>
<?php  } ?>

  <?php  /* if(isset($item2)) {
    echo 
  }*/
    ?>

   <div class='container'>
    
       <div class="row text-center" style="text-align: center;">
           <img  src="<?php echo base_url(); ?>public/assets/img/pay.jpg" style="width:100%">
           <br><br><br><br>
<a href="https://mistarship.com/afiliacion/" style="text-decoration: blink;
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
Afilia tu tarjeta aquí  
           </a>
       </div>
   </div>
   
    <br>
    <p style="font-size: 2vw;text-align: center;">
    ¡Gracias por confiar en nosotros!</p>
    </div>
    <img src="<?php echo base_url(); ?>public/assets/img/footer.png" style="width:100%">
</body>
</html>