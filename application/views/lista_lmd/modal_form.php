<!--Vista de Formulario para editar o eliminar campos de partidas-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  
</head>
<style>
  .group 
  {
    width:     750px;
    height:    850px;
    top:          0%;
    margin-left: 15%;
  }

  .boton 
  {
    top:         75%;
    margin-left: 35%;
  }
</style>

<?php
  $id                 =  NULL;
  $fecha_manifiesto   =    '';
  //$tipo_entrega       =    '';
  $observaciones      =    '';
  
if (isset($listado_lmd)) 
{
    $id                     = $listado_lmd->id;
    $fecha_manifiesto       = $listado_lmd->fecha_manifiesto;
    //$tipo_entrega           = $listado_lmd->tipo_entrega;
    $observaciones          = $listado_lmd->observaciones;
}
?>

<body>
  <div class="container">
    <form enctype="multipart/form-data" class="add_manifiesto" id="add_manifiesto" action="javascript:guardar_manifiesto()">
      <div class="form-group">
        <br>
        <h3 id="titulo"></h3>
        <br>

        <div class="form-group">
            <label>ID</label>
            <input type="text" class="form-control w-100" value="<?php echo $id; ?>" id="id" name="id" readonly>
        </div>

        <div class="form-group">
            <label class="control-label">Fecha</label>
            <input type="date" class="form-control w-100" value="<?php echo $fecha_manifiesto; ?>" name="fecha_manifiesto" id="fecha_manifiesto" required> 
        </div>
  
        <div class="form-group">
            <label for="Input2">Observaciones</label>
            <textarea class="form-control" class="form-control" value="<?php echo $observaciones; ?>" name="observaciones" id="observaciones" required rows="3"><?php echo $observaciones; ?></textarea>
        </div>

        <div class="form-group">
            <button onclick="listado_lmd()" type="button" class="btn btn-dark btn-lg"> Atras</button>
            <button class="btn btn-success btn-lg"> Guardar</button>
        </div>
</form>
</div>


</body>
</html>