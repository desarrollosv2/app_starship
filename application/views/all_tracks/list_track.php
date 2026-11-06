<?php

if ($list_warehouse) {

    echo '<div class="accordion-container">
';
    foreach ($list_warehouse as $key => $value) { ?>

        <div class="card-aco">
            <div class="card-aco-header" onclick="getStatusByWarehouse('<?= $value->warehouse ?>',<?= $key ?>, this);">
                <h3 style="display:flex; justify-content: space-around; width: 100%;">
                    <div class="p-2"><b>Guia:</b> <span style="color:#FE4F00; font-weight: bold;"><?= $value->warehouse ?></span> </div>
                    <div class="p-2"><b>Ultimo estado:</b> <span style="color:#FE4F00; font-weight: bold;"><?= $value->name_status ?></span><b> - </b> <span style="color:#FE4F00; font-weight: bold;"><?= $value->status_date ?></span></div>
                </h3>
                <span class="icon" style="font-weight: bold;">+</span>
            </div>
            <div class="card-aco-body">
                <h5 class="p-3" style="text-align: center;">Estados de la guia</h5>

                <div id="status_<?= $key ?>" class="px-5"></div>
            </div>
        </div>

    <?php

    }


    echo '</div>';
} else { ?>

    <h2 class="my-4 text-danger" style="margin: 0 auto;">Sin resultados para tu busqueda</h2>

<?php

}

?>