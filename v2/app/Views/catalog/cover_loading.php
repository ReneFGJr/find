<!-- catalog/cover_loading.php -->
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px;">
    <?php if (isset($msg)) echo $msg; ?>

    <h5>Buscando capa do livro...</h5>

    <hr class="my-4" style="width:100%;max-width:400px;">
    <h6>Ou envie manualmente uma nova capa (JPEG):</h6>
    <form method="post" enctype="multipart/form-data" action="<?= site_url('rdf/form/upload_cover') ?>" class="w-100" style="max-width:400px;">
        <div class="mb-2">
            <label for="isbn-upload" class="form-label">ISBN:</label>
            <input type="text" class="form-control" id="isbn-upload" name="isbn" value="<?= htmlspecialchars($isbn ?? '') ?>" required>
        </div>
        <div class="mb-2">
            <label for="cover_file" class="form-label">Selecione a imagem (JPEG):</label>
            <input type="file" class="form-control" id="cover_file" name="cover_file" accept="image/jpeg,image/jpg" required>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Capa</button>
    </form>
    <div class="alert alert-info mt-3">
        A imagem será salva como <code>_covers/image/{isbn}.jpg</code> e substituirá a capa atual.
    </div>
</div>
