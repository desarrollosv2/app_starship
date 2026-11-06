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
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"-->
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script-->


    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="dist/css/component-chosen.css" rel="stylesheet">
    <!--script src="https://code.jquery.com/jquery-3.1.1.min.js"></script-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.6/chosen.jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/chosen/chosen.min.css')?>">
    <!--script type="text/javascript" src="<?php echo base_url();?>public/chosen/chosen.jquery.min.js"></script-->

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/estilos.css')?>">

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
    <div class="container-fluid">
        <div class="row borde-orange">
            <div class="col-md-6">
                <h3 class="m-3">TRASPASO</h3>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-primary  pull-right " data-toggle="modal"
                    data-target="#ModalAdd_Traspaso" data-whatever="">
                    <i class="glyphicon glyphicon-plus"></i>
                    Crear
                </button>
            </div>
        </div>
    </div>

    <!-- modal  para agregar manifiesto-->
    <div class="modal fade" id="ModalAdd_Traspaso" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true"
        data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Crear Traspaso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_manifiesto" id="add_traspaso"
                        action="javascript:traspaso_guardar()">
                        <input type="hidden" class="form-control" placeholder="Introduzca manfiesto" name="id" id="id">
                        <div class="form-group">
                            <label for="manfiesto">Guia</label>
                            <input type="hidden" class="form-control" name="manifiesto" id="manifiesto"
                                value="<?php echo 'TRA-'.date('dmyHis') ?>">
                            <input type="text" class="form-control" value="<?php echo 'TRA-'.date('dmyHis') ?>"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control" placeholder="Introduzca fecha " id="fecha"
                                name="fecha" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <input type="text" class="form-control" placeholder="Introduzca descripcion"
                                id="descripcion" name="descripcion">
                        </div>
                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                        </button>
                        <button class="btn btn-primary float-md-right">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Modal para agregar guias -->
    <div class="modal fade" id="Modal_Add_Guia" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true"
        data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Agregar Guia a Traspaso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_guia" id="add_guia"
                        action="javascript:guardar_guia_traspaso()">
                        <input type="hidden" class="form-control" placeholder="Introduzca manfiesto" name="id" id="id">
                        <div class="form-group">
                            <label for="manfiesto">Traspaso</label>
                            <input type="text" class="form-control" placeholder="Introduzca manfiesto" name="manifiesto"
                                id="manifiesto" readonly>
                        </div>
                        <div class="form-group">
                            <label for="guia">Warehouse</label>
                            <input type="number" class="form-control" placeholder="Introdusca guia" id="guia"
                                name="guia" required>
                        </div>
                        <div class="form-group">
                            <label for="guia">Estatus</label>
                            <div class="form-group">
                                <?php $this->load->view('catalogos/estatus'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="idcasillero" name="idcasillero" readonly>
                            <input type="hidden" class="form-control" id="nombre_destinatario"
                                name="nombre_destinatario" readonly>
                            <input type="hidden" id="referencia" name="referencia" value=0>
                            <input type="hidden" class="form-control" id="cajas" name="cajas" required value=0>
                        </div>
                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
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
                            <?php $this->load->view('preclasifica/lista_guias');?>
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