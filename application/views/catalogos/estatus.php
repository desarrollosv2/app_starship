<select name="estatus" id="estatus" class="form-control chosen">

    <?php foreach ($estatus as $row): ?>

    <option value="<?php echo $row->id_estatus; ?>"><?php echo  $row->id_estatus.' - '.$row->nombre; ?></option>

    <?php endforeach ?>

</select>
