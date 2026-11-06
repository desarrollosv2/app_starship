function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}


function setFechasDefaultPOD() {
    const hoy = new Date();

    const fechaHasta = hoy.toISOString().split('T')[0];

    const primerDiaMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    const fechaDesde = primerDiaMes.toISOString().split('T')[0];

    $('#fecha_desde').val(fechaDesde);
    $('#fecha_hasta').val(fechaHasta);
}


function listado_pod()
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/listado_pod");

    $.get(url, function(response)
    {
        $("#loader").css("display", "none");
        $('#div_tbl_pod').html(response);
        $('#tabla_pod').DataTable({
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
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                }
        });
    })
    .fail(function(xhr) {
        $("#loader").css("display", "none");
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar la tabla de POD");
    });

}

function buscar_manifiesto_pod()
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/buscar_manifiesto_pod");
    var data = $('#form_buscar_manifiesto_pod').serialize();

    $.ajax({
        url: url,
        method : 'POST',
        data: data,
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            $("#loader").css("display", "none");
            if(response.status === 'error')
            {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                for(const campo in response.errors)
                {
                    const mensaje = response.errors[campo];
                    $(`#${campo}`).addClass('is-invalid');
                    $(`#error_${campo}`).text(mensaje);
                }
                    alertify.alert()
                    .setting({
                        title: 'Validación',
                        message: 'Verifique los datos que son requeridos.!',
                        transition: 'fade',
                        onok: function () {
                        }
                    })
                    .show();
                return;
            
            }else
            {
                $('#principal').html(response.html);
                //$('#modal_form_pod').modal({ backdrop: 'static', keyboard: false },"show");
            }
        }, error: function ()
        {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "top-right");
            alertify.error("Ocurrió un error en la petición.");
        }
    });
}

function crear_pod()
{
    let manifiestosSeleccionados = $('input[name="idpreclasificaciones[]"]:checked');

    if (manifiestosSeleccionados.length === 0) {
        alertify.set("notifier", "position", "top-right");
        alertify.error('No hay máster seleccionada.');
        return;
    }

    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/crear_pod");
    var data = $('#form_PDO').serialize();

    $.ajax({
        url: url,
        method : 'POST',
        data: data,
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            console.log(data);
            $("#loader").css("display", "none");
            if(response.status === 'error')
            {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                for(const campo in response.errors)
                {
                    const mensaje = response.errors[campo];
                    $(`#${campo}`).addClass('is-invalid');
                    $(`#error_${campo}`).text(mensaje);
                }
                    alertify.alert()
                    .setting({
                        title: 'Validación',
                        message: 'Verifique los datos que son requeridos.!',
                        transition: 'fade',
                        onok: function () {
                        }
                    })
                    .show();
                return;
            
            }else
            {
                $('.datos_generales_pdo').addClass("d-none");
                $('#detalle_manifiesto_pdo').html(response.html);
            }
        }, error: function ()
        {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "top-right");
            alertify.error("Ocurrió un error en la petición.");
        }
    });
}

function cargarEncabezadoPOD(id_pod)
{
     $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/cargarEncabezadoPOD/" + id_pod);
    $.get(url, function(response)
    {
        $("#loader").css("display", "none");
        $('#contenedorEncabezado').html(response);
        desglosePOD(id_pod);
        cargarDetallePOD(id_pod, 0);
    })
    .fail(function(xhr) {
        $("#loader").css("display", "none");
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar el encabezado de la POD");
    });

}

function cargarDetallePOD(id_pod, flag)
{
    var url = base_url("index.php/POD_Controller/cargarDetallePOD/" + id_pod);
    $.get(url, function(response)
    {
        $('#contenedorDetalle').html(response);

        if(flag == 1)
        {
            $('.pod_excluir').addClass('d-none');
            $('.pod_incluir').addClass('d-none');
        }
             
         $('#tbl_detalle_pod').DataTable({
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
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                }
        });
    })
    .fail(function(xhr) {
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar el detalle de la POD");
    });

}

function desglosePOD(id_pod)
{
    var url = base_url("index.php/POD_Controller/desglosePOD/" + id_pod);
    $.get(url, function(response)
    {
        $('#desglosedePOD').html(response);
    })
    .fail(function(xhr) {
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar el detalle de la POD");
    });

}

function excluirCaja(id, bag_number, id_pod, id_pre)
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/excluirCaja/" + id);
    
    $.get(url, function(response){
        $("#loader").css("display", "none");
        $('#observacion_pod').html(response);
        $('#bag_number_obs').text(bag_number);
        $('#pod_id_detalle').val(id_pod);
        $('#preclasificacion_pod').val(id_pre)
        $('#id_detalle_pod').val(id);
        $('#modal_observacion_pod').modal({ backdrop: 'static', keyboard: false },"show");
    })
    .fail(function(xhr) {
        $("#loader").css("display", "none");
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar el modal para excluir");
    });
    
}

function verCaja(id, bag_number)
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/verCaja/" + id);
    
    $.get(url, function(response){
        $("#loader").css("display", "none");
        $('#reproceso_pod').html(response);
        $('#bag_number_repro').text(bag_number);
        $('#modal_reproceso_pod').modal({ backdrop: 'static', keyboard: false },"show");
    })
    .fail(function(xhr) {
         $("#loader").css("display", "none");
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar el modal para excluir");
    });
}

