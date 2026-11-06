<div class="container m-top" >
        <div class="table-responsive">
            <table  id="warehouse2" class="table   table-hover">
                <thead>
                    <tr>
                        <th>idwarehouse</th>
                        <th>referencia</th>
                        <th>fecha_ingreso</th>
                        <th>fecha_salida</th>
                        <th>dias</th>
                        <th>remitente</th>
                        <th>destinatario</th>
                        <th>cuenta</th>
                        <th>po</th>
                        <th>inv</th>
                        <th>inv_value</th>
                        <th>descripcion</th>
                        <th>bultos</th>
                        <th>peso</th>
                        <th>volumen</th>
                        <th>peso_tasable</th>
                        <th>ubicacion</th>
                        <th>tracking</th>
                        <th>file</th>
                        <th>usuario</th>
                        <th>linea</th>
                        <th>instr</th>
                        <th>insurance</th>
                        <th>total</th>
                        <!--th>Acciones</th-->
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>    
                    <?php
                    if (isset($lista)) {
                    foreach ($lista as $row) { ?>
                    <tr>
                        <td><?php echo $row->idwarehouse; ?></td>
                        <td><?php echo $row->referencia; ?></td>
                        <td><?php echo $row->fecha_ingreso; ?></td>
                        <td><?php echo $row->fecha_salida; ?></td>
                        <td><?php echo $row->dias; ?></td>
                        <td><?php echo $row->remitente; ?></td>
                        <td><?php echo $row->destinatario; ?></td>
                        <td><?php echo $row->cuenta; ?></td>
                        <td><?php echo $row->po; ?></td>
                        <td><?php echo $row->inv; ?></td>
                        <td><?php echo $row->inv_value; ?></td>
                        <td><?php echo $row->descripcion; ?></td>
                        <td><?php echo $row->bultos; ?></td>
                        <td><?php echo $row->peso; ?></td>
                        <td><?php echo $row->volumen; ?></td>
                        <td><?php echo $row->peso_tasable; ?></td>
                        <td><?php echo $row->ubicacion; ?></td>
                        <td><?php echo $row->tracking; ?></td>
                        <td><?php echo $row->file; ?></td>
                        <td><?php echo $row->usuario; ?></td>
                        <td><?php echo $row->linea; ?></td>
                        <td><?php echo $row->instr; ?></td>
                        <td><?php echo $row->insurance; ?></td>
                        <td><?php echo $row->total; ?></td>
                        <td>
                                <a href="#" class="btn btn-warning btn-sm"
                                onclick="consulta_guias(<?php echo trim($row->referencia); ?> )" title="Listado de Guias"
                                style="margin:0px;">
                                <i class="fa fa-list"></i></a>
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
$(document).ready(function() {    
    $('#warehouse2').DataTable({
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
                "sLast":"Último",
                "sNext":">>",
                "sPrevious": "<<"
             },
             "sProcessing":"Procesando...",
        }
    });     
});
</script>
