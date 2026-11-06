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

    <title>Cambiar Estatus</title>
    <style>
    body {
        font-family: 'Montserrat', Sans-serif;
    }
    </style>
</head>
<div class="container">

    <div class="card text-center ">
        <div class="card-header">
            <h4>Cambiar Estatus</h4>

        </div>
       
        <div class="card-body">
            <div class="row">
            <div class="col-md-6"></div>
                <div class="alert alert-primary alert-dismissible col-md-6" role="alert" name="msg" id="msg"  style="display: none">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>
                        <p id="msg_alert" name="msg_alert"></p>
                    </strong>

                </div>
                
            </div>
            <form enctype="multipart/form-data" class="cambiarestatus forminline" id="cambiarestatus">
                <div class="row">

                    <div class="col-md-2">
                        <p class="center_v"> Estatus </p>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <select name="estatus" id="estatus" class="form-control "
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
                <div class="form-group">
                <div class="row" id="div_n" style="display:none;">
                    <div class="col-md-2">
                        <p class="center_v"> Nombre</p>
                    </div>
                    <div class="col-md-5">
                        <div class="box">
                            <div class="container-1">
                                <span class="icon"><i class="fa fa-user"></i></span>
                                <input type="search" id="n_retiro" name="n_retiro" placeholder="Nombre que retira paquete"
                                    class="form-control" />
                            </div>
                        </div>
                    </div>    
                </div>
                </div>
                <div class="row" class="form-group">
                    <div class="col-md-2">
                        <p class="center_v"> Warehouse </p>
                    </div>
                    <div class="col-md-5">
                        <div class="box">
                            <div class="container-1">
                                <span class="icon"><i class="fa fa-search"></i></span>
                                <input type="search" id="search" name="search" placeholder="Buscar..."
                                    class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <button type="button" class="btn btn-primary " style="float:left;margin-left"
                            onclick="verificar_warehouse()">Aplicar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
   */ $("#search").blur(function() {
        var warehouse = $("#search").val();
        var guiamaster = $("#guia_master").val();
        var st = $("#estatus").val();
        if ($("#estatus").val().length <= 0) {
            alertify.set("notifier", "position", "top-right");
           
        } else {
             update_warehouse(warehouse, guiamaster, st);
        }
    });
*/

    /*$("select").change(function () {
        var str = "";
        $( "select option:selected" ).each(function() {
          str += $( this ).text() + " ";
        });
        $("div").text( str );
      }).change();*/

      $("#estatus").change(function () {
         var estado=  $(this).val();
         if(estado=='59'){  
            $('#div_n').css('display','flex');
         }else{
            $('#n_retiro').val("");
            $('#div_n').css('display','none');
         }
      });

});
</script>

</body>

</html>