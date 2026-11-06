<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Costos</title>
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

                <form enctype="multipart/form-data" class="add_referencia_md" id="add_referencia_md" action="javascript:guardar_costos()">
                    <div class="form-group">
                        <h2 class="text-muted"> <?php echo $manifiesto[0]->manifiesto; ?></h2>
                    </div>
                    <div class="form-group">
                        <?php foreach ($manifiesto as $row) : ?>
                            <input type="hidden" class="form-control" placeholder=" " id="id_manifiesto" name="id_manifiesto" value="<?php echo $row->id_manifiesto; ?>">
                        <?php endforeach ?>
                    </div>

                    <div class="form-group">

                        <select name="referencia" id="referencia" class="form-control" onchange="guiasxref();valor_guiasxref_general();">
                            <?php foreach ($referencia as $row) : ?>
                                <option> </option>
                                <option value="<?php echo $row->referencia; ?>">
                                    <?php echo $row->referencia; ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-group">

                        <div class="input-group-prepend">
                            <label for="fullref" class="d-flex align-items-center">
                                <input id="fullref" class="form-control mr-2" type="checkbox" onchange="guiareffull();">
                                Todo
                            </label>
                        </div>

                    </div>

                    <div class="form-group">
                        <div id="referenciasxguias"></div>
                    </div>

                    <div class="form-group">
                        <div id="valor_guiasxref"></div>
                    </div>
                    <div class="form-group">
                        <div id="valor_guiasxref"></div>
                    </div>

                    <div class="form-group">
                        <div id="valor_guiasxref_general"></div>
                    </div>

                    <div class="modal-footer text-center">
                        <button type="button" class="btn btn-secondary" onclick="lista_awb()"> Cerrar </button>

                        <button type="submit" class="btn btn-primary"> Guardar </button>
                    </div>
                </form>
            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("referencia").addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); // Evitar el comportamiento predeterminado del Enter (enviar formulario)
                    document.getElementById("add_referencia_md").submit(); // Enviar el formulario
                }
            });
        });
        $(document).ready(function() {
            $("#referencia").select2({
                theme: 'bootstrap4',
                placeholder: "Seleccione una Referencia ",
                allowClear: true,
                width: 'resolve',
            });
        });
    </script>
</body>

</html>