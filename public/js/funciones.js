function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}


function validar_usuario(event) {
    //event.preventDefault();
    alert("validar");
    var url = base_url("index.php/login/validar_login");
    var email = $("#correo").val();
    var password = $("#clave").val();
    $.ajax({
        url: url,
        type: "POST",
        data: { email: correo, password: clave },
        success: function(datos) {}
    });

}

function salir() {
    var url = base_url("index.php/login/cerrar");

    $.get(url, function(data) {});
    location.href = base_url("");
}

