<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>StarShip</title>



	<!-- Font Awesome Icons -->

	<link rel="stylesheet" href="<?php echo base_url(); ?>public/font-awesome/css/font-awesome.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>public/dist/css/adminlte.min.css">

	<link rel="stylesheet" href="<?php echo base_url(); ?>public/bootstrap/css/bootstrap.css">

	<!-- Google Font: Source Sans Pro -->
	<link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/chosen/chosen.min.css') ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/estilos.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/upload.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/wmodals.css') ?>">



	<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/tbl/fonts/icomoon/style.css') ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/tbl/css/owl.carousel.min.css') ?>">

	<!--<link rel="stylesheet" href="css/owl.carousel.min.css">-->

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/file-upload/css/fileinput.min.css') ?>">

	<!-- table -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/datatables/datatables.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/estilo22.css') ?>">


	<style>
		/* Fondo oscuro translúcido con desenfoque de fondo */
		.delivery-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(11, 15, 25, 0.85);
			backdrop-filter: blur(6px);
			z-index: 9999;
			display: flex;
			justify-content: center;
			align-items: center;
			display: none;
			/* Cambiar a 'flex' con JS para activar */
		}

		/* Tarjeta del Loader */
		.loader-card {
			background: #111827;
			border: 1px solid rgba(255, 255, 255, 0.08);
			padding: 35px 40px;
			border-radius: 16px;
			box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
			text-align: center;
			width: 320px;
		}

		/* Escenario de la animación */
		.animation-container {
			position: relative;
			height: 100px;
			margin-bottom: 20px;
			overflow: hidden;
		}

		/* El Avión (Inclinado en ángulo de despegue) */
		.delivery-plane {
			position: absolute;
			bottom: 25px;
			left: 35px;
			font-size: 36px;
			color: #FE4F00;
			/* Tu color de acento naranja */
			transform: rotate(-15deg);
			/* Ángulo de ascenso */
			animation: planeFly 1.5s infinite ease-in-out;
			z-index: 2;
		}

		/* Nube en el extremo superior derecho */
		.sky-cloud {
			position: absolute;
			top: 10px;
			right: 30px;
			font-size: 32px;
			color: rgba(255, 255, 255, 0.15);
			animation: cloudDrift 3s infinite ease-in-out;
			z-index: 1;
		}

		/* Paquetes que vuelan/entran al avión */
		.cargo-box {
			position: absolute;
			font-size: 14px;
			color: #10B981;
			/* Verde esmeralda para el contraste */
			opacity: 0;
		}

		.box-1 {
			bottom: 5px;
			left: 10px;
			animation: loadCargo 1.2s infinite linear;
		}

		.box-2 {
			bottom: 15px;
			left: -5px;
			animation: loadCargo 1.2s infinite linear 0.4s;
			/* Desfase */
		}

		/* Estelas de viento diagonales traseras */
		.wind-streak {
			position: absolute;
			height: 2px;
			background: linear-gradient(90deg, transparent, rgba(254, 79, 0, 0.4));
			transform: rotate(-15deg);
			opacity: 0.5;
		}

		.streak-1 {
			bottom: 20px;
			left: 5px;
			width: 40px;
			animation: windPass 0.8s infinite linear;
		}

		.streak-2 {
			bottom: 45px;
			left: 15px;
			width: 25px;
			animation: windPass 0.8s infinite linear 0.2s;
		}

		/* Textos informativos */
		.loader-text {
			color: #F3F4F6;
			font-size: 16px;
			font-weight: 600;
			margin: 10px 0 4px 0;
			font-family: 'Inter', sans-serif;
		}

		.loader-subtext {
			color: #9CA3AF;
			font-size: 13px;
			font-weight: 400;
		}

		/* --- ANIMACIONES CSS --- */

		/* Turbulencia y cabeceo del avión en vuelo */
		@keyframes planeFly {

			0%,
			100% {
				transform: rotate(-15deg) translate(0, 0);
			}

			50% {
				transform: rotate(-13deg) translate(2px, -6px);
				/* Sube un poco */
			}
		}

		/* Movimiento sutil de la nube lejana */
		@keyframes cloudDrift {

			0%,
			100% {
				transform: translateX(0) scale(1);
				color: rgba(255, 255, 255, 0.15);
			}

			50% {
				transform: translateX(-5px) scale(1.05);
				color: rgba(255, 255, 255, 0.25);
			}
		}

		/* Efecto de paquetes succionados/cargados hacia el avión */
		@keyframes loadCargo {
			0% {
				transform: translate(0, 0) scale(0.6);
				opacity: 0;
			}

			30% {
				opacity: 0.8;
			}

			80% {
				/* Se dirige diagonalmente hacia la panza del avión */
				transform: translate(45px, -25px) scale(1);
				opacity: 0.9;
			}

			100% {
				transform: translate(60px, -35px) scale(0.4);
				opacity: 0;
			}
		}

		/* Velocidad del viento en dirección opuesta al vuelo */
		@keyframes windPass {
			0% {
				transform: rotate(-15deg) translateX(0);
				width: 0;
				opacity: 0;
			}

			50% {
				width: 30px;
				opacity: 0.5;
			}

			100% {
				transform: rotate(-15deg) translateX(-40px);
				width: 0;
				opacity: 0;
			}
		}
	</style>
</head>

