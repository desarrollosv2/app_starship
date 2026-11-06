function base_url(url) {
    return window.location.origin + "/app_starship/" + url;
}

function lista_usuarios_sistema()
{
    $("#loader").css("display", "block");
    var url = base_url("index.php/Usuarios_Controller/lista_usuarios_sistema/");

  $.ajax({
    url: url,
    type: "GET",
    success: function (response) {
      $('#div_tbl_usuarios_sistema').html(response);
      $('#tabla_usuarios_sistema').DataTable({
        destroy: true,
        "dom": 'Bfrtip',

        "buttons": [
          {
            extend: 'excelHtml5',

            text: '<i class="fa fa-file-excel-o"></i> Excel',

            className: 'btn btn-success',

            title: 'Usuarios del sistema',

            exportOptions: {

              // No exportar ACCIONES
              columns: ':not(:last-child)'
            }

          }

        ],
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
        },

        //"order": [[1, "desc"]],
        "ordering": false,
      });
    },
    error: function (xhr) {
      var respuesta = xhr.responseJSON;

      if (xhr.status === 403 && respuesta) {
        alertify.error(respuesta.msg);
        return;
      }

      alertify.error("No fue posible cargar el módulo.");
    },
    complete: function () {
      $("#loader").css("display", "none");
    }
  });
}


function agregar_nuevo_usuario_sistema()
{
  $("#loader").css("display", "block");
  var url = base_url("Usuarios_Controller/agregar_nuevo_usuario_sistema/");

  $.ajax({
        url: url,
        type: "GET",
        success: function(response)
        {
          $("#div_gestion_usuarios_sistema_form").html(response);
          $('#titulo_modal_usuario_sistema').text('Agregar nuevo usuario');
          $('#Modal_Usuario').modal({ backdrop: 'static', keyboard: false },"show");
            
        },
        error: function (xhr) {
              var respuesta = xhr.responseJSON;

              if (xhr.status === 403 && respuesta) {
                  alertify.error(respuesta.msg);
                  return;
              }

              alertify.error("No fue posible cargar el módulo.");
      },
      complete: function () {
        $("#loader").css("display", "none");
      }
  });
}


function editar_usuario_sistema(id_usuario)
{
  $("#loader").css("display", "block");
  var url = base_url("Usuarios_Controller/editar_usuario_sistema/" + id_usuario);

  $.get(url, function(response)
  {
    $("#loader").css("display", "none");
    //console.log(response);
    $("#div_gestion_usuarios_sistema_form").html(response);
    $('#titulo_modal_usuario_sistema').text('Editar usuario');
    $('#Modal_Usuario').modal({ backdrop: 'static', keyboard: false },"show");
  }).fail(function(xhr) {
        $("#loader").css("display", "none");
        alertify.set("notifier", "position", "bottom-right");
        var respuesta = xhr.responseJSON;
        alertify.error(respuesta && respuesta.msg
          ? respuesta.msg
          : "No se pudo cargar la información del usuario.");
    })
}

function guardar_usuario_sistema()
{
  $("#loader").css("display", "block");
    var url = base_url("Usuarios_Controller/guardar_usuario_sistema/");
    var data = $('#form_usuarios_sistema').serialize();

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
            if (response.status === 'errors')
            {
                $('.form-control').removeClass('is-invalid');

                $('.invalid-feedback')
                    .removeClass('d-block')
                    .text('');

                for (const campo in response.errors)
                {
                    const mensaje = response.errors[campo];

                    $(`#${campo}`).addClass('is-invalid');

                    $(`#error_${campo}`)
                        .addClass('d-block')
                        .text(mensaje);
                }

                alertify.alert()
                    .setting({
                        title: 'Validación',
                        message: response.message || 'Por favor, complete los campos obligatorios.',
                        transition: 'fade'
                    })
                    .show();

                return;
            }
            else if(response.status === 'success')
            {
                alertify.set("notifier", "position", "top-right");
                alertify.success(response.msg);
                lista_usuarios_sistema();
                $('#Modal_Usuario').modal("hide");
                $(".modal-backdrop").remove();

            }else
            {
                alertify.set("notifier", "position", "top-right");
                alertify.error(response.msg);
            }
        }, error: function (xhr)
        {
            $("#loader").css("display", "none");
            alertify.set("notifier", "position", "top-right");
          var respuesta = xhr.responseJSON;
          alertify.error(respuesta && respuesta.msg
            ? respuesta.msg
            : "Ocurrió un error en la petición.");
        }
    });
}

function mostrar_contrasena()
{
    var input = document.getElementById("usuario_clave");
    var icono = document.getElementById("icono_contrasena");

    if (input.type === "password") {
        input.type = "text";
        icono.classList.remove("fa-eye");
        icono.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icono.classList.remove("fa-eye-slash");
        icono.classList.add("fa-eye");
    }
}

function inactivar_usuario_sistema(id_usuario)
{
  
  alertify.confirm()
        .setting({
            title: 'Inactivar',
            message: '¿Desea inactivar este usuario?',
            labels: { ok: 'Sí', cancel: 'Cancelar' },
            onok: function () {
                var estado = 0;
                var url = base_url("index.php/Usuarios_Controller/estado_usuario/" + id_usuario + "/" + estado);
                $("#loader").css("display", "block");

                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function (response) {
                        var data = JSON.parse(response);
                        $("#loader").css("display", "none");

                        if(data.status === 'success')
                        {
                            alertify.set("notifier", "position", "top-right");
                            alertify.success(data.msg);
                        }else{
                            alertify.set("notifier", "position", "top-right");
                            alertify.error(data.msg);
                        }


                        lista_usuarios_sistema();
                        
                    },
                    error: function(xhr, status, error)
                    {
                        $("#loader").css("display", "none");
                        
                        alertify.set("notifier", "position", "top-right");
                      var respuesta = xhr.responseJSON;
                      alertify.error(respuesta && respuesta.msg
                        ? respuesta.msg
                        : "La petición no se pudo procesar");
                    }
                });
            },
            oncancel: function () {
                alertify.error('Cancelado');
            }
        }).show();
}

function activar_usuario_sistema(id_usuario)
{
  alertify.confirm()
        .setting({
            title: 'Activar',
            message: '¿Desea activar este usuario?',
            labels: { ok: 'Sí', cancel: 'Cancelar' },
            onok: function () {
                var estado = 1;
                var url = base_url("index.php/Usuarios_Controller/estado_usuario/" + id_usuario + "/" + estado);
                $("#loader").css("display", "block");

                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function (response) {
                        var data = JSON.parse(response);
                        $("#loader").css("display", "none");

                        if(data.status === 'success')
                        {
                            alertify.set("notifier", "position", "top-right");
                            alertify.success(data.msg);
                        }else{
                            alertify.set("notifier", "position", "top-right");
                            alertify.error(data.msg);
                        }


                        lista_usuarios_sistema();
                        
                    },
                    error: function(xhr, status, error)
                    {
                        $("#loader").css("display", "none");
                        
                        alertify.set("notifier", "position", "top-right");
                      var respuesta = xhr.responseJSON;
                      alertify.error(respuesta && respuesta.msg
                        ? respuesta.msg
                        : "La petición no se pudo procesar");
                    }
                });
            },
            oncancel: function () {
                alertify.error('Cancelado');
            }
        }).show();
}