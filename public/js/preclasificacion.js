function base_url(url) {
	return window.location.origin + "/app_starship/" + url;
}

function guardar_manifiesto() {
	var formData;

	url_destino = "index.php/PreclasificacionController/guardar_manifiesto/";
	formData = new FormData($(".add_manifiesto")[0]);

	var message = "";
	$.ajax({
		url: base_url(url_destino),
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		beforeSend: function () {
			// Show image container
			$("#loader").css("display", "block");
		},
		success: function (data) {
			alertify.set("notifier", "position", "top-right");
			alertify.success("Referencia ha sigo guardada correctamente");

			$("#descripcion").val("");
			$("#paquetes").val("");
			$("#sacos").val("");
			$("#referencia").val("");
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});
}

function preclasifica_lista() {
	var url = base_url("index.php/PreclasificacionController/pre_clasificacion");
	$.post(url, function (data) {
		$("#principal").html(data);
	});
}

function mostrar_referencia() {
	$("#ModalAdd_Inventario").on("show.bs.modal", function (e) {
		var bookId = $(e.relatedTarget).data("book-id");
		var bookId1 = $(e.relatedTarget).data("book-id1");
		var bookId2 = $(e.relatedTarget).data("book-id2");

		$(e.currentTarget).find('input[name="id"]').val(bookId);
		$(e.currentTarget).find('input[name="manifiesto"]').val(bookId1);
		$(e.currentTarget).find('input[name="referencia"]').val(bookId2);

		$("#id").val(bookId);
		$("#manifiesto").val(bookId1);
		$("#referencia").val(bookId2);

		//document.ready = document.getElementById("paises").value=bookId15; esto tambien funciona
	});
}

function mostrar_poliza() {
	$("#Modal_Add_Poliza").on("show.bs.modal", function (e) {
		var bookId = $(e.relatedTarget).data("book-id");
		var bookId1 = $(e.relatedTarget).data("book-id1");

		$(e.currentTarget).find('input[name="manifiesto"]').val(bookId);
		$(e.currentTarget).find('input[name="referencia"]').val(bookId1);

		$("#id").val(bookId);
		$("#manifiesto").val(bookId);
		$("#referencia").val(bookId1);

		//document.ready = document.getElementById("paises").value=bookId15; esto tambien funciona
	});
}

function guardar_guia() {
	var formData;
	var valido = "S";
	url_destino = "index.php/PreclasificacionController/guardar_guia/";
	formData = new FormData($(".add_guia")[0]);
	if (valido == "S") {
		var message = "";
		$.ajax({
			url: base_url(url_destino),
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function () {
				// Show image container
				$("#loader").css("display", "block");
			},
			success: function (data) {
				console.log(data);

				if (data >= 1) {
					var mf = $("#manifiesto").val();
					alertify.set("notifier", "position", "top-right");
					alertify.error("Warehouse ya asignado al manifiesto : '" + mf + "'");
				} else {
					alertify.set("notifier", "position", "top-right");
					alertify.success("warehouse ha sido guardado correctamente");
					$("#guia").val("");
					$("#cajas").val("");
					$("#idcasillero").val("");
					$("#nombre_destinatario").val("");
					/*var elemento = document.getElementById("guia");
                    elemento.focus();*/
					// location.reload();
				}
			},
			complete: function () {
				// Show image container
				$("#loader").css("display", "none");
			},
		});
	}
}

function lista_guias(id) {
	var url = base_url(
		"index.php/PreclasificacionController/consulta_guias/" + id
	);
	//cargando('contenidoLista');

	$.get(url, function (data) {
		//document.getElementById("listado_guias").innerHTML = data;

		$("#listado_guias").html(data);
	});
}

function consulta_guias(id) {
	var formData;
	var valido = "S";
	url_destino = "index.php/PreclasificacionController/consulta_guias/" + id;
	formData = new FormData($(".lguias")[0]);

	if (valido == "S") {
		var message = "";
		//hacemos la petición ajax
		$.ajax({
			url: base_url(url_destino),
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function () {
				// Show image container
				$("#loader").css("display", "block");
			},
			success: function (data) {
				$("#modal_guias").modal("show");
				$("#listado_guias").html(data);
			},
			complete: function () {
				// Show image container
				$("#loader").css("display", "none");
			},
			error: function () {
				message = $("<span class='error'>Ha ocurrido un error.</span>");
				showMessage(message);
			},
		});
	}
}

function confirmar_eliminar_guia(id, ref) {
	alertify
		.confirm(
			"¿Esta seguro de eliminar esta guia?",
			function () {
				var numero_referencia = ref;
				var url = base_url(
					"index.php/PreclasificacionController/eliminar_guia/" + id
				);

				$.get(url, function (data) {
					alertify.set("notifier", "position", "top-right");
					alertify.success("Guia ha sido eliminada correctamente");
					//lista_guias(id_guia);
					//lista_guias(numero_referencia);
					$(".modal-backdrop").remove();
					$("#modal_guias .close").click();
				});
			},
			function () {
				alertify.error("Cancel");
			}
		)
		.set({
			title: "Eliminar  guía",
		})
		.set({
			labels: {
				ok: "Aceptar",
				cancel: "Cancelar",
			},
		});
}

function confirmar_eliminar_referencia(id) {
	alertify
		.confirm(
			"¿Esta seguro de eliminar esta referencia?",
			function () {
				//var numero_referencia=ref;
				var url = base_url(
					"index.php/PreclasificacionController/eliminar_referencia/" + id
				);
				$.get(url, function (data) {
					alertify.set("notifier", "position", "top-right");
					alertify.success("Referencia ha sido eliminada correctamente");
					//lista_guias(id_guia);
					//lista_guias(numero_referencia);
					var url = base_url("index.php/InventarioController/inventario");
					$.get(url, function (data) {
						$("#principal").html(data);
					});
				});
			},
			function () {
				alertify.error("Cancel");
			}
		)
		.set({
			title: "Eliminar  Referencia",
		})
		.set({
			labels: {
				ok: "Aceptar",
				cancel: "Cancelar",
			},
		});
}

function procesando_paquetes(id) {
	alertify
		.confirm(
			"¿Esta seguro de eliminar esta referencia?",
			function () {
				//var numero_referencia=ref;
				var url = base_url(
					"index.php/PreclasificacionController/procesar_p/" + id
				);
				$.get(url, function (data) {
					alertify.set("notifier", "position", "top-right");
					alertify.success("Referencia ha sido eliminada correctamente");
					//lista_guias(id_guia);
					//lista_guias(numero_referencia);
				});
			},
			function () {
				alertify.error("Cancel");
			}
		)
		.set({
			title: "Eliminar  Referencia",
		})
		.set({
			labels: {
				ok: "Aceptar",
				cancel: "Cancelar",
			},
		});
}

function verifica() {
	consulta_guia_master();
}

function consulta_guia_master() {
	$(document).ready(function () {
		//  $("#guia_master").blur(function() {
		//  var guia = $("#guia_master").val();
		var guia = $("#id-master-awb").val();

		var url = base_url(
			"index.php/PreclasificacionController/consulta_guia_master/" + guia
		);

		$.get(url, function (data) {
			$("#lista_guia_master").html(data);
		});
		// });
	});
}

function update_warehouse(wh, st) {
	var guiamaster = $("#id-master-awb").val();

	var url = base_url(
		"index.php/PreclasificacionController/consulta_guia/" +
			wh +
			"/" +
			guiamaster
	);


   
	$.get(url, function (data) {
        

		if (Boolean(data.numero_warehouse)) {
			var url = base_url(
				"index.php/PreclasificacionController/update_guia/" +
					wh +
					"/" +
					guiamaster
			);

			$.get(url, function (data) {
				var guia = $("#id-master-awb").val();
				var url = base_url(
					"index.php/PreclasificacionController/consulta_guia_master/" + guia
				);
				$.get(url, function (data) {
					$("#lista_guia_master").html(data);
					$("#search").val("");
                    $("#referencia").val(wh);
					var elemento = document.getElementById("search");
					elemento.focus();
					alertify.set("notifier", "position", "top-right");
					alertify.success("Warehouse registrado");
				});
			});
		} else {
              $("#referencia").val(wh);
			alertify.warning("¡Número de warehouse no encontrado, favor verifique!");

			const audio = new Audio(base_url("assets/notfound01.mp3"));
			audio.play().catch(() => {
				const btn = document.createElement("button");
				btn.onclick = () => audio.play();
				document.body.appendChild(btn);
				btn.click();
				document.body.removeChild(btn);
			});

			return false;
		}
	});
}
function pdf_clasificado() {
	var guia_master = $("#id-master-awb").val();
	var manifiesto = $("#manifiesto-number").val();

	var url = base_url(
		"index.php/PreclasificacionController/pdf_clasificado/" +
			guia_master +
			"/" +
			manifiesto
	);

	$.get(url, function (data) {
		ver_pdf_retaceo(guia_master);
	});
}

function ver_pdf_retaceo(ruta) {
	var url = ruta;
	window.open(
		base_url(url + ".pdf"),
		"ventana1",
		"width=600,height=600,scrollbars=no,toolbar=no, titlebar=no, menubar=no"
	);
}

function guardar_poliza() {
	var formData;
	var valido = "S";
	url_destino = "index.php/PreclasificacionController/guardar_poliza/";
	formData = new FormData($(".add_poliza")[0]);
	if (valido == "S") {
		var message = "";
		$.ajax({
			url: base_url(url_destino),
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function () {
				// Show image container
				$("#loader").css("display", "block");
			},
			success: function (data) {
				alertify.set("notifier", "Poliza", "top-right");
				alertify.success("Póliza ha sigo guardada correctamente");
				$(".modal-backdrop").remove();
				$("#Modal_Add_Poliza").modal("hide");

				preclasifica_lista();
			},
			complete: function () {
				// Show image container
				$("#loader").css("display", "none");
			},
		});
	}
}

function buscar_warehouse_wd(guia) {
	var url = base_url(
		"index.php/PreclasificacionController/buscar_warehouse_wd/" + guia
	);
	$.get(url, function (data) {
		$("#idcasillero").val(data.casillero);
		$("#nombre_destinatario").val(data.nombre_destinatario);
	});
}

function guia_master_Off() {
	$("#master").show();
}

function cerrar_div() {
	$("#master").hide();
	$("#tabla").hide();
}

function traspaso_automatico(guia) {
	alertify
		.confirm(
			"¿Esta seguro de crear manifiesto?",
			function () {
				var formData;
				var valido = "S";
				url_destino =
					"index.php/PreclasificacionController/consulta_guia_master_auto/" +
					guia;
				formData = new FormData($(".lguias")[0]);

				var message = "";
				//hacemos la petición ajax
				$.ajax({
					url: base_url(url_destino),
					type: "GET",
					data: formData,
					cache: false,
					contentType: false,
					processData: false,

					success: function (data) {
						alertify.set("notifier", "position", "top-right");
						alertify.success("Manifiesto  ha sido creado correctamente");
						lista_awb();
					},
				});
			},
			function () {
				alertify.error("Cancel");
			}
		)
		.set({
			title: "CREAR TRASPASO",
		})
		.set({
			labels: {
				ok: "Aceptar",
				cancel: "Cancelar",
			},
		});
}
