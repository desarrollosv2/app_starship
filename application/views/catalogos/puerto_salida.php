<select name="puerto_salida" id="puerto_salida" class="form-control chosen input-sm">
    <?php foreach ($puertos as $row): ?>
    <option value="<?php echo $row->idPuerto; ?>"><?php echo  $row->Nombre; ?></option>
    <?php endforeach ?>
    </select>