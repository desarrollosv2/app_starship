<?php
//var_dump($detalle);
//die();

?>


    

    <div class="container-fluid mt-4">

    <?php if (!empty($detalle)) : ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tbl_detalle_impuestos_temu">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Master</th>
                        <th>CH - SV</th>
                        <th>Referencia</th>
                        <th>Tracking</th>
                        <th>Package Number</th>
                        <th>Declarado</th>
                        <th>Pagado</th>

                    </tr>
                </thead>
                <tbody>
                <?php 
                    $i = 1;
                    foreach ($detalle as $index => $d)
                    {
                        ?>

                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $d->manifiesto ?></td>
                            <td><?= $d->manifiesto_tempo ?></td>
                            <td><?= $d->referencia ?></td>
                            <td><?= $d->tracking_number ?></td>
                            <td><?= $d->package_number ?></td>
                            <td><?= $d->impuestos ?></td>
                            <td><?= $d->pago_imp_temu ?></td>
                        </tr>

                        <?php 

                        $i++;
                    } 
                    
                    ?>
                </tbody>
            </table>
        </div>

    <?php  endif; ?>
    </div>


