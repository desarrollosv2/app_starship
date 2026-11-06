<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <title></title>

    <style>
        .h {
            display: none;
        }
    </style>

</head>

<body>
    <?php
    // $this->load->view('hd'); 
    ?>

    <div class="container-fluid">
        <div class="row mt-2 ml-5 ">


            <div class="col-sm-10 mt-3">
                <h2 class="text-muted">Declaraciones</h2>
            </div>

            <!-- Botón alineado a la derecha -->
            <div class="col-sm-1 d-flex justify-content-end">
                <button class="btn btn-primary btn-circle btn-xl" title="Agregar Master" onclick="modal_add_master()">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>

           

        </div>
        <hr class="borde-orange mt-0">
    </div>

    <div class="container-fluid">
        <div id="awb_list"></div>
    </div>


    <!-- modal  para agregar manifiesto-->
    <div class="modal fade" id="modal_crear_master" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
    <?php $this->load->view("verifica");?>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title" id="" style="color: #F15A29">Crear Master</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="master">Master</label>
                        <input type="text" class="form-control" placeholder="Introduzca Master" id="master_fast" name="master_fast">
                    </div>

                    <div class="form-group">
                        <label for="transportista">Transportista</label>
                        <select name="transportista_fast" id="transportista_fast" class="form-control chosen">
                            <?php foreach ($transportista_fast as $row): ?>
                                <option value="<?php echo $row->id; ?>">
                                    <?php echo  $row->id . ' - ' . $row->nombre_tra; ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <button class="btn btn-primary float-md-right" onclick="guardar_master_fast()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="upfileModal" role="dialog" data-backdrop="static">
    <?php $this->load->view("verifica");?>
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cargar Archivos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>
                <form enctype="multipart/form-data" method="post" class="up_wh_fast" id="up_wh_fast" action="javascript:upload_fast()">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <select name="provee_id" id="provee_id" class=" form-control my-2">
                                    <?php foreach ($proveedor as $row): ?>
                                        <option value="<?php echo $row->client_id; ?>">
                                            <?php echo $row->nombre; ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <div class="row">
                                <input type="file" name="file" id="file" class="w-50" required accept=".xls" />
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="modal-footer">
                        <button type="submit" id="enviar" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true">Cargar</i></button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close" aria-hidden="true">Cancelar</i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- modal  para agregar manifiesto-->
    <div class="modal fade" id="modal_asignar_trasportista" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
    <?php $this->load->view("verifica");?>   
    <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title" id="" style="color: #F15A29">Asignar Transportista</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" id="id_trasportista" name="id_trasportista" hidden>
                    </div>

                    <div class="form-group">
                        <label for="transportista">Transportista</label>
                        <select name="Atransportista_fast" id="Atransportista_fast" class="form-control chosen">
                            <?php foreach ($transportista_fast as $row): ?>
                                <option value="<?php echo $row->id; ?>">
                                    <?php echo  $row->id . ' - ' . $row->nombre_tra; ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <button class="btn btn-primary float-md-right" onclick="guardar_transportista_fast()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


</body>

</html>