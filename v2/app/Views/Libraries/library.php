<?= view('layout/header', ['title' => 'Biblioteca selecionada • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?> mb-4"><?= esc(session()->getFlashdata('msg')); ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card auth-card border-0">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-success mb-3">Biblioteca ativa</span>
                    <div class="row align-items-center g-4">
                        <div class="col-md-4 text-center">
                            <div class="logo-card">
                                <img src="<?= esc($library['logo']); ?>" alt="<?= esc($library['name']); ?>">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h1 class="h3 fw-bold mb-2"><?= esc($library['name']); ?></h1>
                            <p class="text-secondary mb-2">Código da biblioteca: <strong><?= esc((string) ($library['code'] ?? '')); ?></strong></p>
                            <p class="text-secondary mb-3">ID recuperado do cookie: <strong><?= esc((string) $cookieId); ?></strong></p>
                            <p class="mb-4"><?= esc($library['about'] ?: 'A biblioteca foi recuperada com sucesso a partir do cookie salvo no navegador.'); ?></p>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="<?= base_url('/bibliotecas'); ?>" class="btn btn-outline-secondary">Trocar biblioteca</a>
                                <a href="<?= base_url('/'); ?>" class="btn btn-primary">Ir para o FIND</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layout/footer'); ?>
