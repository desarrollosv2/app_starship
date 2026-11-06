<style>
    .upload-form {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #FE4F00;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .drop-zone {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        position: relative;
        min-height: 100px;
    }

    .drop-zone:hover {
        background: #f8f9fa;
    }

    .drop-zone input[type="file"] {
        opacity: 0;
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .file-list {
        max-height: 200px;
        overflow-y: auto;
    }

    .btn-subir-docs {
        background-color: #FE4F00;
        color: white;
        font-size: 16px;
        border: none;
        padding: 10px;
        border-radius: 5px;
    }

    .btn-subir-docs:hover {
        background-color: #f14731ff;
        color: white;
    }

    .badge-count {
        background: #FE4F00;
        color: white;
        font-size: 12px;
    }
</style>

<div class="container-fluid mt-4">

    <form id="form_oea" method="post" action="<?= base_url('index.php/Gestion/procesar_impuestos_gt') ?>" enctype="multipart/form-data" class="upload-form">

        <div class="row">
            <!-- XML -->
            <div class="col-md-6 mb-4">

                <label class="form-label">
                    <i class="fa fa-file-code-o"></i>
                    Archivos XML
                </label>

                <div class="drop-zone" id="drop_zone_xml">
                    <p class="text-muted mb-0">
                        Arrastra los XML aquí o haz clic para seleccionarlos
                    </p>

                    <input type="file" name="xml[]" id="xml" multiple accept=".xml">
                </div>

                <div class="mt-2">
                    <span class="badge badge-count">
                        <span id="total_xml">0</span> archivos
                    </span>
                </div>

                <div id="lista_xml" class="mt-2 text-muted small file-list"></div>

            </div>

            <!-- PDF -->
            <div class="col-md-6 mb-4">

                <label class="form-label">
                    <i class="fa fa-file-pdf-o text-danger"></i>
                    Archivos PDF
                </label>

                <div class="drop-zone" id="drop_zone_pdf">
                    <p class="text-muted mb-0">
                        Arrastra los PDF aquí o haz clic para seleccionarlos
                    </p>

                    <input type="file" name="pdf[]" id="pdf" multiple accept=".pdf">
                </div>

                <div class="mt-2">
                    <span class="badge badge-count">
                        <span id="total_pdf">0</span> archivos
                    </span>
                </div>

                <div id="lista_pdf" class="mt-2 text-muted small file-list"></div>

            </div>

        </div>

        <div class="row mt-3">

            <div class="col text-end">
                <button type="button" class="btn-subir-docs" onclick="procesar_reporte_impuestos_gt()">
                    <i class="fa fa-upload me-2"></i> Procesar documentos
                </button>
            </div>

        </div>

    </form>


    <form id="form_generate_oea" target="__blank" method="post" action="<?= base_url('index.php/WarehouseController/generar_oea') ?>" enctype="multipart/form-data" class="upload-form">

        <div class="row">
            <!-- XML -->
            <div class="col-md-6 mb-4">

                <label class="form-label">
                    <i class="fa fa-file-code-o"></i>
                    Archivos XML
                </label>

                <div class="drop-zone" id="drop_zone_xml">
                    <p class="text-muted mb-0">
                        Arrastra los XML aquí o haz clic para seleccionarlos
                    </p>

                    <input type="file" name="xml[]" id="xml" multiple accept=".xml">
                </div>

                <div class="mt-2">
                    <span class="badge badge-count">
                        <span id="total_xml">0</span> archivos
                    </span>
                </div>

                <div id="lista_xml" class="mt-2 text-muted small file-list"></div>

            </div>

            

        </div>

        <div class="row mt-3">

            <div class="col text-end">
                <button type="submit" class="btn-subir-docs">
                    <i class="fa fa-upload me-2"></i> Generar OEA
                </button>
            </div>

        </div>

    </form>

</div>

<script>
    $(document).ready(function() {

        configurarDropZone(
            '#drop_zone_xml',
            '#xml',
            '#lista_xml',
            '#total_xml',
            'fa-file-code-o'
        );

        configurarDropZone(
            '#drop_zone_pdf',
            '#pdf',
            '#lista_pdf',
            '#total_pdf',
            'fa-file-pdf-o text-danger'
        );

    });

    function configurarDropZone(
        dropZoneSelector,
        inputSelector,
        listaSelector,
        totalSelector,
        icono
    ) {
        const dropZone = $(dropZoneSelector);
        const input = $(inputSelector);

        function actualizarLista(files) {
            mostrarArchivos(files, listaSelector, totalSelector, icono);
        }

        function setInputFiles(files) {
            const dataTransfer = new DataTransfer();
            for (let i = 0; i < files.length; i++) {
                dataTransfer.items.add(files[i]);
            }
            input[0].files = dataTransfer.files;
        }

        // CLICK EN EL DIV
        dropZone.on('click', function(e) {
            if (e.target !== input[0]) {
                input.trigger('click');
            }
        });

        // DRAG OVER
        dropZone.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();

            $(this).css({
                background: '#fff4ef',
                borderColor: '#FE4F00'
            });
        });

        // DRAG LEAVE
        dropZone.on('dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();

            $(this).css({
                background: '',
                borderColor: '#ccc'
            });
        });

        // DROP
        dropZone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const files = e.originalEvent.dataTransfer.files;

            if (files.length > 0) {
                setInputFiles(files);
                actualizarLista(files);
            }
        });

        input.on('change', function() {
            actualizarLista(this.files);
        });

        actualizarLista(input[0].files);
    }

    function mostrarArchivos(
        files,
        listaSelector,
        totalSelector,
        icono
    ) {
        let html = '';

        if (files.length > 0) {
            html += '<ul class="list-unstyled mb-0">';

            for (let i = 0; i < files.length; i++) {
                html += `
                <li>
                    <i class="fa ${icono}"></i>
                    ${files[i].name}
                </li>
            `;
            }

            html += '</ul>';
        } else {
            html = '<span class="text-muted">No hay archivos seleccionados</span>';
        }

        $(listaSelector).html(html);
        $(totalSelector).text(files.length);
    }
</script>