function guardar_exclusion_caja()
{
    var id_detalle = $('#id_detalle_pod').val();
    var observacion_caja_pod = $('#observacion_caja_pod').val();
    var id_pod = $('#pod_id_detalle').val();
    var id_pre = $('#preclasificacion_pod').val();


    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/guardar_exclusion_caja");

    var data = {
        id_detalle : id_detalle,
        observacion_caja_pod : observacion_caja_pod,
        id_pre : id_pre,
        id_pod : id_pod
    };

    $.ajax({
        url: url,
        method : 'POST',
        data: data,
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            console.log(data);
            $("#loader").css("display", "none");
            if(response.status === 'error')
            {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                for(const campo in response.errors)
                {
                    const mensaje = response.errors[campo];
                    $(`#${campo}`).addClass('is-invalid');
                    $(`#error_${campo}`).text(mensaje);
                }
                    alertify.alert()
                    .setting({
                        title: 'Validación',
                        message: 'Es necesario detallar la motivo para excluir esta caja.!',
                        transition: 'fade',
                        onok: function () {
                        }
                    })
                    .show();
                return;
            
            }else if(response.status === 'success')
            {
                $('#modal_observacion_pod').modal("hide");
                $(".modal-backdrop").remove();
                cargarEncabezadoPOD(id_pod);
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
            }else
            {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
        }, error: function ()
        {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "top-right");
            alertify.error("Ocurrió un error en la petición.");
        }
    });
}

function incluirCaja(id, id_pod, id_pre)
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/incluirCaja");

    var data = {
        id : id,
        id_pod : id_pod,
        id_pre : id_pre
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            $("#loader").css("display", "none");

            if(response.status === 'success')
            {
                cargarEncabezadoPOD(id_pod);
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
            }else
            {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
            
        }, error: function ()
        {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "top-right");
            alertify.error("Ocurrió un error en la petición.");
        }
    });

}

function generar_pod()
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/generar_pod");
    var ids_pod = [];

    $('#tabsMasters .nav-link').each(function () {
        ids_pod.push($(this).data('id'));
    });

    if (ids_pod.length === 0) {
        alertify.error('No hay manifiestos para procesar');
        return;
    }

    console.log('IDs enviados:', ids_pod);

    $.ajax({
        url: url,
        method: 'POST',
        data: {ids_pod: ids_pod },
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            $("#loader").css("display", "none");
            if(response.status === 'success')
            {
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
                generar_pdf_masivo();
                lista_pod();

            }else if(response.status === 'error'){
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }else{
                alertify.set("notifier", "position", "top-right");
                alertify.error("Problemas al registar el estado de la master.");
            }
        }, error: function ()
        {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "top-right");
            alertify.error("Ocurrió un error en la petición.");
        }
    });
}

function ver_pdf(id)
{
    let url = base_url("index.php/POD_Controller/ver_pdf/" + id);
    window.open(url, '_blank');
}

function generar_pdf_masivo()
{
    var ids_pod = [];

    $('#tabsMasters .nav-link').each(function () {
        ids_pod.push($(this).data('id'));
    });

    if (ids_pod.length === 0) {
        alertify.error('No hay manifiestos para procesar');
        return;
    }

    console.log('IDs enviados:', ids_pod);

    // Abrir un PDF por cada ID
    ids_pod.forEach(function(id, index) {
        setTimeout(function() {
            let url = base_url("index.php/POD_Controller/ver_pdf/" + id);
            window.open(url, '_blank');
        }, index * 500); // 0.5 segundos entre cada PDF
    });
}

function editar_pod(id, manifiesto )
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/editar_pod/" + id);
    
    $.get(url, function(response){
        $("#loader").css("display", "none");
        $('#principal').html(response);
        var piloto = $('#id_piloto').val();
        $('#manifiesto_pod_id').val(manifiesto);
        $('#piloto').val(piloto).trigger('change');
        $('#form_editar_titulo').text(manifiesto);
        desglosePOD(id);
        cargarDetallePOD(id, 0);
        
       
    })
    .fail(function(xhr) {
        $("#loader").css("display", "none");
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar la vista para editar la POD");
    });
}

function ver_pod(id, manifiesto)
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/editar_pod/" + id);
    
    $.get(url, function(response){
        $("#loader").css("display", "none");
        $('#principal').html(response);
        var piloto = $('#id_piloto').val();
        $('#piloto').val(piloto).trigger('change');
        $('#form_editar_titulo').text(manifiesto);
        desglosePOD(id);
        cargarDetallePOD(id, 1);
        $('#div_form_encabezadopod').removeClass('d-none');
        $('#btn_editar_encabezado').addClass('d-none');
        $('#btn_cancelar_editar_encabezado').addClass('d-none');
        $('#fecha_entrega').prop('readonly', true);        
        $('#piloto').prop('disabled', true);
        $('#placa').prop('readonly', true);       
        $('#marchamo').prop('readonly', true);     
        

    })
    .fail(function(xhr) {
        $("#loader").css("display", "none");
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar la vista para editar la POD");
    });
}

function editar_encabezado() {
    // Mostrar formulario
    $('#div_form_encabezadopod').removeClass('d-none');

    // Ocultar detalle
    $('.contenido_detalle').addClass('d-none');

    // Mostrar botón cancelar
    $('#btn_cancelar_editar_encabezado').removeClass('d-none');

    // Ocultar botón editar
    $('#btn_editar_encabezado').addClass('d-none');

    $('#crear_pod').removeClass('d-none');
    $('#regresar_lista_pod').addClass('d-none')

    $('.btn_add_gt_pod').addClass('d-none');
    $('.btn_del_gt_pod').addClass('d-none');


}

