<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>



<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

<!-- DataTables -->

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

   
    <style>
        .tooltip-inner {
            background-color: #0F4C5C !important;
            color: #0F4C5C;
        }

        .bs-tooltip-top .arrow::before,
        .bs-tooltip-auto[x-placement^="top"] .arrow::before {
            border-top-color: #0F4C5C !important;
        }

        .bs-tooltip-right .arrow::before,
        .bs-tooltip-auto[x-placement^="right"] .arrow::before {
            border-right-color: #0F4C5C !important;
        }


        .bs-tooltip-bottom .arrow::before,
        .bs-tooltip-auto[x-placement^="bottom"] .arrow::before {
            border-bottom-color: #0F4C5C !important;
        }


        .bs-tooltip-left .arrow::before,
        .bs-tooltip-auto[x-placement^="left"] .arrow::before {
            border-left-color: #0F4C5C !important;
        }

        .modal-text-header {
            color: #F15A29;

        }
    </style>





</head>

<body>

    <div class="container-fluid">

        <div class="row mt-2 ml-5 ">
            <div class="form-group mt-4">
                <h2 id="master-number" name="master-number"></h2>
                <input type="hidden" id="id-master">
                <input type="hidden" id="name_master">
            </div>
        <hr class="borde-orange mt-0">

    </div>
    <input type="hidden" id="opc-clasif">
    <div class="container" id="detalle-partida" style="display:none">

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <div class="form-group">
                    <?php $this->load->view("catalogos/partidas", $this->datos); ?>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary btn-sm " data-toggle="tooltip" data-placement="bottom" title="" onclick="asignar_partida_paquete()">Guardar</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="bottom" title="" onclick="ocultar_detalle_partida()">Cancelar</button>
                    </div>

                </div>
            </div>
            <div class="col-sm-2"></div>

        </div>


    </div>
    <div class="container-fluid m-top ">

        <div class="table-responsive">
            <table class="table table-hover" id="tbl_detalle"  name ="tbl_detalle">
                <thead>

                    <tr>
                        <th scope="col">Loading Guide</th>
                        <th scope="col">Date </th>
                        <th scope="col">Warehouse </th>
                        <th scope="col"> Shipper's ID</th>
                        <th scope="col">Shipper</th>
                        <th scope="col">Shipper's Address</th>
                        <th scope="col">Shipper's City</th>
                        <th scope="col">Shipper's State</th>
                        <th scope="col">Shipper's Zip</th>
                        <th scope="col">Consignee's ID</th>
                        <th scope="col">Consignee</th>
                        <th scope="col">Consignee's Address</th>
                        <th scope="col">Consignee's City</th>
                        <th scope="col">Consignee's State</th>
                        <th scope="col">Consignee's Zip </th>
                        <th scope="col">Consignee's Box Number</th>
                        <th scope="col">Consignee's Email</th>
                        <th scope="col">Consignee's Phone</th>
                        <th scope="col">Consignee's Mobile</th>
                        <th scope="col">Pieces</th>
                        <th scope="col" class="font-weight-bold">Gross Weight</th>
                        <th scope="col" class="font-weight-bold">Chargeable Weight</th>
                        <th scope="col" class="font-weight-bold">Volume</th>
                        <th scope="col" class="font-weight-bold">Description</th>
                        <th scope="col" class="font-weight-bold">Invoice Number</th>
                        <th scope="col" class="font-weight-bold">Invoice Amount</th>
                        <th scope="col" class="font-weight-bold">Description of Packages</th>
                        <th scope="col" class="font-weight-bold">Charges</th>

                    

                    </tr>
                </thead>
                <tbody id="awb_detalle">
                    <?php
                    if (isset($lista)) {
                        $c = 1;
                        foreach ($lista as $row) { ?>
                            <tr class="rw ">
                                <td scope="row"><?php echo $row->manifiesto ?></td>
                                <td scope="row"><?php echo  date("d/m/Y", strtotime($row->fecha)); ?></td>
                                <td scope="row"><?php echo $row->tracking_number; ?></td>
                                <td scope="row"><?php echo '';?></td>
                                <td scope="row"><?php echo ' MailAmeericas';?></td>
                                <td scope="row"><?php echo $row->shipper_address1;?></td>
                                <td scope="row"><?php echo $row->shipper_city;?></td>
                                <td scope="row"><?php echo $row->shipper_state;?></td>
                                <td scope="row"><?php echo $row->shipper_zip;?></td>
                                <td scope="row"><?php echo $row->consignee_account; ?></td>
                                <td scope="row"><?php echo $row->consignee; ?></td>
                                <td scope=""><?php echo $row->consignee_address1; ?></td>
                                <td scope="row"><?php echo $row->consignee_city; ?></td>
                                <td scope="row"><?php echo $row->consignee_state; ?></td>
                                <td scope="row"><?php echo $row->consignee_zip; ?></td>
                                <td scope="row"><?php echo '' ?></td>
                                <td scope="row"><?php echo $row->consignee_email; ?></td>
                                <td scope="row"><?php echo $row->consignee_phone; ?></td>
                                <td scope="row"><?php echo $row->consignee_mobile; ?></td>
                                <td scope="row"><?php echo 1 ?></td>
                                <td scope="row"><?php echo $row->gweight * 2.2046; ?></td>
                                <td scope="row"><?php echo $row->cweight; ?></td>
                                <td scope="row"><?php echo 0; ?></td>
                                <td scope="row"><?php echo $row->commodity; ?></td>
                                <td scope="row"><?php echo ''; ?></td>
                                <td scope="row"><?php echo $row->value; ?></td>
                                <td scope="row"><?php echo ''; ?></td>
                                <td scope="row"><?php echo ''; ?></td>    

        </tr>
<?php
                            $c = $c + 1;
                        }
                    }
