<style>
    .tracking-item {
        cursor: pointer;
        transition: 0.2s;
    }

    .tracking-item:hover {
        background-color: #f1f1f1;
    }
</style>


<div class="container-fluid">

    <!-- BUSCADOR -->
    <div class="row mb-2">
        <div class="col-12">
            <input type="text"
                   id="buscar_tracking_gt_pod"
                   class="form-control form-control-sm"
                   placeholder="Buscar paquete...">
        </div>
    </div>

    <!-- LISTA -->
    <div class="row">
        <div class="col-12">

            <div id="contenedor_tracking" class="list-group" style="max-height: 350px; overflow-y: auto;">

                <?php foreach (explode(',', $detalle) as $t): ?>
                    <div class="list-group-item tracking-item py-1">
                        <?= trim($t) ?>
                    </div>
                <?php endforeach; ?>

            </div>

        </div>
    </div>

</div>

<script>
    $(document).ready(function () {

        $("#buscar_tracking_gt_pod").on("keyup", function () {

            let value = $(this).val().toLowerCase();

            $(".tracking-item").filter(function () {
                $(this).toggle(
                    $(this).text().toLowerCase().indexOf(value) > -1
                );
            });

        });

    });
</script>