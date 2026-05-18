<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>
<?php
$titleWork = $titleWork ?? '';
$results = $results ?? [];
?>

<div class="container my-4">
    <?php $hasSearch = !empty(trim((string)($titleWork ?? ''))); ?>
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
            <?php if ($hasSearch && empty($results)): ?>
                <div class="col-12 text-center mt-5">
                    <p class="text-muted mb-2">Nenhum titulo encontrado.</p>
                    <form method="post" action="<?= base_url('/catalog/catalogar/no_isbn_create') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="titleWork" value="<?= htmlspecialchars($titleWork) ?>">
                        <button type="submit" class="btn btn-sm btn-outline-warning full">
                            <i class="bi bi-plus-circle me-1"></i> Cadastrar nova obra
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-8 col-lg-6">
            <div class="container">
                <div class="row">
                    <?php foreach ($results as $result): ?>
                        <?php $hasTitle = !empty(trim((string)($result['i_titulo'] ?? ''))); ?>
                        <div class="mb-3 col-3 text-center">
                            <img src="<?= $result['cover']; ?>" alt="Capa do Livro" class="img-fluid mb-2" style="max-height: 200px;">
                            <?php if ($hasTitle): ?>
                                <a href="#" class="full btn btn-sm btn-outline-primary insert-exemplar" data-itemid="<?= (int)($result['id_i'] ?? 0) ?>">Inserir no catalogo</a>
                            <?php else: ?>
                                <a href="<?= base_url('/catalog/catalogar/metadadoSearch/' . (int)($result['id_i'] ?? 0)) ?>" class="full btn btn-sm btn-outline-primary">Cadastrar obra</a>
                            <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.insert-exemplar').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.getAttribute('data-itemid');
            if (confirm('Deseja adicionar um novo exemplar deste livro na biblioteca?')) {
                window.location.href = '<?= base_url("/catalog/catalogar/criar_exemplar") ?>/' + itemId;
            }
        });
    });
});
</script>