function cancelar_encabezado_pod() {
    // Ocultar formulario
    $('#div_form_encabezadopod').addClass('d-none');

    // Mostrar detalle
    $('.contenido_detalle').removeClass('d-none');

    // Ocultar botón cancelar
    $('#btn_cancelar_editar_encabezado').addClass('d-none');

    // Mostrar botón editar
    $('#btn_editar_encabezado').removeClass('d-none');
    $('#regresar_lista_pod').removeClass('d-none')

    $('#crear_pod').addClass('d-none');

    $('.btn_add_gt_pod').removeClass('d-none');
    $('.btn_del_gt_pod').removeClass('d-none');
}

function guardar_encabezado_pdo()
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/guardar_encabezado_pdo");
    var data = $('#form_encabezadopod').serialize();
    var id_pod = $('#id_pod').val();
    var manifiesto = $('#manifiesto_pod_id').val();

    $.ajax({
        url: url,
        method : 'POST',
        data: data,
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            console.log(data);
            $("#loader").css("display", "none");
            if(response.status === 'error')
            {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                for(const campo in response.errors)
                {
                    const mensaje = response.errors[campo];
                    $(`#${campo}`).addClass('is-invalid');
                    $(`#error_${campo}`).text(mensaje);
                }
                    alertify.alert()
                    .setting({
                        title: 'Validación',
                        message: 'Verifique los datos que son requeridos.!',
                        transition: 'fade',
                        onok: function () {
                        }
                    })
                    .show();
                return;
            
            }else
            {
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
                editar_pod(id_pod,manifiesto);
                
            }
        }, error: function ()
        {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "top-right");
            alertify.error("Ocurrió un error en la petición.");
        }
    });
}

function anular_pod(id_pod, id_pre, master)
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/anular_pod/");
    
    $.get(url, function(response){
        $("#loader").css("display", "none");
        $('#anular_pod').html(response);
        $('#master_anular_pod').text(master);
        $('#id_pre_anulacion').val(id_pre)
        $('#id_anulacion_pod').val(id_pod);
        $('#modal_anular_pod').modal({ backdrop: 'static', keyboard: false },"show");
    })
    .fail(function(xhr) {
        $("#loader").css("display", "none");
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar el modal para anular la POD");
    });
}

function guardar_anulacion_pod()
{
    var id_pod = $('#id_anulacion_pod').val();
    var id_pre = $('#id_pre_anulacion').val();
    var comentario_anulacion_pod = $('#comentario_anulacion_pod').val();

    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/guardar_anulacion_pod");

    var data = {
        id_pod : id_pod,
        id_pre : id_pre,
        comentario_anulacion_pod : comentario_anulacion_pod
    };

     $.ajax({
        url: url,
        method : 'POST',
        data: data,
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            //console.log(data);
            $("#loader").css("display", "none");
            if(response.status === 'error')
            {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                for(const campo in response.errors)
                {
                    const mensaje = response.errors[campo];
                    $(`#${campo}`).addClass('is-invalid');
                    $(`#error_${campo}`).text(mensaje);
                }
                    alertify.alert()
                    .setting({
                        title: 'Validación',
                        message: 'Es necesario detallar la motivo para anular esta POD.!',
                        transition: 'fade',
                        onok: function () {
                        }
                    })
                    .show();
                return;
            
            }else if(response.status === 'success')
            {
                $('#modal_anular_pod').modal("hide");
                $(".modal-backdrop").remove();
                listado_pod();
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
            }else
            {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
        }, error: function ()
        {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "top-right");
            alertify.error("Ocurrió un error en la petición.");
        }
    });
}

function subir_comprobante_pod(id_pod, manifiesto)
{
    $("#loader").css("display", "block");
    var url = base_url('index.php/POD_Controller/subir_comprobante_pod/');
    $.get(url, function(response)
    {
        $("#loader").css("display", "none");
        $('#comprobantes_pod_body').html(response);
        $('#pod_comprobante_master').text(manifiesto);
        $('#pod_comprobante').val(id_pod);
        listar_comprobantes_pod(id_pod);
        $('#modal_comprobante_pod').modal({backdrop: 'static', keyboard: false},'show');
    })
    .fail(function(xhr) {
        $("#loader").css("display", "none");
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar el modal para cargar comprobantes de POD");
    });
}

function listar_comprobantes_pod(id)
{
    var url = base_url("index.php/POD_Controller/listar_comprobantes_pod/" + id);
    $.get(url, function(response)
    {
        $('#lista_pod_comprobantes').html(response);
    })
    .fail(function(xhr) {
        alertify.set("notifier", "position", "bottom-right");
        alertify.error("No se pudo cargar la lista de comprobante de entrega");
    });
}

function guardar_comprobantes_pod()
{
    var url = base_url("index.php/POD_Controller/guardar_comprobantes_pod/");
    const id_pod = $('#pod_comprobante').val();
    const files = $('#comprobante_pod')[0].files;

    if(files.length === 0)
    {
        alertify.set("notifier", "position", "top-right");
        alertify.error('Por favor, selecciona al menos un documento.');
        return;
    }

    let formData = new FormData();
    formData.append('id_pod', id_pod);

    for (let i = 0; i < files.length; i++)
    {
        formData.append('comprobante_pod[]', files[i]);
    }

    $("#loader").css("display", "block");

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response)
        {
            $("#loader").css("display", "none");

            if(response.status === 'success')
            {
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
                listar_comprobantes_pod(id_pod);
                listado_pod();
                $('#comprobante_pod').val('');
                $('#lista_archivos').html('');

            }else
            {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
        },
        error: function(xhr, status, error)
        {
            $("#loader").css("display", "none");
            
            alertify.set("notifier", "position", "top-right");
            alertify.error("La petición no se pudo procesar");
        }
    });
}

