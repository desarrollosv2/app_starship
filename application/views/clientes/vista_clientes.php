<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <title>Consulta Cliente</title>
    <style>
        .xyz 
        {
            background-size: auto;
            text-align: center;
            padding-top: 100px;
        }

        .btn-circle.btn-sm 
        {
            width: 30px;
            height: 30px;
            padding: 6px 0px;
            border-radius: 15px;
            font-size: 8px;
            text-align: center;
        }

        .btn-circle.btn-md 
        {
            width: 50px;
            height: 50px;
            padding: 7px 10px;
            border-radius: 25px;
            font-size: 10px;
            text-align: center;
        }

        .btn-circle.btn-xl 
        {
            width: 50px;
            height: 50px;
            padding: 8px 8px;
            border-radius: 100px;
            font-size: 12px;
            text-align: center;
        }

        body 
        {
            /* el tamaño por defecto es 14px */
            font-size: 14px;
            font-family: 'Roboto', Sans-serif, Helvetica;
        }

        .color 
        {
            background: #ef7f1b;
        }

        .ch-titulo 
        {
            color: #f05b2a;
        }

        body 
        {
            background: #343d46;
        }

        .box 
        {
            margin: 5px auto;
        }

        .container-1 
        {
            vertical-align: middle;
            white-space: nowrap;
            position: relative;
        }

        .container-1 input#search 
        {
            width: 100%;
            height: 50px;
            font-size: 10pt;
            float: left;
            color: #63717f;
            padding-left: 45px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }

        .container-1 input#search::-webkit-input-placeholder 
        {
            color: #65737e;
        }

        .container-1 input#search:-moz-placeholder 
        {
            color: #65737e;
        }

        .container-1 input#search::-moz-placeholder 
        {
            color: #65737e;
        }

        .container-1 input#search:-ms-input-placeholder 
        {
            color: #65737e;
        }

        .container-1 .icon 
        {
            position: absolute;
            top: 50%;
            margin-left: 17px;
            margin-top: 17px;
            z-index: 1;
            color: #4f5b66;
        }

        .container-1 input#search:hover,
        .container-1 input#search:focus,
        .container-1 input#search:active 
        {
            outline: none;
            background: #ffffff;
        }

        .table td,
        .table th 
        {
            font-size: 12px;
        }
    </style>
</head>

<div class="container">
    <div class="row">
        <div class="col">
            <br>
            <h2>Consulta Cliente</h2>
        </div>

        <div class="col">
            <br>
            <button type="button" class="btn btn-primary btn-circle btn-xl" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">
                <i class="fa fa-filter fa-2x"></i>
            </button>

            <button type="button" class="btn btn-dark btn-circle btn-xl" data-toggle="modal" data-target="#exampleModal1" data-whatever="@mdo">
                <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
            </button>

            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Filtro de Busqueda de Clientes</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <form method="$_POST" class="buscar_abono" id="buscar_abono">
                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Correo</label>
                                    <input type="text" class="form-control" id="correo" name="correo">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" onclick="busqueda_clientes();" data-dismiss="modal">Buscar</button>
                        </div>
                    </div>
                </div>
            </div>



            <!--  PARA INGRESAR DATOS DE CLIENTES -->
            <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ingresar Comentario</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <form method="$_POST" class="filtro_reporte" id="filtro_reporte">
                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Correo</label>
                                    <input type="email" class="form-control" id="correos" name="correos">
                                </div>

                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Comentario</label>
                                    <textarea class="form-control" name="comentario" id="comentario"></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" onclick="guardar_comentario();" data-dismiss="modal">Agregar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid m-top">
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table custom-table" id="tbl_detalle">
                    <thead>
                        <tr>
                            <th scope="col">Tracking Number</td>
                            <th scope="col">Reemplazo</td>
                            <th scope="col">Cliente</td>
                            <th scope="col">Direccion</td>
                            <th scope="col">Telefono</td>
                            <th scope="col">Correo</td>
                            <th scope="col">Comentarios</td>
                            <th scope="col">Estado</td>
                        </tr>
                    </thead>

                    <tbody id="tabla_busqueda_track"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>





<script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jszip/jszip.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script>

</script>


<!--</body>-->

</html>