function base_url(url) {
	return window.location.origin + "/app_starship/" + url;
}

function consulta_warehouse() {
	var formData;
	var valido = "S";
	url_destino = "index.php/WarehouseController/consulta_warehouse";
	formData = new FormData($(".qry_warehouse")[0]);
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
				$("#tabla_estatus").html(data);
				//	document.getElementById("tabla_estatus").innerHTML = data;
			},
			complete: function () {
				// Show image container
				$("#loader").css("display", "none");
			},
			//si ha ocurrido un error
			error: function () {
				message = $("<span class='error'>Ha ocurrido un error.</span>");
				//showMessage(message);
			},
		});
	}
}
//verifica si exite warehouse en clasificacion de paquetes
function verificar_warehouse() {
	if ($("#estatus").val() == "") {
		$("#msg").fadeIn("slow");
		$("#msg_alert").fadeIn("slow");

		document.getElementById("msg_alert").innerHTML =
			"Debe seleccionar un estatus, para poder continuar";
		cerrar_alert();
		return false;
	}
	if ($("#search").val() == "") {
		$("#msg").fadeIn("slow");
		$("#msg_alert").fadeIn("slow");

		document.getElementById("msg_alert").innerHTML =
			"Debe ingresar un número de Warehouse, para poder continuar";
		cerrar_alert();
		return false;
	}

	var warehouse = $("#search").val();
	var estatus = $("#estatus").val();
	var nombre_retiro = $("#n_retiro").val();

	var casillero = 100; //data.casillero;
	url_destino =
		"index.php/WarehouseController/guardar_estatus/" +
		warehouse +
		"/" +
		estatus;
	var str = $('select[name="estatus"] option:selected').text();
	str = str.trim();
	nombre_estatus = str.substring(4, str.length);

	const nuevaStr = nombre_estatus.replace("-", "");
	nombre_estatus = nuevaStr;

	$.ajax({
		url: base_url(url_destino),
		method: "POST",
		dataType: "json",
		data: {
			n_warehouse: warehouse,
			n_casillero: casillero,
			n_estatus: estatus,
			nombreestatus: nombre_estatus,
			n_retiro: nombre_retiro,
		},
		beforeSend: function () {
			// Show image container
			$("#loader").css("display", "block");
		},
		success: function (data) {
			alertify.set("notifier", "position", "top-right");
			alertify.success("Estatus ha sido guardado correctamente");
			$("#search").val("");
			var elemento = document.getElementById("search");
			elemento.focus();
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	})
		//})
		//   .done(function () { })
		.fail(function () {
			alertify.set("notifier", "position", "top-right");
			alertify.success("Estatus ha sido guardado correctamente");
			return false;
		});
}

/*Envio de correo DONDE ESTA LA PEPSI*/

function send_mail(body, mail) {
	var url = base_url("index.php/SendController/sendmail/" + body + "/" + mail);
	$.get(url, function (data) {
		//$("#principal").html(data);
	});
}
/*End envio de correo*/

function enviar_email(id) {
	var formData;
	url_destino = "index.php/SaldosController/sendmail/" + id;
	formData = new FormData($(".up_saldos")[0]);

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
		success: function (result) {
			$("#message").html(
				'<div class="alert alert-success alertgreen "><button type="button" class="close">x</button><strong>Correo electrónico ha sido enviado correctamente</strong></div>'
			);

			window.setTimeout(function () {
				$(".alert")
					.fadeTo(500, 0)
					.slideUp(500, function () {
						$(this).remove();
					});
			}, 5000);
			$(".alert .close").on("click", function (e) {
				$(this).parent().fadeTo(500, 0).slideUp(500);
			});
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});
}

function cerrar_alert() {
	window.setTimeout(function () {
		$("#msg").hide();
		$("#msg_alert").hide();
	}, 2000);
}

function upload_fssile() {
	var url = base_url("index.php/WarehouseController/upload_file/");
	$.ajax({
		url: url,
		data: $("form").serialize(),
		type: "POST",
		success: function (response) {
			if (response > 0) {
			} else {
			}
		},
		error: function (error) {
			//$.notify("Error al intentar guardar ", "warning");
		},
	});
}

function upload_file() {
	var formData;
	var url = base_url("index.php/WarehouseController/upload_file/");
	formData = new FormData($(".upwh")[0]);
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
			console.log(response);
			$(".modal-backdrop").remove();
			$("#upfileModal").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 5000);
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});
}

function upload_file_facturacion() {
	var formData;
	var url = base_url("index.php/WarehouseController/upload_file_facturacion/");
	formData = new FormData($(".upwh")[0]);
	$.ajax({
		url: url,
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		success: function (response) {
			$(".modal-backdrop").remove();
			$("#upfileModal").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 5000);
		},
	});
}

function listado_warehouse() {
	var url = base_url("index.php/WhController/listado_warehouse");
	$.get(url, function (data) {
		document.getElementById("contenidoLista").innerHTML = data;
	});
}

function consulta_wh() {
	var campo = "";
	var args = $("#qry_warehouse").val();
	args = args.trim();
	console.log(args);

	//args=args.replace("%20", " ");
	//args=utf8_encode(args);
	if ($("#qry_warehouse").val().length < 1) {
		alertify.error("Introduzca patrón de busqueda");
	}
	cadena1 = $("#qry_warehouse").val();
	cadena = cadena1.toUpperCase().trim();
	if (cadena.substr(0, 3) == "SAL") {
		campo = "cuenta";
	} else {
		campo = "idwarehouse";
	}

	var url = base_url(
		"index.php/WhController/consulta_warehouse/" + campo + "/" + args
	);
	$.get(url, function (data) {
		$("#table_wh").html(data);
		//document.getElementById("table_wh").innerHTML = data;
	});
}

function filtro_manifiesto() {
	$("#modal_filtro_manifiesto").modal("show");
}

function manifiesto_listado() {
	if (!$("#input_filtro").val().length) {
		alertify.error("Introduzca patrón de busqueda");
		return false;
	}
	var opc = 0;
	if ($("#rb-guia").is(":checked")) {
		opc = 1;
	} else {
		opc = 2;
	}

	//alert($("#input_filtro").val());
	$(".modal-backdrop").remove();
	$("#modal_filtro_manifiesto").modal("hide");
	var formData;
	url_destino =
		"index.php/WhController/manifiesto_listado" +
		"/" +
		$("#input_filtro").val() +
		"/" +
		opc;

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
			$("#principal").html(data);
		},
		complete: function () {
			$("#loader").css("display", "none");
		},

		error: function () {
			message = $("<span class='error'>Ha ocurrido un error.</span>");
		},
	});
}

function consulta_awb() {
	/** busca en encabezados tabla preclasificacion */
	inicio = $("#fecha-inicio").val();
	fin = $("#fecha-fin").val();
	opcion = $("#opciones").val();
	buscar = $("#buscar-manifiesto").val();
	if (!inicio) {
		inicio = "0";
	}
	if (!fin) {
		fin = "0";
	}
	if (!buscar) {
		buscar = "0";
	}

	var url = base_url(
		"index.php/WhController/consulta_awb/" +
			inicio +
			"/" +
			fin +
			"/" +
			opcion +
			"/" +
			buscar
	);
	$.get(url, function (data) {
		$("#awb_list").html(data);
	});
}

function lista_warehouse(id, master, correla) {

	document.getElementById('delivery-loader').style.display = 'flex';
	//   alert(id+" "+master);
	//alert("miercoles");
	/** busca los detalles (warehouse de una master) tabla manifiesto */
	master_number = master;
	//master_number = "AWB: " + master;
	var url = base_url(
		"index.php/WhController/manifiesto/" + id + "/" + master + "/" + correla
	);

	$.get(url, function (data) {
		$("#principal").html(data);
		$("#master-number").val(master_number);
		$("#id-master").val(id);
		$("#name_master").val(master);
		$("#correla").val(correla);
		document.getElementById("master-number").innerHTML = master_number;

		

		//$("#loader").show();

		$('#tbl_detalle').DataTable({
			dom: 'Bfrtip',
			paging: false,
			buttons: [
				{
					extend: 'csv',
					text: '<i class="fas fa-file-csv"></i>'
				},
				{
					extend: 'excel',
					text: '<i class="fas fa-file-excel"></i>'
				}
			],
			initComplete: function () {
				document.getElementById('delivery-loader').style.display = 'none';
			}
		});
	});
}

function lista_warehouse_confirma(id, master, correla) {
	//  alert(id+" "+master)
	//alert("miercoles");
	/** busca los detalles (warehouse de una master) tabla manifiesto */
	master_number = master;
	//master_number = "AWB: " + master;
	var url = base_url(
		"index.php/WhController/manifiesto_confirma/" +
			id +
			"/" +
			master +
			"/" +
			correla
	);

	$.get(url, function (data) {
		$("#principal").html(data);
		$("#master-number").val(master_number);
		$("#id-master").val(id);
		$("#name_master").val(master);
		document.getElementById("master-number").innerHTML = master_number;
	});
}

function lista_warehouseexport() {
	//alert("miercoles");
	/** busca los detalles (warehouse de una master) tabla manifiesto */

	document.getElementById('delivery-loader').style.display = 'flex';
	var id = 0;
	id = $("#id-master").val();
	//master_number = "AWB: " + master;
	var master = $("#name_master").val();
	var url = base_url(
		"index.php/WhController/manifiesto_export/" + id + "/" + master
	);
	$.get(url, function (data) {
		$("#principal").html(data);

		document.getElementById('delivery-loader').style.display = 'none';
		//$("#master-number").val(master_number);
		//$("#id-master").val(id);
		//$("#name_master").val(master);
		//document.getElementById("master-number").innerHTML = master_number;
	});
}

