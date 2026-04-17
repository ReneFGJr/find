<?= view('layout/header', ['title' => 'Login • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <div class="row justify-content-center align-items-center g-4">
        <div class="col-lg-6">
            <div class="card auth-card">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-primary mb-3">Acesso do usuário</span>
                    <h1 class="h3 fw-bold mb-2">Entrar no FIND</h1>
                    <p class="text-secondary mb-4">Use seu login ou e-mail cadastrado no módulo Social.</p>

                    <?php if (session()->getFlashdata('msg')): ?>
                        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?>"><?= esc(session()->getFlashdata('msg')); ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('/authenticate'); ?>" method="post">
                        <div class="mb-3">
                            <label for="us_login" class="form-label">E-mail ou usuário</label>
                            <input type="text" name="us_login" id="us_login" class="form-control" value="<?= old('us_login'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="us_password" class="form-label">Senha</label>
                            <input type="password" name="us_password" id="us_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                        </button>
                    </form>

                    <div class="d-flex justify-content-between flex-wrap gap-2 mt-4 small">
                        <a href="<?= base_url('/forgot-password'); ?>" class="text-decoration-none">Esqueci minha senha</a>
                        <a href="<?= base_url('/register'); ?>" class="text-decoration-none fw-semibold">Quero me cadastrar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="hero-section p-4 p-lg-5 h-100">
                <img src="<?= base_url('public/img/logo/logo_find_big.png'); ?>" alt="FIND" class="hero-logo mb-3">
                <h2 class="h4 fw-bold">Acesso rápido ao ecossistema FIND</h2>
                <p class="text-secondary mb-0">Entre para pesquisar, organizar e acompanhar seus recursos dentro da plataforma.</p>
            </div>
        </div>
    </div>
</main>

<?= view('layout/footer'); ?>