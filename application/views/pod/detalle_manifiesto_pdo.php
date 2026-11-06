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

    <div id="contenedorEncabezado"></div>
    <div id="desglosedePOD"></div>
    <div id="contenedorDetalle"></div>

</div>





<script>
    $(document).ready(function () {

        $('#tabsMasters').on('click', '.nav-link', function (e) {
            e.preventDefault();

            $('#tabsMasters .nav-link').removeClass('active');
            $(this).addClass('active');

            let id_pod = $(this).data('id');

            cargarEncabezadoPOD(id_pod);
        });

        let $tabActivo = $('#tabsMasters .nav-link.active');

        if ($tabActivo.length) {
            let id_pod = $tabActivo.data('id');
            cargarEncabezadoPOD(id_pod);
        }
    });


</script>