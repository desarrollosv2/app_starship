<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style></style>
</head>
<body>
    <div class="container-fluid">
        <div class="row borde-orange">
            <div class="col-md-6">
                <h4 class="m-3">INVENTARIOS</h4>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-primary  pull-right " data-toggle="modal"
                data-target="#ModalAdd_Inventario" data-whatever="">
                <i class="glyphicon glyphicon-plus"></i>
                Crear
            </button>
        </div>
    </div>
</div>
<!-- modal  para agregar manifiesto-->
<div class="modal fade" id="ModalAdd_Inventario" tabindex="-1" role="dialog"aria-labelledby="" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Crear </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" class="add_inventario" id="add_inventario"
                    action="javascript:guardar_inventario()">
                    <input type="hidden" class="form-control" name="id" id="id">
                    <div class="form-group">
                        <label for="manfiesto">ID Inventario</label>
                        <input type="text" class="form-control" name="id_inventario"
                        value="<?php echo 'INV-'.date('dmyHis') ?>" id="id_inventario" readonly>
                    </div>
                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" class="form-control" placeholder="Introduzca fecha " id="fecha" name="fecha" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <textarea class="form-control" rows="2" id="descripcion" name="descripcion"></textarea>
                    </div>
                    <div class="form-group">
                        <?php $this->load->view('catalogos/estatus'); ?>
                    </div>
                    <div class="form-group">
                        <?php $this->load->view('catalogos/opc_inventario'); ?>
                    </div>
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                    </button>
                    <button class="btn btn-primary float-md-right ">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!--- modal para mostrar guias -->
<div class="modal fade " id="modal_guias" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered mimodal " role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" style="background-color:white">
                <h4 class="modal-title" style="color:#F15a29">Consulta de Guias</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="row-fluid">
                <div class="messageup" id='messageup'>
                </div>
            </div>
            <form enctype="multipart/form-data" class="listaguia" id="listaguia" action="javascript:consulta_guias;">
                <!-- Modal body -->
                <div class="modal-body">
                    <div id="listado_guias">
                        <?php $this->load->view('preclasifica/lista_guias');?>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--- fin modal para mostrar guias -->



</body>
</html>