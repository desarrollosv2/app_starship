<style>
    .pdf-upload-form {
        background: #fff;
        border: 1px solid #e8e8e8;
        border-top: 4px solid #FE4F00;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .05);
    }

    .form-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }

    .custom-zip-input {
        height: 46px;
        border: 1px solid #d8d8d8;
        border-radius: 8px;
        padding: 0 15px;
        font-size: 15px;
        transition: all .2s ease;
        background: #fff;
    }

    .custom-zip-input::placeholder {
        color: #b5b5b5;
    }

    .custom-zip-input:focus {
        border-color: #FE4F00;
        box-shadow: 0 0 0 .18rem rgba(254, 79, 0, .18);
    }

    #drop_zone {
        position: relative;
        min-height: 150px;
        border: 2px dashed #d7d7d7;
        border-radius: 10px;
        background: #fcfcfc;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: .25s;
        overflow: hidden;
    }

    #drop_zone:hover {
        border-color: #FE4F00;
        background: #fff8f5;
    }

    #drop_zone i {
        font-size: 42px;
        color: #FE4F00;
        margin-bottom: 10px;
    }

    #drop_zone strong {
        display: block;
        font-size: 17px;
        color: #444;
    }

    #drop_zone small {
        color: #777;
        margin-top: 5px;
    }

    #lista_archivos {
        margin-top: 15px;
        border: 1px solid #ececec;
        border-radius: 8px;
        background: #fafafa;
        padding: 12px;
        max-height: 220px;
        overflow-y: auto;
    }

    #lista_archivos ul {
        margin: 0;
        padding-left: 0;
    }

    #lista_archivos li {
        list-style: none;
        padding: 8px 0;
        border-bottom: 1px solid #ececec;
        word-break: break-word;
    }

    #lista_archivos li:last-child {
        border-bottom: none;
    }

    .btn-subir-docs {
        background: #FE4F00;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        height: 46px;
        min-width: 220px;
        font-size: 15px;
        font-weight: 600;
        transition: .25s;
        box-shadow: 0 4px 12px rgba(254, 79, 0, .18);
    }

    .btn-subir-docs:hover {
        background: #ea4900;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(254, 79, 0, .25);
    }

    .btn-subir-docs:focus {
        outline: none;
    }

    @media (max-width:768px) {

        .pdf-upload-form {
            padding: 18px;
        }

        .btn-subir-docs {
            width: 100%;
            min-width: 100%;
            margin-top: 15px;
        }

        #drop_zone {
            min-height: 130px;
        }

    }
</style>


<div class="container-fluid mt-4">

    <form id="form_renombrar_ducas" method="post" action="<?= base_url('index.php/Gestion/zip_ducas_renombradas') ?>" enctype="multipart/form-data" class="pdf-upload-form">

        <div class="row">

            <div class="col-lg-9">

                <div class="row align-items-end">

                    <div class="col-md-8">

                        <label for="nombre_zip" class="form-label">
                            <i class="fa fa-file-archive-o text-secondary"></i>
                            Nombre del archivo ZIP resultante
                        </label>

                        <input type="text" class="form-control custom-zip-input" id="nombre_zip" name="nombre_zip" required>

                    </div>

                    <div class="col-md-4 text-md-end mt-3 mt-md-0">

                        <button type="button" class="btn-subir-docs" onclick="zip_ducas_renombradas()"> <i class="fa fa-download"></i> Procesar ZIP</button>

                    </div>

                </div>

                <div class="mt-4">

                    <label for="ducas" class="form-label"> <i class="fa fa-file-pdf-o text-danger"></i> Seleccionar o arrastrar DUCAs</label>

                    <div id="drop_zone">

                        <i class="fa fa-cloud-upload"></i>

                        <strong>

                            Arrastra aquí tus archivos PDF

                        </strong>

                        <small>

                            o haz clic para seleccionarlos

                        </small>

                        <input type="file" name="ducas[]" id="ducas" multiple style="opacity:0;position:absolute;width:100%;height:100%;top:0;left:0;cursor:pointer;">

                    </div>

                    <div id="lista_archivos"></div>

                </div>

            </div>

        </div>

    </form>

</div>




<script>
    $(document).ready(function() {
        const dropZone = $('#drop_zone');
        const input = $('#ducas');

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
        $('#ducas').on('change', function() {
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