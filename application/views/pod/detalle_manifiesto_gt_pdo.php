<div class="container-fluid mt-2">


    <div class="d-flex justify-content-between align-items-center mb-2">

        <ul class="nav nav-tabs mb-0" id="tabsMasters">
            <?php foreach ($master as $i => $pod): ?>
                <li class="nav-item">
                    <a href="#"
                        class="nav-link <?= $i === 0 ? 'active' : '' ?>"
                        data-id="<?= $pod->id ?>">
                        <?= $pod->manifiesto ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <button type="button" class="btn btn-sm btn-primary" onclick="generar_pod();">Generar POD</button>

    </div>

    <div id="contenedorEncabezadoGT"></div>
    <div id="desglosedePOD"></div>
    <div id="contenedorDetalleGT"></div>

</div>
<div class="d-flex gap-2 mt-1">
    <button type="button" class="btn btn-outline-primary" onclick="agregar_paquetes_gt();">
        Agregar paquetes
    </button>

    <button type="button" class="btn btn-outline-danger" onclick="quitar_paquetes_gt();">
        Quitar paquetes
    </button>

    <button type="button" class="btn btn-outline-danger" onclick="quitar_paquetes_gt_todo();">
        Quitar todos los paquetes 
    </button>
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



<script>
    $(document).ready(function() {

        let id_pod_actual = null;

        $('#tabsMasters').on('click', '.nav-link', function(e) {
            e.preventDefault();

            $('#tabsMasters .nav-link').removeClass('active');
            $(this).addClass('active');

            let id_pod_actual = $(this).data('id');

            cargarEncabezadoPOD_gt(id_pod_actual);
        });

        let $tabActivo = $('#tabsMasters .nav-link.active');

        if ($tabActivo.length) {
            let id_pod_actual = $tabActivo.data('id');
            cargarEncabezadoPOD_gt(id_pod_actual);
        }
    });
</script>