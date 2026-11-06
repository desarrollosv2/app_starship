
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>

    </style>
    <style>
        table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
}
    </style>
</head>

<body>

  
<div class="container" ><br>
    <div class="table-responsive">
        <table id="poliza" class="table table-hover">
            <thead>
                <tr>
                    <th>Poliza</th>
                    <th>Warehouse</th>
                    <th>Nombre</th>
                    <th>Monto Factura</th>
                    <th>Piezas</th>
                    <th>Retenido en Aduna</th>
                    <th>Peso Neto</th>
                    <th>Peso Volumen</th>
                    <th>Peso Toma</th>
                    <th>Código Tarifa</th>
                    <th>Cargo Básico</th>
                    <th>Corte</th>
                    <th>Manejo de Transporte</th>
                    <th>Descuento</th>
                    <th>SED</th>
                    <th>Seguro</th>
                    <th>Entrega Local</th>
                    <th>Trámite Adunal</th>
                    <th>Rayos X</th>
                    <th>IVA Vta. Propia</th>
                    <th>Cepa</th>
                    <th>Total</th>
                    <th>Orden de Compra</th>
                    <th>impuesto</th>
                     <th>Permiso</th>

                </tr>
            </thead>
            <tbody>
                <form enctype="multipart/form-data" class="lguias">
                </form>
                <?php 
                if (isset($lista_poliza)) {
                    foreach ($lista_poliza as $row) {?>
                        <tr>
                            <td><?php echo $row->poliza; ?></td>
                            <td><?php echo $row->guia; ?></td>
                            <td><?php echo $row->nombre; ?></td>
                            <td><?php echo $row->montofactura; ?></td>
                            <td><?php echo $row->piezas; ?></td>
                            <td><?php echo $row->ret_en_adn; ?></td>
                            <td><?php echo $row->peso_neto; ?></td>
                            <td><?php echo $row->peso_volumen; ?></td>
                            <td><?php echo $row->pesotomar; ?></td>
                            <td><?php echo $row->codtarifa; ?></td>
                            <td><?php echo $row->cargobasico; ?></td>
                            <td><?php echo $row->corte; ?></td>
                            <td><?php echo $row->manejo_trans; ?></td>
                            <td><?php echo $row->descuento; ?></td>
                            <td><?php echo $row->sed; ?></td>
                            <td><?php echo $row->seguro; ?></td>
                            <td><?php echo $row->entrega_local; ?></td>
                            <td><?php echo $row->tramite_aduanal; ?></td>
                            <td><?php echo $row->rayosx; ?></td>
                            <td><?php echo $row->iva_vta_propia; ?></td>
                            <td><?php echo $row->cepa; ?></td>
                            <td><?php echo $row->total; ?></td>
                            <td><?php echo $row->ot; ?></td>
                            <td><?php echo $row->imp; ?></td>
                            <td><?php echo $row->permiso; ?></td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>
//t_preclasifica_lista()
</script>

<script>
$(document).ready(function() {
    $('#poliza').DataTable({
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
</body>
</html>