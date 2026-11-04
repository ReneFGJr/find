<?= view('layout/header', ['title' => 'Trocar senha • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card auth-card">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3 fw-bold mb-2">Trocar senha</h1>
                    <p class="text-secondary mb-4">Informe sua senha atual e escolha uma nova senha.</p>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
                    <?php endif; ?>
                    <form action="<?= base_url('/perfil/senha'); ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Senha atual</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nova senha</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" autocomplete="new-password" minlength="8" maxlength="72" aria-describedby="password_help" required>
                            <div id="password_help" class="form-text">Use pelo menos 8 caracteres, sem espaços no início ou no final. Limite de 72 bytes (acentos e emojis ocupam mais de um byte).</div>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirmar nova senha</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" autocomplete="new-password" minlength="8" maxlength="72" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar nova senha</button>
                        <a href="<?= base_url('/perfil'); ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layout/footer'); ?>
