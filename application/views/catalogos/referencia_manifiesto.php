<select name="referencia" id="referencia" class="form-control chosen input-sm">
    <option value="0">Seleccione una referencia</option>
    <?php foreach ($referencias as $row): ?>
        <option value="<?php echo $row->id; ?>"><?php echo  $row->referencia; ?></option>
    <?php endforeach ?>
</select>