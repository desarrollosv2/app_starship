<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="javascript:;"><strong>Gestion de Llamadas y Copias</strong></a>
      <?php if (isset($ico)): ?>
        <i class="<?php echo $ico; ?>" aria-hidden="true" style="font-size: 50px; text-align: center; color: #0E874F;"></i>
      <?php endif ?>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <?php if (isset($buscador)): ?>
        <button type="button" class="btn btn-sm navbar-btn btn-success" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-search"></i></button>
      <?php endif ?>
      <?php if (isset($excel)): ?>
        <a href="javascript:;" onclick="exportExcel();" class="btn btn-xs btn-default navbar-btn">
          <i class="glyphicon glyphicon-print"></i> Exportar A Excel 
        </a>
      <?php endif ?>
      <ul class="nav navbar-nav navbar-right" >
        <li class="dropdown">
          <li class="dropdown dropdown-big">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->session->userdata("nombre")?><b class="caret"></b></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="<?php echo base_url('catalogos/') ?>"><i class="fa fa-file"></i>Crear Empresa</a></li>
                <li><a href="<?php echo base_url('index.php/solicitud/solicitudes/') ?>"><i class="fa fa-list"></i>  Oportunidades</a></li>
                <li>
                  <a href="<?php echo base_url('index.php/consulta/')?>"><i class="glyphicon glyphicon-list-alt"></i> Auto Consulta</a>
                </li>
                <?php if (isset($seguimiento)): ?>
                  <li><a href="<?php echo base_url('index.php/solicitud/solicitudes/1') ?>"><i class="fa fa-refresh"></i> Seguimiento</a></li>  
                <?php endif ?>
                <li><a href="javascript:;" id="salir"><i class="fa fa-file"></i>Salir</a></li>
              </ul>
            </li>      
          </li>
        </li>        
      </ul>

      <?php if (isset($submenu)): ?>
        <?php $this->load->view($submenu)?>
      <?php endif ?>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
