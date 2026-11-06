<?php // Los 2 espacios de fotografia del pallet activo (se usan en el panel y en Modal_fotos; los pinta pintar_fotos()) 
?>
<div class="row g-3">
    <?php foreach (array(1, 2) as $posicion): ?>
        <div class="col-6">
            <div class="foto-slot" data-posicion="<?php echo $posicion; ?>">
                <div class="foto-slot-img">
                    <img src="" alt="Foto <?php echo $posicion; ?>" class="d-none">
                    <div class="foto-slot-vacio">
                        <i class="fa-solid fa-camera"></i>
                        <span>Foto <?php echo $posicion; ?> pendiente</span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-2" onclick="tomar_foto(<?php echo $posicion; ?>)">
                    <i class="fa-solid fa-camera"></i> <span class="foto-slot-texto">Tomar foto <?php echo $posicion; ?></span>
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>