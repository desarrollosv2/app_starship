function base_url(url) 
{
    return window.location.origin + "/app_starship/" + url;
}



// Detectar qué botón fue presionado
$(document).on("click", "#frm_buscar_master_fecha button[type=submit]", function() 
{
    $("#frm_buscar_master_fecha button[type=submit]").removeAttr("clicked");
    $(this).attr("clicked", "true");
});

$(document).on('submit', '#frm_buscar_master_fecha', function(e)
{
    e.preventDefault();
    var data = $(this).serialize();
    var desde = $('#fecha_inicio_imile').val();
    var hasta = $('#fecha_fin_imile').val();

    if (desde > hasta) {
        alertify.error('La fecha de inicio no puede ser mayor que la fecha de fin.');
        return false;
    }

    var accion = $(this).find('button[clicked=true]').val();
    $("#loader").css("display", "block");

    if (accion === 'buscar') {
        $.ajax({
            url: base_url('index.php/Imile_Controllers/buscar_master'),
            method: 'POST',
            data: data,
            success: function(response) {
                $("#loader").css("display", "none");
                $('#tbl_manifiestos_imile').html(response);
                $('#tbl_lista_master').DataTable({
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ registros",
                        "zeroRecords": "No se encontraron resultados",
                        "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "sSearch": "Buscar:",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": ">>",
                            "sPrevious": "<<"
                        },
                        "sProcessing": "Procesando...",
                    }
                });
               // $('#fecha_inicio_imile').val('');
                //$('#fecha_fin_imile').val('');
            }
        });
    } else if (accion === 'grafica')
        {
        $.ajax({
            url: base_url('index.php/Imile_Controllers/generar_grafica_general'),
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) 
            {
                $("#loader").css("display", "none");
                $('#modal_grafica_general').modal({ backdrop: 'static', keyboard: false });

                if (response.status === 'ok') {
                    $('#datos_para_grafica_general').html(response.html);

                    $('#modal_grafica_general').on('shown.bs.modal', function () {
                        if (typeof drawChart === "function") {
                            drawChart();
                        }
                    });

                } else if (response.status === 'empty') {
                    $('#datos_para_grafica_general').html(
                        '<div class="alert alert-warning text-center">No se encontraron datos en el rango seleccionado.</div>'
                    );
                }

               // $('#fecha_inicio_imile').val('');
               // $('#fecha_fin_imile').val('');
            }
        });
    }
});


if (typeof formDataGlobal === 'undefined')
{
    var formDataGlobal = null;
}

$(document).on('change', 'input[type="file"]', function()
{
    const tipo = $(this).data('tipo');
    const file = this.files[0];
    if (!file) return;

    if (this.target.classList.contains('ignore-global')) {
        alert("No entró");
        return;
    }

    const formData = new FormData();
    formData.append('archivo', file);
    formData.append('tipo', tipo);
    formDataGlobal = formData;

    $.ajax({
        url: base_url('index.php/Imile_Controllers/verificar_excel'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: () => $("#loader").css("display", "block"),
        success: function (respuesta)
        {
            $("#loader").css("display", "none");
            const res = JSON.parse(respuesta);

            if (res.status === 'ok')
            {
                if (res.cantidad === 0) 
                {
                    alertify.set("notifier", "position", "top-right");
                    alertify.warning("No hay registros nuevos para actualizar.");
                    return;
                }

                alertify.confirm(
                    `Confirmar actualización`,
                    `Se encontraron <strong>${res.cantidad}</strong> registros en el archivo de <strong>${tipo.toUpperCase()}</strong>. ¿Deseas continuar?`,
                    function()
                    {
                        // Confirmado
                        $.ajax({
                            url: base_url('index.php/Imile_Controllers/actualizar_datos'),
                            type: 'POST',
                            data: formDataGlobal,
                            contentType: false,
                            processData: false,
                            beforeSend: () =>  $("#loader").css("display", "block"),
                            success: function (respuestaFinal)
                            {
                                 $("#loader").css("display", "none");
                                const resFinal = JSON.parse(respuestaFinal);
                                if (resFinal.status === 'ok')
                                    {
                                        alertify.set("notifier", "position", "top-right");
                                        alertify.success(resFinal.message || 'Actualización exitosa');
                                } else 
                                {
                                    alertify.set("notifier", "position", "top-right");
                                    alertify.error(resFinal.message || 'Error al actualizar');
                                }
                            }
                        });
                    },
                    function () 
                    {
                        // Cancelado
                        alertify.set("notifier", "position", "top-right");
                        alertify.error('Actualización cancelada');
                    }
                );
            } else 
            {
                alertify.set("notifier", "position", "top-right");
                alertify.error(res.message || 'Error al leer el archivo');
            }
        }
    });
});


function mostrar_tracking_imile(id)
{
    var url = base_url("index.php/Imile_Controllers/cargar_vista_trackings/" + id);
    $("#loader").css("display", "block");
    $.get(url, function (data) 
    {
        $("#loader").css("display", "none");
        $("#principal").html(data);

        var id_detalles = $('#manifiesto_id').val();
        cargar_tabla_imile(id_detalles);
  });
}

function cargar_tabla_imile(id)
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/Imile_Controllers/obtener_trackings/" + id);
    $.get(url, function (data) 
    {
        $("#tbl_trackings_imile_respuesta").html(data);
        $("#loader").css("display", "none");
         $('#tbl_trackings_imile').DataTable({
                //para cambiar el lenguaje a español
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": ">>",
                        "sPrevious": "<<"
                    },
                    "sProcessing": "Procesando...",
                }
            });
    });
}

function regresar_imile()
{
    imile_comparar();
}



function grafica_imile(id)
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/Imile_Controllers/ver_grafica/"+ id);
    $.get(url, function(data)
    {
        $("#loader").css("display", "none");
        $('#grafica_imile').html(data);
            $('#modal_grafica').modal({
            backdrop: 'static',
            keyboard: false
        });

        // Esperamos a que el modal esté completamente visible
        $('#modal_grafica').on('shown.bs.modal', function ()
        {
            // Buscamos si existe la función drawChart que viene en el contenido cargado
            if (typeof drawChart === "function") {
                drawChart();  // Redibujamos la gráfica
            }
        });
    });
}

function cerrar_modal_grafica()
{
    $(".modal-backdrop").remove();
    $('#modal_grafica_general').modal('hide');
  //  $('#fecha_inicio_imile_grafica').val('');
  //  $('#fecha_fin_imile_grafica').val('');
    $('#datos_para_grafica_general').html('');
    $('#grafica_imile_general').html('')
}


