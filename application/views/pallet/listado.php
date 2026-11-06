<?php $this->load->view('templates/filtros', array('clientes' => $clientes, 'tipos_producto' => $tipos_producto, 'accion' => 'lista_pallets')); ?>

<div class="tabla-cards">
    <table id="tabla-pallets" class="table align-middle w-100">
        <thead>
            <tr>
                <th>N°</th>
                <th>Cliente</th>
                <th>Tipo de producto</th>
                <th>Cajas</th>
                <th>Estado</th>
                <th>Creado</th>
                <th>Cerrado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Fotografias de un pallet (Acciones > Ver fotos) -->
<div class="modal fade" id="Modal_ver_fotos" tabindex="-1" aria-labelledby="Modal_ver_fotos_titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Modal_ver_fotos_titulo"><i class="fa-solid fa-camera"></i> Fotografías del pallet N° <span id="Modal_ver_fotos_numero"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3" id="Modal_ver_fotos_contenido"></div>
            </div>
        </div>
    </div>
</div>