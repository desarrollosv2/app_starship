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
    <link href='//fonts.googleapis.com/css?family=Montserrat:thin,extra-light,light,100,200,300,400,500,600,700,800'
        rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/estilos.css')?>">


    <style>
    body {
        font-family: 'Montserrat', Sans-serif;
    }

    h1 {

        font-family: 'Montserrat', Sans-serif;
        font-weight: bold;
    }

    .cerrar_div {
        width: 400px;
        height: 100px;
        background: lightblue;
        display: block
    }

    .boton {
        float: right;
        background: #ef7f1b;
        color: white;
        width: 30px;
        height: 25px;
        border-radius: 100%;
        border: 1px solid white;
    }

    .boton:hover {
        
        border: 1px solid #ef7f1b;
        color:yellow;
    }
    </style>
</head>


<div class="container">

    <div class="card text-center ">
        <div class="card-header">
            <h4>Verificar Paquetes</h4>
        </div>
        <div class="card-body">
        <div class="row  p-4 m-3 " id="master" name="master" style="background-color:#F15a29;">
                <div class="col-md-2">
                </div>
                <div class="col-md-4 " style="float:left;margin:5px">
                    <input type="text" id="guia_master" name="guia_master" placeholder="Buscar..."
                        class="form-control" />
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-secondary pdf" onclick="pdf_clasificado()">Generar PDF</button>
                </div>
                <div class="col-md-1">
                </div>
                <span class="boton text-center" onclick="cerrar_div()" style="display:none;cursor: pointer">X</span>
            </div>
            <br>
            <form enctype="multipart/form-data" class="qry_warehouse forminline" id="qry_warehouse">
                <div class="row">

                    <div class="col-md-2">
                        <p class="center_v"> Estatus </p>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <select name="estatus" id="estatus" class="form-control  form-control-chosen"
                                data-placeholder="Seleccione estatus" required>
                                <option value=""></option>
                                <?php foreach ($estatus as $row): ?>
                                <option value="<?php echo $row->id_estatus; ?>">
                                    <?php echo  $row->id_estatus.' - '.$row->nombre; ?>
                                </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row ">
                    <div class="col-md-2">
                        <p class="center_v"> Warehouse </p>
                    </div>
                    <div class="col-md-6">
                        <div class="box">
                            <div class="container-1">
                                <span class="icon"><i class="fa fa-search"></i></span>
                                <input type="search" id="search" name="search" placeholder="Buscar..."
                                    class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">

                        <button type="button" class="btn btn-primary " style="float:left;margin-left:0" 
                                          onclick="consulta_warehouse()">Buscar</button>


                        <!--button type="button" class="btn btn-primary " onclick="guia_master_Off()"
                            style="float:left;margin-left">Detalle
                            Guía</button-->
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-2">
                        <p class="center_v"> Referencia </p>
                    </div>
                    <div class="col-md-10">
                        <div class="box">
                            <div class="container-1">
                                <input type="text" id="referencia" name="referencia" class="form-control"
                                    style="font-weight: bold; font-size:180px; text-align:center" />
                            </div>
                        </div>
                    </div>

                </div>
                <input type="hidden" id="casillero" name="casillero" class="form-control" />
            </form>
            <br>
            <div id="lista_guia_master">

            </div>
        </div>


        <div class="card-footer text-muted">

        </div>
    </div>

</div>



<script>
$(document).ready(function() {

    $("#search").on("keyup", function() {

        var value = $(this).val().toLowerCase();

        $("#myTable tr").filter(function() {

            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)

        });

    });

});
</script>


<script>
$(document).ready(function() {

    $("#search").blur(function() {

        var warehouse = $("#search").val();
        var guiamaster = $("#guia_master").val();
        var st = $("#estatus").val();
        if ($("#estatus").val().length <= 0) {
            alertify.set("notifier", "position", "top-right");
            alertify.warning("!Es necesario ingresar estatus para poder continuar!");

        } else {
			
            update_warehouse(warehouse, st);
        }



    });

});
</script>

<script>
verifica();

</script>

</body>

</html>