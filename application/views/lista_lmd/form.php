<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>
	<div class="container-fluid p-0 mt-0 " style="background:#E8E8EB">
		<div class="row pt-0 mt-0">
			
			<div class="col pt-0 mt-0">
                
                <h4 class="text-muted text-center"> Listado Manifiesto</h4>
            </div>

			
		</div>
	</div>
	<div class="container">
		<div class="row">

			<div class="container-fluid m-top">
				<div class="row">
					<div class="table-responsive">
						<table class="table custom-table" id="tbl_tipo_entrega">
							<thead>
								<tr>
									<th>#</th>
									<th>Fecha</th>
									<th>Observaciones</th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody id="tabla_tipo_entrega">
								<?php
								if (isset($listado_lmd)) {
									foreach ($listado_lmd as $row) {
								?>
										<tr>
											<td><?php echo $row->id; ?></td>
											<td><?php echo $row->fecha_manifiesto; ?></td>
											<td><?php echo $row->observaciones; ?></td>
											<td>
												<a href="#" class="btn btn-secondary btn-sm mx-1" onclick="ver_manifiesto(<?php echo ($row->id); ?>)" title="Ver Manifiesto" style="margin:0px;">
													<i class="fa fa-th-list" aria-hidden="true"></i>

													<a href="#" class="btn btn-primary btn-sm mx-1" onclick="agregar_manifiesto(<?php echo ($row->id); ?>)" title="Editar Manifiesto" style="margin:0px;">
														<i class="fa fa-pencil" aria-hidden="true"></i>

														<a href="#" class="btn btn-danger btn-sm mx-1" onclick="eliminar_manifiesto(<?php echo ($row->id); ?>)" title="Eliminar Manifiesto" style="margin:0px;">
															<i class="fa fa-trash" aria-hidden="true"></i>
											</td>
										</tr>
								<?php
									}
								}
								?>
							</tbody>
						</table>

					</div>
				</div>
			</div>
		</div>




	</div>

	<!-- llamado de libreria de datatable -->
	<?php $this->load->view('lib/datatable_scritp'); ?>

	<script>
		$(document).ready(function() {
			$("#tbl_tipo_entrega").DataTable({
				"responsive": true,
				"lengthChange": true,
				"paging": true,
				"searching": true,
				"ordering": true,
				"info": true,
				"lengthMenu": [
					[5, 10, 100, -1],
					[5, 10, 100, "Todos"]
				],
				"order": [
					[0, 'desc']
				],
				"language": {
					"info": "Mostrando _START_ a _END_ de _TOTAL_ entradas"
				},
				// Configuración del diseño con la barra de búsqueda y botón en la misma línea a la derecha
				"dom": '<"d-flex justify-content-between align-items-center mb-2"<"left-section"l><"right-section d-flex align-items-center"f>>rtip'
			});

			// Añadimos el botón al lado del campo de búsqueda y le damos un margen de 2px
			$("div.right-section").append(`
            <button type="button" class="btn btn-primary btn-sm" style="margin-left: 2px;" onclick="agregar_manifiesto(0);">
                <i class="fa fa-plus" aria-hidden="true"></i> Crear
            </button>
        `);
		});
	</script>


</body>

</html>