<div class="table-responsive">
    <table class="table custom-table" id="tbl_lista">
        <thead>
            <tr>
                <th scope="col"> # </th>
                <th scope="col">Bag Number</th>
                <th scope="col">Tracking Number</th>
                <th scope="col">Recolecta</th>
                <th scope="col">Guía</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if(isset($lista))
                {
                    $c = 1;
                    foreach($lista as $key => $value)
                    {
                        ?>
                        <tr>
                            <td><?= $c ?></td>
                            <td><?= $value->bag_number ?></td>
                            <td><?= $value->tracking_number ?></td>
                            <td><?= $value->pickup_number ?></td>
                            <td><?= $value->internal_tracking ?></td>
                            
                        </tr>
                        <?php
                    $c = $c + 1;
                    }
                }
            ?>
        </tbody>
    </table>
    <br>
</div>