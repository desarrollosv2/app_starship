<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .dropdown-options {
            display: none;
            position: relative;
            overflow: auto;
        }

        .dropdown:hover .dropdown-options {
            display: block;
        }


        .accordion-single {
            border-bottom: 2px solid #0088FD;
            margin-top: 10px;

        }

        .accordion-single-title {
            box-shadow: 0 1px 0px rgb(0 136 253 / 15%), 0 1px 5px rgb(0 136 253 / 25%) !important;
            border-radius: 9px;
            background-color: #f0f0f082;

            padding: 20px;
            cursor: pointer;
            position: relative;
            font-size: 20px;
        }

        .accordion-single-title::after {
            content: "";
            position: absolute;
            right: 25px;
            top: 50%;
            transition: all 0.2s ease-in-out;
            display: block;
            width: 8px;
            height: 8px;
            border-top: solid 2px #0088FD;
            border-right: solid 2px #0088FD;
            transform: translateY(-50%) rotate(135deg);

        }

        .accordion-single-content {
            margin: 1rem 0rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease-in-out;

        }

        .accordion-single-content p {
            padding: 20px;

        }

        .accordion-single-item.is-open .accordion-single-content {
            max-height: 600px;

        }

        .accordion-single-item.is-open .accordion-single-title::after {
            transform: translateY(-50%) rotate(315deg);
        }
    </style>
