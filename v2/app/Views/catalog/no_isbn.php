<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h3 class="mb-4">Catalogar Obra Sem ISBN</h3>
            <form method="post" action="">
                <div class="mb-3">
                    <label for="tituloObra" class="form-label">Título da Obra</label>
                    <input type="text" class="form-control" id="titleWork" name="titleWork" value="<?= $titleWork; ?>" placeholder="Digite o título da obra" required></input>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save"></i> Pesquisar e Catalogar
                </button>
            </form>
        </div>
        <div class="col-md-8 col-lg-6">
            <div class="container">
                <div class="row">
                    <?php foreach ($results as $result): ?>
                        <div class="mb-3 col-3 text-center">
                            <img src="<?= $result['cover']; ?>" alt="Capa do Livro" class="img-fluid mb-2" style="max-height: 200px;">
                            <a href="<?= base_url('/catalog/catalogar/metadadoSearch/' . urlencode($result['id_i'] ?? '')) ?>" class="full btn btn-sm btn-outline-primary">Catalogar</a>
                            <p class="card-text text-truncate-2 mb-0" style="font-size:0.7rem;" title="<?= htmlspecialchars($result['i_titulo'] ?? 'Título Desconecido') ?>">
                                <?= htmlspecialchars($result['i_titulo'] ?? 'Título Desconecido') ?>
                                <br>
                                <i> <?= htmlspecialchars($result['i_autores'] ?? 'Desconecido') ?></i>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include(APPPATH . 'Views/layout/footer.php'); ?>