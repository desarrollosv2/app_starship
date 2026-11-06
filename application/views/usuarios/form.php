<?php 
    //var_dump($usuario);
    /** @var object|null $usuario */

    if(isset($usuario) && !empty($usuario)) {
        $id_usuario = $usuario->id;
        $nombre = $usuario->nombre;
        $correo = $usuario->correo;
        $id_proceso = $usuario->id_proceso;
        $id_rol = $usuario->id_rol;
        $id_estatus = $usuario->id_estatus;
        $id_pais = $usuario->id_pais;
    } else {
        $id_usuario = '';
        $nombre = '';
        $correo = '';
        $id_proceso = '';
        $id_rol = '';
        $id_estatus = '';
        $id_pais = '';
    }

?>


<div class="gestion_usuarios_sistema">

        <form id="form_usuarios_sistema" autocomplete="new-password">
            <input type="hidden" id="id_usuario" name="id_usuario" value="<?= $id_usuario ?>">

            <div class="row">
                <div class="col-md-6 form-group mb-3">

                    <label class="font-weight-bold text-muted small text-uppercase mb-1">
                        Nombre usuario
                    </label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0">
                                <i class="fa fa-user text-muted"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control border-left-0" id="usuario_nombre" name="usuario_nombre" value="<?= $nombre ?>">
                    </div>

                    <div class="invalid-feedback" id="error_usuario_nombre"></div>

                </div>

                <div class="col-md-6 form-group mb-3">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Correo electrónico</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0">
                                <i class="fa fa-envelope text-muted"></i>
                            </span>
                        </div>
                        <input type="email" class="form-control border-left-0" id="usuario_correo" name="usuario_correo" value="<?= $correo ?>">
                    </div>
                    <div class="invalid-feedback" id="error_usuario_correo"></div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-3 form-group mb-3">

                    <label class="font-weight-bold text-muted small text-uppercase mb-1">
                        Contraseña
                    </label>

                    <div class="input-group">

                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0"
                                onclick="mostrar_contrasena()"
                                style="cursor:pointer;">
                                <i class="fa fa-eye text-muted" id="icono_contrasena"></i>
                            </span>
                        </div>

                        <input type="password" class="form-control border-left-0" id="usuario_clave" name="usuario_clave" autocomplete="new-password">
                        <small class="form-text text-muted" id="ayuda_contrasena"></small>
                    </div>

                    <div class="invalid-feedback" id="error_usuario_clave"></div>

                </div>


                <div class="col-md-3 form-group mb-3">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Pais</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0">
                                <i class="fa fa-male text-muted"></i>
                            </span>
                        </div>
                        <select class="form-control border-left-0" id="usuario_pais" name="usuario_pais">
                            <option value="" disabled
                                <?= empty($id_pais) ? 'selected' : '' ?>>
                                Seleccionar pais
                            </option>

                            <?php if (!empty($paises)): ?>
                                <?php foreach ($paises as $pais): ?>

                                    <option value="<?= $pais->id ?>"
                                        <?= (!empty($id_pais) && $id_pais == $pais->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pais->nombre) ?>
                                    </option>

                                <?php endforeach; ?>
                            <?php endif; ?>

                        </select>
                    </div>
                    <div class="invalid-feedback" id="error_usuario_pais"></div>
                </div>

                <div class="col-md-3 form-group mb-3">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Rol</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0">
                                <i class="fa fa-male text-muted"></i>
                            </span>
                        </div>
                        <select class="form-control border-left-0" id="usuario_rol" name="usuario_rol">
                            <option value="" disabled
                                <?= empty($id_rol) ? 'selected' : '' ?>>
                                Selecciona rol
                            </option>

                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $rol): ?>

                                    <option value="<?= $rol->id ?>"
                                        <?= (!empty($id_rol) && $id_rol == $rol->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($rol->nombre_rol) ?>
                                    </option>

                                <?php endforeach; ?>
                            <?php endif; ?>

                        </select>
                    </div>
                    <div class="invalid-feedback" id="error_usuario_rol"></div>
                </div>
                

                <div class="col-md-3 form-group mb-3">
                    <label class="font-weight-bold text-muted small text-uppercase mb-1">Proceso</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0">
                                <i class="fa fa-sitemap text-muted"></i>
                            </span>
                        </div>
                        <select class="form-control border-left-0" id="usuario_proceso" name="usuario_proceso">
                            <option value="" disabled
                                <?= empty($id_proceso) ? 'selected' : '' ?>>
                                Selecciona proceso
                            </option>

                            <?php if (!empty($procesos)): ?>
                                <?php foreach ($procesos as $proceso): ?>

                                    <option value="<?= $proceso->id ?>"
                                        <?= (!empty($id_proceso) && $id_proceso == $proceso->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($proceso->nombre_proceso) ?>
                                    </option>

                                <?php endforeach; ?>
                            <?php endif; ?>

                        </select>
                    </div>
                    <div class="invalid-feedback" id="error_usuario_proceso"></div>
                </div>

        </div>
    </form>
</div>