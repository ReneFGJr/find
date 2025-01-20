<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark border-secondary">
                <div class="card-header text-primary text-center">
                    <h4>Esqueci Minha Senha</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('msg')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('msg') ?></div>
                    <?php endif; ?>
                    <form action="/send-password-reset" method="post">
                        <div class="form-group mb-3">
                            <label for="us_email" class="form-label text-primary">E-mail</label>
                            <input type="email" name="us_email" id="us_email" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>