function eliminar_comprobante_pod(id, id_pod)
{
    //var id_pod = $('#pod_comprobante').val();
    alertify.confirm()
        .setting({
            title: 'Eliminar',
            message: '¿Desea eliminar este comprobante de entrega?',
            labels: { ok: 'Sí', cancel: 'Cancelar' },
            onok: function () {
                var url = base_url("index.php/POD_Controller/eliminar_comprobante_pod/" + id + "/" + id_pod);
                $("#loader").css("display", "block");

                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function (response)
                    {
                        var data = JSON.parse(response);
                        $("#loader").css("display", "none");

                        if(data.status === "success")
                        {
                            listado_pod();
                            alertify.set("notifier", "position", "top-right");
                            alertify.success(data.msg);
                        }else{
                            alertify.set("notifier", "position", "top-right");
                            alertify.error(data.msg);
                        }

                        listar_comprobantes_pod(id_pod);
                    },
                    error: function(xhr, status, error)
                    {
                        $("#loader").css("display", "none");
                        
                        alertify.set("notifier", "position", "top-right");
                        alertify.error("La petición no se pudo procesar");
                    }
                });
            },
            oncancel: function () {
                alertify.error('Cancelado');
            }
        }).show();
}

$(document).on('keyup', '#buscador-manifiestos-pod', function () {
    let texto = $(this).val().toLowerCase().trim();
    let visibles = 0;

    // Si el input está vacío
    if (texto === '') {

        // Mostrar todas las cards
        $('.manifest-card').each(function () {
            $(this).closest('[class*="col-"]').show();
        });

        // Ocultar contador
        $('#contador-manifiestos').addClass('d-none').text('');
        return;
    }

    // Filtrar
    $('.manifest-card').each(function () {
        let manifiesto = $(this).data('manifiesto');

        if (manifiesto && manifiesto.toString().includes(texto)) {
            $(this).closest('[class*="col-"]').show();
            visibles++;
        } else {
            $(this).closest('[class*="col-"]').hide();
        }
    });

    // Mostrar contador SOLO cuando hay texto
    $('#contador-manifiestos')
        .removeClass('d-none')
        .text(visibles + ' manifiestos encontrados');

});

$(document).ready(function () {

    $(document).on('input change', '.form-control', function () {
        const id = $(this).attr('id');
        if (!id) return;

        $(`#${id}`).removeClass('is-invalid');
        $(`#error_${id}`).text('');
    });
});


function cargarEncabezadoPOD_gt(id_pod) {
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/cargarEncabezadoPOD/" + id_pod);
    $.get(url, function (response) {
        $("#loader").css("display", "none");
        $('#contenedorEncabezadoGT').html(response);
        desglosePOD(id_pod);
        cargarDetallePOD_gt(id_pod, 0);

    })
        .fail(function (xhr) {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar el encabezado de la POD");
        });

}


function cargarDetallePOD_gt(id_pod, flag) {
    var url = base_url("index.php/POD_Controller/cargarDetallePOD_gt/" + id_pod);
    $.get(url, function (response) {
        $('#contenedorDetalleGT').html(response);

        if (flag == 1) {
            $('.pod_excluir').addClass('d-none');
            $('.pod_incluir').addClass('d-none');
        }

        $('#tbl_detalle_pod').DataTable({
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
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "sProcessing": "Procesando...",
            }
        });
    })
        .fail(function (xhr) {
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar el detalle de la POD");
        });

}

function verPaquetesGT(idpre, referencia, bandera, id_pod) {
    $("#loader").css("display", "block");
    var url = base_url('index.php/POD_Controller/verPaquetesGT/' + idpre + '/' + referencia + '/' + bandera + '/' + id_pod);
    $.get(url, function (response) {
        $("#loader").css("display", "none");
        $('#body_paquetes_estado_gt_pod').html(response);

        if (bandera == 1) {
            $('#title_paquetes_estado_gt').text('Paquetes Incluidos');
        }
        else {
            $('#title_paquetes_estado_gt').text('Paquetes Excluidos');
        }


        $('#modal_paquetes_estado_gt').modal({ backdrop: 'static', keyboard: false }, "show");
    })
        .fail(function (xhr) {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar el modal para ver paquetes de POD");
        });


}

function agregar_paquetes_gt() {
    let id_pod = $('#tabsMasters .nav-link.active').data('id');

    if (!id_pod) {
        alert('No se encontró el POD seleccionado.');
        return;
    }

    $("#loader").css("display", "block");
    var url = base_url('index.php/POD_Controller/form_paquetes_gt/' + id_pod);
    $.get(url, function (response) {
        $("#loader").css("display", "none");
        $('#body_paquetes_gt_pod').html(response);

        $('#btnPaquetesGTPOD')
            .text('Agregar')
            .removeClass('btn-danger')
            .addClass('btn-primary')
            .attr('onclick', 'agregarPaquetesGTPOD()');

        $('#title_paquetes_gt').text('Incluir paquetes');

        $('#modal_paquetes_gt').modal({ backdrop: 'static', keyboard: false }, "show");
    })
        .fail(function (xhr) {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar el modal para agregar paquetes de POD");
        });

}

