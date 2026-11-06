<!doctype html>

<html>

<head>

    <title>StarShip ERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="css/style.css">

    <script type="text/javascript" src="<?php echo base_url('public/js/funciones.js') ?>"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #2d3436;
        }

        .btn-primary:hover {

            display: block;
            color: white !important;;
            font-size: 14px;
            font-family: "montserrat";
            text-decoration: none;


            padding: 14px 60px;
            text-transform: uppercase;
            overflow: hidden;
            transition: 1s all ease;
            border-color: #F0420E !important;
            background: #F0420E !important;
            box-shadow: inset -640px 0 0 0 #F0420E  !important;

            background: #ccc !important;
           
        }


    /*  https://javiniguez.com/botones-sociales-en-css3-con-efecto-hover-2 */

    </style>

</head>

<body>



    <section class="ftco-section w-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center mb-5">
                    <h2 class="heading-section"></h2>

                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-20">
                    <div class="wrap d-md-flex">
                        <div class="text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last  " style="background:#F15a29">

                            <div class="text w-100 ">
                                <img src="<?php echo base_url(); ?>public/dist/img/logo_2.png" alt="starship Logo" class=" w-100">

                                <h2></h2>
                                <p></p>
                                <!--  <a href="#" class="btn btn-white btn-outline-white">Sign Up</a>-->
                            </div>
                        </div>

                        <div class="login-wrap  p-lg-4 ">
                            <div class="d-flex ">
                                <div class="row ">
                                    <!-- <h3 class="mb-4">Sin In</h3> -->
                                    <div class="col  text-center">
                                        <img src="<?php echo base_url(); ?>public/dist/img/AdminLTELogo.png" alt="starship Logo" class="">
                                    </div>


                                </div>

                                <div class="w-100">
                                    <p class="social-media d-flex justify-content-end">
                                        <!--  <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-facebook"></span></a>
                                        <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-twitter"></span></a>-->
                                    </p>
                                </div>
                            </div>
                            <br><br>
                            <!--  <form action="#" class="signin-form"> -->
                            <?php echo $form ?>
                            <div class="form-group mb-3">
                                <div class="col" <label class="label" for="name">Usuario</label>
                                    <!-- <input type="text" class="form-control" placeholder="Username" required> -->
                                    <?php echo $correo ?>
                                </div>
                                <div class="form-group mb-3">
                                    <div class="col" <label class="label" for="name">Clave</label>
                                        <!--    <input type="password" class="form-control" placeholder="Password" required> -->
                                        <?php echo $clave ?>
                                    </div>
                                    <div class="form-group pt-4 ">
                                        <!--   <button type="submit" class="form-control btn btn-primary submit px-3">Sign In</button> -->
                                        <?php echo $submit ?>
                                    </div>
                                    <!--   <div class="form-group d-md-flex">
                                    <div class="w-50 text-left">
                                        <label class="checkbox-wrap checkbox-primary mb-0">Remember Me
                                            <input type="checkbox" checked>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="w-50 text-md-right">
                                        <a href="#">Forgot Password</a>
                                    </div>-->
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>




</body>

</html>