function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}

function lista_partidas() {
    var url = base_url("index.php/ArancelController/listado_partida/");
    $.get(url, function(data) {
        $("#principal").html(data);
    });
}

function agregar_partida(ID) {

    var url = base_url("index.php/ArancelController/agregar_partida/" + ID);
    $.get(url, function(data) {
        $("#principal").html(data);
        if (ID > 0) {
            $('#id_arancel').val(ID);

            document.getElementById("titulo").innerHTML = "Editar Partida";

        } else {
            document.getElementById("titulo").innerHTML = "Agregar Partida"
        }

    });
}


//Funcion para guardar las partidas creadas
function guardar_partidas() {

    url_destino = "index.php/ArancelController/insertar_partidas/";
    formData = new FormData($(".add_arancel")[0]);

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
            //  $("#loader").css("display", "block");
        },
        success: function(data) {
            regresaremos();

            alertify.set("notifier", "position", "top-right");
            alertify.success("El listado de partida ha sido actualizado correctamente");
            $("#id_arancel").val("");
            $("#numeroPartida").val("");
            $("#descripcion").val("");
            $("#origen").val("");
            $("#anulado").val("");

        },
        complete: function() {

        }
    });
}


//funcion para eliminar un item de la tabla
function eliminar_partida(ID) {
    //  var idPartida = $("#id").val();
    alertify
        .confirm(
            "¿Esta seguro de eliminar esta Partida?",
            function() {
                var url = base_url("index.php/ArancelController/eliminar_partida/" + ID);
                $.get(url, function(data) {
                    alertify.set("notifier", "position", "top-right");
                    alertify.success("La partida ha sido eliminada correctamente");
                    $(".modal-backdrop").remove();
                    $("#form .close").click();
                    regresaremos();
                });
            },
            function() {
                alertify.error("Cancel");
            }
        )
        .set({ title: "Eliminar  partida" })
        .set({ labels: { ok: "Aceptar", cancel: "Cancelar" } });
}


function regresaremos() {
    var url = base_url("index.php/ArancelController/listado_partida/");
    $.get(url, function(data) {
        $("#principal").html(data);
    });
}


function permisos_lista(id) {
    var url = base_url(
        "index.php/ArancelController/listado_permisos/" + id
    );

    $.get(url, function(data) {
        document.getElementById("contenidoLista").innerHTML = data;
    });
}

function mostrar_permisos(id, partida) {

    $("#contenedor-tbl").hide();
    $("#c-permisos").show();
    $("#p_import").val(id);
    $("#numero-partida").val(partida);
    permisos_lista(id);
}


function permisos_off() {
    $("#contenedor-tbl").show();
    $("#c-permisos").hide();
}

function addpermiso() {
    if ($("#p_import").val()) {
        permiso = $("#catalogopermisos").val();
        id = $("#p_import").val();
        var url = base_url(
            "index.php/ArancelController/agregar_permiso/" +
            permiso +
            "/" +
            id
        );
        $.post(url, function(data) {
            permisos_lista(id);
        });
    }
}


function eliminar_permiso(id) {
    //  var idPartida = $("#id").val();
    // alert(id);
    alertify
        .confirm(
            "¿Esta seguro de eliminar esta permiso?",
            function() {
                var url = base_url("index.php/ArancelController/eliminar_permiso/" + id);
                $.get(url, function(data) {
                    alertify.set("notifier", "position", "top-right");
                    alertify.success("El permiso ha sido eliminado ");
                    id = $("#p_import").val();
                    permisos_lista(id);


                });
            },
            function() {
                alertify.error("Cancel");
            }
        )
        .set({ title: "Eliminar  partida" })
        .set({ labels: { ok: "Aceptar", cancel: "Cancelar" } });
}

function Alista_warehouse_manifiesto(id, master, correla) {
    master_number = master;
    var url = base_url(
        "index.php/DeclaracionController/Amanifiesto/" +
        id +
        "/" +
        master +
        "/" +
        correla
    );
    console.log(
        "JSpara cargar la vista de  manifiestos pendientes de transmitir"
    );

    $.get(url, function (data) {
        //  console.log( "resultado......  "+ data);
        $("#principal").html(data);
        $("#master-number").html(master_number);
        $("#id-master").val(id);
        $("#name_master").val(master);
        document.getElementById("master-number").innerHTML = master_number;
        $("#processSelected").prop("disabled", true);

        var tipo_transporte_id = $("#tipo_transporte_id").val();
        $("#tipotransporte").val(tipo_transporte_id);
        $("#tipotransporte").change();

        var transportista_id = $("#transportista_id").val();
        $("#transportistas").val(transportista_id);
        $("#transportistas").change();

        var puertosalida_id = $("#puertosalida_id").val();
        $("#puerto_salida").val(puertosalida_id);
        $("#puerto_salida").change();

        var puertosalida2_id = $("#puertosalida2_id").val();
        $("#puerto_salida2").val(puertosalida2_id);
        $("#puerto_salida2").change();

        var declarante_id = $("#declarante_id").val();
        $("#declarante").val(declarante_id);
        $("#declarante").change();

        var agent_exp_id = $("#agent_exp_id").val();
        $("#agent_exp").val(agent_exp_id);
        $("#agent_exp").change();
        var agent_cong_id = $("#agent_cong_id").val();
        $("#agent_cong").val(agent_cong_id);
        $("#agent_cong").change();
    });
}

