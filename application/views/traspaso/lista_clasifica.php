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
    <div class="container mx-auto" >
    <div class=" table responsive">
        <table class="table  table-striped  table-hover " id="tabla" name="tabla">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>ID</th>
                    <th>Warehouse </th>
                    <th>Manifiesto</th>
                    <th>Referencia</th>
                    <th>poliza</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $c=1;
                        session_start();
                        foreach ($guiamaster as $row) {
                            ?>
                <tr style="background-color:white">
                    <?php
                        if ($row->estado =="R") {
                            echo "<td style='background-color:red;color:white;'> Pendiente </td>";
                        }
                        if ($row->estado =="V") {
                                echo "<td style='background-color:#ef7f1b;color:white'> Recibido </td>";
                        } ?>
                    <td><?php echo $c; ?></td>
                    <td><?php echo $row->numero_warehouse; ?></td>
                    <td><?php echo $row->manifiesto; ?></td>
                    <td><?php echo $row->referencia; ?></td>
                    <td><?php echo $row->poliza; ?></td>
                </tr>

                <?php
                $c=$c+1;
                        }
                        ?>
            </tbody>
        </table>
        </div>
    </div>


</body>

</html>