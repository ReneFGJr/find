    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark border-secondary">
                    <div class="card-header text-center bg-secondary">
                        <h4 class="text-primary">Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('msg')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('msg') ?></div>
                        <?php endif; ?>
                        <form action="/authenticate" method="post">
                            <div class="form-group mb-3">
                                <label for="us_login" class="form-label text-primary">Usuário</label>
                                <input type="text" name="us_login" id="us_login" class="form-control bg-dark text-light border-secondary" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="us_password" class="form-label text-primary">Senha</label>
                                <input type="password" name="us_password" id="us_password" class="form-control bg-dark text-light border-secondary" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="/forgot-password" class="text-primary d-block">Esqueci minha senha</a>
                        <a href="/register" class="text-primary">Cadastrar-se</a>
                    </div>
                </div>
            </div>
        </div>
    </div>