function cargarReferencias(master, referencia) {
    var url = base_url(
        "index.php/DeclaracionController/cargarReferencias/" +
        master +
        "/" +
        referencia
    );

    $.get(url, function (data) {
        // Actualiza el contenido del div con la tabla generada
        document.getElementById("contenidoLista").innerHTML = data;
    }).fail(function (xhr, status, error) {
        console.error("Error al cargar la tabla:", error);
    });
}

function imprimir_referenciaAPG() {
    referencia = $("#referencia option:selected").text();
    console.log("Nombre de referencia:", referencia);
    $("#modal_print_referenciaAPG").modal("show");

    $("#modal_print_referenciaAPG").on("shown.bs.modal", function () {
        $("#referenciaAPG").val(referencia);
    });
}

function ImprimirGuiasApg() {
    /** busca los detalles (warehouse de una master) tabla manifiesto */
    var id = 0;
    var referencia = 0;
    id = $("#id-master").val();
    referencia = $("#referenciaAPG").val();

    //console.log(" Los  valores Capturados son " + id + "Referencia" + referencia);

    var url = base_url(
        "index.php/DeclaracionController/generarPdfGuia/" + id + "/" + referencia
    );

    $.get(url, function (data) {
        var datos = JSON.parse(data);
        var destino = datos["destino"];
        var nombre_archivo = datos["nombre_archivo"];
        var ruta_manifiesto = destino + nombre_archivo;

        ver_report_comprobantePDF(ruta_manifiesto);
    });
}

function agreDatosdeclaracion_modal() {
    seleccionatrack(0, 0);
    $("#modal_addinfo_declaracion").modal("show");
}

function seleccionatrack(c, edit) {
    console.log("#dentro de la  funcion para seleccionar la  guia  ");
    var guiaSeleccionada = 0;
    var adeclarar = 0;
    var idManifiesto = 0;
    if (edit == 1) {
        if (document.getElementById("mdidMaster")) {
            idManifiesto = $("#mdidMaster").val();
        }
    } else {
        if (document.getElementById("id-master")) {
            idManifiesto = $("#id-master").val();
        }
    }

    var idTracking = 0;

    /*if ($("#trackno" + c).is(":checked")) {
        adeclarar = 1;
    } else {
        adeclarar = 0;
    }*/
    var select = document.getElementById("referencia");
    var adeclarar = 1;
    //var selected   = combo.options[combo.selectedIndex].text;
    var referencia = select.options[select.selectedIndex].text; //  (document.getElementById('referencia'))? $('#referencia').val() : 0 ;
    //idTracking = $("#trackno" + c).val();
    console.log(
        "los valores para procesar agregar a declaracio n  #####3  " +
        idManifiesto +
        "TRackin" +
        referencia +
        " seleccionado " +
        adeclarar
    );
    // set_adeclarar($idManifiesto, $idTracking, $adeclarar)
    var url = base_url(
        "index.php/DeclaracionController/set_adeclarar/" +
        idManifiesto +
        "/" +
        referencia +
        "/" +
        adeclarar
    );

    $.get(url, function (data) {
        console.log("Retornos del  valor " + data);
        if (edit == 0) {
            // console. log('Ejecuntando con  cero');
            /*if (data > 0) {
                $("#processSelected").prop("disabled", false);
            } else {
                $("#processSelected").prop("disabled", true);
            }*/
            // $("#BultosLiberacion").val(data);
        } else {
            console.log("Seleccionando desde la modal con  cero");
            if (data > 0) {
                $("#btnprocesar_GuiasAdd").prop("disabled", false);
            } else {
                $("#btnprocesar_GuiasAdd").prop("disabled", true);
            }
        }
        document.getElementById("BultosLiberacion")
            ? $("#BultosLiberacion").val(data)
            : "";
        document.getElementById("BultosLiberacion")
            ? $("#BultosLiberacion").prop("disabled", true)
            : "";
        document.getElementById("Referencia")
            ? $("#Referencia").val(referencia)
            : "";
        document.getElementById("Referencia")
            ? $("#Referencia").prop("disabled", true)
            : "";

        // guiaSeleccionada = data ;
    });
    return guiaSeleccionada;
}

