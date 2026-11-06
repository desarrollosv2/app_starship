<?php
    //var_dump($encabezado);
?>

<?php if (!empty($encabezado)): ?>
<div class="card mt-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <b>Fecha entrega:</b><br>
                <?= $encabezado->fecha_entrega ?>
            </div>

            <div class="col-md-4">
                <b>Piloto:</b><br>
                <?= $encabezado->piloto ?>
            </div>

            <div class="col-md-4">
                <b>Placa:</b><br>
                <?= $encabezado->placa ?>
            </div>

            <div class="col-md-4 mt-2">
                <b>Marchamo:</b><br>
                <?= $encabezado->marchamo ?>
            </div>

            <div class="col-md-4 mt-2">
                <b>Proveedor:</b><br>
                <?= $encabezado->proveedor ?>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>
