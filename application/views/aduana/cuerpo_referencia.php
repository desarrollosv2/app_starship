<div class="table-responsive">


    <div class="row"></div>


    <?php
    $bultos = 0;
    $pesoKG = 0;
    $unidades = 0;
    $valor = 0;
    $flete = 0;
    $seguro = 0;
    $otros = 0;

    foreach ($lista as $row) {
        $bultos = $bultos + $row->Bultos;
        $pesoKG = $pesoKG + $row->PesoKg;
        $unidades = $unidades + $row->Unidades;
        $valor = $valor + $row->Valor;
        $flete = $flete + $row->Flete;
        $seguro = $seguro + $row->Seguro;
        $otros = $otros + $row->Otros;
    }
    ?>
    <table class="table custom-table" id="tbl_detalle">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Tracking Number</th>
                <th scope="col">Consignatario</th>
                <th scope="col">HSC</th>
                <th scope="col" class="text-center">Bultos<br> <strong> <?php echo number_format($bultos, 2) ?> </strong></th>
                <th scope="col" class="text-center">Peso <br> <strong> <?php echo number_format($pesoKG, 2) ?> </strong> </th>
                <th scope="col">Contenido</th>
                <th scope="col" class="text-center">Unidades <br> <strong> <?php echo number_format($unidades, 2) ?> </strong></th>
                <th scope="col" class="text-center">Valor <br> <strong> <?php echo number_format($valor, 2) ?> </strong></th>
                <th scope="col" class="text-center">Flete <br> <strong> <?php echo number_format($flete, 2) ?> </strong></th>
                <th scope="col" class="text-center">Seguro <br> <strong> <?php echo number_format($seguro, 2) ?> </strong></th>
                <th scope="col" class="text-center">Otros <br> <strong> <?php echo number_format($otros, 2) ?> </strong></th>
                <th scope="col">Serial</th>
                <th scope="col">Registro</th>
                <th scope="col" class="text-center">Fecha</th>
                <th scope="col" class="text-ceneter">Acciones</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
        </thead>

        <tbody id="awb_detalle">
            <?php
            if (isset($lista)) {
                $c = 1;
                foreach ($lista as $row) {
                    if ($c == 1) {
                        echo "<tr>";
                        echo "<td><dt><h2 class='text-primary'>" . $row->tipo . "</h2></dt></td>";

                        echo "</tr>";
                    }
            ?>
                    <tr class="rw ">
                        <td scope="row"><?php echo $c; ?></td>
                        <td scope="row"><?php echo $row->DocumentoTransporte; ?></td>
                        <td scope="row"><?php echo $row->Consignatario; ?></td>
                        <!-- <td scope="row"><?php //echo $row->HSC3 . $row->TAR_PR1 . $row->TAR_PR2; 
                                                ?></td> -->
                        <td scope="row"><?php echo $row->HSC3 . $row->TAR_PR1; ?></td>

                        <td scope="row"><?php echo number_format($row->Bultos, 2); ?></td>
                        <td scope="row"><?php echo number_format($row->PesoKg, 2); ?></td>

                        <td scope="row"><?php echo $row->ContenidoDescripcion;  ?></td>

                        <td scope="row"><?php echo number_format($row->Unidades, 2);  ?></td>

                        <td scope="row" style="width:100px"><?php echo number_format($row->Valor, 2); ?></td>

                        <td scope="row"><?php echo  number_format($row->Flete, 2); ?></td>
                        <td scope="row"><?php echo  number_format($row->Seguro, 2); ?></td>
                        <td scope="row"><?php echo  number_format($row->Otros, 2); ?></td>

                        <td scope="row" class="text-center text-primary font-weight-bold"><?php echo  $row->serial; ?></td>
                        <td scope="row" class="text-center text-primary font-weight-bold"><?php echo  $row->registrationNumber; ?></td>
                        <td scope="row" class="text-center text-primary font-weight-bold"><?php echo  $row->registrationDate; ?></td>

                        <td scope="row"><?php echo  ''; ?></td>


                        <td>

                            <div class="row">
                                <button class="col col-md-3 mx-1 btn btn-default d-flex align-items-center justify-content-center" onclick="show_infoTracking(<?php echo $row->idItem; ?>)" title="Editar Guia">
                                    <i class="fa fa-pencil text-primary" aria-hidden="true"></i>
                                </button>

                                <button class="col col-md-3 mx-1 btn btn-default d-flex align-items-center justify-content-center" onClick="open_modal_multiplicar(<?php echo "'$row->id'"; ?>)" title="Multiplicar">
                                    <i class="fa fa-cut text-default"></i>
                                </button>
                                <button class="col col-md-3 mx-1 btn btn-default d-flex align-items-center justify-content-center" onclick="eliminar_wh_fast(<?php echo $row->id; ?>)" title="Eliminar Guia">
                                    <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                                </button>


                            </div>
                        </td>
                    </tr>

            <?php
                    $c = $c + 1;
                }
            }
            ?>
        </tbody>
    </table>
</div>