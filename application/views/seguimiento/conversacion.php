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

 <?php if (isset($dconversacion)) { ?>

    <div class="form-group">
        <form enctype="multipart/form-data" class="vista_guias">
        </form>


        <div class="table-responsive">

            <table class="table  table-striped  table-hover">

                <thead>
                   

                    <th class=" col-xs-6">Guia Master</th>

                    <th class=" col-xs-6">Referencia</th>

                    <th class=" col-xs-6">Paliza</th>

                    <th class=" col-xs-6">Warehouse</th>

                    <th colspan="2" class="acciones">Acciones</th>

                    </tr>

                </thead>

                <tbody id="myTable1">


                    <?php
                    $con=1;
                    foreach ($dconversacion as $row): ?>

                    <tr>
                        
                        <td>
                            <?php echo $row->id; ?>
                        </td>

                        <td>
                            <?php echo $row->fecha; ?>
                        </td>

                        <td>
                            <?php echo $row->comentarios; ?>
                        </td>


                        <td>
                            <?php echo $row->usuario; ?>
                        </td>
                        <td>
                            <a href="#" class=" btn-danger btn-sm" title="Eliminar Guia" id="btndelete"
                                onclick="confirmar_eliminar_guia(<?php echo $row->idwarehouse; ?>, <?php echo $row->referencia; ?>);return false;">
                                <i class="fa fa-trash"></i></a>
                        </td>

                    </tr>

                    <?php  $con=$con+1; endforeach ?>

                </tbody>

            </table>
        </div>
    
    </div>

 <?php } ?>




</body>

</html>