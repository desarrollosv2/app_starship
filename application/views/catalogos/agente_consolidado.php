<select name="agent_cong" id="agent_cong" class="form-control chosen input-sm">
    <?php foreach ($agentes as $row): ?>
    <option value="<?php echo $row->idAgente; ?>"><?php echo  $row->Nombre; ?></option>
    <?php endforeach ?>
</select>