?>
</tbody>
</table>
    </div>
    </div>


    <!--- modal para mostrar guias en Express-->



    <!-- modal  para agregar manifiesto-->
  

    <!-- modal  para agregar paquetes a referencia-->
   

    <!-- modal mostrar referencia referencia-->
   
    <!--- RETORNO INFO guias en Express-->
    
    <!-- modal  para multiplicar  paquetes-->
  
    
    <!-- actualizar manifiesto -->


    <div id="ContPDF" style="display: none;">
    </div>



    <script>
        // listado_warehouse()
    </script>

    <script>
        $(document).ready(function() {



            $('.js-check-all').on('click', function() {

                if ($(this).prop('checked')) {
                    $('th input[type="checkbox"]').each(function() {
                        $(this).prop('checked', true);
                        $(this).closest('tr').addClass('active');
                    })
                } else {
                    $('th input[type="checkbox"]').each(function() {
                        $(this).prop('checked', false);
                        $(this).closest('tr').removeClass('active');
                    })
                }

            });

            $('th[scope="row"] input[type="checkbox"]').on('click', function() {
                if ($(this).closest('tr').hasClass('active')) {
                    $(this).closest('tr').removeClass('active');
                } else {
                    $(this).closest('tr').addClass('active');
                }
            });

            $(document).ready(function() {
                $("#buscar").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $("#awb_detalle tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });

            });

            /** ara determinar si esta seleccinando checknox */
            //     var checkbox = document.getElementById('chk1');
            //      checkbox.addEventListener("change", validaCheckbox, false);

            //      function validaCheckbox() {
            //          var checked = checkbox.checked;
            //          if (checked) {
            //              alert('checkbox1 esta seleccionado');
            //           } else {
            //  alert('checkbox1 esta sin seleccionado');
            //          }
            //       }



        });


        /**Fin */
        function verificar_id(id) {

            nombre = "chk" + id;
            opc = 0;
            if ($('#' + nombre).is(':checked')) {
                $('#valor').val(1);
                //  alert(nombre+' esta Seleccionado');
                opc = 1;

            } else {
                $('#valor').val(0);
                //  alert(nombre+' esta off');
                opc = 0;

            }
            var url = base_url("index.php/WhController/seleccionar_item/" + id + "/" + opc);
            $.get(url, function(data) {

            });



        }
    </script>

    <script>
        $(document).ready(function() {
            
        });

        $('#catalogo_ref').on('change', function() {
            id = $("#id-master").val();
            master = $("#name_master").val();
            ref = this.value;

            if (ref == 'Todo') {
                lista_warehouse(id, master,0)
            } else {
                if (ref == 'Pendiente') {
                    lista_referencia(id, master, ref)
                } else {
                    lista_referencia(id, master, ref)
                }


            }

        });
    </script>

    



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
    $(document).ready(function() {

        let date = new Date()
        fecha = "";
        let day = date.getDate();
        let month = date.getMonth() + 1;
        let year = date.getFullYear();

        if (month < 10) {
            fecha = `${day}-0${month}-${year}`;
        } else {
            fecha = `${day}-${month}-${year}`;
        }

        var enca = "coloque aqui el dia";
        var usuario = $("#usuario").val();

        var origen = $("#hub-origen").val();
        var destino = $("#hub-destino").val();

        $('#tbl_detalle').DataTable({
            "retrieve": true,
            "paging": true,
            "info": false,
            dom: 'Bfrtip',
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": ">>",
                "sPrevious": "<<"
            },
            "sProcessing": "Procesando...",
            "oLanguage": {

                "sSearch": "Buscar:",

            },
            columnDefs: [{
                    width: "100px",
                    targets: 0
                },
                {
                    width: "300px",
                    targets: 1
                },

            ],
            buttons: [

                {
                    extend: 'excelHtml5',
                    text: 'Exportar',
                    exportOptions: {
                        columns: [0, 1, 2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27],
                        format: {
                            body: function(data, row, column, node) {
                                return data;
                            }
                        }
                    },
                },

               

            ]

            //COLUMNAS QUE SE VAN A EXPORTAR AL PDF
            //exportOptions: {
            // columns: '1,3,4,5,6,7'
            //}
        }).buttons().container().appendTo('#tbl_detalle_wrapper .col-md-6:eq(0)');
        $(".buttons-html5").addClass("btn");
        $(".buttons-html5").addClass("btn-success");




    });
    </script>


</body>

</html>