function quitar_paquetes_gt_todo(id_pod=null){

    console.log(id_pod);

    if(id_pod == null || id_pod == ''){

        id_pod = $('#tabsMasters .nav-link.active').data('id');

    }

    console.log(id_pod)
    
    var url = base_url('index.php/POD_Controller/paquetes_pod_gt_todo/');

    $("#loader").css("display", "block");

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            id_pod: id_pod,
        },
        cache: false,
        dataType: 'json',
        success: function (response) {
            $("#loader").css("display", "none");

            if (response.status === 'success') {
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
                cargarDetallePOD_gt(id_pod, 0);
                desglosePOD(id_pod);
                
            } else {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
        },
        error: function (xhr, status, error) {
            $("#loader").css("display", "none");

            alertify.set("notifier", "position", "top-right");
            alertify.error("La petición no se pudo procesar");
        }
    });

}

function quitar_paquetes_gt() {
    let id_pod = $('#tabsMasters .nav-link.active').data('id');

    if (!id_pod) {
        alert('No se encontró el POD seleccionado.');
        return;
    }

    $("#loader").css("display", "block");
    var url = base_url('index.php/POD_Controller/form_paquetes_gt/' + id_pod);
    $.get(url, function (response) {
        $("#loader").css("display", "none");
        $('#body_paquetes_gt_pod').html(response);

        $('#btnPaquetesGTPOD')
            .text('Quitar')
            .removeClass('btn-primary')
            .addClass('btn-danger')
            .attr('onclick', 'quitarPaquetesGTPOD()');

        $('#title_paquetes_gt').text('Excluir paquetes');

        $('#modal_paquetes_gt').modal({ backdrop: 'static', keyboard: false }, "show");
    })
        .fail(function (xhr) {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar el modal para quitar paquetes de POD");
        });


}

function quitarPaquetesGTPOD() {
    var id_pod = $('#tabsMasters .nav-link.active').data('id');
    var paquetes = $('#paquetes_gt_pod').val();

    if (paquetes.length === 0) {
        alertify.set("notifier", "position", "top-right");
        alertify.error('No hay paquetes ingresados.');
        return;
    }

    var url = base_url('index.php/POD_Controller/paquetes_pod_gt/');

    $("#loader").css("display", "block");

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            id_pod: id_pod,
            paquetes: paquetes,
            accion: 1
        },
        cache: false,
        dataType: 'json',
        success: function (response) {
            $("#loader").css("display", "none");

            if (response.status === 'success') {
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
                cargarDetallePOD_gt(id_pod, 0);
                desglosePOD(id_pod);
                $('#paquetes_gt_pod').val('');
                $('#modal_paquetes_gt').modal("hide");
                $(".modal-backdrop").remove();
            } else {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
        },
        error: function (xhr, status, error) {
            $("#loader").css("display", "none");

            alertify.set("notifier", "position", "top-right");
            alertify.error("La petición no se pudo procesar");
        }
    });


}

function agregarPaquetesGTPOD() {
    var id_pod = $('#tabsMasters .nav-link.active').data('id');
    var paquetes = $('#paquetes_gt_pod').val();

    if (paquetes.length === 0) {
        alertify.set("notifier", "position", "top-right");
        alertify.error('No hay paquetes ingresados.');
        return;
    }


    var url = base_url('index.php/POD_Controller/paquetes_pod_gt/');

    $("#loader").css("display", "block");

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            id_pod: id_pod,
            paquetes: paquetes,
            accion: 0
        },
        cache: false,
        dataType: 'json',
        success: function (response) {
            $("#loader").css("display", "none");

            if (response.status === 'success') {
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
                cargarDetallePOD_gt(id_pod, 0);
                desglosePOD(id_pod);
                $('#paquetes_gt_pod').val('');
                $('#modal_paquetes_gt').modal("hide");
                $(".modal-backdrop").remove();
            } else {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
        },
        error: function (xhr, status, error) {
            $("#loader").css("display", "none");

            alertify.set("notifier", "position", "top-right");
            alertify.error("La petición no se pudo procesar");
        }
    });
}


function agregar_paquetes_gt_edit(id_pod) {
    if (!id_pod) {
        alert('No se encontró el POD seleccionado.');
        return;
    }

    $("#loader").css("display", "block");
    var url = base_url('index.php/POD_Controller/form_paquetes_gt/' + id_pod);
    $.get(url, function (response) {
        $("#loader").css("display", "none");
        $('#body_paquetes_gt_pod').html(response);

        $('#btnPaquetesGTPOD')
            .text('Agregar')
            .removeClass('btn-danger')
            .addClass('btn-primary')
            .attr('onclick', 'agregarPaquetesGTPOD_edit(' + id_pod + ')');

        $('#title_paquetes_gt').text('Incluir paquetes');

        $('#modal_paquetes_gt').modal({ backdrop: 'static', keyboard: false }, "show");
    })
        .fail(function (xhr) {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar el modal para agregar paquetes de POD");
        });

}

function quitar_paquetes_gt_edit(id_pod) {

    if (!id_pod) {
        alert('No se encontró el POD seleccionado.');
        return;
    }

    $("#loader").css("display", "block");
    var url = base_url('index.php/POD_Controller/form_paquetes_gt/' + id_pod);
    $.get(url, function (response) {
        $("#loader").css("display", "none");
        $('#body_paquetes_gt_pod').html(response);

        $('#btnPaquetesGTPOD')
            .text('Quitar')
            .removeClass('btn-primary')
            .addClass('btn-danger')
            .attr('onclick', 'quitarPaquetesGTPOD_edit(' + id_pod + ')');

        $('#title_paquetes_gt').text('Excluir paquetes');

        $('#modal_paquetes_gt').modal({ backdrop: 'static', keyboard: false }, "show");
    })
        .fail(function (xhr) {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar el modal para quitar paquetes de POD");
        });


}



