<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Incluye Font Awesome para usar iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>Reporte de Manifiesto</title>
    <style>
        .container {
            margin-top: 50px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #F4F6F9;
            margin-bottom: 10px;
        }

        .form-control,
        .btn {
            border-radius: 50px !important;
        }
    </style>
    <?php
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);

    $idpreclasificacion                = 0;
    ?>
</head>

<body>
    <div class="container-fluid">
        <?php $idpreclasificacion = $lista[0]->idpreclasificacion; ?>
        <div class="row bg-white">
            <div class="col text-center">

                <h2 class="text-muted">Reporte de Manifiesto</h2>
            </div>

        </div>
        <div class="row mt-2 ml-2">
            <input type="hidden" value="<?php echo $idpreclasificacion; ?>" id="id-manifiesto">

            <div class="container-fluid m-top ">
                <div class="row">

                    <div class="table-responsive">

                        <table class="table table-custom" id="tbl_listadoguias">
                            <thead>

                                <tr>
                                    <th>N</th>
                                    <th>AWB</th>
                                    <th>Tracking Number</th>
                                    <th>Recolecta</th>
                                    <th>Guia</th>
                                    <th>Tracking Replace</th>
                                    <th>Referencia</th>
                                    <th>Bag Number</th>
                                    <th>Value</th>
                                    <th>Items Description</th>
                                    <th>Peso</th>
                                    <th>Buyer id</th>
                                    <th>Cliente</th>
                                    <th>Tipo Entrega</th>
                                    <th>Tipo Servicio</th>
                                    <th>Direccion</th>
                                    <th>Estatus</th>
                                    <th>Estado</th>
                                    <th>DAI</th>
                                    <th>IVA</th>
                                    <th>Almacenaje</th>
                                    <th>Manejo</th>
                                    <th>Cobro Final</th>
                                    <th>Usuario</th>
                                    <th>Sobrante</th>
                                    <th>Departamento</th>
                                    <th>Package Number</th>


                                </tr>
                            </thead>

                            <tbody id="tabla_tracking_guia">
                                <?php
                                if (isset($lista)) {
                                    $c = 1;
                                    foreach ($lista as $row) {
                                ?>
                                        <tr class="rw">
                                            <td scope="row" style="margin-left: 9px"><?php echo $c; ?></td>
                                            <td scope="row"><?= ($flag_temu == 1) ? $master_cn : $row->awb; ?></td>
                                            <td scope="row"><?= $row->tracking_number; ?></td>
                                            <td scope="row"><?php echo $row->pickup_number; ?></td>
                                            <td scope="row"><?php echo $row->internal_tracking; ?></td>
                                            <td scope="row"><?php echo $row->tracking_replace; ?></td>
                                            <td scope="row"><?php echo $row->referencia; ?></td>
                                            <td scope="row"><?php echo $row->bag_number; ?></td>

                                            <td scope="row"><?= ($flag_temu == 1) ? number_format((float)($row->value / 1.115), 2) : $row->value; ?></td>
                                            <td scope="row"><?php echo $row->commodity; ?></td>

                                            <td scope="row"><?php echo $row->gweight; ?></td>
                                            <td scope="row"><?php echo $row->consignee_account; ?></td>
                                            <td scope="row"><?php echo $row->consignee; ?></td>
                                            <td scope="row"><?php echo $row->tipo_entrega; ?></td>
                                            <td scope="row"><?php echo $row->tipo_servicio; ?></td>
                                            <td scope="row"><?php echo $row->consignee_address1; ?></td>
                                            <td scope="row"><?php echo $row->estatus; ?></td>
                                            <td scope="row"><?php echo $row->estado; ?></td>

                                            <td scope="row"><?php echo '$' . number_format($row->dai, 2); ?></td>
                                            <td scope="row"><?php echo '$' . number_format($row->iva, 2); ?></td>

                                            <td scope="row"><?php echo '$' . number_format($row->almacenaje, 2); ?></td>
                                            <td scope="row"><?php echo '$' . number_format($row->manejo, 2); ?></td>

                                            <td scope="row"><?php echo '$' . number_format($row->cobro_final, 2); ?></td>
                                            <td scope="row"><?php echo $row->user_name; ?></td>
                                            <?php if ($row->sobrante == 1) {
                                                echo '<td scope="row" class="font-weight-bold text-success">SI</td>';
                                            } else {
                                                echo '<td scope="row" class="font-weight-bold text-danger">NO</td>';
                                            } ?>

                                            <td scope="row"><?php echo $row->departamento_id; ?></td>
                                            <td scope="row"><?php echo $row->package_number; ?></td>

                                        <?php
                                    }
                                        ?>

                                    <?php
                                }
                                    ?>
                            </tbody>
                        </table>
                    </div>
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
        $(document).ready(function() {
            var table = $("#tbl_listadoguias").DataTable({
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
                "buttons": [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar',
                        className: 'btn btn-success mr-2' // Botón estilo Bootstrap
                    },

                    {
                        extend: 'pdfHtml5', // Botón PDF
                        text: '<i class="fas fa-file-pdf"></i> Exportar', // Icono de Font Awesome
                        className: 'btn btn-danger mr-2', // Espacio a la derecha con Bootstrap
                        action: function(e, dt, button, config) {
                            // Ejecuta la función personalizada antes de generar el PDF
                            miFuncionPersonalizada();

                            // Llama al comportamiento predeterminado para generar el PDF
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                        }
                    }
                ]
            });

            // Coloca los botones en el contenedor deseado
            table.buttons().container().appendTo('#tbl_listadoguias_wrapper .col-md-6:eq(0)');
        });

        // Función personalizada
        function miFuncionPersonalizada() {
            reporte_guias($("#id-manifiesto").val());
        }
    </script>
</body>

</html>