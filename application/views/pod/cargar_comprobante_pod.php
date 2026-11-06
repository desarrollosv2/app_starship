<style>

.pdf-upload-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #FE4F00;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.pdf-upload-form input[type="file"] {
    border: 1px solid #ced4da;
    border-radius: 5px;
    padding: 6px 10px;
    width: 100%;
}


.pdf-list-container h6 {
    font-weight: 600;
    margin-bottom: 10px;
}

.pdf-list {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}


.btn-subir-docs{
    background-color: #FE4F00; 
    color: white; 
    font-size: 16px;
    border: none;
    padding: 10px;
    border-radius: 5px; 
    text-align: center;
}
.btn-subir-docs:hover{
    background-color: #f14731ff; 
    color: white; 
}




</style>


<div class="container-fluid">


    <form id="form_subir_comprobante_pod" enctype="multipart/form-data" class="pdf-upload-form">
        <input type="hidden" name="pod_comprobante" id="pod_comprobante">

        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-9">
                <label for="comprobante_pod" class="form-label"><i class="fa fa-file-o"></i> Seleccionar o arrastrar documentos</label>
                <div id="drop_zone" style="border: 2px dashed #ccc; border-radius: 8px; padding: 20px; text-align:center; cursor:pointer;">
                    <p class="text-muted mb-0">Arrastra tus documentos aquí o haz clic para seleccionarlos</p>
                    <input type="file" name="comprobante_pod[]" id="comprobante_pod" multiple style="opacity:0; position:absolute; width:100%; height:100%; top:0; left:0; cursor:pointer;">
                </div>
                <div id="lista_archivos" class="mt-2 text-muted small"></div>
            </div>

            <div class="col-12 col-md-3 text-md-end text-center">
                <button type="button" class="btn-subir-docs" onclick="guardar_comprobantes_pod()"><i class="fa fa-upload me-2"></i> Subir</button>
            </div>
        </div>
    </form>

    <div class="pdf-list-container">
        <h6>Documentos cargados</h6>
        <div id="lista_pod_comprobantes" class="pdf-list"></div>
    </div>

</div>
    



<script>
$(document).ready(function() {
    const dropZone = $('#drop_zone');
    const input = $('#comprobante_pod');

    dropZone.on('dragover', function(e) {
        e.preventDefault();
        dropZone.css('background', '#f0f8ff');
    });

    dropZone.on('dragleave', function(e) {
        e.preventDefault();
        dropZone.css('background', '#fff');
    });

    dropZone.on('drop', function(e) {
        e.preventDefault();
        dropZone.css('background', '#fff');

        const files = e.originalEvent.dataTransfer.files;
        input[0].files = files;
    });
    $('#comprobante_pod').on('change', function() {
    const files = this.files;
    let html = '';

    if (files.length > 0) {
        html += '<ul class="list-unstyled">';
        for (let i = 0; i < files.length; i++) {
            html += `<li><i class="fa fa-file-o text-secondary"></i> ${files[i].name}</li>`;
        }
        html += '</ul>';
    } else {
        html = '<p class="text-muted">No hay archivos seleccionados</p>';
    }

    $('#lista_archivos').html(html);
});


});
</script>