function quitar_paquetes_gt_edit(id_pod) {

    if (!id_pod) {
        alert('No se encontró el POD seleccionado.');
        return;
    }

    $("#loader").css("display", "block");
    var url = base_url('index.php/POD_Controller/form_paquetes_gt/' + id_pod);
    $.get(url, function (response) {
        $("#loader").css("display", "none");
        $('#body_paquetes_gt_pod').html(response);

        $('#btnPaquetesGTPOD')
            .text('Quitar')
            .removeClass('btn-primary')
            .addClass('btn-danger')
            .attr('onclick', 'quitarPaquetesGTPOD_edit(' + id_pod + ')');

        $('#title_paquetes_gt').text('Excluir paquetes');

        $('#modal_paquetes_gt').modal({ backdrop: 'static', keyboard: false }, "show");
    })
        .fail(function (xhr) {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar el modal para quitar paquetes de POD");
        });


}

function quitarPaquetesGTPOD_edit(id_pod) {
    var paquetes = $('#paquetes_gt_pod').val();

    if (paquetes.length === 0) {
        alertify.set("notifier", "position", "top-right");
        alertify.error('No hay paquetes ingresados.');
        return;
    }

    var url = base_url('index.php/POD_Controller/paquetes_pod_gt/');

    $("#loader").css("display", "block");

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            id_pod: id_pod,
            paquetes: paquetes,
            accion: 1
        },
        cache: false,
        dataType: 'json',
        success: function (response) {
            $("#loader").css("display", "none");

            if (response.status === 'success') {
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
                cargarDetallePOD_gt(id_pod, 0);
                desglosePOD(id_pod);
                $('#paquetes_gt_pod').val('');
                $('#modal_paquetes_gt').modal("hide");
                $(".modal-backdrop").remove();
            } else {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
        },
        error: function (xhr, status, error) {
            $("#loader").css("display", "none");

            alertify.set("notifier", "position", "top-right");
            alertify.error("La petición no se pudo procesar");
        }
    });


}

function agregarPaquetesGTPOD_edit(id_pod) {
    var paquetes = $('#paquetes_gt_pod').val();

    if (paquetes.length === 0) {
        alertify.set("notifier", "position", "top-right");
        alertify.error('No hay paquetes ingresados.');
        return;
    }


    var url = base_url('index.php/POD_Controller/paquetes_pod_gt/');

    $("#loader").css("display", "block");

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            id_pod: id_pod,
            paquetes: paquetes,
            accion: 0
        },
        cache: false,
        dataType: 'json',
        success: function (response) {
            $("#loader").css("display", "none");

            if (response.status === 'success') {
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
                cargarDetallePOD_gt(id_pod, 0);
                desglosePOD(id_pod);
                $('#paquetes_gt_pod').val('');
                $('#modal_paquetes_gt').modal("hide");
                $(".modal-backdrop").remove();
            } else {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
        },
        error: function (xhr, status, error) {
            $("#loader").css("display", "none");

            alertify.set("notifier", "position", "top-right");
            alertify.error("La petición no se pudo procesar");
        }
    });
}

function editar_pod_gt(id, manifiesto) {
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/editar_pod/" + id);

    $.get(url, function (response) {
        $("#loader").css("display", "none");
        $('#principal').html(response);
        var piloto = $('#id_piloto').val();
        $('#manifiesto_pod_id').val(manifiesto);
        $('#piloto').val(piloto).trigger('change');
        $('#form_editar_titulo').text(manifiesto);
        desglosePOD(id);
        cargarDetallePOD_gt(id, 0);


    })
        .fail(function (xhr) {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar la vista para editar la POD");
        });
}

function ver_pod_gt(id, manifiesto) {
    $("#loader").css("display", "block");
    var url = base_url("index.php/POD_Controller/editar_pod/" + id);

    $.get(url, function (response) {
        $("#loader").css("display", "none");
        $('#principal').html(response);
        var piloto = $('#id_piloto').val();
        $('#piloto').val(piloto).trigger('change');
        $('#form_editar_titulo').text(manifiesto);
        desglosePOD(id);
        cargarDetallePOD_gt(id, 1);
        $('#div_form_encabezadopod').removeClass('d-none');
        $('#btn_editar_encabezado').addClass('d-none');
        $('#btn_cancelar_editar_encabezado').addClass('d-none');
        $('.btn_del_gt_pod').addClass('d-none');
        $('.btn_add_gt_pod').addClass('d-none');
        $('#fecha_entrega').prop('readonly', true);
        $('#piloto').prop('disabled', true);
        $('#placa').prop('readonly', true);
        $('#marchamo').prop('readonly', true);


    })
        .fail(function (xhr) {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "bottom-right");
            alertify.error("No se pudo cargar la vista para editar la POD");
        });
}

