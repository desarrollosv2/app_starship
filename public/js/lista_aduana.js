function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}

function lista_aduana() {
   
    var url = base_url("index.php/AduanaController/lista_awb");
    $.get(url, function (data) {
        $("#principal").html(data);

        var url = base_url("index.php/AduanaController/lista_awb_first");
        $.get(url, function (data) {
            $("#awb_list").html(data);
        });
    });
}

function cargar_manifiesto() {
    var url = base_url("index.php/AduanaController/cargar_archivo_fast/");
    $.get(url, function (data) {
        $("#upfileModal").modal("show");
    });
}




function listado_warehouse() {
    var url = base_url("index.php/AduanaController/listado_warehouse");
    $.get(url, function (data) {
        document.getElementById("contenidoLista").innerHTML = data;
    });
}

function filtro_manifiesto() {
    $("#modal_filtro_manifiesto").modal("show");
}

function manifiesto_listado() {
    if (!$('#input_filtro').val().length) {
        alertify.error("Introduzca patrón de busqueda");
        return false;
    }

    var opc = 0;
    if ($("#rb-guia").is(':checked')) {
        opc = 1;
    }
    else {
        opc = 2;
    }

    $(".modal-backdrop").remove();
    $("#modal_filtro_manifiesto").modal("hide");
    var formData;
    url_destino = "index.php/AduanaController/manifiesto_listado" + "/" + $("#input_filtro").val() + "/" + opc;



    $.ajax({
        url: base_url(url_destino),
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,

        beforeSend: function () {
            // Show image container
            $("#loader").css("display", "block");
        },
        success: function (data) {
            $("#principal").html(data);
        },
        complete: function () {
            $("#loader").css("display", "none");
        },

        error: function () {
            message = $("<span class='error'>Ha ocurrido un error.</span>");
        },
    });

}

function consulta_awb() {
    /** busca en encabezados tabla preclasificacion */
    inicio = $("#fecha-inicio").val();
    fin = $("#fecha-fin").val();
    opcion = $("#opciones").val();
    buscar = $("#buscar-manifiesto").val();

    if (!inicio) {
        inicio = "0";
    }

    if (!fin) {
        fin = "0";
    }

    if (!buscar) {
        buscar = "0";
    };

    var url = base_url("index.php/AduanaController/consulta_awb/" + inicio + "/" + fin + "/" + opcion + "/" + buscar);
    $.get(url, function (data) {
        $("#awb_list").html(data);
    });
}

function Alista_warehouse_fast(id, master, correla) {
    
    //  console.log(id,master,correla,'subguias');
    /** busca los detalles (warehouse de una master) tabla manifiesto */
    master_number = master;
    var url = base_url("index.php/AduanaController/Amanifiesto/" + id + "/" + master + "/" + correla);

    $.get(url, function (data) {
        //console.log(data);
        $("#principal").html(data);
        $("#master-number").html(master_number);
        $("#id-master").val(id);
        $("#name_master").val(master);
        document.getElementById("master-number").innerHTML = master_number;
    });
}

function Alista_warehouse_fast2() {
    alert("02");
    var id = $("#id_manifiesto").val();
    var master = $("#master").val();
    /** busca los detalles (warehouse de una master) tabla manifiesto */
    master_number = master;
    var url = base_url("index.php/AduanaController/Amanifiesto/" + id + "/" + master + "/" + 0);

    $.get(url, function (data) {
        $("#principal").html(data);
        $("#master-number").html(master_number);
        $("#id-master").val(id);
        $("#name_master").val(master);
        document.getElementById("master-number").innerHTML = master_number;
    });
}

function modal_add_master() {
    $("#modal_crear_master").modal("show");
}

function guardar_master_fast() {
    var master = $("#master_fast").val();
    var transportista = $("#transportista_fast").val();

    var url = base_url("index.php/Cargar_ManAduanaController/guardar_master_fast/" + master + "/" + transportista);
    $.get(url, function (data) {
        $(".modal-backdrop").remove();
        $("#modal_crear_master").modal("hide");
        $("#msg").show();
        setTimeout(function () {
            $("#msg").fadeTo("slow", 0.1, function () {
                $("#msg").alert("close");
            });
        }, 5000);
        lista_aduana();
    });
}

function trans_wh_fast(id) {
    $("#modal_asignar_trasportista").modal("show");
    $("#id_trasportista").val(id);
    console.log(id);
}

