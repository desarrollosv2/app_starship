<select name="proveedor_lm" id="proveedor_lm" class="form-control ">

    <?php foreach ($proveedor_lm as $row): ?>

        <option value="<?php echo $row->id; ?>"><?php echo $row->nombre; ?></option>

    <?php endforeach ?>

</select>