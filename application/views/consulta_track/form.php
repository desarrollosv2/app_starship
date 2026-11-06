<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <title>Consulta Tracking</title>
</head>
<style>
    table {
        font-family: 'Roboto', sans-serif !important;
        font-size: 12px;
    }

    .dropdown-options {
        display: none;
        position: relative;
        overflow: auto;
    }

    .dropdown:hover .dropdown-options {
        display: block;
    }

    .btn {
        border-radius: 50px !important;
    }

    .form-control {
        border-radius: 50px !important;
    }



    .tracking-card {
        background: #fff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .08);
        border: 1px solid #f1f1f1;
    }

    .tracking-card .form-control,
    .tracking-card .custom-select {
        height: 48px;
        border-radius: 10px;
        border: 1px solid #dcdcdc;
        font-size: 15px;
    }

    .tracking-card .form-control:focus,
    .tracking-card .custom-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .15);
    }

    .tracking-card .btn-search {
        height: 48px;
        border-radius: 10px;
        font-weight: 600;
        width: 100%;
    }

    .tracking-card .form-check-label {
        font-weight: 500;
        cursor: pointer;
    }

    @media (max-width:768px) {

        .tracking-card .col-md-2,
        .tracking-card .col-md-3,
        .tracking-card .col-md-5 {
            margin-bottom: 12px;
        }
    }
</style>

