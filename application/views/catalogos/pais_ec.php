<select name="pais" id="pais" class="form-control w-50" required>

    <?php foreach ($pais as $row): ?>

    <option value="<?php echo $row->id; ?>"><?php echo  $row->id.' - '.$row->nombre; ?></option>

    <?php endforeach ?>

</select>