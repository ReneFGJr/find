<?= view('layout/header', ['title' => 'Incluir Remissiva • FIND']); ?>
<main class="container py-3">
    <h2 class="mb-3">Incluir Remissiva</h2>
    <form id="formVincularRemissiva" method="post" action="<?= base_url('/catalog/authority/') . '/' . esc(get('id_cc')) ?>/save">
        <input type="hidden" name="id" id="id" value="<?= esc(get('id_cc')) ?>">
        <div class="mb-3">
            <label for="remissiveFilter" class="form-label">Filtrar termo</label>
            <div class="input-group">
                <input type="text" class="form-control" id="remissiveFilter" name="remissiveFilter" value="<?= esc($initialFilter ?? '') ?>" autocomplete="off">
                <button type="button" class="btn btn-outline-secondary" id="btnLupaFiltro" title="Buscar">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        <div class="mb-3">
            <label for="remissiveSelect" class="form-label">Selecione a remissiva</label>
            <select class="form-select" id="remissiveSelect" name="remissiveSelect" size="10">
                <?php foreach ($options as $id => $name): ?>
                    <option value="<?= esc($id) ?>"><?= esc($name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="d-flex justify-content-end mt-3">

            <input type="hidden" name="remissive_id" id="remissive_id">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-link-45deg"></i> Vincular remissiva
            </button>
        </div>
</main>
<script>
    // Filtro instantâneo no select
    document.getElementById('remissiveFilter').addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        const select = document.getElementById('remissiveSelect');
        Array.from(select.options).forEach(function(opt) {
            if (!val || opt.text.toLowerCase().includes(val)) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    });

    // Vincular remissiva: ao submeter, pega o valor selecionado
    document.getElementById('formVincularRemissiva').addEventListener('submit', function(e) {
        var select = document.getElementById('remissiveSelect');
        var selected = select.value;
        document.getElementById('remissive_id').value = selected;
    });
</script>
</form>
</main>
<script>
    // Filtro instantâneo no select
    document.getElementById('remissiveFilter').addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        const select = document.getElementById('remissiveSelect');
        Array.from(select.options).forEach(function(opt) {
            if (!val || opt.text.toLowerCase().includes(val)) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    });
</script>