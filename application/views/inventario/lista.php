<div class="container m-top">
        <div class="table-responsive">
            <table id="tbl_inventario" class="table   table-hover" style="width:99%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Master</th>
                        <th>Fecha</th>
                        <th>Descripcion</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="contenidoLista">
                    <?php
                    if (isset($inventarios)) { $c=1;
                        foreach ($inventarios as $row) { 
                            $manifiesto=$row->manifiesto;
                            $cadena="'$manifiesto'";?>
                            
                    <tr>
                        <td><?php    echo $c ?></td>
                        <td><?php echo $row->manifiesto; ?></td>
                        <td><?php echo $row->fecha; ?></td>
                        <td><?php echo $row->descripcion; ?></td>

                        <td>
                            <div class="row">
                               
                                <div class="col-md-3">
                                    <a href='#' class="btn btn-danger btn-sm" data-toggle="modal" style="margin:0px;"
                                        title="Eliminar Referencia"
                                        onclick="confirmar_eliminar_referencia(<?php echo $row->idpreclasificacion; ?>);return false;">
                                        <i class="fa fa-trash"></i> </a>
                                </div>

                                <div class="col-md-3">
                                    <a href="#" class="btn btn-secondary btn-sm"
                                        onclick="consulta_guias_i(<?php echo trim("'$row->manifiesto'"); ?>)"
                                         title="Listado de Guias" style="margin:0px;">
                                        <i class="fa fa-list"></i></a>
                                </div>
                               
                                <div class="col-md-3">
                                    <a href="#" class="btn btn-primary btn-sm"
                                        onclick="pdf_inventario(<?php echo $cadena; ?>,<?php echo "'ID-INVENTARIO'"; ?> )"
                                        title="Imprimir" style="margin:0px;">
                                        <i class="fa fa-print"></i></a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $c=$c+1;}}
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!--- fin modal para mostrar guias -->

    <script>
    $(document).ready(function() {
        $('#tbl_inventario').DataTable({
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
                    "sNext": ">>",
                    "sPrevious": "<<"
                },
                "sProcessing": "Procesando...",
            }
        });
    });
    </script>