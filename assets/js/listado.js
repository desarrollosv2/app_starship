/* Listado de pallets: filtros, reimpresion de etiqueta y retomar pallets abiertos. */

var tabla_pallets = null;

function lista_pallets() {
    if (tabla_pallets) {
        tabla_pallets.ajax.reload();
        return;
    }

    tabla_pallets = $("#tabla-pallets").DataTable({
        ajax: {
            url: base_url("index.php/PalletController/listado_datos"),
            type: "POST",
            data: function (d) {
                $.each($(".form_filtros").serializeArray(), function (i, f) {
                    d[f.name] = f.value;
                });
            },
            dataSrc: function (data) {
                if (!data.success) {
                    alertify.error(esc_html(data.message));
                    return [];
                }
                return data.pallets;
            },
            error: function (xhr) {
                alertify.error(mensaje_error(xhr));
            }
        },
        // El numero se repite entre tipos (GG-000001, CN-000001...): por defecto se ordena por fecha de creacion
        order: [[5, "desc"]],
        pageLength: 10,
        language: DATATABLES_ES,
        columns: [
            { data: "numero_fmt", className: "fw-bold" },
            { data: "cliente_nombre", render: $.fn.dataTable.render.text() },
            { data: "tipo_legible", render: $.fn.dataTable.render.text() },
            { data: "total_cajas", render: columna_cajas },
            { data: "estado", render: columna_estado },
            { data: "created_at", render: columna_fecha },
            { data: "cerrado_at", defaultContent: "", render: columna_fecha },
            { data: null, orderable: false, searchable: false, className: "text-end", render: botones_acciones }
        ]
    });
}

function columna_cajas(valor, tipo, p) {
    if (tipo !== "display") {
        return valor;
    }
    return valor + (parseInt(p.excede_limite, 10)
        ? ' <span class="badge text-bg-warning" title="Superó el límite de ' + p.max_cajas + ' cajas con consentimiento del usuario">excede</span>'
        : "");
}

function columna_estado(valor, tipo) {
    if (tipo !== "display") {
        return valor;
    }
    return valor === "abierto"
        ? '<span class="badge rounded-pill text-bg-primary">Abierto</span>'
        : '<span class="badge rounded-pill text-bg-secondary">Cerrado</span>';
}

function columna_fecha(valor, tipo) {
    return tipo === "display" ? esc_html(formato_fecha(valor)) : (valor || "");
}

function botones_acciones(valor, tipo, p) {
    var retomar = p.estado === "abierto"
        ? '<li><hr class="dropdown-divider"></li><li><a class="dropdown-item" href="' +
        base_url("index.php/PalletController") + "?pallet=" + parseInt(p.id, 10) +
        '"><i class="fa-solid fa-barcode fa-fw text-primary"></i> Retomar escaneo</a></li>'
        : "";

    return '<div class="dropdown">' +
        '<button class="btn btn-outline-primary btn-acciones dropdown-toggle" type="button" data-bs-toggle="dropdown" ' +
        'data-bs-popper-config=\'{"strategy":"fixed"}\' aria-expanded="false"><i class="fa-solid fa-gears"></i> Acciones</button>' +
        '<ul class="dropdown-menu dropdown-menu-end">' +
        '<li><a class="dropdown-item" target="_blank" href="' + p.pdf_url + '"><i class="fa-solid fa-file-pdf fa-fw text-danger"></i> Etiqueta A4</a></li>' +
        '<li><a class="dropdown-item" target="_blank" href="' + p.pdf_url + '?formato=ETIQUETA"><i class="fa-solid fa-tag fa-fw text-secondary"></i> Etiqueta 10x15</a></li>' +
        (p.total_fotos > 0
            ? '<li><a class="dropdown-item" href="javascript:void(0)" onclick="ver_fotos(' + parseInt(p.id, 10) + ", '" + p.numero_fmt +
            '\')"><i class="fa-solid fa-camera fa-fw text-secondary"></i> Ver fotos</a></li>'
            : "") +
        retomar +
        acciones_del_dia(p) +
        "</ul></div>";
}

// Reiniciar / eliminar: solo para pallets creados hoy
function acciones_del_dia(p) {
    if (!p.editable_hoy) {
        return "";
    }
    var id = parseInt(p.id, 10);
    return '<li><hr class="dropdown-divider"></li>' +
        '<li><a class="dropdown-item text-warning" href="javascript:void(0)" onclick="reiniciar_pallet(' + id + ", '" + p.numero_fmt +
        '\', recargar_listado)"><i class="fa-solid fa-rotate-left fa-fw"></i> Reiniciar pallet</a></li>' +
        '<li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="eliminar_pallet(' + id + ", '" + p.numero_fmt +
        '\', recargar_listado)"><i class="fa-solid fa-trash-can fa-fw"></i> Eliminar pallet</a></li>';
}

function recargar_listado() {
    tabla_pallets.ajax.reload(null, false);
}

// Si una de las fotos no existe se muestra el espacio vacio
function foto_no_disponible(img) {
    $(img).closest("a").replaceWith('<div class="foto-slot-img"><div class="foto-slot-vacio"><i class="fa-solid fa-camera"></i><span>Sin foto</span></div></div>');
}

function ver_fotos(pallet_id, numero_fmt) {
    var html = "";
    $.each([1, 2], function (i, posicion) {
        var url = base_url("index.php/PalletController/foto/" + pallet_id + "/" + posicion) + "?v=" + Date.now();
        html += '<div class="col-md-6"><div class="fw-bold mb-2">Foto ' + posicion + "</div>" +
            '<a href="' + url + '" target="_blank"><img src="' + url + '" class="img-fluid rounded w-100" alt="Foto ' + posicion + '" ' +
            'onerror="foto_no_disponible(this)"></a></div>';
    });
    $("#Modal_ver_fotos_numero").text(numero_fmt);
    $("#Modal_ver_fotos_contenido").html(html);
    $("#Modal_ver_fotos").modal("show");
}

$(document).ready(function () {
    lista_pallets();
});
