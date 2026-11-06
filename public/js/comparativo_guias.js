function base_url(url) 
{
    return window.location.origin + "/app_starship/" + url;
}

function comparativo_guias() 
{
  var url = base_url("index.php/Comparativo_guias_Controller/comparativo_guias");
  $.get(url, function (data) 
  {
    $("#principal").html(data);
  });
}

// Funcion para buscar cualquier tipo de dato desde un formulario
function buscar_fecha_guia() 
{
  var fecha_desde_guia = $("#fecha_desde_guia").val();
  var fecha_hasta_guia = $("#fecha_hasta_guia").val();
 

  if (fecha_desde_guia) 
  {
    
  }
  else 
  {
    alertify.alert("", "Seleccione la fecha inicial", "");
    return false;
  }

  if (fecha_hasta_guia) 
  {

  }
  else 
  {
    alertify.alert("", "Seleccione la fecha final", "");
    return false;
  }

  var url = base_url("index.php/Comparativo_guias_Controller/buscar_fecha_guia/" + fecha_desde_guia + "/" + fecha_hasta_guia);
  $.get(url, function (data) 
  {
    $("#tabla_tracking_guia").html(data);  //$("Nombre segun el id del tbody").html(data);
  });
}


function PDF_buscar_fecha_guia()
{
  var fecha_desde_guia = $("#fecha_desde_guia").val();
  var fecha_hasta_guia = $("#fecha_hasta_guia").val();
  
  var url = base_url("index.php/Comparativo_guias_Controller/PDF_buscar_fecha_guia/" + fecha_desde_guia +  "/" + fecha_hasta_guia);

  $.get(url, function (data) 
  {
      var datos = JSON.parse(data);
      var directorio = datos["destino"];
      var archivo = datos["nombre_archivo"];
      var documento = directorio + archivo;
      ver_report_fecha_guiaPDF(documento);
  });
}

function ver_report_fecha_guiaPDF(ruta) 
{
  var url = ruta;
  //alert("lo que trae el data" + ruta)
  window.open(base_url(url), "ventana1", "width=600,height=600,scrollbars=no,toolbar=no, titlebar=no, menubar=no"
  );
}