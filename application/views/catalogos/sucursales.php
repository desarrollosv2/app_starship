<select name="sucursal" id="sucursal" class="form-control chosen">

    <?php foreach ($sucursales as $row): ?>

    <option value="<?php echo $row->idsucursal; ?>"><?php echo  $row->idsucursal.' - '.$row->nombre; ?></option>

    <?php endforeach ?>

</select>
