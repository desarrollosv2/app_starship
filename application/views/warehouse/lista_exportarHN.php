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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.5/xlsx.full.min.js"></script>

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
        <h1>
            HONDURAS
        </h1>
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
                <table class="table table-hover" id="tbl_detalle" name="tbl_detalle">
                    <thead>

                        <tr>
                            <th scope="col">Cantidad</th>
                            <th scope="col">POS.Arancelaria</th>
                            <th scope="col">Unidad </th>
                            <th scope="col">Designación</th>
                            <th scope="col">Carcteristicas</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Modelo</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Origen</th>
                            <th scope="col">FOB</th>
                            <th scope="col">FLETE</th>
                            <th scope="col">Seguro</th>
                            <th scope="col">O.Gastos</th>
                            <th scope="col">Total FOB</th>
                            <th scope="col">Peso </th>

                        </tr>
                    </thead>
                    <tbody id="awb_detalle">
                        <?php
                        if (isset($lista)) {
                            $c = 1;
                            foreach ($lista as $row) { ?>
                                <tr class="rw ">
                                    <td scope="row"><?php echo $row->pieces ?></td>
                                    <td scope="row"><?php echo $row->numero_partida;  ?></td>
                                    <td scope="row"><?php echo ''; ?></td>
                                    <td scope="row"><?php echo $row->descripcion; ?></td>
                                    <td scope="row"><?php echo $row->descripcion_generica; ?></td>
                                    <td scope="row"><?php echo $row->marca; ?></td>
                                    <td scope="row"><?php echo $row->modelo; ?></td>
                                    <td scope="row"><?php echo $row->id_estado; ?></td>
                                    <td scope="row"><?php echo $row->id_origen; ?></td>
                                    <td scope="row"><?php echo $row->value; ?></td>
                                    <td scope="row"><?php echo ''; ?></td>
                                    <td scope=""><?php echo ''; ?></td>
                                    <td scope="row"><?php echo ''; ?></td>
                                    <td scope="row"><?php echo $row->value; ?></td>
                                    <td scope="row"><?php echo $row->gweight; ?></td>

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
           <!-- datos pra segunda hoja en archivo de excel -->
        <div class="container-fluid m-top ">

            <div class="table-responsive">
                <table class="table table-hover" id="tbl_detalle_2" name="tbl_detalle">
                    <thead>

                        <tr>
                            <th scope="col">Cantidad</th>
                            <th scope="col">POS.Arancelaria</th>
                            <th scope="col">Unidad </th>
                            <th scope="col">Designación</th>
                            <th scope="col">Carcteristicas</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Modelo</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Origen</th>
                            <th scope="col">FOB</th>
                            <th scope="col">FLETE</th>
                            <th scope="col">Seguro</th>
                            <th scope="col">O.Gastos</th>
                            <th scope="col">Total FOB</th>
                            <th scope="col">Peso </th>

                        </tr>
                    </thead>
                    <tbody id="awb_detalle">
                        <?php
                        if (isset($lista)) {
                            $c = 1;

                          
$palabra_anterior = '';
$contador = 0;

                            foreach ($lista as $row) {
                                if ($row->numero_partida !== $palabra_anterior) {
                                    if ($palabra_anterior !== '') {
                                      //  echo  "$contador\n";
                                      //  echo "elmer"."$row->numero_partida\n";
                                    }
                                    $palabra_anterior = $row->numero_partida;
                                 
                                    ?>
                                    <tr class="rw ">
                                        <td><?php echo $contador ;?></td>
                                    </tr>
                                    <tr class="rw ">
                                        <td><?php echo "" ;?></td>
                                    </tr>
                                    <?php
                                       $contador = 1;
                                } else {
                                    $contador++;
                                }
                               
                           
                            
                            // Imprimir el contador para la última palabra
                          //  echo $contador;
                               // $suma_cantidad = $suma_cantidad + $row->pieces;
                        ?>
                                <tr class="rw ">
                                    <td scope="row"><?php echo $row->pieces ?></td>
                                    <td scope="row"><?php echo $row->numero_partida;  ?></td>
                                    <td scope="row"><?php echo ''; ?></td>
                                    <td scope="row"><?php echo $row->descripcion; ?></td>
                                    <td scope="row"><?php echo $row->descripcion_generica; ?></td>
                                    <td scope="row"><?php echo $row->marca; ?></td>
                                    <td scope="row"><?php echo $row->modelo; ?></td>
                                    <td scope="row"><?php echo $row->id_estado; ?></td>
                                    <td scope="row"><?php echo $row->id_origen; ?></td>
                                    <td scope="row"><?php echo $row->value; ?></td>
                                    <td scope="row"><?php echo ''; ?></td>
                                    <td scope=""><?php echo ''; ?></td>
                                    <td scope="row"><?php echo ''; ?></td>
                                    <td scope="row"><?php echo $row->value; ?></td>
                                    <td scope="row"><?php echo $row->gweight; ?></td>

                                </tr>

                        <?php
                                $c = $c + 1;
                            }
                            ?>
                            <tr class="rw ">
                                <td><?php echo $contador ;?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="ContPDF" style="display: none;"> </div>


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
                $('[data-toggle="tooltip"]').tooltip();
            });

            $('#catalogo_ref').on('change', function() {
                id = $("#id-master").val();
                master = $("#name_master").val();
                ref = this.value;

                if (ref == 'Todo') {
                    lista_warehouse(id, master, 0)
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
                let date = new Date();
                let day = date.getDate();
                let month = date.getMonth() + 1;
                let year = date.getFullYear();
                let fecha = month < 10 ? `${day}-0${month}-${year}` : `${day}-${month}-${year}`;

                var table = $('#tbl_detalle').DataTable({
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
                    }, {
                        width: "300px",
                        targets: 1
                    }],
                    buttons: [{
                        extend: 'excelHtml5',
                        text: 'Exportar',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
                            format: {
                                body: function(data, row, column, node) {
                                    return data;
                                }
                            }
                        },
                        customize: function(xlsx) {
                            // Obtener la primera hoja existente
                            var sheet1 = xlsx.xl.worksheets['sheet1.xml'];

                            // Crear un nuevo libro
                            var wb = XLSX.utils.book_new();

                            // Crear la primera hoja basada en la tabla exportada
                            var ws1 = XLSX.utils.table_to_sheet(document.getElementById('tbl_detalle'));
                            var ws2 = XLSX.utils.table_to_sheet(document.getElementById('tbl_detalle_2'));

                            // Crear la segunda hoja con datos personalizados
                            // var ws2 = XLSX.utils.aoa_to_sheet([
                            //     ["Second Sheet Content"],
                            //     ["More Data", "More Data"]
                            // ]);

                            // Añadir ambas hojas al libro
                            XLSX.utils.book_append_sheet(wb, ws1, "First Sheet");
                            XLSX.utils.book_append_sheet(wb, ws2, "Second Sheet");

                            // Guardar el archivo Excel con ambas hojas
                            XLSX.writeFile(wb, 'Manifiesto_HN.xlsx');
                        }
                    }, ]
                });

                $(".buttons-html5").addClass("btn");
                $(".buttons-html5").addClass("btn-success");
            });
        </script>


</body>

</html>