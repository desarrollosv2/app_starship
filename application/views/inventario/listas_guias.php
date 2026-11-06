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

 <?php if (isset($guias)) { ?>

    <div class="form-group">
        <form enctype="multipart/form-data" class="vista_guias">
        </form>


        <div class="table-responsive">

            <table class="table  table-striped  table-hover">

                <thead>
                    <th class=" col-xs-6">#</th>
                    <th class=" col-xs-6">Guia Master</th>
                    <th class=" col-xs-6">Warehouse</th>
                    </tr>

                </thead>

                <tbody id="myTable1">
                    <?php
                    $con=1;
                    foreach ($guias as $row): ?>
                    <tr>
                        <td>
                            <?php echo $con;?>
                        </td>
                        <td>
                            <?php echo $row->manifiesto; ?>
                        </td>
                        <td>
                            <?php echo $row->numero_warehouse; ?>
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