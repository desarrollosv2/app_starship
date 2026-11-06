function base_url(url) {
	return window.location.origin + "/app_starship/" + url;
}

function consulta_track() {
	document.getElementById('delivery-loader').style.display = 'flex';
	var tracking = $("#tracking-number").val();
	var url = base_url("index.php/Consulta_track_Controller/consulta_track");
	$.get(url, function (data) {
		$("#principal").html(data);
		document.getElementById('delivery-loader').style.display = 'none';
	});
}

function lista_warehouse_consulta(tracking) {
	/** busca los detalles (warehouse de una master) tabla manifiesto */
	//master_number =  master;
	//var tracking = $("#tracking-number").val();
	
	var url = base_url(
		"index.php/Consulta_track_Controller/buscar_tracking_numb/" + tracking
	);

	$.get(url, function (data) {
		document.getElementById("tabla_busqueda_track").innerHTML = data;
		$("#tabla_busqueda_track").html(data);
		$("#tracking-number").val(tracking);
		//$("#id-master").val(id);
		//$("#name_master").val(master);
		document.getElementById("tracking-number").innerHTML = tracking;
	});
}

// Funcion para buscar cualquier tipo de dato desde un formulario
function buscar_tracking() {

	document.getElementById('delivery-loader').style.display = 'flex';
	var tracking = $("#tracking-number").val();


	var url = base_url(
		"index.php/Consulta_track_Controller/buscar_tracking_numb/" + tracking
	);
	$.get(url, {
		tipo: $("#tipo_busqueda").val(),
		
	}, function (data) {
		document.getElementById("tabla_busqueda_track").innerHTML = data;
		$("#tabla_busqueda_track").html(data); //$("Nombre segun el id del tbody").html(data);

		const checkbox = document.getElementById("miCheckbox");
		if (checkbox.checked) {
			document.getElementById("createGuide").click();
			//document.getElementById("makeGuide").click();
			
		} else {
			//alert("Apagado");
		}

		document.getElementById('delivery-loader').style.display = 'none';
	});
}

function ver_agencias_consulta(id, dui, correo, telefono, direccion) {
	//alert(id);
	$("#Modal_asignar_agencia").modal("show");
	$("#direccion").val("");
	$("#id-paquete").val(id);
	$("#dui").val(dui);
	$("#correo").val(correo);
	$("#telefono").val(telefono);
	$("#direccion").val(direccion);
	$("#tipo_entrega").val("NRML");
	$("#tipo_servicio").val("CCE");
}

// guarda DUI, direccion, y agencia
function update_info_consulta() {
	var id = $("#id-paquete").val();
	url_destino = "index.php/WhController/update_info";
	formData = new FormData($(".add_agencia")[0]);

	$.ajax({
		url: base_url(url_destino),
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		beforeSend: function () {
			$("#loader").css("display", "block");
		},
		success: function (data) {
			$(".modal-backdrop").remove();
			$("#Modal_asignar_agencia").modal("hide");
			alertify.set("notifier", "position", "top-right");
			alertify.success("Los cambios han sido guardados correctamente");
			lista_warehouse_consulta($("#tracking-number").val());
		},
		complete: function () {
			$("#loader").css("display", "none");
		},
	});
}

function crear_guia_express_consulta() {
	//este IF actualiza un GUIA YA CREADA
	if ($("#actualizarDatos").is(":checked")) {
		id = $("#order_number").val();
		var formData;
		url_destino = "index.php/WhController/update_guia/" + id;
		formData = new FormData($(".guia_ex")[0]);

		var message = "";
		$.ajax({
			url: base_url(url_destino),
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,

			success: function (data) {

				alert(data);


			},
			error: function () {
				message = $("<span class='error'>Ha ocurrido un error.</span>");
				showMessage(message);
			},
		});
	} else {
		id = $("#order_number").val();
		var formData;
		url_destino = "index.php/WhController/obtener_guia/" + id;
		formData = new FormData($(".guia_ex")[0]);

		var message = "";
		$.ajax({
			url: base_url(url_destino),
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,

			success: function (data) {
				console.log(data)
				var obj = JSON.parse(data);
				mensaje = obj.mensaje;
				recolecta = obj.recolecta;
				guia = obj.guias[0].guia;

				$.each(obj, function (index, item) {});

				$(".modal-backdrop").remove();
				$("#guia_express_modal").modal("hide");
				$("#info_modal_express").modal("show");
				$("#mensaje").text(mensaje);
				$("#numero-recolecta").text("RECOLECTA: " + recolecta);
				$("#numero-guia").text("GUIA: " + guia);

				var url = base_url(
					"index.php/WarehouseController/guardar_estatus_RCD/" + guia
				);
				$.get(url, function (data) {});
				update_tracking_id_consulta($("#order_number").val(), recolecta, guia);
				lista_warehouse_consulta($("#tracking-number").val());

				//new code Nico

				pdf_guia_xpress($("#order_number").val(), guia, guia)


			},
			error: function () {
				message = $("<span class='error'>Ha ocurrido un error.</span>");
				showMessage(message);
			},
		});
	}
}

function update_tracking_id_consulta(id, recolecta, guia) {
	var url = base_url(
		"index.php/WhController/update_tracking_id/" +
			id +
			"/" +
			recolecta +
			"/" +
			guia
	);
	$.post(url, function (data) {
		if (data == 1) {
			lista_warehouse_consulta($("#tracking-number").val());
		} else {
			document.getElementById("error_resp").innerHTML =
				"Error: proceso no se completó correctamente. anote la recoleta y la guia para asociarla al siguinte ID: " +
				id;
		}
	});
}

function reemplazar_tracking_consulta(id) {
	var tracking_number = $("#tracking_number").val();
	var tracking_replace = $("#tracking_replace").val();
	var formData;
	var url = base_url(
		"index.php/WarehouseController/reemplazar_tracking/" +
			id +
			"/" +
			tracking_number +
			"/" +
			tracking_replace
	);
	formData = new FormData($(".reemplazar_tracking")[0]);
	$.ajax({
		url: url,
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		beforeSend: function () {
			// Show image container
			$("#loader").css("display", "block");
		},
		success: function (response) {
			$(".modal-backdrop").remove();
			$("#modal_reemplace").modal("hide");
			lista_warehouse_consulta($("#tracking-number").val());
			alertify.set("notifier", "position", "top-right");
			alertify.success("Los cambios han sido guardados correctamente");
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});
}

function comentario(comentario) {
	alert(comentario);
}
