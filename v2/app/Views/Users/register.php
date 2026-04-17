<?= view('layout/header', ['title' => 'Cadastro • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card auth-card">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-success mb-3">Novo cadastro</span>
                    <h1 class="h3 fw-bold mb-2">Quero me cadastrar</h1>
                    <p class="text-secondary mb-4">Crie sua conta para acessar os recursos do FIND.</p>

                    <?php if (session()->getFlashdata('msg')): ?>
                        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?>"><?= esc(session()->getFlashdata('msg')); ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('/store-user'); ?>" method="post" class="row g-3">
                        <div class="col-12">
                            <label for="us_nome" class="form-label">Nome completo</label>
                            <input type="text" name="us_nome" id="us_nome" class="form-control" value="<?= old('us_nome'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="us_email" class="form-label">E-mail</label>
                            <input type="email" name="us_email" id="us_email" class="form-control" value="<?= old('us_email'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="us_login" class="form-label">Usuário</label>
                            <input type="text" name="us_login" id="us_login" class="form-control" value="<?= old('us_login'); ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="us_password" class="form-label">Senha</label>
                            <input type="password" name="us_password" id="us_password" class="form-control" required>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="btn btn-primary">Criar conta</button>
                            <a href="<?= base_url('/login'); ?>" class="btn btn-outline-secondary">Já tenho conta</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layout/footer'); ?>