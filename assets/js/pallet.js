/* Armado de pallet: creacion, escaneo de cajas, limite con consentimiento y cierre. */

var pallet_activo = null;        // datos del pallet en pantalla
var cola_escaneo = [];           // lecturas pendientes: se envian de a una para respetar el orden y el limite
var enviando_escaneo = false;
var modal_limite_abierto = false;
var codigo_pendiente = null;     // caja que espera consentimiento para superar el limite
var audio_ctx = null;

/* ---------- Paso 1: crear / retomar ---------- */

function validar_nuevo_pallet() {
    $("#btn-crear").prop("disabled", !($("#cliente_id").val() && $("#tipo_producto_id").val()));
}

function crear_pallet() {
    var url_destino = "index.php/PalletController/crear";
    var formData = new FormData($(".form_nuevo_pallet")[0]);

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

            var p = data.pallet;
            mostrar_escaneo(p);

            if (data.existente) {
                alertify.warning("Ya existía el pallet N° " + p.numero_fmt + " abierto para " + esc_html(p.cliente) +
                    " / " + esc_html(p.tipo_producto) + ". Se retomó ese pallet.", 8);
                return;
            }

            alertify.success("Pallet N° " + p.numero_fmt + " creado");
            alertify.confirm(
                "Etiqueta del pallet",
                "¿Desea imprimir la etiqueta del pallet N° <b>" + p.numero_fmt + "</b>?",
                function () {
                    window.open(p.pdf_url, "_blank");
                    enfocar_escaneo();
                },
                function () {
                    enfocar_escaneo();
                }
            ).set({ labels: { ok: "Imprimir", cancel: "Ahora no" }, onclosed: enfocar_escaneo });
        },
        error: function (xhr) {
            alertify.error(mensaje_error(xhr));
        },
        complete: function () {
            $("#loader").css("display", "none");
            validar_nuevo_pallet();
        },
    });
}

function retomar_pallet(id) {
    var url_destino = "index.php/PalletController/ver";
    var formData = nuevo_form_data({ pallet_id: id });

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
            mostrar_escaneo(data.pallet);
        },
        error: function (xhr) {
            alertify.error(mensaje_error(xhr));
        },
        complete: function () {
            $("#loader").css("display", "none");
        },
    });
}

// Retoma el pallet leyendo el codigo de barras de su etiqueta PDF (solo si esta abierto)
function retomar_por_etiqueta() {
    if (!$.trim($("#codigo_pallet").val())) {
        return;
    }

    var url_destino = "index.php/PalletController/retomar_por_etiqueta";
    var formData = new FormData($(".form_retomar")[0]);

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
                beep(false);
                alertify.error(esc_html(data.message));
                $("#codigo_pallet").val("").trigger("focus");
                return;
            }
            alertify.success("Pallet N° " + data.pallet.numero_fmt + " retomado");
            mostrar_escaneo(data.pallet);
        },
        error: function (xhr) {
            alertify.error(mensaje_error(xhr));
        },
        complete: function () {
            $("#loader").css("display", "none");
            $("#codigo_pallet").val("");
        },
    });
}

function mostrar_escaneo(p) {
    cola_escaneo = [];
    pintar_pallet(p);
    mostrar_feedback("Listo para escanear", "info");
    $("#paso-config").addClass("d-none");
    $("#paso-escaneo").removeClass("d-none");
    $("#codigo_barras").val("");
    enfocar_escaneo();
}

/* ---------- Pantalla del pallet activo ---------- */

