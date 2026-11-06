<select name="declarante" id="declarante" class="form-control chosen input-sm">
    <?php foreach ($agentes as $row): ?>
    <option value="<?php echo $row->idAgente; ?>"><?php echo  $row->Nombre; ?></option>
    <?php endforeach ?>
</select>