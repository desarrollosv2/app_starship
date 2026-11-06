
<select name="catalogo_ref" id="catalogo_ref" class=" form-control " >
<option value="Todo">Todo</option>
<option value="Pendiente">Pendiente</option>
	<?php foreach ($catalogo_ref as $row): ?>
		<option value="<?php echo $row->referencia; ?>">
		<?php echo $row->referencia; ?>
		</option>
	<?php endforeach ?>
</select>