</head>
<body>
    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $client_id = 0;
    if (isset($preclasificacion)) {
        $client_id = $preclasificacion[0]->client_id;
    }
    ?>
    <div class="container-fluid" id="enca">
        <input type="text" id="client-id" name="client-id" value="<?php echo $client_id ;?>" >
        <div class="row mt-2 ml-5 ">
            <div class="form-group mt-3 col-md-3 my-auto">
                <h2 id="master-number" name="master-number"></h2>
                <input type="hidden" id="id-master">
                <input type="hidden" id="name_master">
            </div>
            <div class="form-group md-2 mt-3 col-md-5">
                <div class="row">
                    <button type="button" class="btn btn-primary btn-lg " onclick="editar_wh_fast()" title="Editar Guia">
                        <i class="fa fa-plus"></i>
                    </button>
                    <button class="btn btn-success btn-lg "
                        title="Manisfiesto Fast" onclick="lista_warehouseexport_cla()">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-lg " data-toggle="tooltip" data-placement="bottom" title="Imprimir facturas referencia" onclick="imprimir_referencia()">
                        <i class="fa fa-file-pdf-o"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg  " data-toggle="tooltip" data-placement="bottom" title="Imprimi">
                        <i class="fa fa-cog "></i>
                    </button>
                    <div class="col-md my-auto pb-2">
                        <select name="refer" id="refer" class="form-control chosen">
                            <option> seleccion refencia </option>
                            <?php
                            if (isset($ref)) {
                                $sel = $ref;
                            } else {
                                $sel = 0;
                            }
                            echo $sel;

                            if (isset($lst_referencia)) {
                                foreach ($lst_referencia as $row) { ?>
                                    <option value="<?php echo $row->referencia; ?>" <?php if ($sel === $row->referencia) {
                                                                                        echo 'selected';
                                                                                    } else {
                                                                                        echo '';
                                                                                    } ?>>
                                        <?php echo $row->referencia; ?>
                                    </option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group md-2 mt-3 col-md-3 my-auto">
                <div class="row float-right">
                    <!--  <button type="button" class="btn btn-primary btn-lg m-0 float-right" data-toggle="tooltip" data-placement="bottom" title="Transmitir" onclick="tramite_aduana_cla()">
                        <i class="fa fa-share-alt-square"></i>
                    </button> -->
                    <button type="button" class="btn btn-primary btn-lg m-0 float-right" data-toggle="tooltip" data-placement="bottom" title="Transmitir" onclick="trasmitir_modal()">
                        <i class="fa fa-share-alt-square"></i>
                    </button>
                </div>
            </div>
        </div>
        <hr class="borde-orange mt-0">
        <div class="container-fluid m-top" id="contenido-individual">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
            </div>
        </div>
    </div>
    <? var_dump($preclasificacion); ?>
    <div class="container-fluid ">
        <div class="contents">
            <!-- acc single -->
            <div class="accordion-single js-acc-single">
                <div class="accordion-single-item js-acc-item">
                    <h2 class="accordion-single-title js-acc-single-trigger text-primary  ">Información de guia master</h2>
                    <div class="accordion-single-content">
                        <div class="container-fluid">
                            <form class="update_wh_fast">
                                <div class="form-row">
                                    <div>
                                        <input type="hidden" id="id_precla" name="id_precla" value="<?php echo $preclasificacion[0]->idpreclasificacion; ?>">

                                        <input type="hidden" id="transporte_id" name="transporte_id" value="<?php echo $preclasificacion[0]->tipo_transporte; ?>">
                                        <input type="hidden" id="transportista_id" name="transportista_id" value="<?php echo $preclasificacion[0]->id_transportista; ?>">
                                        <input type="hidden" id="puertosalida_id" name="puertosalida_id" value="<?php echo $preclasificacion[0]->puerto_salida; ?>">
                                        <input type="hidden" id="puertosalida2_id" name="puertosalida2_id" value="<?php echo $preclasificacion[0]->puerto_salida2; ?>">
                                        <input type="hidden" id="agente_id" name="agente_id" value="<?php echo $preclasificacion[0]->agent_exp; ?>">
                                        
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4">Declarante </label>
                                        <?php $this->load->view('catalogos/agente'); ?>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">Transporte</label>
                                        <?php $this->load->view('catalogos/tipo_transporte'); ?>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4">Transportista Master</label>
                                        <?php $this->load->view('catalogos/transportista'); ?>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">Documento Master</label>
                                        <input type="text" class="form-control input-sm" id="manifiesto" name="manifiesto"
                                            value="<?php echo $preclasificacion[0]->manifiesto; ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4">Agente Expedidor</label>
                                        <?php $this->load->view("catalogos/empresa", $this->datos); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">Puerto Salida</label>
                                        <?php $this->load->view("catalogos/puerto_salida"); ?>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4">Agente Consignatario</label>
                                        <?php $this->load->view("catalogos/agente_consolidado"); ?>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">Puerto Salida</label>
                                        <?php $this->load->view("catalogos/puerto_salida2"); ?>
                                    </div>

                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4">Fecha Salida</label>
                                        <input type="date" class="form-control input-sm" id="fechasalida" name="fechasalida"
                                            value="<?php echo $preclasificacion[0]->fechasalida; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">Aduana</label>
                                        <!--input type="text" class="form-control" id="aduana" name="aduana"
                                        value="<?php echo $preclasificacion[0]->aduana; ?>"-->
                                        <select name="aduana" id="aduana"
                                            class="form-control chosen input-sm">
                                            <option <?php echo ($preclasificacion[0]->aduana == 'AEREA DE COMALAPA') ? "selected" : ""; ?>
                                                value="AEREA DE COMALAPA">AEREA DE COMALAPA
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4">Viaje</label>
                                        <input type="text" class="form-control input-sm" value="<?php echo $preclasificacion[0]->viaje; ?>"
                                            id="viaje" name="viaje">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">Línea del máster</label>
                                        <input type="text" class="form-control input-sm" value="<?php echo $preclasificacion[0]->linea_master; ?>"
                                            id="linea_master" name="linea_master">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Núm. reg. adu</label>
                                        <input type="text" class="form-control input-sm" value="<?php echo $preclasificacion[0]->num_reg; ?>"
                                            id="num_reg" name="num_reg">
                                    </div>
                                    <div class="form-group col-md-6 my-auto">
                                        <button type="button" class="btn btn-primary float-md-right" onclick="update_wh_fast()">Aceptar</button>

                                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div><!-- accordion-single -->
        </div>
    </div>

    <input type="hidden" id="opc-clasif">
    <div class="container" id="detalle-partida" style="display:none">

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <div class="form-group">
                    <div class="text-center"></div>
                </div>
            </div>

            <div class="col-sm-2"></div>
        </div>
    </div>

    <div class="container-fluid mt-3" id="contenido-principal">


        <div class="table-responsive">
            <div class="row">
                <div class="col-sm-3 mr-0 pr-0">
                    <input type="text" class="form-control" id="nueva-guia" name="nueva-guia" placeholder="Guía a consolidar aqui">
                </div>
                <div class="col-sm-1 ml-0 pl-0">
                    <button id="processSelected" class="btn btn-primary mt-0 ml-1 ">Consolidar</button>
                </div>
                <div class="col-sm-8">

                </div>
            </div>

            <table class="table custom-table" id="tbl_detalle">
                <thead>
                    <tr>
                        <th scope="col">
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th scope="col">#</th>
                        <th scope="col">Tracking Number</th>
                        <th scope="col">Referencia</th>
                        <th scope="col">Expendidor</th>
                        <th scope="col">Consignatario</th>
                        <th scope="col">Bultos</th>
                        <th scope="col">Peso kg</th>
                        <th scope="col">Contenido</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Declaracion</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>

                <tbody id="awb_detalle">
                    <?php
                    if (isset($lista)) {
                        $c = 1;
                        foreach ($lista as $row) {
                    ?>
                            <tr class="rw">
                                <td>
                                    <input type="checkbox" class="select-row" value="<?php echo $row->id; ?>">
                                </td>
                                <td><?php echo $c; ?></td>

                                <td scope="row"
                                    <?php if ($row->guia_consolidada !== null && $row->guia_consolidada > 0): ?>
                                    class="text-danger"
                                    <?php endif; ?>>
                                    <?php echo $row->tracking_number; ?>
                                </td>

                                <td><?php echo $row->referencia; ?></td>
                                <td><?php echo $row->shipper_name; ?></td>
                                <td><?php echo utf8_decode($row->consignee); ?></td>
                                <td><?php echo $row->pieces; ?></td>
                                <td><?php echo $row->gweight . ' kg'; ?></td>
                                <td><?php echo $row->commodity; ?></td>
                                <td><?php echo '$' . $row->cobro_final; ?></td>
                                <td><?php echo ''; ?></td>
                                <td>
                                    <div class="row">
                                        <button class="col col-md-3 mx-1 btn btn-light" onclick="editar_wh_fast(<?php echo $row->id; ?>)" title="Editar Guia">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </button>
                                        <button class="col col-md-3 mx-1 btn btn-light" onclick="open_modal_multiplicar(<?php echo "'$row->id'"; ?>)" title="Multiplicar">
                                            <i class="fa fa-cut"></i>
                                        </button>
                                        <button class="col col-md-3 mx-1 btn btn-light" onclick="eliminar_wh_fast(<?php echo $row->id; ?>)" title="Eliminar Guia">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                    <?php
                            $c++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <?php
    if (isset($info_master)) {
        $c = 1;
        foreach ($info_master as $row) {
            $master = $row->manifiesto;
        }
    }
    ?>
    <!-- Modal Para Reemplazar Numero de Guias -->
    <div class="modal fade bd-example-modal-lg" id="modal_info_fast" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Editar Manifiesto</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="conatainer">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Declarante</label>
                                <input type="text" class="form-control input-sm" id="decalarante" name="decalarante" value="Global Cargo de El Salvador S.A. de C.V. - TC263">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Transporte</label>
                                <input type="text" class="form-control input-sm" id="inputPassword4" value="Aéreo">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Transportista Master</label>
                                <select name="Atransportista_fast" id="Atransportista_fast" class="form-control chosen input-sm">
                                    <?php foreach ($transportista_fast as $row): ?>
                                        <option value="<?php echo $row->id; ?>">
                                            <?php echo  $row->id . ' - ' . $row->nombre_tra; ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Documento Master</label>
                                <input type="text" class="form-control input-sm" id="inputPassword4" value="<?php echo $master; ?>"><?php echo $master; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Agente Expedidor</label>
                                <input type="text" class="form-control input-sm" id="decalarante" name="decalarante" value="PAK YA">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Puerto Salida</label>
                                <input type="text" class="form-control input-sm" id="inputPassword4" value="MIAMI, FL., US. (USMIA)">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Agente Consignatario</label>
                                <input type="text" class="form-control input-sm" id="inputEmail4" value="Global Cargo de El Salvador S.A. de C.V.">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Puerto Salida</label>
                                <input type="text" class="form-control input-sm" id="inputPassword4" value="COMALAPA, SV. (SVCOM)">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Fecha Salida</label>
                            <input type="date" class="form-control input-sm" id="declarante" name="declarante">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Aduana</label>
                            <input type="text" class="form-control input-sm" id="inputPassword4">
                        </div>
                    </div>


                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Viaje</label>
                            <input type="text" class="form-control input-sm" id="decalarante" name="decalarante">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Línea del máster</label>
                            <input type="text" class="form-control input-sm" id="inputPassword4">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Núm. reg. adu</label>
                            <input type="text" class="form-control input-sm" id="decalarante" name="decalarante">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary float-md-right" onclick="reemplazar_tracking(<?php echo $row->id ?>)">Aceptar</button>
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal  para multiplicar  paquetes-->
    <div class="modal fade" id="Modal_multiplicar" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
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

                        <!--button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                        </button-->
                        <button class="btn btn-primary float-md-right ">Guardar</button>

                    </form>


                </div>

            </div>
        </div>
    </div>

    <!-- Print referencia -->
    <div class="modal fade" id="modal_print_referencia" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
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

    <!-- modal para tramite-->
    <div class="modal fade" id="modal_tramite_cla" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title" id="" style="color: #F15A29">Transmitir</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_multi">

                        <div class="form-group">
                            <label for="sacos">Si está seguro de continuar, click en el botón enviar</label>
                        </div>
                        <div style="float: inline-end;">
                            <button class="btn btn-secondary float-md-left " data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary float-md-left " onclick="add_xml()">Enviar</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- modal para trasmitir manifiesto -->
    <div class="modal fade" id="modal_transmitir_maifiesto" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title" id="" style="color: #F15A29">Desconsolidar manifiesto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_multi">

                        <div class="form-group">
                            <p>Se dará inicio al proceso de transmisión, desea continuar?</p>
                        </div>
                        <div style="float: inline-end;">
                            <button class="btn btn-secondary float-sm-left " data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary float-sm-left " onclick="degroup_xml()">Enviar</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- llamado de libreria de datatable -->
    <?php $this->load->view('lib/datatable_scritp'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            master = $("#id-master").val();
            cantConsolidada = 0;
            cantAgrupada = 0;
            var url = base_url("index.php/AduanaController/sumar_consolida/" + master);

            // Realizar la llamada AJAX
            $.get(url, function(data) {

                try {
                    data = typeof data === 'string' ? JSON.parse(data) : data;
                } catch (e) {
                    console.error("Error al parsear JSON:", e);
                }
                cantConsolidada = data.sum;
                cantAgrupada = data.count;


                // Inicializar DataTables solo después de obtener el valor de `cantConsolidada`
                $("#tbl_detalle").DataTable({
                    "responsive": true,
                    "lengthChange": true,
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "lengthMenu": [
                        [10, 20, 100, -1],
                        [10, 20, 100, "Todos"]
                    ],
                    "language": {
                        "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas"
                    },
                    "drawCallback": function(settings) {
                        // Obtener el total de entradas
                        var total = settings.fnRecordsTotal();
                        // Restar `cantConsolidada` del total
                        var adjustedTotal = total - cantConsolidada - cantAgrupada;

                        // Calcular el número inicial y final de entradas mostradas en la página actual
                        var start = settings._iDisplayStart + 1;
                        var end = settings._iDisplayStart + settings._iDisplayLength;
                        if (end > adjustedTotal) {
                            end = adjustedTotal; // Ajustar el valor de end si es mayor que el total ajustado
                        }

                        // Actualizar el texto de la información
                        $('.dataTables_info').html(`Mostrando ${start} a ${end} de ${total-cantAgrupada} entradas [- ${cantConsolidada} consolidadas = ${adjustedTotal}] `);
                    }
                });
            });
        });



        $(document).ready(function() {
            const accSingleTriggers = document.querySelectorAll('.js-acc-single-trigger');

            accSingleTriggers.forEach(trigger => trigger.addEventListener('click', toggleAccordion));

            function toggleAccordion() {
                const items = document.querySelectorAll('.js-acc-item');
                const thisItem = this.parentNode;

                items.forEach(item => {
                    if (thisItem == item) {
                        thisItem.classList.toggle('is-open');
                        return;
                    }
                    item.classList.remove('is-open');
                });
            }
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
                Alista_warehouse_fast(id, master, 0)
            } else {
                if (ref == 'Pendiente') {
                    Alista_referencia(id, master, ref)
                } else {
                    Alista_referencia(id, master, ref)
                }
            }
        });

        $("#refer").change(function() {
            id = $("#id-master").val();
            master = $("#name_master").val();
            ref = this.value;

            console.log(id, master, ref);

            lista_manifiesto_refer(id, master, ref);

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
            Alista_warehouse_fast($('#id-master').val(), $('#master-number').val());
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
            Alista_warehouse_fast($("#id-master").val(), $("#name_master").val(), $("#numero-guia").val());
            //  $("#tracking01").val( $("#numero-guia").val());
        });

        function tramite_aduana_cla() {
            $("#modal_tramite_cla").modal("show");
        }

        function trasmitir_modal() {
            $("#modal_transmitir_maifiesto").modal("show");
        }

        function add_xml() {
            var ref = $("#refer").val();
            var url = base_url("index.php/AduanaController/XFM/" + ref);
            //var url = base_url("index.php/AduanaController/crear_xml/" + ref);
            $.get(url, function(e) {
                console.log(e);
                //$(".modal-backdrop").remove();
                //$("#Modal_consolidado").modal("hide");
                //Alista_warehouse_fast($("#id-master").val(), $("#name_master").val(),0);
            });
        }

        // $("#numero-guia").val($("#master-number").val());
    </script>


    <script>
        $(document).ready(function() {

            $("#empresa").val($('#client-id').val());
            $("#empresa").change();

            $("#tipo_transporte").val($('#transporte_id').val());
            $("#tipo_transporte").change();
            $("#id_transportista").val($('#transportista_id').val());
            $("#id_transportista").change();
            $("#puerto_salida").val($('#puertosalida_id').val());
            $("#puerto_salida").change();
            $("#puerto_salida2").val($('#puertosalida2_id').val());
            $("#puerto_salida2").change();
            /*$("#empresa").val($('#agente_id').val());
            $("#empresa").change();*/



            master = $("#id-master").val();
            //  alert(master);
            // Seleccionar o deseleccionar todos los checkboxes
            $('#checkAll').on('click', function() {
                $('.select-row').prop('checked', this.checked);
            });

            // Botón para procesar los seleccionados
            $('#processSelected').on('click', function() {
                let selected = [];
                let nuevaGuia = $('#nueva-guia').val(); // Obtener el valor del campo 'nueva-guia'

                // Validar si el campo nueva-guia está vacío
                if (nuevaGuia.trim() === '') {
                    alertify.set("notifier", "position", "top-right");
                    alertify.error("El campo Nueva Guía no puede estar vacío.");
                    return; // Detener la ejecución si está vacío
                }
                // Recorrer los checkboxes seleccionados
                $('.select-row:checked').each(function() {
                    let val = $(this).val(); // Obtener el valor del checkbox
                    if (val) {
                        selected.push(val); // Guardar el valor (ID) si es válido
                    }
                });

                if (selected.length > 0) {
                    // Enviar IDs seleccionados al servidor con AJAX

                    url_destino = "index.php/AduanaController/update_agrupar/" + master + '/' + nuevaGuia;

                    $.ajax({
                        url: base_url(url_destino), // Cambia por la ruta a tu archivo PHP
                        method: 'POST',
                        data: {
                            ids: selected

                        }, // Enviar el arreglo de IDs
                        success: function(response) {
                            // Aquí manejas la respuesta del servidor
                            alertify.set("notifier", "position", "top-right");
                            alertify.success("Datos guardados correctamente");

                            console.log(response);
                        },
                        error: function(xhr, status, error) {
                            alertify.set("notifier", "position", "top-right");
                            alertify.error("Error al enviar los datos: " + error);

                        }
                    });
                } else {
                    alertify.set("notifier", "position", "top-right");
                    alertify.error("No hay filas seleccionadas.");

                }
            });
        });

        function base_url(url) {
            return window.location.origin + "/app_starship/" + url;
        }

    </script>

</body>

</html>