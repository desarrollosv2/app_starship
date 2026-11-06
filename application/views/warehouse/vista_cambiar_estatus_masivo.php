<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
	<link href='//fonts.googleapis.com/css?family=Montserrat:thin,extra-light,light,100,200,300,400,500,600,700,800'
		rel='stylesheet' type='text/css'>
	<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.2.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	
	<title>Cambiar Estatus</title>
	<style>
		.select2-container--bootstrap4 .select2-selection--single {
			height: calc(2.25rem + 2px);
			/* Altura estándar de un select */
			padding: 0.375rem 0.75rem;
			/* Ajusta el padding vertical */
			border: 1px solid #ced4da;
			border-radius: .25rem;
			line-height: 1.5rem;
			/* Ajusta la línea de altura para centrar el texto */
			display: flex;
			align-items: center;
			/* Alineación vertical */
		}


		.card {
			border-radius: 10px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		}
	</style>
</head>

<body>	
	<div class="container text-center">
		<div class="row">
			<div class="col">
				<div class="card text-center">
					<div class="card-header">
						<h4 class="text-muted text-justify">Cambiar Estatus Masivo</h4>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6"></div>
							<div class="alert alert-primary alert-dismissible col-md-6" role="alert" name="msg" id="msg" style="display: none">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong>
									<p id="msg_alert" name="msg_alert" class="text-justify"></p>
								</strong>
							</div>
						</div>
						<form enctype="multipart/form-data" class="cambiarestatus forminline" id="cambiarestatus">
							<div class="row form-group">
								<div class="col-md-2">
									<p class="center_v text-justify"> Estatus </p>
								</div>
								<div class="col-md-8">
									<div class="form-group">
										<select name="estatus" id="estatus" class="select2 form-control" required>
											<option value=""></option>
											<?php foreach ($estatus as $row): ?>
												<option value="<?php echo $row->id_estatus.'-'.$row->nombre; ?>">
													<?php echo $row->id_estatus . ' - ' . $row->nombre; ?>
												</option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row" id="div_n" style="display:none;">
									<div class="col-md-2">
										<p class="center_v text-justify"> Nombre</p>
									</div>
									<div class="col-md-5">
										<div class="box">
											<div class="container-1">
												<span class="icon"><i class="fa fa-user"></i></span>
												<input type="search" id="n_retiro" name="n_retiro" placeholder="Nombre que retira paquete" class="form-control" />
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-md-2">
									<p class="center_v text-justify"> Warehouse </p>
								</div>
								<div class="col-md-8">
									<div class="mb-3">

										<div class="form-floating">
										  <textarea class="form-control" id="txtListWarehouse" name="txtListWarehouse" style="height: 40vh" placeholder="Ingresa el listado de guías aqui"></textarea>
										 
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<button type="button" class="btn btn-primary" onclick="insertar_estatus_masivo()">Aplicar</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$("#estatus").change(function() {
				var estado = $(this).val();
				if (estado == '59') {
					$('#div_n').css('display', 'flex');
				} else {
					$('#n_retiro').val("");
					$('#div_n').css('display', 'none');
				}
			});

			$('#estatus').select2({
				theme: 'bootstrap4',
			});
		});
	</script>
</body>

</html>