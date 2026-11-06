function base_url(url) {
	return window.location.origin + "/app_starship/" + url;
}

function cliente() {
	var url = base_url("index.php/ClientesController/clientes");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function warehouse() {
	var url = base_url("index.php/WarehouseController/warehouse");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function preclasifica() {
	var url = base_url("index.php/PreclasificacionController/pre_clasificacion");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function lista_pod() {
	var url = base_url("index.php/POD_Controller/pod_principal");
	$.get(url, function (data) {

		$("#principal").html(data);
		setFechasDefaultPOD();
		listado_pod();
	});
}

function user_list() {
	var url = base_url("index.php/Usuarios/");

	$.ajax({
		type: "POST",
		url: url,
		async: false,
		cache: false,
		beforeSend: function () {
			$("#loader").css("display", "block");
		},
		success: function (data) {
			$("#principal").html(data);
			$("#loader").fadeOut(500);
		},
		error: function (xhr, status, error) {
			console.log("error: " + error);
		},
		complete: function () {
			//	document.onload = pageLoaded();
			//$("#loader").css("display", "none");
			//	$('#loader').fadeOut(100);
		},
	});
}

function pageLoaded() {
	let loaderSection = document.querySelector(".loader-section");
	loaderSection.classList.add("loaded");
}

function clasifica(id, manifiesto) {
	// alert(manifiesto);
	var url = base_url("index.php/WarehouseController/clasifica/" + id);
	$.get(url, function (data) {
		$("#principal").html(data);
		$("#id-master-awb").val(id);
		$("#manifiesto-number").val(manifiesto);
	});
}

function cambiar_estatus() {
	var url = base_url("index.php/WarehouseController/cambiar_estatus");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function cargar_archivo() {
	var url = base_url("index.php/WarehouseController/cargar_archivo");
	$.get(url, function (data) {
		$("#principal").html(data);
		$("#upfileModal").modal("show");
	});
}

// fucion  para  cargar  en modal  la acccion de cargar  archivps 02-10-2023
function cargar_archivo1(idpreclasificacion) {
	console.log(
		"Cargando  la modal para  actualizar los   paquetes" + idpreclasificacion
	);
	var url = base_url("index.php/WarehouseController/cargar_archivoMF");
	$.get(url, function (data) {
		$("#awb_update").html(data);
		$("#upfileModal").modal("show");
		$("#idpreclasificacion").val(idpreclasificacion);
	});
}

function lista_awb() {
	startDate = $("#startDate").val();
	endDate = $("#endDate").val();
	//alert(startDate + " " + endDate);

	document.getElementById('delivery-loader').style.display = 'flex';

	var url = base_url("index.php/WhController/lista_awb");
	$.get(url, function (data) {
		$("#principal").html(data);
		var url = base_url(
			"index.php/WhController/lista_awb_first/" + startDate + "/" + endDate
		);
		$.get(url, function (data) {
			$("#awb_list").html(data);
			$("#startDate").val(startDate);
			$("#endDate").val(endDate);

			$(".dropdown-toggle").dropdown(); // por si acaso

			document.getElementById('delivery-loader').style.display = 'none';
		});
	});
}

function customers() {
	var url = base_url("index.php/Customers/customers");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function contactanos() {
	var url = base_url("index.php/Customers/contactanos");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}
/*
function cambiar_clave() {
	var url = base_url("index.php/Customers/cambiar_clave");
	$.get(url, function (data) {
		$("#principal").html(data);
	//	$("#cambiar_clave_modal").modal("show");
	});
}

function cambiar_email() {
	var url = base_url("index.php/Customers/cambiar_email");
	$.get(url, function (data) {
		$("#principal").html(data);
		$("#cambiar_correo_modal").modal("show");
	});
}

function suc_entrega() {
	var url = base_url("index.php/Customers/suc_entrega");
	$.get(url, function (data) {
		$("#principal").html(data);
		$("#cambiar_sucursal_modal").modal("show");
	});
}
*/
function prealertas() {
	var url = base_url("index.php/Customers/prealertas");
	$.get(url, function (data) {
		$("#principal").html(data);
		//$("#prealerta_modal").modal("show");
	});
}

function info_cuenta() {
	var url = base_url("index.php/Customers/info_cuenta");
	$.get(url, function (data) {
		$("#principal").html(data);
		var op = $("#id_sucursal").val();
		$("#sucursal").val(op);
		$("#s_entrega").val($("#sucursal option:selected").text());
	});
}
/*
function datos_clientes() {
	var url = base_url("index.php/Sincronizar/datos_clientes_magic");
	$.get(url, function (data) {
		$("#principal").html(data);
		//$("#prealerta_modal").modal("show");
	});
}
*/
function crear_credenciales() {
	var url = base_url("index.php/Sincronizar/crear_credencial");
	$.get(url, function (data) {
		$("#principal").html(data);
		alertify.set("notifier", "position", "top-right");
		alertify.success("Proceso terminado correctamente");
	});
}

function lista_inventario() {
	var url = base_url("index.php/InventarioController/inventario");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function traspaso() {
	var url = base_url("index.php/Traspaso/pre_clasificacion");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}
/*end traspaso*/

/* send mail */
function send_mail(body, mail) {
	var url = base_url("index.php/SendController/sendmail/" + body + "/" + mail);
	$.get(url, function (data) {
		//$("#principal").html(data);
	});
}

function archivo_facturacion() {
	var url = base_url("index.php/WhController/archivo_facturacion");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function facturacion() {
	var url = base_url("index.php/FController/facturacion");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

// funcion  para  actualizar el  dui  de los   manifiestos 12-10-2023
function cargar_archivo2(idpreclasificacion) {
	console.log(
		"Cargando  la modal para  actualizar DATOS  DUI" + idpreclasificacion
	);
	var url = base_url("index.php/WarehouseController/cargar_archivoDUI");
	$.get(url, function (data) {
		$("#dui_update").html(data);
		$("#upfileModalDui").modal("show");
		$("#idpreclasificacion").val(idpreclasificacion);
	});
}

function clasificacion() {
	var url = base_url("index.php/ClasificacionController/lista_awb");
	$.get(url, function (data) {
		$("#principal").html(data);
		var url = base_url("index.php/ClasificacionController/lista_awb_first");
		$.get(url, function (data) {
			$("#awb_list").html(data);
		});
	});
}

function consulta_cliente() {
	var url = base_url("index.php/ClientesController/consulta_cliente");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function send_tracking() {
	var url = base_url("index.php/WarehouseController/send_tracking");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function report_send_tracking() {
	var url = base_url("index.php/WarehouseController/report_send_tracking");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function cargar_declaracion() {
	var url = base_url("index.php/DeclaracionController/lista_awb");
	$.get(url, function (data) {
		$("#principal").html(data);

		var url = base_url("index.php/DeclaracionController/lista_awb_first");
		$.get(url, function (data) {
			$("#awb_list").html(data);
		});
	});
}

function reporte_envio_tracking() {
	var url = base_url("index.php/WarehouseController/report_send_tracking");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function listar_tasa_cambio() {
	var url = base_url("index.php/Tasa_cambioController/listar_tasa_cambio");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}
function cambiar_estatus_masivo() {
	var url = base_url("index.php/WarehouseController/cambiar_estatus_masivo");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function recolecta_corte() {
	var url = base_url("index.php/CortesController/");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

function bag_guides(master) {
	var url = base_url("index.php/Gestion/");
	$.get(url, function (data) {
		$("#principal").html(data);
		$("#master-id").val(master);
	});
}

function imile_comparar() {
	var url = base_url("index.php/Imile_Controllers/imile_index");
	$.get(url, function (data) {
		$("#principal").html(data);
	});
}

// Carga la vista (sin disparar DataTables)
function ultimos_estatus() {
	var url = base_url("index.php/LazyList/ultimos_estatus");
	$.ajax({
		url: url,
		type: "GET",
		timeout: 30000,
		beforeSend: function () {
			$("#loader").css("display", "block");
		},
		success: function (html) {
			$("#principal").html(html);

			ensureDataTables(function () {
				wireUltimosEstatusEvents();
				loadEmpresas(); // 👈 llena el select de empresa
			});
		},
		complete: function () {
			$("#loader").css("display", "none");
		},
		error: function (xhr) {
			$("#loader").css("display", "none");
			console.error(
				"[VIEW] Error:",
				xhr.status,
				xhr.statusText,
				xhr.responseText
			);
			alert("No se pudo cargar la vista (" + xhr.status + ")");
		},
	});
}

// Carga empresas para el <select>
function loadEmpresas() {
	$.ajax({
		url: base_url("index.php/LazyList/empresas"),
		type: "GET",
		dataType: "json",
		success: function (rows) {
			var $sel = $("#empresa_id");
			$sel.find("option:not(:first)").remove();
			(rows || []).forEach(function (r) {
				$sel.append('<option value="' + r.id + '">' + r.nombre + "</option>");
			});
		},
		error: function (xhr) {
			console.error("Error cargando empresas:", xhr.status, xhr.statusText);
			if (window.alertify) {
				alertify.error("No se pudieron cargar las empresas");
			}
		},
	});
}

var dtUltimos = null;

function wireUltimosEstatusEvents() {
	$(document).off("click", "#btnFiltrar");
	$(document).off("click", "#btnLimpiar");
	$(document).off("input change", "#empresa_id, #fecha_desde, #fecha_hasta");

	// Habilita/Deshabilita el botón Filtrar
	$(document).on(
		"input change",
		"#empresa_id, #fecha_desde, #fecha_hasta",
		function () {
			const ok =
				$("#empresa_id").val() &&
				$("#fecha_desde").val() &&
				$("#fecha_hasta").val();
			$("#btnFiltrar").prop("disabled", !ok);
		}
	);

	$(document).on("click", "#btnFiltrar", function () {
		const emp = $("#empresa_id").val();
		const desde = $("#fecha_desde").val();
		const hasta = $("#fecha_hasta").val();

		if (!emp || !desde || !hasta) {
			if (window.alertify) {
				alertify.error("Selecciona empresa y rango de fechas.");
			} else {
				alert("Selecciona empresa y rango de fechas.");
			}
			return;
		}
		initUltimosEstatusDataTable(); // aquí recién hace el primer POST
	});

	$(document).on("click", "#btnLimpiar", function () {
		$("#empresa_id").val("");
		$("#fecha_desde").val("");
		$("#fecha_hasta").val("");
		$("#btnFiltrar").prop("disabled", true);
		if (dtUltimos) {
			dtUltimos.clear().draw();
			dtUltimos.destroy();
			dtUltimos = null;
		}
		$("#tabla-ultimos tbody").empty();
	});
}

function initUltimosEstatusDataTable() {
	if (!document.getElementById("tabla-ultimos")) return;

	if (dtUltimos && $.fn.DataTable.isDataTable("#tabla-ultimos")) {
		dtUltimos.destroy();
		dtUltimos = null;
	}

	$(document)
		.off("xhr.dt")
		.on("xhr.dt", function (e, settings, json, xhr) {
			console.log("[DT] xhr:", xhr.status, xhr.statusText);
		});

	dtUltimos = $("#tabla-ultimos").DataTable({
		processing: true,
		serverSide: true,
		deferRender: true,
		responsive: true,
		ajax: {
			url: base_url("index.php/LazyList/ultimos_estatus_data"),
			type: "POST",

			beforeSend: function () {
				//$("#loader").css("display", "block");
				//$("#btnFiltrar").prop("disabled", true); // opcional: deshabilita botón durante la carga
			},
			complete: function () {
				//$("#loader").css("display", "none");
				//$("#btnFiltrar").prop("disabled", false);
			},

			timeout: 30000,
			data: function (d) {
				d.empresa_id = $("#empresa_id").val();
				d.fecha_desde = $("#fecha_desde").val();
				d.fecha_hasta = $("#fecha_hasta").val();
				// CSRF si aplica:
				// d['<?= $this->security->get_csrf_token_name(); ?>'] = '<?= $this->security->get_csrf_hash(); ?>';
			},
			error: function (xhr) {
				 // $("#loader").css("display", "none");
				console.error(
					"[DT] Error:",
					xhr.responseText || xhr.statusText,
					xhr.status
				);
				alert("Error cargando datos (" + xhr.status + ")");
			},
		},
		order: [[4, "desc"]],
		lengthMenu: [
			[10, 25, 50, 100, 250],
			[10, 25, 50, 100, 250],
		],
		pageLength: 25,
		columns: [
			{ data: 0, orderable: false }, // #
			{ data: 1 }, // tracking
			{ data: 2 }, // id_estatus
			{ data: 3 }, // nombre_estatus
			{ data: 4 }, // fecha_ultimo_estatus
			{ data: 5 }, // fecha_manifiesto
			{ data: 6 }, // empresa_nombre
		],
		language: {
			url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json",
		},
	});
}

// (por si no está en tu layout)
function ensureDataTables(cb) {
	if ($.fn && $.fn.DataTable) {
		cb();
		return;
	}
	var link = document.createElement("link");
	link.rel = "stylesheet";
	link.href = "https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css";
	document.head.appendChild(link);
	var s = document.createElement("script");
	s.src = "https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js";
	s.onload = cb;
	s.onerror = function () {
		alert("No se pudo cargar DataTables desde CDN");
	};
	document.body.appendChild(s);
}


// codigo pra exportar a csv

// Habilita/Deshabilita botones según filtros
$(document).on("input change", "#empresa_id, #fecha_desde, #fecha_hasta", function () {
  const ok = $("#empresa_id").val() && $("#fecha_desde").val() && $("#fecha_hasta").val();
  $("#btnFiltrar").prop("disabled", !ok);
  $("#btnExport").prop("disabled", !ok);
});

// Exportar TODO respetando filtros (+ búsqueda opcional)
$(document).off("click", "#btnExport").on("click", "#btnExport", function () {
  const empresa_id  = $('#empresa_id').val();
  const fecha_desde = $('#fecha_desde').val();
  const fecha_hasta = $('#fecha_hasta').val();
  const search      = $('#search_export').val() || ''; // opcional

  if (!empresa_id || !fecha_desde || !fecha_hasta) {
    if (window.alertify) alertify.error('Selecciona empresa y rango de fechas.');
    else alert('Selecciona empresa y rango de fechas.');
    return;
  }

  // Construye URL GET al endpoint de export
  const url = base_url('index.php/LazyList/export_ultimos_estatus')
            + '?empresa_id=' + encodeURIComponent(empresa_id)
            + '&fecha_desde=' + encodeURIComponent(fecha_desde)
            + '&fecha_hasta=' + encodeURIComponent(fecha_hasta)
            + '&search=' + encodeURIComponent(search);

			alert(url)

  // Muestra loader (opcional) mientras descarga
  $("#loader").show();
  // Abrir descarga en la misma ventana:
  window.location.href = url;

  // Quita loader después de unos segundos por UX (no hay callback de descarga)
  setTimeout(function(){ $("#loader").hide(); }, 3000);
});
// fin export csv

function conversor_csv() {
	var url = base_url("index.php/Gestion/conversor_csv");
	$.get(url, function (data) {

		$("#principal").html(data);

	});
}


/*function convertir_duca() {
	const files = $('#duca')[0].files;

	//console.log(files);

	if (files.length === 0) {
		alertify.error('Por favor, selecciona al menos una duca.');
		return;
	}

	document.getElementById('form_subir_duca').submit();
}*/

function convertir_duca() {
	const input = $('#duca')[0];
	const files = input ? input.files : [];

	if (files.length === 0) {
		alertify.error('Por favor, selecciona al menos una duca.');
		return;
	}

	const form = document.getElementById('form_subir_duca');
	const formData = new FormData(form);

	$('#loader').css('display', 'block');

	fetch(form.action, {
		method: 'POST',
		body: formData,
		credentials: 'same-origin'
	})
		.then(async response => {
			const resultados = leerResultadosDuca(response.headers.get('X-DUCA-Resultados'));

			if (!response.ok) {
				const texto = await response.text();
				const error = new Error(texto || 'No se pudieron procesar las DUCA.');
				error.resultados = resultados;
				throw error;
			}

			const disposition = response.headers.get('content-disposition') || '';
			const filename = getFilenameFromDisposition(disposition) || 'REPORTE_DUCA.zip';
			const blob = await response.blob();

			return { blob, filename, resultados };
		})
		.then(({ blob, filename, resultados }) => {
			const url = window.URL.createObjectURL(blob);
			const link = document.createElement('a');

			link.href = url;
			link.download = filename;
			document.body.appendChild(link);
			link.click();
			link.remove();
			window.URL.revokeObjectURL(url);

			alertify.set('notifier', 'position', 'top-right');
			alertify.success('Proceso de DUCA finalizado.');
			mostrarResultadosDuca(resultados);
		})
		.catch(error => {
			console.error(error);
			alertify.set('notifier', 'position', 'top-right');

			if (error.resultados && error.resultados.length > 0) {
				mostrarResultadosDuca(error.resultados);
			} else {
				alertify.error(error.message || 'Ocurrió un error al procesar las DUCA.');
			}
		})
		.finally(() => {
			$('#loader').css('display', 'none');
		});
}

function leerResultadosDuca(header) {
	if (!header) return [];

	try {
		const binary = atob(header);
		const bytes = Uint8Array.from(binary, character => character.charCodeAt(0));
		return JSON.parse(new TextDecoder().decode(bytes));
	} catch (error) {
		console.error('No se pudo leer el resumen de DUCA.', error);
		return [];
	}
}

function escaparHtmlDuca(value) {
	return $('<div>').text(value || '').html();
}

function mostrarResultadosDuca(resultados) {
	if (!resultados || resultados.length === 0) return;

	const filas = resultados.map(resultado => {
		const esCorrecta = resultado.estado === 'correcta';
		const esAdvertencia = resultado.estado === 'advertencia';
		const icono = esCorrecta ? 'fa-check' : (esAdvertencia ? 'fa-exclamation-triangle' : 'fa-times');
		const color = esCorrecta ? '#16803c' : (esAdvertencia ? '#d98200' : '#c0392b');

		return `<li style="margin-bottom:7px; color:${color};">
            <i class="fa ${icono}"></i>
            <strong>${escaparHtmlDuca(resultado.nombre)}</strong>
            <span style="color:#555;"> - ${escaparHtmlDuca(resultado.mensaje)}</span>
        </li>`;
	}).join('');

	alertify.alert(
		'<i class="fa fa-info-circle"></i> Resultado del procesamiento'+
		'<ul style="padding-left:20px; margin:0; list-style:none;">'+filas+'</ul>'
	);
}

function reporte_impuestos_gt() {
	var url = base_url("index.php/Gestion/reporte_impuestos_gt");
	$.get(url, function (data) {

		$("#principal").html(data);

	});
}

function reporte_impuestos_sv() {
	var url = base_url("index.php/Gestion/reporte_impuestos_sv");
	$.get(url, function (data) {

		$("#principal").html(data);

	});
}

function renombrar_ducas() {
	var url = base_url("index.php/Gestion/renombrar_ducas");
	$.get(url, function (data) {

		$("#principal").html(data);

	});
}


function conciliacion_pago_temu() {
	var url = base_url("index.php/Gestion/conciliacion_pago_temu");
	$.get(url, function (data) {

		$("#principal").html(data);

	});
}

function cargar_detalle_impuesto_temu() {
	$("#loader").css("display", "block");
	var url = base_url("index.php/Gestion/cargar_detalle_impuesto_temu/");

	$.get(url, function (response) {
		$("#loader").css("display", "none");
		$("#div_form_cargar_detalle_impuestos_temu").html(response);
		$('#carga_archivos_impuestos_temu').modal({ backdrop: 'static', keyboard: false }, "show");

	})
		.fail(function (xhr) {
			$("#loader").css("display", "none");
			alertify.set("notifier", "position", "bottom-right");
			alertify.error("No se pudo cargar la vista para cargar el detalle de impuestos TEMU");
		});
}

function detalle_impuestos_temu() {
	const Input = $('#detalle_temu')[0];

	const Files = Input ? Input.files : [];

	const invalidFile = [];

	const getExtension = (fileName) => {
		const parts = fileName.split('.');
		return parts.length > 1 ? parts.pop().toLowerCase() : '';
	};

	for (let i = 0; i < Files.length; i++) {

		const extension = getExtension(Files[i].name);

		if (extension !== 'xlsx' && extension !== 'xls') {
			invalidFile.push(Files[i].name);
		}

	}

	if (invalidFile.length > 0) {
		alertify.set('notifier', 'position', 'top-right');
		alertify.error('Debes de subir un archivo de Excel en formato .xlsx ó .xls');
		return;
	}

	if (Files.length === 0) {
		alertify.alert('Validación', 'Debe seleccionar al menos un archivo.');
		return;
	}

	if (!$('#empresas').val()) {
		alertify.alert('Validación', 'Debe seleccionar una empresa.');
		return;
	}

	const form = document.getElementById('form_subir_detalle_impuestos_temu');
	const formData = new FormData(form);

	$("#loader").css("display", "block");
	var url = base_url("index.php/Gestion/detalle_impuestos_temu/");

	$.ajax({
		url: url,
		method: 'POST',
		data: formData,
		cache: false,
		processData: false,
		contentType: false,
		dataType: 'json',
		success: function (response) {
			$("#loader").css("display", "none");

			const noEncontrados = Array.isArray(response.no_encontrados_lista)
				? response.no_encontrados_lista
				: [];
			const registrosProcesados = Number(response.actualizados || 0)
				+ Number(response.no_encontrados || 0);

			$('#resultado_procesados').text(registrosProcesados);
			$('#resultado_actualizados').text(response.actualizados || 0);
			$('#resultado_no_encontrados').text(response.no_encontrados || 0);
			$('#resultado_detalle_impuestos_temu').show();

			if ($.fn.DataTable.isDataTable('#tbl_no_encontrados_impuestos_temu')) {
				$('#tbl_no_encontrados_impuestos_temu').DataTable().destroy();
			}

			const filas = noEncontrados.map(function (tracking, index) {
				return '<tr><td>' + (index + 1) + '</td><td>'
					+ $('<div>').text(tracking).html() + '</td></tr>';
			}).join('');

			$('#tbl_no_encontrados_impuestos_temu tbody').html(filas);

			if (noEncontrados.length === 0) {
				$('#resultado_no_encontrados_contenedor').hide();
				$('#resultado_sin_no_encontrados').show();
				return;
			}

			$('#resultado_sin_no_encontrados').hide();
			$('#resultado_no_encontrados_contenedor').show();

			$('#tbl_no_encontrados_impuestos_temu').DataTable({
				dom: 'Bfrtip',
				pageLength: 10,
				buttons: [
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						title: 'Tracking_No_Encontrados_TEMU',
						className: 'btn btn-success'
					}
				],
				language: {
					zeroRecords: 'No se encontraron resultados',
					info: 'Mostrando registros del START al END de un total de TOTAL',
					infoEmpty: 'Mostrando los registros encontrados',
					search: 'Buscar:',
					paginate: {
						first: 'Primero',
						last: 'Último',
						next: 'Siguiente',
						previous: 'Anterior'
					}
				}
			});
		},
		error: function (xhr, status, error) {
			$("#loader").css("display", "none");

			alertify.set("notifier", "position", "top-right");
			alertify.error("La petición no se pudo procesar");
		}
	});
}

function usuarios_sistema() {
	var url = base_url("index.php/Usuarios_Controller/usuarios_sistema");

	$.ajax({
		url: url,
		type: "GET",
		success: function (data) {
			$("#principal").html(data);
			lista_usuarios_sistema();
		},
		error: function (xhr) {
			var respuesta = xhr.responseJSON;

			if (xhr.status === 403 && respuesta) {
				alertify.error(respuesta.msg);
				return;
			}

			alertify.error("No fue posible cargar el módulo.");
		}
	});
}


function crear_pallet() {
	var url = base_url("index.php/PalletController");
	window.open(url, '_blank');
}

function crears_pallet() {
	var url = base_url("index.php/PalletController");

	$.ajax({
		url: url,
		type: "GET",
		success: function (data) {
			$("#principal").html(data);
			//lista_usuarios_sistema();
		},
		error: function (xhr) {
			var respuesta = xhr.responseJSON;

			if (xhr.status === 403 && respuesta) {
				alertify.error(respuesta.msg);
				return;
			}

			alertify.error("No fue posible cargar el módulo.");
		}
	});
}




function buscar_conciliacion_temu() {
	var empresa = $('#empresa').val();
	var estado = $('#estado_pago').val();
	var master = $('#master').val();
	var referencia = $('#referencia').val();

	if (!empresa) {
		alertify.set("notifier", "position", "top-right");
		alertify.error("Debe seleccionar una empresa.");
		return;
	}

	if (!estado) {
		alertify.set("notifier", "position", "top-right");
		alertify.error("Debe seleccionar un estado.");
		return;
	}

	$("#loader").css("display", "block");

	var peticionTabla = $.ajax({
		url: base_url("index.php/Gestion/buscar_detalle_impuestos_temu/"),
		method: 'POST',
		data: {
			empresa: empresa,
			estado: estado,
			master: master,
			referencia: referencia
		}
	});

	var peticionDashboard = $.ajax({
		url: base_url("index.php/Gestion/dashboard_detalle_impuestos_temu/"),
		method: 'POST',
		data: {
			empresa: empresa,
			estado: estado,
			master: master,
			referencia: referencia
		}
	});

	$.when(peticionTabla, peticionDashboard)
		.done(function (tablaResponse, dashboardResponse) {
			$("#div_tbl_detalle_impuestos_temu").html(tablaResponse[0]);

			$('#tbl_detalle_impuestos_temu').DataTable({
				dom: 'Bfrtip',

				buttons: [
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						title: 'Detalle_Impuestos_TEMU',
						className: 'btn btn-success'
					}
				],

				language: {
					lengthMenu: "Mostrar MENU registros",
					zeroRecords: "No se encontraron resultados",
					info: "Mostrando registros del START al END de un total de TOTAL registros",
					infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
					infoFiltered: "(filtrado de un total de MAX registros)",
					sSearch: "Buscar:",
					oPaginate: {
						sFirst: "Primero",
						sLast: "Último",
						sNext: "Siguiente",
						sPrevious: "Anterior"
					},
					sProcessing: "Procesando..."
				}
			});


			$("#div_dash_detalle_impuestos_temu").html(dashboardResponse[0]);

			$("#loader").css("display", "none");
		})
		.fail(function () {
			$("#loader").css("display", "none");

			alertify.set("notifier", "position", "top-right");
			alertify.error("La petición no se pudo procesar");
		});
}