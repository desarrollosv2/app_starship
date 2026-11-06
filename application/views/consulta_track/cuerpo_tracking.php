<div class="container-fluid m-top">
<?php $this->load->view("verifica");  ?>
    <div class="table-responsive">
        <table id="tbl-manifiesto" class="table custom-table">

            <thead>

                <tr>
                        <th>N</th>
                        <th>Fecha</th>
                        <th>Tracking Number</th>
                        <th>Sucursal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //##F0A8AF
                $arr_ciudades_colores = array(
                    'NO' => 'color:#ccc;',
                    'SI' => 'green'
                );
                $i = 1;
                if (isset($tracking_send)) {
                    foreach ($tracking_send as $row) { ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $row->fecha_envio ?></td>
                            <td><?php echo $row->tracking_number ?></td>
                            <td><?php echo $row->nombre_agencia ?></td>
                           


                        </tr>
                <?php
                        $i = $i + 1;
                    }
                }
                ?>
            </tbody>
        </table>
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


<!-- AdminLTE App -->

<script>

    $(document).ready(function() {
       

       
        $('#tbl-manifiesto').DataTable({
            "paging": true,
            dom: 'Bfrtip',
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": ">>",
                "sPrevious": "<<"
            },
            "sProcessing": "Procesando...",
            buttons: [

                {
                    extend: 'excelHtml5',
                    text: 'Exportar',
                    className: 'exportButton',
                  //  title: nombre_archivo,
                   

                },
               

             

            ],

            //COLUMNAS QUE SE VAN A EXPORTAR AL PDF
            //exportOptions: {
            // columns: '1,3,4,5,6,7'
            //}
        }).buttons().container().appendTo('#tbl-manifiesto_wrapper .col-md-6:eq(0)');
        $(".buttons-html5").addClass("btn");
        $(".buttons-html5").addClass("btn-success");

    });



  
</script>
