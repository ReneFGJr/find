<?= view('layout/header', ['title' => 'Erro na importação Z39.50']); ?>
<div class="container py-4">
    <h2 class="mb-3">Erro na importação Z39.50</h2>
    <p>ISBN: <?= esc($isbn ?? '') ?></p>
    <div class="alert alert-danger" role="alert" style="white-space:pre-wrap;"><?= esc($message ?? 'Não foi possível concluir a importação.') ?></div>
    <a class="btn btn-primary" href="<?= esc(base_url('catalog/import_z3950') . '?isbn=' . rawurlencode((string) ($isbn ?? '')), 'attr') ?>">Tentar novamente</a>
</div>
<?= view('layout/footer'); ?>
