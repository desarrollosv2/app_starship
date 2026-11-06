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

    <p id="tracking-id"></p>

    <?php if (isset($wareh)) {
        $i = 1;
        foreach ($wareh as $row) { ?>


            <?php if ($i == 1) {
                echo '<hr class="bg-warning">';
                $i = $i + 1;
            } ?>

            <div class="container">
                <div class="row mb-3 bg-light ">
                    <div class="col-md-3 st-title text-center">
                        <p class="font-weight-bold" style="color:#787B7E"> <?php echo date('d-m-Y',strtotime( $row->fecha ))?> </p>
                      <!--  <p> <?php echo $row->usuario ?> </p> -->
                        <p class="font-weight-bold" style="color:#787B7E"> <?php echo date(' H:i',strtotime( $row->fecha )) ?> </p>
                        
                        

                    </div>
                    <div class="col-md-3 text-left">
                        <p class="font-weight-bold " style="color:#787B7E"> <?php echo $row->nombre ?> </p>


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
        $('#btn-cerrar-globo').click(function() {
            $("#chk_aceptado").prop('checked', false);
            $("#form-datos").hide();

        });
    </script>

</body>

</html>