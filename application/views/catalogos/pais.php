<select name="origen" id="origen" class="form-control w-50" required>

    <?php foreach ($origen as $row): ?>

    <option value="<?php echo $row->id; ?>"><?php echo  $row->id.' - '.$row->nombre; ?></option>

    <?php endforeach ?>

</select>
