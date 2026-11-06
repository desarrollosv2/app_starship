<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatus</title>
    <style>
        .cuerpo {
            margin-top: 10px;
            border-radius: 20px;

        }

        .container-fluid {
            max-width: 700px !important;
        }
    </style>
</head>

<body>

    <div class="container-fluid cuerpo  alert-secondary">
       

        <div class="row">
            <div class="col text-center">
                <h3> <small class="text-muted">Cambiar estatus</small></h3>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <label class="sr-only" for="inlineFormInputName2">Name</label>
                <input type="text" class="form-control mb-2 mr-sm-2" id="guia" placeholder="Guia">
                <button type="button" class="btn btn-primary mb-2" onclick="aplicar_estatus(0)">Buscar</button>
               <!-- <button type="button" class="btn btn-primary mb-2" onclick="reload_guia()">Reload</button> -->

            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table custom-table" id="tbl_detalle">
                    <thead>

                        <tr>


                            <th scope="col">Estatus</td>
                            <th scope="col">Código</td>
                            <th scope="col">Fecha</td>
                            <th scope="col">Latitud</td>
                            <th scope="col">Longitud</td>



                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($lista)) {
                            // var_dump($lista);
                            $c = 1;
                            foreach ($lista as $row) { ?>
                                <tr class="rw ">
                                    <td><?php echo $row->estatus ?></td>
                                    <td><?php echo $row->codigo ?></td>
                                    <td><?php echo $row->fecha ?></td>
                                    <td><?php echo $row->latitud ?></td>
                                    <td><?php echo $row->longitud ?></td>
                                </tr>
                        <?php

                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>