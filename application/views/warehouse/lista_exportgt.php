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


        <form style="display:none;" method="post"
            enctype="multipart/form-data"
            action="<?= site_url('zip_splitter/procesar') ?>">

            <div class="form-group">
                <label>ZIP Original</label>
                <input type="file"
                    name="zip_original"
                    accept=".zip"
                    required
                    class="form-control">
            </div>

            <div class="form-group">
                <label>JSONs</label>
                <input type="file"
                    name="jsons[]"
                    accept=".json"
                    multiple
                    required
                    class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">
                Procesar
            </button>

        </form>

        <div class="row mt-2 ml-5 " style="display:none;">
            <div class="form-group mt-4">
                <h2 id="master-number" name="master-number"></h2>
                <input type="text" id="id-master" value="<?= $id_master ?>">
                <input type="text" id="name_master" value="<?= $name_master ?>">
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

        <div class="bg-white rounded shadow-sm p-3 mb-3">

            <div class="d-flex align-items-center justify-content-between flex-wrap">

                <div>
                    <h5 class="mb-0">
                        <i class="fa fa-box"></i>
                        <?= $name_master ?>
                    </h5>



                </div>

                <div class="mt-2 mt-md-0">

                    <button class="btn btn-light border mr-2"
                        onclick="lista_warehouse(<?= $id_master ?>,'<?= $name_master ?>',0)">
                        <i class="fa fa-arrow-left"></i>
                    </button>

                    <button class="btn btn-primary mr-2"
                        onclick="lista_warehouseexport()">
                        <i class="fa fa-sync-alt"></i>
                        Actualizar
                    </button>

                    <button class="btn btn-success mr-2"
                        onclick="clasificar_master(<?= $id_master ?>,'<?= $name_master ?>',0)">
                        <i class="fa fa-check-circle"></i>
                        Clasificar
                    </button>

                    <button class="btn btn-dark"
                        onclick="generar_zip_facturas()">
                        Exportar Facturas
                    </button>

                    <button class="btn btn-dark"
                        onclick="generar_zip_facturas_x_lote()">
                        Exportar Facturas por Lote
                    </button>

                    <a target="__blank" href="https://stargateserp.com/app_starship/index.php/WhController/exportar_manifiesto_x_lote_3/<?= $id_master ?>/<?= $name_master ?>" class="btn btn-dark">
                        Exportar manifiesto por Lote
                    </a>

                </div>

            </div>

        </div>

        <div class="bg-white rounded shadow-sm p-3 mb-3">

            <div class="d-flex align-items-center justify-content-around flex-wrap">

                <div>
                    <h5 class="mb-0">
                        Valores recibidos
                    </h5>

                </div>

                <div class="mt-2 mt-md-0">
                    <input type="hidden" class="form-control input-sm" placeholder="$ Flete Recibido" id="txtFleteRecibido" />
                </div>

                <div class="mt-2 mt-md-0">
                    <input type="text" class="form-control input-sm" placeholder="Kg Peso Recibido" id="txtPesoRecibido" />
                </div>

                <div class="mt-2 mt-md-0">
                    <button class="btn btn-success" onclick="proratear_master(<?= $id_master ?>)">Aplicar prorateo</button>
                </div>

            </div>


        </div>

        <div class="bg-white rounded shadow-sm p-3 mb-3">





            <div class="container-fluid m-top ">

                <div class="table-responsive">
                    <table class="table table-hover" id="tbl_detalle" name="tbl_detalle">
                        <thead>

                            <tr>
                                <th scope="col">Guia</th>
                                <th scope="col">Embarcador/Proveedor</th>

                                <th scope="col">Consignatario</th>
                                <th scope="col">Dirección Consignatario</th>
                                <th scope="col">NIT Consignatario</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Peso</th>
                                <th scope="col">Piezas</th>
                                <th scope="col">Flete</th>
                                <th scope="col">Valor Mercaderia</th>

                                <th scope="col">Inciso Arancelario</th>
                                <th scope="col">% Arancelario</th>
                                <th scope="col">Gastos</th>
                                <th scope="col">Ticket/Documento</th>
                                <th scope="col">Dim </th>
                                <th scope="col">Peso Api</th>
                                <th scope="col">Value Api</th>
                                <th scope="col">Seguro Calculado</th>



                            </tr>
                        </thead>
                        <tbody id="awb_detalle">
                            <?php


                            function limpiarTexto($texto)
                            {
                                // Si detecta caracteres típicos de mala codificación
                                if (preg_match('/Ã|Â|â/', $texto)) {
                                    $convertido = @utf8_decode($texto);

                                    // Solo reemplaza si la conversión no produjo caracteres inválidos
                                    if (strpos($convertido, '�') === false) {
                                        return $convertido;
                                    }
                                }

                                return $texto;
                            }



                            function review_letters($value)
                            {
                                return str_replace(
                                    [
                                        'á',
                                        'é',
                                        'í',
                                        'ó',
                                        'ú',
                                        'Á',
                                        'É',
                                        'Í',
                                        'Ó',
                                        'Ú',
                                        'ñ',
                                        'Ñ'
                                    ],
                                    [
                                        'A',
                                        'E',
                                        'I',
                                        'O',
                                        'U',
                                        'A',
                                        'E',
                                        'I',
                                        'O',
                                        'U',
                                        'N',
                                        'N'
                                    ],
                                    $value
                                );
                            }





                            if (isset($lista)) {
                                $c = 1;
                                foreach ($lista as $row) {



                                    $nombre_cliente = $row->consignee;





                                    $direccion_cliente = $row->consignee_address1;
                                    if (isset($flag_temu)) {
                                        if ($flag_temu == 1) {

                                            $busqueda = $row->package_number;
                                            $nombre_cliente = '';
                                            $documento_cliente = '';

                                            if (isset($decrypted)) {
                                                foreach ($decrypted as $item) {
                                                    if ($item->package_number === $busqueda) {
                                                        $nombre_cliente = $item->consignee;
                                                        $direccion_cliente = $item->consignee_address1;
                                                        break; // Detiene el foreach al encontrar el primero
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $nombre_cliente = review_letters(limpiarTexto($nombre_cliente));

                                    $pesokm = (float) $row->gweight;
                                    $valorFlete = calcularValorFlete($pesokm);
                                    $embarcador = '';
                                    $tm = 0;
                                    if ($row->id_empresa == 112 || $row->id_empresa == 113 || $row->id_empresa == 114) {
                                        $embarcador = "TEMU";
                                        $tm = 1;
                                    }


                                    if ($row->id_empresa == 109 || $row->id_empresa == 108 || $row->id_empresa == 100 || $row->id_empresa == 101 || $row->id_empresa == 102) {
                                        $embarcador = "ALIEXPRESS";
                                    }



                                    $direccion_cliente = review_letters($direccion_cliente);

                                    $direccion_cliente = iconv('UTF-8', 'ASCII//TRANSLIT', $direccion_cliente);

                                    $direccion_cliente = preg_replace('/[^A-Za-zÁÉÍÓÚáéíóú0-9\-\s]/u', '', $direccion_cliente);

                                    $direccion_cliente = strtoupper(trim($direccion_cliente));


                                    if ($tm == 1) {
                                        $listado_productos = implode(
                                            ',',
                                            array_unique(
                                                array_map('trim', explode(',', $row->commodity))
                                            )
                                        );
                                    } else {
                                        $listado_productos = $row->commodity;

                                        $listado_productos = iconv('UTF-8', 'ASCII//TRANSLIT', $listado_productos);

                                        $listado_productos = preg_replace('/[^A-Za-zÁÉÍÓÚáéíóú0-9\-\s]/u', ' ', $listado_productos);

                                        $listado_productos = implode(
                                            ' ',
                                            array_unique(
                                                array_map('trim', explode(' ', $listado_productos))
                                            )
                                        );

                                        $listado_productos = strtoupper(trim($listado_productos));
                                    }


                                    $listado_productos = review_letters($listado_productos);



                                    


                            ?>

                                    <tr class="rw ">
                                        <td scope="row"><?= ($tm == 1) ? $row->package_number : $row->tracking_number ?></td>
                                        <td scope="row"><?php echo strtoupper(trim($embarcador));  ?></td>
                                        <td scope="row"><?= ($tm == 1) ? strtoupper(trim($nombre_cliente)) : strtoupper(trim($nombre_cliente)) ?></td>

                                        <td scope="row"><?php echo $direccion_cliente; ?></td>

                                        <td scope="row"><?php echo ''; ?></td>

                                        <td scope="row"><?php echo strtoupper($listado_productos); ?></td>

                                        <td scope="row"><?php echo ($tm == 1) ? $row->peso_prorateado_gt : $row->peso_prorateado_gt; ?></td>
                                        <td scope="row"><?php echo '1'; ?></td>
                                        <td scope="row"><?php echo ($tm == 1) ? $row->flete_gt : number_format($valorFlete, 2); ?></td>
                                        <td scope="row"><?php echo ($tm == 1) ? $row->fob_gt : $row->value; ?></td>

                                        <td scope="row" style="font-weight:bold;"><?php echo $row->partida; ?></td>

                                        <td scope="row" style="font-weight:bold;"><?php echo $row->porcentaje_dai; ?></td>
                                        <td scope="row"><?php echo '0'; ?></td>
                                        <td scope="row"><?php echo ''; ?></td>
                                        <td scope="row"><?php echo ''; ?></td>
                                        <td scope="row"><?php echo $row->gweight; ?></td>
                                        <td scope="row"><?php echo $row->value; ?></td>
                                        <td scope="row"><?php echo $row->seguro_gt; ?></td>




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
                        "ordering": false,
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
                                title: null,
                                text: 'Exportar Manifiesto',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17],
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

            <?php

            function calcularValorFlete(float $pesokm): float
            {
                if ($pesokm <= 0) {
                    return 0;
                }

                $precioPorKg = 1.75;
                $tramos = ceil($pesokm);

                return $tramos * $precioPorKg;
            }

            ?>


</body>

</html>