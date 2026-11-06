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
            <b>Detalle de paquetes por caja</b>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive mt-2">
                <table class="table table-sm table-striped table-hover mt-5" id="tbl_detalle_pod">
                    <thead class="thead-light">
                        <tr>
                            <?php if ($_SESSION['pais'] == 3): ?>
                                <th>Referencia</th>
                            <?php endif; ?>
                            <th>Bag Number</th>
                            <th class="text-center">Paquetes</th>
                            <th class="text-center">Estado</th>
                            <th style="width:120px">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle as $d): ?>
                            <?php
                            switch ($d->estado) {
                                case '1':
                                    $badge = 'badge-success';
                                    break;
                                case '2':
                                    $badge = 'badge-danger';
                                    break;
                                case '3':
                                    $badge = 'badge-primary';
                                    break;
                                default:
                                    $badge = 'badge-secondary';
                                    break;
                            }

                            ?>

                            <tr>
                                <?php if ($_SESSION['pais'] == 3): ?>
                                    <td style="vertical-align: middle;">
                                        <?= $d->referencia ?>
                                    </td>
                                <?php endif; ?>
                                <td style="vertical-align: middle;"><?= $d->bag_number ?></td>
                                <td class="text-center" style="vertical-align: middle;"><?= $d->numero_paquetes ?></td>
                                <td class="text-center" style="vertical-align: middle;"><span class="badge <?= $badge ?> p-2"><?= $d->estatus ?></span></td>
                                <td>
                                    <?php if ($d->estado == 1): ?>
                                        <button class="btn btn-xs text-white btn-danger pod_excluir" title="Excluir" style="height: 25px; width: 25px; padding: 0px; vertical-align: middle;" onclick="excluirCaja(<?= $d->id ?>, '<?= $d->bag_number ?>', <?= $d->id_pod ?>, <?= $d->idpreclasificacion ?>)">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    <?php elseif ($d->estado == 2) : ?>
                                        <button class="btn btn-xs text-white btn-success pod_incluir" title="Incluir" style="height: 25px; width: 25px; padding: 0px; vertical-align: middle;" onclick="incluirCaja(<?= $d->id ?>, <?= $d->id_pod ?>, <?= $d->idpreclasificacion ?>)">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    <?php elseif ($d->estado == 3) : ?>
                                        <button class="btn btn-xs text-white btn-primary" title="Ver" style="height: 25px; width: 25px; padding: 0px; vertical-align: middle;" onclick="verCaja(<?= $d->id ?>, '<?= $d->bag_number ?>')">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    <?php else : ?>

                                    <?php endif; ?>
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


<div class="modal fade" id="modal_observacion_pod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span id="bag_number_obs"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="observacion_pod">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardar_exclusion_caja()">Excluir</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_reproceso_pod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span id="bag_number_repro"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="reproceso_pod">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>