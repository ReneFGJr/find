<?= view('layout/header', ['title' => 'Classes e Propriedades RDF • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <a href="<?= base_url('/admin/configuration'); ?>" class="btn btn-outline-secondary mb-3">Voltar às Configurações</a>
    <h1 class="h3 fw-bold mb-3">Classes e Propriedades RDF</h1>
    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('msg')); ?></div>
    <?php endif; ?>
    <?php foreach (['C' => ['Classes', $classes], 'P' => ['Propriedades', $properties]] as $type => [$title, $rows]): ?>
        <section class="card shadow-sm mb-4" aria-labelledby="rdf-<?= esc($type); ?>">
            <div class="card-header">
                <h2 class="h5 mb-0" id="rdf-<?= esc($type); ?>"><?= esc($title); ?> <span class="badge bg-secondary"><?= count($rows); ?></span></h2>
            </div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead><tr><th scope="col">ID</th><th scope="col">Nome</th><th scope="col">Tipo</th><th scope="col">Ações</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= esc($row['id_c']); ?></td>
                            <td><?= esc($row['c_class']); ?></td>
                            <td><?= $type === 'C' ? 'Classe' : 'Propriedade'; ?></td>
                            <td><a class="btn btn-sm btn-outline-primary" href="<?= base_url('/admin/rdf-classes/' . $row['id_c'] . '/edit'); ?>" aria-label="Editar <?= esc($row['c_class']); ?>">Editar</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?>
                        <tr><td colspan="4" class="text-secondary">Nenhum registro encontrado.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endforeach; ?>
</main>

<?= view('layout/footer'); ?>
