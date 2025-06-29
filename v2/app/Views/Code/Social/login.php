    <div class="login-container">
        <h2>FindSERVER</h2>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('/tt/social/login') ?>" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" id="email" required placeholder="exemplo@dominio.com">
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" name="senha" id="senha" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>
