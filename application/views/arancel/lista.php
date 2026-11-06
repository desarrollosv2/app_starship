<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Sweet Alert-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

    <title>Listado de Partidas</title>
</head>

<body>
    <div class="container-fluid">


        <div class="row mt-2  ">

            <div class="col-sm-10 mt-3">
                <h2 class="text-muted">Partidas Arancelarias</h2>
            </div>

            <div class="col-sm-2  mt-1 mb-2">
                <button type="button" class="btn btn-primary btn btn-primary btn-circle btn-xl mt-0" onclick="agregar_partida(0)"><i class="fas fa-plus"></i></button>

            </div>

        </div>

        <hr class="borde-orange mt-0">

    </div>

    <div class="container" style="margin-top:6px; display: none" id="c-permisos">
        <div class="card">
            <div class="card-header bg-secondary">
                <h5 id="titulo">PERMISOS</h5>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-7 pr-0">
                        <?php $this->load->view("catalogos/permisos", $this->datos); ?>
                    </div>

                    <div class="col-md-2 pl-0">
                        <button type="button" class="btn btn-primary mt-0 ml-0 " id="btn-add-permiso" name="btn-add-permiso" onclick="addpermiso()"> <i class="fa fa-plus fa-lg" aria-hidden="true"></i></button>
                    </div>
                    <div class="col-sm-4"></div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="hidden" id="p_import" name="p_import" class="form-control col-sm-4  mb-1 " readonly>
                        <input type="text" id="numero-partida" name="numero-partida" class="form-control col-sm-4  mb-1 " readonly>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-10">
                        <?php $this->load->view("arancel/lista_permisos");
                        ?>
                        <div class="col-sm-2">
                        </div>
                    </div>
                </div>

                <div class="row text-center">
                    <div class="col-sm-10">
                        <button type="button" class="btn btn-primary" id="btn-add-permiso" name="btn-add-permiso" onclick="permisos_off()">Cerrar</button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="container-fluid" id="contenedor-tbl">


        <div class="row">
            <div class="col">
                <div class="table-responsive ">

                    <table class="table custom-table" id="tbl_detalle">
                        <thead class="">
                            <tr>
                            //    <th> ID </th>
                                <th>C&oacutedigo Producto</th>
                                <th>N&uacutemero de Partida</th>
                                <th> Descripci&oacuten </th>
                                 <!--  <th> Modelo </th>
                                <th> Marca </th>
                                <th> Descripci&oacuten Genérica </th>
                                <th> Estado </th>
                                <th> Origen </th>
                                 -->
                                <th> Porcentaje </th>
                                <th> Acci&oacuten </th>

                            </tr>

                        </thead>

                        <tbody id="tabla">
                            <?php $i = 1;

                            foreach ($partidas as $row) : ?>


                                <tr>
                                    <td style="font-size: 15px;"> <?php echo $row->id            ?> </td>
                                    <td style="font-size: 15px;"> <?php echo $row->codigo_producto ?> </td>
                                    <td style="font-size: 15px;"> <?php echo $row->numero_partida ?> </td>
                                    <td style="font-size: 15px;"> <?php echo $row->descripcion    ?> </td>
                                  <!--  <td style="font-size: 15px;"> <?php // echo $row->modelo    ?> </td>
                                    <td style="font-size: 15px;"> <?php // echo $row->marca    ?> </td>
                                    <td style="font-size: 15px;"> <?php // echo //$row->descripcion_generica    ?> </td>
                                    <td style="font-size: 15px;"> <?php  //echo $row->id_estado    ?> </td>
                                    <td style="font-size: 15px;"> <?php //echo $row->id_origen    ?> </td> -->
                                    <td style="font-size: 15px;"> <?php //echo $row->porcentaje ?> </td>





                                    <td>
                                        <!--Editar partidas-->
                                        <button id="editar" class="btn btn-default btn-sm" data-toggle="modal" style="margin:0px;" title="Editar servicio" onclick="agregar_partida(<?php echo $row->id; ?>); return false;">
                                            <i class="fa fa-edit text-primary fa-lg" aria-hidden="true"></i></button>

                                        <!--Eliminar Partidas-->
                                        <button id="eliminar" class="btn btn-default btn-sm" data-toggle="modal" style="margin:0px;" title="Eliminar servicio" onclick="eliminar_partida(<?php echo $row->id; ?>); return false;">
                                            <i class="fa fa-trash text-danger fa-lg" aria-hidden="true"></i></button>


                                    </td>
                                </tr>

                            <?php $i = $i + 1;
                            endforeach ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
    <br><br><br><br><br>
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
        $(document).ready(function() {
            $('#tbl_detalle').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "No se encontraron datos que mostrar",
                    "info": "Mostrando Página _PAGE_ de _PAGES_",
                    "infoEmpty": "registros no disponibles",
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "sSearch": "Buscar:",

                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior",
                    },
                },
                "lengthChange": true,
                "buttons": ["excel", "pdf"]
            }).buttons().container().appendTo('#tbl_detalle_wrapper .col-md-6:eq(0)');
        });
    </script>

</body>

</html>