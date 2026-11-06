<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
    <title></title>
    <style>
        #table_wh {
            overflow-y: scroll;
        }

        #mensajes {
            height: auto;
        }

        .tcenter {
            text-align: center;
        }

        .td {
            background-color: #fff;
        }

        body {
            font-family: "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            background-color: #fff;
            font-weight: 300;
        }

        p {
            color: #b3b3b3;
            font-weight: 300;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .h1,
        .h2,
        .h3,
        .h4,
        .h5,
        .h6 {
            font-family: "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        a {
            -webkit-transition: .3s all ease;
            -o-transition: .3s all ease;
            transition: .3s all ease;
        }

        a,
        a:hover {
            text-decoration: none !important;
        }

        .content {
            padding: 7rem 0;
        }

        h2 {
            font-size: 20px;
        }

        .custom-table {
            min-width: 900px;
        }

        .custom-table thead tr,
        .custom-table thead th {
            border-top: none;
            border-bottom: none !important;
        }

        .custom-table tbody th,
        .custom-table tbody td {
            color: #777;
            font-weight: 400;
            padding-bottom: 20px;
            padding-top: 20px;
            font-weight: 300;
        }

        .custom-table tbody th small,
        .custom-table tbody td small {
            color: #b3b3b3;
            font-weight: 300;
        }

        .custom-table tbody tr th,
        .custom-table tbody tr td {
            position: relative;
            -webkit-transition: .3s all ease;
            -o-transition: .3s all ease;
            transition: .3s all ease;
        }

        .custom-table tbody tr th:before,
        .custom-table tbody tr th:after,
        .custom-table tbody tr td:before,
        .custom-table tbody tr td:after {
            -webkit-transition: .3s all ease;
            -o-transition: .3s all ease;
            transition: .3s all ease;
            content: "";
            left: 0;
            right: 0;
            position: absolute;
            height: 1px;
            background: #007bff;
            width: 100%;
            opacity: 0;
            visibility: hidden;
        }

        .custom-table tbody tr th:before,
        .custom-table tbody tr td:before {
            top: -1px;
        }

        .custom-table tbody tr th:after,
        .custom-table tbody tr td:after {
            bottom: -1px;
        }

        .custom-table tbody tr:hover th,
        .custom-table tbody tr:hover td {
            background: rgba(0, 123, 255, 0.03);
        }

        .custom-table tbody tr:hover th:before,
        .custom-table tbody tr:hover th:after,
        .custom-table tbody tr:hover td:before,
        .custom-table tbody tr:hover td:after {
            opacity: 1;
            visibility: visible;
        }

        .custom-table tbody tr.active th,
        .custom-table tbody tr.active td {
            background: rgba(0, 123, 255, 0.03);
        }

        .custom-table tbody tr.active th:before,
        .custom-table tbody tr.active th:after,
        .custom-table tbody tr.active td:before,
        .custom-table tbody tr.active td:after {
            opacity: 1;
            visibility: visible;
        }

        /* Custom Checkbox */
        .control {
            display: block;
            position: relative;
            margin-bottom: 25px;
            cursor: pointer;
            font-size: 18px;
        }

        .control input {
            position: absolute;
            z-index: -1;
            opacity: 0;
        }

        .control__indicator {
            position: absolute;
            top: 2px;
            left: 0;
            height: 20px;
            width: 20px;
            border-radius: 4px;
            border: 2px solid #ccc;
            background: transparent;
        }

        .control--radio .control__indicator {
            border-radius: 50%;
        }

        .control:hover input~.control__indicator,
        .control input:focus~.control__indicator {
            border: 2px solid #007bff;
        }

        .control input:checked~.control__indicator {
            border: 2px solid #007bff;
            background: #007bff;
        }

        .control input:disabled~.control__indicator {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
            border: 2px solid #ccc;
        }

        .control__indicator:after {
            font-family: 'icomoon';
            content: '\e5ca';
            position: absolute;
            display: none;
        }

        .control input:checked~.control__indicator:after {
            display: block;
            color: #fff;
        }

        .control--checkbox .control__indicator:after {
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -52%);
            -ms-transform: translate(-50%, -52%);
            transform: translate(-50%, -52%);
        }

        .control--checkbox input:disabled~.control__indicator:after {
            border-color: #7b7b7b;
        }

        .control--checkbox input:disabled:checked~.control__indicator {
            background-color: #007bff;
            opacity: .2;
            border: 2px solid #007bff;
        }
    </style>
