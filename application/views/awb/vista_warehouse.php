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

    <!-- https://www.quackit.com/css/css_color_codes.cfm -->
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
    </style>
</head>


<div class="container">

    <div class="card text-center ">
        <div class="card-header">
            <h4>Seguimiento a Paquetes</h4>
        </div>
        <div class="card-body">
            <form enctype="multipart/form-data" class="qry_warehouse forminline" id="qry_warehouse">
               
                <br>
                <div class="row">
                    <div class="col-md-2">
                        <h4 class="center_v"> Warehouse </h4>
                    </div>
                    <div class="col-md-6">
                        <div class="box">
                            <div class="container-1">
                                <span class="icon"><i class="fa fa-search"></i></span>
                                <input type="search" id="search" name="search" placeholder="Buscar..."
                                    class="form-control" style="font-size:30px" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">

                        <button type="button" class="btn btn-warning btnbuscar" 
                            onclick="consulta_warehouse()">Buscar</button>

                    </div>

                </div>
            </form>
            <br>
            <div id="tabla_estatus">

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



</body>

</html>