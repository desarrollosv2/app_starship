<select name="opc_inventario" id="opc_inventario" class="form-control chosen">

    <?php foreach ($opc_inventario as $row): ?>

    <option value="<?php echo $row->id_opcion; ?>"><?php echo  $row->id_opcion.' - '.$row->descripcion; ?></option>

    <?php endforeach ?>

</select>
