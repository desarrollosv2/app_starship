<style>
    .filtro-conciliacion {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 18px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .05);
    }

    .titulo-conciliacion {
        font-size: 24px;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 5px;
    }

    .filtro-conciliacion label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .btn-accion {
        min-width: 120px;
    }

    /* Botones alineados a la derecha */
    .acciones-conciliacion {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        width: 100%;
    }

    @media (max-width: 991px) {
        .acciones-conciliacion {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767px) {
        .acciones-conciliacion {
            flex-direction: column;
            align-items: stretch;
        }

        .acciones-conciliacion .btn-accion {
            width: 100%;
        }
    }
</style>


<div class="container-fluid">

    <div class="filtro-conciliacion mt-4">

        <!-- Título -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="titulo-conciliacion">
                    <i class="fa fa-exchange text-warning"></i>
                    Conciliación de pago de impuestos TEMU
                </div>
            </div>
        </div>


        <!-- Filtros -->
        <div class="row align-items-end">

            <!-- Empresa -->
            <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
                <label>Empresa</label>
                <?php $this->load->view('catalogos/empresa') ?>
            </div>


            <!-- Estado -->
            <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
                <label>Estado del pago</label>

                <select name="estado_pago"
                    id="estado_pago"
                    class="form-control">

                    <option value="" disabled selected>
                        Seleccione una opción
                    </option>

                    <option value="1">Pagados</option>
                    <option value="2">Pendientes</option>

                </select>
            </div>


            <!-- Master -->
            <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
                <label>Master</label>

                <input type="text"
                    name="master"
                    id="master"
                    class="form-control"
                    placeholder="Master">
            </div>


            <!-- Referencia -->
            <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
                <label>Referencia</label>

                <input type="text"
                    name="referencia"
                    id="referencia"
                    class="form-control"
                    placeholder="Referencia">
            </div>


            <!-- Botones -->
            <div class="col-xl-4 col-lg-12 col-md-12">

                <div class="acciones-conciliacion">

                    <button type="button"
                        class="btn btn-outline-secondary btn-accion"
                        onclick="buscar_conciliacion_temu()">

                        <i class="fa fa-search"></i>
                        Buscar

                    </button>

                    <button type="button"
                        class="btn btn-outline-success btn-accion"
                        onclick="cargar_detalle_impuesto_temu()">

                        <i class="fa fa-upload"></i>
                        Cargar

                    </button>

                </div>

            </div>

        </div>

    </div>

    <div id="div_dash_detalle_impuestos_temu"></div>
    <div id="div_tbl_detalle_impuestos_temu"></div>

</div>


<!-- Modal para cargar los archivos de excel de TEMU -->
<div class="modal fade" id="carga_archivos_impuestos_temu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cargar detalle de impuestos pagados TEMU</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="div_form_cargar_detalle_impuestos_temu">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jszip/jszip.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>