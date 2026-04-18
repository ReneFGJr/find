<?= view('layout/header', ['title' => 'Logotipo da Biblioteca • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5" style="max-width: 600px;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/admin/configuration'); ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Logotipo</li>
        </ol>
    </nav>

    <h1 class="h3 fw-bold mb-4"><i class="bi bi-image me-2"></i>Logotipo da Biblioteca</h1>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center">
            <?php if (!empty($library['logo'])): ?>
                <img src="<?= esc($library['logo']); ?>" alt="Logotipo atual" class="rounded shadow-sm mb-3" style="max-height:140px;">
            <?php else: ?>
                <div class="text-muted mb-3"><i class="bi bi-image" style="font-size:3rem;"></i></div>
                <p class="text-muted">Nenhum logotipo definido.</p>
            <?php endif; ?>
            <div class="form-text mb-2">Formatos permitidos: JPG, PNG. Tamanho recomendado: até 400x400px.</div>
        </div>
    </div>

    <form id="logoForm" enctype="multipart/form-data" class="mb-4">
        <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
        <div class="mb-3">
            <label for="logo" class="form-label fw-semibold">Escolher novo logotipo</label>
            <input class="form-control" type="file" id="logo" name="logo" accept="image/png, image/jpeg">
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <span id="logoStatus" class="text-success fw-semibold" style="opacity:0;"></span>
            <button type="submit" class="btn btn-primary px-4" id="btnUpload">
                <i class="bi bi-upload me-1"></i>Enviar
            </button>
        </div>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('logoForm');
    const btnUpload = document.getElementById('btnUpload');
    const logoStatus = document.getElementById('logoStatus');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        btnUpload.disabled = true;
        btnUpload.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando...';
        logoStatus.style.opacity = '0';
        const formData = new FormData(form);
        fetch('<?= base_url('/admin/logo/upload'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(function (data) {
            btnUpload.disabled = false;
            btnUpload.innerHTML = '<i class="bi bi-upload me-1"></i>Enviar';
            if (data.status === '200') {
                logoStatus.className = 'text-success fw-semibold';
                logoStatus.innerHTML = '<i class="bi bi-check-circle me-1"></i>Logotipo atualizado!';
                logoStatus.style.opacity = '1';
                setTimeout(function () { logoStatus.style.opacity = '0'; }, 3000);
                if (data.logo) {
                    document.querySelector('.card-body img').src = data.logo + '?t=' + Date.now();
                }
            } else {
                logoStatus.className = 'text-danger fw-semibold';
                logoStatus.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' + (data.message || 'Erro ao enviar.');
                logoStatus.style.opacity = '1';
            }
        })
        .catch(function () {
            btnUpload.disabled = false;
            btnUpload.innerHTML = '<i class="bi bi-upload me-1"></i>Enviar';
            logoStatus.className = 'text-danger fw-semibold';
            logoStatus.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Erro de conexão.';
            logoStatus.style.opacity = '1';
        });
    });
});
</script>

<?= view('layout/footer'); ?>
