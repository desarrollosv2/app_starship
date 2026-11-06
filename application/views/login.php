<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StarShip ERP</title>

    <script src="<?php echo base_url('public/js/jquery-2.2.3.min.js') ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/bootstrap/css/bootstrap.min.css') ?>">
    <script src="<?php echo base_url('public/bootstrap/js/bootstrap.min.js') ?>"></script>

    <script src="<?php echo base_url('public/js/funciones.js') ?>"></script>
    <script src="<?php echo base_url('public/js/recoveri.js') ?>"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f4f6f9;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 20px;
        }

        .login-container {

            width: 100%;
            max-width: 820px;
            min-height: 500px;

            background: #fff;

            border-radius: 18px;

            overflow: hidden;

            display: flex;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .08);
        }

        .login-left {

            width: 320px;

            background:
                linear-gradient(180deg,
                    #FE4F00 0%,
                    #ff7b3c 100%);

            color: white;

            padding: 35px;

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .logo {
            width: 170px;
            margin-bottom: 25px;
        }

        .product-img {
            width: 100%;
            max-width: 220px;
            margin-bottom: 20px;
        }

        .left-title {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
        }

        .left-subtitle {
            margin-top: 10px;
            font-size: 13px;
            text-align: center;
            opacity: .9;
            line-height: 1.5;
        }

        .login-form {

            flex: 1;

            padding: 40px 45px;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .welcome-sub {
            color: #718096;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .label {
            display: block;

            margin-bottom: 7px;

            font-size: 13px;
            font-weight: 600;

            color: #4a5568;
        }

        .form-control {

            width: 100%;

            height: 46px;

            border: 1px solid #d9dfe7;

            border-radius: 10px;

            padding: 0 15px;

            font-size: 14px;

            transition: .2s;
        }

        .form-control:focus {

            border-color: #FE4F00;

            box-shadow:
                0 0 0 3px rgba(254, 79, 0, .12);

            outline: none;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {

            position: absolute;

            right: 15px;
            top: 15px;

            color: #999;

            cursor: pointer;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }

        .forgot-password a {

            color: #FE4F00;

            font-size: 13px;

            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .submitBtn input,
        .submitBtn button {

            width: 100% !important;

            height: 48px !important;

            border: none !important;

            border-radius: 10px !important;

            background: #FE4F00 !important;

            color: white !important;

            font-size: 15px !important;

            font-weight: 600 !important;

            transition: .2s;
        }

        .submitBtn input:hover,
        .submitBtn button:hover {

            background: #e64b00 !important;
        }

        .copyright {

            margin-top: 20px;

            text-align: center;

            font-size: 12px;

            color: #a0aec0;
        }

        @media(max-width:768px) {

            .login-container {
                flex-direction: column;
                max-width: 100%;
                min-height: auto;
            }

            .login-left {
                width: 100%;
                padding: 30px;
            }

            .product-img {
                display: none;
            }

            .login-form {
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="login-left">

            <img class="logo"
                src="<?php echo base_url(); ?>public/UpdateSatrship/logowhite.png">

            <img class="product-img"
                src="<?php echo base_url(); ?>public/UpdateSatrship/Productos STS-01.png">

            <div class="left-title">
                StarShip ERP
            </div>

            <div class="left-subtitle">
                Gestión empresarial, logística,
                inventario y facturación en una sola plataforma.
            </div>

        </div>

        <div class="login-form">

            <div class="welcome">
                Bienvenido
            </div>

            <div class="welcome-sub">
                Inicia sesión para continuar.
            </div>

            <?php echo $form ?>

            <div class="form-group">

                <label class="label">
                    Correo electrónico
                </label>

                <?php echo $correo ?>

            </div>

            <div class="form-group">

                <label class="label">
                    Contraseña
                </label>

                <div class="password-wrapper">

                    <?php echo $clave ?>

                    <i class="fa fa-eye toggle-password"></i>

                </div>

            </div>

            <div class="forgot-password">
                <a href="#">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <div class="form-group">
                <div class="submitBtn">
                    <?php echo $submit ?>
                </div>
            </div>

            <?php echo $form_cierre ?>

            <div class="copyright">
                © <?php echo date('Y'); ?> StarShip ERP
            </div>

        </div>

    </div>

    <script>
        $(document).ready(function() {

            let passwordField = $('input[type="password"]').first();

            $('.toggle-password').click(function() {

                let type = passwordField.attr('type') === 'password' ?
                    'text' :
                    'password';

                passwordField.attr('type', type);

                $(this).toggleClass('fa-eye');
                $(this).toggleClass('fa-eye-slash');

            });

        });
    </script>

</body>

</html>