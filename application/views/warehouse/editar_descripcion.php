<?php echo form_open('', ['id' => 'frm_editar_descripcion']); ?>
<input type="hidden" name="id" value="<?php echo $descripcion->id; ?>">
<div class="form-group">
    <label for="descripcion_textarea"><strong>Descripción</strong></label>
    <textarea id="descripcion_textarea" name="descripcion" class="form-control" rows="5"><?php echo $descripcion->commodity; ?></textarea>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
</div>
<?php echo form_close(); ?>


<script>
    function base_url(url) {
        return window.location.origin + "/app_starship/" + url;
    }

    $(document).ready(function() {

        $('#frm_editar_descripcion').on('submit', function(e) {
            e.preventDefault();
            var id = $("#id-master").val();
            var master = $("#master-number").val();
            var correla = $("#correla").val();
            var url = base_url('index.php/WhController/guardar_descripcion');
           
            var data = $('#frm_editar_descripcion').serialize();
            $("#loader").css("display", "block");
            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(response) {
                    alertify.set("notifier", "position", "top-right");
                    alertify.success("Items Description ha sido actualizado correctamente");
                    $(".modal-backdrop").remove();
                    $('#editar_descripcion_modal').modal('hide');

                    $("#loader").css("display", "none");
                    lista_warehouse(id, master, correla);
                }
            });
        });
    });
</script>