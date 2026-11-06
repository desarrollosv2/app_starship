<?php $i = 1;

foreach ($cliente as $row) : ?>

  <tr>
    <td style="font-size: 14px;"> <?php echo $row->tracking_number ?></td>
    <td style="font-size: 14px;"> <?php echo $row->tracking_replace ?></td>
    <td style="font-size: 14px;"> <?php echo $row->consignee ?></td>
    <td style="font-size: 14px;"> <?php echo $row->consignee_address1 ?></td>
    <td style="font-size: 14px;"> <?php echo $row->consignee_phone ?></td>
    <td style="font-size: 14px;"> <?php echo $row->consignee_email ?></td>
    <td style="font-size: 14px;"> <?php echo $row->comentario ?></td>
    <td style="font-size: 14px;"> <?php echo $row->nombre ?></td>
    
  </tr>

  <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ingresar Comentario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <input type="hidden" class="form-control" id="consignee_email" name="consignee_email">
        <div class="modal-body">
          <form method="$_POST" class="filtro_reporte" id="filtro_reporte">
            <div class="form-group">
              <label for="message-text" class="col-form-label">Correo</label>
              <input type="text" class="form-control" id="correo" name="correo" value="<?php echo $row->consignee_email; ?>">
            </div>

            <div class="form-group">
              <label for="message-text" class="col-form-label">Comentario</label>
              <textarea class="form-control" name="comentario" id="comentario"></textarea>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" onclick="guardar_comentario();" data-dismiss="modal">Agregar</button>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>

<?php $i = $i + 1;
endforeach ?>

<script>
  $(document).ready(function() 
  {
    $("#tbl_detalle").DataTable({
      "retrieve"     : true,
      "paging": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "language": 
      {
        "lengthMenu": "Mostrar _MENU_ registros por página",
        "zeroRecords": "No se encontraron datos que mostrar",
        "info": "Mostrando Página _PAGE_ de _PAGES_",
        "infoEmpty": "registros no disponibles",
        "infoFiltered": "(filtered from _MAX_ total records)",
        "sSearch": "Buscar:",
        "oPaginate": 
        {
          "sFirst": "Primero",
          "sLast": "Último",
          "sNext": "Siguiente",
          "sPrevious": "Anterior",
        },
      },
      "lengthChange": true,
      "buttons": ["excel", "colvis"]
    }).buttons().container().appendTo('#tbl_detalle_wrapper .col-md-6:eq(0)');
  });
</script>