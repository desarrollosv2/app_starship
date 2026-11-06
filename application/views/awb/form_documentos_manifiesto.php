<style>
    .pdf-upload-form {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #FE4F00;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
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

    .pdf-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid #FE4F00 !important;
    }

    .btn-subir-docs {
        background-color: #FE4F00;
        color: white;
        font-size: 16px;
        border: none;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
    }

    .btn-subir-docs:hover {
        background-color: #f14731ff;
        color: white;
    }

    .pdf-card .btn-group-docs {
        display: flex;
        justify-content: center;
        gap: 8px;
        width: 100%;
    }
</style>


<div class="container-fluid">


    <div class="row g-3 mb-3 align-items-center">


        <div class="col-8">

            <select class="form-control input-sm" name="tipoZip" id="tipoZip">
                <option value="">Zip completo</option>
                <option value="1">Ducas</option>
                <option value="2">Mandamientos</option>
                <option value="3">Comprobantes</option>
                <option value="4">Adimex</option>
            </select>

        </div>
        <div class="col-4">

            <button type="button" class="btn btn-warning" onclick="downloadZip()">Descargar Zip</button>

        </div>

    </div>

    <form id="form_subir_documentos_manifiesto" enctype="multipart/form-data" class="pdf-upload-form">
        <input type="hidden" name="manifiesto_id_documentos" id="manifiesto_id_documentos">

        <div class="row g-3 mb-3 align-items-center">
            <div class="col-12 col-md-9">

                <label>Tipo de archivo a cargar</label>
                <div>
                    <select class="form-control input-sm" id="selectTipoArchivo" name="selectTipoArchivo">
                        <option value="referencia">Referencia</option>
                        <option value="manifiesto">Manifiesto</option>
                    </select>
                </div>


            </div>
        </div>

        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-9">
                <label for="Documentos" class="form-label"><i class="fa fa-file-o"></i> Seleccionar o arrastrar documentos</label>
                <div id="drop_zone" style="border: 2px dashed #ccc; border-radius: 8px; padding: 20px; text-align:center; cursor:pointer;">
                    <p class="text-muted mb-0">Arrastra tus documentos aquí o haz clic para seleccionarlos</p>
                    <input type="file" name="documentos_manifiesto[]" id="documentos_manifiesto" multiple style="opacity:0; position:absolute; width:100%; height:100%; top:0; left:0; cursor:pointer;">
                </div>
                <div id="lista_archivos" class="mt-2 text-muted small"></div>
            </div>

            <div class="col-12 col-md-3 text-md-end text-center">
                <button type="button" class="btn-subir-docs" onclick="subir_doc_manifiesto()"><i class="fa fa-upload me-2"></i> Subir</button>
            </div>
        </div>
    </form>

    <div>

        <div id="resultado-container" class="custom-table-container"></div>

    </div>


    <div class="pdf-list-container">
        <h6>Documentos cargados</h6>
        <div id="lista_pdfs" class="pdf-list"></div>
    </div>

</div>




<script>
    $(document).ready(function() {
        const dropZone = $('#drop_zone');
        const input = $('#documentos_manifiesto');

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
        $('#documentos_manifiesto').on('change', function() {
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