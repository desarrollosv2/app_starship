<!DOCTYPE html>
<html>
<head>
    <title>Editar Usuario</title>
</head>
<body>
    <h1>Editar Usuario</h1>
    <form method="post" action="<?php echo site_url('usuarios/update/'.$usuario['id']); ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo $usuario['nombre']; ?>"><br>
        <label for="correo">Correo:</label>
        <input type="text" id="correo" name="correo" value="<?php echo $usuario['correo']; ?>"><br>
        <label for="clave">Clave:</label>
        <input type="password" id="clave" name="clave"><br>
        <label for="id_empresa">ID Empresa:</label>
        <input type="text" id="id_empresa" name="id_empresa" value="<?php echo $usuario['id_empresa']; ?>"><br>
        <label for="ver_doc_privado">Ver Doc Privado:</label>
        <input type="text" id="ver_doc_privado" name="ver_doc_privado" value="<?php echo $usuario['ver_doc_privado']; ?>"><br>
        <label for="id_estatus">ID Estatus:</label>
        <input type="text" id="id_estatus" name="id_estatus" value="<?php echo $usuario['id_estatus']; ?>"><br>
        <label for="casillero">Casillero:</label>
        <input type="text" id="casillero" name="casillero" value="<?php echo $usuario['casillero']; ?>"><br>
        <label for="interno">Interno:</label>
        <input type="checkbox" id="interno" name="interno" value="1" <?php echo $usuario['interno'] ? 'checked' : ''; ?>><br>
        <label for="id_pais">ID País:</label>
        <input type="text" id="id_pais" name="id_pais" value="<?php echo $usuario['id_pais']; ?>"><br>
        <label for="perfil">Perfil:</label>
        <input type="text" id="perfil" name="perfil" value="<?php echo $usuario['perfil']; ?>"><br>
        <input type="submit" value="Guardar">
    </form>
</body>
</html>
