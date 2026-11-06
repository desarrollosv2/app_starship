<select name="partidas" id="partidas" class="form-control w-100" required>

    <?php foreach ($partidas as $row) : ?>

        <option value="<?php echo $row->id; ?>"><?php echo "Partida: " . $row->numero_partida . ' Descripción: ' . $row->descripcion; ?></option>

    <?php endforeach ?>

</select>

<script type="text/javascript">
    $(document).ready(function() {

        $("#partidas").select2({
            theme: 'bootstrap4',
            placeholder: "Select partida arancelaria",
            allowClear: true,
            width: 'resolve',
        });

    });
</script>