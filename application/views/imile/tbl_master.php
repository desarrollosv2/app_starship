<div class="table-responsive">
    <table class="table custom-table" id="tbl_lista_master">
        <thead>
            <tr>
                <th scope="col"> # </th>
                <th scope="col">Manifiesto</th>
                <th scope="col">Fecha</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if(isset($master) && count($master) > 0)
                {
                    $c = 1;
                    foreach($master as $key => $value)
                    {
                        ?>
                        <tr>
                            <td><?= $c ?></td>
                            <td><?= $value->manifiesto ?></td>
                            <td><?= $value->fecha ?></td>
                            <td><?= $value->descripcion ?></td>
                            <td>
                                <a href="#" class="btn btn-default btn-sm" title="Verificar" onclick="mostrar_tracking_imile(<?= $value->idpreclasificacion ?>)" style="margin:0px;">
                                    <i class="fa fa-file" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    $c = $c + 1;
                    }
                }else
                {
                    ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No se encontraron registros en el rango de fechas seleccionado.</td>
                        </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
</div>