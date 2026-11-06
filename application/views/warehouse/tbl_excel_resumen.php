
<div class="table-responsive">
    <table class="table custom-table" id="tbl_excel_resumen">
        <thead>
                        <tr>
                            <th scope="col"> # </th>
                            <th scope="col">Tracking Number</th>
                            <th scope="col">Estatus</th>
                            <th scope="col">Fecha de estatus</th>
                            <th scope="col">Días Transcurridos</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if(isset($detalles))
                        {
                            $c = 1;
                            foreach($detalles as $row)
                            {
                               ?> 
                                    <tr>
                                        <td scope="row" style="margin-left: 9px"><?= $c ?></td>
                                        <td scope="row"><?= $row->tracking_number ?></td>
                                        <td scope="row"><?= $row->nombre ?></td>
                                        <td scope="row"><?= $row->fecha_formateada  ?></td>
                                        <td scope="row"><?= $row->dias_transcurridos  ?></td>
                                    </tr>
                                <?php
                                $c = $c + 1;
                            }
                        }
                    ?> 
        </tbody>
    </table>
</div>
