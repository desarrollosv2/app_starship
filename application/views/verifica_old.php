<?php 


if (isset($_COOKIE["acceso"]))
{
	
	if($_COOKIE['acceso'] != "logueado")
	{
   
       echo '<script>salir()</script>';
	}else{
		
	}
}
else
{
	
   echo '<script>salir()</script>';
}
//Comprobamos si esta definida la sesión 'tiempo'.
if(isset($_SESSION['tiempo']) ) {

    //Tiempo en segundos para dar vida a la sesión.
    $inactivo = 600;//20min en este caso.

    //Calculamos tiempo de vida inactivo.
    $vida_session = time() - $_SESSION['tiempo'];
   // echo $vida_session."-". $_SESSION['tiempo'];
        //Compraración para redirigir página, si la vida de sesión sea mayor a el tiempo insertado en inactivo.
        if($vida_session > $inactivo)
        {
            //Removemos sesión.
            session_unset();
            //Destruimos sesión.
            session_destroy();              
            //Redirigimos pagina.
           echo '<script>salir()</script>';

            exit();
        } else {  // si no ha caducado la sesion, actualizamos
            $_SESSION['tiempo'] = time();
        }


} else {
    //Activamos sesion tiempo.
    $_SESSION['tiempo'] = time();
}



if (isset($_COOKIE["acceso"]))
{
	
	if($_COOKIE['acceso'] != "logueado")
	{
   
    //    echo '<script>salir()</script>';
	}else{
		
	}
}
else
{
	
  // echo '<script>salir()</script>';
}

?>
