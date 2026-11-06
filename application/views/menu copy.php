<style>
.user-panel {
    display: flex;
    justify-content: left;
    align-items: left;
    color: white;


}

.colortexto {
    color: #304ffe;
}

/*cambiar color de fondo de todos los item */
.nav-pills .nav-link.active,
.show>.nav-pills .nav-link {
    background: #343a40 !important;


}

.nav-pills .nav-link.active,
.show>.nav-pills .nav-link:hover {
    background: #343a40 !important;

}

/*hover a primarios */

.nav-pills>li>a:hover {
    color: #edc204 !important;
    border: solid 2px #ffae00;
    opacity: 1;

}

/*.main-sidebar { background-color: #fff !important } cambia color de fondo */


.nav-sidebar .nav-item>.nav-link {
    margin-bottom: .2rem;
    background: #343a40 !important;
    /*agregue esto* cambia color de fondo del slider*/
}


.nav-sidebar .menu-open>.nav-treeview>li>a {
    display: block;
    background: green !important;
    /*cambia el color de fondo de los item*/
    background: #343a40 !important;
}

.nav-sidebar .menu-open>.nav-treeview>li>a:hover {
    background: #F15a29 !important;
    /*cambiar color de fondo hover */
    background: #FE4F00 !important;
    /*cambiar color de fondo hover */
    color: white !important;

}

.nav-link:hover {

    margin: 0;
    background: red !important;
    /*cambiar el fondo del menu primario*/

}

.nav-treeview {
    /*background: red !important; /*color de fondo de items*/
}


.nav-sidebar .nav-treeview {
    display: none;
    list-style: none;
    padding: 0;
    /*background: yellow !important;/*cambia color de fondo del submenu*/
}

/* asigna color a icono de submenu */
.nav-sidebar .nav-treeview>.nav-item>.nav-link>.nav-icon {
    width: 1.6rem;
    /*color: yellow !important;*/
    color: #ef7f1b !important;
}

/*------------------------------------- */

/*cambia color de fondo de array */
.nav-sidebar {
    color: #343a40 !important;

}

.nav-sidebar:hover {
    overflow: visible;
    color: #343a40 !important;



}

/*-------------------------- */

/*cambia color a los nombres de los items*/
.nav-sidebar .nav-link p {
    display: inline-block;
    margin: 0;
    color: #edc204 !important;
    color: white !important;
}

/*---------------------------------------- */

.nav-pills .nav-link {
    color: white !important;
    /*cambia color a icono de items */
}


.sidebar {
    width: 250px;
    position: fixed;
    top: 0;
    left: 250;
    right: 10;
    height: 100vh;
    z-index: 999;
    background: #343a40;
    color: #343a40;


}


.sidebar {
    position: absolute;
    left: 0;
    right: 0;
}

.img {
    background: yellow;
    background: #F15A29;
    background: #FE4F00;

    text-align: center;
    border-radius: 100%;
    height: 35px;
    width: 35px;

    padding-top: 5px;

}

.center-h {
    justify-content: center;
}

.center-v {
    display: flex;
    justify-content: center;
    align-items: center;
}

.navbar-nav .nav-item .nav-link:hover {
    color: #F15A29;
    /* Cambia este color por el que prefieras */
    background-color: #808285 !important;
    /* Cambia el fondo al pasar el mouse */
    border-radius: 5px;
    /* Si deseas bordes redondeados */
    transition: all 0.3s ease;
    /* Añade una transición suave */
}
</style>


<!-- Navbar -->

<nav class="main-header navbar navbar-expand  navbar-light border-bottom" style=" background-color:#FE4F00;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars text-white"></i></a>
        </li>
    </ul>


    <ul class="navbar-nav ml-auto">

    </ul>
    <?php //date_default_timezone_set('America/Miami florida');?>
    <h1 style="color: white"><?php //echo  date ("H:i",time());?> </h1>
    <a.nav-link href="#" style="font-size:14px;" class=" btncerrar" onclick="salir()">Cerrar Sesión</a>



</nav>
<!-- /.navbar -->
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="" class="brand-link">
            <img class="col-12 mx-auto" src="<?php echo base_url();?>public/dist/img/AdminLTELogo.png"
                alt="AdminLTE Logo" style="opacity: .8">

        </a>

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex text-center">

            <div class=" center-v ml-3">

                <?php 
           
            if (  $_SESSION["pais"] == 1){?>
                <img src="<?php echo base_url();?>public/dist/img/sv.png" alt=""
                    style="opacity: .8; width: 50px; height: auto">
                <?php
            }

            ?>

                <?php 
           
           if (  $_SESSION["pais"] == 2){?>
                <img src="<?php echo base_url();?>public/dist/img/gt.png" alt=""
                    style="opacity: .8; width: 50px; height: auto">
                <?php
           }

           ?>

                <?php 
           
           if (  $_SESSION["pais"] == 3){?>
                <img src="<?php echo base_url();?>public/dist/img/hn.png" alt=""
                    style="opacity: .8; width: 50px; height: auto">
                <?php
           }

           ?>


                <a href="#" style="font-size:14px; margin-left:3px"><?php echo  $_SESSION["nombre"]?> </a>

            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <?php
                
              // var_dump($_SESSION["menu"])
             var_dump($menu);
        
               ?>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>