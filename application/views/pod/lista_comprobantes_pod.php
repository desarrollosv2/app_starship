<style>
    .documentos-grid {
    margin-top: 15px;
}

.doc-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e6e6e6;
    padding: 12px;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all .2s ease;
}

.doc-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

.doc-preview {
    height: 140px;
    background: #f5f5f5;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.doc-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.doc-preview i {
    font-size: 50px;
    color: #FE4F00;
}

.doc-info {
    margin-top: 10px;
    text-align: center;
}

.doc-name {
    font-size: 13px;
    font-weight: 600;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.doc-actions {
    margin-top: auto;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.doc-actions .btn {
    width: 38px;
}


</style>

<div class="container-fluid">
<div class="row documentos-grid">
    <?php if (!empty($comprobantes)): ?>
        <?php foreach ($comprobantes as $row): ?>
            <?php
                $extension = pathinfo($row->nombre_archivo, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
            ?>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="doc-card">

                    <div class="doc-preview">
                        <?php if ($isImage): ?>
                            <img src="<?= base_url($row->url) ?>" alt="Documento">
                        <?php else: ?>
                            <i class="fa fa-file-pdf-o"></i>
                        <?php endif; ?>
                    </div>

                    <div class="doc-info">
                        <span class="doc-name" title="<?= $row->nombre_archivo ?>">
                            <?= $row->nombre_archivo ?>
                        </span>
                    </div>

                    <div class="doc-actions">
                        <a href="<?= base_url($row->url) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver comprobante">
                            <i class="fa fa-eye"></i>
                        </a>

                        <?php if ($_SESSION['user_id'] == $row->usuario_id): ?>
                            <button class="btn btn-sm btn-outline-danger" onclick="eliminar_comprobante_pod(<?= $row->id ?>, <?= $row->id_pod ?>)" title="Eliminar comprobante">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fa fa-info-circle"></i>
                No hay comprobantes de entrega cargados para este POD.
            </div>
        </div>
    <?php endif; ?>
</div>


</div>


    
