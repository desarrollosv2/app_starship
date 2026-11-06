function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}

function listado_customer() {
    var url = base_url("index.php/Customers/listado_customer");
    $.get(url, function(data) {
        document.getElementById("contenidoLista").innerHTML = data;
    });
}

function guardar_password() {
    var flag = 1;
    if ($("#password").val() == "") {
        $("#msg_pass").show("slow");
        flag = 0;
    }
    if ($("#password_repeat").val() == "") {
        $("#msg_repetir").show("slow");
        flag = 0;
    }
    if (flag == 1) {
        if ($("#password").val() == $("#password_repeat").val()) {
            var formData;
            var url = base_url("index.php/Customers/guardar_password/");
            formData = new FormData($(".form_cambiar_pass")[0]);
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response > 0) {
                        alertify.set("notifier", "position", "top-right");
                        alertify.success("La clave ha sido guardada correctamente");
                        $("#cambiar_clave_modal").modal("hide");
                    } else {
                        alertify.set("notifier", "position", "top-right");
                        alertify.error("Error, no ha sido posible guardar los cambios");
                    }
                },
                error: function(error) {
                    alertify.error("Error, no ha sido posible guardar los cambios");
                },
            });
        } else {
            $("#msg_cambiar_clave").show("slow");
        }
    }
}

/**/
function guardar_password2() {
    if ($("#password").val() == $("#password_repeat").val()) {
        var formData;
        var url = base_url("index.php/Customers/guardar_password2/");
        formData = new FormData($(".form_cambiar_pass2")[0]);
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                alert('rsl ' + response);
                if (response > 0) {
                    alertify.set("notifier", "position", "top-right");
                    alertify.success("La clave ha sido guardada correctamente");

                } else {
                    alertify.set("notifier", "position", "top-right");
                    alertify.error("Error, no ha sido posible guardar los cambios");
                }
            },
            error: function(error) {
                alertify.error("Error, no ha sido posible guardar los cambios");
            },
        });
    } else {
        alertify.set("notifier", "position", "top-right");
        alertify.error("Error, no coinciden las contraseñas");
    }
}


function guardar_email() {
    if (
        $("#correo").val().indexOf("@", 0) == -1 ||
        $("#correo").val().indexOf(".", 0) == -1
    ) {
        $("#msg_correo").show("slow");
        return false;
    } else {
        var formData;
        var url = base_url("index.php/Customers/guardar_email/");
        formData = new FormData($(".form_cambiar_correo")[0]);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response > 0) {
                    alertify.set("notifier", "position", "top-right");
                    alertify.success(
                        "El correo electrónico ha sido guardado correctamente"
                    );
                    $(".modal-backdrop").remove();
                    $("#cambiar_correo_modal").modal("hide");

                } else {
                    alertify.set("notifier", "position", "top-right");
                    alertify.error("Error, no ha sido posible guardar los cambios");
                }
            },
            error: function(error) {
                alertify.error("Error, no ha sido posible guardar los cambios");
            },
        });
    }
}

function guardar_sucursal() {


    var formData;
    var url = base_url("index.php/Customers/guardar_sucursal/");
    formData = new FormData($(".form_cambiar_sucursal")[0]);

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response > 0) {
                alertify.set("notifier", "position", "top-right");
                alertify.success(
                    "La sucursal de retiro  ha sido guardada correctamente"
                );
                $(".modal-backdrop").remove();
                $("#cambiar_sucursal_modal").modal("hide");
                info_cuenta();
            } else {
                alertify.set("notifier", "position", "top-right");
                alertify.error("Error, no ha sido posible guardar los cambios");
            }
        },
        error: function(error) {
            alertify.error("Error, no ha sido posible guardar los cambios");
        },
    });
}


function lista_prealerta() {
    var url = base_url("index.php/Customers/listado_customer");
    $.get(url, function(data) {
        document.getElementById("contenidoLista").innerHTML = data;
    });
}


/*js table*/
function mostrar_lista_prealerta() {
    //url controler
    var url = base_url("index.php/Customers/mostrar_lista_prealerta");
    $.get(url, function(data) {
        $('#list_prealert').html(data);
        //document.getElementById("list_prealert").innerHTML = data;
    });

}


function guardar_prealerta() {
    var formData;
    //funcion al mi controlador
    var url = base_url("index.php/Customers/guardar_prealerta/");
    formData = new FormData($(".form_prealerta")[0]);

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log("e " + response);
            if (response > 0) {
                alertify.set("notifier", "position", "top-right");
                alertify.success(
                    "Prealerta guardada correctamente"
                );

                $(".file_remove").click();
                $('#form_prealerta')[0].reset();

                $(".modal-backdrop").remove();
                $("#prealerta_modal").modal("hide");
                mostrar_lista_prealerta();

            } else {
                alertify.set("notifier", "position", "top-right");
                alertify.error("Error, no ha sido posible guardar los cambios");
            }
        },
        error: function(error) {
            alertify.error("Error, no ha sido posible guardar los cambios.");
        },
    });
}


/*function lista_prealerta() {
	/*url controler*/
/*	var url = base_url("index.php/Customers/listado_prealerta");
	$.get(url, function (data) {
		document.getElementById("contenidoLista").innerHTML = data;
	});
}*/



