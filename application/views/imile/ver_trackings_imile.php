<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <title></title>
</head>

<body>
    <?php //var_dump($manifiesto); 
    if(isset($manifiesto))
    {
        $idpre = $manifiesto->idpreclasificacion;
        $master = $manifiesto->manifiesto;
    }
    else
    {
        $idpre  = '';
        $master = '';
    }

    ?>

    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap">

            <div class="d-flex align-items-center flex-wrap">
                <input type="hidden" name="manifiesto_id" id="manifiesto_id" value="<?= $idpre ?>">
                <h2 class="mr-3 mb-0"><?= $master ?></h2>

            </div>
                <div class="form-group mt-3 ml-auto mr-5">
                    <a class="btn btn-info text-white mr-2" title="Ver gráfica" onclick="grafica_imile(<?= $idpre ?>)">
                        <i class="fas fa-chart-bar"></i>
                    </a>

                    <a  class="btn btn-secondary text-white" title="Regresar" onclick="regresar_imile()">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>

        </div>
        <hr class="borde-orange mt-3">

        <div class="container-fluid m-top" id="tbl_trackings_imile_respuesta"></div>

        <!-- Modal -->
        <div class="modal fade" id="modal_grafica" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header bg-white text-dark">
                    <h5 class="modal-title" id="exampleModalLabel"><?= $master ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="grafica_imile">
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
                </div>
            </div>
        </div>


    </div>
    <!-- Carga Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/js/imile.js') ?>"></script>
</body>

</html>