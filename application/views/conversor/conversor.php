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

    .switch-ma {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }

    .switch-ma input {
        display: none;
    }

    .switch-slider {
        position: relative;
        width: 48px;
        height: 24px;
        background: #ccc;
        border-radius: 30px;
        transition: all .3s ease;
        cursor: pointer;
    }

    .switch-slider:before {
        content: "";
        position: absolute;
        width: 18px;
        height: 18px;
        background: white;
        border-radius: 50%;
        top: 3px;
        left: 4px;
        transition: all .3s ease;
    }

    .switch-ma input:checked+.switch-slider {
        background: #FE4F00;
    }

    .switch-ma input:checked+.switch-slider:before {
        transform: translateX(22px);
    }

    .switch-label {
        font-size: 15px;
    }
</style>


<div class="container-fluid mt-4">


    <form id="form_subir_duca" method="post" action="<?= base_url('index.php/Gestion/convertir_duca') ?>" enctype="multipart/form-data" class="pdf-upload-form">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-9">
                <label for="duca" class="form-label"><i class="fa fa-file-o"></i> Seleccionar o arrastrar Duca</label>
                <div id="drop_zone" style="border: 2px dashed #ccc; border-radius: 8px; padding: 20px; text-align:center; cursor:pointer;">
                    <p class="text-muted mb-0">Arrastra la duca aquí o haz clic para seleccionarla</p>
                    <input type="file" class="ignore-global" name="duca[]" id="duca" multiple style="opacity:0; position:absolute; width:100%; height:100%; top:0; left:0; cursor:pointer;">
                </div>
                <div id="lista_archivos" class="mt-2 text-muted small"></div>
            </div>

            <div class="col-12 col-md-3 text-md-end text-center">
                <div class="switch-ma mb-2">

                    <label class="switch-ma">
                        <input type="checkbox" name="ma" id="ma" value="1">
                        <span class="switch-slider"></span>
                    </label>

                    <span class="switch-label">
                        Procesar como <strong>MA</strong>
                    </span>

                </div>

                <button type="button" class="btn-subir-docs" onclick="convertir_duca()"><i class="fa fa-upload me-2"></i> Convertir</button>
            </div>
        </div>
    </form>


</div>




<script>
    $(document).ready(function() {
        const dropZone = $('#drop_zone');
        const input = $('#duca');

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
        $('#duca').on('change', function() {
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