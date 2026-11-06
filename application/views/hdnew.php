<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
<nav class="navbar navbar-default navbar-fixed-top w-100 borde-orange" >
    <div class="container">
        <div class="navbar-header">
          <!--  <a class="navbar-brand" href="#" style="color:black;"><h4 class="m-3">	<?php if (isset($navtext)) { echo $navtext;} ?></h4></a>-->
          <h4 class="m-3">	<?php if (isset($navtext)) { echo $navtext;} ?></h4>
        </div>
        <?php if (isset($form)): ?>
        <?php $this->load->view($form); ?>
        <?php endif ?>
    </div>
</nav>