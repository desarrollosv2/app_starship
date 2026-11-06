<div class="table-responsive mt-4">
    <table class="table custom-table" id="tabla_pod">
        <thead>
            <tr>
                <th scope="col">Fecha entrega</th>
                <th scope="col">Cliente</th>
                <th scope="col">Master</th>
                <th scope="col">Paquetes</th>
                <th scope="col">Cajas</th>
                <th scope="col">Piloto</th>
                <th scope="col">Estatus</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if(isset($lista))
                {
                    foreach($lista as $row)
                    {
                        ?>
                            <tr>
                                <td><?= $row->fecha_entrega ?></td>
                                <td><?= $row->nombre_empresa ?></td>
                                <td><?= $row->manifiesto ?></td>
                                <td><?= $row->numero_paquetes ?></td>
                                <td><?= $row->numero_cajas ?></td>
                                <td><?= $row->piloto ?></td>
                                <td>
                                    <?php
                                        $badgeClass = 'badge-secondary';

                                        if ($row->estatus_pod === 'Generada') {
                                            $badgeClass = 'badge-primary';
                                        } elseif ($row->estatus_pod === 'Finalizada') {
                                            $badgeClass = 'badge-success';
                                        }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> text-white">
                                        <?= $row->estatus_pod ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-link p-0 text-success" onclick="ver_pod_gt(<?= $row->id ?>, '<?= $row->manifiesto ?>')" title="Ver POD">
                                        <i class="fa fa-eye"></i></button>
                                    <?php if($_SESSION['user_id'] == 11200 || $_SESSION['user_id'] == 41 || $_SESSION['user_id'] == 1 || $_SESSION['user_id'] == 2) : ?>
                                    <button type="button" class="btn btn-link p-0 text-primary" onclick="editar_pod_gt(<?= $row->id ?>, '<?= $row->manifiesto ?>')" title="Editar POD">
                                        <i class="fa fa-edit"></i></button>
                                    <?php endif; ?>

                                    <button type="button" class="btn btn-link p-0 text-danger" onclick="ver_pdf(<?= $row->id ?>)" title="Ver PDF">
                                        <i class="fa fa-file-pdf-o"></i></button>
                                    
                                    <button type="button" class="btn btn-link p-0 text-info" onclick="subir_comprobante_pod(<?= $row->id ?>, '<?= $row->manifiesto ?>')" title="Subir comprobante de entrega">
                                        <i class="fa fa-upload"></i></button>

                                    <?php if($_SESSION['user_id'] == 11200 || $_SESSION['user_id'] == 41 || $_SESSION['user_id'] == 1 || $_SESSION['user_id'] == 2) : ?>
                                    <button type="button" class="btn btn-link p-0 text-secondary" onclick="anular_pod(<?= $row->id ?>, <?= $row->idpreclasificacion ?>,'<?= $row->manifiesto ?>')" title="Anular POD">
                                        <i class="fa fa-ban"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php
                    }
                }
            ?>
        </tbody>
    </table>
</div>