// SEGMENTO PARA LLAMAR LA  CREACION DEL  PDF
function lista_warehouse1() {
	/** busca los detalles (warehouse de una master) tabla manifiesto */
	var id = 0;
	id = $("#id-master").val();
	referencia = $("#ref_number").val();

	var url = base_url(
		"index.php/WhController/generar_manifiesto/" + id + "/" + referencia
	);

	$.get(url, function (data) {
		var datos = JSON.parse(data);
		var destino = datos["destino"];
		var nombre_archivo = datos["nombre_archivo"];
		var ruta_manifiesto = destino + nombre_archivo;

		console.log(ruta_manifiesto)

		ver_report_comprobantePDF(ruta_manifiesto);
	});
}

function clasificar_master(id,master,d=0){
	document.getElementById('delivery-loader').style.display = 'flex';

	var url = base_url(
		"index.php/WarehouseController/proceso_clasificacion_ma/" + id
	);
	$.get(url, function (data) {
		//$("#principal").html(data);
		document.getElementById('delivery-loader').style.display = 'none';

		lista_warehouseexport(); //to refresh
		
	});
}

function proratear_master(id) {
	document.getElementById('delivery-loader').style.display = 'flex';

	var flete = $("#txtFleteRecibido").val();
	var peso = $("#txtPesoRecibido").val();

	var url = base_url(
		"index.php/WarehouseController/prorateo_manifiesto_gt/" + id
	);
	$.get(url, { peso: peso, flete: flete }, function (data) {
		//$("#principal").html(data);
		document.getElementById('delivery-loader').style.display = 'none';

		lista_warehouseexport(); //to refresh

	});
}

function lista_warehouse_individual() {
	/** busca los detalles (warehouse de una master) tabla manifiesto */
	var id = 0;
	id = $("#id-master").val();
	referencia = $("#ref_number").val();

	var url = base_url(
		"index.php/WhController/generar_manifiesto_individual/" + id
	);

	$.get(url, function (data) {
		var datos = JSON.parse(data);

		var destino = datos["destino"];
		var nombre_archivo = datos["nombre_archivo"];
		var ruta_manifiesto = destino + nombre_archivo;

		descargarArchivoZIP(ruta_manifiesto);
	});
}

function generar_zip_facturas_x_lote() {

	document.getElementById('delivery-loader').style.display = 'flex';

	var id = 0;
	id = $("#id-master").val();
	referencia = $("#ref_number").val();

	var url = base_url(
		"index.php/WarehouseController/generar_manifiesto_pdf_x_lote/" + id
	);

	$.get(url, function (data) {
		var datos = JSON.parse(data);
		var destino = datos["destino"];
		var nombre_archivo = datos["nombre_archivo"];
		var ruta_manifiesto = destino + nombre_archivo;

		descargarArchivoZIP(ruta_manifiesto);

		document.getElementById('delivery-loader').style.display = 'none';


	});



}


function generar_zip_facturas() {
	
	document.getElementById('delivery-loader').style.display = 'flex';

	var id = 0;
	id = $("#id-master").val();
	referencia = $("#ref_number").val();

	var url = base_url(
		"index.php/WarehouseController/generar_manifiesto_pdf_new/" + id
	);

	$.get(url, function (data) {
		var datos = JSON.parse(data);
		var destino = datos["destino"];
		var nombre_archivo = datos["nombre_archivo"];
		var ruta_manifiesto = destino + nombre_archivo;

		descargarArchivoZIP(ruta_manifiesto);

		document.getElementById('delivery-loader').style.display = 'none';


	});


	
}

function uploadCostosDuca(idpreclasificacion) {

	$("#modalCargarDuca").modal("show");
	$("#idpreclasificacioncostos").val(idpreclasificacion);

}




function descargarArchivoZIP(ruta) {
	var url = base_url(ruta);
	var a = document.createElement("a");
	a.href = url;
	a.download = ruta.split("/").pop(); // Extrae el nombre del archivo
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}

function lista_pendientes_confirmar() {
	var url = base_url("index.php/WhController/lista_pendientes_confirmar");
	$.get(url, function (data) {
		$("#principal").html(data);
		$("#opcion").val(0);
		$("td:nth-child(20)").hide();
		//    $('td:nth-child(19),th:nth-child(19)').hide(); //para ocultar encabezado y detalle
		document.getElementById("titulo").innerHTML =
			"Paquetes pendientes de confirmar entrega";
	});
}

function lista_confirmados() {
	var url = base_url("index.php/WhController/lista_awb_confirma");
	$.get(url, function (data) {
		$("#principal").html(data);
		var url = base_url("index.php/WhController/lista_awb_first_confirma");
		$.get(url, function (data) {
			$("#awb_list").html(data);
		});
	});

	//var url = base_url("index.php/WhController/lista_confirmados");
	// $.get(url, function(data) {
	//    $("#principal").html(data);
	//   $("#opcion").val(1);
	//  $('td:nth-child(19)').hide(); //oculata columna de conversacion
	//    document.getElementById("titulo").innerHTML = "Paquetes pendientes de facturar";
	// });
}

function modalCargarClient(idpreclasificacion) {
	$("#modalCargarClient").modal("show");
	$("#idpreclasificacionclient").val(idpreclasificacion);
}


function changeClientId() {


	var formData;
	var idpreclasificacion = 0;
	idpreclasificacion = $("#idpreclasificacionclient").val();

	alert(idpreclasificacion);

	var url = base_url("index.php/WarehouseController/procesarTrackings/" + idpreclasificacion);
	formData = new FormData($("#frmChangeClientId")[0]);

	$.ajax({

		url: url,
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		beforeSend: function () {
			// Show image container
			//$("#loader").css("display", "block");
		},
		success: function (response) {

			console.log(response);

			$(".modal-backdrop").remove();
			$("#modalCargarClient").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 10000);



		}

	});


}

function uploadDuca() {
	$("#loader").css("display", "block");

	var formData;
	var idpreclasificacion = 0;
	idpreclasificacion = $("#idpreclasificacioncostos").val();

	alert(idpreclasificacion);

	var url = base_url("index.php/WarehouseController/uploadDuca/" + idpreclasificacion);
	formData = new FormData($("#frmUploadDUCA")[0]);

	$.ajax({

		url: url,
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		beforeSend: function () {
			// Show image container
			//$("#loader").css("display", "block");
		},
		success: function (response) {

			console.log(response);

			if(response == "without-almacenaje"){
				alert("Por favor, ingresa el almacenaje");
			}
			
			if(response == "type-error"){
				alert("Por favor, ingresa el tipo de archivo correcto para tu pais.");
			}

			$("#loader").css("display", "none");
			$(".modal-backdrop").remove();
			$("#modalCargarDuca").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 10000);

		}

	});






}



function crear_guia_xpress_bk(id) {
	$("#info_modal_express").modal("show");
}

function crear_guia_xpress(
	id,
	tracking,
	consignee,
	addres,
	depto,
	munic,
	cobro_final,
	comentario
) {
	//muestra formulario para crear guia

	if (comentario != "") {
		Swal.fire({
			title: "Comentario",
			text: comentario,
			icon: "info",
		});
		//alert("Comentario: " + comentario);
	}

	if (cobro_final <= 0) {
		alertify.set("notifier", "position", "top-right");
		alertify.error(
			"No puede crear guias con valores menores o iguales a cero(0)!"
		);
		return false;
	}

	var url = base_url("index.php/Customers/lista_servicios");

	$.get(url, function (data) {
		//alert(data);
		$("#guia_express_modal").modal("show");
		$("#direccion").val(addres);
		document.getElementById("tracking-number").innerHTML =
			"Tracking number: " + tracking;

		$("#order_number").val(id);
		$("#depto").val(depto);
		$("#munic").val(munic);
		$("#listado_servicios").html(data);
		$("#servicio").change(function () {
			//$('#nservi').val($("#servicio option:selected").text());
			//  $('#nservi').val($("#servicio").val());
		});

		catalogos_pais_new();
		$("#lmunicipio").hide();

		const checkbox = document.getElementById("miCheckbox");
		if (checkbox.checked) {
			document.getElementById("makeGuide").click();
		}
	});
}

function guiareffull() {
	var url = base_url(
		"index.php/WarehouseController/guiasxref/" +
			$("#referencia").val() +
			"/" +
			$("#id_manifiesto").val() +
			"/" +
			1
	);
	$.get(url, function (data) {
		$("#referenciasxguias").html(data);
	});
}

function crear_guia_express() {
	//crea la guia atraves de la api
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
			//   alert(data);
			var obj = JSON.parse(data);

			// alert('exito' + obj.exito);
			//lert('guia' + obj.guias.numero);
			mensaje = obj.mensaje;
			recolecta = obj.recolecta;
			//   guia = obj.guias.numero;
			guia = obj.guias[0].guia;
			// $.each(obj, function(index, item) {
			// alert(item.guias.numero);
			// alert(item);
			//});
			//  alert('recolecta' + obj.recolecta);

			$(".modal-backdrop").remove();
			$("#guia_express_modal").modal("hide");
			$("#info_modal_express").modal("show");
			$("#mensaje").text(mensaje);
			$("#numero-recolecta").text("RECOLECTA: " + recolecta);
			$("#numero-guia").text("GUIA: " + guia);

			update_tracking_id($("#order_number").val(), recolecta, guia);
		},
		error: function () {
			message = $("<span class='error'>Ha ocurrido un error.</span>");
			showMessage(message);
		},
	});
}

