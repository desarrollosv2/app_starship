<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <title></title>
    <style>
        .table .th {
            color: #fff;
            background-color: #f00;
        }
    </style>
</head>

<body>

    <?php if (isset($l_estatus)) { ?>

        <div class="form-group">
            <form enctype="multipart/form-data" class="vista_guias">
            </form>


            <div class="table-responsive">

                <table class="table  table-striped  table-hover">

                    <thead>
                        <th class=" col-xs-6">#</th>

                        <th class=" col-xs-6">Fecha</th>

                        <th class=" col-xs-6">Código</th>

                        <th class=" col-xs-6">Estatus</th>

                        </tr>

                    </thead>

                    <tbody id="listado_estatus">


                        <?php
                        $con = 1;
                        foreach ($whouse->shipment->event as $fila) :  ?>

                            <tr>
                                <td>
                                    <?php echo $con; ?>
                                </td>
                                <td>
                                      <?php echo date("d/m/Y H:i:s", strtotime($fila->date)); ?>
                                </td>

                                <td>
                                    <?php echo  $fila->code; ?>
                                </td>

                                <td>
                                    <?php echo  $fila->description; ?>
                                </td>


                            </tr>

                        <?php $con = $con + 1;
                        endforeach ?>
                        <?php
                        foreach ($l_estatus as $row) :  ?>

                            <tr>
                                <td>
                                    <?php echo $con; ?>
                                </td>
                                <td>
                                    <?php echo date("d/m/Y H:i:s", strtotime($row->fecha)); ?>
                                </td>

                                <td>
                                    <?php echo  $row->id_estatus  ?>
                                </td>

                                <td>
                                    <?php echo $row->nombre_estatus; ?>
                                </td>

                            </tr>

                        <?php $con = $con + 1;
                        endforeach ?>

                    </tbody>

                </table>
            </div>

        </div>

    <?php } ?>




</body>

</html>