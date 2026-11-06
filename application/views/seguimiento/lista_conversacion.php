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

        .st-title {
            background-color: #a1bf5b;
            background-color: #F4F6F9;
            padding: 10px;
            border-left: 8px solid #F15A29;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-10 text-center  text-uppercase mt-3">
                <h6 class="bg-white">
                    <p id="tracking-id"></p>

                </h6>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary btn-sm" title="Agregar comentario" OnClick="add_comentario();"> <i class="fas fa-plus"></i></button>
            </div>
        </div>
    </div>
    <div class="container text-center" id="form-datos" name="form-datos" style="display:none">
                <form enctype="multipart/form-data" class="add_comenta" id="add_comenta" action="javascript:guardar_comentario()">
                    <input type="hidden" id="id-tracking" name="id-tracking">
                    <div class="form-group">
                        <textarea class="form-control" rows="3" placeholder="Introduzca comentarios" id="comenta" name="comenta"></textarea>

                    </div>

                    <div class="row ">
                        <div class="col text-left">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input " id="chk_aceptado" checked>
                                <label class="custom-control-label" for="chk_aceptado">Paquete aceptado</label>
                            </div>
                        </div>
                        <div class="col text-right">
                            <button type="button" class="btn btn-secondary btn-sm float-sm-right" id="btn-cerrar-globo">Cancelar
                            </button>
                            <button class="btn btn-primary btn-sm float-sm-right ">Guardar</button>
                        </div>


                    </div>


                </form>

            </div>

    <?php if (isset($dconversacion)) {
        $i = 1;
        foreach ($dconversacion as $row) {?>

           
            <?php if ($i == 1) {
                echo '<hr class="bg-warning">';
                $i = $i + 1;
            } ?>

            <div class="container">
                <div class="row mb-3 bg-light ">
                    <div class="col-md-3 st-title text-left">
                        <p class="font-weight-bold"> <?php echo $row->fecha ?> </p>
                        <p> <?php echo $row->usuario ?> </p>

                    </div>
                    <div class="col-md-9 m-0 p-0 bg-light">
                        <p> <?php echo $row->comentarios ?> </p>
                    </div>

                </div>
            </div>



    <?php }
    }
    ?>

<script>
    $('#btn-cerrar-globo').click(function(){
        $("#chk_aceptado").prop('checked', false);
        $("#form-datos").hide();
   
});
</script>

</body>

</html>