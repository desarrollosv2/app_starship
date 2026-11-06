function base_url(url) {
	return window.location.origin + "/app_starship/" + url;
}

$(document).ready(function () {
	$("#frm_buscar_bag_number").on("submit", function (e) {
		e.preventDefault();
		var data = $("#frm_buscar_bag_number").serialize();
		var url = base_url("index.php/Gestion/buscar_bag_number");

		$.ajax({
			url: url,
			method: "POST",
			data: data,
			beforeSend: function () {
				// Show image container
				$("#loader").css("display", "block");
			},
			success: function (response) {
				$("#tbl_respuesta_busqueda").html(response);
				$("#tbl_lista").DataTable({
					language: {
						lengthMenu: "Mostrar _MENU_ registros",
						zeroRecords: "No se encontraron resultados",
						info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
						infoEmpty:
							"Mostrando registros del 0 al 0 de un total de 0 registros",
						infoFiltered: "(filtrado de un total de _MAX_ registros)",
						sSearch: "Buscar:",
						emptyTable: "No hay datos disponibles en la tabla", // <<--- Este es el que faltaba
						oPaginate: {
							sFirst: "Primero",
							sLast: "Último",
							sNext: ">>",
							sPrevious: "<<",
						},
						sProcessing: "Procesando...",
					},
				});
				$("#tracking-number").val("");
			},
			complete: function () {
				// Show image container
				$("#loader").css("display", "none");
			},
		});
	});
});

function TraslateMaster(id) {
	var url = base_url("index.php/Traductor/traducir/" + id);

	$.ajax({
		url: url,
		method: "GET",

		beforeSend: function () {
			// Show image container
			$("#loader").css("display", "block");
		},
		success: function (response) {
			$("#tbl_respuesta_busqueda").html(response);

			$("#tracking-number").val("");
		},
		complete: function () {
			// Show image container
			$("#loader").css("display", "none");
		},
	});
}

function push_list() {
	var url = base_url("index.php/Gestion/push");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function push_send() {
	var url = base_url("index.php/Gestion/push_events_apg");
	$.get(url, function (data) {
		//echo	$("#referenciasxguias").html(data);
		alert("Finalizado");
	});
}

function sincronizar_modal() {
	id = $("#id-master").val();
	master=($("#name_master").val());
	$("#loader").css("display", "block");
	var url = base_url("index.php/Gestion/sincronizar/" + id);
	$.get(url, function (data) {
		$(".modal-backdrop").remove();
		$("#confirmacionModal").modal("hide");
		$("#loader").css("display", "none");
		lista_warehouse(id,master,0);
	});
}

function ver_contenido_tracking(id) {
	$("#loader").css("display", "block");
	var url = base_url('index.php/Gestion/ver_contenido_tracking/' + id);
	$.get(url, function (response) {
		$("#loader").css("display", "none");
		$('#manifiesto_number').text(id);
		$('#div_contenido_tracking').html(response);
		$('#ver_contenido_tracking').modal({ backdrop: 'static', keyboard: false }, 'show');
	})

}
