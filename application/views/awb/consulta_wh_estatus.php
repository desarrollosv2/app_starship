<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width; initial-scale=1.0">
   
    <style>
    body {
        font-family: 'Montserrat', Sans-serif;
    }

    h1 {

        font-family: 'Montserrat', Sans-serif;
        font-weight: bold;
    }

    table {
        margin: 0 auto
    }

    </style>

</head>

<body>
    <div class="container mx-auto ">
        <div class="container">


            <div class="table-responsive">

                <table class="table  table-striped table-hover ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Código</th>
                            <th>Descrpción</th>
                        </tr>
                    </thead>
                    <tbody>
                   
                        <div class="alert alert-primary bg-orange text-white" role="alert">
                            <h4><?php   echo   $cliente[0]['nombre']; ?></h4>
                        </div>
                        <?php
                        $con=1;
                        foreach ($whouse->shipment->event as $fila) { ?>

                        <tr style="background-color:white">
                            <td><?php echo $con; ?></th>
                            <td><?php echo $fila->date; ?></th>
                            <td><?php echo $fila->code; ?></th>
                            <td><?php echo $fila->description; ?></th>
                        </tr>

                        <?php $con=$con+1;} ?>
                        <?php
                        foreach ($wareh as $row) { ?>

                        <tr style="background-color:white">
                            <td><?php echo $con; ?></th>
                            <td><?php echo $row->fecha; ?></th>
                            <td><?php echo $row->id_estatus; ?></th>
                            <td><?php echo $row->nombre; ?></th>
                        </tr>

                         <?php $con=$con+1;} ?>

                    </tbody>
                </table>



            </div>
        </div>
    </div>
</body>

</html>