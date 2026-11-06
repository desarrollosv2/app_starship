function busqueda_clientes()
{
    var correo = $("#correo").val();
   
    var url = base_url("index.php/ClientesController/busqueda_clientes/" + correo);
    $.get(url, function (data) 
    {
        $("#tabla_busqueda_track").html(data);
    });
}   


function guardar_comentario()
{
    var correo              =   $("#correo").val();
    var comentario_cliente  =   $("#comentario").val();
   
    var formData;
    var url = base_url("index.php/ClientesController/guardar_comentario/" + $("#correos").val() + "/" + $("#comentario").val());
    formData = new FormData($(".modal_comentario")[0]);
    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () 
        {
            // Show image container
            $("#loader").css("display", "block");
        },
        success: function (response) 
        {
            console.log(response);
            $(".modal-backdrop").remove();
            $("#exampleModal1").modal("hide");
            $("#msg").show();
            $("#correo").val('');
            $("#comentario").val('');
            setTimeout(function () 
            {
                $("#msg").fadeTo("slow", 0.1, function () 
                {
                    $("#msg").alert("close");
                });
            }, 5000);
        },
        complete: function () 
        {
            // Show image container
            $("#loader").css("display", "none");
        },
    });
}   


function modal_comentario_cliente(consignee_email) 
{
    var url = base_url("index.php/ClientesController/modal_comentario_cliente/" + consignee_email);
    $.get(url, function (data) 
    {
        //$("#tabla_busqueda_track").html(data);
        $("#exampleModal1").modal("show");
        $("#consignee_email").val(consignee_email);
    });
}

function actualizar_comentario() 
{
    var consignee_email = $("#consignee_email").val();
    var comentario = $("#comentario").val();

    var formData;
    var url = base_url("index.php/ClientesController/actualizar_comentario/" + comentario + "/" + consignee_email);
    formData = new FormData($(".modal_comentario")[0]);
    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () 
        {
            // Show image container
            $("#loader").css("display", "block");
        },
        success: function (response) 
        {
            console.log(response);
            $(".modal-backdrop").remove();
            $("#exampleModal1").modal("hide");
            $("#msg").show();
            setTimeout(function () 
            {
                $("#msg").fadeTo("slow", 0.1, function () 
                {
                    $("#msg").alert("close");
                });
            }, 5000);
        },
        complete: function () 
        {
            // Show image container
            $("#loader").css("display", "none");
        },
    });
}

function historial_cliente(consignee_email) 
{
    var formData;
    url_destino = "index.php/ClientesController/historial_cliente/" + consignee_email;
    formData = new FormData($(".historial")[0]);

    //hacemos la petición ajax
    $.ajax({
        url: base_url(url_destino),
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) 
        {
            $("#modal_historial").modal("show");
            $("#listado_historial").html(data);
        },
        error: function () {

        },
    });
}