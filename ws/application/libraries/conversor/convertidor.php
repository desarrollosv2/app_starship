<?php
class Convertidor{
   function convertirnumeroletra($cantidad){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 
        //echo  'DENTRO DE LA FUNCION  DE CONVERSION ';   
            $valor = $cantidad;
            //echo  'a  convertir valores';
                if ($valor <= 999.99) {
                    $valor = number_format($valor, 2);
                    $formatterES = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
                    $n = number_format($valor, 2);
                    $Dolares = intval(floor($n));
                    $Centavos = intval(($n - floor(number_format($n, 2))) * 100);
                    $Centavos = substr($valor, -2, 2);
                    //echo $valor . '/';
                    return  strtoupper($formatterES->format($Dolares)) . ' DOLARES' . " CON " . strtoupper($formatterES->format($Centavos)) . ' CENTAVOS';
                } else {
                    $formatterES = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
                    $n = $valor;
                    $valor = number_format($valor, 2);
                    $Dolares = intval(floor($n));
                    $Centavos = intval(($n - floor($n)) * 100);
                    $Centavos = substr($valor, -2, 2);
                    //echo $valor . '/';
                    return   strtoupper($formatterES->format($Dolares)) . ' DOLARES' . " CON " . strtoupper($formatterES->format($Centavos)) . ' CENTAVOS';
                }

   } 
}  
?>