function base_url2(url) {
	return window.location.origin + "/app_starship/" + url;
}

function mostrar_tasa_cambio(id, ver) {


	
	var url = base_url2("index.php/Tasa_cambioController/mostrar_tasa_cambio/" + id);
	$.get(url, function (data) {
		$("#principal").html(data);
		
		//if (ver == 1) {
			//$("#btnSave").hide();
		//}

		$("#pais").val($("#id_pais").val());
		$("#pais").change();
		
		//$("#nombre_pais").val($("#nombre_pais").val());
		//$("#nombre_pais").change();

		$("#nombre_moneda").val($('#nombre_moneda').val());
		$("#nombre_moneda").change();
		
		$("#tasa_de_cambio").val($('#tasa_de_cambio').val());
		$("#tasa_de_cambio").change();

	});
}

function guardar_tasa_cambio(){

		//console.log($("#id_tasa1").val());
		//var validar = validacion();
		var formData;
		//console.log(validar);

		url_destino = "index.php/Tasa_cambioController/guardar/";
		formData = new FormData($(".add_tasa")[0]);

		//if (!validar==false){
			$.ajax({
				url: base_url2(url_destino),
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					$("#loader").css("display", "block");
				},
				success: function (data) {
				
					alertify.set("notifier", "position", "top-right");
					alertify.success("La tasa de cambio ha sigo guardada correctamente.");
					listar_tasa_cambio();
					
				},
				complete: function () {
					$("#loader").css("display", "none");
				}
			});
		/*}else{
			alert('error');
		}*/
	
}

function validacion(){
	
        if($("#id_pais").val() == null || $("#id_pais").val() == 0 ){
            $("#id_pais").css("border", "1px solid red");
            $("#id_pais").focus();
            return false;
        }
        if($("#nombre_pais").val() == null || $("#nombre_pais").val() == 0 ){
            $("#nombre_pais").css("border", "1px solid red");
            $("#nombre_pais").focus();
            return false;
        }
        if($("#nombre_moneda").val() == null || $("#nombre_moneda").val() == 0 ){
            $("#nombre_moneda").css("border", "1px solid red");
            $("#nombre_moneda").focus();
            return false;
        }
        if($("#tasa_de_cambio").val() == null || $("#tasa_de_cambio").val() == 0 ){
            $("#tasa_de_cambio").css("border", "1px solid red");
            $("#tasa_de_cambio").focus();
            return false;
        }
        
        return true;

}

function eliminar_tasa_cambio(id_tasa) {
	alertify.confirm(
			"¿Está seguro(a) de eliminar esta tasa de cambio?",
			function () {
				var url = base_url2(
					"index.php/Tasa_cambioController/eliminar_tasa_cambio/" + id_tasa
				);

				
				$.get(url, function (data) {
					alertify.set("notifier", "position", "top-right");
					alertify.success("La tasa de cambio ha sido eliminada correctamente");
					
					listar_tasa_cambio();
				});
			},
			function () {
				alertify.error("Cancel");
			}
		)
		.set({ title: "Tasa de Cambio" })
		.set({ labels: { ok: "Aceptar", cancel: "Cancelar" } });

}

