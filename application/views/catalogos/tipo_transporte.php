<select name="tipo_transporte" id="tipo_transporte" class="form-control chosen input-sm">
    <?php foreach ($t_transporte as $row): ?>
    <option value="<?php echo $row->idTipo; ?>"><?php echo  $row->Nombre; ?></option>
    <?php endforeach ?>
</select>
