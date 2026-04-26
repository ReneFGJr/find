<?= view('layout/header', ['title' => 'Autoridades • FIND']); ?>
<?= view('layout/navbar'); ?>
<main class="container py-4">
    <h1 class="mb-4">Autoridades (Autores e Tradutores)</h1>
    <div class="mb-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Filtrar autoridade...">
    </div>
    <?php if (!empty($authors)): ?>
        <ul class="list-group mb-4" id="authorityList">
            <?php $count = 0; ?>
            <?php foreach ($authors as $author): ?>
                <?php foreach ($author as $id => $nome): ?>
                    <li class="list-group-item authority-item" data-nome="<?= strtolower(esc($nome)) ?>" style="<?= (++$count > 20) ? 'display:none;' : '' ?>">
                        <a href="<?= base_url('catalog/authority/' . $id); ?>">
                            <i class="bi bi-person-badge me-2"></i>
                            <strong><?= esc($nome) ?></strong> <span class="text-muted small">[<?= esc($id) ?>]</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-warning">Nenhum autor ou tradutor encontrado.</div>
    <?php endif; ?>
</main>

<script>
    document.getElementById('searchBox').addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        let shown = 0;
        document.querySelectorAll('.authority-item').forEach(function(item) {
            if (!val) {
                // Mostra só os 20 primeiros se não houver filtro
                if (shown < 20) {
                    item.style.display = '';
                    shown++;
                } else {
                    item.style.display = 'none';
                }
            } else {
                if (item.dataset.nome.includes(val)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            }
        });
    });
</script>

<?= view('layout/footer'); ?>
