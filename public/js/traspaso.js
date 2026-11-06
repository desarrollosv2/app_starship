function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}

function traspaso_guardar() {
    var formData;
    var valido = "S";
    url_destino = "index.php/Traspaso/traspaso_guardar/";
    formData = new FormData($(".add_manifiesto")[0]);
    if (valido == "S") {
        var message = "";
        $.ajax({
            url: base_url(url_destino),
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                // Show image container
                $("#loader").css("display", "block");
            },
            success: function(data) {
                /*alertify.set("notifier", "position", "top-right");
                alertify.success("Referencia ha sigo guardada correctamente");*/
                $(".modal-backdrop").remove();
                $('#ModalAdd_Traspaso .close').click();

                var url = base_url("index.php/Traspaso/pre_clasificacion");
                $.get(url, function(data) {
                    $("#principal").html(data);
                });
            },
            complete: function() {
                // Show image container
                $("#loader").css("display", "none");
            }
        });
    }
}

function t_preclasifica_lista() {
    var url = base_url("index.php/Traspaso/listado");
    //cargando('contenidoLista');
    $.post(url, function(data) {
        document.getElementById("contenidoLista").innerHTML = data;
    });
}

function mostrar_referencia() {
    $("#Modal_Add_Guia").on("show.bs.modal", function(e) {
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
/*
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
*/
function guardar_guia_traspaso() {
    setTimeout(function() {
        $("#guia").focus();
        $("#guia").select();
    }, 1);
    var formData;
    var valido = "S";
    url_destino = "index.php/Traspaso/guardar_guia/";
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
            beforeSend: function() {
                // Show image container
                $("#loader").css("display", "block");
            },
            success: function(data) {
                console.log(data);
                if (data >= 1) {
                    alertify.set("notifier", "position", "top-right");
                    alertify.error("Se ha actualizado estado de warehouse");
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
            complete: function() {
                // Show image container
                $("#loader").css("display", "none");
            }
        });
    }
}


function consulta_guias_t(id) {
    var formData;
    var valido = "S";
    url_destino = "index.php/Traspaso/consulta_guias/" + id;
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
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            },
        });
    }
}