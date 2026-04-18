<?= view('layout/header', ['title' => 'Papéis de Usuários • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/admin/configuration'); ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Papéis de Usuários</li>
        </ol>
    </nav>

    <h1 class="h3 fw-bold mb-4"><i class="bi bi-person-badge me-2"></i>Papéis de Usuários</h1>

    <?php if (empty($groups)): ?>
        <div class="alert alert-info">Nenhum grupo cadastrado.</div>
    <?php else: ?>
        <div class="accordion" id="rolesAccordion">
            <?php foreach ($groups as $idx => $group): ?>
            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button <?= $idx > 0 ? 'collapsed' : ''; ?>" type="button"
                            data-bs-toggle="collapse" data-bs-target="#group-<?= $idx; ?>"
                            aria-expanded="<?= $idx === 0 ? 'true' : 'false'; ?>">
                        <i class="bi bi-people-fill me-2 text-primary"></i>
                        <strong><?= esc($group['name']); ?></strong>
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2"><?= count($group['users']); ?></span>
                    </button>
                </h2>
                <div id="group-<?= $idx; ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : ''; ?>"
                     data-bs-parent="#rolesAccordion">
                    <div class="accordion-body">

                        <!-- Botão adicionar -->
                        <div class="mb-3 text-end">
                            <button class="btn btn-sm btn-outline-primary rounded-pill btn-add-member"
                                    data-group-id="<?= esc($group['id']); ?>"
                                    data-group-name="<?= esc($group['name']); ?>">
                                <i class="bi bi-plus-lg me-1"></i>Adicionar participante
                            </button>
                        </div>

                        <?php if (empty($group['users'])): ?>
                            <p class="text-muted small mb-0">Nenhum participante neste grupo.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($group['users'] as $user): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0" id="member-<?= esc($user['id_gr']); ?>">
                                    <div>
                                        <i class="bi bi-person me-1 text-secondary"></i>
                                        <?= esc($user['name']); ?>
                                        <?php if (!empty($user['nickname'])): ?>
                                            <span class="text-muted small">(<?= esc($user['nickname']); ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill btn-disable-member"
                                            data-id="<?= esc($user['id_gr']); ?>"
                                            data-name="<?= esc($user['name']); ?>"
                                            title="Desabilitar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<!-- Modal Adicionar Participante -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Adicionar participante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Grupo: <strong id="modalGroupName"></strong></p>
                <input type="hidden" id="modalGroupId">
                <label for="userSearch" class="form-label">Buscar usuário</label>
                <input type="text" id="userSearch" class="form-control" placeholder="Digite o nome ou e-mail..." autocomplete="off">
                <div id="userResults" class="list-group mt-2" style="max-height:200px; overflow-y:auto;"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Abrir modal para adicionar participante
    document.querySelectorAll('.btn-add-member').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('modalGroupId').value = this.dataset.groupId;
            document.getElementById('modalGroupName').textContent = this.dataset.groupName;
            document.getElementById('userSearch').value = '';
            document.getElementById('userResults').innerHTML = '';
            new bootstrap.Modal(document.getElementById('addMemberModal')).show();
        });
    });

    // Busca de usuários com debounce
    let searchTimer;
    document.getElementById('userSearch').addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            document.getElementById('userResults').innerHTML = '';
            return;
        }
        searchTimer = setTimeout(function () {
            fetch('<?= base_url('/admin/users/search'); ?>?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(function (users) {
                    const container = document.getElementById('userResults');
                    container.innerHTML = '';
                    if (users.length === 0) {
                        container.innerHTML = '<div class="list-group-item text-muted small">Nenhum usuário encontrado</div>';
                        return;
                    }
                    users.forEach(function (u) {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = u.us_nome + ' (' + u.us_email + ')';
                        item.addEventListener('click', function () {
                            addMember(u.id_us);
                        });
                        container.appendChild(item);
                    });
                });
        }, 300);
    });

    function addMember(userId) {
        const groupId = document.getElementById('modalGroupId').value;
        const formData = new FormData();
        formData.append('id_us', userId);
        formData.append('id_gr', groupId);
        formData.append('<?= csrf_token(); ?>', '<?= csrf_hash(); ?>');

        fetch('<?= base_url('/admin/roles/add-member'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(function (data) {
            if (data.status === '200') {
                location.reload();
            } else {
                alert(data.message || 'Erro ao adicionar participante.');
            }
        });
    }

    // Desabilitar membro com confirmação
    document.querySelectorAll('.btn-disable-member').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            if (!confirm('Deseja desabilitar o papel de "' + name + '"?')) return;

            const formData = new FormData();
            formData.append('id_grm', id);
            formData.append('<?= csrf_token(); ?>', '<?= csrf_hash(); ?>');

            fetch('<?= base_url('/admin/roles/disable-member'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(function (data) {
                if (data.status === '200') {
                    const el = document.getElementById('member-' + id);
                    if (el) el.remove();
                } else {
                    alert(data.message || 'Erro ao desabilitar.');
                }
            });
        });
    });

});
</script>

<?= view('layout/footer'); ?>