function modal_prealerta(id) {

    if (id > 0) {
        var url = base_url("index.php/Customers/consulta_prealerta/" + id);
        $.get(url, function(data) {
            resp = JSON.parse(data);
            //imgfactura
            $("#numero_tracking").val(resp[0].ntracking);
            $("#tienda_compra").val(resp[0].tcompraste);
            $("#valor_paquete").val(resp[0].vpaquete);
            $("#comment").val(resp[0].desc_paquete);
            $("#comp_courier").val(resp[0].id_courier);

            $("#id_prealerts").val(resp[0].id_prealert);
            //$("#comp_courier").prop('disabled', true);
            /*$("#numero_tracking").val(resp[0].ntracking);
            $("#numero_tracking").val(resp[0].ntracking);*/
            $("#prealerta_modal").modal("show");
        });
    } else {
        var select = $("#comp_courier").val();
        //console.log(select);
        $("#numero_tracking").val("");
        $("#tienda_compra").val("");
        $("#valor_paquete").val("");
        $("#comment").val("");
        $("#prealerta_modal").modal("show");

    }

}




function busqueda_prealer() {
    var bs = $("#busqueda").val();
    if (bs == "") {
        $("#busqueda").focus();
    }
    var formData;
    //funcion al mi controlador
    var url = base_url("index.php/Customers/busqueda_prealerta");

    formData = new FormData($("#busqueda_prealerta")[0]);
    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            document.getElementById("contenidoLista").innerHTML = data;
        },
        error: function(error) {
            alertify.error("Error de ajax");
        },
    });

}

function nbuscar() {
    var bs = $("input#busqueda").val();
    if (bs == "") {
        lista_prealerta();
    }
}

function confirmar_prealerta(id) {
    alertify.confirm('Confirmar Prealerta', 'Desea confirmar revisión de prealerta?',
        function() {
            var url = base_url("index.php/Customers/confirmar_prealerta/" + id);
            $.get(url, function(data) {
                //resp =  JSON.parse(data);

                if (data > 0) {
                    alertify.success('Revisión confirmada');
                    mostrar_lista_prealerta()
                } else {
                    alertify.error('Revisión cancelada');
                }

            });
        },
        function() {
            alertify.error('Revisión cancelada');
        });

}

function ver_estatus() {
    $("#ver_estatus_modal").modal("show");
}



function crear_guia_express_bk() {


    id = $('#order_number').val();
    alert(id);

    var formData;

    url_destino = "index.php/Customers/obtener_guia/" + id;
    formData = new FormData($(".guia_ex")[0]);


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
            var obj = JSON.parse(data);

            // alert('exito' + obj.exito);
            //lert('guia' + obj.guias.numero);
            mensaje = obj.mensaje;
            recolecta = obj.recolecta;
            guia = obj.guias.numero;
            alert(guia);
            $.each(obj, function(index, item) {
                // alert(item.guias.numero);
                // alert(item);
            });
            //  alert('recolecta' + obj.recolecta);
            $(".modal-backdrop").remove();
            $("#guia_express_modal").modal("hide");
            $("#info_modal_express").modal("show");
            $("#mensaje").text(mensaje);
            $("#recolecta").text(recolecta);



        },
        error: function() {
            message = $("<span class='error'>Ha ocurrido un error.</span>");
            showMessage(message);
        },
    });

}



function lista_servicios_express(id) {

    var url = base_url(
        "index.php/Customers/lista_servicios"
    );


    $.get(url, function(data) {
        //alert(data);
        $("#guia_express_modal").modal("show");
        $("#order_number").val(id);
        $("#listado_servicios").html(data);
        $("#servicio").change(function() {
            $('#nservi').val($("#servicio option:selected").text());


        });

        catalogos_pais();
        $('#lmunicipio').hide();

    });
}

function catalogos_pais() {
    var url = base_url(
        "index.php/Customers/lista_departamentos"
    );

    $.get(url, function(data) {
        $("#listado_departamentos").html(data);

        $("#departamento").change(function() {
            $('#ndepto').val($("#departamento option:selected").text());
            // $('#nservi').val($("#servicio option:selected").text());
            var url = base_url(
                "index.php/Customers/lista_municipios/" + $('#departamento').val()
            );

            $.get(url, function(data) {
                $("#listado_municipios").html(data);
                $("#municipio").change(function() {
                    $('#nmunic').val($("#municipio option:selected").text());
                    // $('#nservi').val($("#servicio option:selected").text());


                });

                $('#lmunicipio').show();

            });


        });




    });


}



function lista_estatus(id) {
    var url = base_url(
        "index.php/Customers/consulta_estatus/" + id
    );


    $.get(url, function(data) {

        $("#ver_estatus_modal").modal("show");
        $("#listado_estatus").html(data);

    });
}


function lista_estatus_(id) {
    var formData;

    url_destino = "index.php/Customers/consulta_estatus/" + id;
    formData = new FormData($(".listado_estatus")[0]);


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
            $("#ver_estatus_modal").modal("show");
            $("#listado_estatus").html(data);
        },
        error: function() {
            message = $("<span class='error'>Ha ocurrido un error.</span>");
            showMessage(message);
        },
    });

}