<?= view('layout/header', ['title' => 'Sem ação disponível • FIND']) ?>
<?= view('layout/navbar') ?>

<div class="container py-5 text-center">
    <h1 class="display-4 mb-3">Sem ação disponível</h1>
    <p class="lead">Nenhuma ação está disponível para este item.</p>
    <?php if (!empty($id)): ?>
        <p class="text-muted">ID do item: <strong><?= htmlspecialchars($id) ?></strong></p>
    <?php endif; ?>
    <a href="<?= base_url('/catalog/catalogar') ?>" class="btn btn-primary mt-4"><i class="bi bi-arrow-left me-2"></i>Voltar para Catalogação</a>
</div>

<?= view('layout/footer') ?>
