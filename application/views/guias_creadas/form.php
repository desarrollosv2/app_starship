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
    <div class="container">
        <div class="row">

            <div class="col">
                <br>
                <h2>Reporte de Tracking Replace</h2>
            </div>

            <div class="col">
                <br>
                <button type="button" class="btn btn-dark btn-lg" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Filtrar</button>

                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Buscar Por Fecha De Creacion</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form method="$_POST" class="fecha_comprobante" id="fecha_comprobante">
                                    <div class="form-group">
                                        <label for="message-text" class="col-form-label">Fecha Desde</label>
                                        <input type="date" class="form-control" id="fecha_desde_guia" name="fecha_desde_guia">
                                    </div>

                                    <div class="form-group">
                                        <label for="message-text" class="col-form-label">Fecha Hasta</label>
                                        <input type="date" class="form-control" id="fecha_hasta_guia" name="fecha_hasta_guia">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" onclick="buscar_fecha_creacion_guia();" data-dismiss="modal">Buscar</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid m-top">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-hover table-light table-responsive-sm" id="tbl_crear_guia">
                            <thead>
                                <tr>
                                    <th>N</th>
                                    <th>Fecha</th>
                                    <th>Master</th>
                                    <th>Tracking Number</th>
                                    <th>Tracking Replace</th>
                                    <th>Cliente</th>
                                </tr>
                            </thead>

                            <tbody id="tabla_crea_guia"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
 <script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
     <script src="<?php echo base_url(); ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
      <script src="<?php echo base_url(); ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
       <script src="<?php echo base_url(); ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
         <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
          <script src="<?php echo base_url(); ?>assets/plugins/jszip/jszip.min.js"></script>
           <script src="<?php echo base_url(); ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
            <script src="<?php echo base_url(); ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
             <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
              <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
               <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
</html>