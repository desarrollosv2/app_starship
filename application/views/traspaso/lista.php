<div class="container" ><br>
    <div class="table-responsive">
        <table id="traspaso" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Guia Master</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <form enctype="multipart/form-data" class="lguias">
                </form>
                <?php 
                if (isset($preclasifica)) {
                    foreach ($preclasifica as $row) {
                        $manifiesto = $row->manifiesto; 
                        $cadena="'$manifiesto'";
                        ?>
                        <tr>
                            <td><?php echo $row->idpreclasificacion; ?></td>
                            <td><?php echo $row->manifiesto; ?></td>
                            <td><?php echo $row->fecha; ?></td>
                            <td><?php echo $row->descripcion; ?></td>
                            <td>
                                <a href='#Modal_Add_Guia' onclick="mostrar_referencia()"  class="btn btn-primary btn-sm" data-id=""
                                title="Agregar Guia" data-toggle="modal" style="margin:0px;"
                                data-book-id="<?php  echo $row->idpreclasificacion;?>" data-book-id1="<?php echo $row->manifiesto;?>"
                                data-book-id2="<?php echo $row->referencia;?>">
                                <i class="fa fa-plus"></i></a>
                                <a href='#' class="btn btn-danger btn-sm" data-toggle="modal"  style="margin:0px;"
                                title="Eliminar Referencia"
                                onclick="confirmar_eliminar_referencia(<?php echo $row->idpreclasificacion; ?>);return false;">
                                <i class="fa fa-trash"></i> </a>
                                <a href="#" class="btn btn-warning btn-sm" onclick="consulta_guias_t('<?php echo trim($row->manifiesto);?>')"
                                    title="Listado de Guias"  style="margin:0px;">
                                    <i class="fa fa-list"></i></a>
                                <a href="#" class="btn btn-primary btn-sm"
                                onclick="pdf_inventario(<?php echo $cadena; ?>,<?php echo "'ID-TRASPASO'"; ?> )"
                                title="Imprimir" style="margin:0px;">
                                <i class="fa fa-print"></i></a>
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


<script>
//t_preclasifica_lista()
</script>
<script>
$(document).ready(function() {
    $('#traspaso').DataTable({
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