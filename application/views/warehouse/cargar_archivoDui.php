<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        .hide {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="alert alert-primary alert-dismissible fade show hide" id="msg" role="alert">
            <strong>La carga se ha realizado correctamente!</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

    </div>

    <div class="modal fade" id="upfileModalDui" role="dialog" data-backdrop="static">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Actualizar número identidad personal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="row-fluid" id="messagefile"></div>
                <form enctype="multipart/form-data" method="post" class="upwh" id="upwh"
                    action="javascript:upload_fileDUI()">
                    <input type="hidden" id="idpreclasificacion">

                    <div class="modal-body">

                        <!--   <div class="container-fluid">
                            <div class="row">
                                <input type="file" name="file" id="file" class="custom-file-input" required accept=".xls" />

                            </div>
                            
                        </div>

                    </div> -->
                        <div class="form-group">
                           <div class="custom-file">
                                <input type="file" name="file" id="file" class="custom-file-input" required accept=".xls" />
                                <label class="custom-file-label shadow-sm" for="file">Seleccione archivo</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" id="enviar" class="btn btn-primary">Aplicar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>