<?= view('layout/header', ['title' => 'Autoridades • FIND']); ?>
<?= view('layout/navbar'); ?>
<main class="container py-4">
    <h1 class="mb-4">Autoridades (Autores e Tradutores)</h1>
    <?php if (!empty($authors)): ?>
        <ul class="list-group mb-4">
            <?php foreach ($authors as $author): ?>
                <?php foreach ($author as $id => $nome): ?>
                    <li class="list-group-item"> <strong><?= esc($nome) ?></strong> <span class="text-muted small">[<?= esc($id) ?>]</span></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-warning">Nenhum autor ou tradutor encontrado.</div>
    <?php endif; ?>
</main>

<?= view('layout/footer'); ?>
