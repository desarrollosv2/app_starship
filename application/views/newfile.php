<?php

if ($this->session->flashdata('mensaje')!='')
{
    ?>
			     	<div class="alert alert-<?php echo $this->session->flashdata('css')?>">
			     		<?php echo $this->session->flashdata('mensaje')?>
			     	</div>
			     <?php     
			     }
			?>
