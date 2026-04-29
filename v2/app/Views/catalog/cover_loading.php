<!-- catalog/cover_loading.php -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px;">
                <h6>Envie manualmente uma nova capa:</h6>
                <form method="post" enctype="multipart/form-data" action="<?= base_url('/rdf/form/upload_cover') ?>" class="w-100" style="max-width:400px;">
                    <input type="hidden" class="form-control" id="isbn-upload" name="isbn" value="<?= htmlspecialchars($isbn ?? '') ?>" required>
                    <input type="hidden" class="form-control" id="id_item" name="id_item" value="<?= htmlspecialchars($id_item ?? '') ?>" required>
                    <div class="mb-2">
                        <label for="cover_file" class="form-label">Selecione a imagem (JPEG):</label>
                        <input type="file" class="form-control" id="cover_file" name="cover_file" accept="image/jpeg,image/jpg" required>
                    </div>
                    <div><input type="hidden" name="id_item" value="<?= htmlspecialchars($id_item ?? '') ?>"></div>
                    <button type="submit" class="btn btn-primary">Enviar Capa</button>
                </form>
            </div>
            <div><?php if (isset($msg)) echo $msg; ?></div>
        </div>
        <!-- Menu Lateral: Pesquisa Google -->
        <div class="col-lg-3 col-md-2 d-none d-md-block align-self-start mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Pesquisar ISBN no Google</div>
                <div class="card-body">
                    <?= searchGoogle($isbn ?? '') ?>
                </div>
            </div>
        </div>
    </div>
</div>