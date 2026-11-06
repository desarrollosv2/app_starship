function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}

function encabezado_inventario() {}

function guardar_inventario() {
    var formData;
    var valido = "S";
    console.log($("#opc_inventario").val());
    url_destino = "index.php/InventarioController/guardar_inventario/";
    formData = new FormData($(".add_inventario")[0]);
    if (valido == "S") {
        var message = "";
        $.ajax({
            url: base_url(url_destino),
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                alertify.set("notifier", "position", "top-right");
                alertify.success("Inventario creado correctamente");
                var url = base_url("index.php/InventarioController/inventario");
                $.get(url, function(data) {
                    //inventario_lista();
                    $(".modal-backdrop").remove();
                    $("#ModalAdd_Traspaso .close").click();
                    $("#principal").html(data);
                });

                //inventario_lista();
                //inventario_lista();
            },
        });
    }
}

function inventario_lista() {
    //var url = base_url("index.php/InventarioController/listado");
    var url = base_url("index.php/InventarioController/inventario");
    $.post(url, function(data) {
        document.getElementById("contenidoLista").innerHTML = data;
    });
}

function pdf_inventario(id, titulo) {
    var url = base_url(
        "index.php/InventarioController/pdf_inventario/" + id + "/" + titulo
    );

    $.get(url, function(data) {
        ver_pdf_inventario(id);
    });
}

function ver_pdf_inventario(ruta) {
    var url = ruta;
    window.open(
        base_url(url + ".pdf"),
        "ventana1",
        "width=600,height=600,scrollbars=no,toolbar=no, titlebar=no, menubar=no"
    );
}

function consulta_guias_i(id) {
    var formData;
    var valido = "S";
    url_destino = "index.php/InventarioController/consulta_guias/" + id;
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
            success: function(data) {
                $("#modal_guias").modal("show");
                $("#listado_guias").html(data);
            },
            error: function() {
                //message = $("<span class='error'>Ha ocurrido un error.</span>");
                //showMessage(message);
            },
        });
    }
}