function update_tracking_id(id, recolecta, guia) {
	//  alert(id);
	//   alert(recolecta);
	//  alert(guia);

	var url = base_url(
		"index.php/WhController/update_tracking_id/" +
			id +
			"/" +
			recolecta +
			"/" +
			guia
	);
	$.post(url, function (data) {
		//  alert(data);
		if (data == 1) {
		} else {
			document.getElementById("error_resp").innerHTML =
				"Error: proceso no se completó correctamente. anote la recoleta y la guia para asociarla al siguinte ID: " +
				id;
		}
	});
}

function catalogos_pais_new() {
	var url = base_url("index.php/WhController/lista_departamentos");

	$.get(url, function (data) {
		$("#listado_departamentos").html(data);

		$("#departamento").change(function () {
			//  $('#nepto').val($("#departamento option:selected").text()); //muestra el nombre
			$("#depto").val($("#departamento").val());
			// $('#nservi').val($("#servicio option:selected").text());
			var url = base_url(
				"index.php/WhController/lista_municipios/" + $("#departamento").val()
			);

			$.get(url, function (data) {
				$("#listado_municipios").html(data);
				$("#municipio").change(function () {
					//   $('#nmunic').val($("#municipio option:selected").text());
					$("#munic").val($("#municipio").val());
				});

				$("#lmunicipio").show();
			});
		});
	});

	// get_municipio(2);
}

function get_municipio(id) {
	var url = base_url("index.php/WhController/lista_municipios/" + id);

	$.get(url, function (data) {
		$("#listado_municipios").html(data);

		$("#lmunicipio").show();
	});
}

function lista_referencia(id, master, ref) {
	/** busca los detalles (warehouse de una master) tabla manifiesto */
	master_number = master;

	var url = base_url(
		"index.php/WhController/lista_referencia/" + id + "/" + ref
	);

	$.get(url, function (data) {
		$("#principal").html(data);
		$("#master-number").val(master_number);
		$("#id-master").val(id);
		$("#name_master").val(master);
		document.getElementById("master-number").innerHTML = master_number;
		$("#catalogo_ref").val(ref);
	});
}

function cerrar_modal() {
	id = $("#id-master").val();
	master = $("#name_master").val();
	$(".modal-backdrop").remove();
	$("#ModalAdd_referencia").modal("hide");
	lista_warehouse(id, master, 0);
}

function modal_referencia() {
	//muestra modal para creacion de referencia
	$("#ModalAdd_referencia").modal("show");
	$("#id-manifiesto").val($("#id-master").val());
}

function modal_add_guias() {
	$("#ModalAdd_guias").modal("show");
	$("#manifiesto_id").val($("#id-master").val());
}

function reference_list() {
	$("#list_reference_modal").modal("show");
	$("#manifiesto_id_ref").val($("#id-master").val());
}

function guardar_referencia() {
	var formData;

	url_destino = "index.php/WhController/guardar_referencia/";
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
			//  $("#loader").css("display", "block");
		},
		success: function (data) {
			alertify.set("notifier", "position", "top-right");
			alertify.success("Referencia ha sigo guardada correctamente");

			$("#descripcion").val("");
			$("#paquetes").val("");
			$("#sacos").val("");
			$("#referencia").val("");
			// $(".modal-backdrop").remove();
			// $("#ModalAdd_referencia").modal("hide");
		},
		complete: function () {
			// Show image container
			//  $("#loader").css("display", "none");
		},
	});
}

function asignar_guia() {
	var formData;

	url_destino = "index.php/WhController/asignar_guia/";
	formData = new FormData($(".add_guia")[0]);

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
			//  $("#loader").css("display", "block");
		},
		success: function (data) {
			id = $("#id-master").val();
			master = $("#name_master").val();
			$(".modal-backdrop").remove();
			$("#ModalAdd_guias").modal("hide");
			lista_warehouse(id, master, 0);
			alertify.set("notifier", "position", "top-right");
			alertify.success("Guia ha sigo asignada correctamente");
		},
		complete: function () {
			// Show image container
			//  $("#loader").css("display", "none");
		},
	});
}

function mostrar_conversacion(id, tracking, consignee) {
	var url = base_url("index.php/WhController/consulta_conversacion/" + id);

	$.get(url, function (data) {
		$("#modal_conversacion").modal("show");
		$("#lconversacion").html(data);
		$("#id-tracking").val(id);
		document.getElementById("tracking-id").innerHTML =
			tracking + " - " + consignee;
	});
}

function add_comentario() {
	$("#form-datos").show();
	$("#chk_aceptado").prop("checked", false);
}

function guardar_comentario() {
	id = $("#id-tracking").val();
	aceptado = 0;
	if ($("#chk_aceptado").prop("checked", true)) {
		aceptado = 1;
	}
	var formData;

	url_destino = "index.php/WhController/guardar_comentario/";
	formData = new FormData($(".add_comenta")[0]);

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
			//  $("#loader").css("display", "block");
		},
		success: function (data) {
			alertify.set("notifier", "position", "top-right");
			alertify.success("Comentario ha sido agregado correctamente");

			$("#descripcion").val("");
			$("#paquetes").val("");
			$("#sacos").val("");
			$("#referencia").val("");
			$(".modal-backdrop").remove();
			$("#modal_conversacion").modal("hide");
			if (aceptado == 1) {
				var url = base_url("index.php/WhController/update_aceptado/" + id);

				$.get(url, function (data) {
					lista_pendientes_confirmar();
				});
			}
		},
		complete: function () {
			// Show image container
			//  $("#loader").css("display", "none");
		},
	});
}

function ocultar_detalle_partida() {
	$("#detalle-partida").hide();
	$("#opc-clasif").val("0");
	$("td:nth-child(3)").show();
	$("th:nth-child(3)").show();
	$("td:nth-child(4)").hide();
	$("th:nth-child(4)").hide();
}

function mostrar_detalle_partida() {
	$("#detalle-partida").show();
	$("#opc-clasif").val("1");

	$("td:nth-child(3)").hide();
	$("th:nth-child(3)").hide();
	$("td:nth-child(4)").show();
	$("th:nth-child(4)").show();
}

function open_modal_multiplicar(id) {
	$("#Modal_multiplicar").modal("show");
	$("#id-item").val(id);
}

function guardar_item_multi() {
	cantidad = $("#cant").val();
	// alert(cantidad);
	if ($("#cant").val().length <= 0) {
		alertify.set("notifier", "position", "top-right");
		alertify.error("Introduzca cantidad. debe ser un valor mayor que cero(0) ");
		return false;
	}

	id = $("#id-item").val();

	url_destino =
		"index.php/WhController/guardar_item_multi/" + id + "/" + cantidad;
	formData = new FormData($(".add_comenta")[0]);

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
			$master = $("#name_master").val();
			$id = $("#id-master").val();

			alertify.set("notifier", "position", "top-right");
			alertify.success("Comentario ha sido agregado correctamente");

			$("#Modal_multiplicar").modal("hide");
			$(".modal-backdrop").remove();

			Alista_warehouse_fast($id, $master, 0);
		},
		complete: function () {
			$("#loader").css("display", "none");
		},
	});
}

function asignar_partida_paquete() {
	partida = $("#partidas").val();

	var filas = $("#tbl_detalle").find("tr");

	for (i = 1; i <= filas.length - 1; i++) {
		var celdas = $(filas[i]).find("td");
		var control = "#chk_d" + $(celdas[0]).text();
		var correla = $(celdas[0]).text();
		if ($("#chk_d" + correla).is(":checked")) {
			asignar_partida($(celdas[0]).text(), partida);
		}
	}
	lista_warehouse($("#id-master").val(), $("#name_master").val(), 0);
}

function asignar_partida(id, partida) {
	var url = base_url(
		"index.php/WhController/asignar_partida/" + id + "/" + partida
	);

	$.get(url, function (data) {});
}

/***update de servicio ***/
function pdf_wh(id, master) {
	master_number = master;

	var url = base_url("index.php/WhController/pdf_wh/" + id + "/" + master);

	$.get(url, function (data) {
		//console.log(data);
		$("#pdf_wh_manifiesto").html(data);
		//$('#id-master-awb').val(id);
	});
	//muestra modal para creacion de referencia
	$("#modal_pdf").modal("show");
	//$("#id-manifiesto").val($("#id-master").val());
}

function cerrar_modal_pdf() {
	$(".modal-backdrop").remove();
	$("#modal_pdf").modal("hide");
}

function pdf_ticket_bk(id, master) {
	var url = base_url("index.php/WhController/pdf_wh2_bk/" + id + "/" + master);
	let name = master + ".pdf";
	$.get(url, function (data) {
		$("#ContPDF").html(data);
		window.jsPDF = window.jspdf.jsPDF;
		var doc = new jsPDF("p", "mm", [100.1, 152.4]);
		var elementHTML = document.querySelector("#contentToPrint");
		doc.html(elementHTML, {
			callback: function (doc) {
				//doc.save(name);
				doc.output("save", name); //Try to save PDF as a file (not works on ie before 10, and some mobile devices)
				//doc.output('datauristring');        //returns the data uri string
				//doc.output('datauri');              //opens the data uri in current window
				doc.output("dataurlnewwindow");
			},
			margin: [10, 10, 10, 10],
			autoPaging: "text",
			x: 0,
			y: 0,
			width: 190,
			windowWidth: 775,
		});
		//console.log(data);
	});
}

