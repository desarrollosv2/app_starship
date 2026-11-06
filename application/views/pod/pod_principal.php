<style>
    .table-sm td,
    .table-sm th {
        padding: 0.35rem 0.5rem !important;
        font-size: 0.85rem;
    }
</style>


<div class="container-fluid">
    <div class="row mt-2 align-items-center">

        <!-- Título -->
        <div class="col-md-4 ml-5">
            <h2>POD</h2>
        </div>

        <!-- Filtros -->
        <div class="col-md-7">
            <form id="form_buscar_manifiesto_pod">
                <div class="form-row justify-content-end align-items-end">

                    <div class="form-group mr-2" style="height: 100px;">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde">
                        <div class="invalid-feedback" id="error_fecha_desde"></div>
                    </div>

                    <div class="form-group mr-2" style="height: 100px;">
                        <label for="fecha_hasta">Fecha hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta">
                        <div class="invalid-feedback" id="error_fecha_hasta"></div>
                    </div>

                    <div class="form-group mr-2" style="height: 100px;">
                        <label for="empresa">Empresas</label>
                        <?php $this->load->view('catalogos/empresa'); ?>
                        <div class="invalid-feedback" id="error_empresa"></div>
                    </div>

                    <div class="form-group mr-2" style="height: 100px;">
                        <label for="proveedor_lm">Proveedor</label>
                        <?php $this->load->view('catalogos/proveedores_lm'); ?>
                        <div class="invalid-feedback" id="error_proveedor_lm"></div>
                    </div>

                    <!-- Botón al nivel del input -->
                    <div class="form-group mb-0" style="height: 100px;">
                        <button type="button" class="btn btn-primary" onclick="buscar_manifiesto_pod()">
                            Buscar manifiesto
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>

    <hr class="borde-orange mt-0">

    <div id="div_tbl_pod"></div>

    <div class="modal fade" id="modal_anular_pod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="master_anular_pod"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="anular_pod">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
                    <button type="button" class="btn btn-danger" onclick="guardar_anulacion_pod()">Anular</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para subir comprobante de entrega -->
    <div class="modal" id="modal_comprobante_pod" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Comprobante de entrega POD: <span id="pod_comprobante_master"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;" id="comprobantes_pod_body">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-arrow-circle-left"></i> Salir
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>