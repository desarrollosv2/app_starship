<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    -->
    <!-- jsf
    <script src="https://demos.codexworld.com/convert-html-to-pdf-using-javascript-jspdf/js/html2canvas.min.js"></script>
    <script src="https://demos.codexworld.com/3rd-party/jsPDF-2.5.1/dist/jspdf.umd.js"></script>
 -->

    <style>
        .modal-text-header {
            color: #F15A29;

        }

        .dropdown-options {
            display: none;
            position: relative;
            overflow: auto;
        }

        .dropdown:hover .dropdown-options {
            display: block;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .custom-scroll {
            max-height: calc(100vh - 100px);
            /* Ajusta el alto para que sea responsivo */
            overflow-y: auto;
            /* Habilita scroll vertical */
            border: 1px solid #ddd;
            /* Opcional */
        }

        .table-responsive {
            overflow-x: auto;
            /* Mantiene la funcionalidad de scroll horizontal de Bootstrap */
        }

        /* Encabezado fijo */
        .sticky-header th {
            position: sticky;
            top: 0;
            /* Fija el encabezado en la parte superior */

            /* Fondo para que sea visible */
            z-index: 2;
            /* Mantiene el encabezado sobre las filas */

            /* Sombra opcional */
            text-align: center;
            /* Opcional: alinea texto al centro */
            background-color: #F4F6F9;
        }

        /* Scrollbar personalizado */
        .custom-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #808285;
            background-color: #FE4F00;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }

        .toolbar-card {
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
        }

        .search-modern .input-group-text {
            background: #FE4F00;
            color: #fff;
            border: none;
        }

        .search-modern input {
            border-left: none;
            height: 46px;
        }

        .search-modern input:focus {
            box-shadow: none;
            border-color: #FE4F00;
        }

        .btn-action {

            margin: 4px;
            border-radius: 12px;
            padding: 10px 18px;
            transition: .25s;
            font-weight: 600;

        }

        .btn-action i {

            margin-right: 6px;

        }

        .btn-action:hover {

            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .18);

        }

        .btn-primary {
            background: #FE4F00;
            border-color: #FE4F00;
        }

        .btn-primary:hover {

            background: #df4500;
            border-color: #df4500;

        }

        .toolbar-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .08);
        }

        .search-modern .input-group-text {
            background: #FE4F00;
            border: none;
            color: #fff;
        }

        .search-modern input {
            border-left: none;
            height: 45px;
        }

        .search-modern input:focus {
            box-shadow: none;
            border-color: #FE4F00;
        }

        .toolbar-btn {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            margin: 4px;
            transition: .25s;
        }

        .toolbar-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
        }

        .toolbar-btn i {
            font-size: 17px;
        }

        .action-btn {

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 8px;

            margin: 5px;

            padding: 10px 18px;

            border-radius: 12px;

            font-weight: 600;

            transition: .25s;

            min-width: 140px;

        }

        .action-btn i {

            font-size: 18px;

        }

        .action-btn:hover {

            transform: translateY(-3px);

            box-shadow: 0 10px 20px rgba(0, 0, 0, .18);

        }

        .btn-primary {

            background: #FE4F00;
            border-color: #FE4F00;

        }

        .btn-primary:hover {

            background: #e24700;
            border-color: #e24700;

        }

        .btn-action {
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 18px;
            transition: all .25s ease;
        }

        .btn-action i {
            font-size: 16px;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .15);
        }
    </style>

</head>

