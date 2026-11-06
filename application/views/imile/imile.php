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
    <div class="container mt-4">
        <!-- BUSCADOR + VER GRÁFICA -->

        <div class="d-flex align-items-center flex-wrap">
            <h2 class="mr-3 mx-2">Buscar</h2>

            <?php echo form_open('', ['id' => 'frm_buscar_master_fecha', 'class' => 'form-inline']); ?>
            <div class="form-group mt-3">
                <input type="date" id="fecha_inicio_imile" name="fecha_inicio_imile" class="form-control" value="<?php echo $_SESSION['fi']?>"  required>
            </div>
            <div class="form-group mx-2 mt-3">
                <input type="date" id="fecha_fin_imile" name="fecha_fin_imile" class="form-control"  value="<?php echo $_SESSION['ff']?>"  required>
            </div>
            <div class="form-group   mt-4 ">
                <button type="submit" name="accion" value="buscar" class="btn btn-primary" title="Buscar manifiestos">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="form-group mx-2 mt-4 ">
                <button type="submit" name="accion" value="grafica" class="btn btn-info text-white mr-1" title="Generar gráfica">
                    <i class="fas fa-chart-bar"></i>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>

        <!-- Botón Ver gráfica -->


    </div>

    <div class="container mt-4">
        <!-- BOTONES DE SUBIDA -->
        <div class="border p-3 rounded">
            <h5 class="mb-1">Subir documentos</h5>

            <?php echo form_open('', ['id' => 'form_excel_upload', 'enctype' => 'multipart/form-data', 'class' => 'd-flex flex-wrap align-items-center']); ?>
            <label class="btn btn-secondary mx-1 mb-2">
                Scan Global
                <input type="file" name="archivo" id="archivo_scan_global" data-tipo="scan_global" accept=".xls,.xlsx" style="display: none;">
            </label>
            <label class="btn btn-secondary mx-1 mb-2">
                Entregado
                <input type="file" name="archivo" id="archivo_entregado" data-tipo="entregado" accept=".xls,.xlsx" style="display: none;">
            </label>
            <label class="btn btn-secondary mx-1 mb-2">
                Reingreso
                <input type="file" name="archivo" id="archivo_reingreso" data-tipo="reingreso" accept=".xls,.xlsx" style="display: none;">
            </label>
            <label class="btn btn-secondary mx-1 mb-2">
                Devolución
                <input type="file" name="archivo" id="archivo_devolucion" data-tipo="devolucion" accept=".xls,.xlsx" style="display: none;">
            </label>
            <?php echo form_close(); ?>
        </div>
        <hr class="borde-orange mt-3">



        <div class="container-fluid m-top" id="tbl_manifiestos_imile"></div>



    </div>


    <!-- Modal -->
    <div class="modal fade" id="modal_grafica_general" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white text-dark">
                    <h5 class="modal-title" id="exampleModalLabel">Reporte de Movimientos</h5>
                    <button type="button" class="close" onclick="cerrar_modal_grafica()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="datos_para_grafica_general">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="cerrar_modal_grafica()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Carga Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
</body>

</html>