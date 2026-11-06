/* Funciones comunes a todas las pantallas. BASE_URL se define en templates/footer.php */

function base_url(ruta) {
    return BASE_URL + (ruta || "");
}

// FormData con el token CSRF, para llamadas que no salen de un <form>
function nuevo_form_data(campos) {
    var formData = new FormData();
    formData.append($('meta[name="csrf-name"]').attr("content"), $('meta[name="csrf-hash"]').attr("content"));
    $.each(campos || {}, function (clave, valor) {
        formData.append(clave, valor);
    });
    return formData;
}

// Filtros del formulario sin el token CSRF (para armar URLs de descarga)
function filtros_query() {
    var csrf = $('meta[name="csrf-name"]').attr("content");
    return $.param($(".form_filtros").serializeArray().filter(function (f) {
        return f.name !== csrf && f.value !== "";
    }));
}

function mensaje_error(xhr) {
    if (xhr.status === 403) {
        return "La sesión expiró. Recargue la página.";
    }
    return "Error de comunicación con el servidor.";
}

function esc_html(texto) {
    return $("<div/>").text(texto === null || texto === undefined ? "" : String(texto)).html();
}

// 'Y-m-d H:i:s' -> 'dd/mm/yyyy HH:MM' (o solo 'HH:MM')
function formato_fecha(fecha, solo_hora) {
    if (!fecha) {
        return "";
    }
    var m = String(fecha).match(/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/);
    if (!m) {
        return fecha;
    }
    return solo_hora ? m[4] + ":" + m[5] : m[3] + "/" + m[2] + "/" + m[1] + " " + m[4] + ":" + m[5];
}

function formato_numero(numero, decimales) {
    return Number(numero || 0).toLocaleString("es", {
        minimumFractionDigits: decimales || 0,
        maximumFractionDigits: decimales || 0
    });
}

// Menu lateral: en escritorio se contrae, en movil se superpone
function toggle_sidebar() {
    if (window.matchMedia("(max-width: 991.98px)").matches) {
        $("body").toggleClass("sidebar-visible");
    } else {
        $("body").toggleClass("sidebar-oculto");
    }
}

// Textos de DataTables en espanol
var DATATABLES_ES = {
    lengthMenu: "Mostrar _MENU_ registros",
    search: "Buscar:",
    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
    infoEmpty: "Sin registros",
    infoFiltered: "(filtrado de _MAX_)",
    zeroRecords: "No se encontraron resultados",
    emptyTable: "Sin datos",
    loadingRecords: "Cargando...",
    processing: "Procesando...",
    paginate: { first: "«", last: "»", next: "›", previous: "‹" }
};

/* ---------- Reiniciar / eliminar pallet (solo el mismo dia de su creacion) ---------- */

// Quita todas las cajas y fotos; el pallet queda abierto y vacio con el mismo numero
function reiniciar_pallet(pallet_id, numero_fmt, al_terminar) {
    alertify.confirm(
        "Reiniciar pallet N° " + numero_fmt,
        "Se quitarán <b>todas las cajas y las fotografías</b> del pallet N° <b>" + esc_html(numero_fmt) +
        "</b> y quedará <b>abierto y vacío</b> con el mismo número.<br>Esta acción no se puede deshacer.",
        function () {
            var url_destino = "index.php/PalletController/reiniciar";
            var formData = nuevo_form_data({ pallet_id: pallet_id });

            $.ajax({
                url: base_url(url_destino),
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    $("#loader").css("display", "block");
                },
                success: function (data) {
                    if (!data.success) {
                        alertify.error(esc_html(data.message), 8);
                        return;
                    }
                    alertify.success("Pallet N° " + esc_html(numero_fmt) + " reiniciado");
                    if (al_terminar) {
                        al_terminar(data);
                    }
                },
                error: function (xhr) {
                    alertify.error(mensaje_error(xhr));
                },
                complete: function () {
                    $("#loader").css("display", "none");
                },
            });
        },
        function () { }
    ).set({ labels: { ok: "Reiniciar", cancel: "Cancelar" } });
}

// Borrado logico: el pallet deja de verse en listados y reportes y sus codigos de caja quedan libres
function eliminar_pallet(pallet_id, numero_fmt, al_terminar) {
    alertify.confirm(
        "Eliminar pallet N° " + numero_fmt,
        "El pallet N° <b>" + esc_html(numero_fmt) + "</b> dejará de aparecer en listados y reportes, " +
        "y sus cajas quedarán libres para escanearse en otro pallet.<br>Esta acción no se puede deshacer.",
        function () {
            var url_destino = "index.php/PalletController/eliminar";
            var formData = nuevo_form_data({ pallet_id: pallet_id });

            $.ajax({
                url: base_url(url_destino),
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    $("#loader").css("display", "block");
                },
                success: function (data) {
                    if (!data.success) {
                        alertify.error(esc_html(data.message), 8);
                        return;
                    }
                    alertify.success("Pallet N° " + esc_html(numero_fmt) + " eliminado");
                    if (al_terminar) {
                        al_terminar(data);
                    }
                },
                error: function (xhr) {
                    alertify.error(mensaje_error(xhr));
                },
                complete: function () {
                    $("#loader").css("display", "none");
                },
            });
        },
        function () { }
    ).set({ labels: { ok: "Eliminar", cancel: "Cancelar" } });
}

$(document).ready(function () {
    alertify.set("notifier", "position", "top-right");
    alertify.defaults.glossary.ok = "Aceptar";
    alertify.defaults.glossary.cancel = "Cancelar";
    alertify.defaults.theme.ok = "btn btn-naranja";
    alertify.defaults.theme.cancel = "btn btn-light";
});
