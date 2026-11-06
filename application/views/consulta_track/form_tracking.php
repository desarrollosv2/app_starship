<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <title></title>
</head>

<body>
<?php $this->load->view("verifica");  ?>
    <div class="container-fluid">
        <div class="row bg-primary">

            <div class="col-sm-11 mt-3">

                <h2>Reporte de Tracking Enviados</h2>
            </div>
       

           <div class="col-sm-1 mt-2">

            <button type="button" class="btn btn-secondary btn-md" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Filtrar</button>

           </div>

       </div>
     </div>
     
    <div class="container-fluid m-top">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-hover table-light table-responsive-sm" id="tbl_guia_enviada">
                    <thead>

                    </thead>

                    <tbody id="tabla_envio_guia"></tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Buscar Por Fecha De Envio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form method="$_POST" class="fecha_envio" id="fecha_envio">
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="fecha_desde_envio" name="fecha_desde_envio">
                        </div>

                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fecha_hasta_envio" name="fecha_hasta_envio">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" onclick="buscar_fecha_envio_guia();" data-dismiss="modal">Buscar</button>

                </div>
            </div>
        </div>
    </div>



</body>

</html>