function procesar_reporte_impuestos_gt() {
    
    const xmlInput = $('#xml')[0];
    const pdfInput = $('#pdf')[0];

    const xmlFiles = xmlInput ? xmlInput.files : [];
    const pdfFiles = pdfInput ? pdfInput.files : [];

    const invalidXml = [];
    const invalidPdf = [];

    const getExtension = (fileName) => {
        const parts = fileName.split('.');
        return parts.length > 1 ? parts.pop().toLowerCase() : '';
    };

    for (let i = 0; i < xmlFiles.length; i++) {
        if (getExtension(xmlFiles[i].name) !== 'xml') {
            invalidXml.push(xmlFiles[i].name);
        }
    }

    for (let i = 0; i < pdfFiles.length; i++) {
        if (getExtension(pdfFiles[i].name) !== 'pdf') {
            invalidPdf.push(pdfFiles[i].name);
        }
    }

    if (invalidXml.length > 0) {
        alertify.set('notifier', 'position', 'top-right');
        alertify.error('El campo XML solo acepta archivos .xml. Archivo inválido: ' + invalidXml[0]);
        return;
    }

    if (invalidPdf.length > 0) {
        alertify.set('notifier', 'position', 'top-right');
        alertify.error('El campo PDF solo acepta archivos .pdf. Archivo inválido: ' + invalidPdf[0]);
        return;
    }

    if (xmlFiles.length === 0 && pdfFiles.length === 0) {
        alertify.alert('Validación', 'Debe seleccionar al menos un archivo XML y un archivo PDF.');
        return;
    }

    if (xmlFiles.length === 0) {
        alertify.alert('Validación', 'Debe seleccionar al menos un archivo XML.');
        return;
    }

    if (pdfFiles.length === 0) {
        alertify.alert('Validación', 'Debe seleccionar al menos un archivo PDF.');
        return;
    }

    if (xmlFiles.length !== pdfFiles.length) {
        alertify.alert(
            'Validación',
            'La cantidad de archivos XML debe coincidir con la cantidad de archivos PDF. Verifica tus selecciones.'
        );
        return;
    }

    const form = document.getElementById('form_oea');
    const formData = new FormData(form);

    $("#loader").css("display", "block");

    fetch(form.action, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
        .then(async response => {

            if (!response.ok) {
                const text = await response.text();
                throw new Error(text || 'Ocurrió un error al generar el reporte.');
            }

            const disposition = response.headers.get('content-disposition') || '';
            const filename = getFilenameFromDisposition(disposition) || 'CONSOLIDADO_GT.csv';

            const xmlSinPdf = JSON.parse(response.headers.get('X-XML-Sin-PDF') || '[]');
            const pdfSinXml = JSON.parse(response.headers.get('X-PDF-Sin-XML') || '[]');

            const blob = await response.blob();

            return {
                blob,
                filename,
                xmlSinPdf,
                pdfSinXml
            };

        })
        .then(({ blob, filename, xmlSinPdf, pdfSinXml }) => {

            const url = window.URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = filename;

            document.body.appendChild(a);
            a.click();
            a.remove();

            window.URL.revokeObjectURL(url);

            alertify.set("notifier", "position", "top-right");
            alertify.success('Reporte generado correctamente.');

            let mensaje = '';

            if (xmlSinPdf.length > 0) {
                mensaje += `
                <div style="margin-bottom:15px;">
                    <strong style="color:#d98200;">
                        <i class="fa fa-file-code-o"></i>
                        XML sin PDF relacionado
                    </strong>
                    <ul style="margin-top:8px;">
                        ${xmlSinPdf.map(x => "<li>"+x+"</li>").join('')}
                    </ul>
                </div>
            `;
            }

            if (pdfSinXml.length > 0) {
                mensaje += `
                <div>
                    <strong style="color:#c0392b;">
                        <i class="fa fa-file-pdf-o"></i>
                        PDF sin XML relacionado
                    </strong>
                    <ul style="margin-top:8px;">
                        ${pdfSinXml.map(x => "<li>" + x + "</li>").join('')}
                    </ul>
                </div>
            `;
            }

            if (mensaje !== '') {
                alertify.alert(
                    '<i class="fa fa-info-circle"></i> Archivos que no se incluyeron en el reporte',
                    mensaje
                );
            }

        })
        .catch(error => {

            console.error(error);

            alertify.set("notifier", "position", "top-right");
            alertify.error(error.message || 'Ocurrió un error.');

        })
        .finally(() => {

            $("#loader").css("display", "none");

        });

}

function convertir_xml() {
    const files = $('#xml')[0].files;

    if (files.length === 0) {
        alertify.set("notifier", "position", "top-right");
        alertify.error('Por favor, selecciona al menos un archivo XML.');
        return;
    }

    const form = document.getElementById('form_subir_xml');
    const action = form.action;
    const formData = new FormData(form);

    $("#loader").css("display", "block");

    fetch(action, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
        .then(async response => {
            if (!response.ok) {
                const text = await response.text();
                throw new Error(text || "Error en el servidor: " + response.status );
            }

            const contentType = response.headers.get('content-type') || '';
            const disposition = response.headers.get('content-disposition') || '';
            const filename = getFilenameFromDisposition(disposition) || 'reporte.csv';
            const blob = await response.blob();

            if (contentType.includes('application/json') || contentType.includes('text/html')) {
                const text = await blob.text();
                const cleaned = text.replace(/<[^>]+>/g, '').trim();
                throw new Error(cleaned || 'Ocurrió un error al generar el reporte.');
            }

            return { blob, filename };
        })
        .then(({ blob, filename }) => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

            alertify.set("notifier", "position", "top-right");
            alertify.success('Reporte generado exitosamente');
        })
        .catch(error => {
            console.error(error);
            alertify.set("notifier", "position", "top-right");
            alertify.error(error.message || 'Ocurrió un error al generar el reporte.');
        })
        .finally(() => {
            $("#loader").css("display", "none");
        });
}

function getFilenameFromDisposition(disposition) {
    const filenameRegex = /filename\*?=(?:UTF-8'')?(?:"?)([^";\n]+)/i;
    const matches = filenameRegex.exec(disposition);
    if (matches && matches[1]) {
        return decodeURIComponent(matches[1].replace(/"/g, '').trim());
    }
    return null;
}

function zip_ducas_renombradas() {
    const files = $('#ducas')[0].files;
    const nombreZip = $('#nombre_zip').val().trim();

    if (files.length === 0) {
        alertify.set("notifier", "position", "top-right");
        alertify.error('Seleccione al menos una DUCA.');
        return;
    }

    if (nombreZip === "") {
        alertify.set("notifier", "position", "top-right");
        alertify.error('Ingrese un nombre para el archivo ZIP.');
        $('#nombre_zip').focus();
        return;
    }

    const form = document.getElementById('form_renombrar_ducas');
    const formData = new FormData(form);

    $("#loader").css("display", "block");

    fetch(form.action, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
        .then(async response => {

            if (!response.ok) {
                const text = await response.text();
                throw new Error(text || 'Ocurrió un error al generar el ZIP.');
            }

            const disposition = response.headers.get('content-disposition') || '';
            const filename = getFilenameFromDisposition(disposition) || (nombreZip + ".zip");

            const noRenombrados = JSON.parse(response.headers.get('X-No-Renombrados') || "[]");
            const duplicados = JSON.parse(response.headers.get('X-Duplicados') || "[]");

            const blob = await response.blob();

            return {
                blob,
                filename,
                noRenombrados,
                duplicados
            };

        })
        .then(({ blob, filename, noRenombrados, duplicados }) => {

            const url = window.URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = filename;

            document.body.appendChild(a);
            a.click();
            a.remove();

            window.URL.revokeObjectURL(url);

            alertify.set("notifier", "position", "top-right");
            alertify.success('ZIP generado correctamente.');

            let mensaje = '';

            if (noRenombrados.length > 0) {
                mensaje += `
                <div style="margin-bottom:15px;">
                    <strong style="color:#d98200;">
                        <i class="fa fa-exclamation-triangle"></i>
                        No se pudieron renombrar
                    </strong>
                    <ul style="margin-top:8px;">
                        ${noRenombrados.map(a => "<li>" + a + "</li>").join('')}
                    </ul>
                </div>
            `;
            }

            if (duplicados.length > 0) {
                mensaje += `
                <div>
                    <strong style="color:#c0392b;">
                        <i class="fa fa-copy"></i>
                        Archivos duplicados (omitidos)
                    </strong>
                    <ul style="margin-top:8px;">
                        ${duplicados.map(a => "<li>" + a + "</li>").join('')}
                    </ul>
                </div>
            `;
            }

            if (mensaje !== '') {
                alertify.alert(
                    '<i class="fa fa-info-circle"></i> Resultado del procesamiento',
                    mensaje
                );
            }

        })
        .catch(error => {

            console.error(error);

            alertify.set("notifier", "position", "top-right");

            alertify.error(error.message || 'Ocurrió un error.');

        })
        .finally(() => {

            $("#loader").css("display", "none");

        });

}


/*function procesar_reporte_impuestos_gt() {
    const xmlInput = $('#xml')[0];
    const pdfInput = $('#pdf')[0];

    const xmlFiles = xmlInput ? xmlInput.files : [];
    const pdfFiles = pdfInput ? pdfInput.files : [];

    const invalidXml = [];
    const invalidPdf = [];

    const getExtension = (fileName) => {
        const parts = fileName.split('.');
        return parts.length > 1 ? parts.pop().toLowerCase() : '';
    };

    for (let i = 0; i < xmlFiles.length; i++) {
        if (getExtension(xmlFiles[i].name) !== 'xml') {
            invalidXml.push(xmlFiles[i].name);
        }
    }

    for (let i = 0; i < pdfFiles.length; i++) {
        if (getExtension(pdfFiles[i].name) !== 'pdf') {
            invalidPdf.push(pdfFiles[i].name);
        }
    }

    if (invalidXml.length > 0) {
        alertify.set('notifier', 'position', 'top-right');
        alertify.error('El campo XML solo acepta archivos .xml. Archivo inválido: ' + invalidXml[0]);
        return;
    }

    if (invalidPdf.length > 0) {
        alertify.set('notifier', 'position', 'top-right');
        alertify.error('El campo PDF solo acepta archivos .pdf. Archivo inválido: ' + invalidPdf[0]);
        return;
    }

    if (xmlFiles.length === 0 && pdfFiles.length === 0) {
        alertify.alert()
            .setting({
                title: 'Validación',
                message: 'Debe seleccionar al menos un archivo XML y un archivo PDF.',
                transition: 'fade',
                onok: function () {
                }
            })
            .show();
        return;
    }

    if (xmlFiles.length === 0) {
        alertify.alert()
            .setting({
                title: 'Validación',
                message: 'Debe seleccionar al menos un archivo XML.',
                transition: 'fade',
                onok: function () {
                }
            })
            .show();
        return;
    }

    if (pdfFiles.length === 0) {
        alertify.alert()
            .setting({
                title: 'Validación',
                message: 'Debe seleccionar al menos un archivo PDF.',
                transition: 'fade',
                onok: function () {
                }
            })
            .show();
        return;
    }

    if (xmlFiles.length !== pdfFiles.length) {
        alertify.alert()
            .setting({
                title: 'Validación',
                message: 'La cantidad de archivos XML debe coincidir con la cantidad de archivos PDF. Verifica tus selecciones.',
                transition: 'fade',
                onok: function () {
                }
            })
            .show();
        return;
    }

    const formData = new FormData();

    for (let i = 0; i < xmlFiles.length; i++) {
        formData.append('xml[]', xmlFiles[i]);
    }

    for (let i = 0; i < pdfFiles.length; i++) {
        formData.append('pdf[]', pdfFiles[i]);
    }

    document.getElementById('form_oea').submit();


} 




*/