<?php if(isset($usuarios)) : ?>

    <div class="table-responsive">
        <table class="table" id="tabla_usuarios_sistema">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Pais</th>
                    <th scope="col">Proceso</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Estado</th>
                    <?php if (isset($acciones_usuario['u']) && $acciones_usuario['u'] == 1): ?>
                        <th scope="col">Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($usuarios)) {
                    $i = 1;
                    foreach ($usuarios as $row) {
                ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row->nombre ?></td>
                            <td><?= $row->correo ?></td>
                            <td><?= $row->pais ?></td>
                            <td><?= $row->proceso ?></td>
                            <td><?= $row->rol ?></td>
                            <td>
                                <?php if ($row->id_estatus == 1): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <?php if (isset($acciones_usuario['u']) && $acciones_usuario['u'] == 1): ?>
                                <td>
                                    
                                        <a href='#' class="btn btn-default btn-sm" style="margin:0px;" title="Editar" onclick="editar_usuario_sistema(<?php echo $row->id;?>);">
                                            <i class="fa fa-pencil" style="color: blue;" aria-hidden="true"></i> </a>

                                        <?php if ($row->id_estatus == 1): ?>
                                            <a href='#' class="btn btn-default btn-sm" style="margin:0px;" title="Inactivar" onclick="inactivar_usuario_sistema(<?php echo $row->id;?>);">
                                            <i class="fa fa-times" style="color: red;" aria-hidden="true"></i> </a>
                                        <?php else: ?>
                                            <a href='#' class="btn btn-default btn-sm" style="margin:0px;" title="Activar" onclick="activar_usuario_sistema(<?php echo $row->id;?>);">
                                            <i class="fa fa-check" style="color: green;" aria-hidden="true"></i> </a>
                                        <?php endif; ?>
                                    
                                </td>
                            <?php endif; ?>
                        </tr>
                <?php
                    $i++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>