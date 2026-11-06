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

</head>

<body class="hold-transition sidebar-mini">

	<?php
	$_SESSION['buscar'] = "";
	$this->load->view('menu');

	?>

	<div class="wrapper" style="background:#343a40">

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">

			<!-- Main content -->
			<!-- <div class="content"> -->
			<div class="container-fluid " style="margin-top: 50px;">
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
				<strong>Global Cargo</strong>
				
			</div>
			<!-- Default to the left -->
			<strong>Copyright &copy; <?php echo date("Y"); ?></strong> All rights reserved.
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
	<script src="<?php echo base_url(); ?>public/jquery-ui/jquery-ui.js"></script>



	<script type="text/javascript" src="<?php echo base_url(); ?>public/chosen/chosen.jquery.min.js"></script>

	<script type="text/javascript" src="<?php echo base_url(); ?>public/file-upload/js/fileinput.min.js"></script>
	<script src="<?php echo base_url(); ?>public/grafico/Chart.min.js"></script>

	<script src="<?php echo base_url(); ?>public/js/funciones.js"></script>
	<script src="<?php echo base_url(); ?>public/js/funcion.js"></script>
	<script src="<?php echo base_url(); ?>public/js/wh.js"></script>
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
	<script src="<?php echo base_url(); ?>public/js/consulta_track.js"></script>
	<script src="<?php echo base_url(); ?>public/js/lista_aduana.js"></script>
    <script src="<?php echo base_url(); ?>public/js/tracking_replace.js"></script>
	<script src="<?php echo base_url(); ?>public/js/tasa.js"></script>
	<script src="<?php echo base_url(); ?>public/js/ia.js"></script>
	<script src="<?php echo base_url(); ?>public/js/imile.js"></script>
<!--	<script src="<?php echo base_url(); ?>public/js/cargar_declaracion.js"></script> -->
		
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

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