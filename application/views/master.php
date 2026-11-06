<!doctype html>
<html>
	<head>
    <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta http-equiv="Cache-control" content="no-cache">
		<title>Sistema de Gestion de Llamadas y Copias</title>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/font-awesome/css/font-awesome.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/jquery-ui/jquery-ui.css')?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/bootstrap/css/bootstrap.min.css')?>">
		
    <script type="text/javascript" src="<?php echo base_url('public/jquery-ui/jquery-ui.js')?>"></script>
		<script type="text/javascript" src="<?php echo base_url('public/js/jquery-2.2.3.min.js')?>"></script>
		<script type="text/javascript" src="<?php echo base_url('public/bootstrap/js/bootstrap.min.js')?>"></script>
		<script type="text/javascript" src="<?php echo base_url('public/notify/notify.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('public/js/funciones.js')?>"></script>
    <script type="text/javascript" src="<?php echo base_url('public/js/funcion.js')?>"></script>

    
	</head>
	<body>
  <header style="margin-bottom: 5%;">
      <?php $this->load->view('encabezado')?>
  </header>
  <div class="container-fluid">
    <div class="row" id="principal">
        <div class="container-fluid" id="subcontent">
            <?php ((isset($vista)) ? $this->load->view($vista) : 'Hola :)'); ?> 
        </div>
    </div>
</div>
<div class="container">
  <!-- Modal vbuscador-->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content" id="body_modal">
        <!--<?php if ($vbuscador): ?>-->
          <!-- <?php $this->load->view($vbuscador)?>-->
        <!-- <?php endif ?>-->
      </div>
      
    </div>
  </div>
</div>
</body>
<!-- <script type="text/javascript">
  $('.chosen').chosen({width: "100%"});
</script> -->
</html>

