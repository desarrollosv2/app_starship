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
    body 
    {
        font-family: 'Montserrat', Sans-serif;
    }

    h1 
    {
        font-family: 'Montserrat', Sans-serif;
        font-weight: bold;
    }

    .opciones 
    {
        color: #F15a29;    
    }

    .color-text-header
    {
       color: #D96D6D;
    }
    </style>
</head>


<div class="container">
    <div class="card text-center ">
        <div class="card-header bg-white">
            <h4 class="color-text-header font-weight-bold">CONSULTA  </h4>
        </div>
        
        <div class="card-body">
            <form enctype="multipart/form-data" class="qry_warehouse forminline" id="qry_tracking">
                <div class="row">
                    <div class="col-md-2">
                        <h4 class="center_v font-weight-bold" style="color:#787B7E"> Tracking: </h4>
                    </div>

                    <div class="col-md-6">
                        <div class="box">
                            <div class="container-1">
                                <span class="icon"><i class="fa fa-search"></i></span>
                                <input type="search" id="search" name="search" placeholder="Buscar..." class="form-control" style="font-size:20px ;color:#787B7E"" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">

                        <button type="button" class="btn btn-warning btn-lg mt-0" style="float:left;margin-left:0;background:#F15A29;color:aliceblue" onclick= "consulta_tracking()">
                            <i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
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
$(document).ready(function() 
{
    $("#search").on("keyup", function() 
    {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() 
        {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>



</body>

</html>