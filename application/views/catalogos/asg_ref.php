<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/btnstyles.css') ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <title></title>


    <style>
        .group {
            width: 750px;
            height: 850px;
            top: 0%;
            margin-left: 15%;
        }

        .boton {
            top: 75%;
            margin-left: 35%;
        }

        .container {
            margin-top: 50px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #F4F6F9;
            margin-bottom: 10px;
        }

        .form-control,
        .btn {
            border-radius: 50px !important;
        }
    </style>


</head>

<body>


    <div class="container">
        <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
                <form enctype="multipart/form-data" class="add_referencia_md" id="add_referencia_md" action="javascript:update_ref_bolsa()">
                    <div class="form-group">
                       <input type="hidden" id="id-manifiesto" name="id-manifiesto" value="<?php echo $manifiesto[0]->id_manifiesto; ?>">
                        <h2 class="text-muted">
                            <?php
                            
                            if (isset($manifiesto)) {
                                foreach ($manifiesto as $row) {
                                    echo $row->manifiesto;
                                }
                            }

                            ?>
                        </h2>

                      
                    </div>

                    <div class="form-group">
                        <label for="dui">Sacos</label>
                        <?php ?>
                        <select name="bolsas" id="bolsas" class="form-control" onchange="listadoxsaco()">
                            <?php 
                                                      
                            foreach ($refxbolsa as $row) : ?>
                                <option value="<?php echo $row->bag_number; ?>">
                                    <?php echo $row->bag_number; ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dui">Referencia</label>
                        <input type="text" class="form-control" placeholder=" " id="referencia" name="referencia">
                    </div>

                    <div class="form-group">
                        <div id="cantidad_guias"></div>
                    </div>

                    <div class="form-group">
                        <div id="cantidad_guias"></div>
                    </div>

                    <div class="modal-footer text-center">
                        <button type="button" class="btn btn-secondary" onclick="lista_awb()"> Cerrar</button>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            $("#bolsas").select2({
                theme: 'bootstrap4',
                placeholder: "Seleccione un saco",
                allowClear: true,
                width: 'resolve',
            });
        });
    </script>
</body>

</html>