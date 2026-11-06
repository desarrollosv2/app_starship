<!--Vista de Formulario para editar o eliminar campos de partidas-->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/btones.css') ?>">

  <title></title>
  <style>

  </style>
</head>


<body>

  <?php
  $id              = NULL;
  $numero_partida  =    "";
  $descripcion     =   '';
  $origen          =   '';
  $estado          =    0;
  $id_origen        = "";
  $codigo_producto = "";
  $tlc = "";
  $marca = "";
  $modelo = "";
  $descripcion_generica = "";

  $estado_id = "";
  $origen_id = "";




  if (isset($datos_partida)) {
    $numero_partida = $datos_partida->numero_partida;
    $descripcion    = $datos_partida->descripcion;
    $id_origen         = $datos_partida->id_origen;
    $anulado        = $datos_partida->anulado;
    $codigo_producto        = $datos_partida->codigo_producto;
    $modelo = $datos_partida->modelo;
    $marca = $datos_partida->marca;
    $descripcion_generica = $datos_partida->descripcion_generica;
    $tlc        = $datos_partida->tlc;

    $estado_id = $datos_partida->id_estado;
    $origen_id = $datos_partida->id_origen;
    //echo($descripcion);
  }
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  ?>
  <!--Vista de Formulario para editar o eliminar campos de partidas-->

  <div class="container">


    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-2">
        <h5 class="text-muted m-0" id="titulo"></h5>
      </div>


      <div class="card-body">

        <form enctype="multipart/form-data" class="add_arancel" id="add_arancel" action="javascript:guardar_partidas()">
          <input type="hidden" class="form-control w-100" id="id_arancel" name="id_arancel" readonly>

          <input type="hidden" id="id-origen" value="<?php echo $origen_id ?>">
          <input type="hidden" id="id-estado" value="<?php echo $estado_id ?>">


          <div class="form-row">

            <div class="form-group col-md-6">
              <label class="control-label">Código Producto</label>
              <input type="text" class="form-control " value="<?php echo $codigo_producto; ?>" name="codigo_producto" id="codigo_producto" required>
            </div>

            <div class="form-group col-md-6">
              <label for="Input2">Descripción</label>
              <input type="text" class="form-control " value="<?php echo $descripcion; ?>" name="descripcion" id="descripcion" required>
            </div>

          </div>


          <div class="form-row">
            <div class="form-group col-md-6">

              <label class="control-label">Numero Partida</label>
              <input type="text" class="form-control " value="<?php echo $numero_partida; ?>" name="numeroPartida" id="numeroPartida" required>
            </div>

            <!--  <div class="form-group col-md-6">
              <label class="control-label">Marca</label>
              <input type="text" class="form-control " value="<?php echo $marca; ?>" name="marca" id="marca" required>
            </div>   -->
          </div>

         <!-- <div class="form-row">

            <div class="form-group col-md-6">
              <label class="control-label">Modelo</label>
              <input type="text" class="form-control " value="<?php echo $modelo; ?>" name="modelo" id="modelo" required>
            </div>

            <div class="form-group col-md-6">
              <label class="control-label">Descripción Genérica</label>
              <input type="text" class="form-control " value="<?php echo $descripcion_generica; ?>" name="descripcion_generica" id="descripcion_generica" required>
            </div>

          </div> -->

          <div class="form-row">

            <div class="form-group col-md-6">
              <label class="control-label">Porcentaje</label>
              <input type="text" class="form-control " value="<?php echo "10"; ?>" name="porcentaje" id="porcentaje" required>
            </div>

          <!--  <div class="form-group col-md-6">
              <label class="control-label">Estado</label>
              <?php //$this->load->view('catalogos/estado'); ?>
            </div> -->

          </div>
          <!-- 
          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="control-label">Origen</label>
              <?php //$this->load->view('catalogos/origen'); ?>
            </div>
          </div>
         <div class="form-group" style="display:none">
             <label class="control-label">TLC</label>
            <input type="hidden" id="id-tlc"  value="<?php //echo $tlc; 
                                                      ?>">
            <select class="form-control w-50" id="tlc" name="tlc" required>
            <option title="NO">NO</option> 
            <option value="SI">SI</option>
           
             
            </select>

          </div>
          -->
          <footer class=" text-center">
            <button onclick="regresaremos()" type="button" class="btn btn-secondary btn-md button">Cancelar</button>
            <button class="btn btn-primary btn-md button">Guardar</button>

          </footer>
        </form>
      </div>
    </div>

  </div>

  <script>
    $(document).ready(function() {

      $("#tlc").val($("#id-tlc").val());
      $("#tlc").change();




      var origen = document.getElementById("id-origen").value;
      var estado = document.getElementById("id-estado").value;

      $("#origen").val(origen);
      $('#origen').change();

      $("#estado").val(estado);
      $('#estado').change();

    });
  </script>

</body>

</html>