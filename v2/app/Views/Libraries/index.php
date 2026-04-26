<script>
    function submitLibraryForm(element) {
            form.submit();
    }
</script>
<?= view('layout/header', ['title' => 'Bibliotecas • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?> mb-4"><?= esc(session()->getFlashdata('msg')); ?></div>
    <?php endif; ?>

    <section class="hero-section p-4 p-lg-5 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge text-bg-primary mb-3">Seleção de biblioteca</span>
                <h1 class="display-6 fw-bold mb-2">Escolha uma biblioteca</h1>
                <p class="text-secondary mb-0">Os dados abaixo foram carregados a partir do model de bibliotecas do FIND. Ao selecionar uma opção, a identificação fica salva em cookie e você será levado para a área da biblioteca.</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-buildings display-2 text-primary"></i>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <?php if (!empty($libraries)): ?>
            <?php foreach ($libraries as $library): ?>
                <div class="col-md-4 col-xl-3">
                    <div class="card feature-card h-100">
                        <form action="<?= base_url('/bibliotecas/select?library_id='); ?><?= esc((string) ($library['code'] ?? $library['id'] ?? '')); ?>" method="post" class="mt-auto">
                            <div class="card-body p-0 d-flex flex-column">
                               <div class="logo-card mb-1" style="cursor:pointer" onclick="submitLibraryForm(this)">
                                    <img src="<?= esc($library['logo']); ?>" alt="<?= esc($library['name']); ?>">
                                </div>
                                <h3 class="h5 mb-1" style="cursor:pointer" onclick="submitLibraryForm(this)"><?= esc($library['name']); ?></h3>
                                <button type="submit" class="btn <?= ((string) ($selectedId ?? '') === (string) ($library['code'] ?? '')) ? 'btn-success' : 'btn-primary'; ?> w-100">
                                    <i class="bi bi-check2-circle me-1"></i> Selecionar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning mb-0">Nenhuma biblioteca visível foi encontrada.</div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?= view('layout/footer'); ?>