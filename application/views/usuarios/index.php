<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">   
<title>Usuarios</title>
  



</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-10 text-left mt-4">
                <h5 class="text-secondary">Gestión de usuarios</h5>
            </div>
            <div class="col-sm-2 text-right">
                <a href="<?php echo site_url('usuarios/create'); ?>" class="btn btn-primary btn-md " role="button" aria-pressed="true"> <i class="fa fa-user-plus"></i> Agregar</a>
            </div>
        </div>
        <div class="row">
            <div class="col">

                <table class="table table-condensed table-bordered table-hover" id="userTable">
                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <?php foreach ($usuarios as $usuario) : ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td><?php echo $usuario['correo']; ?></td>
                            <td>
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="<?php echo site_url('usuarios/edit/' . $usuario['id']);   ?>" title="Editar"> <i class="fa fa-pencil text-primary"></i></a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?php echo site_url('usuarios/delete/' . $usuario['id']); ?>" title="Eliminar"> <i class="fa fa-trash text-danger"></i></a>

                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?php echo site_url('usuarios/permisos/' . $usuario['id']); ?>" title="Permisos"> <i class="fa fa-list text-secondary"></i></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

    </div>

    <script>
        $(document).ready(function() {
           // document.onload = pageLoaded();
            $('#userTable').DataTable({
                //para cambiar el lenguaje a español
                "order": [
                    [3, "desc"]
                ],
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
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                }
            });
        });
    </script>
   
</body>

</html>