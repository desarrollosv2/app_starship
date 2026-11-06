function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}


function ia_clasificador(id) {
       
    var url = base_url("index.php/ArancelController/asignar_codigos_arancelarios/"+id);
    $.post(url, function(data) {
       // $("#principal").html(data);
    });
}