</head>

<body>
    <?php //$this->load->view('hd'); 
    ?>


    <div class="container-fluid m-top ">
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>

                    <tr>
                        <th scope="col ">
                            <label class="control control--checkbox">
                                <input type="checkbox" class="js-check-all" />
                                <div class="control__indicator"></div>
                            </label>
                        </th>

                        <th scope="col">#</td>
                        <th scope="col">MAWB</td>
                        <th scope="col">Bag Number</td>
                        <th scope="col">Etd</td>
                        <th scope="col">Eta</td>
                        <th scope="col">Order Number</td>
                        <th scope="col">Tracking Number</td>
                        <th scope="col">Origin</td>
                        <th scope="col">Destination</td>
                        <th scope="col">Consignee Account</td>
                        <th scope="col">Consignee</td>
                        <th scope="col">Consignee Address1</td>
                        <th scope="col">Consignee Address2</td>
                        <th scope="col">Consignee Address3</td>
                        <th scope="col">Consignee Neighborhood </td>
                        <th scope="col">Consignee City</td>
                        <th scope="col">Consignee_state</td>
                        <th scope="col">Consignee_zip </td>
                        <th scope="col">Consignee_country</td>
                        <th scope="col">Consignee_email</td>
                        <th scope="col">Consignee_phone</td>
                        <th scope="col">Consignee_mobile</td>
                        <th scope="col">Consignee_taxid </td>
                        <th scope="col">Pieces</td>
                        <th scope="col">Gweight</td>
                        <th scope="col">Cweight</td>
                        <th scope="col">Ceight_type</td>
                        <th scope="col">Height</td>
                        <th scope="col">Length</td>
                        <th scope="col">Width </td>
                        <th scope="col">Commodity</td>
                        <th scope="col">Value</td>
                        <th scope="col">Freight</td>
                        <th scope="col">Currency</td>
                        <th scope="col">Service type</td>
                        <th scope="col">Service level</td>
                        <th scope="col">Shipper account</td>
                        <th scope="col">Shipper name</td>
                        <th scope="col">Shipper address1</td>
                        <th scope="col">Shipper address2</td>
                        <th scope="col">Shipper city</td>
                        <th scope="col">shipper state</td>
                        <th scope="col">shipper zip</td>
                        <th scope="col">shipper country</td>
                        <th scope="col">shipper email</td>
                        <th scope="col">shipper phone</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($lista)) {
                        $c = 1;
                        foreach ($lista as $row) { ?>
                            <tr>
                                <td scope="row">
                                    <label class="control control--checkbox">
                                        <input type="checkbox" />
                                        <div class="control__indicator"></div>
                                    </label>
                                </td>
                                <td scope="row"><?php echo $c; ?></td>
                                <td scope="row"><?php echo $row->mawb; ?></td>
                                <td scope="row"><?php echo $row->bag_number; ?></td>
                                <td scope="row"><?php echo $row->etd; ?></td>
                                <td scope="row"><?php echo $row->esta; ?></td>
                                <td scope="row"><?php echo $row->order_number; ?></td>
                                <td scope="row"><?php echo $row->tracking_number; ?></td>
                                <td scope="row"><?php echo $row->origin; ?></td>
                                <td scope="row"><?php echo $row->destination; ?></td>
                                <td scope="row"><?php echo $row->consignee_account; ?></td>
                                <td scope="row"><?php echo $row->consignee; ?></td>
                                <td scope="row"><?php echo $row->consignee_address1; ?></td>
                                <td scope="row"><?php echo $row->consignee_address2; ?></td>
                                <td scope="row"><?php echo $row->consignee_address3; ?></td>
                                <td scope="row"><?php echo $row->consignee_neighborhood; ?></td>
                                <td scope="row"><?php echo $row->consignee_city; ?></td>
                                <td scope="row"><?php echo $row->consignee_state; ?></td>
                                <td scope="row"><?php echo $row->consignee_zip; ?></td>
                                <td scope="row"><?php echo $row->consignee_country; ?></td>
                                <td scope="row"><?php echo $row->consignee_email; ?></td>
                                <td scope="row"><?php echo $row->consignee_phone; ?></td>
                                <td scope="row"><?php echo $row->consignee_mobile; ?></td>
                                <td scope="row"><?php echo $row->consignee_taxid; ?></td>
                                <td scope="row"><?php echo $row->pieces; ?></td>
                                <td scope="row"><?php echo $row->gweight; ?></td>
                                <td scope="row"><?php echo $row->cweight; ?></td>
                                <td scope="row"><?php echo $row->weight_type; ?></td>
                                <td scope="row"><?php echo $row->height; ?></td>
                                <td scope="row"><?php echo $row->length; ?></td>
                                <td scope="row"><?php echo $row->width; ?></td>
                                <td scope="row"><?php echo $row->commodity; ?></td>
                                <td scope="row"><?php echo $row->value; ?></td>
                                <td scope="row"><?php echo $row->freight; ?></td>
                                <td scope="row"><?php echo $row->currency; ?></td>
                                <td scope="row"><?php echo $row->service_type; ?></td>
                                <td scope="row"><?php echo $row->service_level; ?></td>
                                <td scope="row"><?php echo $row->shipper_account; ?></td>
                                <td scope="row"><?php echo $row->shipper_name; ?></td>
                                <td scope="row"><?php echo $row->shipper_address1; ?></td>
                                <td scope="row"><?php echo $row->shipper_address2; ?></td>
                                <td scope="row"><?php echo $row->shipper_city; ?></td>
                                <td scope="row"><?php echo $row->shipper_state; ?></td>
                                <td scope="row"><?php echo $row->shipper_zip; ?></td>
                                <td scope="row"><?php echo $row->shipper_country; ?></td>
                                <td scope="row"><?php echo $row->shipper_email; ?></td>
                                <td scope="row"><?php echo $row->shipper_phone; ?></td>


                                <td>
                                    <a href="#" class="btn btn-default btn-sm" onclick="lista_servicios_express(<?php echo "'$row->order_number'"; ?>)" title="Crear Guia Express" style="margin:0px;">
                                        <i class="fa fa-paperclip"></i></a>
                                </td>


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
    <div class="modal fade" id="guia_express_modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Crear Guia C807Express </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form enctype="multipart/form-data" class="guia_ex" id="guia_ex" action="javascript:crear_guia_express();">
                    <div class="modal-body">

                        <input type="text" id="order_number" name="order_number">
                        <input type="text" id="ndepto" name="ndepto">
                        <input type="text" id="nmunic" name="nmunic">
                        <input type="text" id="nservi" name="nservi">

                        <div class="form-group">
                            <label for="">Servicio:</label>
                            <div id="listado_servicios">

                            </div>

                            <label for="">Departamento :</label>
                            <div id="listado_departamentos">

                            </div>

                            <label for="" id="lmunicipio" name="lmunicipio">Municipio:</label>
                            <div id="listado_municipios">

                            </div>

                        </div>

                        <div class="form-group">
                            <label for="direccion">Dirección:</label>
                            <input type="text" class="form-control" id="direccion" placeholder="Introduzca dirección" name="direccion" required>
                            <div class="valid-feedback">Valid.</div>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>


                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                        </button>
                        <button type="submit" class="btn btn-primary float-md-right">Guardar</button>

                    </div>
                </form>

            </div>
        </div>
    </div>


    <!--- RETORNO INFO guias en Express-->
    <div class="modal fade" id="info_modal_express" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Crear Guia C807Express </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>


                <form action="">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <div class="form-group tcenter">
                                    <h4 id="mensaje" name="mensaje"></h4>


                                </div>
                                <div class="form-group tcenter">

                                    <h4 id="recolecta"></h4>

                                </div>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                    </button>
                </div>
            </div>


        </div>
    </div>
    </div>


    <script>
        listado_warehouse()
    </script>

    <script>
        $(document).ready(function() {
            $('#warehouse').DataTable({
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
                        "sLast": "Último",
                        "sNext": ">>",
                        "sPrevious": "<<"
                    },
                    "sProcessing": "Procesando...",
                }
            });

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



        });
    </script>

    <script>
        $(function() {

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



        });
    </script>
</body>

</html>