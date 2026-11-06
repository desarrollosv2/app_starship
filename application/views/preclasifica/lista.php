
<form enctype="multipart/form-data" class="lguias"></form>
<div class="container m-top">
    <div class="table-responsive">
        <table  id="tbl_clasifica_t" class="table   table-hover " style="width:99%" > 
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Guia Master</th>
                    <th>Poliza</th>
                    <th>Fecha</th>
                    <th>Referencia</th>
                    <th>paquetes</th>
                    <th>Sacos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (isset($preclasifica)) {
                        foreach ($preclasifica as $row) { ?>
                <tr>
                    <td><?php echo $row->idpreclasificacion; ?></td>
                    <td><?php echo $row->manifiesto; ?></td>
                    <td><?php echo $row->poliza; ?></td>
                    <td><?php echo $row->fecha; ?></td>
                    <td><?php echo $row->referencia; ?></td>
                    <td><?php echo $row->paquetes; ?></td>
                    <td><?php echo $row->sacos; ?></td>
                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <a href='#Modal_Add_Guia' onclick="mostrar_referencia()" class="btn btn-primary btn-sm"
                                    data-id="" title="Agregar Guia" data-toggle="modal" style="margin:0px;"
                                    data-book-id="<?php  echo $row->idpreclasificacion;?>"
                                    data-book-id1="<?php echo $row->manifiesto;?>"
                                    data-book-id2="<?php echo $row->referencia;?>">
                                    <i class="fa fa-plus"></i></a>
                            </div>
                            <div class="col-md-2">
                                <a href='#' class="btn btn-danger btn-sm" data-toggle="modal" style="margin:0px;"
                                    title="Eliminar Referencia"
                                    onclick="confirmar_eliminar_referencia(<?php echo $row->idpreclasificacion; ?>);return false;">
                                <i class="fa fa-trash"></i></a>
                            </div>
                            <div class="col-md-2">
                                <a href="#" class="btn btn-secondary btn-sm"
                                    onclick="consulta_guias(<?php echo trim("'$row->referencia'"); ?> )"
                                                                     
                                    title="Listado de Guias" style="margin:0px;">
                                    <i class="fa fa-list"></i></a>
                            </div>
                            <div class="col-md-2">
                                <a href='#Modal_Add_Poliza' onclick="mostrar_poliza()" class="btn btn-primary btn-sm"
                                    data-id="" data-toggle="modal" title=" Agregar Póliza" style="margin:0px;"
                                    data-book-id="<?php  echo $row->manifiesto;?>"
                                    data-book-id1="<?php echo $row->referencia;?>">
                                    <i class="fa fa-file-text"></i></a>
                            </div>
                            <div class="col-md-2">
                                <a href='#' class="btn btn-info btn-sm" data-toggle="modal" style="margin:0px;"
                                    title="procesado"
                                    onclick="procesando_paquetes(<?php echo $row->idpreclasificacion; ?>);return false;">
                                <i class="fa fa-lock"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
                        }
                        }
                ?>
            </tbody>
        </table>
    </div>
</div>


<!--script src="https://code.jquery.com/jquery-3.1.1.min.js"></script-->
<script>
//preclasifica_lista()
</script>
<script>
$(document).ready(function() {
    $('#tbl_clasifica_t').DataTable({
        //para cambiar el lenguaje a español
          "order": [[ 3, "desc" ]],
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
