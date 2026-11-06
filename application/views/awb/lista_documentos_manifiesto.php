<style>
    

    .file-card {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.07);
        transition: transform .2s ease, box-shadow .2s ease;
        border: 1px solid #f1f1f1;
        width: fit-content;
        flex-direction: column;
    }

    .file-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 26px rgba(0, 0, 0, 0.1);
    }

    .file-icon {
        background: #FEEFEA;
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 26px;
        color: #FE4F00;
    }

    .file-info {
        flex: 1;
    }

    .file-title {
        font-size: 15px;
        font-weight: 600;
        color: #333;
        margin: 0;
        text-align: left;
        height: 25px;
        word-break: break-word;
        text-overflow: hidden;
    }

    .file-actions {
        margin-top: 10px;
        display: flex;
        gap: 10px;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: .2s ease;
        font-size: 16px;
    }

    .action-btn.view {
        background: #E8F1FF;
        color: #0B5ED7;
    }

    .action-btn.view:hover {
        background: #d8e7ff;
    }

    .action-btn.delete {
        background: #FFE5E5;
        color: #D70000;
    }

    .action-btn.delete:hover {
        background: #ffd2d2;
    }
</style>

<div class="row g-3" style="margin-top: 1rem; width: 100%;">
    <?php if (!empty($documentos)): ?>
        <?php foreach ($documentos as $row): ?>
            <div class="col-md-3 col-sm-6">
                <div class="file-card">
                    <div class="file-icon">
                        <i class="fa fa-file-pdf-o"></i>
                    </div>

                    <div class="file-info">
                        <h6 class="file-title" title="<?= $row->nombre_archivo ?>">
                            <?= $row->nombre_nuevo ?>
                        </h6>

                        <div class="file-actions">
                            <a href="<?= base_url($row->url) ?>" target="_blank" class="action-btn view" title="Ver documento">
                                <i class="fa fa-eye"></i>
                            </a>

                            <?php if ($_SESSION['user_id'] == $row->usuario_id): ?>
                                <button class="action-btn delete" onclick="eliminar_documento_manifiesto(<?= $row->id ?>)" title="Eliminar documento">
                                    <i class="fa fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-info text-center" role="alert">
                <i class="fa fa-info-circle me-2"></i>
                Aún no hay documentos cargados para este manifiesto.
            </div>
        </div>
    <?php endif; ?>
</div>