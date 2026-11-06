<?php
    if(isset($detalle))
    {
        $reproceso = $detalle->observacion;
    }else{
        $reproceso = "";
    }
?>

<div class="container-fluid mt-2">
    <div class="row g-3">
        <div class="col-md-12">
            <textarea name="reproceso" id="reproceso" class="form-control" readonly><?= htmlspecialchars($reproceso) ?></textarea>
        </div>
    </div>
</div>