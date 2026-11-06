function listado_lmd() 
{
    var url = base_url("index.php/ListadoLMD_Controller/listado_lmd");
    $.get(url, function(data) 
    {
        $("#principal").html(data);
    });
}


function agregar_manifiesto(id) 
{
    var url = base_url("index.php/ListadoLMD_Controller/agregar_manifiesto/" + id);
	$.get(url, function (data) 
    {
		$("#principal").html(data); 
  
        if(id > 0)
        {
          $('#id').val(id);
          document.getElementById("titulo").innerHTML = "Editar Manifiesto";
        }
        else
        {
          document.getElementById("titulo").innerHTML = "Agregar Manifiesto"
        }
  
    });
}

function guardar_manifiesto()  //Funcion para guardar las partidas creadas
{ 
  
  url_destino = "index.php/ListadoLMD_Controller/guardar_manifiesto/";
  formData = new FormData($(".add_manifiesto")[0]);

  var message = "";
  $.ajax({
      url: base_url(url_destino),
      type: "POST",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      beforeSend: function() 
      {
  
      },
      success: function(data) 
      {
        listado_lmd();
        alertify.set("notifier", "position", "top-right");
        alertify.success("La Promocion ha sido actualizado correctamente");
      },
      complete: function() 
      {
      
      }
  });
}


function ver_manifiesto(id)
{
   
    var url = base_url("index.php/ListadoLMD_Controller/ver_manifiesto/" + id);
    $.get(url, function(data) 
    {
        $("#principal").html(data);
        document.getElementById("no_guia").focus();
    });
         
}

function guardar_guia(id)
{
    var no_guia   =   $("#no_guia").val(); 
    var url = base_url("index.php/ListadoLMD_Controller/guardar_guia_man/" + id + "/" + no_guia);     
    
    $.get(url, function(data) 
    {
        document.getElementById("tabla_no_guia").innerHTML = data;
        ver_manifiesto(id);
    });
}


function editar_manifiesto(id)
{
    var url = base_url("index.php/ListadoLMD_Controller/editar_manifiesto/" + id);
    $.get(url, function(data) 
    {
       // $("#principal").html(data);
    });
}

function eliminar_manifiesto(id)
{
    
    alertify.confirm("���Esta seguro de eliminar este manifiesto?",
      function() 
      {
        var url = base_url("index.php/ListadoLMD_Controller/eliminar_manifiesto/" + id);
        $.get(url, function(data) 
        {
          alertify.set("notifier", "position", "top-right");
          alertify.success("El Manifiesto ha sido eliminado correctamente");
          $(".modal-backdrop").remove();
          $("#form .close").click();
          listado_lmd(); 
        });
      },
          function() 
          {
              alertify.error("Cancelado");
          }
      )
      .set({ title: "Eliminar " })
      .set({ labels: { ok: "Aceptar", cancel: "Cancelar" } });      
}

function eliminar_guia_manifiesto(id_guia, id)
{
    alertify.confirm("Esta seguro de eliminar esta guia?",
      function() 
      {
        var url = base_url("index.php/ListadoLMD_Controller/eliminar_guia_manifiesto/" + id_guia);
        $.get(url, function(data) 
        {
          alertify.set("notifier", "position", "top-right");
          alertify.success("La Guia ha sido eliminada correctamente");
          $(".modal-backdrop").remove();
          $("#form .close").click();
          ver_manifiesto(id);
        });
      },
          function() 
          {
              alertify.error("Cancelado");
          }
      )
      .set({ title: "Eliminar " })
      .set({ labels: { ok: "Aceptar", cancel: "Cancelar" } });      
}

function generar_guia_pdf(id, fecha_manifiesto, observaciones)
{
   var url = base_url("index.php/ListadoLMD_Controller/generar_guia_pdf/" + id + "/" + fecha_manifiesto + "/" + observaciones);

   $("#loader").css("display", "block");
   $.get(url, function (data) 
   {
       ver_report_guiasPDF("/document/reporte_ventas/" + data);
       $("#loader").css("display", "none");
   });
}

function ver_report_guiasPDF(ruta) 
{
    var url = ruta;
	window.open(base_url(url), "ventana1", "width = 600, height = 600, scrollbars = no, toolbar = no, titlebar = no, menubar = no");
}