function procesardeclaracion() {
    console.log("procesando la declaracion de las  guias ");
    var eJecutar = "S";
    var Master = "5";
    var correla = 0;
    var idMaster = document.getElementById("id-master")
        ? $("#id-master").val()
        : 0;
    var Tipo = document.getElementById("Tipo") ? $("#Tipo").val() : 0;
    var Aforador = document.getElementById("Aforador")
        ? $("#Aforador").val()
        : "";
    var Declarante = document.getElementById("Declarante")
        ? $("#Declarante").val()
        : "";
    var Referencia = document.getElementById("Referencia")
        ? $("#Referencia").val()
        : "";
    var BultosLiberacion = document.getElementById("BultosLiberacion")
        ? $("#BultosLiberacion").val()
        : 0;
    var tipodecl = document.getElementById("tipodecl") ? $("#tipodecl").val() : 0;
    dJson = {
        idMaster: idMaster,
        Tipo: Tipo,
        Aforador: Aforador,
        Declarante: Declarante,
        Referencia: Referencia,
        BultosLiberacion: BultosLiberacion,
        tipodecl: tipodecl,
    };
    if (eJecutar == "S") {
        var url = base_url("index.php/DeclaracionController/procesardeclaracion/");
        // url = "dad";
        $.ajax({
            url: url,
            type: "POST",
            data: dJson,
            //cache: false,
            //contentType: false,
            // processData: false,
            beforeSend: function () {
                $("#loader").css("display", "block");
            },
            success: function (data) {
                if (data != 0) {
                    alertify.set("notifier", "position", "top-right");
                    alertify.success("La declaracion se ha ejecutado  con exito.");
                    if (document.getElementById("Referencia")) {
                        $("#Referencia").val("");
                    }
                    $(".modal-backdrop").remove();
                    $("#modal_addinfo_declaracion").modal("hide");
                    document.getElementById("processSelected")
                        ? $("#processSelected").prop("disabled", true)
                        : $("#processSelected").prop("disabled", true);
                    // $("#processSelected").prop("disabled", true);
                    getListaDeclaraciones();
                    //  lista_detalleManifiesto(idMaster, Master, correla);
                } else {
                    alertify.set("notifier", "position", "top-right");
                    alertify.error(
                        "Verifique el proceso de declaracion, surgio un error durante la ejecucion!!."
                    );
                }
            },
            complete: function () {
                $("#loader").css("display", "none");
            },
        });
    }
}

function openmodalConocimiento() {
    $("#modal_conocimiento").modal("show");
}

function agregar_conociminto() {
    var descripcion = $("#descripcion").val();
    var sac = $("#sac").val();
    var pais = $("#codigoPais").val();
    $("#preloader").fadeIn();
    var url = base_url(
        "index.php/DeclaracionController/agregar_conociminto/" +
        descripcion +
        "/" +
        sac +
        "/" +
        pais
    );
    $.get(url, function (data) {
        $("#preloader").fadeOut();
        $("#descripcion").val("");
        $("#sac").val("");

        alertify.set("notifier", "position", "top-right");
        alertify.success("Conocimiento ha sido guardado correctamente!");
    });
}

//    console.log("Fcuncion que muestra la    vista para  editar la  infomacion  del   tracking");
function show_infoTracking(idMnifiesto) {
    var url = base_url(
        "index.php/DeclaracionController/show_infoTracking/" + idMnifiesto
    );
    $.get(url, function (data) {
        $("#contenmodaledittrack").html(data);
        $("#modal_editTrack").modal("show");
    });
}

//Funcion para guardar las partidas creadas
function updateInfoTrack() {
    var ref = $("#referencia").val();
    var id = $("#id-master").val();

    var item = $("#id_item").val();
    url_destino = "index.php/DeclaracionController/updateInfoTrack/" + item;
    formData = new FormData($(".edit_declara")[0]);

    var message = "";
    $.ajax({
        url: base_url(url_destino),
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () { },
        success: function (data) {
            alertify.set("notifier", "position", "top-right");
            alertify.success("Actualización realizada con éxito");
            $(".modal-backdrop").remove();
            $("#modal_editTrack").modal("hide");
            cargarReferencias(id, ref);
        },
        complete: function () { },
    });
}
