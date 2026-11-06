<?php


    if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])) {
        
    }else{
        echo '<script>salir()</script>';
    }