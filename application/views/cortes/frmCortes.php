<?php
//var_dump($info); 
if (isset($info)) {
  $id_corte = $info->id_corte;
  $hora_corte = $info->hora_corte;
  $dia = $info->dia_adicional;
} else {
  $id_corte = '';
  $hora_corte = '';
  $dia = '';
}

?> <!-- Aquí va tu formulario -->
<?php echo form_open('CortesController/agregarCorte', ['id' => 'formCorte']); ?>
<div class="row mb-3">
  <input type="hidden" id="id_corte" name="id_corte" value="<?= $id_corte ?>">
  <div class="col-md-6">
    <label for="hora_corte" class="form-label">Hora del corte:</label>
    <input type="time" class="form-control" value="<?= $hora_corte ?>" id="hora_corte" name="hora_corte" step="1" required>
  </div>

  <div class="col-md-6 text-center">
    <label class="form-label" for="dia_adicional">Día adicional:</label>
    <div>
      <input type="hidden" name="dia_adicional" value="0">
      <input type="checkbox"
        class="form-check-input"
        id="dia_adicional"
        name="dia_adicional"
        value="1"
        <?= $dia == 1 ? 'checked' : '' ?>
        style="transform: scale(2.5); margin-top: 10px;">
    </div>
  </div>
</div>
<div>
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
  <button type="submit" form="formCorte" class="btn btn-primary">Guardar</button>
</div>
<?php echo form_close(); ?>


<script>
  $(document).ready(function() {
    function base_url(url) {
      return window.location.origin + "/app_starship/" + url;
    }

    $('#formCorte').on('submit', function(e) {
        e.preventDefault();

        var data = $('#formCorte').serialize();
       
        var url = base_url('index.php/CortesController/agregarCorte')
        $.ajax({
            url: url,
          method: 'POST',
          data: data,
          dataType: 'json',
          success: function(response) {
            if (response.status === "success") {
              alertify.set('notifier', 'position', 'top-right');
              alertify.success(response.mensaje);
              $('#id_corte').val('');
              $('#hora_corte').val('');
              $('#dia_adicional').val('');
             // const modal = bootstrap.Modal.getInstance(document.getElementById('modalCorte'));
             // modal.hide();
             $(".modal-backdrop").remove();
        $("#modalCorte").modal("hide");

              recolecta_corte();
            } else {
              alertify.set('notifier', 'position', 'top-right');
              alertify.error("Algo salió mal");
            }
          },
          error: function(xhr, status, error) {
            console.error("Error:", error);
          }
        });
    });
  });
</script>