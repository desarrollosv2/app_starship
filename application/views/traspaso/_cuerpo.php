<!--form enctype="multipart/form-data" class="lguias">
</form>
<?php 
/*if (isset($preclasifica)) {
foreach ($preclasifica as $row) { ?>
<tr>
    <td><?php echo $row->idpreclasificacion; ?></td>
    <td><?php echo $row->manifiesto; ?></td>
    <td><?php echo $row->fecha; ?></td>
    <td>
        <a href='#Modal_Add_Guia' onclick="mostrar_referencia()"  class="btn btn-primary btn-sm" data-id=""
            title="Agregar Guia" data-toggle="modal" style="margin:0px;"
            data-book-id="<?php  echo $row->idpreclasificacion;?>" data-book-id1="<?php echo $row->manifiesto;?>"
            data-book-id2="<?php echo $row->referencia;?>">
            <i class="fa fa-plus"></i></a>

            <a href='#' class="btn btn-danger btn-sm" data-toggle="modal"  style="margin:0px;"
            title="Eliminar Referencia"
            onclick="confirmar_eliminar_referencia(<?php echo $row->idpreclasificacion; ?>);return false;">
            <i class="fa fa-trash"></i> </a>

        <a href="#" class="btn btn-warning btn-sm" onclick="consulta_guias(<?php echo trim($row->referencia); ?> )"
             title="Listado de Guias"  style="margin:0px;">
            <i class="fa fa-list"></i></a>
    </td>
</tr>
<?php
}
}
?>