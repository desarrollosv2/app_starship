<select name="id_transportista" id="id_transportista" class="form-control chosen input-sm">
    <?php foreach ($transportista as $row): ?>
    <option value="<?php echo $row->id; ?>"><?php echo  $row->nombre_tra; ?></option>
    <?php endforeach ?>
</select>