<body>
    <?php
    $id_manifiesto = $encabezado->idpreclasificacion;
    $empresas = [112, 113, 114];
    $master = $encabezado->manifiesto;
    ?>

    <div class="container-fluid" id="enca">

        <div class="row mt-2 ml-5 ">
            <div class="form-group mt-3">
                <h2 class="text-muted" id="master-number" name="master-number"></h2>
                <input type="hidden" id="id-master">
                <input type="hidden" id="name_master">
                <input type="hidden" id="correla">
            </div>

            <?php
            if ($_SESSION["client_code"] == 107) {
            } else {
            ?>
                <div class="col-12 mb-3">

                    <div class="card shadow-sm border-0 toolbar-card">
                        <div class="card-body py-3">

                            <div class="row align-items-center">

                                <!-- Referencia -->
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
                                    <?php $this->load->view("catalogos/referencia", $this->datos); ?>
                                </div>

                                <!-- Buscador -->
                                <div class="col-xl-3 col-lg-3 col-md-4 mb-2">
                                    <div class="input-group search-modern">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>

                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Buscar..."
                                            id="buscar"
                                            name="buscar">
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-12 mt-2 mt-xl-0">

                                    <div class="d-flex flex-wrap justify-content-xl-end justify-content-center">

                                        <button class="btn btn-primary action-btn"
                                            onclick="modal_referencia()">

                                            <i class="fas fa-plus-circle"></i>
                                            <span>Crear Referencia</span>

                                        </button>

                                        <button class="btn btn-info action-btn"
                                            onclick="reference_list()">

                                            <i class="fas fa-list"></i>
                                            <span>Listado Referencia</span>

                                        </button>

                                        <button class="btn btn-success action-btn"
                                            onclick="lista_warehouseexport()">

                                            <i class="fas fa-file-excel"></i>
                                            <span>Ver Manifiesto</span>

                                        </button>

                                        <button class="btn btn-danger action-btn"
                                            onclick="imprimir_referencia()">

                                            <i class="fas fa-file-pdf"></i>
                                            <span>PDF</span>

                                        </button>

                                        <button class="btn btn-warning action-btn"
                                            onclick="imprimir_facturas()">

                                            <i class="fas fa-receipt"></i>
                                            <span>Facturas</span>

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            <?php } ?>

        </div>

        <hr class="borde-orange mt-0">

        <div class="container-fluid m-top " id="contenido-individual">

        </div>

    </div>


    <input type="hidden" id="opc-clasif">
    <div class="container" id="detalle-partida" style="display:none">

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <div class="form-group">
                    <?php $this->load->view("catalogos/partidas", $this->datos); ?>
                    <div class="text-center">
                        <button type="button" class="btn custom-btn-outline  " data-toggle="tooltip" data-placement="bottom" title="" onclick="asignar_partida_paquete()">Guardar</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="bottom" title="" onclick="ocultar_detalle_partida()">Cancelar</button>

                    </div>

                </div>
            </div>
            <div class="col-sm-2"></div>

        </div>


    </div>


    <div class="container-fluid">
        <div class="row">
            <!-- verificar deptos y municipios -->

            <?php
            if ($_SESSION["client_code"] == 107) {
            } else {
            ?>

                <div class="col-5">

                    <button type="button"
                        class="btn btn-outline-primary btn-action"
                        onclick="verificar_lm()">
                        <i class="fas fa-map-marked-alt mr-2"></i>
                        Asignar Municipios IA
                    </button>


                    <div class="col-auto" style="display:none;">
                        <button type="button"
                            class="btn btn-outline-secondary btn-action"
                            onclick="quiantities_add()">
                            <i class="fas fa-boxes mr-2"></i>
                            Quantities
                        </button>
                    </div>


                    <button type="button"
                        class="btn btn-outline-success btn-action"
                        data-toggle="modal"
                        data-target="#verdeModal">
                        <i class="fas fa-check-circle mr-2"></i>
                        Asignar Paquetes Selectivo Verde
                    </button>

                </div>
            <?php
            }

            ?>


            <div class="col-sm-4">


            </div>

            <div class="col-3">



                <button style="display:none;" type="button"
                    class="btn btn-outline-primary action-btn mr-2 mb-2"
                    onclick="generar_grafica_estatus(<?= $id_manifiesto ?>)">

                    <i class="fas fa-chart-bar"></i>
                    <span>Estatus</span>

                </button>

                <button type="button"
                    class="btn btn-outline-success action-btn mr-2 mb-2"
                    data-toggle="modal"
                    data-target="#confirmacionModal">

                    <i class="fas fa-sync-alt"></i>
                    <span>Recibir Status Gacela</span>

                </button>

                <button type="button"
                    class="btn btn-outline-secondary action-btn mb-2"
                    onclick="vista_lista_awb()">

                    <i class="fas fa-arrow-left"></i>
                    <span>Regresar</span>

                </button>



            </div>

        </div>

    </div>


    </div>

    <div class="container-fluid m-top " id="contenido-principal">

        <div class="table-responsive custom-scroll">
            <table class="table custom-table" id="tbl_detalle">
                <thead class="sticky-header">
                    <tr>
                        <th scope="col" style="display:none">idguia</th>
                        <th scope="col">#</th>
                        <th scope="col">Imagenes</th>
                        <?php

                        if ($_SESSION["client_code"] == 107) {
                        } else {
                        ?>
                            <th scope="col">Editar</td>
                            <?php
                        }

                            ?>


                            <th scope="col" style="display: none;">
                                <label class="control control--checkbox">
                                    <input type="checkbox" class="js-check-all" />
                                    <div class="control__indicator"></div>
                                </label>
                            </th>

                            <th scope="col" id="chk_select_e" style="display:none; background: #0F4C5C;"></th>
                            <th scope="col"></th> <!--validar recolecta -->
                            <th scope="col">Cliente</th>
                            <th scope="col">Referencia</th>
                            <th scope="col">Tracking Number</th>
                            <th scope="col">Package Number</th>
                            <th scope="col">Tracking Replace</th>
                            <th scope="col">Weight Enviado</th>
                            <th scope="col">Weight Prorateado</th>
                            <th scope="col">Value</th>
                            <th scope="col">FOB</th>
                            <th scope="col">Flete</th>
                            <th scope="col">Seguro</th>
                            <th scope="col">Items</th>
                            <th scope="col">Items Description</th>
                            <th scope="col">Partida</th>
                            <th scope="col">Buyer id</th>
                            <th scope="col">Buyer</th>
                            <th scope="col">Buyer address1</th>
                            <th scope="col">Buyer city</th>
                            <th scope="col">Buyer state</th>
                            <th scope="col">Buyer phone</th>
                            <th scope="col">Buyer email</th>
                            <th scope="col">HTS</th>
                            <th scope="col">Pieces</th>
                            <th scope="col" class="font-weight-bold">Partida</th>
                            <th scope="col" class="font-weight-bold">Producto</th>
                            <th scope="col" class="font-weight-bold">Departamento</th>
                            <th scope="col" class="font-weight-bold">Municipio</th>

                            <?php
                            if ($_SESSION["client_code"] == 107) {
                            } else {
                            ?>
                                <th scope="col" class="font-weight-bold">Tipo entrega</th>
                                <th scope="col" class="font-weight-bold">Tipo servicio</th>
                                <th scope="col" class="font-weight-bold">Cobro Final</th>
                                <th scope="col" class="font-weight-bold">Recolecta</th>
                                <th scope="col" class="font-weight-bold">Guia</th>
                            <?php
                            }
                            ?>


                            <th scope="col" class="font-weight-bold">POD</th>
                            <th scope="col" class="font-weight-bold">Estado</th>
                            <th scope="col" class="font-weight-bold">Sobrante</th>
                            <th scope="col" class="font-weight-bold">Bag Number</th>

                    </tr>
                </thead>
                <tbody id="awb_detalle">
                    <?php
                    if (isset($lista)) {
                        $c = 1;
                        foreach ($lista as $row) {
                    ?>
                            <tr class="rw">
                                <td scope="row" style="display:none"><?php echo $row->id; ?></td>
                                <td><?php echo $c; ?></td>

                                <td class="bg-white" style="width: 40px; display:none">
                                    <label class="control control--checkbox">
                                        <input type="checkbox" id="<?php echo 'chk' . $row->id ?>" <?php if ($row->seleccionado == 1) {
                                                                                                        echo 'checked';
                                                                                                    } ?> onclick="verificar_id(<?php echo $row->id; ?>)">
                                        <div class="control__indicator"></div>
                                    </label>
                                </td>

                                <td class="bg-white" style="width: 40px; display:none;" class="ck" id="chk_select_d">
                                    <label class="control control--checkbox">
                                        <input type="checkbox" id="<?php echo 'chk_d' . $row->id ?>" <?php if ($row->clasificado == 1) {
                                                                                                            echo 'checked';
                                                                                                        } ?>>
                                        <div class="control__indicator"></div>
                                    </label>
                                </td>

                                <?php
                                if ($_SESSION["client_code"] == 107) {
                                } else {
                                ?>
                                    <td>
                                        <a href="#" class="btn btn-secundary btn-md mt-0 " Onclick="ver_contenido_tracking(<?php echo $row->id; ?>)" title="Ver Contenido" style="margin:0px;">
                                            <i class="fas fa-images text-secondary"></i></a>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-secundary btn-md mt-0 " Onclick="editar_descripcion(<?php echo "'$row->id'"; ?>)" title="Editar descripción" style="margin:0px;">
                                            <i class="fas fa-edit text-secondary"></i></a>
                                    </td>
                                    <td>
                                        <a href="#"
                                            class="btn btn-secundary btn-md mt-0"
                                            onclick="get_recolecta('<?php echo $row->id; ?>', '<?php echo $row->tracking_number; ?>', '<?php echo $row->id_manifiesto; ?>')"
                                            title="Validar Recolecta"
                                            style="margin:0px;">
                                            <i class="fas fa-check text-<?php echo ($row->pickup_number > 0) ? 'success' : 'danger'; ?>"></i>
                                        </a>


                                    </td>
                                <?php




                                    if ($tm == 1) {
                                        $gt_value = $this->db
                                            ->select('flete_gt,fob_gt,seguro_gt,peso_prorateado_gt')
                                            ->where('id', $row->id)
                                            ->where('id_manifiesto', $id_manifiesto)
                                            ->get('manifiesto')
                                            ->row();

                                        //var_dump($gt_value);
                                    }
                                }


                                ?>

                                <td><?php echo $row->client_id; ?></td>
                                <td><?php echo $row->referencia; ?></td>
                                <td><?php echo $row->tracking_number; ?></td>
                                <td><?php echo $row->package_number; ?></td>
                                <td><?php echo $row->tracking_replace; ?></td>
                                <td><?php echo $row->gweight; ?></td>
                                <td><?php echo $gt_value->peso_prorateado_gt; ?></td>
                                <td><?php echo $row->value; ?></td>
                                <td><?php echo number_format(($gt_value->fob_gt), 2, '.', ''); ?></td>
                                <td><?php echo number_format(($gt_value->flete_gt), 2, '.', ''); ?></td>
                                <td><?php echo number_format(($gt_value->seguro_gt), 2, '.', ''); ?></td>
                                <td><?php echo $row->items; ?></td>
                                <td style="width:100px"><?php echo $row->commodity; ?></td>
                                <td><?php echo $row->id_partida; ?></td>
                                <td><?php echo $row->consignee_account; ?></td>
                                <td><?php echo $row->consignee; ?></td>
                                <td><?php echo $row->consignee_address1; ?></td>
                                <td><?php echo $row->consignee_city; ?></td>
                                <td><?php echo $row->consignee_state; ?></td>
                                <td><?php echo $row->consignee_phone; ?></td>
                                <td><?php echo $row->consignee_email; ?></td>
                                <td><?php echo $row->hts; ?></td>
                                <td><?php echo $row->pieces; ?></td>
                                <td class="font-weight-bold text-danger"><?php echo $row->numero_partida; ?></td>
                                <td class="font-weight-bold text-danger"><?php echo $row->descripcion_producto; ?></td>
                                <td class="font-weight-bold text-primary"><?php echo $row->departamento; ?></td>
                                <td class="font-weight-bold text-primary"><?php echo $row->municipio; ?></td>

                                <?php
                                if ($_SESSION["client_code"] == 107) {
                                } else {
                                ?>
                                    <td><?php echo $row->tipo_entrega; ?></td>
                                    <td><?php echo $row->tipo_servicio; ?></td>
                                    <td><?php echo '$' . number_format($row->cobro_final, 2); ?></td>
                                    <td><?php echo $row->pickup_number; ?></td>
                                    <td><?php echo $row->internal_tracking; ?></td>
                                <?php
                                }
                                ?>


                                <td class="font-weight-bold <?php echo ($row->id_estatus == "15") ? 'text-success' : 'text-danger'; ?>"><?php echo ($row->id_estatus == "15") ? 'SI' : 'NO'; ?></td>
                                <td class="font-weight-bold <?php echo ($row->facturado == "1") ? 'text-success' : 'text-danger'; ?>"><?php echo ($row->facturado == "1") ? 'FACTURADO' : 'PENDIENTE'; ?></td>
                                <td class="font-weight-bold <?php echo ($row->sobrante == 1) ? 'text-success' : 'text-danger'; ?>"><?php echo ($row->sobrante == 1) ? 'SI' : 'NO'; ?></td>
                                <td><?php echo $row->bag_number; ?></td>


                                <?php
                                $string = preg_replace("[\n|\r|\n\r]", "", trim($row->consignee_address1));
                                $string = str_replace(['"', ',', '#', '-', "'", '.', '\\', '//'], '', $string);
                                ?>

                            </tr>
                    <?php
                            $c++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>



        <!-- modal  para agregar manifiesto-->
        <div class="modal fade" id="ModalAdd_referencia" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-white modal-text-header ">
                        <h5 class="modal-title" id="" style="color: #F15A29">Crear referencia</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <form enctype="multipart/form-data" class="add_manifiesto" id="add_manifiesto" action="javascript:guardar_referencia()">

                            <input type="hidden" class="form-control" name="id-manifiesto" id="id-manifiesto">

                            <div class="form-group">
                                <label for="referencia">Referencia</label>
                                <input type="text" class="form-control" placeholder="Introduzca referencia" id="referencia" name="referencia">
                            </div>

                            <div class="form-group">
                                <label for="paquetes">Paquetes</label>
                                <input type="number" class="form-control" placeholder="Introduzca paquetes" id="paquetes" name="paquetes">
                            </div>

                            <div class="form-group">
                                <label for="sacos">Sacos</label>
                                <input type="number" class="form-control" placeholder="Introduzca Sacos" id="sacos" name="sacos">
                            </div>

                            <button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                            </button>
                            <button class="btn btn-primary float-md-right ">Guardar</button>

                        </form>


                    </div>

                </div>
            </div>
        </div>

        <!-- modal  para agregar paquetes a referencia-->
        <div class="modal fade" id="ModalAdd_guias" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <h5 class="modal-title" id="" style="color: #F15A29">Asignar Gu穩as</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <form enctype="multipart/form-data" class="add_guia" id="add_guia" action="javascript:asignar_guia()">

                            <input type="hidden" class="form-control" name="manifiesto_id" id="manifiesto_id">

                            <div class="form-group">
                                <?php $this->load->view("catalogos/referencia", $this->datos); ?>


                            </div>
                            <button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                            </button>
                            <button class="btn btn-primary float-md-right ">Guardar</button>

                        </form>


                    </div>

                </div>
            </div>
        </div>

        <!-- modal mostrar referencia referencia-->
        <div class="modal fade" id="list_reference_modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <h5 class="modal-title" id="" style="color: #F15A29">Referencias</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="manifiesto_id_ref" id="manifiesto_id_ref">

                        <div class="form-group">

                            <div class="table-responsive">
                                <table class="table custom-table" id="tbl_detalle1">
                                    <thead>
                                        <tr>
                                            <th scope="col"> # </td>
                                            <th scope="col">Referencia</td>
                                            <th scope="col">Paquetes</td>
                                            <th scope="col">Sacos</td>
                                            <th scope="col">Acciones</td>
                                        </tr>
                                    </thead>
                                    <tbody id="awb_detalle">
                                        <?php
                                        if (isset($lst_referencia)) {
                                            $c = 1;
                                            foreach ($lst_referencia as $row) { ?>
                                                <tr class="rw ">
                                                    <td scope="row" style="margin-left: 9px"><?php echo $c; ?></td>
                                                    <td scope="row"><?php echo $row->referencia; ?></td>
                                                    <td scope="row"><?php echo $row->paquetes; ?></td>
                                                    <td scope="row"><?php echo $row->sacos; ?></td>
                                                    <td>
                                                        <a href="#" class="btn  btn-sm" title="Editar referencia" style="margin:0px;">
                                                            <i class="fas fa-trash text-danger"></i></a>
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
                        <button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                        </button>

                    </div>

                </div>
            </div>
        </div>


        <!-- modal  para multiplicar  paquetes-->
        <div class="modal fade" id="Modal_multiplicar" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-white modal-text-header ">
                        <h5 class="modal-title" id="" style="color: #F15A29">Multiplicar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <form enctype="multipart/form-data" class="add_multi" id="add_multi" action="javascript:guardar_item_multi()">

                            <input type="text" class="form-control" name="id-item" id="id-item">
                            <div class="form-group">
                                <label for="sacos">Cantidad</label>
                                <input type="number" class="form-control" min="1" step="1" placeholder="Introduzca Cantidad" id="cant" name="cant">
                            </div>

                            <button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                            </button>
                            <button class="btn btn-primary float-md-right ">Guardar</button>

                        </form>


                    </div>

                </div>
            </div>
        </div>

        <!-- actualizar manifiesto -->
        <div class="modal fade" id="guia_express_modal2" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">

                    <div class="modal-header" style="background-color:white">
                        <h4 class="modal-title" style="color:#F15a29">Crear Guia C807 Express </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <input type="hidden" id="id-tracking01" name="id-tracking01">
                    <form id="dm_list">
                        <div id="listado_dpart">
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" id="save_manifiesto_ups" class="btn btn-primary float-md-right">Guardar</button>
                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                        </button>
                    </div>
                </div>


            </div>
        </div>



        <!-- modal  para asignar agencia-->
        <div class="modal fade" id="Modal_asignar_agencia" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-white modal-text-header ">
                        <h5 class="modal-title" id="" style="color: #F15A29">Cambiar datos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form enctype="multipart/form-data" class="add_agencia" id="add_agencia" action="javascript:update_info()">
                            <input type="hidden" id="id-paquete" name="id-paquete">
                            <input type="hidden" id="tipo_entrega" name="tipo_entrega">
                            <input type="hidden" id="tipo_servicio" name="tipo_servicio">

                            <div class="form-group">
                                <label for="dui">DUI</label>
                                <input type="text" class="form-control" placeholder=" " id="dui" name="dui">
                            </div>

                            <div class="form-group">
                                <label for="dui">Correo</label>
                                <input type="text" class="form-control" placeholder=" " id="correo" name="correo">
                            </div>

                            <div class="form-group">
                                <label for="dui">Telefono</label>
                                <input type="text" class="form-control" placeholder=" " id="telefono" name="telefono">
                            </div>

                            <div class="form-group">
                                <label for="">Direcci&oacuten</label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <?php $this->load->view("catalogos/agencias"); ?>
                            </div>
                            <button class="btn btn-primary float-md-right ">Guardar</button>

                        </form>


                    </div>

                </div>
            </div>
        </div>


        <!-- Print referencia -->
        <div class="modal fade" id="modal_print_referencia" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white">
                        <h4 class="modal-title" style="color:#F15a29">Imprimir referencia </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="conatainer">


                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" id="ref_number" placeholder="Referencia">
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary float-md-right" onclick="lista_warehouse1()">Aceptar</button>
                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                        </button>
                    </div>
                </div>


            </div>
        </div>

        <!-- Print referencia -->
        <div class="modal fade" id="modal_factura" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white">
                        <h4 class="modal-title" style="color:#F15a29">Imprimir referencia OLD </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="conatainer">


                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" id="referencia_number" placeholder="Referencia">
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary float-md-right" onclick="generar_facturas()">Aceptar</button>
                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                        </button>
                    </div>
                </div>


            </div>
        </div>

        <!-- Print referencia -->
        <div class="modal fade" id="modal_print_referencia_individual" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white">
                        <h4 class="modal-title" style="color:#F15a29">Comprimir facturas </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!--   <div class="modal-body">
                  <div class="conatainer">

                        <div class="row">
                            <div class="col">
                                <input type="text" class="form-control" id="ref_number" placeholder="Referencia">
                            </div>
                        </div>
                    </div>
                              

                </div>
                 -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary float-md-right" onclick="lista_warehouse_individual()">Aceptar</button>
                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                        </button>
                    </div>
                </div>


            </div>
        </div>



        <div id="ContPDF" style="display: none;">
        </div>




        <!--- modal para mostrar guias en Express-->
        <div class="modal fade" id="guia_express_modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white">
                        <h6 class="modal-title" style="color:#F15a29">Crear guia Xpress </h6>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form enctype="multipart/form-data" class="guia_ex" id="guia_ex" action="javascript:crear_guia_express();">
                        <div class="modal-body">

                            <input type="hidden" id="order_number" name="order_number">
                            <input type="hidden" id="depto" name="depto">
                            <input type="hidden" id="munic" name="munic">
                            <h6 id="tracking-number" class="text-center font-weight-bold"></h6>
                            <div class="div" id="bloque-complemento" style="display:none">
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
                                    <input type="text" class="form-control" id="direccion" placeholder="Introduzca Dirección" name="direccion">
                                    <div class="valid-feedback">Valid.</div>
                                    <div class="invalid-feedback">Please fill out this field.</div>
                                </div>
                            </div>



                            <button type="button" class="btn btn-secondary float-sm-right" data-dismiss="modal">Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary float-sm-right">Crear</button>

                        </div>
                    </form>

                </div>
            </div>
        </div>


        <!--- RETORNO INFO guias en Express-->
        <div class="modal fade" id="info_modal_express" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white">
                        <h4 class="modal-title" style="color:#F15a29">Confirmación </h4>
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
                                        <h5 id="numero-recolecta"></h5>
                                        <h5 id="numero-guia"></h5>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <p id="error_resp" class=" text-danger font-weight-bold"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                        </button>
                    </div>
                </div>


            </div>
        </div>

        <!-- Modal para ver el contenido -->
        <div class="modal fade" id="ver_contenido_tracking" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-white text-dark">
                        <h5 class="modal-title">URL’S Packages Number: <span id="manifiesto_number"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="div_contenido_tracking">

                    </div>

                </div>
            </div>
        </div>


        <!-- Modal Para Reemplazar Numero de Guias -->
        <div class="modal fade" id="modal_reemplace" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white">
                        <h4 class="modal-title" style="color:#F15a29">Reemplazar Guias</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="conatainer">
                            <div class="form-group">
                                <label for="tracking_number">Tracking Number</label>
                                <input type="text" class="form-control" placeholder=" " id="tracking_number" name="tracking_number" value="<?php echo $row->tracking_number; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="tracking_replace">Tracking Replace</label>
                                <input type="text" class="form-control" placeholder=" " id="tracking_replace" name="tracking_replace">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary float-md-right" onclick="reemplazar_tracking(<?php echo $row->id ?>)">Aceptar</button>
                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--- modal para mostrar historial de cambios de guias -->
        <div class="modal fade " id="modal_historial" role="dialog" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered mimodal " role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" style="background-color:white">
                        <h4 class="modal-title" style="color:#F15a29">Historial de Cambios</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="row-fluid">
                        <div class="messageup" id='messageup'>
                        </div>
                    </div>
                    <form enctype="multipart/form-data" class="historial" id="historial" action="javascript:historial;">
                        <!-- Modal body -->
                        <div class="modal-body">
                            <div id="listado_historial">
                                <?php $this->load->view('preclasifica/historial'); ?>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Para Reemplazar Numero de Referencia -->
        <div class="modal fade" id="ModalAsg_referencia" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white">
                        <h4 class="modal-title" style="color:#F15a29">Cambiar Referencia</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="conatainer">
                            <div class="form-group">
                                <label for="tracking_number">Tracking Number</label>
                                <input type="text" class="form-control" id="tracking_number" name="tracking_number" value="<?php echo $row->tracking_number; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="referencia">Referencia</label>
                                <input type="text" class="form-control" placeholder=" " id="no_referencia" name="no_referencia">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary float-md-right" onclick="add_reem_referencia(<?php echo $row->id ?>)"> Aceptar</button>
                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal"> Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal para agregar verdes y rojos -->
        <div class="modal fade" id="verdeModal" tabindex="-1" role="dialog" aria-labelledby="miModalLabel" aria-hidden="true" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="miModalLabel">Pega tu texto aquí</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                    </div>

                    <div class="modal-body">
                        <form enctype="multipart/form-data" method="post" class="addVerdes" id="addVerdes" action="javascript:asignar_verdes()">
                            <textarea id="text-verdes" name="text-verdes" class="form-control" rows="10" placeholder="Pega tu texto aquí..."></textarea>
                            <div class="modal-footer">
                                <button type="submit" id="enviar" class="btn btn-primary"><i class="fas fa-check" aria-hidden="true"> </i>Aplicar</button>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>

        <!-- modal para  actualizar campos en manifiesto  -->
        <div class="modal fade" id="upfileModalUpdate" role=" dialog" data-backdrop="static">
            <?php $this->load->view("verifica"); ?>
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cargar Archivos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="row-fluid" id="messagefile"></div>
                    <form enctype="multipart/form-data" method="post" class="upFieldsManif" id="upFieldsManif" action="javascript:upfields_manifiesto()">
                        <div class="modal-body">
                            <div class="container-fluid mt-2">
                                <div class="row">
                                    <input type="file" name="file" id="file" class="w-50" required accept=".xls" />
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="modal-footer">
                            <button type="submit" id="enviar" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"> </i>Cargar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close" aria-hidden="true"> </i>Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Modal Para Editar la descripcion -->
        <div class="modal fade" id="editar_descripcion_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-white text-dark">
                        <h5 class="modal-title" id="exampleModalLabel">Editar Descripción</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="editar_descripcion">

                    </div>

                </div>
            </div>
        </div>

        <!-- Modal Para Ver Grafica de Estatus -->
        <div class="modal fade" id="modal_grafica_estatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-white text-dark">
                        <h5 class="modal-title w-100 d-flex justify-content-between align-items-center" id="exampleModalLabel">
                            <input type="hidden" id="id_master_detalle" value="<?= $id_manifiesto ?>">
                            <span><strong>Manifiesto:</strong> <?= $master ?></span>
                            <span><strong>Fecha:</strong> <?= $fecha_formateada ?></span>
                            <span><strong>Días transcurridos:</strong> <?= $dias_transcurridos ?> días</span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <!-- Muestra la grafica -->
                        <div id="datos_grafica_estatus"></div>

                        <!-- Muestra los datos a exportar (pendiente de entrega) -->

                        <div id="div_tabla_excel" style="display: none;"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal tabla de detalle de estatus -->
        <div class="modal fade" id="modal_detalle_grafica_por_estatus" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-white text-dark">
                        <h5 class="modal-title">Detalle por Estatus</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="div_detalle_graficas" style="max-height: 60vh; overflow-y: auto;"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal sincronizar -->


        <div class="modal fade" id="confirmacionModal" tabindex="-1" role="dialog" aria-labelledby="confirmacionModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmacionModalLabel">Confirmar Acción</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        ¿Estás seguro de que deseas aplicar esta acción?
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="sincronizar_modal();">Aceptar</button>
                    </div>

                </div>
            </div>
        </div>

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
                    lista_warehouse(id, master, 0)
                } else {
                    if (ref == 'Pendiente') {
                        lista_referencia(id, master, ref)
                    } else {
                        lista_referencia(id, master, ref)
                    }


                }

            });


            $("#agencia").change(function() {
                if ($("#agencia").val() == 0) {
                    $("#tipo_entrega").val("NRML");
                    $("#tipo_servicio").val("CCE");
                } else {
                    var texto = $(this).find('option:selected').text();
                    $("textarea#direccion").text(texto);
                    $("#tipo_entrega").val("ECON");
                    $("#tipo_servicio").val("CCE");
                }
            });


            function refreshForm() {
                lista_warehouse($('#id-master').val(), $('#master-number').val());
            }

            function ver_individual() {
                $("#contenido-principal").hide();
                $("#enca").hide();
                $("#contenido-individual").show();


            }

            function ocultar_individual() {
                $("#contenido-principal").show();
                $("#enca").show();
                $("#contenido-individual").hide();

            }

            $("#numero-guia").blur(function() {
                lista_warehouse($("#id-master").val(), $("#name_master").val(), $("#numero-guia").val());
                //  $("#tracking01").val( $("#numero-guia").val());
            });

            // $("#numero-guia").val($("#master-number").val());
        </script>

        <script>
            //   funcion para actualizar dos campos de la tabla manifiesto 
            function quiantities_add() {

                $("#upfileModalUpdate").modal("show");

            }

            function upfields_manifiesto() {
                var formData;
                var url = base_url("index.php/AduanaController/quiantities_add");
                formData = new FormData($(".upFieldsManif")[0]);
                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        // Show image container
                        $("#loader").css("display", "block");
                    },
                    success: function(response) {
                        console.log(response);
                        $(".modal-backdrop").remove();
                        $("#upfileModalUpdate").modal("hide");
                        $("#msg").show();
                        setTimeout(function() {
                            $("#msg").fadeTo("slow", 0.1, function() {
                                $("#msg").alert("close");
                            });
                        }, 5000);
                        //lista_aduana();
                    },
                    complete: function() {
                        // Show image container
                        $("#loader").css("display", "none");

                    }
                });
            }

            function asignar_verdes() {
                id_master = $("#id-master").val();
                var formData;
                var url = base_url("index.php/AduanaController/asignar_verdes/" + id_master);
                formData = new FormData($(".addVerdes")[0]);
                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        // Show image container
                        $("#loader").css("display", "block");
                    },
                    success: function(response) {

                        setTimeout(function() {
                            $("#msg").fadeTo("slow", 0.1, function() {
                                $("#msg").alert("close");
                            });
                        }, 5000);
                        lista_warehouse(id_master, $("#name_master").val(), 0)
                        $(".modal-backdrop").remove();
                        $("#verdeModal").modal("hide");
                    },
                    complete: function() {
                        // Show image container
                        $("#loader").css("display", "none");

                    }
                });

            }


            
        </script>
        <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/jszip/jszip.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

</body>

</html>