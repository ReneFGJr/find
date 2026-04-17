<?= view('layout/header', ['title' => 'Recuperar senha • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card auth-card">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-warning mb-3">Recuperação de acesso</span>
                    <h1 class="h3 fw-bold mb-2">Esqueci minha senha</h1>
                    <p class="text-secondary mb-4">Informe o e-mail do seu cadastro para receber as instruções.</p>

                    <?php if (session()->getFlashdata('msg')): ?>
                        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?>"><?= esc(session()->getFlashdata('msg')); ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('/send-password-reset'); ?>" method="post">
                        <div class="mb-3">
                            <label for="us_email" class="form-label">E-mail</label>
                            <input type="email" name="us_email" id="us_email" class="form-control" required>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Enviar link</button>
                            <a href="<?= base_url('/login'); ?>" class="btn btn-outline-secondary">Voltar ao login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layout/footer'); ?>