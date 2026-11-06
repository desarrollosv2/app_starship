<?php if (!empty($desglose)): ?>
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <?php if ($desglose->id_pais == 1): ?>
                    <div class="col-md-4">
                        <b>Máster:</b><br>
                        <?= $desglose->manifiesto ?>
                    </div>

                    <div class="col-md-4">
                        <b>Cantidad de paquetes:</b><br>
                        <?= $desglose->numero_paquetes ?>
                    </div>

                    <div class="col-md-4">
                        <b>Cantidad de cajas:</b><br>
                        <?= $desglose->numero_cajas ?>
                    </div>
                <?php else: ?>
                    <div class="col-md-3">
                        <b>Máster:</b><br>
                        <?= $desglose->manifiesto ?>
                    </div>
                    <div class="col-md-3">
                        <b>Referencias:</b><br>
                        <?= $desglose->total_referencias ?>
                    </div>

                    <div class="col-md-3">
                        <b>Cantidad de paquetes:</b><br>
                        <?= $desglose->numero_paquetes ?>
                    </div>

                    <div class="col-md-3">
                        <b>Cantidad de cajas:</b><br>
                        <?= $desglose->numero_cajas ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>