<div class="form-group">
    <?php foreach ($valor_guiasxref_general as $row) : ?>
        <input type="hidden" class="form-control" placeholder=" " id="id_manifiesto" name="id_manifiesto" value="<?php echo $row->id_manifiesto; ?>">
    <?php endforeach ?>
</div>


<div class="row">
        <div class="col">
        <label for="dui">Total DAI</label>
        <select class="form-control" placeholder="" id="total_dai" name="total_dai" readonly>
            <?php foreach ($valor_guiasxref_general as $row) : ?>
                <option value="<?php echo $row->Total_DAI; ?>">
                    <?php echo $row->Total_DAI; ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
    
    <div class="col">
        <label for="dui">Total IVA</label>
        <select class="form-control" placeholder="" id="total_iva" name="total_iva" readonly>
            <?php foreach ($valor_guiasxref_general as $row) : ?>
                <option value="<?php echo $row->Total_IVA; ?>">
                    <?php echo $row->Total_IVA; ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>


</div>