function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}

function all_tracks() {

    var url = base_url("index.php/Gestion/consulta_all_tracks");
    $.get(url, function (data) {
        $("#principal").html(data);
    });
}