function guardar_transportista_fast() {
    var id = 0;
    id = $("#id_trasportista").val();

    var transportista = $("#Atransportista_fast").val();

    var url = base_url("index.php/AduanaController/guardar_transportista_fast/" + id + "/" + transportista);
    $.get(url, function (data) {
        $(".modal-backdrop").remove();
        $("#modal_asignar_trasportista").modal("hide");
        $("#msg").show();
        setTimeout(function () {
            $("#msg").fadeTo("slow", 0.1, function () {
                $("#msg").alert("close");
            });
        }, 5000);
        lista_aduana();
    });
}

function info_wh_fast(id) {
    $("#modal_info_fast").modal("show");
    var url = base_url("index.php/AduanaController/info_wh_fast/" + id);
    $.get(url, function (data) {
        //$("#principal").html(data);
    });

}

function editar_wh_fast(id) {
    var id_master = $('#id-master').val();
    var name_master = $('#name_master').val();

    console.log(id_master, name_master, 'juano');

    var url = base_url("index.php/AduanaController/editar_wh_fast/" + id);
    $.get(url, function (data) {
        $("#principal").html(data);

        if (id > 0) {
            $("#atras2").css('display', 'none');
            $("#atras1").css('display', 'inline');
            $('#id').val(id);
            $('#tracking_number').prop('readonly', true);
            $("h3#title").html("Editar Guia");
        }
        else {
            //id_manifiesto
            $("#atras2").css('display', 'inline');
           // $("#atras1").css('display', 'none');
            $('#tracking_number').prop('readonly', false);
            $('#id_manifiesto').val(id_master);
            $('#master').val(name_master);
            $("h3#title").html("Agregar Guia");
        }
    });
}

function guardar_wh_fast() {
    var formData;
    url_destino = "index.php/AduanaController/guardar_wh_fast/";
    formData = new FormData($(".add_wh")[0]);

    var message = "";
    $.ajax({
        url: base_url(url_destino),
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {

        },
        success: function (data) {
           
            alertify.set("notifier", "position", "top-right");
            alertify.success("La Guia ha sido actualizado correctamente");

           

        },
        complete: function () {
            var id = $("#id_manifiesto").val();
            var master = $("#master").val();
            Alista_warehouse_fast(id, master, 0);
        }
    });
}

function eliminar_wh_fast(id) {
    alertify.confirm("Esta seguro de eliminar esta Guia?",
        function () {
            var url = base_url("index.php/AduanaController/eliminar_wh_fast/" + id);

            $.get(url, function (data) {
                alertify.set("notifier", "position", "top-right");
                alertify.success("La Guia ha sido eliminada correctamente");
                $(".modal-backdrop").remove();
                $("#form .close").click();
                Alista_warehouse_fast($("#id-master").val(), $("#name_master").val(), 0);
            });
        },
        function () {
            alertify.error("Cancelado");
        }
    )

        .set({ title: "Eliminar " })
        .set({ labels: { ok: "Aceptar", cancel: "Cancelar" } });
}

function update_wh_fast() {
    var formData;
    var id_master = $('#id_precla').val();
    var name_master = $('#manifiesto').val();
    url_destino = "index.php/AduanaController/update_wh_fast/";
    formData = new FormData($(".update_wh_fast")[0]);

    var message = "";
    $.ajax({
        url: base_url(url_destino),
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {

        },
        success: function (data) {
            Alista_warehouse_fast(id_master, name_master, 0);

        },
        complete: function () {

        }
    });
}

function upload_fast() {
    var formData;
    var url = base_url("index.php/AduanaController/upload_fast/");
    formData = new FormData($(".up_wh_fast")[0]);
    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            // Show image container
            $("#loader").css("display", "block");
        },
        success: function (response) {
            console.log(response);
            $(".modal-backdrop").remove();
            $("#upfileModal").modal("hide");
            $("#msg").show();
            setTimeout(function () {
                $("#msg").fadeTo("slow", 0.1, function () {
                    $("#msg").alert("close");
                });
            }, 5000);
            lista_aduana();
        },
        complete: function () {
            // Show image container
            $("#loader").css("display", "none");

        }
    });
}

function lista_manifiesto_refer(id, master, ref) {
    console.log(id, master, ref, 'subguias222');
    /** busca los detalles (warehouse de una master) tabla manifiesto */
    master_number = master;
    var url = base_url("index.php/AduanaController/lista_manifiesto_refer/" + id + "/" + master + "/" + ref);

    $.get(url, function (data) {
        //console.log(data);
        $("#principal").html(data);
        $("#master-number").html(master_number);
        $("#id-master").val(id);
        $("#name_master").val(master);
        document.getElementById("master-number").innerHTML = master_number;
    });
}


function degroup_xml() {

    master = $("#name_master").val();
    id = $("#id-master").val();
    var url = base_url("index.php/AduanaController/degroup_xml/" + id + "/" + master);

    $.get(url, function (data) {

    });
}