function pdf_ticket(id, master, internal) {
	if (internal == 0) {
		alertify.set("notifier", "position", "top-right");
		alertify.error("Aún no tiene guia creada, por favor verifique ");
		return false;
	}

	var url = base_url("index.php/WhController/pdf_wh2/" + id + "/" + master);
	$.get(url, function (data) {
		var datos = JSON.parse(data);
		var directorio = datos["destino"];
		var archivo = datos["nombre_archivo"];
		var documento = directorio + archivo;
		ver_pdf_qr_master(documento);
	});
}

function ver_pdf_qr_master(ruta) {
	var url = ruta;
	window.open(
		base_url(url),
		"ventana1",
		"width=600,height=600,scrollbars=no,toolbar=no, titlebar=no, menubar=no"
	);
}

function lista_servicios_express2(id) {
	//console.log(id);
	var url = base_url("index.php/WhController/departamento");
	$.get(url, function (data) {
		//console.log(data);
		//alert(data);
		$("#guia_express_modal2").modal("show");
		//$("#order_number").val(id);
		$("#listado_dpart").html(data);
		/*$("#servicio").change(function() {
		$('#nservi').val($("#servicio option:selected").text());
		});
		catalogos_pais();
		$('#lmunicipio').hide();*/
		$("#id_manifiesto").val(id);
	});
}
//   funcion para  mostrar el detalle del  manifiesto
function ver_report_comprobantePDF(ruta) {
	var url = ruta;
	//alert("lo que trae el data" + ruta)
	window.open(
		base_url(url),
		"ventana1",
		"width=600,height=600,scrollbars=no,toolbar=no, titlebar=no, menubar=no"
	);
}

function cerrar_info_modal_express() {
	$(".modal-backdrop").remove();
	$("#info_modal_express").modal("hide");
	lista_confirmados();
}

// función para aplicar estatus, que no fueron notificados por webhook en app.c807
function aplicar_estatus(guia) {
	guia = $("#guia").val();
	//	alert(guia);
	var url = base_url("index.php/WhController/aplicar_estatus/" + guia);
	$.get(url, function (data) {
		$("#principal").html(data);
		if (!guia == 0) {
			alert("Proceso finalizado");
		}
	});
}

function ver_agencias(id, dui, correo, telefono, direccion) {
	$("#Modal_asignar_agencia").modal("show");
	$("#id-paquete").val(id);
	$("#dui").val(dui);
	$("#correo").val(correo);
	$("#telefono").val(telefono);
	$("textarea#direccion").text(direccion);
	$("#tipo_entrega").val("NRML");
	$("#tipo_servicio").val("CCE");
	//    alert($("#id-master").val());
}

// guarda DUI, direccion, y agencia
function update_info() {
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
			lista_warehouse(
				$("#id-master").val(),
				$("#master-number").val(),
				$("#numero-guia").val()
			);
			alertify.set("notifier", "position", "top-right");
			alertify.success("Los cambios han sido guardados correctamente");
		},
		complete: function () {
			$("#loader").css("display", "none");
		},
	});
}

function imprimir_referencia() {
	$("#modal_print_referencia").modal("show");
}

function imprimir_facturas() {
	$("#modal_factura").modal("show");
}

function imprimir_referencia_individual() {
	$("#modal_print_referencia_individual").modal("show");
}
//  funcion para  actualizar  datos del  manifiesto    13-10-2023
function upload_fileDUI() {
	var idpreclasificacion = 0;
	idpreclasificacion = $("#idpreclasificacion").val();
	var formData;
	var url = base_url(
		"index.php/WarehouseController/upload_fileDUI/" + idpreclasificacion
	);
	formData = new FormData($(".upwh")[0]);
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
			console.log(response);
			$(".modal-backdrop").remove();
			$("#upfileModal").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 5000);
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});
}

//  funcion para  actualizar  datos del  manifiesto    02-102023
function upload_file1() {
	var idpreclasificacion = 0;
	idpreclasificacion = $("#idpreclasificacion").val();
	var formData;
	var url = base_url(
		"index.php/WarehouseController/upload_fileWH/" + idpreclasificacion
	);
	formData = new FormData($(".upwh")[0]);
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
			// console.log(response);
			$(".modal-backdrop").remove();
			$("#upfileModal").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 5000);
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});
}

function verificar_lm() {
	let id = $("#id-master").val();

	alertify
		.confirm(
			"¿Está seguro de ejecutar este proceso?",
			function () {
				let url = base_url("index.php/WhController/verificar_lm/" + id);

				$.get(url, function (data) {
					// Envuelve lista_warehouse en una promesa
					new Promise(function (resolve) {
						lista_warehouse(
							$("#id-master").val(),
							$("#name_master").val(),
							0,
							resolve
						);
					}).then(function () {
						alertify.set("notifier", "position", "top-right");
						alertify.success("Proceso finalizado");
					});
				});
			},
			function () {
				alertify.error("Cancel");
			}
		)
		.set({ title: "Verificar departamentos y municipios" })
		.set({ labels: { ok: "Aceptar", cancel: "Cancelar" } });
}

function reemplazar(tracking_number) {
	$("#modal_reemplace").modal("show");
	$("#tracking_number").val(tracking_number);
}

function reemplazar_tracking(id) {
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
			document.getElementById("tracking_replace").value = "";
			lista_warehouse($("#id-master").val(), $("#name_master").val(), 0);
			alertify.set("notifier", "position", "top-right");
			alertify.success("Los cambios han sido guardados correctamente");
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});
}
function historial(tracking_number) {
	var formData;
	url_destino = "index.php/WarehouseController/historial/" + tracking_number;
	formData = new FormData($(".historial")[0]);
	//alert(id);

	//hacemos la petición ajax
	$.ajax({
		url: base_url(url_destino),
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		success: function (data) {
			$("#modal_historial").modal("show");
			$("#listado_historial").html(data);
		},
		error: function () {},
	});
}

function cargar_referencia(idpreclasificacion) {
	var url = base_url("index.php/WarehouseController/cargar_referencia/");
	$.get(url, function (data) {
		//$("#principal").html(data);
		//alert(idpreclasificacion);
		$("#upfileModal").modal("show");
		$("#idpreclasificacion").val(idpreclasificacion);
	});
}

function upload_file_referencia() {
	var formData;
	var idpreclasificacion = 0;
	idpreclasificacion = $("#idpreclasificacion").val();

	//alert(idpreclasificacion);
	var url = base_url(
		"index.php/WarehouseController/upload_file_referencia/" + idpreclasificacion
	);
	formData = new FormData($(".up_ref")[0]);
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
			console.log(response);
			$(".modal-backdrop").remove();
			$("#upfileref").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 5000);
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});

	$("#upfileModal").modal("hide");
	$("#msg").show();
}

function reporte_manifiesto(idpreclasificacion) {
	var url = base_url(
		"index.php/WhController/reporte_manifiesto/" + idpreclasificacion
	);
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function reporte_guias(idpreclasificacion) {
	var url = base_url(
		"index.php/WhController/reporte_guias/" + idpreclasificacion
	);

	$("#loader").css("display", "block");
	$.get(url, function (data) {
		ver_report_rep_guiasPDF("/document/reporte_ventas/" + data);
		$("#loader").css("display", "none");
	});
}

function ver_report_rep_guiasPDF(ruta) {
	var url = ruta;
	window.open(
		base_url(url),
		"ventana1",
		"width = 600, height = 600, scrollbars = no, toolbar = no, titlebar = no, menubar = no"
	);
}

function lista_sobrantes(idpreclasificacion) {
	var url = base_url("index.php/WarehouseController/lista_sobrantes/");
	$.get(url, function (data) {
		console.log(idpreclasificacion);
		$("#upfileSoModal").modal("show");
		$("#idpreclasificacion").val(idpreclasificacion);
	});
}

function modificar_manifiesto(idpreclasificacion) {
	$("#modalModificarManifiesto").modal("show");
	$("#idpreclasificacionm").val(idpreclasificacion);
}

function arribo_modal(idpreclasificacion) {
	$("#modalArribo").modal("show");
	$("#id-master").val(idpreclasificacion);
}

function upload_file_sobrante() {
	var formData;
	var idpreclasificacion = 0;
	idpreclasificacion = $("#idpreclasificacion").val();

	//alert(idpreclasificacion);
	var url = base_url(
		"index.php/WarehouseController/upload_file_sobrante/" + idpreclasificacion
	);
	formData = new FormData($(".up_sobras")[0]);
	$.ajax({
		url: url,
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		beforeSend: function () {
			// Show image container
			//$("#loader").css("display", "block");
		},
		success: function (response) {
			console.log(response);
			$(".modal-backdrop").remove();
			$("#upfileSoModal").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 10000);
		},
	});
}

function uploadUpdateManifiesto() {
	var formData;
	var idpreclasificacion = 0;
	idpreclasificacion = $("#idpreclasificacionm").val();

	var url = base_url(
		"index.php/WarehouseController/uploadUpdateManifiesto/" + idpreclasificacion
	);
	formData = new FormData($("#frmUploadUpdateManifiesto")[0]);

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
			console.log(response);

			$(".modal-backdrop").remove();
			$("#modalModificarManifiesto").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 10000);

			$("#loader").css("display", "none");
		},
	});
}