<body>

    <div class="container mt-3">

        <div class="tracking-card">

            <div class="row align-items-center">

                <!-- Tipo de búsqueda -->
                <div class="col-md-3">
                    <label class="font-weight-bold mb-1">
                        Buscar por
                    </label>

                    <select class="custom-select" id="tipo_busqueda">
                        <option value="tracking">Tracking Number</option>
                        <option value="package">Package Number</option>
                    </select>
                </div>

                <!-- Tracking -->
                <div class="col-md-5">
                    <label class="font-weight-bold mb-1">
                        Número
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="tracking-number"
                        name="tracking-number"
                        placeholder="Ingrese el número...">
                </div>

                <!-- Botón -->
                <div class="col-md-2">
                    <label class="d-block">&nbsp;</label>

                    <button
                        type="button"
                        class="btn btn-primary btn-search"
                        onclick="buscar_tracking();"
                        id="buscar_tracking">

                        <i class="fa fa-search mr-1"></i>
                        Buscar
                    </button>
                </div>

                <!-- Checkbox -->
                <div class="col-md-2">
                    <label class="d-block">&nbsp;</label>

                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            id="miCheckbox">

                        <label class="form-check-label" for="miCheckbox">
                            Guía automática
                        </label>
                    </div>

                </div>

            </div>

        </div>

    </div>
    
    <div class="container-fluid m-top " id="contenido-principal">

        <div class="table-responsive">
            <div class="row">
                <div class="col-sm-6"></div>
            </div>

            <table class="table custom-table" id="tbl_detalle">
                <thead>
                    <tr>
                        <th scope="col" style="display:none">idguia</td>
                        <th scope="col">#</td>


                        <th scope="col">Master </td>
                        <th scope="col">Referencia</td>
                        <th scope="col">Tracking Number</td>
                        <th scope="col">Tracking Replace</td>
                        <th scope="col">Weight</td>
                        <th scope="col">Value</td>
                        <th scope="col">Items</td>
                        <th scope="col">Items Description</td>
                        <th scope="col">Partida</td>
                        <th scope="col">Buyer id</td>
                        <th scope="col">Buyer</td>
                        <th scope="col">Buyer address1</td>
                        <th scope="col">Buyer city </td>
                        <th scope="col">Buyer state</td>
                        <th scope="col">Buyer phone</td>
                        <th scope="col">Buyer email </td>
                        <th scope="col">HTS</td>
                        <th scope="col">Pieces</td>
                        <th scope="col" class="font-weight-bold">Partida</td>
                        <th scope="col" class="font-weight-bold">Producto</td>
                        <th scope="col" class="font-weight-bold">Departamento</td>
                        <th scope="col" class="font-weight-bold">Municipio</td>
                        <th scope="col" class="font-weight-bold">Tipo entrega</td>
                        <th scope="col" class="font-weight-bold">Tipo servicio</td>

                        <th scope="col" class="font-weight-bold">IVA</td>
                        <th scope="col" class="font-weight-bold">DAI</td>
                        <th scope="col" class="font-weight-bold">Manejo</td>
                        <th scope="col" class="font-weight-bold">Almacenaje</td>

                        <th scope="col" class="font-weight-bold">Cobro Final</td>
                        <th scope="col" class="font-weight-bold">Recolecta</td>
                        <th scope="col" class="font-weight-bold">Guia</td>
                        <th scope="col" class="font-weight-bold">POD</td>
                        <th scope="col" class="font-weight-bold">Estado</td>
                        <th scope="col" class="font-weight-bold">Sobrante</td>
                        <th scope="col" class="font-weight-bold">Bag Number</td>

                        <th scope="col" colspan="6">Acciones</td>
                    </tr>
                </thead>

                <tbody id="tabla_busqueda_track"></tbody>
            </table>
        </div>
    </div>
    </div>
    </div>
    </div>

    <!-- actualizar manifiesto -->
    <div class="modal fade" id="guia_express_modal2" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Crear Guia </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <input type="hidden" id="id-tracking01" name="id-tracking01">
                <form id="dm_list">
                    <div id="listado_dpart"></div>
                </form>

                <div class="modal-footer">
                    <button type="button" id="save_manifiesto_ups_consulta" class="btn btn-primary float-md-right">Guardar</button>
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal  para asignar agencia-->
    <div class="modal fade" id="Modal_asignar_agencia" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title" id="" style="color: #F15A29">Cambiar Datos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_agencia" id="add_agencia" action="javascript:update_info_consulta()">
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

                        <button class="btn btn-primary float-md-right">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--- modal para mostrar guias en Express-->
    <div class="modal fade" id="guia_express_modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h6 class="modal-title" style="color:#F15a29">Crear guia</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form enctype="multipart/form-data" class="guia_ex" id="guia_ex" action="javascript:crear_guia_express_consulta();">
                    <div class="modal-body">
                        <?php $this->load->view("verifica");  ?>
                        <input type="hidden" id="order_number" name="order_number">
                        <input type="hidden" id="depto" name="depto">
                        <input type="hidden" id="munic" name="munic">

                        <h6 id="tracking-number" class="text-center font-weight-bold"></h6>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="actualizarDatos" id="actualizarDatos">
                            <label class="form-check-label" for="actualizarDatos">
                                Actualizar datos
                            </label>
                        </div>
                        <div class="div" id="bloque-complemento" style="display:none">
                            <div class="form-group">
                                <label for="">Servicio:</label>
                                <div id="listado_servicios"></div>

                                <label for="">Departamento :</label>
                                <div id="listado_departamentos"></div>

                                <label for="" id="lmunicipio" name="lmunicipio">Municipio:</label>
                                <div id="listado_municipios"></div>
                            </div>

                            <div class="form-group">
                                <label for="direccion">Direcci��n:</label>
                                <input type="text" class="form-control" id="direccion" placeholder="Introduzca Direcci��n" name="direccion">
                                <div class="valid-feedback">Valid.</div>
                                <div class="invalid-feedback">Please fill out this field.</div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary float-sm-right" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary float-sm-right" id="makeGuide">Crear</button>
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
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Para Reemplazar Numero de Guias -->
    <div class="modal fade" id="modal_reemplace" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
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
                    <button type="button" class="btn btn-primary float-md-right" onclick="reemplazar_tracking_consulta(<?php echo $row->id ?>)">Aceptar</button>
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!--- modal para mostrar historial de cambios de guias -->
    <div class="modal fade " id="modal_historial" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered mimodal " role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Historial de Cambios</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="row-fluid">
                    <div class="messageup" id='messageup'></div>
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
                            <input type="text" class="form-control" placeholder=" " id="tracking_number" name="tracking_number" value="<?php echo $row->tracking_number; ?>" readonly>
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

    <!-- Modal Para Reemplazar Numero de Guias -->
    <div class="modal fade" id="modal_reemplace1" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
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
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar</button>
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
        $("#agencia").change(function() {
            if ($("#agencia").val() == 0) {
                $("#tipo_entrega").val("NRML");
                $("#tipo_servicio").val("CCE");
            } else {
                var texto = $(this).find('option:selected').text();
                $("#direccion").val(texto);
                $("#tipo_entrega").val("ECON");
                $("#tipo_servicio").val("CCE");
            }
        });



        var input = document.getElementById("tracking-number");

        input.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("buscar_tracking").click();
            }
        });

        $('#save_manifiesto_ups_consulta').on('click', function() {
            var id_manifiesto = $('#id_manifiesto').val();
            var departamentos = $('#ls_depart').val();
            var municipios = $('#ls_municipio').val();
            var formData;
            var formData = new FormData($("#dm_list")[0]);
            var url = base_url("index.php/WhController/update_depart");
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $(".modal-backdrop").remove();
                    $("#guia_express_modal2").modal("hide");
                    lista_warehouse_consulta($("#tracking-number").val());
                },
                error: function() {},
            });
        });
    </script>
    <script>
        $("#tbl_detalle").on("click", "tbody tr", function(event) {

            //  var cobro = this.cells[26].innerHTML; //valor de segunda columna


            //   if (cobro<=0){
            //        alertify.set("notifier", "position", "top-right");
            //          alertify.error("No puede crear guias con valores menores o iguales a cero(0)!");
            //   }

        });
    </script>
</body>

</html>