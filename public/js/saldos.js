function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}

function subir_saldo() {

    var url = base_url("index.php/SaldosController/consulta_saldos");

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}


function subir_saldos() {
    var formData;
    url_destino = "index.php/SaldosController/cargar_saldos/";
    formData = new FormData($(".up_saldos")[0]);

    $.ajax({
        url: base_url(url_destino),
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response == 1) {

                $("#messageup").html('<div class="alert alert-success alertgreen "><button type="button" class="close">x</button><strong>Proceso ha finalizado correctamente.</strong></div>');
                window.setTimeout(function() {
                    $(".alert").fadeTo(500, 0).slideUp(500, function() {
                        $(this).remove();
                    });
                }, 5000);
                $('.alert .close').on("click", function(e) {
                    $(this).parent().fadeTo(500, 0).slideUp(500);
                });

            } else {

            }
        },
    });

}


function generar_pdf_cxc() {
    var formData;
    url_destino = "index.php/SaldosController/generar_pdf/";
    formData = new FormData($(".up_saldos")[0]);

    $.ajax({
        url: base_url(url_destino),
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {

        },
    });

}

function generar_pdf_cxcss() {
    url_destino = "index.php/SaldosController/generar_pdf/";
    var url = base_url(url_destino);


    // window.location.href = url +"?"+ $("88").serialize();
    //$.notify("Archivo PDF generado.", "success");

    //  window.open(base_url(url),"ventana1","width=350,height=350,scrollbars=no,toolbar=no, titlebar=no, menubar=no")


}