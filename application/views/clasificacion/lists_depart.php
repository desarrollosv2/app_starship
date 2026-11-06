<?php //var_dump($municipios);?>
<?php //var_dump($departamentos[0]['id']);?>
<?php 
/*$i = 0;
foreach($departamentos as $key) {

	echo $departamentos[$i]['id']."<br>"; 
    echo "Index is $i\n";
    $i++;
}
*/
?>
<div class="container">
	<div class="row">
		<div class="col">
			<div class="form-group">
				<input type="hidden" name="id_manifiesto" id="id_manifiesto" >
			</div>
			<div class="form-group">
				<label for="departamentos">Departamentos</label>
				<select name="ls_depart" id="ls_depart" class="form-control chosen">
				<?php $i = 0; foreach ($departamentos as $row): ?>
				<option value="<?php echo $departamentos[$i]['id']; ?>-<?php echo $departamentos[$i]['codigo']; ?>-<?php echo $departamentos[$i]['nombre']; ?>"><?php echo  $departamentos[$i]['id'].' - '.$departamentos[$i]['nombre']; ?></option>
				<?php  $i++; endforeach ?>
				</select>
			</div>
			<div class="form-group">
				<label for="municipio">Municipio</label>
				<select name="ls_municipio" id="ls_municipio" class="form-control chosen">
				<?php $x = 0; foreach ($municipios as $row): ?>
				<option value="<?php echo $municipios[$x]['id']; ?>-<?php echo $municipios[$x]['nombre']; ?>"><?php echo  $municipios[$x]['id'].' - '.$municipios[$x]['nombre']; ?></option>
				<?php  $x++; endforeach ?>
				</select>
			</div>
			
		   
		</div>
	</div>
</div>

<script type="text/javascript">
 $('#ls_depart').on('change', function() {
    $("#ls_municipio").html('');
    var id = this.value;
    console.log(id);
    var url = base_url("index.php/WhController/municipio/"+id);
    $.get(url, function(data) {
        //console.log(data);
        var obj = JSON.parse(data);
        console.log(obj);
		$.each(obj, function (i, json) {      
			$("#ls_municipio").append('<option value="'+json.id+'-'+json.nombre+'">'+json.id+' - '+json.nombre + '</option>');
		});
    });
 });

$('#save_manifiesto_ups').on('click', function(){
	var id_manifiesto 	= $('#id_manifiesto').val();
	var departamentos 	= $('#ls_depart').val();
	var municipios		= $('#ls_municipio').val();
	var formData;
	var formData = new FormData($("#dm_list")[0]);
	//var url = base_url("index.php/WhController/update_depart/"+id_manifiesto+"/"+departamentos+"/"+municipios);
	var url = base_url("index.php/WhController/update_depart");
	 $.ajax({
        url: url,
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            //if (data == 1){
                $(".modal-backdrop").remove();
            	$("#guia_express_modal2").modal("hide");
            lista_warehouse( $('#id-master').val(), $('#master-number').val(),$('#numero-guia').val());
            //}
        },
        error: function() {
        },
    });
});
</script>