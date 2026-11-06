<select name="origen" id="origen" class="form-control chosen ">

    <?php foreach ($lista_origen as $row): ?>

    <option value="<?php echo $row->id; ?>"><?php echo $row->nombre; ?></option>

    <?php endforeach ?>

</select>
