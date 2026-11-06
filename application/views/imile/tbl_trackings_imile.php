<div class="table-responsive">
    <table class="table custom-table" id="tbl_trackings_imile">
        <thead>
                        <tr>
                            <th scope="col"> # </th>
                            <th scope="col">Tracking Number</th>
                            <th scope="col">Scan Global</th>
                            <th scope="col">Entregado</th>
                            <th scope="col">Reingreso</th>
                            <th scope="col">Devolución</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if(isset($trackings))
                        {
                            $c = 1;
                            foreach($trackings as $row)
                            {
                               ?> 
                                    <tr>
                                        <td scope="row" style="margin-left: 9px"><?= $c ?></td>
                                        <td scope="row"><?= $row->tracking_number ?></td>
                                        <td scope="row"><?= ($row->scan_global == 1) ? 'Sí' : 'No' ?></td>
                                        <td scope="row"><?= ($row->entregado == 1) ? 'Sí' : 'No' ?></td>
                                        <td scope="row"><?= ($row->reingreso == 1) ? 'Sí' : 'No' ?></td>
                                        <td scope="row"><?= ($row->devolucion == 1) ? 'Sí' : 'No' ?></td>
                                    </tr>
                                <?php
                                $c = $c + 1;
                            }
                        }
                    ?> 
        </tbody>
    </table>
</div>