<label for="dui">Tracking Number</label>
<select class="form-control" placeholder=" " id="tracking_number" name="tracking_number" onchange="valor_guiasxref()">
    <?php foreach ($guiasxref as $row) : ?>
       
        <option value="<?php echo $row->tracking_number; ?>" >
            <?php echo $row->tracking_number; ?>
        </option>
    <?php endforeach ?>
</select>




<script>
    $(document).ready(function() {
        $("#tracking_number").select2({
            theme: 'bootstrap4',
      
            allowClear: true,
            width: 'resolve',
        });
    });
</script>