function pintar_pallet(p) {
    pallet_activo = p;

    $("#lbl-numero").text(p.numero_fmt);
    $("#lbl-resumen").text(p.cliente + " · " + p.tipo_producto);
    $("#lbl-contador").text(p.total_cajas);
    $("#lbl-max").text(p.max_cajas);
    $("#btn-pdf").attr("href", p.pdf_url);
    $("#btn-cerrar").prop("disabled", p.total_cajas === 0);
    $("#aviso-excede").toggleClass("d-none", !p.excede_limite);

    var estado = p.total_cajas > p.max_cajas ? "excedido" : (p.total_cajas === p.max_cajas ? "lleno" : "normal");
    var color_barra = { normal: "bg-primary", lleno: "bg-warning", excedido: "bg-danger" };
    $("#contador").attr("class", "contador contador-" + estado);
    $("#barra")
        .css("width", Math.min(100, p.total_cajas * 100 / p.max_cajas) + "%")
        .attr("class", "progress-bar " + color_barra[estado]);

    var html = "";
    if (!p.cajas.length) {
        html = '<tr><td colspan="4" class="text-muted">Sin cajas escaneadas</td></tr>';
    }
    $.each(p.cajas, function (i, c) {
        var n = p.cajas.length - i;
        html += "<tr" + (n > p.max_cajas ? ' class="table-warning"' : "") + ">" +
            "<td>" + n + "</td>" +
            '<td class="font-monospace">' + esc_html(c.codigo_barras) + "</td>" +
            "<td>" + esc_html(formato_fecha(c.scanned_at, true)) + "</td>" +
            '<td class="text-end"><button type="button" class="btn btn-sm btn-link text-danger" title="Quitar caja" ' +
            'onclick="quitar_caja(' + parseInt(c.id, 10) + ')"><i class="fa-solid fa-trash-can"></i></button></td></tr>';
    });
    $("#tabla-cajas").html(html);

    pintar_fotos(p);
}

function mostrar_feedback(mensaje, tipo) {
    $("#feedback").attr("class", "feedback mb-3 feedback-" + tipo).text(mensaje);
}

function enfocar_escaneo() {
    if (pallet_activo && !$(".modal.show").length && !$(".alertify:not(.ajs-hidden)").length) {
        $("#codigo_barras").trigger("focus");
    }
}

function beep(ok) {
    try {
        audio_ctx = audio_ctx || new (window.AudioContext || window.webkitAudioContext)();
        var osc = audio_ctx.createOscillator();
        var gain = audio_ctx.createGain();
        osc.frequency.value = ok ? 880 : 220;
        osc.type = ok ? "sine" : "square";
        gain.gain.value = 0.15;
        osc.connect(gain);
        gain.connect(audio_ctx.destination);
        osc.start();
        osc.stop(audio_ctx.currentTime + (ok ? 0.12 : 0.45));
    } catch (e) { /* sin audio disponible */ }
}

/* ---------- Paso 2: escaneo ---------- */

function escanear_caja() {
    var codigo = $.trim($("#codigo_barras").val());
    $("#codigo_barras").val("");
    if (!codigo || !pallet_activo) {
        return;
    }
    cola_escaneo.push(codigo);
    procesar_cola_escaneo();
}

function procesar_cola_escaneo() {
    if (enviando_escaneo || modal_limite_abierto || !cola_escaneo.length) {
        return;
    }
    enviando_escaneo = true;

    var codigo = cola_escaneo.shift();
    var url_destino = "index.php/PalletController/escanear";
    var formData = nuevo_form_data({ pallet_id: pallet_activo.id, codigo_barras: codigo });

    // Sin #loader: el operador escanea seguido y el overlay estorbaria
    $.ajax({
        url: base_url(url_destino),
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {
            if (data.success) {
                pintar_pallet(data.pallet);
                beep(true);
                mostrar_feedback("✔ Caja " + data.codigo_barras + " agregada (" + data.pallet.total_cajas + ")", "ok");
                if (data.limite_alcanzado) {
                    abrir_modal_limite(null);
                }
                return;
            }

            beep(false);
            if (data.requiere_consentimiento) {
                abrir_modal_limite(codigo);
                return;
            }
            mostrar_feedback("✖ " + data.message, "error");
        },
        error: function (xhr) {
            beep(false);
            mostrar_feedback("✖ " + mensaje_error(xhr), "error");
        },
        complete: function () {
            enviando_escaneo = false;
            procesar_cola_escaneo();
            enfocar_escaneo();
        },
    });
}

