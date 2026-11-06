<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!--<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->


    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script-->


    <!--script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script-->

    <style>
        body {
            /* el tamaño por defecto es 14px */
            font-size: 14px;
            font-family: 'Roboto', Sans-serif, Helvetica;
        }

        .color {
            background: #ef7f1b;
        }

        .ch-titulo {
            color: #f05b2a;

        }

        /*
    table th {

        background: #808080;
        color: #FFF;

    }

    .table-hover tbody tr:hover td {
        background: #778899;
    }
*/
        body {
            background: #343d46;
        }


        .box {
            margin: 5px auto;

        }

        .container-1 {

            vertical-align: middle;
            white-space: nowrap;
            position: relative;
        }

        .container-1 input#search {
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

        .container-1 input#search::-webkit-input-placeholder {
            color: #65737e;
        }

        .container-1 input#search:-moz-placeholder {
            /* Firefox 18- */
            color: #65737e;
        }

        .container-1 input#search::-moz-placeholder {
            /* Firefox 19+ */
            color: #65737e;
        }

        .container-1 input#search:-ms-input-placeholder {
            color: #65737e;
        }

        .container-1 .icon {
            position: absolute;
            top: 50%;
            margin-left: 17px;
            margin-top: 17px;
            z-index: 1;
            color: #4f5b66;
        }

        .container-1 input#search:hover,
        .container-1 input#search:focus,
        .container-1 input#search:active {
            outline: none;
            background: #ffffff;
        }

        .table td,
        .table th {
            font-size: 12px;

        }

        #agregarItem {
            visibility: hidden;
            display: none;
        }

        #listar_Compras {
            visibility: visible;
            display: inline;
            width: 100%;
        }
    </style>
</head>

<body>

    <div class="container-fluid" id="listar_compras">
        <nav class="navbar navbar-default navbar-fixed-top w-100">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#" style="color:#565656;"> Tasas de Cambio</a>
                </div>
                <button type="button" class="btn btn-primary" onclick="mostrar_tasa_cambio(<?php echo 0;?>);">
                    <i class="glyphicon glyphicon-plus"></i>
                    Crear
                </button>
            </div>
        </nav>

        <div class="container m-top">
            <div class="table-responsive">
                <table id="banco_" class="table table-hover">
                    <thead>
                        <tr>
                        
                            <th>ID</th>
                            <th>Nombre de Moneda</th>
                            <th>Tasa de Cambio</th>
                            <th>Impuesto</th>
                            <th>Manejo</th>
                            <th>pais</th>
                            <th>Acciónesddd</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($tasa_cambio as $row) : ?>
                            <tr>
                                <td><?php echo $row->id; ?></td>
                                <td><?php echo $row->nombre_moneda; ?></td>
                                <td><?php echo $row->tasa; ?></td>
                                <td><?php echo $row->impuesto; ?></td>
                                <td><?php echo $row->manejo; ?></td>
                                <td><?php echo $row->nombre_pais; ?></td>
                                
                                <td>
                                   
                                    <a href='#' class="btn btn-default btn-sm" style="margin:0px;" title="Editar Tasa" onclick="mostrar_tasa_cambio(<?php echo $row->id; ?>);">
                                        <i class="fa fa-pencil" style="color: blue;" aria-hidden="true"></i> </a>

                                    <a href='#' class="btn btn-default btn-sm" data-toggle="modal" style="margin:0px;" title="Eliminar Tasa" onclick="eliminar_tasa_cambio(<?php echo $row->id; ?>);">
                                        <i class="fa fa-trash-o" style="color: red;" aria-hidden="true"></i> </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    
    <script>
        $(document).ready(function() {
            $('#banco_').DataTable({
                //para cambiar el lenguaje a español
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            
            $("#btnAdd").click(function() {
                $("#agregarItem").css({
                    'visibility': 'visible',
                    'display': 'inline'

                });

                $("#listar_Proveedores").css({
                    'visibility': 'hidden',
                    'display': 'none'

                });
            });

            $("#btnBack").click(function() {
                $("#agregarItem").css({
                    'visibility': 'hidden',
                    'display': 'none'
                });

                $("#listar_Proveedores").css({
                    'visibility': 'visible',
                    'display': 'inline'

                });
            });
            $(".llamarDiv").click(function() {
                $("#agregarItem").css({
                    'visibility': 'visible',
                    'display': 'inline'
                });

                $("#listar_Proveedores").css({
                    'visibility': 'hidden',
                    'display': 'none'

                });
            });
            
        });
    </script>


</body>

</html>