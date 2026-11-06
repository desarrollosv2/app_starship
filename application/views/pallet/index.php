<div id="pantalla-pallet" data-retomar="<?php echo (int) $retomar_id; ?>">

    <section id="paso-config" class="row g-4">
        <div class="col-lg-5">
            <form class="form_nuevo_pallet panel h-100" onsubmit="crear_pallet(); return false;">
                <?php echo campo_csrf(); ?>
                <h2 class="panel-titulo"><i class="fa-solid fa-circle-plus"></i> Nuevo pallet</h2>
                <div class="mb-3">
                    <label for="cliente_id" class="filtro-label"><i class="fa-solid fa-user-tie"></i> Cliente</label>
                    <select id="cliente_id" name="cliente_id" class="form-select form-select-lg" onchange="validar_nuevo_pallet()">
                        <option value="">Seleccione...</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?php echo (int) $c->id; ?>"><?php echo html_escape($c->nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="tipo_producto_id" class="filtro-label"><i class="fa-solid fa-tags"></i> Tipo de producto</label>
                    <select id="tipo_producto_id" name="tipo_producto_id" class="form-select form-select-lg" onchange="validar_nuevo_pallet()">
                        <option value="">Seleccione...</option>
                        <?php foreach ($tipos_producto as $t): ?>
                            <option value="<?php echo (int) $t->id; ?>"><?php echo html_escape($t->nombre_legible); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" id="btn-crear" class="btn btn-naranja btn-lg w-100" disabled>
                    <i class="fa-solid fa-print"></i> Crear pallet e imprimir etiqueta
                </button>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="panel h-100">
                <h2 class="panel-titulo"><i class="fa-solid fa-box-open"></i> Pallets abiertos</h2>
                <form class="form_retomar mb-3" onsubmit="retomar_por_etiqueta(); return false;">
                    <?php echo campo_csrf(); ?>
                    <label for="codigo_pallet" class="filtro-label"><i class="fa-solid fa-barcode"></i> Retomar escaneando la etiqueta del pallet</label>
                    <input type="text" id="codigo_pallet" name="codigo_pallet" class="form-control form-control-lg" autocomplete="off" placeholder="Escanee el código de barras de la etiqueta">
                </form>
                <?php if (empty($abiertos)): ?>
                    <p class="text-muted mb-0">No hay pallets abiertos.</p>
                <?php else: ?>
                    <div class="tabla-cards tabla-cards-sm">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Cajas</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($abiertos as $p): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo codigo_pallet($p); ?></td>
                                        <td><?php echo html_escape($p->cliente_nombre); ?></td>
                                        <td><?php echo html_escape($p->tipo_legible); ?></td>
                                        <td><?php echo (int) $p->total_cajas; ?> / <?php echo (int) $p->max_cajas; ?></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="retomar_pallet(<?php echo (int) $p->id; ?>)">
                                                <i class="fa-solid fa-play"></i> Retomar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="paso-escaneo" class="row g-4 d-none">
        <div class="col-lg-5">
            <div class="panel">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Pallet activo</div>
                        <div class="pallet-numero">N° <span id="lbl-numero">000000</span></div>
                        <div class="text-muted" id="lbl-resumen"></div>
                    </div>
                    <div class="contador" id="contador">
                        <div class="contador-num"><span id="lbl-contador">0</span> / <span id="lbl-max">12</span></div>
                        <small>cajas</small>
                    </div>
                </div>

                <div class="progress mb-3" role="progressbar" aria-label="Cajas en el pallet">
                    <div class="progress-bar" id="barra" style="width: 0%"></div>
                </div>

                <div id="aviso-excede" class="alert alert-warning py-2 d-none">
                    <i class="fa-solid fa-triangle-exclamation"></i> Límite superado con consentimiento del usuario.
                </div>

                <form class="form_escaneo" onsubmit="escanear_caja(); return false;">
                    <label for="codigo_barras" class="filtro-label"><i class="fa-solid fa-barcode"></i> Escanee el código de barras de la caja</label>
                    <input type="text" id="codigo_barras" class="form-control form-control-lg mb-2" autocomplete="off" placeholder="Enfoque aquí y escanee">
                </form>
                <div id="feedback" class="feedback mb-3">&nbsp;</div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="#" target="_blank" id="btn-pdf" class="btn btn-outline-secondary"><i class="fa-solid fa-print"></i> Etiqueta</a>
                    <button type="button" id="btn-cerrar" class="btn btn-success" onclick="cerrar_pallet(false)"><i class="fa-solid fa-lock"></i> Cerrar pallet</button>
                    <a href="<?php echo site_url('PalletController'); ?>" class="btn btn-light ms-auto"><i class="fa-solid fa-arrow-left"></i> Volver</a>
                </div>

                <!-- Solo visibles si el pallet se creo hoy -->
                <div id="acciones-dia" class="d-flex flex-wrap gap-2 mt-2 d-none">
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="reiniciar_pallet_activo()"><i class="fa-solid fa-rotate-left"></i> Reiniciar pallet</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminar_pallet_activo()"><i class="fa-solid fa-trash-can"></i> Eliminar pallet</button>
                </div>
            </div>

            <div class="panel mt-4">
                <h2 class="panel-titulo"><i class="fa-solid fa-camera"></i> Fotografías del pallet</h2>
                <p class="text-muted small mb-3">Se requieren 2 fotografías para cerrar el pallet.</p>
                <?php $this->load->view('pallet/fotos_slots'); ?>
            </div>

            <!-- En celular/tablet abre la camara; en PC permite elegir una imagen -->
            <input type="file" id="input_foto" accept="image/*" capture="environment" class="d-none" onchange="subir_foto(this)">
        </div>

        <div class="col-lg-7">
            <div class="panel">
                <h2 class="panel-titulo"><i class="fa-solid fa-boxes-stacked"></i> Cajas escaneadas</h2>
                <div class="tabla-cards tabla-cards-sm">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Hora</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tabla-cajas"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Limite de cajas: al completar el pallet o al intentar agregar una caja por sobre el limite -->
<div class="modal fade" id="Modal_limite" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="Modal_limite_titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-limite">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Modal_limite_titulo"><i class="fa-solid fa-triangle-exclamation text-warning"></i> Pallet completo</h5>
            </div>
            <div class="modal-body">
                <p id="Modal_limite_mensaje" class="mb-2"></p>
                <small class="text-muted">Si continúa, quedará registrado que se superó el límite con su consentimiento.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light me-auto" onclick="cancelar_limite()">Decidir después</button>
                <button type="button" class="btn btn-success" onclick="cerrar_desde_limite()"><i class="fa-solid fa-lock"></i> Cerrar pallet</button>
                <button type="button" class="btn btn-naranja" onclick="consentir_limite()"><i class="fa-solid fa-plus"></i> Seguir agregando</button>
            </div>
        </div>
    </div>
</div>

<!-- Fotografias requeridas: se abre al intentar cerrar un pallet sin sus 2 fotos -->
<div class="modal fade" id="Modal_fotos" tabindex="-1" data-bs-backdrop="static" aria-labelledby="Modal_fotos_titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-limite">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Modal_fotos_titulo"><i class="fa-solid fa-camera text-warning"></i> Fotografías requeridas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="Modal_fotos_mensaje" class="mb-3"></p>
                <?php $this->load->view('pallet/fotos_slots'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light me-auto" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn-cerrar-con-fotos" class="btn btn-success" onclick="cerrar_con_fotos()" disabled><i class="fa-solid fa-lock"></i> Cerrar pallet</button>
            </div>
        </div>
    </div>
</div>