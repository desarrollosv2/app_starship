function base_url(url) 
{
    return window.location.origin + "/app_starship/" + url;
}

function tracking_dte() 
{
  var url = base_url("index.php/Tracking_DTE_Controller/tracking");
  $.get(url, function(data) 
  {
    $("#principal").html(data);  
  });
}

function consulta_tracking() 
{
    
    var formData;
    var valido = "S";
    url_destino = "index.php/Tracking_DTE_Controller/consulta_tracking";
    formData = new FormData($(".qry_warehouse")[0]);
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
            beforeSend: function() {
                // Show image container
                $("#loader").css("display", "block");
            },
            success: function(data) {
                $("#tabla_estatus").html(data);
                //	document.getElementById("tabla_estatus").innerHTML = data;
            },
            complete: function() {
                // Show image container
                $("#loader").css("display", "none");
            },
            //si ha ocurrido un error
            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                //showMessage(message);
            },
        });
    }
}
