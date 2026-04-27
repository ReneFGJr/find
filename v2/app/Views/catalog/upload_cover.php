<?php include(APPPATH . 'Views/layout/header.php'); ?>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-upload me-2"></i>Upload de Capa</h2>
    <h5>ISBN:<?= $isbn; ?></h5>
    <div class="card">
        <div class="card-body">
            <form action="<?= base_url('catalog/upload_cover') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="isbn" value="<?= esc($isbn); ?>">
                <div class="mb-3">
                    <label for="coverFile" class="form-label">Selecione a imagem da capa</label>
                    <input class="form-control" type="file" id="coverFile" name="coverFile" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Enviar Capa
                </button>
            </form>
        </div>

        <div>
            <?php if (isset($msg)): ?>
                <div class="card-footer">
                    <?= $msg; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
