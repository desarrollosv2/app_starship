<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400" rel="stylesheet"> -->
    <title></title>
</head>

<body>
    <div class="container-fluid">
        <div class="row borde-orange">
            <div class="col-md-6">
                <h4 class="m-3">CLASIFICACION DE PAQUETES</h4>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-primary btn-md pull-right " data-toggle="modal" data-target="#ModalAdd_Manifiesto" data-whatever="">
                    <i class="glyphicon glyphicon-plus"></i>
                    Crear Referencia
                </button>
            </div>
        </div>
        <div id="contenidoLista"></div>
    </div>



    <!-- MODAL -->
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link href='//fonts.googleapis.com/css?family=Montserrat:thin,extra-light,light,100,200,300,400,500,600,700,800'
            rel='stylesheet' type='text/css'>
        <!--
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->


        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="dist/css/component-chosen.css" rel="stylesheet">
        <!--<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.6/chosen.jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/chosen/chosen.min.css') ?>">
        <!--<script type="text/javascript" src="<?php echo base_url(); ?>public/chosen/chosen.jquery.min.js"></script>-->

        <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/estilos.css') ?>">

        <title>Preclasificación</title>
        <style>
            body {
                font-family: 'Montserrat', Sans-serif;
            }

            h1 {

                font-family: 'Montserrat', Sans-serif;
                font-weight: bold;
            }
        </style>
    </head>

    <body>




        <!--
            <div class="card  w-100">
                <div class="card-heading  hd">
                    <div class="row  h-100 justify-content-center align-items-center">
                        <div class="col">
                            <h1> Preclasificación</h1>
                        </div>
                        <div class="col">
                            <div class="box">
                                <div class="container-1">
                                    <span class="icon"><i class="fa fa-search"></i></span>
                                    <input type="search" id="search" placeholder="Search..." class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col">

                            <button type="button" class="btn btn-secondary btn-md pull-center " data-toggle="modal"
                                data-target="#ModalAdd_Manifiesto" data-whatever="">
                                <i class="glyphicon glyphicon-plus"></i>
                                Crear Referencia
                            </button>


                        </div>
                    </div>

                </div>
            </div>
            -->



        <!-- modal  para agregar manifiesto-->
        <div class="modal fade" id="ModalAdd_Manifiesto" tabindex="-1" role="dialog" aria-labelledby=""
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Crear Manifiesto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <form enctype="multipart/form-data" class="add_manifiesto" id="add_manifiesto"
                            action="javascript:guardar_manifiesto()">
                            <input type="hidden" class="form-control" placeholder="Introduzca manfiesto" name="id"
                                id="id">
                            <div class="form-group">
                                <label for="manfiesto">Guia Master</label>
                                <input type="text" class="form-control" placeholder="Introduzca manfiesto"
                                    name="manifiesto" id="manifiesto">
                            </div>

                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" class="form-control" placeholder="Introduzca fecha " id="fecha"
                                    name="fecha" required>
                            </div>



                            <div class="form-group">
                                <label for="referencia">Referencia</label>
                                <input type="text" class="form-control" placeholder="Introduzca referencia"
                                    id="referencia" name="referencia">
                            </div>

                            <div class="form-group">
                                <label for="paquetes">Paquetes</label>
                                <input type="number" class="form-control" placeholder="Introduzca paquetes"
                                    id="paquetes" name="paquetes">
                            </div>

                            <div class="form-group">
                                <label for="sacos">Sacos</label>
                                <input type="number" class="form-control" placeholder="Introduzca Sacos" id="sacos"
                                    name="sacos">
                            </div>



                            <button type="button" class="btn btn-secondary float-md-right"
                                data-dismiss="modal">Cerrar
                            </button>
                            <button class="btn btn-primary float-md-right ">Guardar</button>

                        </form>


                    </div>

                </div>
            </div>
        </div>


        <!--Modal para agregar guias -->
        <div class="modal fade" id="Modal_Add_Guia" tabindex="-1" role="dialog" aria-labelledby=""
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Agregar Guia a Manifiesto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <form enctype="multipart/form-data" class="add_guia" id="add_guia"
                            action="javascript:guardar_guia()">
                            <input type="hidden" class="form-control" placeholder="Introduzca manfiesto" name="id"
                                id="id">
                            <div class="form-group">
                                <label for="manfiesto">Manifiesto</label>
                                <input type="text" class="form-control" placeholder="Introduzca manfiesto"
                                    name="manifiesto" id="manifiesto" readonly>
                            </div>


                            <div class="form-group">
                                <label for="referencia">referencia</label>
                                <input type="number" class="form-control" placeholder="Introduzca Referencia"
                                    id="referencia" name="referencia" readonly>
                            </div>

                            <!--   <div class="form-group">
                                <input type="texh" class="form-control" id="idcasillero">
                                    <select name="clientes" id="clientes" class="form-control  form-control-chosen"
                                        data-placeholder="Seleccione cliente" required>
                                        <option value=""></option>
                                        <?php foreach ($clientes as $row): ?>
                                        <option value="<?php echo $row->casillero; ?>">
                                            <?php echo  $row->casillero . ' - ' . $row->nombres . ' ' . $row->apellidos; ?>
                                        </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>-->


                            <div class="form-group">
                                <label for="guia">Guia</label>
                                <input type="number" class="form-control" placeholder="Introdusca guia" id="guia"
                                    name="guia" required>
                            </div>
                            <div class="form-group">

                                <input type="text" class="form-control" id="idcasillero" name="idcasillero" readonly>
                                <input type="text" class="form-control" id="nombre_destinatario" name="nombre_destinatario" readonly>
                            </div>
                            <div class="form-group">
                                <label for="cajas">Bultos </label>
                                <input type="number" class="form-control" placeholder="Introdusca cantidad"
                                    id="cajas" name="cajas" required>
                            </div>



                            <button type="button" class="btn btn-secondary float-md-right"
                                data-dismiss="modal">Cerrar
                            </button>
                            <button type="submit" class="btn btn-primary float-md-right">Guardar</button>

                        </form>


                    </div>

                </div>
            </div>
        </div>


        <!--- modal para mostrar guias -->
        <div class="modal fade " id="modal_guias" role="dialog" data-backdrop="static">

            <div class="modal-dialog modal-dialog-centered mimodal " role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header" style="background-color:white">
                        <h4 class="modal-title" style="color:#F15a29">Consulta de Guias</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="row-fluid">
                        <div class="messageup" id='messageup'></div>
                    </div>

                    <form enctype="multipart/form-data" class="listaguia" id="listaguia"
                        action="javascript:consulta_guias;">
                        <!-- Modal body -->
                        <div class="modal-body">
                            <div id="listado_guias">

                                <?php $this->load->view('preclasifica/lista_guias'); ?>
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--- fin modal para mostrar guias -->



        <!--Modal para agregar poliza -->
        <div class="modal fade" id="Modal_Add_Poliza" tabindex="-1" role="dialog" aria-labelledby=""
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Agregar Poliza</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form enctype="multipart/form-data" class="add_poliza" id="add_poliza"
                            action="javascript:guardar_poliza()">
                            <input type="hidden" class="form-control" placeholder="Introduzca manfiesto" name="id"
                                id="id">
                            <div class="form-group">
                                <label for="manfiesto">Manifiesto</label>
                                <input type="text" class="form-control" placeholder="Introduzca manfiesto"
                                    name="manifiesto" id="manifiesto" readonly>
                            </div>
                            <div class="form-group">
                                <label for="referencia">referencia</label>
                                <input type="number" class="form-control" placeholder="Introduzca Referencia"
                                    id="referencia" name="referencia" readonly>
                            </div>
                            <div class="form-group">
                                <label for="guia">Póliza</label>
                                <input type="text" class="form-control" placeholder="Introdusca guia" id="poliza"
                                    name="poliza" require>
                            </div>
                            <button type="button" class="btn btn-secondary float-md-right"
                                data-dismiss="modal">Cerrar
                            </button>
                            <button type="submit" class="btn btn-primary float-md-right">Guardar</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>







        <!-- fin modal guardar poliza-->
        <script type="text/javascript">
            $('.form-control-chosen').chosen({
                allow_single_deselect: true,
                width: '100%'
            });
            $('.form-control-chosen-required').chosen({
                allow_single_deselect: false,
                width: '100%'
            });
            $('.form-control-chosen-search-threshold-100').chosen({
                allow_single_deselect: true,
                disable_search_threshold: 100,
                width: '100%'
            });
        </script>

        <script>
            $("#guia").blur(function() {
                buscar_warehouse_wd($('#guia').val());
            });
        </script>

    </body>

    </html>
    <!-- END MODAL -->





    <script>
        //preclasifica_lista();
    </script>
</body>

</html>