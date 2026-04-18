<?= view('layout/header', ['title' => 'Sublocais • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/admin/configuration'); ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sublocais</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0"><i class="bi bi-geo-alt me-2"></i>Sublocais</h1>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#newPlaceModal">
            <i class="bi bi-plus-lg me-1"></i>Novo local
        </button>
    </div>

    <div class="alert alert-light border d-flex align-items-start mb-4" role="alert">
        <i class="bi bi-info-circle-fill text-primary me-2 mt-1"></i>
        <span>Sublocais podem ser utilizados para representar <strong>bibliotecas filiais</strong>, salas de leitura, acervos especiais ou qualquer subdivisão física da biblioteca.</span>
    </div>

    <?php if (empty($places)): ?>
        <div class="alert alert-info">Nenhum sublocal cadastrado.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Nome</th>
                        <th style="width:100px;" class="text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($places as $place): ?>
                    <tr id="place-<?= esc($place['id_lp']); ?>">
                        <td class="text-muted"><?= esc($place['id_lp']); ?></td>
                        <td>
                            <span class="place-name-display"><?= esc($place['lp_name']); ?></span>
                            <div class="place-name-edit d-none">
                                <div class="input-group input-group-sm" style="max-width:400px;">
                                    <input type="text" class="form-control edit-name-input" value="<?= esc($place['lp_name']); ?>">
                                    <button class="btn btn-outline-success btn-save-name" data-id="<?= esc($place['id_lp']); ?>">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-cancel-edit">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary btn-edit-name" title="Editar nome">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>

<!-- Modal Novo Local -->
<div class="modal fade" id="newPlaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Novo sublocal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <label for="newPlaceName" class="form-label">Nome do local</label>
                <input type="text" id="newPlaceName" class="form-control" placeholder="Ex: Sala de Leitura" autocomplete="off">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnCreatePlace">
                    <i class="bi bi-plus-lg me-1"></i>Criar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Criar novo local
    document.getElementById('btnCreatePlace').addEventListener('click', function () {
        const name = document.getElementById('newPlaceName').value.trim();
        if (!name) { alert('Informe o nome do local.'); return; }

        const formData = new FormData();
        formData.append('lp_name', name);
        formData.append('<?= csrf_token(); ?>', '<?= csrf_hash(); ?>');

        fetch('<?= base_url('/admin/places/create'); ?>', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(function (data) {
                if (data.status === '200') {
                    location.reload();
                } else {
                    alert(data.message || 'Erro ao criar local.');
                }
            });
    });

    // Editar nome - mostrar campo
    document.querySelectorAll('.btn-edit-name').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            row.querySelector('.place-name-display').classList.add('d-none');
            row.querySelector('.place-name-edit').classList.remove('d-none');
            row.querySelector('.edit-name-input').focus();
        });
    });

    // Cancelar edição
    document.querySelectorAll('.btn-cancel-edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            row.querySelector('.place-name-display').classList.remove('d-none');
            row.querySelector('.place-name-edit').classList.add('d-none');
        });
    });

    // Salvar nome
    document.querySelectorAll('.btn-save-name').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const row = this.closest('tr');
            const name = row.querySelector('.edit-name-input').value.trim();
            if (!name) { alert('Nome não pode ser vazio.'); return; }

            const formData = new FormData();
            formData.append('id_lp', id);
            formData.append('lp_name', name);
            formData.append('<?= csrf_token(); ?>', '<?= csrf_hash(); ?>');

            fetch('<?= base_url('/admin/places/update-name'); ?>', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(function (data) {
                    if (data.status === '200') {
                        row.querySelector('.place-name-display').textContent = name;
                        row.querySelector('.place-name-display').classList.remove('d-none');
                        row.querySelector('.place-name-edit').classList.add('d-none');
                    } else {
                        alert(data.message || 'Erro ao atualizar.');
                    }
                });
        });
    });

});
</script>

<?= view('layout/footer'); ?>
