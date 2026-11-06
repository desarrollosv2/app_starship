<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/btnstyles.css') ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

    <title></title>
    <style>
        th:first-child,
        td:first-child {
            border-left-color: transparent;
        }

        .form-control,
        .btn {
            border-radius: 30px;
        }

        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .btn-primary:hover {
            background-color: #375a7f;
            border-color: #375a7f;
        }




        .btn-warning,
        .btn-danger {
            border-radius: 20px;
        }

        .btn-warning {
            background-color: #f6c23e;
            border-color: #f6c23e;
        }

        .btn-danger {
            background-color: #e74a3b;
            border-color: #e74a3b;
        }

        .text-muted {
            color: #858796;
        }

        .search-and-filter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #ffffff;
            border: 1px solid #e3e6f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-and-filter .form-inline {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .search-and-filter .form-inline .date-filters {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }

        .search-and-filter .form-inline .date-filters label {
            margin-right: 5px;
        }

        .search-and-filter .form-inline .date-filters input {
            margin-right: 10px;
        }

        .search-and-filter .form-inline .btn {
            margin-left: 10px;
        }

        .search-and-filter .form-inline .search-input {
            margin-left: auto;
            width: 300px;
        }

        .date-filters input {
            width: 120px;
        }

        .search-input input {
            width: 100%;
        }

        .h {
            display: none;
        }

        .btn {
            border-radius: 50px !important;
        }

        .radius-date {
            height: 32px;
            padding: 4px 10px;
            font-size: 0.85rem;
            line-height: 1.2;
        }

        .btn-icon {
            padding: 6px 14px !important;
            font-size: 0.85rem;
            line-height: 1.2;
        }

        input[type="date"].form-control.radius-date {
            height: 32px !important;
            padding: 4px 10px !important;
            font-size: 0.85rem !important;
            line-height: 1.2 !important;
            max-width: 130px;
        }

        /*new code*/

        /* Estilos generales del cuerpo y contenedor */


        .form-container {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            /* Se ha ampliado el ancho del contenedor */
            max-width: 900px;
            box-sizing: border-box;
            transition: transform 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-5px);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header img {
            max-height: 60px;
            margin-bottom: 10px;
        }

        .form-header h1 {
            font-weight: 600;
            font-size: 28px;
            color: #333;
            margin: 0;
        }

        /* Estilos para el campo de subida de archivos */
        .file-upload-box {
            position: relative;
            padding: 30px;
            border: 2px dashed #dcdfe3;
            border-radius: 10px;
            text-align: center;
            background-color: #fafbfd;
            cursor: pointer;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .file-upload-box:hover {
            background-color: #f5f6f8;
            border-color: #fc5c24;
        }

        .file-upload-box input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload-box .upload-icon {
            font-size: 40px;
            color: #fc5c24;
            margin-bottom: 10px;
        }

        .file-upload-box .upload-text {
            font-size: 16px;
            color: #555;
            font-weight: 500;
        }

        /* Estilos para la sección de opciones */
        .options-container {
            margin-top: 30px;
        }

        .options-container h3 {
            font-size: 20px;
            color: #555;
            margin-bottom: 15px;
        }

        .options-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .checkbox-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .checkbox-item input[type="checkbox"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            outline: none;
            margin-right: 15px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .checkbox-item input[type="checkbox"]:checked {
            background-color: #fc5c24;
            border-color: #fc5c24;
        }

        .checkbox-item input[type="checkbox"]:checked::after {
            content: '\2713';
            display: block;
            color: white;
            font-size: 14px;
            text-align: center;
            line-height: 16px;
        }

        .checkbox-item label {
            font-size: 16px;
            color: #444;
            cursor: pointer;
        }

        /* Estilos para el botón de envío */
        .btn-submit {
            display: block;
            width: 100%;
            padding: 15px;
            border: none;
            background-color: #fc5c24;
            color: white;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 40px;
        }

        .btn-submit:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit svg {
            margin-right: 10px;
        }

        .options-container input[type="text"] {
            /* 1. Base Estética */

            /* Fondo oscuro para contraste */
            background-color: #2c3e50;
            /* Texto en blanco brillante */
            color: #ecf0f1;

            /* 2. Forma */

            /* Ocupar todo el ancho */
            width: 100%;
            /* Espacio interior generoso */
            padding: 12px 15px;
            /* Borde sutil, casi gris */
            border: 1px solid #3e5163;
            /* Esquinas ligeramente redondeadas */
            border-radius: 4px;
            /* Fuente clara */
            font-size: 16px;

            /* 3. Transición */

            /* Suaviza los cambios de color/sombra */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            /* Eliminar el resplandor por defecto */
            outline: none;
        }

        /* Estado de Foco (cuando el usuario hace clic o tabula) */
        .options-container input[type="text"]:focus {
            /* Resalta el borde con el color base */
            border-color: #fc5c24;

            /* Efecto de resplandor "Blaze" usando el color base */
            box-shadow:
                0 0 5px rgba(252, 92, 36, 0.7),
                /* Resplandor principal */
                0 0 10px rgba(252, 92, 36, 0.4);
            /* Resplandor exterior suave */

            /* El fondo se oscurece ligeramente más para que el borde resalte */
            background-color: #233140;
        }

        /* Estilo para el placeholder (si el input lo usa) */
        .options-container input[type="text"]::placeholder {
            color: #7b8a9e;
            /* Un gris sutil para la guía */
            opacity: 0.8;
        }

        /* Media queries para responsividad */
        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }

            .options-list {
                grid-template-columns: 1fr;
            }

            .form-header h1 {
                font-size: 22px;
            }
        }

        /* Usamos un contenedor padre único para encapsular todo */
        .logis-update-container {
            box-sizing: border-box;
            width: 100%;
            max-width: 100%;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #eee;
            border-top: 6px solid #FE4F00;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Selectores descendentes para asegurar prioridad */
        .logis-update-container .logis-card-body {
            padding: 30px;
        }

        .logis-update-container .logis-title {
            color: #1a1a1a;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: left;
        }

        .logis-update-container .logis-group {
            margin-bottom: 20px;
            text-align: left;
        }

        /* Labels específicos */
        .logis-update-container label.logis-label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: #555;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        /* Input de China (Bloqueado) */
        .logis-update-container input.logis-input-readonly {
            width: 100% !important;
            padding: 12px 15px !important;
            background-color: #f4f4f4 !important;
            border: 2px solid #e0e0e0 !important;
            border-radius: 8px !important;
            color: #888 !important;
            font-family: monospace !important;
            cursor: not-allowed !important;
        }

        /* Input de Miami (Activo) */
        .logis-update-container input.logis-input-active {
            width: 100% !important;
            padding: 12px 15px !important;
            border: 2px solid #ddd !important;
            border-radius: 8px !important;
            font-size: 16px !important;
            outline: none !important;
            transition: all 0.2s ease-in-out !important;
        }

        .logis-update-container input.logis-input-active:focus {
            border-color: #FE4F00 !important;
            box-shadow: 0 0 0 3px rgba(254, 79, 0, 0.1) !important;
        }

        /* Botón con el color base */
        .logis-update-container .logis-btn-save {
            width: 100%;
            background-color: #FE4F00;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            text-transform: uppercase;
            transition: background 0.3s ease;
        }

        .logis-update-container .logis-btn-save:hover {
            background-color: #d64200;
        }

        /* Divisor visual */
        .logis-divider {
            text-align: center;
            padding: 10px 0;
            color: #FE4F00;
            font-weight: bold;
        }

        /* Contenedor del checkbox */
        .logis-checkbox-group {
            margin-top: -10px;
            margin-bottom: 20px;
        }

        .logis-checkbox-container {
            display: flex;
            align-items: center;
            position: relative;
            padding-left: 30px;
            cursor: pointer;
            font-size: 13px;
            color: #666;
            user-select: none;
            font-weight: 600;
        }

        /* Ocultar el checkbox original */
        .logis-checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* El cuadro personalizado */
        .logis-checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            background-color: #eee;
            border-radius: 4px;
            border: 2px solid #ddd;
            transition: all 0.2s;
        }

        /* Cuando el checkbox está marcado */
        .logis-checkbox-container input:checked~.logis-checkmark {
            background-color: #FE4F00;
            border-color: #FE4F00;
        }

        /* El check (la marca blanca) */
        .logis-checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .logis-checkbox-container input:checked~.logis-checkmark:after {
            display: block;
        }

        .filter-card {

            background: #fff;

            border-radius: 14px;

            padding: 20px;

            margin-bottom: 20px;

            box-shadow:
                0 2px 12px rgba(0, 0, 0, .05);

            border: 1px solid #f1f1f1;
        }

        .filter-header {

            display: flex;
            align-items: center;

            gap: 10px;

            margin-bottom: 18px;

            font-size: 15px;
            font-weight: 600;

            color: #374151;
        }

        .filter-header i {

            color: #FE4F00;
        }

        .filter-body {

            display: flex;
            align-items: end;

            gap: 15px;

            flex-wrap: wrap;
        }

        .filter-group {

            display: flex;
            flex-direction: column;

            min-width: 200px;
        }

        .filter-group label {

            font-size: 13px;

            color: #6B7280;

            margin-bottom: 6px;

            font-weight: 500;
        }

        .filter-group label i {

            color: #FE4F00;

            margin-right: 5px;
        }

        .modern-input {

            height: 45px;

            border-radius: 10px;

            border: 1px solid #D1D5DB;

            transition: .2s;
        }

        .modern-input:focus {

            border-color: #FE4F00;

            box-shadow:
                0 0 0 3px rgba(254, 79, 0, .15);
        }

        .btn-filter {

            height: 45px;

            padding: 0 25px;

            border: none;

            border-radius: 10px;

            background: #FE4F00;

            color: white;

            font-weight: 600;

            transition: .2s;
        }

        .btn-filter:hover {

            background: #E84A00;

            transform: translateY(-2px);
        }

        .btn-filter i {

            margin-right: 8px;
        }

        @media(max-width:768px) {

            .filter-body {

                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {

                width: 100%;
            }

            .btn-filter {

                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php // $this->load->view('hd'); 
    ?>

    <div class="container-fluid">

        <div class="filter-card">

            <div class="filter-header">
                <i class="fas fa-filter"></i>
                <span>Filtros de búsqueda</span>
            </div>

            <div class="filter-body">

                <div class="filter-group">

                    <label>
                        <i class="far fa-calendar-alt"></i>
                        Desde
                    </label>

                    <input type="date"
                        class="form-control modern-input"
                        id="startDate">

                </div>

                <div class="filter-group">

                    <label>
                        <i class="far fa-calendar-alt"></i>
                        Hasta
                    </label>

                    <input type="date"
                        class="form-control modern-input"
                        id="endDate">

                </div>

                <div class="filter-action">

                    <button class="btn-filter"
                        onclick="lista_awb()">

                        <i class="fas fa-search"></i>
                        Consultar

                    </button>

                </div>

            </div>

        </div>

    </div>


    <div class="container-fluid">
        <div id="awb_list">

        </div>
    </div>

    <!-- Div para  cargar la modal  de cargar Archivos 02-10-2023-->
    <div id="awb_update">

    </div>

    <!-- Div para  cargar la modal  de cargar Archivos 12-10-2023-->
    <div id="dui_update">

    </div>

    <!-- Div para cargar modal de Referencias -->
    <div id="referencia_update">

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
                            <label for="direccion">Direcci��n:</label>
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
    <div class="container">
        <div class="alert alert-primary alert-dismissible fade show hide" id="msg" role="alert">
            <strong>La Carga se ha Realizado Correctamente!</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

    <div class="modal fade" id="upfileModal" role="dialog" data-backdrop="static">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-white">
                    <h5 class="modal-title" id="exampleModalLabel">Cargar Referencia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>
                <form enctype="multipart/form-data" method="post" class="up_ref" id="up_ref" action="javascript:upload_file_referencia()">
                    <input type="hidden" id="idpreclasificacion" name="idpreclasificacion">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <input type="file" name="file" id="file" class="w-50" required accept=".xls" />
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="modal-footer">
                        <button type="submit" id="enviar" class="btn btn-primary">Aplicar</button>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="alert alert-primary alert-dismissible fade show hide" id="msg" role="alert">
            <strong>La Carga se ha Realizado Correctamente!</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

    <div class="modal fade" id="upfileSoModal" role="dialog" data-backdrop="static">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cargar Paquetes Sobrantes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>
                <form enctype="multipart/form-data" method="post" class="up_sobras" id="up_sobras" action="javascript:upload_file_sobrante()">
                    <input type="text" id="idpreclasificacion" name="idpreclasificacion" hidden>
                    <div class="modal-body">
                        <!-- <div class="container-fluid">
                            <div class="row">
                                <input type="file" name="file" id="file" class="w-50" required accept=".xls" />
                            </div>
                        </div>
                         -->
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" name="file" id="file" class="custom-file-input" required accept=".xls" />
                                <label class="custom-file-label shadow-sm" for="file">Seleccione archivo</label>
                            </div>
                        </div>

                    </div>

                    <br>

                    <div class="modal-footer">
                        <button type="submit" id="enviar" class="btn btn-primary">Aplicar </button>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalModificarManifiesto" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modificación de manifiestos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>

                <input type="text" id="idpreclasificacionm" name="idpreclasificacionm" hidden>


                <form id="frmUploadUpdateManifiesto" enctype="multipart/form-data" style="padding: 10px;" action="javascript:uploadUpdateManifiesto()">

                    <div class="form-group">
                        <label for="fileUpload" class="form-label">Subir Archivo(s)</label>
                        <div class="file-upload-box">
                            <div class="upload-icon">&#x2191;</div>
                            <div class="upload-text" id="file-label">Arrastra o suelta tu archivo para corregir</div>
                            <input type="file" id="fileUpload" name="fileUpload">
                        </div>
                    </div>

                    <div class="options-container">
                        <h3>Selecciona las opciones a modificar</h3>
                        <div class="options-list">
                            <label class="checkbox-item">
                                <input type="checkbox" name="opciones[]" value="consignee_email">
                                <span>Correo Electronico</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="opciones[]" value="consignee_account">
                                <span>Dui</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="opciones[]" value="consignee_phone">
                                <span>Telefono</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="opciones[]" value="consignee_address1">
                                <span>Dirección</span>
                            </label>

                            <label class="checkbox-item">
                                <input type="checkbox" name="opciones[]" value="consignee">
                                <span>Nombre cliente</span>
                            </label>

                            <label class="checkbox-item">
                                <input type="checkbox" name="opciones[]" value="consignee_state">
                                <span>Departamentos</span>
                            </label>

                            <label class="checkbox-item">
                                <input type="checkbox" name="opciones[]" value="consignee_city">
                                <span>Municipios</span>
                            </label>

                        </div>
                    </div>

                    <button class="btn-submit" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M12.935 3.328a.75.75 0 0 0-.256-1.157c-.52-.28-1.296-.28-1.761 0a4.508 4.508 0 0 0-.825.592l-.008.006c-.198.156-.47.21-.734.15a.75.75 0 0 0-.486.643v.151c-.007.21.054.41.168.583a4.508 4.508 0 0 0 1.096 1.135c.42.338.834.562 1.25.688.416.126.832.188 1.25.188.416 0 .832-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006z" />
                            <path d="M5.005 13.98c.416.126.832.188 1.25.188.416 0 .834-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006c-.47-.28-1.296-.28-1.761 0a4.508 4.508 0 0 0-.825.592l-.008.006c-.198.156-.47.21-.734.15a.75.75 0 0 0-.486.643v.151c-.007.21.054.41.168.583a4.508 4.508 0 0 0 1.096 1.135c.42.338.834.562 1.25.688.416.126.832.188 1.25.188.416 0 .832-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006zM9.405 1.015a.75.75 0 0 1 .158 1.488c-.689.155-1.29.544-1.748 1.157-.458.613-.715 1.346-.715 2.164v.104c.088.005.176.01.264.014h.02a.75.75 0 1 1-.02 1.498h-.02c-.088-.005-.176-.01-.264-.014v-.104c0-.793.226-1.503.663-2.09.437-.587 1.016-.967 1.696-1.12.05-.01.099-.02.149-.03a.75.75 0 0 1-.03-1.498z" />
                        </svg>
                        Procesar
                    </button>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modalArribo" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Fecha de arribo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>

                <form id="frmArribo" enctype="multipart/form-data" style="padding: 10px;" action="javascript:update_fechaArribo()">
                    <input type="hidden" id="id-master" name="id-master">
                    <div class="form-group">
                        <label for="fecha" class="form-label">fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha">

                    </div>

                    <button class="btn-submit " type="submit">
                        Procesar
                    </button>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalCargarClient" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modificar Cliente de Paquetes</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>

                <input type="hidden" id="idpreclasificacionclient" name="idpreclasificacionclient">

                <form id="frmChangeClientId" enctype="multipart/form-data" style="padding: 10px;" action="javascript:changeClientId()">


                    <!-- SELECT -->
                    <div class="form-group">
                        <label>Plataforma</label>
                        <select name="plataforma" class="form-control">
                            <option value="">Seleccione una opción</option>
                            <option value="Busca Libre">Busca Libre</option>
                            <option value="FashionNova">Fashion Nova</option>
                        </select>
                    </div>

                    <!-- TEXTAREA -->
                    <div class="form-group">
                        <label>Trackings</label>
                        <textarea name="trackings" class="form-control" rows="4" placeholder="Ingrese los trackings que desea aplicar"></textarea>
                    </div>

                    <!-- CHECKBOX -->
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="aplicar_todos" value="1">
                            Aplicar a todos los paquetes
                        </label>
                    </div>



                    <!-- BUTTON -->
                    <button class="btn-submit" type="submit">
                        Procesar
                    </button>

                </form>
            </div>
        </div>
    </div>




    <div class="modal fade" id="modalCargarDuca" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cargar DUCA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>

                <input type="hidden" id="idpreclasificacioncostos" name="idpreclasificacioncostos">


                <form id="frmUploadDUCA" enctype="multipart/form-data" style="padding: 10px;" action="javascript:uploadDuca()">

                    <div class="form-group">
                        <label for="fileUpload" class="form-label">Subir Archivo(s)</label>
                        <div class="file-upload-box">
                            <div class="upload-icon">&#x2191;</div>
                            <div class="upload-text" id="file-label">Arrastra o suelta tu archivo para corregir</div>

                            <?php
                            if ($_SESSION["pais"] == 2) {
                            ?>
                                <input type="file" id="fileUploadDuca" name="fileUploadDuca[]" multiple>
                            <?php
                            }
                            ?>

                            <?php
                            if ($_SESSION["pais"] == 1) {
                            ?>
                                <input type="file" id="fileUploadDuca" name="fileUploadDuca[]" multiple>
                            <?php
                            }
                            ?>

                            <?php
                            if ($_SESSION["pais"] == 3) {
                            ?>
                                <input type="file" id="fileUploadDuca" name="fileUploadDuca">
                            <?php
                            }
                            ?>

                        </div>
                    </div>

                    <div class="options-container">
                        <h3>Ingresar Almacenaje</h3>

                        <input type="text" name="txtValorAlmacenaje" value="">

                    </div>

                    <button class="btn-submit" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M12.935 3.328a.75.75 0 0 0-.256-1.157c-.52-.28-1.296-.28-1.761 0a4.508 4.508 0 0 0-.825.592l-.008.006c-.198.156-.47.21-.734.15a.75.75 0 0 0-.486.643v.151c-.007.21.054.41.168.583a4.508 4.508 0 0 0 1.096 1.135c.42.338.834.562 1.25.688.416.126.832.188 1.25.188.416 0 .832-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006z" />
                            <path d="M5.005 13.98c.416.126.832.188 1.25.188.416 0 .834-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006c-.47-.28-1.296-.28-1.761 0a4.508 4.508 0 0 0-.825.592l-.008.006c-.198.156-.47.21-.734.15a.75.75 0 0 0-.486.643v.151c-.007.21.054.41.168.583a4.508 4.508 0 0 0 1.096 1.135c.42.338.834.562 1.25.688.416.126.832.188 1.25.188.416 0 .832-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006zM9.405 1.015a.75.75 0 0 1 .158 1.488c-.689.155-1.29.544-1.748 1.157-.458.613-.715 1.346-.715 2.164v.104c.088.005.176.01.264.014h.02a.75.75 0 1 1-.02 1.498h-.02c-.088-.005-.176-.01-.264-.014v-.104c0-.793.226-1.503.663-2.09.437-.587 1.016-.967 1.696-1.12.05-.01.099-.02.149-.03a.75.75 0 0 1-.03-1.498z" />
                        </svg>
                        Procesar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="modal_detalle_documentos" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span>Resumen de archivos cargados</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;" id="resumen_archivos">





                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-arrow-circle-left"></i> Salir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modal_asignar_master" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span>Asignar # de Master</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">

                    <div class="logis-update-container">
                        <div class="logis-card-body" id="bodyChangeGuide">

                        </div>
                    </div>


                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-arrow-circle-left"></i> Salir
                    </button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal" id="modal_documentos_manifiesto" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span>Cargar documentos manifiesto</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;" id="documentos_manifiesto_body"></div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-arrow-circle-left"></i> Salir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCambiarPais" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cambiar de País Manifiesto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>

                <input type="hidden" id="idpreclasificacionchangecountry" name="idpreclasificacionchangecountry">


                <form id="frmChangeCountry" enctype="multipart/form-data" style="padding: 10px;" action="javascript:changeCountry()">

                    <div class="options-container">
                        <h3>País</h3>

                        <?php $this->load->view("catalogos/pais_ec", $pais); ?>


                    </div>

                    <div class="options-container">
                        <h3>Empresa</h3>

                        <div id="boxSelectCompanies">
                            Selecciona un pais
                        </div>

                    </div>



                    <button class="btn-submit" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M12.935 3.328a.75.75 0 0 0-.256-1.157c-.52-.28-1.296-.28-1.761 0a4.508 4.508 0 0 0-.825.592l-.008.006c-.198.156-.47.21-.734.15a.75.75 0 0 0-.486.643v.151c-.007.21.054.41.168.583a4.508 4.508 0 0 0 1.096 1.135c.42.338.834.562 1.25.688.416.126.832.188 1.25.188.416 0 .832-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006z" />
                            <path d="M5.005 13.98c.416.126.832.188 1.25.188.416 0 .834-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006c-.47-.28-1.296-.28-1.761 0a4.508 4.508 0 0 0-.825.592l-.008.006c-.198.156-.47.21-.734.15a.75.75 0 0 0-.486.643v.151c-.007.21.054.41.168.583a4.508 4.508 0 0 0 1.096 1.135c.42.338.834.562 1.25.688.416.126.832.188 1.25.188.416 0 .832-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006zM9.405 1.015a.75.75 0 0 1 .158 1.488c-.689.155-1.29.544-1.748 1.157-.458.613-.715 1.346-.715 2.164v.104c.088.005.176.01.264.014h.02a.75.75 0 1 1-.02 1.498h-.02c-.088-.005-.176-.01-.264-.014v-.104c0-.793.226-1.503.663-2.09.437-.587 1.016-.967 1.696-1.12.05-.01.099-.02.149-.03a.75.75 0 0 1-.03-1.498z" />
                        </svg>
                        Procesar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalProveedorLm" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Asignar Proveedor Ultima Milla</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>

                <input type="hidden" id="idpreclasificacionlm" name="idpreclasificacionlm">


                <form id="frmProveedorLm" enctype="multipart/form-data" style="padding: 10px;" action="javascript:setProveedorLm()">

                    <div class="options-container">
                        <h3>Selecciona un proveedor de Ultima Milla</h3>

                        <?php $this->load->view("catalogos/proveedores_lm", $proveedor_lm); ?>

                    </div>



                    <button class="btn-submit" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M12.935 3.328a.75.75 0 0 0-.256-1.157c-.52-.28-1.296-.28-1.761 0a4.508 4.508 0 0 0-.825.592l-.008.006c-.198.156-.47.21-.734.15a.75.75 0 0 0-.486.643v.151c-.007.21.054.41.168.583a4.508 4.508 0 0 0 1.096 1.135c.42.338.834.562 1.25.688.416.126.832.188 1.25.188.416 0 .832-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006z" />
                            <path d="M5.005 13.98c.416.126.832.188 1.25.188.416 0 .834-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006c-.47-.28-1.296-.28-1.761 0a4.508 4.508 0 0 0-.825.592l-.008.006c-.198.156-.47.21-.734.15a.75.75 0 0 0-.486.643v.151c-.007.21.054.41.168.583a4.508 4.508 0 0 0 1.096 1.135c.42.338.834.562 1.25.688.416.126.832.188 1.25.188.416 0 .832-.062 1.25-.188.416-.126.834-.35 1.25-.688.42-.338.75-.724.975-1.125a.75.75 0 0 0-.312-1.282l-.005.006zM9.405 1.015a.75.75 0 0 1 .158 1.488c-.689.155-1.29.544-1.748 1.157-.458.613-.715 1.346-.715 2.164v.104c.088.005.176.01.264.014h.02a.75.75 0 1 1-.02 1.498h-.02c-.088-.005-.176-.01-.264-.014v-.104c0-.793.226-1.503.663-2.09.437-.587 1.016-.967 1.696-1.12.05-.01.099-.02.149-.03a.75.75 0 0 1-.03-1.498z" />
                        </svg>
                        Procesar
                    </button>
                </form>
            </div>
        </div>
    </div>


    <script type="">

        $(document).ready(function(){

            $("#pais").on("change", function () {
                var idCountry = $(this).val();
                //alert(idCountry);
                var url = base_url("index.php/Catalogos/companyByCountry/"+idCountry);
                $.get(url, function(data) {
                    $("#boxSelectCompanies").html(data);
                }).fail(function(error) {
                    console.error(error);
                });
            });


        });


            

    </script>

</body>

</html>