/* ---------- Limite de cajas ---------- */

// codigo: caja rechazada por el limite (null si solo se aviso que el pallet se completo)
function abrir_modal_limite(codigo) {
    modal_limite_abierto = true;
    codigo_pendiente = codigo;

    var mensaje = codigo
        ? "El pallet N° <b>" + pallet_activo.numero_fmt + "</b> ya tiene <b>" + pallet_activo.max_cajas +
        " cajas</b>. ¿Desea agregar la caja <b>" + esc_html(codigo) + "</b> de todas formas?"
        : "El pallet N° <b>" + pallet_activo.numero_fmt + "</b> alcanzó el máximo de <b>" + pallet_activo.max_cajas +
        " cajas</b>. ¿Qué desea hacer?";

    $("#Modal_limite_mensaje").html(mensaje);
    $("#Modal_limite").modal("show");
}

function cerrar_modal_limite() {
    $("#Modal_limite").modal("hide");
    modal_limite_abierto = false;
}

function consentir_limite() {
    var url_destino = "index.php/PalletController/consentir";
    var formData = nuevo_form_data({ pallet_id: pallet_activo.id });

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
            pintar_pallet(data.pallet);
            if (codigo_pendiente) {
                cola_escaneo.unshift(codigo_pendiente);
            }
            codigo_pendiente = null;
            cerrar_modal_limite();
            alertify.warning("Puede seguir escaneando por sobre el límite.");
            mostrar_feedback("Puede seguir escaneando por sobre el límite.", "info");
            procesar_cola_escaneo();
        },
        error: function (xhr) {
            alertify.error(mensaje_error(xhr));
        },
        complete: function () {
            $("#loader").css("display", "none");
            enfocar_escaneo();
        },
    });
}

function cancelar_limite() {
    var descartadas = cola_escaneo.length;
    if (codigo_pendiente) {
        mostrar_feedback("Caja " + codigo_pendiente + " no agregada" +
            (descartadas ? " (y " + descartadas + " lecturas pendientes descartadas)" : "") + ".", "error");
    }
    cola_escaneo = [];
    codigo_pendiente = null;
    cerrar_modal_limite();
    enfocar_escaneo();
}

function cerrar_desde_limite() {
    cola_escaneo = [];
    codigo_pendiente = null;
    cerrar_modal_limite();
    cerrar_pallet(true);
}

/* ---------- Quitar caja / cerrar pallet ---------- */

function quitar_caja(caja_id) {
    var caja = $.grep(pallet_activo.cajas, function (c) { return parseInt(c.id, 10) === caja_id; })[0];
    if (!caja) {
        return;
    }

    alertify.confirm(
        "Quitar caja",
        "¿Quitar la caja <b>" + esc_html(caja.codigo_barras) + "</b> del pallet?",
        function () {
            var url_destino = "index.php/PalletController/quitar_caja";
            var formData = nuevo_form_data({ pallet_id: pallet_activo.id, caja_id: caja_id });

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
                    pintar_pallet(data.pallet);
                    alertify.success("Caja " + esc_html(caja.codigo_barras) + " quitada");
                },
                error: function (xhr) {
                    alertify.error(mensaje_error(xhr));
                },
                complete: function () {
                    $("#loader").css("display", "none");
                    enfocar_escaneo();
                },
            });
        },
        function () {
            enfocar_escaneo();
        }
    ).set({ labels: { ok: "Quitar", cancel: "Cancelar" }, onclosed: enfocar_escaneo });
}

