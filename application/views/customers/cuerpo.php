
<?php

if (isset($lista)) {
    $c=1;
foreach ($lista as $row) {  ?>
<tr>
    <td><?php echo $c; ?></td>
    <td><?php echo $row->idwarehouse; ?></td>
    <td><?php echo $row->fecha_ingreso; ?></td>
    <td><?php echo $row->fecha_salida; ?></td>
    <td><?php echo $row->remitente; ?></td>
    <td><?php echo $row->descripcion; ?></td>
    <td><?php echo $row->bultos; ?></td>
    <td><?php echo $row->peso; ?></td>
    <td><?php echo $row->tracking; ?></td>
    <td><?php echo $row->pre_alertada; ?></td>
    <td><?php echo $row->estado; ?></td>
    <td><?php echo $row->total; ?></td>
   
    <td>
        <a href="#" class="btn btn-default btn-sm" onclick="lista_estatus(<?php echo trim($row->idwarehouse); ?> )"
            title="Ver Estatus" style="margin:0px;">
            <i class="fa fa-list"></i></a>
    </td>

    <td>
        <a href="#" class="btn btn-default btn-sm" onclick="consulta_guias(<?php echo trim($row->referencia); ?> )"
            title="Ver adjuntos" style="margin:0px;">
            <i class="fa fa-paperclip"></i></a>
    </td>
    
     <td>
        <a href="#" class="btn btn-default btn-sm" onclick="lista_servicios_express(<?php echo trim($row->idwarehouse); ?>)"
            title="Crear Guia Express" style="margin:0px;">
            <i class="fa fa-paperclip"></i></a>
    </td>

    <!-- <td>
        <a href="#" class="btn btn-default btn-sm" onclick="prealertas()"
            title="Crear Pre-alerta" style="margin:0px;">
            <i class="fa fa-paperclip"></i></a>
    </td>
-->


</tr>
<?php $c=$c+1;
}
}
?>