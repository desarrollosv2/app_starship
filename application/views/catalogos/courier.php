<select name="comp_courier" id="comp_courier" class="form-control chosen">
	<?php foreach ($courier as $row): ?>
		<option value="<?php echo $row->id_courier; ?>">
		<?php echo  $row->id_courier.' - '.$row->descripcion; ?>
		</option>
	<?php endforeach ?>
</select>
