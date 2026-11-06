<style>
    .manifest-card {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .manifest-card:hover {
        border-color: #0d6efd;
        transform: translateY(-3px);
    }

    .manifest-card input[type="checkbox"] {
        transform: scale(1.2);
    }
</style>

<?php
if (isset($cliente)) {
    $cliente_id = $cliente;
} else {
    $cliente_id = "";
}

?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center datos_generales_pdo">
        <div class="col-xl-10 col-lg-11 col-md-12">

            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        Registro de Entrega POD
                    </h5>
                </div>

                <div class="card-body">
                    <form id="form_PDO">
                        <input type="hidden" id="client_id" name="client_id" value="<?= $cliente_id ?>">
                        <div class="row g-3 mb-4">

                            <div class="col-md-4">
                                <label class="form-label">Nombre del piloto</label>
                                <?php $this->load->view('catalogos/pilotos') ?>
                                <div class="invalid-feedback" id="error_piloto"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Placa</label>
                                <input type="text" class="form-control" id="placa" name="placa">
                                <div class="invalid-feedback" id="error_placa"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Marchamo</label>
                                <input type="text" class="form-control" name="marchamo" id="marchamo">
                                <div class="invalid-feedback" id="error_marchamo"></div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea name="direccion" id="direccion" rows="1" class="form-control">Sector Chamelecon, bulevar del Sur, 500 mejor antes del peaje, San Pedro Sula, Cortes.</textarea>
                                <div class="invalid-feedback" id="error_direccion"></div>
                            </div>
                        </div>

                        <hr>

                        <!-- MANIFIESTOS -->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                            <h6 class="text-primary mb-2 mb-md-0"><i class="fas fa-list-check me-1"></i> Seleccione manifiestos</h6>

                            <div style="width: 250px;">
                                <input type="text" id="buscador-manifiestos-pod" class="form-control form-control-sm" placeholder="Buscar manifiesto..." autocomplete="off">
                                <small class="text-muted d-none" id="contador-manifiestos"></small>
                            </div>
                        </div>

                        <div class="row">

                            <?php if (!empty($manifiestos)) : ?>
                                <?php foreach ($manifiestos as $item) : ?>
                                    <div class="col-md-4 col-lg-3 mb-3">
                                        <div class="card h-100 border-secondary shadow-sm manifest-card" data-manifiesto="<?php echo strtolower($item->manifiesto); ?>">
                                            <div class="card-body py-2 px-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="idpreclasificaciones[]" value="<?php echo $item->idpreclasificacion; ?>" id="man_<?php echo $item->idpreclasificacion; ?>">
                                                    <label class="form-check-label small" for="man_<?php echo $item->idpreclasificacion; ?>">
                                                        <span class="text-muted d-block">Manifiesto</span>

                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <strong><?php echo $item->manifiesto; ?></strong>
                                                            <?php if (!empty($item->proveedor_lm)) : ?>
                                                                <span class="badge bg-primary text-white ms-2">
                                                                    <i class="fa fa-truck"></i> <?php echo $item->proveedor_lm; ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>

                                                        <div class="mt-2 fs-6 text-muted d-flex align-items-center">
                                                            <span><i class="fa fa-archive"></i> Cajas: <strong><?php echo $item->total_cajas; ?></strong></span>
                                                            <span class="mx-3">|</span>
                                                            <span><i class="fa fa-cubes"></i> Paquetes: <strong><?php echo $item->total_paquetes; ?></strong></span>
                                                        </div>

                                                        <div class="mt-1 fs-6 d-flex align-items-center text-muted">
                                                            <span class="text-success"><i class="fa fa-check-circle"></i> <?= ($_SESSION['pais'] == 2) ? 'Procesados' : 'Procesadas'; ?>:<strong><?php echo $item->cajas_procesadas; ?></strong></span>
                                                            <span class="mx-3">|</span>
                                                            <span class="text-primary"><i class="fa fa-clock-o"></i> Pendientes: <strong><?php echo $item->cajas_pendientes; ?></strong></span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        No hay manifiestos disponibles
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="lista_pod()">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="crear_pod()">
                                Guardar
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="detalle_manifiesto_pdo"></div>
</div>