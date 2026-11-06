<nav class="navbar navbar-default navbar-fixed-top w-100  borde-orange" style="padding-top:0px; padding-bottom:0px;margin-top:0px;Margin-bottom:0px" >
    <div class="container-fluid mt-0 mb-0 pt-0 pb-0 ">
        <div class="navbar-header">
          <!--  <a class="navbar-brand" href="#" style="color:black;"><h4 class="m-3">	<?php if (isset($navtext)) { echo $navtext;} ?></h4></a>-->
          <h4 class="ml-0">	<?php if (isset($navtext)) { echo $navtext;} ?></h4>
        </div>
        <?php if (isset($form)): ?>
        <?php $this->load->view($form); ?>
        <?php endif ?>
    </div>
</nav>