<select name="servicio" id="servicio" class="form-control chosen">

    <?php foreach ($servicios as $row): ?>

    <option value="<?php echo $row->idservicio; ?>"><?php echo  $row->idservicio.' - '.$row->nombre; ?></option>

    <?php endforeach ?>

</select>
