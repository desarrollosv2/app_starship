<?php 
$estado = isset($detalles[0]) ? ($detalles[0]->nombre ?? 'Sin movimiento') : 'Sin movimiento'; 

?>
<div class="d-flex justify-content-between align-items-center mb-1">
    <strong class="h5 m-0">Estatus: <?= $estado ?></strong>
    <button id="btn_export_excel" class="btn btn-success btn-sm">
        <i class="fas fa-file-excel"></i>
    </button>
</div>
<div class="table-responsive">
    <table class="table custom-table" id="tbl_detalle_estatus">
        <thead>
                        <tr>
                            <th scope="col"> # </th>
                            <th scope="col">Tracking Number</th>
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