<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <style>
        .tooltip-inner {
            background-color: #0F4C5C !important;
            color: #0F4C5C;
        }

        .bs-tooltip-top .arrow::before,
        .bs-tooltip-auto[x-placement^="top"] .arrow::before {
            border-top-color: #0F4C5C !important;
        }

        .bs-tooltip-right .arrow::before,
        .bs-tooltip-auto[x-placement^="right"] .arrow::before {
            border-right-color: #0F4C5C !important;
        }


        .bs-tooltip-bottom .arrow::before,
        .bs-tooltip-auto[x-placement^="bottom"] .arrow::before {
            border-bottom-color: #0F4C5C !important;
        }


        .bs-tooltip-left .arrow::before,
        .bs-tooltip-auto[x-placement^="left"] .arrow::before {
            border-left-color: #0F4C5C !important;
        }

        .modal-text-header {
            color: #F15A29;

        }
    </style>
</head>

<body>
<!--form >
<div class="form-group">
<label for="referencia">Referencia</label>
<input type="text" class="form-control" placeholder="Introduzca referencia" id="referencia" name="referencia">
</div>
<button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal_pdf()">Cerrar
</button>
<button class="btn btn-primary float-md-right ">Guardar</button>
</form-->
 <table class="table">
    <thead>
            <tr>
                    <th>Numero de tracking</td>
                    <th>IMPRIMIR</td>
            </tr>
    </thead>
    <tbody>
        <?php 
        //var_dump($lista);
        if (isset($lista)) {
            foreach ($lista as $row) { ?>
                <tr class="rw">
                    <td><?php echo $row->tracking_number; ?></td>
                    <td><a href="#" class="btn btn-default btn-md mt-0 text-blue" onclick="pdf_ticket(<?php echo "'$row->tracking_number'";?>)" title="Multiplicar" style="margin:0px;">
                <i class="fas fa-cut text-primary"></i></a></td>
                </tr>
        <?php
            }
        }
        ?>
    </tbody>
</table>

</body>
</html>