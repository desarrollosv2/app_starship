<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.2.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <title>Lista Manifiesto</title>


    <style>
        .input-group.shadow {
            box-shadow: 0 8px 30px rgba(0, 0, 0, .15) !important;
        }

        #id-manifiesto {
            height: 60px;
            font-size: 18px;
        }

        #btn-buscar {
            width: 70px;
            background: none;
            border: none;
        }

        .selectivo {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        .selectivo.verde {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .selectivo.rojo {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .check-modern {
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            margin: 0;
        }

        .check-modern input {
            display: none;
        }

        .check-modern span {

            width: 22px;
            height: 22px;

            border: 2px solid #d5dce5;
            border-radius: 7px;

            background: #fff;

            position: relative;

            transition: .25s;

        }



        .check-modern:hover span {

            border-color: #FE4F00;
            transform: scale(1.08);

        }

        .check-modern input:checked+span {

            background: linear-gradient(135deg, #FE4F00, #ff7b3d);
            border-color: #FE4F00;

        }

        .check-modern span:after {

            content: "";

            position: absolute;

            left: 6px;
            top: 2px;

            width: 6px;
            height: 11px;

            border: solid white;
            border-width: 0 2px 2px 0;

            transform: rotate(45deg) scale(0);

            transition: .2s;

        }

        .check-modern input:checked+span:after {

            transform: rotate(45deg) scale(1);

        }

        .valor-inconsistente {
            background: #dc3545 !important;
            color: #fff !important;
            font-weight: bold;
            animation: parpadear 1s infinite;
        }

        @keyframes parpadear {
            0% {
                opacity: 1;
            }

            50% {
                opacity: .45;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <?php $this->load->view("spinner", $this->datos); ?>


    <div class="container-fluid bg-primary" id="segmento-aplicar" style="display:none">
        <div class="row align-items-center bg-light p-3 rounded shadow-sm">
            <input type="hidden" id="reference_number">
            <input type="hidden" id="manifiest_number">
            <input type="hidden" id="manifiest_id">

            <div class="col-md-3 mb-3 mb-md-0">
                <h4 id="enca" class="text-secondary font-weight-bold mb-0"></h4>
            </div>

            <div class="col-md-3 mb-3 mb-md-0">
                <div class="form-group mb-0">
                    <?php $this->load->view("catalogos/estatus_new");  ?>
                </div>
            </div>
            <?php if ($_SESSION["user_id"] == 39109) { ?>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="form-group mb-0">
                        <input type="datetime-local" id="fecha_especifica" name="fecha_especifica" class="form-control form-control-sm border-secondary text-muted">
                    </div>
                </div>
            <?php } ?>

            <div class="col-md-3 text-md-right">
                <div class="btn-group btn-group-sm w-100 w-md-auto" role="group">
                    <button type="button" class="btn btn-primary" id="btn-aplicar">
                        <i class="fas fa-sync-alt mr-1"></i> Referencia
                    </button>
                    <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#miModalMaster">
                        <i class="fas fa-layer-group mr-1"></i> Master
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="btn-reset">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>

        <div class="col-sm-2 pr-0">

        </div>
    </div>

    <div class="modal fade" id="miModalMaster" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="miModalLabel">Aplicar estatus a Master</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Este proceso aplica estatus a toda la master
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="estatusMaster()">Aplicar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="MdlAsignarSelectivo" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="miModalLabel">Asignar selectivo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="frmAsignarSelectivo">
                        <input class="form-control input-sm" type="hidden" name="txtManifiesto" id="txtManifiesto" value="<?= isset($lista) ? $lista[0]->idpreclasificacion : '' ?>" readonly />
                        <input class="form-control input-sm" type="hidden" name="txtManifiestoAll" id="txtManifiestoAll" value="<?= isset($lista) ? $lista[0]->manifiesto : '' ?>" readonly />

                        <div class="form-group">
                            <label>Referencia</label>
                            <input class="form-control input-sm" type="text" name="txtReferencia" id="txtReferencia" value="" readonly />
                        </div>

                        <div class="form-group">
                            <label>Selectivo</label>
                            <select class="form-control" name="selectSelectivo">

                                <option value="verde">Verde</option>
                                <option value="rojo">Rojo</option>

                            </select>
                        </div>

                        <div class="form-group">

                            <button class="btn btn-success pull-right">

                                Asignar

                            </button>

                        </div>
                    </form>

                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="resumenXMaster" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 90vw;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="miModalLabel">Resumen del manifiesto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tblResumen">
                            <thead>
                                <tr>
                                    <th>Referencia</th>
                                    <th>Total</th>
                                    <th>Recibido</th>
                                    <th>Fecha</th>
                                    <th>Proceso Desp.</th>
                                    <th>Fecha</th>
                                    <th>Demorado</th>
                                    <th>Fecha</th>
                                    <th>Liberado</th>
                                    <th>Fecha</th>
                                    <th>Entregado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>





                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>



    <div class="container-fluid m-top " id="segmento-general">
        <div class="row justify-content-center mt-1">

            <div class="col-md-8">

                <h3 class="text-center font-weight-bold mb-4">
                    <i class="fas fa-box-open text-primary"></i>
                    Consulta de Manifiesto
                </h3>

                <div class="input-group shadow" style="border-radius:50px;overflow:hidden;">

                    <input type="text"
                        class="form-control border-0"
                        id="id-manifiesto"
                        placeholder="Ingrese el número del manifiesto">

                    <div class="input-group-append">

                        <button class="px-5" id="btn-buscar">
                            <i class="fas fa-search"></i>
                        </button>

                    </div>

                </div>

            </div>

        </div>

        <div id="contenedorProgreso" style="display:none; margin-top:15px;">
            <div class="progress">
                <div id="barraProgreso"
                    class="progress-bar progress-bar-success progress-bar-striped active"
                    role="progressbar"
                    style="width:0%">
                    0%
                </div>
            </div>

            <div id="lblProgreso" class="text-center">
                Preparando...
            </div>
        </div>

        <div class="card shadow-lg border-0 mt-4">

            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h4 class="mb-0 font-weight-bold">
                            <i class="fas fa-shipping-fast text-primary"></i>
                            Manifiestos
                        </h4>

                        <small class="text-muted">
                            Listado de referencias disponibles
                        </small>
                    </div>

                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="form-group mb-0">
                            <?php $this->load->view("catalogos/estatus_by_chk");  ?>
                        </div>
                    </div>

                    <button class="btn btn-success" id="btn-aplicar-test">
                        <i class="fa fa-check"></i>
                        Aplicar Estatus
                    </button>

                    <button class="btn btn-info" id="btnVerDetalle">
                        <i class="fa fa-th-list"></i>
                        Ver detalle por master
                    </button>


                    <a href="https://stargateserp.com/app_starship/index.php/WhController/descargar_tiempos/<?= isset($lista) ? $lista[0]->idpreclasificacion : '' ?>" target="__blank" class="btn btn-dark" id="btnDownRpt">
                        <i class="fa-solid fa-clock"></i>
                        Descargar reporte control
                    </a>

                    <input type="hidden" id="txtIdPre" value="<?= isset($lista) ? $lista[0]->idpreclasificacion : '' ?>">

                    <span class="badge badge-primary px-3 py-2">
                        <?php echo isset($lista) ? count($lista) : 0; ?> registros
                    </span>



                </div>
            </div>





            <div class="table-responsive">

                <table class="table table-modern mb-0" id="tbl-manifiesto">

                    <thead>

                        <tr>
                            <th width="40">
                                <label class="check-modern">
                                    <input type="checkbox"
                                        class="chk-referencia"
                                        id="check-all">

                                    <span></span>
                                </label>
                            </th>
                            <th>#</th>
                            <th>AWB</th>
                            <th>Referencia</th>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Selectivo</th>
                            <th class="text-center">Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        if (isset($lista)) {

                            $c = 1;

                            foreach ($lista as $row) {





                        ?>

                                <tr>
                                    <td>
                                        <label class="check-modern">
                                            <input type="checkbox"
                                                class="chk-referencia"
                                                value="<?php echo $row->nombre_referencia; ?>"
                                                data-id="<?php echo $row->idpreclasificacion; ?>"
                                                data-manifiesto="<?php echo $row->manifiesto; ?>">

                                            <span></span>
                                        </label>
                                    </td>

                                    <td>
                                        <span class="badge badge-light">
                                            <?php echo $c; ?>
                                        </span>
                                    </td>

                                    <td>

                                        <strong>
                                            <?php echo $row->manifiesto; ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <?php echo $row->nombre_referencia; ?>

                                    </td>

                                    <td>

                                        <?php echo $row->fecha; ?>

                                    </td>

                                    <td>

                                        <?php echo $row->descripcion; ?>

                                    </td>

                                    <td>
                                        <span class="selectivo <?= strtolower($row->selectivo) ?>">
                                            <?= ucfirst($row->selectivo) ?>
                                        </span>
                                    </td>

                                    <td class="text-center">

                                        <button
                                            class="btn btn-action"
                                            onclick="aplicar_estatus_referencia(
                                <?php echo $row->idpreclasificacion; ?>,
                                '<?php echo $row->manifiesto; ?>',
                                '<?php echo $row->nombre_referencia; ?>'
                            )">

                                            <i class="fas fa-cogs"></i>

                                        </button>


                                        <button onclick="asignarSelectivo(<?= $row->nombre_referencia ?>)"
                                            class="btn btn-warning btn-sm">

                                            Asignar selectivo

                                        </button>

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
    </div>


    <script>
        $('#opciones').on('change', function() {
            opcion = this.value;
            if (opcion == 3 || opcion == 4 || opcion == 5) {
                $("#f-ini").show();
                $("#f-fin").show();
                $("#b-buscar").show();
                $("#buscar").hide();
            } else {
                $("#f-ini").hide();
                $("#f-fin").hide();
                $("#b-buscar").hide();
                $("#buscar").show();


            }
        });


        function asignarSelectivo(referencia) {
            $("#MdlAsignarSelectivo").modal("toggle");
            $("#txtReferencia").val(referencia)
        }

        $(document).ready(function() {

            $("#check-all").on("change", function() {
                $(".chk-referencia").prop("checked", $(this).prop("checked"));
            });

            $('#frmAsignarSelectivo').on('submit', function(e) {

                e.preventDefault();

                $.ajax({
                    url: '<?= base_url("WhController/asignar_selectivo") ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',

                    success: function(response) {

                        if (response.status) {
                            //alert(response.message);
                            estatus_referencias($("#txtManifiestoAll").val());
                            $(".modal-backdrop").css("display", "none");

                        } else {
                            alert(response.message);
                        }

                    },

                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert('Ocurrió un error al procesar la solicitud.');
                    }
                });

            });






            $("#btnVerDetalle").on('click', function(event) {


                $("#resumenXMaster").modal("toggle");

                var idManifiesto = $("#txtIdPre").val();

                //alert(idManifiesto);

                $.ajax({
                    url: base_url("index.php/Gestion/obtener_resumen_referencias/"),
                    type: "POST",
                    dataType: "json",
                    data: {
                        id_manifiesto: idManifiesto
                    },
                    beforeSend: function() {
                        $("#tblResumen tbody").html(
                            '<tr><td colspan="10" class="text-center">Cargando...</td></tr>'
                        );
                    },
                    success: function(resp) {

                        var html = "";

                        let totalPaquetes = 0;
                        let totalRecibido = 0;
                        let totalProceso = 0;
                        let totalDemorado = 0;
                        let totalLiberado = 0;
                        let totalEntregado = 0;

                        if (resp.data.length == 0) {
                            html += '<tr>';
                            html += '<td colspan="10" class="text-center">Sin registros</td>';
                            html += '</tr>';
                        } else {


                            $.each(resp.data, function(i, row) {

                                totalPaquetes += parseInt(row.total_paquetes) || 0;
                                totalRecibido += parseInt(row.recibido_aduana) || 0;
                                totalProceso += parseInt(row.pro_despacho_aduana) || 0;
                                totalDemorado += parseInt(row.demorado_aduana) || 0;
                                totalLiberado += parseInt(row.liberado_aduana) || 0;
                                totalEntregado += parseInt(row.entregado_ultima_milla) || 0;

                                let inconsistente =
                                    parseInt(row.recibido_aduana) !== parseInt(row.total_paquetes) ||
                                    parseInt(row.liberado_aduana) !== parseInt(row.total_paquetes) ||
                                    parseInt(row.entregado_ultima_milla) !== parseInt(row.total_paquetes) ||
                                    parseInt(row.pro_despacho_aduana) !== parseInt(row.total_paquetes);

                                html += "<tr class='" + (inconsistente ? "table-danger" : "") + "'>";

                                //html += "<tr>";

                                html += "<td>" + row.referencia + "</td>";
                                html += "<td class='text-center bg-primary text-white'><strong>" + row.total_paquetes + "</strong></td>";

                                // Recibido en Aduana
                                let claseRecibido = (parseInt(row.recibido_aduana) !== parseInt(row.total_paquetes)) ?
                                    "bg-danger text-white font-weight-bold" :
                                    "bg-success text-white";

                                html += "<td class='text-center " + claseRecibido + "'>" + row.recibido_aduana + "</td>";
                                html += "<td>" + (row.fecha_recibido ?? "") + "</td>";

                                //Proceso despacho
                                let claseProceso = (parseInt(row.pro_despacho_aduana) !== parseInt(row.total_paquetes)) ?
                                    "bg-danger text-white font-weight-bold" :
                                    "bg-success text-white";

                                html += "<td class='text-center " + claseProceso + "'>" + row.pro_despacho_aduana + "</td>";
                                html += "<td>" + (row.fecha_pro_despacho ?? "") + "</td>";

                                // Demorado en Aduana
                                let claseDemorado = (parseInt(row.demorado_aduana) > 0) ?
                                    "bg-warning font-weight-bold" :
                                    "";

                                html += "<td class='text-center " + claseDemorado + "'>" + row.demorado_aduana + "</td>";
                                html += "<td>" + (row.fecha_demorado ?? "") + "</td>";

                                // Liberado en Aduana
                                let claseLiberado = (parseInt(row.liberado_aduana) !== parseInt(row.total_paquetes)) ?
                                    "bg-danger text-white font-weight-bold" :
                                    "bg-success text-white";

                                html += "<td class='text-center " + claseLiberado + "'>" + row.liberado_aduana + "</td>";
                                html += "<td>" + (row.fecha_liberado ?? "") + "</td>";

                                // Entregado a Última Milla
                                let claseEntregado = (parseInt(row.entregado_ultima_milla) !== parseInt(row.total_paquetes)) ?
                                    "bg-danger text-white font-weight-bold" :
                                    "bg-success text-white";

                                html += "<td class='text-center " + claseEntregado + "'>" + row.entregado_ultima_milla + "</td>";
                                html += "<td>" + (row.fecha_entregado ?? "") + "</td>";

                                html += "</tr>";

                            });
                        }


                        html += `
                        <tr class="table-dark font-weight-bold">
                            <td><strong>TOTALES</strong></td>

                            <td class="text-center bg-primary text-white">${totalPaquetes}</td>

                            <td class="text-center bg-primary text-white">${totalRecibido}</td>
                            <td></td>

                            <td class="text-center bg-primary text-white">${totalProceso}</td>
                            <td></td>

                            <td class="text-center bg-warning">${totalDemorado}</td>
                            <td></td>

                            <td class="text-center bg-primary text-white">${totalLiberado}</td>
                            <td></td>

                            <td class="text-center bg-primary text-white">${totalEntregado}</td>
                            <td></td>
                        </tr>`;

                        $("#tblResumen tbody").html(html);

                    },
                    error: function() {
                        alert("Error al consultar la información.");
                    }
                });




            });

            $("#btn-buscar").on('click', function(event) {
                estatus_referencias($("#id-manifiesto").val());
                event.preventDefault();
            });

            $("#btn-reset").on('click', function(event) {
                $("#segmento-aplicar").hide();
                $("#segmento-general").show();
                event.preventDefault();
            });

            $("#btn-aplicar").on('click', function(event) {
                guardar_estatus_referencias(1, 1, 1);
                event.preventDefault();
            });


            async function aplicarSeleccionados() {

                const checks = $(".chk-referencia:checked");

                if (checks.length === 0) {
                    alert("Seleccione al menos una referencia.");
                    return;
                }

                //$("#delivery-loader").show();
                $("#contenedorProgreso").show();

                const total = checks.length;
                let procesados = 0;

                const id_estatus = $("#estatuschk").val();
                const nombre_estatus = $("#estatuschk option:selected").text();
                const fecha_especifica = $("#fecha_especifica").val();

                for (const chk of checks) {

                    const referencia = chk.value;
                    const manifiesto = $(chk).data("manifiesto");
                    const id = $(chk).data("id");

                    $("#lblProgreso").html(
                        `Procesando referencia <b>${referencia}</b> (${procesados + 1} de ${total})`
                    );

                    try {

                        await guardar_estatus_referencias_by_chk(
                            id,
                            manifiesto,
                            referencia,
                            id_estatus,
                            nombre_estatus,
                            fecha_especifica
                        );

                    } catch (e) {

                        console.error("Error en referencia:", referencia, e);

                    }

                    procesados++;

                    let porcentaje = Math.round((procesados / total) * 100);

                    $("#barraProgreso")
                        .css("width", porcentaje + "%")
                        .text(porcentaje + "%");

                }

                $("#lblProgreso").html("<b>Proceso finalizado</b>");

                //$("#delivery-loader").hide();

                setTimeout(function() {

                    $("#contenedorProgreso").hide();

                    $("#barraProgreso")
                        .css("width", "0%")
                        .text("0%");

                }, 1500);

                Swal.fire({
                    icon: "success",
                    title: "Proceso finalizado",
                    text: `${procesados} referencias procesadas correctamente.`
                });

            }

            $("#btn-aplicar-test").click(aplicarSeleccionados);
        });
    </script>

    <script>
        $('#estatus').select2({
            theme: 'bootstrap4',
        });
        $('#estatuschk').select2({
            theme: 'bootstrap4',
        });
    </script>

    <script>
        $(document).ready(function() {
            $("#spinner-placeholder").load("spinner.php");
        });
    </script>
</body>

</html>