<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Cortes</title>
</head>

<body>

	<div class="container">
		<div class="row mt-2">
			<div class="col-6 mb-1">
				<h2 class="fw-bold text-muted ">Cortes</h2>
			</div>
			<div class="col-6   mb-3 text-right">
				<button class="btn btn-primary mt-3 text-right" id="btnAgregar">Agregar corte</button>
			</div>
		</div>
		<div class="row">
			<table class="table " id="tblCortes">
				<thead>
					<tr>
						<th>Estado</th>
						<th>Corte</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (isset($cortes)) {
						foreach ($cortes as $row => $value) {
					?>
							<tr>
								<td style="<?= $value->estado_corte == 1 ? 'background-color:rgb(40, 167, 69); font-weight: bold; color: white;' : '' ?>">
									<?= $value->estado_corte == 1 ? 'Activo' : 'Inactivo' ?>
								</td>
								<td><?= $value->hora_corte ?></td>
								<td>
									<button type="button" id="btnEditar" class="btn btn-outline-primary btn-sm" onclick="Editar(<?= $value->id_corte ?>)">Editar</button>
									<button type="button" id="btnEstado" class="btn btn-outline-success btn-sm" onclick="CambiarEstado(<?= $value->id_corte ?>)">Estado</button>
									<button type="button" id="btnEliminar" class="btn btn-outline-danger btn-sm" onclick="Eliminar(<?= $value->id_corte ?>)">Eliminar</button>
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

	<div class="modal fade" id="modalCorte" tabindex="-1" role="dialog" aria-labelledby="modalCorteLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header">
					<h5 class="modal-title" id="modalCorteLabel">Agregar nuevo corte</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body" id="modalForm">
					<!-- Aquí tu contenido dinámico -->
				</div>

			</div>
		</div>
	</div>




	<script>
		$(document).ready(function() {
			function base_url(url) {
				return window.location.origin + "/app_starship/" + url;
			}




			$('#btnAgregar').on('click', function(e) {
				e.preventDefault();

				var url = base_url("index.php/CortesController/cargarFrm");
				$.get(url, function(result) {
					const modal = new bootstrap.Modal(document.getElementById('modalCorte'), {
						backdrop: 'static',
						keyboard: false
					});
					modal.show();
					$('#modalForm').html(result);
				});

			});
		});

		function Editar(id_corte) {
			var url = base_url('index.php/CortesController/editarCorte/' + id_corte);


			$.get(url, function(response) {
				$('#modalCorteLabel').text('Editar corte');
				const modal = new bootstrap.Modal(document.getElementById('modalCorte'), {
					backdrop: 'static',
					keyboard: false
				});
				modal.show();
				$('#modalForm').html(response);
			});
		}

		function Eliminar(id_corte) {
			var url = base_url("index.php/CortesController/eliminarCorte/" + id_corte);

			alertify.confirm('Eliminar corte',
					'¿Desea eliminar el corte?',
					function() {
						// Si el usuario confirma
						$.ajax({
							type: "POST",
							url: url,
							data: {
								id_corte
							},
							success: function(data) {
								alertify.set('notifier', 'position', 'top-right');
								alertify.success('Corte eliminado correctamente');
								setTimeout(function() {
									recolecta_corte();
								}, 1000); // Esperamos 1 segundo para que el usuario vea la notificación
							},
							error: function(xhr, status, error) {
								alertify.set('notifier', 'position', 'top-right');
								alertify.error('Error al eliminar el corte');
							}
						});
					},
					function() {
						// Si el usuario cancela
						alertify.set('notifier', 'position', 'top-right');
						alertify.message('Acción cancelada');
					}
				).set('labels', {
					ok: 'Sí, eliminar',
					cancel: 'Cancelar'
				})
				.set('closable', false);
		}

		function CambiarEstado(id_corte) {
			alertify.confirm(
					'¿Desea cambiar el estado?',
					"Desea cambiar el estado del corte a 'Activo'?",
					function() {
						$.ajax({
							type: "POST",
							url: base_url("index.php/CortesController/cambiarEstado/" + id_corte),
							data: {
								id_corte
							},
							dataType: 'json',
							success: function(response) {
								alertify.set('notifier', 'position', 'top-right');

								alertify.success("Estatus ha sido cambiado correctamente");
								setTimeout(function() {
									recolecta_corte();
								}, 1000);

							},
							error: function(xhr, status, error) {
								alertify.set('notifier', 'position', 'top-right');
								alertify.error('Error al cambiar el estado');
							}
						});
					},
					function() {
						alertify.set('notifier', 'position', 'top-right');
						alertify.message('Operación cancelada');
					}
				).set('labels', {
					ok: 'Sí, Cambiar!',
					cancel: 'Cancelar'
				})
				.set('closable', false)
				.set('transition', 'fade');
		}
	</script>
</body>

</html>