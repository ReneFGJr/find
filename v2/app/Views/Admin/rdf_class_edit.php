<?= view('layout/header', ['title' => 'Editar registro RDF • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <h1 class="h3 fw-bold mb-3">Editar registro RDF #<?= esc($record['id_c']); ?></h1>
    <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= esc($error); ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>
    <form method="post" action="<?= base_url('/admin/rdf-classes/' . $record['id_c'] . '/edit'); ?>" class="card card-body shadow-sm">
        <?= csrf_field(); ?>
        <div class="mb-3">
            <label for="c_class" class="form-label">Nome</label>
            <input id="c_class" name="c_class" class="form-control" required maxlength="200" value="<?= esc($record['c_class']); ?>">
        </div>
        <div class="mb-3">
            <label for="c_type" class="form-label">Tipo</label>
            <select id="c_type" name="c_type" class="form-select" required>
                <option value="C" <?= $record['c_type'] === 'C' ? 'selected' : ''; ?>>Classe (C)</option>
                <option value="P" <?= $record['c_type'] === 'P' ? 'selected' : ''; ?>>Propriedade (P)</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="<?= base_url('/admin/rdf-classes'); ?>" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</main>

<?= view('layout/footer'); ?>
