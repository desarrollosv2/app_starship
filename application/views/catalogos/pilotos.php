<select name="piloto" id="piloto" class="form-control">
    <option value="" disabled selected hidden>Seleccione el piloto</option>
    <?php foreach ($pilotos as $row): ?>
        <option value="<?php echo $row->id; ?>">
            <?php echo $row->nombre; ?>
        </option>
    <?php endforeach ?>
</select>

<script type="text/javascript">
    $(document).ready(function() {
        $("#piloto").select2({
            theme: 'bootstrap4',
            placeholder: "Seleccione o escriba el piloto",
            allowClear: true,
            width: '100%',
            tags: true,
            createTag: function(params) {
                return {
                    id: params.term,
                    text: params.term,
                    newTag: true
                };
            }
        });
    });
</script>