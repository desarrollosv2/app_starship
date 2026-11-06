<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

    <style>
        body {
            font-family: 'Montserrat', Sans-serif;
        }

        h1 {
            font-family: 'Montserrat', Sans-serif;
            font-weight: bold;
        }

        .opciones {
            color: #F15a29;
        }

        .color-text-header {
            color: #D96D6D;
        }
    </style>
</head>


<div class="container">
    <div class="card text-center ">
        <div class="card-header bg-white">
            <h4 class="color-text-header font-weight-bold">ENVIO TRACKING</h4>
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-md-2">
                    <h4 class="center_v font-weight-bold" style="color:#787B7E"> Sucursal: </h4>
                </div>

                <div class="col-md-6">
                    <div class="box">
                        <div class="container-1">
                            <select name="sucursal" id="sucursal" class="form-control-lg" data-placeholder="Seleccione la sucursal" required>
                                <option value=""></option>
                                <?php foreach ($sucursal as $row) : ?>
                                    <option value="<?php echo $row->id; ?>">
                                        <?php echo $row->nombre_agencia; ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <br>
            
            <div class="row">
                <div class="col-md-2">
                    <h4 class="center_v font-weight-bold" style="color:#787B7E"> Tipo: </h4>
                </div>

                <div class="col-md-6">
                    <div class="box">
                        <div class="container-1">
                            <select name="tipo_estatus" id="tipo_estatus" class="form-control-lg" data-placeholder="Seleccione la sucursal" required>
                                <option value="1">Notificacion</option>
                                <option value="2">Abandono</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-md-2">
                    <h4 class="center_v font-weight-bold" style="color:#787B7E"> Tracking: </h4>
                </div>

                <div class="col-md-6">
                    <div class="box">
                        <div class="container-1">
                            <input class="form-control form-control-lg" type="text" id="tracking_send" name="tracking_send">
                            <button type="submit" class="btn btn-primary" onclick="enviar_tracking();" data-dismiss="modal" name="buscar_tracking" id="buscar_tracking" style="float:left;margin-left:0;background:#F15A29;color:aliceblue">
                                <i class="fa fa-envelope" aria-hidden="true"></i> Enviar
                            </button>
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="box">

                    </div>
                </div>
            </div>

            <br>
            <div id="tabla_estatus"></div>
        </div>


        <div class="card-footer text-muted">

        </div>
    </div>

</div>

<script>
    $(document).ready(function() {
        $("#sucursal").select2({
            theme: 'bootstrap4',
            placeholder: "Seleccione la sucursal",
            allowClear: true,
            width: 'resolve',
        });

        $("#tipo_estatus").select2({
            theme: 'bootstrap4',
            placeholder: "Seleccione la sucursal",
            allowClear: true,
            width: 'resolve',
        });

    });
    var input = document.getElementById("tracking_send");

    input.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            document.getElementById("buscar_tracking").click();
        }
    });



    $(document).ready(function() {
        $("#search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>



</body>

</html>