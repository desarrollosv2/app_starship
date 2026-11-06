<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Usuarios</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col">

                <h1>Crear Usuario</h1>
                <form method="post" action="<?php echo site_url('usuarios/store'); ?>">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre"><br>
                    <label for="correo">Correo:</label>
                    <input type="text" id="correo" name="correo"><br>
                    <label for="clave">Clave:</label>
                    <input type="password" id="clave" name="clave"><br>
                    <label for="id_empresa">ID Empresa:</label>
                    <input type="text" id="id_empresa" name="id_empresa"><br>
                    <label for="ver_doc_privado">Ver Doc Privado:</label>
                    <input type="text" id="ver_doc_privado" name="ver_doc_privado"><br>
                    <label for="id_estatus">ID Estatus:</label>
                    <input type="text" id="id_estatus" name="id_estatus"><br>
                    <label for="casillero">Casillero:</label>
                    <input type="text" id="casillero" name="casillero"><br>
                    <label for="interno">Interno:</label>
                    <input type="checkbox" id="interno" name="interno" value="1"><br>
                    <label for="id_pais">ID País:</label>
                    <input type="text" id="id_pais" name="id_pais"><br>
                    <label for="perfil">Perfil:</label>
                    <input type="text" id="perfil" name="perfil"><br>
                    <input type="submit" value="Guardar">
                </form>
            </div>
        </div>
    </div>
</body>

</html>