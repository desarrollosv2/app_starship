<?php
$id_tasa            = "";
$id_pais            = "";
$nombre_moneda      = "";
$t_cambio           = 0;
$impuesto = 0;
$manejo = 0;

if (isset($tasa_cambio)) {
    //echo $tasa_cambio->id_tasa;
    $id_tasa            = $tasa_cambio->id;
    $id_pais            = $tasa_cambio->id_pais;
    $nombre_moneda      = $tasa_cambio->nombre_moneda;
    $t_cambio           = $tasa_cambio->tasa;
    $impuesto = $tasa_cambio->impuesto;
    $manejo = $tasa_cambio->manejo;
}



?>
<div class="container" id="agregarItem">
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-danger mt-2">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Datos Tasa de Cambio</h3>

                </div>

                <div class="input-group input-group-sm mb-3">
                </div>
                <form enctype="multipart/form-data" class="add_tasa" id="add_tasa" action="javascript:guardar_tasa_cambio()">

                    <div class="container ">
                        <div class="row">
                            <div class="input-group input-group-sm mb-3  mr-3">

                                <label class="col-sm-2 control-label">País</label>
                                <input type="hidden" id="id_pais" name="id_pais" value="<?php echo $id_pais ?>">
                                <input type="hidden" id="id_tasa1" name="id_tasa1" value="<?php echo $id_tasa ?>">
                                <?php $this->load->view('catalogos/pais_ec') ?>

                            </div>
                            <div class="input-group input-group-sm mb-3  mr-3">

                                <label class="col-sm-2 control-label">Nombre de Moneda</label>
                                <input type="text" class="form-control" value="<?php echo $nombre_moneda ?>" name="nombre_moneda" id="nombre_moneda">


                                <label class="col-sm-2 control-label">Tasa de Cambio X Dolar</label>
                                <input type="text" class="form-control " value="<?php echo number_format($t_cambio, 5) ?>" name="tasa_de_cambio" id="tasa_de_cambio" step="any" style="text-align: end;">



                            </div>

                            <div class="input-group input-group-sm mb-3  mr-3">

                                <label class="col-sm-2 control-label">Impuesto</label>
                                <input type="text" class="form-control" value="<?php echo $impuesto ?>" name="impuesto" id="impuesto">


                                <label class="col-sm-2 control-label">Manejo</label>
                                <input type="text" class="form-control" value="<?php echo number_format($manejo, 5) ?>" name="manejo" id="manejo" step="any" style="text-align: end;">



                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="btnSave" id="btnSave" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"> Guardar</i></button>
                        <button type="button" class="btn btn-primary" id="btnBack" onclick="listar_tasa_cambio()" pull-right data-whatever="">
                            <i class=" fa fa-arrow-left">
                                Volver</i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {

            $("#pais").change(function() {
                $("#id_pais").val($("#pais").val())
            });

        });
    </script>