function agregar_manifiestoxref(idpreclasificacion) {
	var url = base_url(
		"index.php/WarehouseController/asignar_ref/" + idpreclasificacion
	);
	$.get(url, function (data) {
		$("#principal").html(data);

		if (idpreclasificacion > 0) {
			$("#id").val(idpreclasificacion);
			document.getElementById("titulo").innerHTML = $("#id-master").val();
		} else {
			document.getElementById("titulo").innerHTML = $("#id-master").val();
		}
	});
}

function listadoxsaco() {
	var url = base_url(
		"index.php/WarehouseController/listadoxsaco/" + $("#bolsas").val()
	);
	$.get(url, function (data) {
		$("#cantidad_guias").html(data);
		//$("#valor_total").html(data);
	});
}

function update_ref_bolsa() {
	var referencia = $("#referencia").val();
	var bolsas = $("#bolsas").val();
	var id = $("#id-manifiesto").val();

	url_destino =
		"index.php/WarehouseController/update_ref_bolsa/" +
		referencia +
		"/" +
		bolsas +
		"/" +
		id;
	formData = new FormData($(".add_referencia_bolsa")[0]);

	$.ajax({
		url: base_url(url_destino),
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		beforeSend: function () {
			// $("#loader").css("display", "block");
		},

		success: function (data) {
			alertify.set("notifier", "position", "top-right");
			alertify.success("Los cambios han sido guardados correctamente");
			console.log(response);
			$(".modal-backdrop").remove();
			$("#add_referencia_bolsa").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 10000);
		},

		complete: function () {
			$("#loader").css("display", "none");
		},
	});
}

function referencia_upd(tracking_number) {
	$("#ModalAsg_referencia").modal("show");
	$("#tracking_numbers").val(tracking_number);
}

function add_reem_referencia(id) {
	var tracking_number = $("#tracking_numbers").val();
	var no_referencia = $("#no_referencia").val();
	var formData;
	var url = base_url(
		"index.php/WarehouseController/add_reem_referencia/" +
			tracking_number +
			"/" +
			no_referencia
	);
	formData = new FormData($(".ModalAsg_referencia")[0]);

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
			lista_warehouse($("#id-master").val(), $("#name_master").val(), 0);
			alertify.set("notifier", "position", "top-right");
			alertify.success("Los cambios han sido guardados correctamente");
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});
}
function update_fechaArribo(e) {
	// si llamas desde onsubmit, pasa el event
	if (e) e.preventDefault();

	const master = $("#id-master").val();
	const form = document.getElementById("frmArribo");
	const formData = new FormData(form); // ← usa el ID correcto

	formData.append("id_master", master);

	const url = base_url("index.php/Gestion/update_fechaArribo/" + master);

	$.ajax({
		url,
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		beforeSend: function () {
			$("#loader").css("display", "block");
		},
		success: function (response) {
			$(".modal-backdrop").remove();
			$("#modalArribo").modal("hide");
			alertify.set("notifier", "position", "top-right");
			alertify.success("fecha de arribo ha sido actualizada");
		},
		complete: function () {
			$("#loader").css("display", "none");
		},
		error: function (xhr) {
			console.error("error:", xhr.responseText);
		},
	});
}

function reload_guia() {
	var url = base_url("index.php/WhController/reload_guia");
	$.get(url, function (data) {
		alert("finalizado");
	});
}

/*add by juanjo*/
function consulta_awb_cla() {
	/** busca en encabezados tabla preclasificacion */
	inicio = $("#fecha-inicio").val();
	fin = $("#fecha-fin").val();
	//opcion = $("#opciones").val();
	opcion = 3;
	//buscar = $("#buscar-manifiesto").val();
	buscar = 0;
	if (!inicio) {
		inicio = "0";
	}
	if (!fin) {
		fin = "0";
	}
	if (!buscar) {
		buscar = "0";
	}

	var url = base_url(
		"index.php/ClasificacionController/consulta_awb/" +
			inicio +
			"/" +
			fin +
			"/" +
			opcion +
			"/" +
			buscar
	);
	$.get(url, function (data) {
		$("#awb_list").html(data);
	});
}

function lista_warehouse_cla(id, master, correla) {
	//alert(id+" "+master)
	//alert("miercoles");
	/** busca los detalles (warehouse de una master) tabla manifiesto */
	master_number = master;
	//master_number = "AWB: " + master;
	var url = base_url(
		"index.php/ClasificacionController/manifiesto/" +
			id +
			"/" +
			master +
			"/" +
			correla
	);

	$.get(url, function (data) {
		$("#principal").html(data);
		$("#master-number").val(master_number);
		$("#id-master").val(id);
		$("#name_master").val(master);
		document.getElementById("master-number").innerHTML = master_number;
	});
}

function lista_warehouseexport_cla() {
	//alert("miercoles");
	/** busca los detalles (warehouse de una master) tabla manifiesto */
	var id = 0;
	id = $("#id-master").val();

	//master_number = "AWB: " + master;
	var master = 0;
	var url = base_url(
		"index.php/clasificacionController/manifiesto_export/" + id + "/" + master
	);
	$.get(url, function (data) {
		$("#principal").html(data);
		//$("#master-number").val(master_number);
		//$("#id-master").val(id);
		//$("#name_master").val(master);
		//document.getElementById("master-number").innerHTML = master_number;
	});
}

function lista_referencia_cla(id, master, ref) {
	/** busca los detalles (warehouse de una master) tabla manifiesto */
	master_number = master;

	var url = base_url(
		"index.php/ClasificacionController/lista_referencia/" + id + "/" + ref
	);

	$.get(url, function (data) {
		$("#principal").html(data);
		$("#master-number").val(master_number);
		$("#id-master").val(id);
		$("#name_master").val(master);
		document.getElementById("master-number").innerHTML = master_number;
		$("#catalogo_ref").val(ref);
	});
}

function cerrar_modal_cla() {
	id = $("#id-master").val();
	master = $("#name_master").val();
	$(".modal-backdrop").remove();
	$("#ModalAdd_referencia").modal("hide");
	lista_warehouse_cla(id, master, 0);
}

function mostrar_detalle_partida_cls() {
	$("#detalle-partida").show();
	$("#opc-clasif").val("1");

	$("td:nth-child(3)").hide();
	$("th:nth-child(3)").hide();

	$("td:nth-child(4)").show();
	$("th:nth-child(4)").show();
}

function guardar_item_multi_clas() {
	cantidad = $("#cant").val();
	// alert(cantidad);
	if ($("#cant").val().length <= 0) {
		alertify.set("notifier", "position", "top-right");
		alertify.error("Introduzca cantidad. debe ser un valor mayor que cero(0) ");
		return false;
	}

	id = $("#id-item").val();

	url_destino =
		"index.php/ClasificacionController/guardar_item_multi/" +
		id +
		"/" +
		cantidad;
	formData = new FormData($(".add_comenta")[0]);

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
			alertify.set("notifier", "position", "top-right");
			alertify.success("Comentario ha sido agregado correctamente");
		},
		complete: function () {
			$("#loader").css("display", "none");
		},
	});
}

function asignar_partida_paquete_cls() {
	//alert();
	partida = $("#partidas").val();

	var filas = $("#tbl_detalle_cl").find("tr");

	for (i = 1; i <= filas.length - 1; i++) {
		var celdas = $(filas[i]).find("td");
		var control = "#chk_d" + $(celdas[0]).text();
		var correla = $(celdas[0]).text();

		if ($("#chk_d" + correla).is(":checked")) {
			//console.log($(celdas[0]).text(),'id');
			asignar_partida_cls($(celdas[0]).text(), partida);
		}
	}
	//console.log(partida,filas,'filas');
	lista_warehouse_cla($("#id-master").val(), $("#name_master").val(), 0);
}

function asignar_partida_cls(id, partida) {
	var url = base_url(
		"index.php/ClasificacionController/asignar_partida/" + id + "/" + partida
	);
	$.get(url, function (data) {});
}
function enviar_tracking() {

	$("#loader").css("display", "block");
	if ($("#sucursal").val() == "") {
		$("#msg").fadeIn("slow");
		$("#msg_alert").fadeIn("slow");

		document.getElementById("msg_alert").innerHTML =
			"Debe seleccionar una sucursal, para poder continuar";
		cerrar_alert();
		return false;
	}

	if ($("#tracking_send").val() == "") {
		$("#msg").fadeIn("slow");
		$("#msg_alert").fadeIn("slow");
		document.getElementById("msg_alert").innerHTML =
			"Debe ingresar un número de Tracking, para poder continuar";
		cerrar_alert();
		return false;
	}

	var warehouse = $("#tracking_send").val();
	var sucursal = $("#sucursal").val();
	var tipo_estatus = $("#tipo_estatus").val();

	var url = base_url(
		"index.php/WarehouseController/enviar_tracking/" +
			warehouse +
			"/" +
			sucursal +
			"/" +
			tipo_estatus
	);

	$.getJSON(url, { wh: warehouse.value }, function (data) {
		//url_destino = "index.php/WarehouseController/enviar_tracking/" + warehouse + "/" + sucursal + "/" + tipo_estatus;
		var str = $('select[name="sucursal"] option:selected').text();
		str = str.trim();

		$.ajax({
			url: base_url(url),
			method: "POST",
			data: {
				n_warehouse: warehouse,
				n_sucursal: sucursal,
			},
			beforeSend: function () {
				// Show image container
				
			},
			success: function (data) {
				//alertify.set("notifier", "position", "top-right");
				//alertify.success("Estatus ha sido guardado correctamente");
				$("#tracking_send").val("");
				var elemento = document.getElementById("tracking_send");
				elemento.focus();
				$("#loader").css("display", "none");
			},
			complete: function () {
				// Show image container
				$("#loader").css("display", "none");
				$("#tracking_send").val("");
			},
		});
	})
		.done(function () {})
		.fail(function () {
			alertify.set("notifier", "position", "top-right");
			alertify.success("El tracking a sido enviado con exito!");
			$("#tracking_send").val("");
			$("#loader").css("display", "none");
			return false;
		});
}

