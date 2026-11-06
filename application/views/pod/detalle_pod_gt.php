<?php
//var_dump($detalle);

?>
<style>

.table-sm td {
    vertical-align: middle;
    padding-top: 3px !important;
    padding-bottom: 3px !important;
}


</style>

<?php if (!empty($detalle)): ?>
<div class="card mt-3">
    <div class="card-header">
        <b>Detalle de paquetes</b>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive mt-2">
            <table class="table table-sm table-striped table-hover mt-5" id="tbl_detalle_pod">
                <thead class="thead-light">
                    <tr>
                        <th>Referencia</th>
                        <th class="text-center">Paquetes agregados</th>
                        <th class="text-center">Paquetes excluidos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($detalle as $d): ?>


                    <tr>
                        <td style="vertical-align: middle;"><?= $d->referencia ?></td>
                        <td class="text-center" style="vertical-align: middle;"><span class="badge badge-success"><?= $d->paquetes_agregados ?></span></td>
                        <td class="text-center" style="vertical-align: middle;"><span class="badge badge-danger"><?= $d->paquetes_excluidos ?></span></td>
                        <td>
                            <div class="d-flex justify-content-end flex-nowrap">
                                <?php if($d->paquetes_agregados > 0) : ?>
                                    <button class="btn btn-sm text-white btn-primary mr-1"
                                        title="Ver agregados"
                                        onclick="verPaquetesGT(<?= $d->id_manifiesto ?>, '<?= $d->referencia ?>', 1, <?= $id_pod ?>)">
                                        <i class="fa fa-eye"></i> Ver agregados
                                    </button>
                                <?php endif ?>
                                <?php if($d->paquetes_excluidos > 0) : ?>
                                    <button class="btn btn-sm text-white btn-danger"
                                        title="Ver excluidos"
                                        onclick="verPaquetesGT(<?= $d->id_manifiesto ?>, '<?= $d->referencia ?>', 0, <?= $id_pod ?>)">
                                        <i class="fa fa-eye"></i> Ver excluidos
                                    </button>
                                <?php endif ?>
                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning mt-3">
    No hay detalle para este POD.
</div>
<?php endif; ?>


<div class="modal fade" id="modal_paquetes_estado_gt" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span id="title_paquetes_estado_gt"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="body_paquetes_estado_gt_pod">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>