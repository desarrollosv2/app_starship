<style>
    .item-card {
        border: 5px solid #e2e2e2;
        border-radius: 10px;
        padding: 12px;
        background: white;
        transition: transform .2s ease-in-out;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.06);
        height: 350px;
        display: flex;
        flex-direction: column;
    }

    .item-card:hover {
        transform: scale(1.03);
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
    }

    .item-card img {
        width: 100%;
        height: 170px;
        object-fit: contain;
        background: #f8f8f8;
        border-radius: 6px;
    }
</style>

<div class="container-fluid" style="max-height: 70vh; overflow-y: auto; overflow-x: hidden; padding-right: 10px;">
    <div class="row">
        <?php foreach ($contenido as $item): 
            
               
            
            ?>
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="item-card">

                    <a href="<?= $item->url ?>" target="_blank">
                        <img src="<?= $item->url ?>">
                    </a>

                    <h6 class="mt-2 font-weight-bold"><?= $item->items_description ?></h6>
                    <p class="mb-1"><strong>Cantidad:</strong> <?= $item->items_quantities ?></p>
                    <p class="mb-1"><strong>Peso Unitario:</strong> <?= $item->weight_unit ?> kg</p>
                    <p class="mb-0"><strong>Valor Unitario:</strong> $<?= number_format($item->value_unit, 2) ?></p>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>