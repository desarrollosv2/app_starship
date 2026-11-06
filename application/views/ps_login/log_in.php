<!doctype html>

<html>

<head>

    <title>StarShip </title>
    <script type="text/javascript" src="<?php echo base_url('public/js/jquery-2.2.3.min.js')?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/bootstrap/css/bootstrap.min.css')?>">
    <script type="text/javascript" src="<?php echo base_url('public/bootstrap/js/bootstrap.min.js')?>"></script>

    <script type="text/javascript" src="<?php echo base_url('public/js/customer.js')?>"></script>

    <style>
    body {
        background-color: #4B515D;
        background: #F15a29;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        -webkit-transform: translate(-50%, -50%);
    }

    .container {
        background-color: #fff;
        /*  box-shadow: 3px 3px 10px #2E2E2E;*/
       /* height: 400px;
        width: 500px;*/
        /* background: #F15a29;*/
        border-radius: 20px;
    }

   
    .card-title {
        color: white;
    }

    .card-header {
        /* background: #F15a29;*/
        background: #FFF;
    }

    .card-body {
      /*  height: 225px;*/
       /* padding-top: 5px;*/
    }

    .card-footer {
        height: 50px;
        width: 200px;
        margin-left: auto;
        padding-top: 20px;

    }

    .btn-info {
        background-color: #0099CC !important;
    }

    .btn-info:hover {
        background-color: #33b5e5 !important;
    }

    .btn-primary {
        background-color: #ffae00 !important;
        color: white;
        float: left;
        border-color: white;
        width: 100%;
        border-radius: 50px;
        padding: 10px;
        border: 2px solid #FFF;
        
    }

    .btn-primary:hover {
        border: 1px solid #F15a29;
    }
    .texto {
        color: #FFF;
    }

    input.form-control {
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        border-radius: 50px;
        border: 2px solid #000000;
        padding: 10px;
        width: 100%;
        border-color: #FFF;
        border-color: #edc204;
        border-color: #F15a29;
    }

    input:hover {
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        border-radius: 50px;
        border: 2px solid #000000;
        padding: 10px;
        width: 100%;
        border-color: #edc204;
        border-color: #ffae00; 
    }

    input:focus {
        outline: none;
    }
    .center {
        margin-left: auto;
        margin-right: auto;
        display: block;
    }
    </style>
    <!-- JavaScript -->
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <!-- Default theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
    <!-- Semantic UI theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css"/>
    <!-- Bootstrap theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body>
    <div class="container row col-12 mx-auto" id="contenedor">
        <?php  echo $valido;?>
        <div class="card-header card border-0">
            <div class="row center">
                <h3 class="text-center">BIENVENIDO a </h3>
                <img src="<?php echo base_url();?>public/dist/img/AdminLTELogo.png" alt="starship Logo"
                style="opacity:.8">
            </div>
        </div>
        <div class="card-body">
            <form  class="form_cambiar_pass2" name="form_cambiar_pass2" id="form_cambiar_pass2">
                <input type="hidden" name="user_id" id="user_id" value="<?php echo $data2; ?>">
                <input type="hidden" name="user_email" id="user_email" value="<?php echo $data3; ?>">

                <div class="form-group">
                    <label for="email">Introduzca nueva clave:</label>
                    <input type="password" class="form-control" name="password" id="password">
                </div>
                <div class="form-group">
                    <label for="pwd">Repita Clave:</label>
                    <input type="password" class="form-control" id="password_repeat" name="password_repeat">
                </div>
                <button type="button" class="btn btn-primary" onclick="guardar_password2()">Aceptar</button>
            </form>
        </div>
    </body>

</html>

