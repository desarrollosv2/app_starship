<?php $i = 1;
    
    foreach ($tracking as $row) : ?>
        <tr>
            <td style="font-size: 14px;"> <?php echo $i ?></td>
            <td style="font-size: 14px;"> <?php echo $row->fecha_creacion ?></td>
            <td style="font-size: 14px;"> <?php echo $row->awb ?></td>
            <td style="font-size: 14px;"> <?php echo $row->tracking_number ?></td>
            <td style="font-size: 14px;"> <?php echo $row->tracking_replace ?></td>
            <td style="font-size: 14px;"> <?php echo $row->consignee ?> </td>
        </tr> 
<?php $i = $i + 1; endforeach ?>

<script>
     $(document).ready(function() 
    {
        $("#tbl_crear_guia").DataTable({
                "retrieve"     : true, 
                "paging"       : true,
                "searching"    : true,
                "ordering"     : true,
                "info"         : true,
                "language"     : 
            {
                "lengthMenu"   : "Mostrar _MENU_ registros por página",
                "zeroRecords"  : "No se encontraron datos que mostrar",
                "info"         : "Mostrando Página _PAGE_ de _PAGES_",
                "infoEmpty"    : "registros no disponibles",
                "infoFiltered" : "(filtered from _MAX_ total records)",
                "sSearch"      : "Buscar:",
                "oPaginate"    : 
              {
                "sFirst"       : "Primero",
                "sLast"        : "Último",
                "sNext"        : "Siguiente",
                "sPrevious"    : "Anterior",
              },
            },
                "lengthChange" : true,
                "buttons"      : ["copy", "csv", "excel", "print", "colvis"]
        }).buttons().container().appendTo('#tbl_crear_guia_wrapper .col-md-6:eq(0)');
    });
</script>