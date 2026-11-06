<select name="tipo_cuenta" id="tipo_cuenta" class="form-control chosen">
    <?php foreach ($tipo_cuenta as $row): ?>        
    		<option value="<?php echo $row->id_tipocuenta; ?>">
		<?php echo  $row->id_tipocuenta.' - '.$row->descripcion; ?>
		</option>
	<?php endforeach ?>
</select>
