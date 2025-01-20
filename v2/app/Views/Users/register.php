<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark border-secondary">
                <div class="card-header text-primary text-center">
                    <h4>Cadastrar-se</h4>
                </div>
                <div class="card-body">
                    <form action="/store-user" method="post">
                        <div class="form-group mb-3">
                            <label for="us_nome" class="form-label text-primary">Nome</label>
                            <input type="text" name="us_nome" id="us_nome" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="us_email" class="form-label text-primary">E-mail</label>
                            <input type="email" name="us_email" id="us_email" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="us_login" class="form-label text-primary">Usuário</label>
                            <input type="text" name="us_login" id="us_login" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="us_password" class="form-label text-primary">Senha</label>
                            <input type="password" name="us_password" id="us_password" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>