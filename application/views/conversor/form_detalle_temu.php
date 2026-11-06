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

.switch-ma input:checked + .switch-slider {
    background: #FE4F00;
}

.switch-ma input:checked + .switch-slider:before {
    transform: translateX(22px);
}

.switch-label {
    font-size: 15px;
}



</style>


<div class="container-fluid mt-4">


    <form id="form_subir_detalle_impuestos_temu" method="post" enctype="multipart/form-data" class="pdf-upload-form">

		<div class="row mb-4">

			<div class="col-md-6">

				<label class="form-label">Empresa</label>

				<select name="empresas" id="empresas" class="form-control">
					<option value="" disabled selected hidden>Seleccione la empresa</option>
						<?php foreach ($empresa as $row): ?>
							<option value="<?php echo $row->id; ?>" >
							<?php echo $row->nombre; ?>
							</option>
						<?php endforeach ?>
					</select>

			</div>

		</div>

		<div class="row g-3 align-items-center">

			<div class="col-12 col-md-9">

				<label for="detalle_temu" class="form-label">
					Seleccione o arrastre los archivos
				</label>

				<div id="drop_zone" style="border:2px dashed #ccc; border-radius:8px; padding:20px; text-align:center; cursor:pointer; position:relative;">

					<p class="text-muted mb-0">
						Haz clic para seleccionar los archivos o arrástralos aquí
					</p>

					<input type="file" name="detalle_temu[]" id="detalle_temu" multiple accept=".xls,.xlsx" style="opacity:0; position:absolute; width:100%; height:100%; top:0; left:0; cursor:pointer;">

				</div>

				<div id="lista_archivos" class="mt-2 text-muted small"></div>

			</div>

			<div class="col-12 col-md-3 text-center">

				<button type="button" class="btn-subir-docs" onclick="detalle_impuestos_temu()">

					<i class="fa fa-upload"></i>
					Cargar

				</button>

			</div>

		</div>

    </form>

    <div id="resultado_detalle_impuestos_temu" class="mt-4" style="display: none;">
        <hr>
        <h5 class="mb-3">Resultado del procesamiento</h5>
        <div class="row text-center mb-3">
            <div class="col-md-4 mb-2"><div class="border rounded p-3 h-100">
                <div class="text-muted small">Registros procesados</div>
                <strong id="resultado_procesados" class="h4">0</strong>
            </div></div>
            <div class="col-md-4 mb-2"><div class="border rounded p-3 h-100">
                <div class="text-muted small">Registros actualizados</div>
                <strong id="resultado_actualizados" class="h4 text-success">0</strong>
            </div></div>
            <div class="col-md-4 mb-2"><div class="border rounded p-3 h-100">
                <div class="text-muted small">No encontrados</div>
                <strong id="resultado_no_encontrados" class="h4 text-danger">0</strong>
            </div></div>
        </div>
        <div id="resultado_sin_no_encontrados" class="alert alert-success" style="display: none;">
            Todos los paquetes fueron procesados correctamente.
        </div>
        <div id="resultado_no_encontrados_contenedor" style="display: none;">
            <h6>Tracking no encontrados</h6>
            <div class="table-responsive">
                <table id="tbl_no_encontrados_impuestos_temu" class="table table-striped table-hover w-100">
                    <thead class="thead-light"><tr><th>#</th><th>Tracking</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


</div>
    




<script>
$(document).ready(function() {
        const dropZone = $('#drop_zone');
        const input = $('#detalle_temu');

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
        $('#detalle_temu').on('change', function() {
            const files = this.files;
            let html = '';

            if (files.length > 0) {
                html += '<ul class="list-unstyled">';
                for (let i = 0; i < files.length; i++) {
                    html += `<li><i class="fa fa-file-excel-o text-secondary"></i> ${files[i].name}</li>`;
                }
                html += '</ul>';
            } else {
                html = '<p class="text-muted">No hay archivos seleccionados</p>';
            }

            $('#lista_archivos').html(html);
        });


    });

	$(document).ready(function()
	{
		$("#empresas").select2({
			theme: 'bootstrap4',
			placeholder: "Seleccione la empresa ",
			allowClear: true,
			width: '100%',
		});
			
	});



</script>
