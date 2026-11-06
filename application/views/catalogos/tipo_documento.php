<select name="tipo_documento" id="tipo_documento" class="form-control chosen">
    <?php foreach ($tipo_documento as $row): ?>        
    		<option value="<?php echo $row->id_tipodocumento; ?>">
		<?php echo  $row->id_tipodocumento.' - '.$row->descripcion; ?>
		</option>
	<?php endforeach ?>
</select>
