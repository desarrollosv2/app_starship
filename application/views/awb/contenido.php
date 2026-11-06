<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="preconnect" href="https://fonts.gstatic.com">


	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
	<title></title>

</head>

<body>

	<nav class="navbar navbar-expand-lg navbar-light px-0 mb-0" style="background:#F4F6F9;  padding-top:0px; padding-bottom:0px;margin-top:0px;Margin-bottom:0px">
		<div class="container-fluid">

			<div class="row mt-2">


				<div class="col-sm-4 pl-0">
					<div class="search-box ">
						<button class="btn-search"><i class="fas fa-search"></i></button>
						<input type="text" class="input-search" placeholder="Introduzca  manifiesto">

					</div>
				</div>


			</div>
			<div class="col-sm-1 ml-0">
				<button type="button" class="btn btn-primary btn-circle btn-xl mt-4 ml-0" onclick="agregar_partida(0,0)"><i class="fa fa-plus"></i></button>
			</div>
			<div class="col-sm-1">
				<button type="button" class="btn btn-primary btn-circle btn-xl mt-4" onclick="filtro_manifiesto()"><i class="fa fa-search"></i></button>
			</div>
			<div class="col-sm-1 ml-1">
			</div>
		</div>


	</nav>


	<!--- modal para filtrar -->
	<div class="modal fade " id="modal_filtro_manifiesto" role="dialog" data-backdrop="static">

		<div class="modal-dialog modal-dialog-centered mimodal " role="document">
			<div class="modal-content">

				<!-- Modal Header -->
				<div class="modal-header" style="background-color:white">
					<h4 class="modal-title" style="color:#F15a29">Filtrar</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<div class="row-fluid">
					<div class="messageup" id='messageup'></div>
				</div>

				<form enctype="multipart/form-data" class="filtro_m" id="filtro_m">
					<!-- Modal body -->
					<div class="modal-body">

						<div class="form-check">
							<input class="form-check-input" type="radio" name="rb-opc" id="rb-guia" value="c">
							<label class="form-check-label" for="exampleRadios3">
								Guia
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="rb-opc" id="rb-manifiesto" value="d">
							<label class="form-check-label" for="exampleRadios4">
								Manifiesto
							</label>
						</div>



						<div class="row">
							<div class="col">
								<input type="text" class="form-control" placeholder="" id="input_filtro" name="input_filtro">
							</div>

						</div>

					</div>

					<!-- Modal footer -->
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" onclick="manifiesto_listado()" ;>Aplicar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!--- fin modal filtrar -->




</body>

</html>