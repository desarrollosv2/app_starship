
<select name="estado" id="estado" class="form-control chosen ">

    <?php foreach ($lista_estado as $row): ?>

    <option value="<?php echo $row->id; ?>"><?php echo $row->nombre; ?></option>

    <?php endforeach ?>

</select>