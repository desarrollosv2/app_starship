<select name="departamento" id="departamento" class="form-control chosen">

    <?php foreach ($departamento as $row): ?>

    <option value="<?php echo $row->id; ?>"><?php echo  $row->nombre; ?></option>

    <?php endforeach ?>

</select>
