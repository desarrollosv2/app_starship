function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}


function celular() {

    var url = base_url("index.php/movimientos/celular");

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}
/* llama vista de llamadas celulares */
function celularquery() {

    var url = base_url("index.php/movimientos/celularquery");

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}

/* llama vista de documentos aduan */
function documentos_aduana() {

    var url = base_url("index.php/AduanaController/documentos_aduana");

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}



function subir_archivos(opcion) {
    var formData;
    var valido = "S";

    if (opcion == 'a') {
        url_destino = "index.php/movimientos/import_llamadas_celular";
        formData = new FormData($(".sarchivo")[0]);
    }

    if (opcion == 'b') {
        url_destino = "index.php/movimientos/subir_documentos";
        formData = new FormData($(".sformatos")[0]);
        /*  if ($("#nombre_doc").val().trim().length == 0){
             valido = "N";
             $("#nombre_doc").focus();
             $.notify("Falta digitar Nombre de Documento.", "error");
         } */
    }

    if (opcion == 'c') {
        url_destino = "index.php/catalogos/crear_cliente_lotes";
        formData = new FormData($(".cliformatos")[0]);
    }

    if (opcion == 'd') {
        url_destino = "index.php/movimientos/subir_documentos_clientdddde";
        formData = new FormData($(".sclientes")[0]);
        /*  if ($("#nombre_doc").val().trim().length == 0){
             valido = "N";
             $("#nombre_doc").focus();
             $.notify("Falta digitar Nombre de Documento.", "error");
         } */
    }

    if (opcion == 'e') {
        // url_destino = "index.php/movimientos/import_llamadas_fijos";
        url_destino = "index.php/Crud/importdata";
        formData = new FormData($(".sarchivo")[0]);
    }

    if (opcion == 'ee') {
        // url_destino = "index.php/movimientos/import_llamadas_fijos";
        url_destino = "index.php/Crud/importdata_celulares";
        formData = new FormData($(".sarchivo")[0]);
    }


    if (opcion == 'f') {
        url_destino = "index.php/cempleadosrh/import_doc_rh";
        formData = new FormData($(".sclientes")[0]);
    }

    if (valido == "S") {
        var message = "";
        //hacemos la petición ajax  
        $.ajax({
            url: base_url(url_destino),
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,
            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            //mientras enviamos el archivo
            beforeSend: function() {
                message = $("<span class='before'>Subiendo la archivo, por favor espere...</span>");
                showMessage(message)
            },
            //una vez finalizado correctamente
            success: function(data) {
                message = $("<div class='alert alert-success' role='alert'> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button> <strong></strong> <h5>El archivo se ha subido correctamente</h5> </div>");
                // message = $("<div class='message alert alert-success' data-alert='alert'> <strong>El archivo ha subido correctamente </strong></div>");
                if (opcion == 'a') { $("#tabla_llamadas").html(data); }
                if (opcion == 'c') { $("#tabla_cliente").html(data); }
                if (opcion == 'e') { $("#tabla_llamadas_fijos").html(data); }
                if (opcion == 'ee') { $("#tabla_llamadas_fijos").html(data); }

                showMessage(message);
                /*  if(isImage(fileExtension))
                    {
                        $(".showImage").html("<img src='files/"+data+"' />");
                    } */
            },
            //si ha ocurrido un error
            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    }
}


function consulta_llamadas() {

    var url = base_url("index.php/movimientos/consulta_llamadas");

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}

function buscar_llamadas(opcion) {

    var formData;
    var valido = "S";



    if (opcion == 'm') {
        // url_destino = "index.php/movimientos/import_llamadas_fijos";
        url_destino = "index.php/Crud/consulta_llamadas";
        formData = new FormData($(".f_llamadas")[0]);
    }

    if (opcion == 'mm') {
        // url_destino = "index.php/movimientos/import_llamadas_fijos";
        url_destino = "index.php/Crud/consulta_llamadas_celulares";
        formData = new FormData($(".f_llamadas")[0]);
    }




    if (valido == "S") {
        var message = "";
        //hacemos la petición ajax  
        $.ajax({
            url: base_url(url_destino),
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,
            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            //mientras enviamos el archivo
            beforeSend: function() {
                message = $("<span class='before'>Subiendo la archivo, por favor espere...</span>");
                showMessage(message)
            },
            //una vez finalizado correctamente
            success: function(data) {
                message = $("<span class='success'>El archivo ha subido correctamente dos.</span>");
                if (opcion == 'm') { $("#tabla_llamadas").html(data); }
                if (opcion == 'c') { $("#tabla_cliente").html(data); }
                if (opcion == 'e') { $("#tabla_llamadas_fijos").html(data); }
                if (opcion == 'mm') { $("#tabla_llamadas").html(data); }
                showMessage(message);
                /*  if(isImage(fileExtension))
                    {
                        $(".showImage").html("<img src='files/"+data+"' />");
                    } */
            },
            //si ha ocurrido un error
            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    }
}



function formatos() {

    var url = base_url("index.php/movimientos/formato");

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}


function consulta_formatos() {

    var url = base_url("index.php/movimientos/consulta_formatos");

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}

function buscar_formatos() {

    var url = base_url("index.php/movimientos/buscar_formatos");

    datos = $("#formatos").serialize();

    $.get(url, datos,
        function(data) {
            $("#tabla_formatos").html(data);
        }
    );
}


/* function consulta_doc_formato_cliente(){

    var url = base_url("index.php/movimientos/formato_doc_cliente");

    $.get(url,
        function (data) {
            $("#principal").html(data); 
        }
    );

}


function consulta_doc_cliente(){

    var url = base_url("index.php/movimientos/consulta_doc_cliente");

    $.get(url, 
        function (data) {
            $("#principal").html(data); 
        }
    );

}

function buscar_doc_cliente(){

    var url = base_url("index.php/movimientos/buscar_doc_cliente");

    datos = $("#doc_cliente").serialize();

    $.get(url, datos,
        function (data) {
            $("#tabla_doc_cliente").html(data); 
        }
    );
}
 */
function telefono_fijos() {

    //  var url = base_url("index.php/movimientos/tfijos");
    var url = base_url("index.php/movimientos/tfijos_new");
    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}

function ingreso_caja() {

    var url = base_url("index.php/movimientos/ingreso_cajas");

    $.get(url,
        function(data) {
            $("#principal").html(data);

        }
    );
}

function generar_codigo_caja() {

    var em = $("#empresas").val();
    var pr = $("#procesos").val();

    var aLetras = new Array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    var aNumeros = new Array('1', '2', '3', '4', '5', '6', '7', '8', '9');
    var cLetra = "";
    var cNumero = "";
    var union = "";

    for (index = 0; index < 3; index++) {
        cLetra = aLetras[Math.floor(Math.random() * aLetras.length)];
        cNumero = aNumeros[Math.floor(Math.random() * aNumeros.length)];
        union = union + cLetra + cNumero;
    }

    $("#codigo").val(em + "-" + pr + "-C-" + union);
}

function grabar_caja() {

    var valido = "s";
    var tabla_caja = "";

    if ($("#codigo").val().trim().length == 0) {
        $.notify("Falta ingresar Codigo de Caja.", "error");
        valido = "n";
    }

    if (valido == "s") {

        var url = base_url("index.php/movimientos/validar_nombre");

        $.get(url, "codigo=" + $("#codigo").val().trim(),
            function(data) {
                if (data.trim() == "n") {
                    url = base_url("index.php/movimientos/grabar_cajas");

                    datos = $("#cajas").serialize();
                    $.get(url, datos,
                        function(data) {
                            $.notify("Nombre de Caja Grabado.", "success");
                            /* $("#tabla_empleado_rh").html(data); */

                            var fi = $("#fechaingreso").val();
                            var fv = $("#fechavencimiento").val();
                            fi = (convertDateFormat(fi));
                            fv = (convertDateFormat(fv));

                            var empresa = document.getElementById("empresas");
                            var nombreempresa = empresa.options[empresa.selectedIndex].text;

                            var proceso = document.getElementById("procesos");
                            var nombreproceso = proceso.options[proceso.selectedIndex].text;

                            tabla_caja = "<table id='dvData'>";
                            tabla_caja += "<tr><th colspan='2' class='text-center'><h1>Informacion de Caja</h1></th></tr>"
                            tabla_caja += "<tr><td><h1>Fecha Ingreso</h1></td><td><h1>" + fi + "</h1><td></tr>";
                            tabla_caja += "<tr><td><h1>Fecha Vencimiento</h1></td><td><h1>" + fv + "</h1><td></tr>";
                            tabla_caja += "<tr><td><h1>Codigo</h1></td><td><h1>" + $("#codigo").val() + "</h1></td></tr>";
                            tabla_caja += "<tr><td><h1>Empresa</h1></td><td><h1>" + nombreempresa + "</h1><td></tr>";
                            tabla_caja += "<tr><td><h1>Proceso</h1></td><td><h1>" + nombreproceso + "</h1><td></tr>";
                            tabla_caja += "</table>";

                            $("#codigo").val("");

                            document.getElementById("cajacreada").innerHTML = tabla_caja;
                            exportar_excel();

                        }
                    );
                } else {
                    $.notify("Nombre ya existe.", "error");
                    $("#codigo").focus();
                }
            }
        );
    }

}


function consulta_caja() {

    var url = base_url("index.php/movimientos/consulta_cajas");

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );
}

function buscar_caja() {

    var url = base_url("index.php/movimientos/buscar_cajas");

    datos = $("#bcajas").serialize();

    $.get(url, datos,
        function(data) {
            $("#ver_cajas").html(data);
        }
    );
}

function editar_cajas($id_Reg) {

    $("#v_editar_cajas" + $id_Reg).show("blind");

    var url = base_url("index.php/movimientos/consulta_caja_editar/" + $id_Reg);

    $.get(url, function(data) {
        $('#codigo' + $id_Reg).val(data.codigo);
        $('#fvencimiento' + $id_Reg).val(data.fvencimiento.substring(6, 10) + "-" + data.fvencimiento.substring(3, 5) + "-" + data.fvencimiento.substring(0, 2));
        $('#id_cajas' + $id_Reg).val(data.id);
    });
}

function actualizar_cajas($id_Reg) {

    var xmlhttp;
    xmlhttp = xhr_request();
    var valido = 's';

    if ($("#codigo" + $id_Reg).val().trim().length == 0) {
        $.notify("Falta ingresar Codigo de Caja.", "error");
        valido = "n";
    }

    //opcion2 
    var infoClienteA = $("#f_editar_cajas" + $id_Reg).serializeArray();
    //alert($("#nombre"+$id_Reg).val());



    if (valido == "s") {
        var infoCliente = $("#f_editar_cajas" + $id_Reg).serialize();

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                $.notify("Cajas Modificado.", "success");
                buscar_caja();
            }
            if (xmlhttp.readyState == 4 && xmlhttp.status !== 200) {
                $.notify("Error al modificar el cajas", "success");
            }
        }

        infoCliente = infoCliente + "&id_Reg=" + $id_Reg;

        xmlhttp.open("POST", base_url('index.php/movimientos/editar_cajas'), true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(infoCliente);
    }
}


function cerrar_act_cajas($id_Reg) {
    $("#v_editar_cajas" + $id_Reg).hide("slow");
}

function exportar_excel() {
    //window.open('data:application/vnd.ms-excel,' + encodeURIComponent($("'#" + datos + "'").html()));
    window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#dvData').html()));
    //window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#codigo_creado').html()));
    e.preventDefault();
}

function exportar_word() {
    //window.open('data:application/vnd.ms-excel,' + encodeURIComponent($("'#" + datos + "'").html()));
    //window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#dvData').html()));
    window.open('data:application/vnd.ms-word,' + encodeURIComponent($('#dvData').html()));
    e.preventDefault();
}

function exportar_pdf() {
    window.open('data:application/vnd.cups-pdf,' + encodeURIComponent($('#dvData').html()));
    e.preventDefault();
}

function ver_url(URL) {


    var url = base_url("index.php/Movimientos/getUserIpAddress");

    $.get(url,
        function(data) {
            //$("#ver_cajas").html(data); 
            console.log(data);
        }
    );
    window.open(base_url(URL), "ventana1", "width=350,height=350,scrollbars=no,toolbar=no, titlebar=no, menubar=no")

}

function ver_url_tramite_aduana(URL) {

    window.open(base_url(URL), "ventana1", "width=350,height=350,scrollbars=no,toolbar=no, titlebar=no, menubar=no")

}

//como la utilizamos demasiadas veces, creamos una función para 
//evitar repetición de código
function showMessage(message) {
    $(".messages").html("").show();
    $(".messages").html(message);
}


// @param string (string) : Fecha en formato YYYY-MM-DD
// @return (string)       : Fecha en formato DD/MM/YYYY
function convertDateFormat(string) {
    var info = string.split('-');
    return info[2] + '/' + info[1] + '/' + info[0];
}


//comprobamos si el archivo a subir es una imagen
//para visualizarla una vez haya subido
function isImage(extension) {
    switch (extension.toLowerCase()) {
        case 'jpg':
        case 'gif':
        case 'png':
        case 'jpeg':
        case 'xls':
        case 'pdf':
            return true;
            break;
        default:
            return false;
            break;
    }
}

$(document).ready(function() {

    $("#codigo_cliente").on("change", function(event) {
        event.preventDefault();
        alert("entro");
        $.ajax({
            url: base_url("indx.php/login/cerrar"),
            type: "POST",
            data: {},
            success: function() {
                window.location.href = base_url("index.php/login/");
            }
        });
    });

});


/*comence aqui */
function gestion_cliente(opcion) {

    var formData;
    var valido = "S";






    if (opcion == 'c') {

        url_destino = "index.php/ClientesController/crear_cliente";
        formData = new FormData($(".add_cliente")[0]);
    }

    if (opcion == 'e') {
        url_destino = "index.php/ClientesController/editar_cliente";
        formData = new FormData($(".edit_cliente")[0]);
    }
    if (opcion == 'b') {

        url_destino = "index.php/ClientesController/borrar_cliente";
        formData = new FormData($(".del_cliente")[0]);
    }

    if (opcion == 'dq') {
        //vista principal de clientes
        url_destino = "index.php/ClientesController/consulta_documentos_cliente";
        formData = new FormData($(".qry_cliente")[0]);
    }





    if (valido == "S") {
        var message = "";
        //hacemos la petición ajax  
        $.ajax({
            url: base_url(url_destino),
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,

            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            //mientras enviamos el archivo
            beforeSend: function() {
                message = $("<span class='before'>Subiendo la archivo, por favor espere...</span>");
                showMessage(message)
            },
            //una vez finalizado correctamente
            success: function(data) {
                message = $("<span class='success'>El archivo ha subido correctamente tres.</span>");
                if (opcion == 'm') { $("#tabla_llamadas").html(data); }
                if (opcion == 'c') { $("#tabla_cliente").html(data); }
                if (opcion == 'e') { $("#tabla_cliente").html(data); }
                if (opcion == 'b') { $("#tabla_cliente").html(data); }
                if (opcion == 'dq') { $("#tabla_cliente").html(data); }
                showMessage(message);
                /*  if(isImage(fileExtension))
                    {
                        $(".showImage").html("<img src='files/"+data+"' />");
                    } */
            },
            //si ha ocurrido un error
            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    }

}

function gestion_documentos_cliente(opcion, nombre, opc) {

    var formData;
    var valido = "S";

    if (opc == 1) {
        url_destino = 'index.php/ClientesController/consulta_documentos_cliente/' + opcion + '/' + nombre;
        formData = new FormData($("qry_clientes")[0]);
        opcion = 'dq';
    } else {

        url_destino = 'index.php/ClientesController/consulta_documentos_cliente/' + opcion + '/' + nombre;
        formData = new FormData($("sclientes")[0]);
        opcion = 'db';
    }


    if (valido == "S") {
        var message = "";
        //hacemos la petición ajax  
        $.ajax({
            url: base_url(url_destino),

            type: 'POST',

            // Form data
            //datos del formulario
            data: formData,

            //necesario para subir archivos via ajax
            cache: false,


            processData: false,
            //mientras enviamos el archivo
            beforeSend: function() {
                message = $("<span class='before'>Subiendo la archivo, por favor espere...</span>");
                showMessage(message)
            },
            //una vez finalizado correctamente
            success: function(data) {
                message = $("<span class='success'> El archivo ha subido correctamente cuatro.</span>");
                if (opcion == 'dq') { $("#tabla_cliente").html(data); }
                if (opcion == 'db') { $("#tabla_cliente").html(data); }
                showMessage(message);
                /*  if(isImage(fileExtension))
                    {
                        $(".showImage").html("<img src='files/"+data+"' />");
                    } */
            },
            //si ha ocurrido un error
            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    }




}

function back_cliente(opcion) {

    var formData;
    var valido = "S";


    url_destino = 'index.php/ClientesController/consulta_documentos_cliente';
    formData = new FormData($("sclientes")[0]);




    if (valido == "S") {
        var message = "";
        //hacemos la petición ajax  
        $.ajax({
            url: base_url(url_destino),
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,

            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            //mientras enviamos el archivo
            beforeSend: function() {
                message = $("<span class='before'>Subiendo la archivo, por favor espere...</span>");
                showMessage(message)
            },
            //una vez finalizado correctamente
            success: function(data) {
                message = $("<span class='success'> El archivo ha subido correctamente cuatro.</span>");
                if (opcion == 'c') { $("#tabla_clientecc").html(data); }

                showMessage(message);
                /*  if(isImage(fileExtension))
                    {
                        $(".showImage").html("<img src='files/"+data+"' />");
                    } */
            },
            //si ha ocurrido un error
            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    }




}

function logdown(opcion) {

    var formData;
    var valido = "S";

    // url_destino = 'index.php/ClientesController/consulta_documentos_cliente/' + opcion + '/' + nombre;
    url_destino = 'index.php/ClientesController/log_documentos_cliente/' + opcion;
    formData = new FormData($("upclientes")[0]);

    opcion = 'ld';


    if (valido == "S") {
        var message = "";
        //hacemos la petición ajax  
        $.ajax({
            url: base_url(url_destino),
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,

            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            //mientras enviamos el archivo
            beforeSend: function() {
                message = $("<span class='before'>Subiendo la archivo, por favor espere...</span>");
                showMessage(message)
            },
            //una vez finalizado correctamente
            success: function(data) {
                message = $("<span class='success'> El archivo ha subido correctamente cuatro.</span>");
                if (opcion == 'ld') { $("#tabla_clientecc").html(data); }

                showMessage(message);
                /*  if(isImage(fileExtension))
                    {
                        $(".showImage").html("<img src='files/"+data+"' />");
                    } */
            },
            //si ha ocurrido un error
            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    }

}


function historialdescargas(opcion) {

    var formData;
    var valido = "S";

    // url_destino = 'index.php/ClientesController/consulta_documentos_cliente/' + opcion + '/' + nombre;
    url_destino = 'index.php/ClientesController/consulta_documentos_descargados_cliente/' + opcion;
    formData = new FormData($("upclientes")[0]);


    opcion = 'd'

    if (valido == "S") {
        var message = "";
        //hacemos la petición ajax  
        $.ajax({
            url: base_url(url_destino),
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,

            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            //mientras enviamos el archivo
            beforeSend: function() {
                message = $("<span class='before'>Subiendo la archivo, por favor espere...</span>");
                showMessage(message)
            },
            //una vez finalizado correctamente
            success: function(data) {
                message = $("<span class='success'> El archivo ha subido correctamente cuatro.</span>");
                if (opcion == 'd') { $("#tabla_cliente").html(data); }

                showMessage(message);
                /*  if(isImage(fileExtension))
                    {
                        $(".showImage").html("<img src='files/"+data+"' />");
                    } */
            },
            //si ha ocurrido un error
            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    }

}

function tramites_aduana(opcion, opc) {

    var formData;
    var valido = "S";

    if (opcion == 'c') {

        url_destino = "index.php/AduanaController/crear_tramite";
        formData = new FormData($(".add_tramite")[0]);
    }

    if (opcion == 'e') {

        url_destino = "index.php/AduanaController/editar_tramite";
        formData = new FormData($(".edit_tramite")[0]);
    }
    if (opcion == 'u') {
        url_destino = "index.php/AduanaController/subir_documentos_aduana";
        formData = new FormData($(".up_tramite")[0]);

    }
    if (opcion == 'd') {

        // url_destino = 'index.php/ClientesController/consulta_documentos_cliente/' + opc ;
        url_destino = "index.php/AduanaController/consulta_documentos_tramite/" + opc;
        formData = new FormData($(".qry_clientes")[0]);
    }
    if (opcion == 'b') {
        url_destino = "index.php/AduanaController/borrar_tramite";
        formData = new FormData($(".del_tramite")[0]);
    }


    if (valido == "S") {
        var message = "";
        //hacemos la petición ajax  
        $.ajax({
            url: base_url(url_destino),
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,

            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,


            success: function(data) {
                if (opcion == 'c') {
                    documentos_aduana();
                    $('#addDocumento').modal('hide');
                    $("#message").html('<div class="alert alert-success alertgreen "><button type="button" class="close">x</button><strong>Los datos se han sido guardados  correctamente.</strong></div>');
                    $("#fecha").val("");
                    $("#dm").val("");
                    $("#correlativo").val("");
                    $("#observaciones").val("");



                    window.setTimeout(function() {
                        $(".alert").fadeTo(500, 0).slideUp(500, function() {
                            $(this).remove();
                        });
                    }, 5000);
                    $('.alert .close').on("click", function(e) {
                        $(this).parent().fadeTo(500, 0).slideUp(500);

                    });


                }
                if (opcion == 'e') {

                    $('#editDocumento').modal('hide');
                    $("#message").html('<div class="alert alert-success alertgreen "><button type="button" class="close">x</button><strong>la Actualización ha  finalizado correctamente.</strong></div>');

                    window.setTimeout(function() {
                        $(".alert").fadeTo(500, 0).slideUp(500, function() {
                            $(this).remove();
                        });
                    }, 5000);
                    $('.alert .close').on("click", function(e) {
                        $(this).parent().fadeTo(500, 0).slideUp(500);

                    });
                    documentos_aduana();
                }

                if (opcion == 'u') {
                    $('#upModal').modal('hide');
                    $("#message").html('<div class="alert alert-success alertgreen "><button type="button" class="close">x</button><strong>El archivo se ha  subido corretacmente.</strong></div>');

                    alert_hide()

                }


                if (opcion == 'd') {


                }



            },

            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    }
}

function alert_hide() {
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 5000);
    $('.alert .close').on("click", function(e) {
        $(this).parent().fadeTo(500, 0).slideUp(500);

    });
}

function consulta_tramites_aduana(opc) {

    //  var url = base_url("index.php/movimientos/tfijos");
    var url = base_url("index.php/AduanaController/consulta_documentos_tramite/" + opc);

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}


function import_export() {
    //para imagenes de import export    
    var url = base_url("index.php/ImagenesController/consulta_clientes/");

    $.get(url,
        function(data) {
            $("#principal").html(data);
        }
    );

}


function imagenes_impor_exportss(opcion, id) {
    var formData;
    var valido = "S";
    if (opcion == 'u') {
        url_destino = "index.php/ImagenesController/subir_imagenes";
        formData = new FormData($(".up_imagen")[0]);

    }

    if (opcion == 'v') {
        url_destino = "index.php/ImagenesController/consulta_imagenes/" + id;
        formData = new FormData($(".qry_imagenes")[0]);

    }
    if (opcion == 'd') {
        url_destino = "index.php/ImagenesController/borrar_imagen/";
        formData = new FormData($(".del_imagen")[0]);

    }

    if (valido == "S") {
        var message = "";
        //hacemos la petición ajax  
        $.ajax({
            url: base_url(url_destino),
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (opcion == 'u') {
                    // $('#upModal').modal('hide');
                    $("#messageup").html('<div class="alert alert-success alertgreen "><button type="button" class="close">x</button><strong>El archivo se ha  subido corretacmente.</strong></div>');
                    $("#file").val("");
                    $("#referencia").val("");
                    alert_hide()
                }

                if (opcion == 'v') {
                    $('#modal_imagenes').modal('show');
                    $("#listado_imagenes").html(data);
                }

                if (opcion == 'd') {
                    $("#msg_img").html('<div class="alert alert-success alertgreen "><button type="button" class="close">x</button><strong>Imagen a sido Eliminida</strong></div>');

                    var idc = $('#clienteid').val();
                    url_destino = "index.php/ImagenesController/consulta_imagenes/" + idc;


                    $.post(base_url(url_destino), function(data) {

                        //  document.getElementById("listado_imagenes").innerHTML ="";
                        document.getElementById('borrar').disabled = true;
                        document.getElementById("listado_imagenes").innerHTML = data;


                        setTimeout(function() {
                            document.getElementById('borrar').disabled = false;
                            $('#EliminarImagen').modal('hide')
                        }, 2000);


                    })
                }

            },

            error: function() {
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);

            }
        });
    }
}