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
            border-bottom: 1px solid #efefef;
            margin-top: 10px;
        }

        .accordion-single-title {
            box-shadow: 0 1px 0px rgb(0 0 0 / 5%), 0 1px 5px rgb(0 0 0 / 12%) !important;
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
            border-top: solid 2px #999;
            border-right: solid 2px #999;
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



        /** here */
        /* Mensaje de espera */
        .wait-message {
            background-color: #F4F6F9;
            /* Fondo que combina con el solicitado */
            color: #333333;
            /* Texto gris oscuro */
            border: 1px solid #C0C4CC;
            /* Borde gris claro */
            border-radius: 8px;
            padding: 20px 30px;
            font-size: 18px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 350px;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }


        /* Contenedor para resultados */
        #result {
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
            background-color: #FFFFFF;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            color: #555555;
            font-size: 16px;
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            border: 1px solid #E0E4E8;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }

        /* Hover effect for the result container */
        #result:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        /* Optional: Add a spinner for loading (when there's a request in process) */
        #result.loading {

            background-size: 50px;
            height: 150px;
            /* Adjust as needed */
            display: flex;
            justify-content: center;
            align-items: center;
        }




        /* styles.css */

        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        /* Preloader Styling */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0f0f0f, #1a1a1a);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .bar-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .bar {
            width: 10px;
            height: 50px;
            background: linear-gradient(45deg, #ff0080, #ff8c00);
            border-radius: 5px;
            animation: bounce 1.2s infinite ease-in-out;
        }

        .bar:nth-child(1) {
            animation-delay: 0s;
        }

        .bar:nth-child(2) {
            animation-delay: 0.2s;
        }

        .bar:nth-child(3) {
            animation-delay: 0.4s;
        }

        .bar:nth-child(4) {
            animation-delay: 0.6s;
        }

        .bar:nth-child(5) {
            animation-delay: 0.8s;
        }

        h1 {
            color: #fff;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: fadeIn 1s ease-in-out infinite;
        }

        /* Animations */
        @keyframes bounce {

            0%,
            100% {
                transform: scaleY(1);
            }

            50% {
                transform: scaleY(2);
            }
        }

        @keyframes fadeIn {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }


        /* Estilos específicos para textareas con la clase "readonly-style" */
        .readonly-style[readonly] {

            background-color: #27293D !important;

            color: #2DCE89;

            cursor: not-allowed;

        }
    </style>



</head>

