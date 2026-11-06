<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <title>Reporte de Manifiesto</title>
    
    <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        $idpreclasificacion                = 0;
    ?>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <br>
                <h2>Reporte de Manifiesto</h2>
            </div>

            <div class="col">
                <br>
                <?php
                    if (isset($lista)) 
                    {
                        foreach ($lista as $row) 
                        { 
                            $idpreclasificacion   =   $row->idpreclasificacion;
                        }
                    }
                ?>

                <button type="button" class="btn btn-dark btn-lg" onclick="reporte_guias(<?php echo $idpreclasificacion; ?>)"> 
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button>
            </div>

            <div class="container-fluid m-top">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-hover table-light table-responsive-sm" id="tbl_listadoguias">
                            <thead>
                                <tr>
                                    <th>N</th>
                                    <th>AWB</th>
                                    <th>Tracking Number</th>
                                    <th>Tracking Replace</th>
                                    <th>Cliente</th>
                                    <th>Tipo Entrega</th>
                                    <th>Tipo Servicio</th>
                                    <th>Estatus</th>
                                    <th>Estado</th>
                                    <th>Cobro Final</th>   
                                </tr>
                            </thead>

                            <tbody id="tabla_tracking_guia">
                            <?php
                                if (isset($lista)) 
                                {
                                    $c = 1;
                                    foreach ($lista as $row) 
                                    { 
                            ?>
                                        <tr class="rw">
                                            <td scope="row" style="margin-left: 9px"><?php echo $c; ?></td>
                                            <td scope="row"><?php echo $row->awb; ?></td>
                                            <td scope="row"><?php echo $row->tracking_number; ?></td>
                                            <td scope="row"><?php echo $row->tracking_replace; ?></td>
                                            <td scope="row"><?php echo $row->consignee; ?></td>
                                            <td scope="row"><?php echo $row->tipo_entrega; ?></td>
                                            <td scope="row"><?php echo $row->tipo_servicio; ?></td>
                                            <td scope="row"><?php echo $row->estatus; ?></td>
                                            <td scope="row"><?php echo $row->estado; ?></td>
                                            <td scope="row"><?php echo '$'.$row->cobro_final; ?></td>
                                <?php    
                                    } 
                                ?>

                            <?php    
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script>
       $(document).ready(function() 
    {
        $("#tbl_listadoguias").DataTable({
                "paging"       : true,
                "searching"    : true,
                "ordering"     : true,
                "info"         : true,
                "language"     : 
            {
                "lengthMenu"   : "Mostrar _MENU_ registros por página",
                "zeroRecords"  : "No se encontraron datos que mostrar",
                "info"         : "Mostrando Página _PAGE_ de _PAGES_",
                "infoEmpty"    : "registros no disponibles",
                "infoFiltered" : "(filtered from _MAX_ total records)",
                "sSearch"      : "Buscar:",
                "oPaginate"    : 
              {
                "sFirst"       : "Primero",
                "sLast"        : "Último",
                "sNext"        : "Siguiente",
                "sPrevious"    : "Anterior",
              },
            },
                "lengthChange" : true,
                "buttons"      : ["excel", "colvis"]
        }).buttons().container().appendTo('#tbl_listadoguias_wrapper .col-md-6:eq(0)');
    });
    </script>
</body>
</html>