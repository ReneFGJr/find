<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <h2>Editor RDF - Conceito #<?= htmlspecialchars($concept['id'] ?? '') ?></h2>
    <form method="post" action="">
        <div class="mb-3">
            <label for="rdf_titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="rdf_titulo" name="titulo" value="<?= htmlspecialchars($concept['name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="rdf_lang" class="form-label">Idioma</label>
            <input type="text" class="form-control" id="rdf_lang" name="lang" value="<?= htmlspecialchars($concept['lang'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="rdf_class" class="form-label">Classe</label>
            <input type="text" class="form-control" id="rdf_class" name="class" value="<?= htmlspecialchars($concept['Class'] ?? '') ?>">
        </div>
        <h5>Propriedades</h5>
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $prop): ?>
                <div class="mb-2">
                    <label class="form-label"><?= htmlspecialchars($prop['Property']) ?></label>
                    <input type="text" class="form-control" name="prop[<?= htmlspecialchars($prop['Property']) ?>][]" value="<?= htmlspecialchars($prop['Caption']) ?>">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">Nenhuma propriedade encontrada.</div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary mt-3">Salvar</button>
    </form>
</div>
<?= $this->endSection() ?>