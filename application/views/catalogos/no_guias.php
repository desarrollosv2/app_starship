<label for="dui">Cantidad</label>
<select class="form-control" placeholder=" "  id="valor" name="valor" readonly>
    <?php foreach ($listadoxsaco as $row) : ?>
        <option value="<?php echo $row->no_guia; ?>">
            <?php echo $row->no_guia; ?>
        </option>
    <?php endforeach ?>
</select>
<br>
<label for="dui">Valor</label>
<select class="form-control" placeholder=" "  id="valor" name="valor" readonly>
    <?php foreach ($listadoxsaco as $row) : ?>
        <option value="<?php echo $row->total; ?>">
            <?php echo $row->total; ?>
        </option>
    <?php endforeach ?>
</select>