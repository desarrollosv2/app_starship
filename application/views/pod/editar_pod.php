<?php
if (isset($encabezado)) {
    $id_pod         = $encabezado->id;
    $fecha_entrega  = $encabezado->fecha_entrega;
    $id_piloto      = $encabezado->id_piloto;
    $placa          = $encabezado->placa;
    $marchamo       = $encabezado->marchamo;
    $direccion      = $encabezado->direccion;
} else {
    $id_pod         = "";
    $fecha_entrega  = "";
    $id_piloto      = "";
    $placa          = "";
    $marchamo       = "";
    $direccion      = "";
}
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center datos_generales_pdo">
        <div class="col-xl-10 col-lg-11 col-md-12">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" id="regresar_lista_pod" onclick="lista_pod()">Regresar</button>
                <button type="button" class="btn btn-primary" id="btn_editar_encabezado" onclick="editar_encabezado();"> Editar encabezado</button>
            </div>
            <div class="card shadow-sm border-primary d-none" id="div_form_encabezadopod">
                <div class="card-header bg-primary text-white">
                    <span id="form_editar_titulo"></span>
                </div>
                <div class="card-body">

                    <form id="form_encabezadopod">
                        <input type="hidden" id="id_pod" name="id_pod" value="<?= $id_pod ?>">
                        <input type="hidden" id="manifiesto_pod_id" name="manifiesto_pod_id" value="">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Fecha entrega</label>
                                <input type="date" class="form-control" name="fecha_entrega" id="fecha_entrega" value="<?= $fecha_entrega ?>">
                                <div class="invalid-feedback" id="error_fecha_entrega"></div>
                            </div>
                            <div class="col-md-4">
                                <input type="hidden" id="id_piloto" name="id_piloto" value="<?= $id_piloto ?>">
                                <label class="form-label">Nombre del piloto</label>
                                <?php $this->load->view('catalogos/pilotos') ?>
                                <div class="invalid-feedback" id="error_piloto"></div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Placa</label>
                                <input type="text" class="form-control" id="placa" name="placa" value="<?= $placa ?>">
                                <div class="invalid-feedback" id="error_placa"></div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Marchamo</label>
                                <input type="text" class="form-control" name="marchamo" id="marchamo" value="<?= $marchamo ?>">
                                <div class="invalid-feedback" id="error_marchamo"></div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea name="direccion" id="direccion" rows="1" class="form-control"><?= htmlspecialchars($direccion) ?></textarea>
                                <div class="invalid-feedback" id="error_direccion"></div>
                            </div>
                        </div>
                    </form>

                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-primary d-none" id="crear_pod" onclick="guardar_encabezado_pdo()">Guardar</button>
                        <button type="button" class="btn btn-secondary d-none" id="btn_cancelar_editar_encabezado" onclick="cancelar_encabezado_pod();"> Cancelar</button>
                    </div>
                </div>

            </div>

            <div class="contenido_detalle">
                <div id="desglosedePOD"></div>
                <div id="contenedorDetalle"></div>
                <div id="contenedorDetalleGT"></div>
            </div>
            <div class="d-flex gap-2 mt-1">
                <button type="button" class="btn btn-outline-primary btn_add_gt_pod" onclick="agregar_paquetes_gt_edit(<?= $id_pod ?>);">
                    Agregar paquetes
                </button>

                <button type="button" class="btn btn-outline-danger btn_del_gt_pod" onclick="quitar_paquetes_gt_edit(<?= $id_pod ?>);">
                    Quitar paquetes
                </button>

                <button type="button" class="btn btn-outline-danger" onclick="quitar_paquetes_gt_todo(<?= $id_pod ?>);">
                    Quitar todos los paquetes
                </button>
            </div>
        </div>

    </div>

</div>

<div class="modal fade" id="modal_paquetes_gt" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span id="title_paquetes_gt"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="body_paquetes_gt_pod">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnPaquetesGTPOD">Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>