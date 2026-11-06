/* Reportes: el servidor define columnas, filas y totales; aqui solo se dibujan. */

function cargar_reporte() {
    var reporte = $("#reporte").data("reporte");
    var url_destino = "index.php/ReportesController/datos/" + reporte;
    var formData = new FormData($(".form_filtros")[0]);

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
                alertify.error(esc_html(data.message));
                return;
            }
            pintar_reporte(data);
        },
        error: function (xhr) {
            alertify.error(mensaje_error(xhr));
        },
        complete: function () {
            $("#loader").css("display", "none");
        },
    });
}

function exportar_reporte() {
    var reporte = $("#reporte").data("reporte");
    window.location.href = base_url("index.php/ReportesController/csv/" + reporte) + "?" + filtros_query();
}

function pintar_reporte(r) {
    var head = "<tr>";
    $.each(r.columnas, function (i, c) {
        head += "<th" + (c.tipo === "num" || c.tipo === "dec" ? ' class="text-end"' : "") + ">" + esc_html(c.titulo) + "</th>";
    });
    $("#rep-head").html(head + "</tr>");

    var body = "";
    $.each(r.filas, function (i, f) {
        var clase = f.fila === "subtotal" ? "fila-subtotal" : ((f.pallets === 0 && f.cajas === 0) ? "fila-cero" : "");
        body += '<tr class="' + clase + '">';
        $.each(r.columnas, function (j, c) {
            body += celda_reporte(c, f, false);
        });
        body += "</tr>";
    });
    $("#rep-body").html(body || '<tr><td colspan="' + r.columnas.length + '" class="text-muted">Sin datos</td></tr>');

    var foot = "<tr>";
    $.each(r.columnas, function (j, c) {
        foot += celda_reporte(c, r.total, true);
    });
    $("#rep-foot").html(foot + "</tr>");

    pintar_kpis(r.total);
}

function celda_reporte(col, fila, es_total) {
    var valor = fila[col.clave];

    if (col.tipo === "pct") {
        var pct = formato_numero(valor, 1) + "%";
        if (es_total) {
            return '<td class="text-end">' + pct + "</td>";
        }
        return '<td><div class="barra-pct"><div class="progress"><div class="progress-bar" style="width:' +
            Math.min(100, Number(valor) || 0) + '%"></div></div><span>' + pct + "</span></div></td>";
    }
    if (col.tipo === "num") {
        return '<td class="text-end">' + formato_numero(valor) + "</td>";
    }
    if (col.tipo === "dec") {
        return '<td class="text-end">' + formato_numero(valor, 1) + "</td>";
    }
    return "<td>" + esc_html(valor) + "</td>";
}

function pintar_kpis(total) {
    var html = "";
    if (total.pallets !== undefined) {
        html += '<div class="kpi"><small>Pallets</small><strong>' + formato_numero(total.pallets) + "</strong></div>";
    }
    if (total.cajas !== undefined) {
        html += '<div class="kpi"><small>Cajas</small><strong>' + formato_numero(total.cajas) + "</strong></div>";
    }
    if (total.pallets_abiertos !== undefined) {
        html += '<div class="kpi"><small>Abiertos</small><strong>' + formato_numero(total.pallets_abiertos) + "</strong></div>" +
            '<div class="kpi"><small>Cerrados</small><strong>' + formato_numero(total.pallets_cerrados) + "</strong></div>";
    }
    $("#kpis").html(html);
}

$(document).ready(function () {
    cargar_reporte();
});
