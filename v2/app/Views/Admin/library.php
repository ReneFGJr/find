<?= view('layout/header', ['title' => 'Dados da Biblioteca • FIND']); ?>
<?= view('layout/navbar'); ?>

<style>
    .field-card {
        transition: box-shadow .2s;
    }
    .field-card:hover {
        box-shadow: 0 .25rem .75rem rgba(0,0,0,.08);
    }
    .field-label {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6c757d;
        margin-bottom: .25rem;
    }
    .field-readonly {
        background-color: #f8f9fa;
        border: 1px dashed #dee2e6;
        padding: .5rem .75rem;
        border-radius: .375rem;
        color: #495057;
        font-family: monospace;
        font-size: .9rem;
    }
    #saveStatus {
        transition: opacity .3s;
    }
</style>

<main class="container py-5" style="max-width: 800px;">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/admin/configuration'); ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dados da Biblioteca</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center mb-4">
        <?php if (!empty($library['logo'])): ?>
            <img src="<?= esc($library['logo']); ?>" alt="Logo" class="rounded me-3" style="height:48px; width:auto;">
        <?php endif; ?>
        <div>
            <h1 class="h3 fw-bold mb-0"><i class="bi bi-building me-2"></i>Dados da Biblioteca</h1>
            <small class="text-muted">Edite as informações da sua biblioteca</small>
        </div>
    </div>

    <!-- Campos somente leitura -->
    <div class="card border-0 shadow-sm mb-4 field-card">
        <div class="card-body">
            <h6 class="card-title text-muted mb-3"><i class="bi bi-lock me-1"></i>Informações do sistema <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1">Somente leitura</span></h6>
            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="field-label">ID</div>
                    <div class="field-readonly"><?= esc($raw['id_l'] ?? '—'); ?></div>
                </div>
                <div class="col-sm-4">
                    <div class="field-label">Código</div>
                    <div class="field-readonly"><?= esc($raw['l_code'] ?? '—'); ?></div>
                </div>
                <div class="col-sm-4">
                    <div class="field-label">ID Externo</div>
                    <div class="field-readonly"><?= esc($raw['l_id'] ?? '—'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário editável -->
    <div class="card border-0 shadow-sm mb-4 field-card">
        <div class="card-body">
            <h6 class="card-title text-muted mb-3"><i class="bi bi-pencil-square me-1"></i>Dados editáveis</h6>

            <form id="libraryForm">
                <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">

                <div class="mb-3">
                    <label for="l_name" class="form-label fw-semibold">
                        Nome da Biblioteca <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control form-control-lg" id="l_name" name="l_name"
                           value="<?= esc($raw['l_name'] ?? ''); ?>" required placeholder="Ex: Biblioteca Central">
                </div>

                <div class="mb-3">
                    <label for="l_about" class="form-label fw-semibold">Sobre</label>
                    <textarea class="form-control" id="l_about" name="l_about" rows="4"
                              placeholder="Descreva brevemente a biblioteca, sua missão e serviços..."><?= esc($raw['l_about'] ?? ''); ?></textarea>
                    <div class="form-text">Uma breve descrição pública da biblioteca.</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-8">
                        <label for="l_net" class="form-label fw-semibold">
                            <i class="bi bi-diagram-3 me-1"></i>Rede
                        </label>
                        <select class="form-select" id="l_net" name="l_net">
                            <option value="">Selecione uma rede...</option>
                            <?php foreach (($redes ?? []) as $rede): ?>
                                <option value="<?= esc($rede['rd_name']); ?>" <?= (isset($raw['l_net']) && $raw['l_net'] === $rede['rd_name']) ? 'selected' : ''; ?>>
                                    <?= esc($rede['rd_name']); ?>
                                </option>
                            <?php endforeach; ?>
                            <?php if (!empty($raw['l_net']) && (!isset($redes) || !in_array($raw['l_net'], array_column($redes, 'rd_name')))): ?>
                                <option value="<?= esc($raw['l_net']); ?>" selected><?= esc($raw['l_net']); ?> (personalizado)</option>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">Rede ou consórcio ao qual a biblioteca pertence. Se não encontrar, digite manualmente.</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold">Visibilidade</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="l_visible" name="l_visible"
                                   value="1" <?= (!empty($raw['l_visible']) && $raw['l_visible'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="l_visible">
                                <?php if (!empty($raw['l_visible']) && $raw['l_visible'] == 1): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-eye me-1"></i>Visível</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-eye-slash me-1"></i>Oculta</span>
                                <?php endif; ?>
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center">
                    <span id="saveStatus" class="text-success fw-semibold" style="opacity:0;">
                        <i class="bi bi-check-circle me-1"></i>Salvo com sucesso!
                    </span>
                    <button type="submit" class="btn btn-primary px-4" id="btnSave">
                        <i class="bi bi-floppy me-1"></i>Salvar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logo info -->
    <div class="card border-0 shadow-sm field-card">
        <div class="card-body">
            <h6 class="card-title text-muted mb-3"><i class="bi bi-image me-1"></i>Logotipo atual</h6>
            <div class="text-center py-3">
                <?php if (!empty($library['logo'])): ?>
                    <img src="<?= esc($library['logo']); ?>" alt="Logotipo" class="rounded shadow-sm" style="max-height:120px;">
                    <div class="form-text mt-2">Para alterar o logotipo, acesse <a href="<?= base_url('/admin/logo'); ?>">Configurações &raquo; Logotipo</a>.</div>
                <?php else: ?>
                    <div class="text-muted"><i class="bi bi-image" style="font-size:3rem;"></i></div>
                    <p class="text-muted mt-2 mb-0">Nenhum logotipo definido. <a href="<?= base_url('/admin/logo'); ?>">Enviar logotipo</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('libraryForm');
    const btnSave = document.getElementById('btnSave');
    const saveStatus = document.getElementById('saveStatus');
    const visibleCheckbox = document.getElementById('l_visible');
    const visibleLabel = visibleCheckbox.closest('.form-check').querySelector('.form-check-label');

    // Toggle visibilidade label
    visibleCheckbox.addEventListener('change', function () {
        if (this.checked) {
            visibleLabel.innerHTML = '<span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-eye me-1"></i>Visível</span>';
        } else {
            visibleLabel.innerHTML = '<span class="badge bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-eye-slash me-1"></i>Oculta</span>';
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Salvando...';
        saveStatus.style.opacity = '0';

        const formData = new FormData(form);

        fetch('<?= base_url('/admin/library/save'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(function (data) {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-floppy me-1"></i>Salvar alterações';

            if (data.status === '200') {
                saveStatus.className = 'text-success fw-semibold';
                saveStatus.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salvo com sucesso!';
                saveStatus.style.opacity = '1';
                setTimeout(function () { saveStatus.style.opacity = '0'; }, 3000);
            } else {
                saveStatus.className = 'text-danger fw-semibold';
                saveStatus.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' + (data.message || 'Erro ao salvar.');
                saveStatus.style.opacity = '1';
            }
        })
        .catch(function () {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-floppy me-1"></i>Salvar alterações';
            saveStatus.className = 'text-danger fw-semibold';
            saveStatus.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Erro de conexão.';
            saveStatus.style.opacity = '1';
        });
    });
});
</script>

<?= view('layout/footer'); ?>