<body>
    <!-- Preloader -->
    <div id="preloader" style="display: none;">
        <div class="bar-container">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <h1>Procesando...</h1>
    </div>

    <div class="container-fluid" id="enca">
        <input type="hidden" id="id-master">

        <input type="hidden" id="name_master">

        <div class="container-fluid m-top" id="contenido-individual">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
            </div>
        </div>
    </div>

    <div class="container-fluid m-top" id="contenido-principal">

        <div class="row ">
            <div class="col-sm-2 ">

                <h3 id="masterLabel" class="text-muted"></h3>

            </div>

            <div class="col-sm-4 mr-0 pr-0  d-flex justify-content-center">
                <?php $this->load->view("catalogos/referencia_manifiesto"); ?>

            </div>
            <div class="col-sm-3 ">
                <button id="processSelected" class="btn btn-danger btn-md mt-1 ml-1 " onclick="agreDatosdeclaracion_modal()"><i class="fas fa-edit"></i></button>
                <button class="btn btn-success btn-md mt-1 ml-1 " title="Clasificar Declaración " onclick="openmodalClasificar()"><i class="fas fa-check"></i></button>
                <button class="btn btn-secondary btn-md mt-1 ml-1 " title="Agregar conocimiento " onclick="openmodalConocimiento()"><i class="fa-solid fa-pen-nib"></i></button>

                <button type="button" class="btn btn-primary btn-md mt-1 ml-1" title="Imprimir guias aereas" onclick="imprimir_referenciaAPG()">
                    <i class="fa fa-print" aria-hidden="true"></i>
                </button>

            </div>
            <div class="col-sm-2 "></div>
            <div class="col-sm-1" style="display:none;">
                <button type="button" class="btn btn-secondary btn-md m-0 " title="Tipo Declaración" onclick="openModalAsignarTipo()">
                    <i class="fa-solid fa-circle-check"></i>
                </button>
                <button type="button" class="btn btn-primary btn-md m-0 " title="Transmitir" onclick="openModalTrasmitir()">
                    <i class="fa fa-share-alt-square"></i>
                </button>
            </div>


        </div>


        <div id="contenidoLista">
            <!-- Aquí se cargará la tabla generada por el servidor -->
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
                                <input type="text" class="form-control" id="decalarante" name="decalarante" value="Global Cargo de El Salvador S.A. de C.V. - TC263">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Transporte</label>
                                <input type="text" class="form-control" id="inputPassword4" value="Aéreo">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Transportista Master</label>
                                <select name="Atransportista_fast" id="Atransportista_fast" class="form-control chosen">
                                    <?php foreach ($transportista_fast as $row): ?>
                                        <option value="<?php echo $row->id; ?>">
                                            <?php echo  $row->id . ' - ' . $row->nombre_tra; ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Documento Master</label>
                                <input type="text" class="form-control" id="inputPassword4" value="<?php echo $master; ?>"><?php echo $master; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Agente Expedidor</label>
                                <input type="text" class="form-control" id="decalarante" name="decalarante" value="PAK YA">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Puerto Salida</label>
                                <input type="text" class="form-control" id="inputPassword4" value="MIAMI, FL., US. (USMIA)">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Agente Consignatario</label>
                                <input type="text" class="form-control" id="inputEmail4" value="Global Cargo de El Salvador S.A. de C.V.">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Puerto Salida</label>
                                <input type="text" class="form-control" id="inputPassword4" value="COMALAPA, SV. (SVCOM)">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Fecha Salida</label>
                            <input type="date" class="form-control" id="declarante" name="declarante">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Aduana</label>
                            <input type="text" class="form-control" id="inputPassword4">
                        </div>
                    </div>


                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Viaje</label>
                            <input type="text" class="form-control" id="decalarante" name="decalarante">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Línea del máster</label>
                            <input type="text" class="form-control" id="inputPassword4">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Núm. reg. adu</label>
                            <input type="text" class="form-control" id="decalarante" name="decalarante">
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
                    <form enctype="multipart/form-data" class="add_multi" id="add_multi" action="javascript:guardar_item_multi()">

                        <div class="form-group">
                            <label for="sacos">Si está seguro de continuar, click en el botón enviar</label>
                        </div>

                        <!--button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                        </button-->
                        <div style="float: inline-end;">
                            <button class="btn btn-secondary float-md-left ">Cancelar</button>
                            <button class="btn btn-primary float-md-left ">Enviar</button>
                        </div>


                    </form>


                </div>

            </div>
        </div>
    </div>


    <!-- modal para  agregar la   referencia de la declaracion -->
    <div class="modal fade bd-example-modal-lg" id="modal_addinfo_declaracion" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Agregar datos de declaracion</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="conatainer">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="inputEmail4">Tipo</label>
                            </div>
                            <div class="form-group col-md-8">
                                <?php foreach ($declaracionesTipo as  $row): ?>

                                    <div>
                                        <input type="radio" name="Tipo" id="Tipo" value="<?php echo $row->idTipo; ?>"> <label for="one"> <?php echo  $row->Nombre; ?></label>
                                    </div>

                                <?php endforeach ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="inputEmail4">Agente declarante</label>
                            </div>
                            <div class="form-group col-md-8">
                                <?php foreach ($agentes as $row2): ?>
                                    <?php if ($row2->idAgente == 1) { ?>
                                        <div>

                                            <input type="radio" name="Declarante" id="Declarante" value="<?php echo $row2->idAgente; ?>">
                                            <label for="agente"> <?php echo  $row2->Nombre . "(" . $row2->CodigoDec . ")"; ?></label>
                                        </div>
                                <?php }
                                endforeach ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="inputEmail4">Consignatario</label>
                            </div>

                            <div class="form-group col-md-8">
                                <?php foreach ($agentes as $row3): ?>
                                    <?php if ($row3->idAgente == 1) { ?>
                                        <div>

                                            <input type="radio" name="agente" id="agente" value="<?php echo $row3->idAgente; ?>">
                                            <label for="agente"> <?php echo  $row3->Nombre  . "(" . $row3->UsuarioManifiesto . ")"; ?></label>
                                        </div>
                                <?php }
                                endforeach ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="inputEmail4">Referencia</label>
                            </div>
                            <div class="form-group col-md-8">

                                <input type="text" class="form-control input-sm" id="Referencia" name="Referencia" value="" placeholder="78787878788">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="inputEmail4">Declaracion tipo</label>

                            </div>
                            <div class="form-group col-md-8">
                                <select name="tipodecl" id="tipodecl" class="form-control chosen input-sm">
                                    <option value="1"> IML </option>
                                    <option value="2"> IMC </option>
                                </select>
                            </div>

                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="inputEmail4">Cantidad items</label>

                            </div>

                            <div class="form-group col-md-8">

                                <input type="number" class="form-control input-sm" id="BultosLiberacion" name="BultosLiberacion">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="inputEmail4">Aforador</label>

                            </div>
                            <div class="form-group col-md-8">

                                <select name="Aforador" id="Aforador" class="form-control chosen input-sm">
                                    <?php foreach ($aforador as $row4): ?>
                                        <?php if ($row4->Usuario == 'pmedina') { ?>
                                            <option value="<?php echo $row4->Usuario; ?>">
                                                <?php echo  $row4->Nombres . ' ' . $row4->Apellidos; ?>
                                            </option>
                                    <?php }
                                    endforeach ?>
                                </select>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary float-md-right" onclick="procesardeclaracion()">Aceptar</button>
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- modal  clasificar-->
    <div class="modal fade" id="modalClasificacion" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title" id="" style="color: #F15A29">Clasificar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_multi" id="add_multi" action="javascript:obtenerAranceles()">

                        <div class="form-group">
                            <label class="text-muted">Si está seguro de continuar, click en el botón Aceptar</label>
                        </div>

                        <!--button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                        </button-->
                        <div style="float: inline-end;">
                            <button class="btn btn-primary float-md-left" id="startProcess">Aceptar</button>
                        </div>


                    </form>


                </div>

            </div>
        </div>
    </div>


    <!-- modal  clasificar-->
    <div class="modal fade" id="ModalTrasnmitirDeclaracion" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title text-muted" id="">ENVIAR DECLARACION</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_multi" id="add_multi" action="javascript:crear_xmlDeclaracion()">

                        <div class="form-group">
                            <label class="text-muted">Si está seguro de continuar, click en el botón Aceptar</label>
                        </div>


                        <div class="form-group">
                            <textarea id="responseText" class="form-control readonly-style" rows="10" readonly style="display: none;"></textarea>
                        </div>

                        <div style="float: inline-end;">
                            <button class="btn btn-secondary float-sm-left " data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-primary float-md-left" id="startProcess">Aceptar</button>
                        </div>


                    </form>


                </div>

            </div>
        </div>
    </div>


    <!-- modal  clasificar-->
    <div class="modal fade" id="modal_conocimiento" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title text-muted" id="">Agregar conocimiento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_multi" id="add_multi" action="javascript:crear_xmlDeclaracion()">

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" value="storage bag">
                        </div>

                        <div class="form-group">
                            <label for="sac">SAC</label>
                            <input type="text" class="form-control" id="sac" name="sac" value="0039241090">
                        </div>

                        <div class="form-group">
                            <label for="pais">País</label>
                            <input type="text" class="form-control" id="codigoPais" name="codigoPais" value="<?php echo $_SESSION['codigo_pais']; ?>" readonly>
                        </div>

                        <!-- <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="es_courier" name="es_courier" checked>
                            <label class="form-check-label" for="es_courier">
                                Es Courier
                            </label>
                        </div>
                       -->
                        <div style="float: inline-end;">
                            <button class="btn btn-secondary float-sm-left " data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-primary float-md-left" onclick="agregar_conociminto()">Aceptar</button>
                        </div>


                    </form>


                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_print_referenciaAPG" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Imprimir guías aereas </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="conatainer">
                        <div class="row">
                            <div class="col">
                                <input type="text" class="form-control" id="referenciaAPG" name="referenciaAPG" placeholder="Referencia" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary float-md-right" onclick="ImprimirGuiasApg()">Aceptar</button>
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- modal  para multiplicar  paquetes-->
    <div class="modal fade" id="modalAsignarTipo" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title" id="" style="color: #F15A29">Asignar Tipo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_tipo" id="add_tipo" action="javascript:asignar_tipo()">

                        <div class="form-group">
                            <label for="sacos">Tipo</label>
                            <select class="form-control input-sm" id="tipoDeclaracion">
                                <option value="IML">IML</option>
                                <option value="IMC">IMC</option>
                            </select>
                        </div>

                        <!--button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                        </button-->
                        <button class="btn btn-primary float-md-right ">Guardar</button>

                    </form>


                </div>

            </div>
        </div>
    </div>


    <div id="contenmodaledittrack"></div>
    <!-- prelooad -->
    <!-- Contenedor del preload -->
    <div id="preload" class="wait-message" style="display: none;">
        Procesando, por favor espera...
    </div>



    <!-- Contenedor para mostrar los resultados -->
    <div id="result" style="display: none;"></div>




    <script src="<?php echo base_url(); ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/jszip/jszip.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.all.min.js"></script>


    <script>
        $(document).ready(function() {

            $("#tbl_detalle").DataTable({
                "responsive": true,
                "lengthChange": true,
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthMenu": [
                    [3, 5, 10, 20, -1],
                    [3, 5, 10, 20, "Todos"]
                ],
                "dom": "RlBfrtip",
                "buttons": [
                    'excel'
                ]

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
    </script>
    <script>
        //  hacemos un chance para    filtra la lista de paquetes  


        // Evento para el cambio de referencia
        $(document).ready(function() {
            $("#referencia").on("change", function() {

                master = $("#id-master").val();
                ref = $("#referencia").val();
                //   console.log('filtrando la referencia que se desea  agregar a la declaracion ');
                cargarReferencias(master, ref);

                if ($("#referencia").val() != '0') {
                    $("#processSelected").prop("disabled", false);

                } else {
                    $("#processSelected").prop("disabled", true);
                }

            });
            $("#masterLabel").text($("#name_master").val());

        });
    </script>
    <script>
        function openModalTrasmitir() {
            $("#ModalTrasnmitirDeclaracion").modal("show");
            $("#ModalTrasnmitirDeclaracion #responseText").val("");
            $("#ModalTrasnmitirDeclaracion #responseText").hide();

        }

        function openmodalClasificar() {
            $("#modalClasificacion").modal("show");
        }

        function openModalAsignarTipo() {
            $("#modalAsignarTipo").modal("show")
        }

        function obtenerAranceles() {
            var id_master = $("#id-master").val();
            var name_master = $("#name_master").val();
            var referencia = $("#referencia").val();
            var numero_guia = $("#numero-guia").val();
            var tipo = $("#tipo").val();
            var tipo_envio = $("#tipo_envio").val();

            // alert(id_master);
            // alert(name_master);
            // alert(referencia);

            var url = base_url("index.php/DeclaracionController/clasificar_productos/" + id_master + "/" + referencia);

            $('#modalClasificacion').modal('hide');
            // Mostrar el mensaje de carga
            // $("#preload").fadeIn();
            $("#preloader").fadeIn();


            // Realizar la solicitud AJAX con POST
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    parametro1: "valor1",
                    parametro2: "valor2"
                },
                success: function(response) {
                    // Ocultar el preload
                    //  $("#preload").fadeOut();
                    $("#preloader").fadeOut();

                    // Mostrar el resultado en el contenedor
                    $("#result").html(response);


                    master = $("#id-master").val();
                    ref = $("#referencia").val();
                    cargarReferencias(master, ref);

                },
                error: function(xhr, status, error) {
                    // Ocultar el preload
                    //  $("#preload").fadeOut();
                    $("#preloader").fadeOut();

                    // Mostrar un mensaje de error detallado
                    alert("Ocurrió un error: " + xhr.status + " - " + error);
                }
            });


        }
    </script>

    <script>
        $(document).ready(function() {

        });
    </script>
    <script>
        function efecto() {
            document.addEventListener("DOMContentLoaded", () => {
                const preloader = document.getElementById("preloader");
                const content = document.getElementById("content");

                // Hide preloader and show content after 3 seconds
                setTimeout(() => {
                    preloader.style.opacity = "0";
                    preloader.style.visibility = "hidden";
                    content.style.display = "block";

                    // Fade in the content
                    setTimeout(() => {
                        content.style.opacity = "1";
                    }, 100);
                }, 3000);
            });
        }
    </script>
</body>

</html>