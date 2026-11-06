<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <style>
    .modal-header {
        background-color: white;
        background-color: #E9573F;
        color: #F15A29;
        color: white;
    }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">

        <button type="button" class="btn-modal" data-toggle="modal" data-target="#cambiar_clave_modal"><i
                class="fa fa-key" aria-hidden="true"></i>
            Cambiar Clave</button>
        <button type="button" class="btn-modal" data-toggle="modal" data-target="#cambiar_correo_modal"><i
                class="fa fa-envelope-o" aria-hidden="true"></i> Cambiar
            Email</button>
        <button type="button" class="btn-modal" data-toggle="modal" data-target="#cambiar_sucursal_modal"><i
                class="fa fa-map-marker" aria-hidden="true"></i> Sucursal de
            Entrega</button>


        </div>
    </nav>

    <div class="modal fade" id="cambiar_clave_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CAMBIAR CLAVE</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form enctype="multipart/form-data" class="form_cambiar_pass" id="form_cambiar_pass">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="password" class="col-form-label">Introduzca nueva clave:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="alert  alert-dismissible fade show hide alert-orange" id="msg_pass">
                                <strong>Error!</strong> Introduzca clave!
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="password_repeat" class="col-form-label">Repita Clave:</label>
                            <input type="password" class="form-control" id="password_repeat" required>
                            <div class="alert  alert-dismissible fade show hide alert-orange" id="msg_repetir">
                                <strong>Error!</strong> Introduzca clave!
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="alert  alert-dismissible fade show hide alert-red" id="msg_cambiar_clave">
                                <strong>Error!</strong> Las claves introducidas no coinciden. Intentelo nuevamente

                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-modal" data-dismiss="modal"> <i class="fa fa-times"
                                aria-hidden="true"></i> Cancelar</button>
                        <button type="button" class="btn-modal" onclick="guardar_password()"><i class="fa fa-check"
                                aria-hidden="true"></i>
                            Aceptar</button>



                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cambiar_correo_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CAMBIAR CUENTA DE CORREO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form enctype="multipart/form-data" class="form_cambiar_correo" id="form_cambiar_correo">

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="correo" class="col-form-label">Introduzca cuenta de correo:</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                            <div class="alert  alert-dismissible fade show hide alert-orange" id="msg_correo">
                                <strong>Error!</strong> Introduzca cuenta de correo válida!
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-modal" data-dismiss="modal"> <i class="fa fa-times"
                                    aria-hidden="true"></i> Cancelar</button>
                            <button type="button" class="btn-modal" onclick="guardar_email()"><i class="fa fa-check"
                                    aria-hidden="true"></i>
                                Aceptar</button>

                        </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="cambiar_sucursal_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CAMBIAR SUCURSAL DE ENTREGA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form enctype="multipart/form-data" class="form_cambiar_sucursal" id="form_cambiar_sucursal">
                        <div class="form-group">
                            <?php $this->load->view('catalogos/sucursales'); ?>
                        </div>

                    </form>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-modal" data-dismiss="modal"> <i class="fa fa-times"
                            aria-hidden="true"></i> Cancelar</button>
                    <button type="button" class="btn-modal" onclick="guardar_sucursal()"><i class="fa fa-check" aria-hidden="true"></i>
                        Aceptar</button>
                </div>
            </div>
        </div>
    </div>


    <script>
    $("#password").focus(function() {
        // $(this).css("background-color", "#FFFFCC");
        $("#msg_cambiar_clave").hide("slow");
        $("#msg_pass").hide("slow");
        $("#msg_repetir").hide("slow");
        return false;
    });


    $("#password_repeat").focus(function() {
        // $(this).css("background-color", "#FFFFCC");
        $("#msg_cambiar_clave").hide("slow");
        $("#msg_pass").hide("slow");
        $("#msg_repetir").hide("slow");
        return false;
    });


    $("#correo").focus(function() {
        $("#msg_correo").hide("slow");
        return false;
    });

    /*function eatFood() {
        alert('Form has been submitted');
    }*/
    </script>
</body>

</html>