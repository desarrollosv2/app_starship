<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <title></title>
    <style>
        .table .th 
        {
            color: #fff;
            background-color: #f00;
        }
    </style>
</head>

<body>
<?php foreach ($duis as $row) : 
                            ?>
    <!--  PARA INGRESAR DATOS DE CLIENTES -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ingresar Comentario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" class="form-control" id="consignee_email" name="consignee_email" >
                <div class="modal-body">
                    <form method="$_POST" class="filtro_reporte" id="filtro_reporte">
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Correo</label>
                           
                            <input type="hidden" class="form-control" id="consignee_email" name="consignee_email" value="<?php echo $row->consignee_email ?>">
                            <?php endforeach ?>
                        </div>

                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Comentario</label>
                            <textarea class="form-control" name="comentario" id="comentario"></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardar_comentario();" data-dismiss="modal">Agregar</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</body>

</html>