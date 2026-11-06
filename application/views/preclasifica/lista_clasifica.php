<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width; initial-scale=1.0">
    <!-- JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons (para exportar) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <style>
        body {
            font-family: 'Montserrat', Sans-serif;
        }

        h1 {

            font-family: 'Montserrat', Sans-serif;
            font-weight: bold;
        }

        table {
            margin: 0 auto
        }
    </style>

</head>

<body>
    <div class="container mx-auto">
        <div class=" table responsive">
            <table class="table  table-striped  table-hover " id="tabla" name="tabla">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>ID</th>
                        <th>Tracking </th>
                        <th>Manifiesto</th>
                        <th>Referencia</th>
                        <th>Usuario</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $c = 1;
                    session_start();
                    foreach ($guiamaster as $row) {
                    ?>
                        <tr style="background-color:white">
                            <?php
                            if ($row->estado == "R") {
                                echo "<td style='background-color:red;color:white;'> Pendiente </td>";
                            }
                            if ($row->estado == "V") {
                                echo "<td style='background-color:#28a745;color:white'> Recibido </td>";
                            } ?>
                            <td><?php echo $c; ?></td>
                            <td><?php echo $row->tracking_number; ?></td>
                            <td><?php //echo $row->manifiesto; 
                                ?></td>
                            <td><?php echo $row->referencia; ?></td>
                             <td><?php echo $row->user_name; ?></td>

                        </tr>

                    <?php
                        $c = $c + 1;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable("#tabla")) {
                $("#tabla").DataTable().destroy();
            }

            $("#tabla").DataTable({
                paging: true,
                pageLength: 10,
                searching: false,
                ordering: false,
                autoWidth: false,
                dom: 'Bfrtip', // Define la posición de los botones
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel',
                    title: 'Reporte_Warehouse', // Título del archivo Excel
                    className: 'btn btn-success', // Clases CSS para el botón
                    exportOptions: {
                        columns: ':visible' // Exporta solo columnas visibles
                    }
                }],
                language: {
                    lengthMenu: "Mostrar _MENU_ registros",
                    zeroRecords: "No se encontraron resultados",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "No hay registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    search: "Buscar:",
                    paginate: {
                        next: "Siguiente",
                        previous: "Anterior",
                    },
                }
            });
        });
    </script>
</body>

</html>