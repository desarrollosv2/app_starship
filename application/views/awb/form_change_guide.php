<form id="frmGuiaToSet">

    <input type="hidden" name="txtIdPreclasificacionChangeGuide" id="txtIdPreclasificacionChangeGuide">

    <div class="logis-group">
        <label class="logis-label">Nueva Guía (Miami)</label>
        <input type="text" name="guiaEscrita" class="logis-input-active" placeholder="Ej: 1Z999AA10123456784" required>
    </div>

    <div class="logis-checkbox-group">
        <label class="logis-checkbox-container">
            <input type="checkbox" name="chkOnlyCurrent" id="chkOnlyCurrent">
            <span class="logis-checkmark"></span>
            Modificar únicamente guía actual
        </label>
    </div>

    <div class="logis-group">
        <label class="logis-label">Guia Actual</label>
        <input type="text" name="guiaToChange" class="logis-input-readonly" value="<?= $manifiesto ?>" readonly>
    </div>



    <div class="logis-divider">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block; margin: 0 auto;">
            <path d="M7 13l5 5 5-5M7 6l5 5 5-5" />
        </svg>
    </div>

    <div class="logis-group">
        <label class="logis-label">Guía Antigua (China)</label>
        <input type="text" class="logis-input-readonly" value="<?= ($manifiesto_tempo != '') ? $manifiesto_tempo : 'Sin asignar' ?>" readonly>
    </div>


    <button type="button" id="btnGuardarGuia" onclick="setGuia();" disabled style="opacity: 0.5; cursor: not-allowed;" class="logis-btn-save">Actualizar Registro</button>
</form>

<script>
    // Escuchar teclas en todo el documento
    $(document).on('keydown', function(e) {

        // Combinación: CTRL + SHIFT + U
        if (e.ctrlKey && e.shiftKey && e.which === 85) {
            e.preventDefault(); // Evitar acciones del navegador

            var btn = $("#btnGuardarGuia");

            // Si estaba deshabilitado, lo desbloqueamos
            if (btn.is(':disabled')) {
                btn.prop("disabled", false)
                    .text("Confirmar Actualización")
                    .css({
                        "opacity": "1",
                        "cursor": "pointer",
                        "background-color": "#FE4F00" // Tu color base
                    });

                // Feedback visual rápido
                btn.fadeOut(100).fadeIn(100);
                console.log("Acceso de actualización desbloqueado");
            }
        }
    });
</script>