<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark border-secondary">
                <div class="card-header text-primary text-center">
                    <h4>Redefinir Senha</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('msg')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('msg') ?></div>
                    <?php endif; ?>
                    <form action="/update-password" method="post">
                        <input type="hidden" name="token" value="<?= $token ?>">
                        <div class="form-group mb-3">
                            <label for="us_password" class="form-label text-primary">Nova Senha</label>
                            <input type="password" name="us_password" id="us_password" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="confirm_password" class="form-label text-primary">Confirmar Nova Senha</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Redefinir Senha</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>