<body class="hold-transition sidebar-mini ml-0 pl-0 mr-0 pr-0">

	<?php
	$_SESSION['buscar'] = "";
	$this->load->view('menu');

	?>

	<div class="wrapper mt-3" style="background:#1A1F2C">

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper mt-4">

			<!-- Main content -->
			<!-- <div class="content"> -->
			<div class="container-fluid">
				<div class="row" id="principal">
					<div class="col-lg-12">
						<?php
						if (isset($vista)) {
							$this->load->view($vista);
						}
						?>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->
			</div><!-- /.container-fluid -->
			<!--</div>-->
			<!-- /.content -->
			<!-- /. preloading -->


			<div class="content-fluid">

				<div id="loader" style="display:none" ;>
					<div id="preloader-inner">
					</div>
				</div>





				<div id="delivery-loader" class="delivery-overlay">
					<div class="loader-card">
						<div class="animation-container">
							<div class="sky-cloud">
								<i class="fas fa-cloud"></i>
							</div>

							<div class="delivery-plane">
								<i class="fas fa-plane"></i>
							</div>

							<div class="cargo-box box-1"><i class="fas fa-box"></i></div>
							<div class="cargo-box box-2"><i class="fas fa-box"></i></div>

							<div class="wind-streak streak-1"></div>
							<div class="wind-streak streak-2"></div>
						</div>
						<p class="loader-text">Cargando Informacion</p>
						<span class="loader-subtext">Estamos ejecutando un proceso</span>
					</div>
				</div>


				<!--
                <div id='loader' class="carga_overlay">
                  <img class="col" src='<?php echo base_url(); ?>/public/img/loading.gif'>
                </div>
                -->
			</div>
			<!--/. end preloading -->
		</div>
		<!-- /.content-wrapper -->

		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-light">
			<!-- Control sidebar content goes here -->
			<div class="p-3">
				<h5>Title</h5>
				<p>Sidebar content</p>
				<div>

				</div>
			</div>
		</aside>
		<!-- /.control-sidebar -->

		<!-- Main Footer -->
		<footer class="main-footer">
			<!-- To the right -->
			<div class="float-right d-none d-sm-inline">
				Global Cargo
			</div>
			<!-- Default to the left -->
			<strong>Copyright &copy; 2019 <a href="#"></a>.</strong> All rights reserved.
		</footer>

	</div>
	<!-- ./wrapper -->



	<!-- REQUIRED SCRIPTS -->

	<!-- jQuery -->
	<!-- <script type="text/javascript" src="<?php echo base_url('public/js/jquery-2.2.3.min.js') ?>"></script> -->
	<script src="<?php echo base_url(); ?>public/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="<?php echo base_url(); ?>public/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- AdminLTE App -->
	<script src="<?php echo base_url(); ?>public/dist/js/adminlte.min.js"></script>
	<!--<script src="<?php echo base_url(); ?>public/jquery-ui/jquery-ui.js"></script>-->



	<script type="text/javascript" src="<?php echo base_url(); ?>public/chosen/chosen.jquery.min.js"></script>

	<script type="text/javascript" src="<?php echo base_url(); ?>public/file-upload/js/fileinput.min.js"></script>
	<script src="<?php echo base_url(); ?>public/grafico/Chart.min.js"></script>

	<script src="<?php echo base_url(); ?>public/js/funciones.js?v=<?php echo rand(); ?>"></script>
	<script src="<?php echo base_url(); ?>public/js/funcion.js?v=<?php echo rand(); ?>"></script>
	<script src="<?php echo base_url(); ?>public/js/wh.js?v=<?php echo rand(); ?>"></script>
	<script src="<?php echo base_url(); ?>public/js/preclasificacion.js"></script>
	<script src="<?php echo base_url(); ?>public/js/clientes.js"></script>
	<script src="<?php echo base_url(); ?>public/js/customer.js"></script>
	<script src="<?php echo base_url(); ?>public/js/inventario.js"></script>
	<script src="<?php echo base_url(); ?>public/js/traspaso.js"></script>
	<script src="<?php echo base_url(); ?>public/js/upload.js"></script>
	<script src="<?php echo base_url(); ?>public/js/arancel.js"></script>
	<script src="<?php echo base_url(); ?>public/js/listado_lmd.js"></script>
	<script src="<?php echo base_url(); ?>public/js/tracking_dte.js"></script>
	<script src="<?php echo base_url(); ?>public/js/comparativo_guias.js"></script>
	<script src="<?php echo base_url(); ?>public/js/consulta_track.js?v=<?= rand() ?>"></script>
	<script src="<?php echo base_url(); ?>public/js/lista_aduana.js"></script>
	<script src="<?php echo base_url(); ?>public/js/tracking_replace.js"></script>
	<script src="<?php echo base_url(); ?>public/js/gestion.js"></script>
	<script src="<?php echo base_url(); ?>public/js/all_tracks.js"></script>
	<script src="<?php echo base_url(); ?>public/js/imile.js"></script>
	<script src="<?php echo base_url(); ?>public/js/pod.js?v=<?= rand() ?> ?> "></script>
	<script src="<?php echo base_url(); ?>public/js/usuarios.js?v=<?php echo rand(); ?>"></script>

	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<!--	<script src="<?php echo base_url(); ?>public/js/cargar_declaracion.js"></script> -->

	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->



	<!-- bloque para alertify -->
	<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
	<!-- CSS -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
	<!-- Default theme -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
	<!-- Semantic UI theme -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css" />
	<!-- Bootstrap theme -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />
	<!-- fin bloque para alertify -->
	<script type="text/javascript" src="<?php echo base_url(); ?>public/datatables/datatables.min.js"></script>
	<!--script src="<?php echo base_url(); ?>public/js/table.js"></script-->
	<!-- table data-->



	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/css/select2-bootstrap4.min.css" />




</body>

</html>