// sin_confirmar: true cuando el usuario ya eligio "Cerrar pallet" en el modal del limite
function cerrar_pallet(sin_confirmar) {
    // Sin las 2 fotografias no se puede cerrar: se piden en ese momento
    if (!fotos_completas()) {
        abrir_modal_fotos();
        return;
    }

    if (!sin_confirmar) {
        alertify.confirm(
            "Cerrar pallet N° " + pallet_activo.numero_fmt,
            "Se cerrará con <b>" + pallet_activo.total_cajas + " cajas</b>. Luego no podrá agregar ni quitar cajas.",
            function () {
                cerrar_pallet(true);
            },
            function () {
                enfocar_escaneo();
            }
        ).set({ labels: { ok: "Cerrar pallet", cancel: "Cancelar" }, onclosed: enfocar_escaneo });
        return;
    }

    var url_destino = "index.php/PalletController/cerrar";
    var formData = nuevo_form_data({ pallet_id: pallet_activo.id });

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
                if (data.requiere_fotos) {
                    pintar_pallet(data.pallet);
                    abrir_modal_fotos();
                }
                alertify.error(esc_html(data.message));
                return;
            }
            pallet_activo = null;
            alertify.success("Pallet N° " + data.pallet.numero_fmt + " cerrado con " + data.pallet.total_cajas + " cajas");
            setTimeout(function () {
                window.location.href = base_url("index.php/PalletController");
            }, 1500);
        },
        error: function (xhr) {
            alertify.error(mensaje_error(xhr));
        },
        complete: function () {
            $("#loader").css("display", "none");
        },
    });
}

/* ---------- Fotografias (2 obligatorias para cerrar) ---------- */

var foto_posicion = null;   // posicion (1 o 2) que se esta cargando

function fotos_completas() {
    return !!(pallet_activo && pallet_activo.fotos && pallet_activo.fotos[1] && pallet_activo.fotos[2]);
}

// Pinta los 2 espacios de foto (panel y Modal_fotos) y las acciones del dia
function pintar_fotos(p) {
    $.each([1, 2], function (i, posicion) {
        var url = p.fotos ? p.fotos[posicion] : null;
        var $slot = $('.foto-slot[data-posicion="' + posicion + '"]');

        $slot.toggleClass("foto-slot-ok", !!url);
        $slot.find("img").attr("src", url || "").toggleClass("d-none", !url);
        $slot.find(".foto-slot-vacio").toggleClass("d-none", !!url);
        $slot.find(".foto-slot-texto").text((url ? "Cambiar foto " : "Tomar foto ") + posicion);
    });

    $("#btn-cerrar-con-fotos").prop("disabled", !fotos_completas());
    mensaje_modal_fotos();
    $("#acciones-dia").toggleClass("d-none", !p.editable_hoy);
}

// Mensaje de Modal_fotos segun las fotos que faltan (se actualiza con cada foto cargada)
function mensaje_modal_fotos() {
    var faltan = (pallet_activo.fotos[1] ? 0 : 1) + (pallet_activo.fotos[2] ? 0 : 1);
    $("#Modal_fotos_mensaje").html(faltan === 0
        ? "Fotografías completas. Ya puede cerrar el pallet N° <b>" + pallet_activo.numero_fmt + "</b>."
        : "Para cerrar el pallet N° <b>" + pallet_activo.numero_fmt + "</b> debe cargar las 2 fotografías. " +
        (faltan === 2 ? "Faltan las 2." : "Falta 1."));
}

function abrir_modal_fotos() {
    mensaje_modal_fotos();

    // Si viene de "Cerrar pallet" en el modal del limite, se espera a que ese modal termine de cerrarse
    if ($("#Modal_limite").is(":visible")) {
        $("#Modal_limite").one("hidden.bs.modal", function () {
            $("#Modal_fotos").modal("show");
        });
        return;
    }
    $("#Modal_fotos").modal("show");
}

function cerrar_con_fotos() {
    $("#Modal_fotos").modal("hide");
    cerrar_pallet(true);
}

function tomar_foto(posicion) {
    foto_posicion = posicion;
    $("#input_foto").val("").trigger("click");
}

