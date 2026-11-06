<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .card .card-header {
            background-color: white;
        }
        .bgn {
            background-color: #f44a17!important;
        }

		.card {
    border: 1px solid #e0e0e0; /* Borde gris claro */
    border-radius: 8px; /* Bordes redondeados */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra sutil */
    padding: 20px;
    background-color: #fff;
    margin: 20px auto;
    
}

    </style>
</head>

<body>
    <div class="container-fluid m-2">
        <div class="row">

            <div class="container-fluid m-top">

                <div class="card">
                    <div class="card-header">
                        <?php
                        if (isset($listado_man)) {
                            foreach ($listado_man as $fila) {
                        ?>
                                <h2> Manifiesto <?php echo $fila->id; ?> </h2>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6 mx-auto">
                                <label for="labelguia" class="form-label">GUIA</label>
                                <input class="form-control form-control-lg" type="text" id="no_guia" name="no_guia">
                                <div class="my-3">

                                    <button type="button" class=" btn bg-secondary m-0" onclick="listado_lmd();">
                                        <i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar</button>
                                    <button type="button" class="btn btn-primary my-1 " id="guardar_guia" onclick="guardar_guia(<?php echo $fila->id; ?>);">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                                    <!--button type="button" class="btn btn-dark " Onclick="generar_guia_pdf(<?php echo "'$fila->id'"; ?>, <?php echo "'$fila->fecha_manifiesto'"; ?>, <?php echo "'$fila->observaciones'"; ?>)">
                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button-->
                                </div>
                            </div>
					</div>
                    </div>
                </div>



                <div class="table-responsive">
                    <table class="table table-hover table-light table-responsive-sm" id="tbl_guias_no_lmd">
                        <br>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Master</th>
                                <th>Referencia</th>
                                <th>No Guia</th>
                                <th>Tracking Replace</th>
                                <th>Cliente</th>
                                <th>Departamento</th>
                                <th>Municipio</th>
                                <th>Tipo Entrega</th>
                                <th>Tipo Servicio</th>
                                <th>Descripcion</th>
                                <th>Value</th>
                                <th>IVA</th>
                                <th>DAI</th>
                                <th>Total</th>
                                <th colspan="1">Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="tabla_no_guia">
                            <?php
                            $con = 1;
                            if (isset($ver_manifiesto)) {
                                foreach ($ver_manifiesto as $row) {
                            ?>
                                    <tr>
                                        <td> <?php echo $con; ?> </td>
                                        <td> <?php echo $row->manifiesto; ?> </td>
                                        <td> <?php echo $row->referencia; ?> </td>
                                        <td> <?php echo $row->no_guia; ?> </td>
                                        <td> <?php echo $row->tracking_replace; ?> </td>
                                        <td> <?php echo $row->consignee; ?> </td>
                                        <td> <?php echo $row->departamento; ?> </td>
                                        <td> <?php echo $row->municipio; ?> </td>
                                        <td> <?php echo $row->tipo_entrega; ?> </td>
                                        <td> <?php echo $row->tipo_servicio; ?> </td>
                                        <td> <?php echo $row->commodity; ?> </td>
                                        <td> <?php echo '$' . number_format($row->value, 2); ?> </td>
                                        <td> <?php echo '$' . number_format($row->iva, 2); ?> </td>
                                        <td> <?php echo '$' . number_format($row->dai, 2); ?> </td>
                                        <td> <?php echo '$' . number_format($row->cobro_final, 2); ?> </td>
                                        <td>
                                            <a href="#" class="btn btn-danger btn-sm" onclick="eliminar_guia_manifiesto(<?php echo ($row->id) ?>, <?php echo ($row->id_manifiesto); ?>)" title="Eliminar Guia" style="margin:0px;">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                        </td>
                                    </tr>
                            <?php
                                    $con = $con + 1;
                                }
                            }
                            ?>
                            <input type="email" class="form-control form-control-lg" id="exampleFormControlInput1" value="<?php echo ($con - 1) . " Guias"; ?>" readonly>
                            <br><br>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- llamado de libreria de datatable -->
    <?php $this->load->view('lib/datatable_scritp'); ?>
    <script>
        $(document).ready(function() {
            $("#tbl_guias_no_lmd").DataTable({
                "responsive": true,
                "lengthChange": true,
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthMenu": [
                    [5, 10, 100, -1],
                    [5, 10, 100, "Todos"]
                ],
                "order": [
                    [0, 'desc']
                ],
                "language": {
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas"
                },
                "buttons": [{
                        extend: "excel",
                        text: "Exportar a Excel",
                        /* customize: function(doc) {
                             doc.content[1].table.body.forEach(function(row) {
                                 row.splice(15, 1);
                             });
                         }*/
                    },
                    {
                        extend: "pdf",
                        text: "Exportar a PDF",
                        orientation: "landscape", // Orientación horizontal
                        pageSize: "A4", // Tamaño de página A4
                        customize: function(doc) {

                            doc.styles.tableBodyEven = {
                                fontSize: '7',
                                fontFamily: 'Arial'
                            };
                            doc.styles.tableBodyOdd = {
                                fontSize: '7',
                                fontFamily: 'Arial'
                            };
                            // Cambiar el tamaño de la fuente del contenido del PDF
                            /* doc.styles.tableBody = {
                                 fontSize: '5' // Ajusta el tamaño de la letra aquí
                             };*/
                            // Cambiar el tamaño de la fuente de los encabezados de la tabla (opcional)
                            doc.styles.tableHeader = {
                                fontSize: 7, // Ajusta el tamaño de la letra del encabezado aquí
                                bold: true // Puedes hacerla negrita si lo deseas
                            };
                            doc.content[1].table.body.forEach(function(row) {
                                // Excluir la doceaba celda (columna) en cada fila (ajusta el índice según tu tabla)
                                row.splice(15, 1);
                            });
                        }
                    }
                ]
            }).buttons().container().appendTo('#tbl_guias_no_lmd_wrapper .col-md-6:eq(0)');
        });
    </script>
    <script>
        var input = document.getElementById("no_guia");

        // Execute a function when the user presses a key on the keyboard
        input.addEventListener("keypress", function(event) {
            // If the user presses the "Enter" key on the keyboard
            if (event.key === "Enter") {
                // Cancel the default action, if needed
                event.preventDefault();
                // Trigger the button element with a click
                document.getElementById("guardar_guia").click();
                //alert("estoy aqui");
            }
            document.getElementById('no_guia').addEventListener('focus');
            //document.getElementById("no_guia").focus();
        });
    </script>
</body>

</html>