function buscar_fecha_envio_guia() {
	var fecha_desde_envio = $("#fecha_desde_envio").val();
	var fecha_hasta_envio = $("#fecha_hasta_envio").val();

	if (fecha_desde_envio) {
	} else {
		alertify.alert("", "Seleccione la fecha inicial", "");
		return false;
	}

	if (fecha_hasta_envio) {
	} else {
		alertify.alert("", "Seleccione la fecha final", "");
		return false;
	}

	var url = base_url(
		"index.php/WarehouseController/buscar_fecha_envio_guia/" +
			fecha_desde_envio +
			"/" +
			fecha_hasta_envio
	);
	$.get(url, function (data) {
		$("#tabla_envio_guia").html(data); //$("Nombre segun el id del tbody").html(data);
	});
}

function agregar_costosxref(idpreclasificacion) {
	var url = base_url(
		"index.php/WarehouseController/agregar_costosxref/" + idpreclasificacion
	);
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function guiasxref() {
	$("#fullref").prop("checked", false);
	var url = base_url(
		"index.php/WarehouseController/guiasxref/" +
			$("#referencia").val() +
			"/" +
			$("#id_manifiesto").val()
	);

	$.get(url, function (data) {
		$("#referenciasxguias").html(data);
		$("select#tracking_number").change();
	});
}

function valor_guiasxref() {
	var url = base_url(
		"index.php/WarehouseController/valor_guiasxref/" +
			$("#referencia").val() +
			"/" +
			$("#tracking_number").val()
	);
	$.get(url, function (data) {
		$("#valor_guiasxref").html(data);
	});
}

function valor_guiasxref_general() {
	var url = base_url(
		"index.php/WarehouseController/valor_guiasxref_general/" +
			$("#referencia").val() +
			"/" +
			$("#id_manifiesto").val()
	);
	$.get(url, function (data) {
		$("#valor_guiasxref_general").html(data);
	});
}

function guardar_costos() {
	var id_manifiesto = $("#id_manifiesto").val();
	var referencia = $("#referencia").val();
	var tracking_number = $("#tracking_number").val();
	var iva = $("#iva").val();
	var dai = $("#dai").val();

	url_destino =
		"index.php/WarehouseController/guardar_costos/" +
		id_manifiesto +
		"/" +
		referencia +
		"/" +
		tracking_number +
		"/" +
		iva +
		"/" +
		dai;
	formData = new FormData($(".add_referencia_bolsa")[0]);

	$.ajax({
		url: base_url(url_destino),
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,

		beforeSend: function () {
			// $("#loader").css("display", "block");
		},

		success: function (data) {
			valor_guiasxref_general();
			$("#iva").val("0.00");
			$("#dai").val("0.00");
			$("#valor_total").val("");
			alertify.set("notifier", "position", "top-right");
			alertify.success("Los cambios han sido guardados correctamente");

			$(".modal-backdrop").remove();
			$("#add_referencia_bolsa").modal("hide");
			$("#msg").show();
			setTimeout(function () {
				$("#msg").fadeTo("slow", 0.1, function () {
					$("#msg").alert("close");
				});
			}, 10000);
		},

		complete: function () {
			$("#loader").css("display", "none");
			$("select#referencia").change();
		},
	});
}

function estatus_referencias(manifiesto) {
	var url = base_url(
		"index.php/WhController/estatus_referencias/" + manifiesto
	);
	$.get(url, function (data) {
		$("#principal").html(data);
		$("#segmento-aplicar").hide();
		$("#segmento-general").show();
	});
}

function aplicar_estatus_referencia(id, manifiesto, referencia) {
	$("#manifiest_id").val(id);
	$("#manifiest_number").val(manifiesto);
	$("#reference_number").val(referencia);
	$("#enca").html(manifiesto + ": " + referencia);

	var url = base_url(
		"index.php/WhController/aplicar_estatus_referencia/" +
			id +
			"/" +
			manifiesto +
			"/" +
			referencia
	);
	$.get(url, function (data) {
		$("#segmento-aplicar").show();
		$("#segmento-general").hide();
	});
}


async function guardar_estatus_referencias_by_chk(
	id,
	manifiesto,
	referencia,
	id_estatus,
	nombre_estatus,
	fecha_especifica
) {

	const url = base_url(
		"index.php/WhController/guardar_estatus_referencias/" +
		id + "/" +
		manifiesto + "/" +
		referencia + "/" +
		id_estatus + "/" +
		nombre_estatus + "/" +
		fecha_especifica
	);

	try {

		const data = await $.get(url);

		return data;

	} catch (error) {

		console.error(error);

		throw error;

	}

}


function guardar_estatus_referencias() {
	id_estatus = $("#estatus").val();
	nombre_estatus = $('select[name="estatus"] option:selected').text();

	alert("Hola Francy");

	id = $("#manifiest_id").val();
	manifiesto = $("#manifiest_number").val();
	referencia = $("#reference_number").val();

	fecha_especifica = $("#fecha_especifica").val();

	document.getElementById('delivery-loader').style.display = 'flex';


	var url = base_url(
		"index.php/WhController/guardar_estatus_referencias/" +
			id +
			"/" +
			manifiesto +
			"/" +
			referencia +
			"/" +
			id_estatus +
			"/" +
		nombre_estatus +
		"/" +
		fecha_especifica 
	);
	$.get(url, function (data) {
		$("#segmento-aplicar").hide();
		$("#segmento-general").show();
		alertify.set("notifier", "position", "top-right");
		alertify.success("Proceso finalizado");

		document.getElementById('delivery-loader').style.display = 'none';
	});
}

function estatusMaster() {
	id_estatus = $("#estatus").val();
	nombre_estatus = $('select[name="estatus"] option:selected').text();
	id = $("#manifiest_id").val();
	manifiesto = $("#manifiest_number").val();
	referencia = "general";

	var url = base_url(
		"index.php/WhController/guardar_estatus_referencias/" +
			id +
			"/" +
			manifiesto +
			"/" +
			referencia +
			"/" +
			id_estatus +
			"/" +
			nombre_estatus
	);

	// Mostrar el spinner
	$("#spinner-placeholder").show();

	$.get(url, function (data) {
		$(".modal-backdrop").remove();
		$("#miModalMaster").modal("hide");

		$("#segmento-aplicar").hide();
		$("#segmento-general").show();
		alertify.set("notifier", "position", "top-right");
		alertify.success("Proceso finalizado");
	}).always(function () {
		// Ocultar el spinner después de la petición
		$("#spinner-placeholder").hide();
	});
}

/*function generar_facturas() {
	

	id = $("#id-master").val();
	referencia = $("#referencia_number").val();

	var url = base_url(
		"index.php/WhController/generar_facturas/" + id + "/" + referencia
	);

	$.get(url, function (data) {
		var datos = JSON.parse(data);
		var destino = datos["destino"];
		var nombre_archivo = datos["nombre_archivo"];
		var ruta_manifiesto = destino + nombre_archivo;

		ver_report_comprobantePDF(ruta_manifiesto);
	});





}*/


function generar_facturas(){

	id = $("#id-master").val();
	referencia = $("#referencia_number").val();

	var url = base_url(
		"index.php/WarehouseController/generar_facturas_new/" + id + "/" + referencia
	);

	var pdfWindow = window.open('', '_blank');
	pdfWindow.location.href = url;

}

function insertar_estatus_masivo() {
	alertify.set("notifier", "position", "top-right");
	if ($("#estatus").val() == "") {
		alertify.error("Debes ingresar un estatus");
		return false;
	}
	if ($("#txtListWarehouse").val() == "") {
		alertify.error("Debes ingresar un maximo de 50 guías");
		return false;
	}

	var frm = $("#cambiarestatus").serialize();

	console.log(frm);

	$.post(
		base_url("index.php/WarehouseController/insertar_estatus_masivo/"),
		frm,
		function (result) {
			console.log(result);
			if (result == 1) {
				alertify.success("Cambiado con exito");
				$("#txtListWarehouse").val("");
			} else {
				alertify.error("Error en cambio");
			}
		}
	);
}

function editar_descripcion(id) {
	$("#loader").css("display", "block");
	var url = base_url("index.php/WhController/editar_descripcion/" + id);
	$.get(url, function (response) {
		$("#loader").css("display", "none");
		$("#editar_descripcion_modal").modal(
			{ backdrop: "static", keyboard: false },
			"show"
		);
		$("#editar_descripcion").html(response);
	});
}

function get_recolecta(id, tracking_number,manifiesto) {
	$("#loader").css("display", "block");
	var url = base_url(
		"index.php/WhController/get_recolecta/" + id + "/" + tracking_number + "/" + manifiesto
	);
	$.get(url, function (response) {
		$("#loader").css("display", "none");

		
			alertify.set("notifier", "position", "top-right");
		    alertify.success("Proceso finalizado correctamente");
	
	});
}

function vista_lista_awb() {
	//Funcion para regresar
	$('[data-toggle="tooltip"]').tooltip("dispose");
	lista_awb();
}

function obtener_Detalles_Por_Estatus(nombreEstatus) {
	//Funcion para ver los detalles desde la grafica
	var id = $("#id_master_detalle").val();
	var url = base_url("index.php/WhController/obtener_Detalles_Por_Estatus/");
	$("#loader").css("display", "block");

	$.get(url, { id: id, nombre: nombreEstatus }, function (response) {
		$("#loader").css("display", "none");
		$("#modal_detalle_grafica_por_estatus").modal(
			{ backdrop: "static", keyboard: false },
			"show"
		);
		$("#div_detalle_graficas").html(response);
		var tabla = $("#tbl_detalle_estatus").DataTable({
			buttons: [
				{
					extend: "excel",
					className: "d-none",
					title: nombreEstatus,
				},
			],
			columnDefs: [
				{ width: "5%", targets: 0 },
				{ width: "25%", targets: 2 },
			],
			autoWidth: false,
			language: {
				lengthMenu: "Mostrar _MENU_ registros",
				zeroRecords: "No se encontraron resultados",
				info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
				infoFiltered: "(filtrado de un total de _MAX_ registros)",
				sSearch: "Buscar:",
				oPaginate: {
					sFirst: "Primero",
					sLast: "Último",
					sNext: ">>",
					sPrevious: "<<",
				},
				sProcessing: "Procesando...",
			},
		});
		$("#btn_export_excel").on("click", function () {
			tabla.button(".buttons-excel").trigger();
		});
	});
}

//inicio de bloque para mostrar graficas
let chartsReadyPromise = null;
let chartEstatusDibujado = false;
let chartResumenDibujado = false;

function ensureGoogleCharts() {
	if (chartsReadyPromise) return chartsReadyPromise;

	chartsReadyPromise = new Promise((resolve) => {
		function loadAndResolve() {
			if (window.google && google.visualization && google.charts) {
				resolve();
			} else {
				google.charts.load("current", { packages: ["corechart"] });
				google.charts.setOnLoadCallback(() => resolve());
			}
		}

		// Si ya está loader.js y/o google.charts:
		if (window.google && google.charts) {
			loadAndResolve();
			return;
		}

		// Inyecta loader.js una sola vez si no existe
		let s = document.querySelector("script[data-googlecharts-loader]");
		if (!s) {
			s = document.createElement("script");
			s.src = "https://www.gstatic.com/charts/loader.js";
			s.async = true;
			s.defer = true;
			s.setAttribute("data-googlecharts-loader", "1");
			document.head.appendChild(s);
		}
		s.addEventListener("load", loadAndResolve, { once: true });
	});

	return chartsReadyPromise;
}

async function safeDrawBothCharts() {
	try {
		await ensureGoogleCharts();
		// Espera un frame para que el modal tenga tamaño real
		requestAnimationFrame(() => {
			if (
				!chartEstatusDibujado &&
				document.getElementById("graficas_estatus")
			) {
				if (typeof drawChart === "function") {
					drawChart();
					chartEstatusDibujado = true;
				} else {
					console.warn(
						"drawChart() no está definida (la vista aún no insertó el script)."
					);
				}
			}
			if (
				!chartResumenDibujado &&
				document.getElementById("grafica_resumen_entrega")
			) {
				if (typeof drawChartResumen === "function") {
					drawChartResumen();
					chartResumenDibujado = true;
				} else {
					console.warn(
						"drawChartResumen() no está definida (la vista aún no insertó el script)."
					);
				}
			}
		});
	} catch (e) {
		console.error("Error cargando/dibujando Google Charts:", e);
	}
}

// (Opcional) Redibujar al cambiar tamaño
window.addEventListener("resize", () => {
	if (
		document.getElementById("graficas_estatus") ||
		document.getElementById("grafica_resumen_entrega")
	) {
		chartEstatusDibujado = false;
		chartResumenDibujado = false;
		safeDrawBothCharts();
	}
});

function generar_grafica_estatus(id) {
	chartEstatusDibujado = false;
	chartResumenDibujado = false;

	$("#loader").css("display", "block");
	const url = base_url("index.php/WhController/grafica_estatus/" + id);

	$.get(url, function (response) {
		$("#loader").css("display", "none");
		$("#datos_grafica_estatus").html(response);

		const $modal = $("#modal_grafica_estatus");

		// Evita handlers duplicados
		$modal.off("shown.bs.modal hidden.bs.modal");

		// Dibuja cuando el modal YA es visible y tiene tamaño
		$modal.on("shown.bs.modal", function () {
			safeDrawBothCharts();
		});

		// Resetea flags al cerrar
		$modal.on("hidden.bs.modal", function () {
			chartEstatusDibujado = false;
			chartResumenDibujado = false;
		});

		$modal.modal({
			backdrop: "static",
			keyboard: false,
		});
	});
}

//fin graficas
function descargarExcelResumen(id, estatus) {
	var url = base_url("index.php/WhController/exportar_excel_grafica_resumen/");
	$("#loader").css("display", "block");

	$.ajax({
		url: url,
		method: "POST",
		data: { id_manifiesto: id, estatus: estatus },
		success: function (response) {
			$("#loader").css("display", "none");
			console.log(response);

			// Insertar tabla en el contenedor
			$("#div_tabla_excel").html(response);

			// Asegurarse de destruir cualquier instancia previa
			if ($.fn.DataTable.isDataTable("#tbl_excel_resumen")) {
				$("#tbl_excel_resumen").DataTable().destroy();
			}

			// Esperar un poco a que el DOM esté listo
			setTimeout(() => {
				$("#tbl_excel_resumen").DataTable({
					buttons: [
						{
							extend: "excel",
							className: "d-none", // oculto, se dispara automáticamente
							title: estatus,
						},
					],
					columnDefs: [
						{ width: "5%", targets: 0 },
						{ width: "25%", targets: 2 },
					],
					autoWidth: false,
					language: {
						lengthMenu: "Mostrar _MENU_ registros",
						zeroRecords: "No se encontraron resultados",
						info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
						infoEmpty:
							"Mostrando registros del 0 al 0 de un total de 0 registros",
						infoFiltered: "(filtrado de un total de _MAX_ registros)",
						sSearch: "Buscar:",
						oPaginate: {
							sFirst: "Primero",
							sLast: "Último",
							sNext: ">>",
							sPrevious: "<<",
						},
						sProcessing: "Procesando...",
					},
					initComplete: function () {
						// Disparar exportación automática
						this.api().button(".buttons-excel").trigger();
					},
				});
			}, 100); // Espera corta para asegurar que el DOM se haya actualizado
		},
		error: function () {
			alertify.alert("Error al generar el archivo Excel.", function () {
				alertify.message("OK");
			});
			$("#loader").css("display", "none");
		},
	});
}

function printPdf(url) {
	var iframe = window._printIframe;

	if (!iframe) {
		iframe = window._printIframe = document.createElement("iframe");
		document.body.appendChild(iframe);
		iframe.style.display = "none";
	}

	// Asignamos el onload cada vez que cambiamos la URL
	iframe.onload = function () {
		setTimeout(function () {
			try {
				iframe.contentWindow.focus();
				iframe.contentWindow.print();
			} catch (e) {
				console.error("No se pudo imprimir automáticamente:", e);
			}
		}, 100);
	};

	iframe.src = url;
}

function pdf_guia_xpress(id, master, internal) {
	if (internal == 0) {
		alertify.set("notifier", "position", "top-right");
		alertify.error("Aún no tiene guía creada, por favor verifique ");
		return false;
	}

	var url = base_url(
		"index.php/WhController/pdf_guia_xpress/" + id + "/" + master
	);

	$.ajax({
		url: url,
		type: "GET",
		xhrFields: {
			responseType: "blob",
		},
		success: function (data, status, xhr) {
			if (xhr.getResponseHeader("Content-Type") === "application/pdf") {
				var blob = new Blob([data], { type: "application/pdf" });
				var blobUrl = URL.createObjectURL(blob);
				printPdf(blobUrl);
			} else {
				var reader = new FileReader();
				reader.onload = function () {
					var error = JSON.parse(reader.result).error;
					alertify.alert("Error", error);
				};
				reader.readAsText(data);
			}
		},
		error: function () {
			alertify.alert("Error", "No se pudo generar la guía");
		},
	});
}

function cargarDocumentosManifiesto(id) {
	$("#loader").css("display", "block");
	var url = base_url('index.php/WhController/cargar_documentos_manifiesto/' + id);
	$.get(url, function (response) {
		$("#loader").css("display", "none");
		$('#documentos_manifiesto_body').html(response);
		$('#manifiesto_id_documentos').val(id);
		listar_documentos_manifiesto(id);
		$("#modal_documentos_manifiesto").modal("show");
	});
}



function listar_documentos_manifiesto(id) {
	var url = base_url("index.php/WhController/listar_documentos_manifiesto/" + id);
	$.get(url, function (response) {
		$('#lista_pdfs').html(response);
	});
}

/*function subir_doc_manifiesto() {
	var url = base_url("index.php/WhController/subir_doc_manifiesto/");
	const manifiesto = $('#manifiesto_id_documentos').val();
	const files = $('#documentos_manifiesto')[0].files;

	if (files.length === 0) {
		alertify.set("notifier", "position", "top-right");
		alertify.error('Por favor, selecciona al menos un documento.');
		return;
	}

	let formData = new FormData();
	formData.append('manifiesto', manifiesto);

	for (let i = 0; i < files.length; i++) {
		formData.append('documentos_manifiesto[]', files[i]);
	}

	$("#loader").css("display", "block");

	$.ajax({
		url: url,
		method: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		dataType: 'json',
		success: function (response) {
			$("#loader").css("display", "none");

			if (response.status === 'success') {
				alertify.set("notifier", "position", "top-right");
				alertify.success(response.msg);
				listar_documentos_manifiesto(manifiesto);
				$('#documentos_manifiesto').val('');
				$('#lista_archivos').html('');

			} else {
				alertify.set("notifier", "position", "top-right");
				alertify.error(response.msg);
			}
		},
		error: function (xhr, status, error) {
			$("#loader").css("display", "none");

			alertify.set("notifier", "position", "top-right");
			alertify.error("La petición no se pudo procesar");
		}
	});
}*/

function mostrarTabla(datos) {

	//console.log(data)

	const container = document.getElementById('resultado-container');

	//const datos = JSON.parse(data);

	let html = `
                    <table class="status-table">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Mensaje</th>
                            </tr>
                        </thead>
                        <tbody>`;


	datos.forEach((item) => {
		// Nota: Tu JSON tiene un espacio en la clave "archivo "


		const isSuccess = item["status"] === 'success';


		html = html + `<tr>
                                <td>
                                    <span class="badge ${isSuccess ? 'badge-success' : 'badge-danger'}">
                                        ${item["status"]}
                                    </span>
                                </td>
                                <td>Archivo: ${item["archivo "]} - Razón: ${item.razon}</td>
                                
                            </tr>`;
	})


	html = html + `</tbody>
                    </table>
                `;



	console.log(html);

	container.innerHTML = html;
}

function downloadZip(){
	const manifiesto = $('#manifiesto_id_documentos').val();
	const typeSelected = $('#tipoZip').val();
	var url = base_url("index.php/WhController/downloadzip/"+manifiesto+"/"+typeSelected);
	console.log(url);
	window.location.href = url;

	/*$.get(url, function (respuesta) {
		console.log("Ejecutado exitosamente");
	});*/
}

function subir_doc_manifiesto() {
	var url = base_url("index.php/WhController/subir_doc_manifiesto/");
	const manifiesto = $('#manifiesto_id_documentos').val();
	const files = $('#documentos_manifiesto')[0].files;
	const tipo_archivo = $('#selectTipoArchivo').val();

	if (files.length === 0) {
		alertify.set("notifier", "position", "top-right");
		alertify.error('Por favor, selecciona al menos un documento.');
		return;
	}

	let formData = new FormData();
	formData.append('manifiesto', manifiesto);
	formData.append('selectTipoArchivo', tipo_archivo);

	for (let i = 0; i < files.length; i++) {
		formData.append('documentos_manifiesto[]', files[i]);
	}

	$("#loader").css("display", "block");

	$.ajax({
		url: url,
		method: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		dataType: 'json',
		success: function (response) {
			$("#loader").css("display", "none");

			if (response.status === 'success') {
				alertify.set("notifier", "position", "top-right");
				alertify.success(response.msg);
				listar_documentos_manifiesto(manifiesto);
				$('#documentos_manifiesto').val('');
				$('#lista_archivos').html('');

			} else {
				mostrarTabla(response);
			}
		},
		error: function (xhr, status, error) {
			$("#loader").css("display", "none");

			alertify.set("notifier", "position", "top-right");
			alertify.error("La petición no se pudo procesar");
		}
	});
}

function eliminar_documento_manifiesto(id) {
	var id_manifiesto = $('#manifiesto_id_documentos').val();
	alertify.confirm()
		.setting({
			title: 'Eliminar',
			message: '¿Desea eliminar este documento?',
			labels: { ok: 'Sí', cancel: 'Cancelar' },
			onok: function () {
				var url = base_url("index.php/WhController/eliminar_documento_manifiesto/" + id);
				$("#loader").css("display", "block");

				$.ajax({
					url: url,
					method: 'POST',
					success: function (response) {
						var data = JSON.parse(response);
						$("#loader").css("display", "none");

						if (data.status === "ok") {
							alertify.set("notifier", "position", "top-right");
							alertify.success(data.msg);
						} else {
							alertify.set("notifier", "position", "top-right");
							alertify.error(data.msg);
						}

						listar_documentos_manifiesto(id_manifiesto);
					},
					error: function (xhr, status, error) {
						$("#loader").css("display", "none");

						alertify.set("notifier", "position", "top-right");
						alertify.error("La petición no se pudo procesar");
					}
				});
			},
			oncancel: function () {
				alertify.error('Cancelado');
			}
		}).show();
}

function openChangeCountry(idpreclasificacion) {

	$("#modalCambiarPais").modal("show");
	$("#idpreclasificacionchangecountry").val(idpreclasificacion);

}

function set_proveedorlm(idpreclasificacion) {
	$("#modalProveedorLm").modal("show");
	$("#idpreclasificacionlm").val(idpreclasificacion);
}


function changeCountry(idpreclasificacion) {

	var formData;
	var idpreclasificacion = 0;
	idpreclasificacion = $("#idpreclasificacionchangecountry").val();

	var url = base_url("index.php/WarehouseController/changeOfCountry/" + idpreclasificacion);
	formData = new FormData($("#frmChangeCountry")[0]);


	let isConfirmed = confirm("Estas seguro de modificar este manifiesto?");

	if (isConfirmed) {
		$.ajax({

			url: url,
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function () {
				// Show image container
				//$("#loader").css("display", "block");
			},
			success: function (response) {

				console.log(response);

				var result = JSON.parse(response);


				if (result.success) {
					alert(result.success)
				} else {
					alert(result.error)
				}


				$(".modal-backdrop").remove();
				$("#modalCambiarPais").modal("hide");
				$("#msg").show();
				setTimeout(function () {
					$("#msg").fadeTo("slow", 0.1, function () {
						$("#msg").alert("close");
					});
				}, 10000);



			}

		});
	} else {
		alert("No realizaste la operacion");
	}

	return false;


}

function detalle_documentos(manifiesto) {

	//$("#loader").css("display", "block");
	var url = base_url('index.php/WhController/reporte_documentos/'+manifiesto);
	$.get(url, function (response) {
		//$("#loader").css("display", "none");
		$('#resumen_archivos').html(response);
		$('#modal_detalle_documentos').modal({ backdrop: 'static', keyboard: false }, 'show');

	})

}

function setProveedorLm() {
	var formData;
	var idpreclasificacion = 0;
	idpreclasificacion = $("#idpreclasificacionlm").val();

	var url = base_url("index.php/WarehouseController/setProveedorLm/" + idpreclasificacion);
	formData = new FormData($("#frmProveedorLm")[0]);


	let isConfirmed = confirm("Estas seguro que desea asignar el proveedor de ultima milla?");

	if (isConfirmed) {
		$.ajax({

			url: url,
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function () {
				// Show image container
				//$("#loader").css("display", "block");
			},
			success: function (response) {

				console.log(response);

				var result = JSON.parse(response);

				if (result.success) {
					alert(result.success)
				} else {
					alert(result.error)
				}

				$(".modal-backdrop").remove();
				$("#modalProveedorLm").modal("hide");
				$("#msg").show();
				setTimeout(function () {
					$("#msg").fadeTo("slow", 0.1, function () {
						$("#msg").alert("close");
					});
				}, 10000);
				lista_awb();

			}

		});
	} else {
		alert("No realizaste la operacion");
	}

	return false;
}


function set_master_temu(id) {
	$("#loader").css("display", "block");
	var url = base_url('index.php/WhController/get_current_guide/' + id);
	$.get(url, function (response) {
		$("#loader").css("display", "none");
		$('#bodyChangeGuide').html(response);
		$('#txtIdPreclasificacionChangeGuide').val(id);
		$('#modal_asignar_master').modal({ backdrop: 'static', keyboard: false }, 'show');
	})
}

function setGuia() {

	// Referencia al botón para feedback visual
	var btn = $(this).find(".logis-btn-save");
	var btnOriginalText = btn.text();

	var data = $("#frmGuiaToSet").serialize();

	var url = base_url("index.php/WhController/cambiarGuia/");

	// Bloquear botón
	btn.prop("disabled", true).text("Procesando...").css("opacity", "0.7");

	$.ajax({
		url: url, // Cambia esto por tu ruta real
		type: "POST",
		data: data,
		success: function (response) {
			alertify.set("notifier", "position", "top-right");
			alertify.success("Guia actualizada correctamente");
			var id = $("#txtIdPreclasificacionChangeGuide").val();
			set_master_temu(id);

		},
		error: function () {
			alertify.set("notifier", "position", "top-right");
			alertify.error("La petición no se realizó");
		},
		complete: function () {
			// Restaurar botón
			btn.prop("disabled", false).text(btnOriginalText).css("opacity", "1");
		}
	});

}

