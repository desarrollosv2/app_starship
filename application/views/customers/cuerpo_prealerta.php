<div class="container m-top">
    <div class="table-responsive">
        <table id="example" class="table   table-hover" style="width:99%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Numero de tracking</th>
                    <th>Compania courier</th>
                    <th>Tienda donde compraste</th>
                    <th>Valor del paquete (USD)</th>
                    <th>Describe tu paquete</th>
                    <th>Casillero</th>
                    <th>Factura</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="contenidoLista">
                <?php
                    if (isset($lista)) {
                        $c=1;
                        foreach ($lista as $row) {  ?>
                <tr>
                    <td><?php echo $c; ?></td>
                    <td><?php echo $row->ntracking; ?></td>
                    <td><?php echo $row->nombre_courier; ?></td>
                    <td><?php echo $row->tcompraste; ?></td>
                    <td><?php echo $row->vpaquete; ?></td>
                    <td><?php echo $row->desc_paquete; ?></td>
                    <td><?php echo $row->casillero;?></td>
                    <td>
                        <a target="_blank" id="<?php echo $row->id_prealert; ?>"
                            href="<?php echo base_url().$row->imgfactura; ?>"><i class="fa fa-download"
                                aria-hidden="true"></i></a>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-4">
                                <div class="checkbox icheck-primary">
                                    <input type="checkbox" class="my-2" id="check_tlc"
                                        <?php if ( $row->estado == 1){ echo 'checked';} ?> onclick="return false">
                                </div>
                            </div>
                            <div class="col-6">
                                <?php if($_SESSION['interno']==1){
                                    
                                    echo '<a href="#" class="btn btn-seconday btn-sm" onclick="confirmar_prealerta('.$row->id_prealert.')"
                                    title="Confirmar Pre Alerta" style="margin:0px; color:#FFF; background:#E9573F">
                                    <i class="fa fa-check"></i>
                                    </a>';
                                    }else{
                                    echo '<a href="#" class="btn btn-seconday btn-sm" onclick="modal_prealerta('.$row->id_prealert.')"
                                    title="Editar Pre Alerta" style="margin:0px; color:#FFF; background:#E9573F">
                                    <i class="fa fa-pencil"></i>
                                    </a>';
                                    }
                                    ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php $c=$c+1;
                        }
                    }
                    ?>
            </tbody>
        </table>
    </div>
</div>


<script>
$(document).ready(function() {
    $('#example').DataTable({
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