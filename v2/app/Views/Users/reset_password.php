<?= view('layout/header', ['title' => 'Nova senha • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card auth-card">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-info mb-3">Segurança da conta</span>
                    <h1 class="h3 fw-bold mb-2">Redefinir senha</h1>
                    <p class="text-secondary mb-4">Informe sua nova senha para concluir o acesso.</p>

                    <?php if (session()->getFlashdata('msg')): ?>
                        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?>"><?= esc(session()->getFlashdata('msg')); ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('/update-password'); ?>" method="post">
                        <input type="hidden" name="token" value="<?= esc($token); ?>">
                        <div class="mb-3">
                            <label for="us_password" class="form-label">Nova senha</label>
                            <input type="password" name="us_password" id="us_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar nova senha</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Salvar nova senha</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layout/footer'); ?>