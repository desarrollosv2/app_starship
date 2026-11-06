<select name="empresa" id="empresa" class="form-control chosen">
	<?php foreach ($empresa as $row): ?>
		<option value="<?php echo $row->id; ?>">
		<?php echo  $row->nombre; ?>
		</option>
	<?php endforeach ?>
</select>