function subir_foto(input) {
    var archivo = input.files && input.files[0];
    if (!archivo || !pallet_activo || !foto_posicion) {
        return;
    }
    var posicion = foto_posicion;

    $("#loader").css("display", "block");
    comprimir_imagen(archivo, function (imagen, nombre) {
        var url_destino = "index.php/PalletController/subir_foto";
        var formData = nuevo_form_data({ pallet_id: pallet_activo.id, posicion: posicion });
        formData.append("foto", imagen, nombre);

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
                pintar_pallet(data.pallet);
                alertify.success("Foto " + posicion + " cargada");
                if (fotos_completas() && $("#Modal_fotos").hasClass("show")) {
                    alertify.message("Fotografías completas: ya puede cerrar el pallet.");
                }
            },
            error: function (xhr) {
                alertify.error(mensaje_error(xhr));
            },
            complete: function () {
                $("#loader").css("display", "none");
                $("#input_foto").val("");
                enfocar_escaneo();
            },
        });
    });
}

// Reduce la foto de la camara (max. 1600 px, JPEG) para que suba rapido; si no se puede, se envia la original
function comprimir_imagen(archivo, callback) {
    var LADO_MAXIMO = 1600;
    var nombre_jpg = (archivo.name || "foto").replace(/\.[^.]+$/, "") + ".jpg";

    if (!/^image\//.test(archivo.type) || !window.FileReader || !HTMLCanvasElement.prototype.toBlob) {
        callback(archivo, archivo.name || "foto.jpg");
        return;
    }

    var lector = new FileReader();
    lector.onerror = function () {
        callback(archivo, archivo.name);
    };
    lector.onload = function (e) {
        var img = new Image();
        img.onerror = function () {
            callback(archivo, archivo.name);
        };
        img.onload = function () {
            var escala = Math.min(1, LADO_MAXIMO / Math.max(img.width, img.height));
            var canvas = document.createElement("canvas");
            canvas.width = Math.round(img.width * escala);
            canvas.height = Math.round(img.height * escala);
            canvas.getContext("2d").drawImage(img, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(function (blob) {
                if (blob) {
                    callback(blob, nombre_jpg);
                } else {
                    callback(archivo, archivo.name);
                }
            }, "image/jpeg", 0.85);
        };
        img.src = e.target.result;
    };
    lector.readAsDataURL(archivo);
}

/* ---------- Reiniciar / eliminar el pallet activo (solo el mismo dia) ---------- */

function reiniciar_pallet_activo() {
    reiniciar_pallet(pallet_activo.id, pallet_activo.numero_fmt, function (data) {
        cola_escaneo = [];
        pintar_pallet(data.pallet);
        mostrar_feedback("Pallet reiniciado: listo para escanear", "info");
        enfocar_escaneo();
    });
}

function eliminar_pallet_activo() {
    eliminar_pallet(pallet_activo.id, pallet_activo.numero_fmt, function () {
        pallet_activo = null;
        setTimeout(function () {
            window.location.href = base_url("index.php/PalletController");
        }, 1500);
    });
}

$(document).ready(function () {
    validar_nuevo_pallet();

    // Al terminar de cerrarse el modal del limite o el de fotos, el foco vuelve al campo de escaneo
    $("#Modal_limite, #Modal_fotos").on("hidden.bs.modal", function () {
        enfocar_escaneo();
    });

    // Si el operador hace clic fuera, el foco vuelve al campo de escaneo
    $("#paso-escaneo").on("click", function (e) {
        if (!$(e.target).closest("a, button, input").length) {
            enfocar_escaneo();
        }
    });

    var retomar_id = parseInt($("#pantalla-pallet").data("retomar"), 10);
    if (retomar_id > 0) {
        retomar_pallet(retomar_id);
    } else {
        // Listo para escanear la etiqueta de un pallet apenas se abre la pantalla
        $("#codigo_pallet").trigger("focus");
    }
});
