<select name="municipio" id="municipio" class="form-control chosen">

    <?php foreach ($municipio as $row): ?>

    <option value="<?php echo $row->id; ?>"><?php echo  $row->nombre; ?></option>

    <?php endforeach ?>

</select>
