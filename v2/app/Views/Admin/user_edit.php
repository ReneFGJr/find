<!-- Formulário de edição de usuário -->
<div class="container my-4">
    <h2 class="mb-4">Editar Usuário</h2>
    <form method="post" action="">
        <div class="mb-3">
            <label for="us_nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="us_nome" name="us_nome" value="<?= esc($user['us_nome']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="us_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="us_email" name="us_email" value="<?= esc($user['us_email']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="us_login" class="form-label">Login</label>
            <input type="text" class="form-control" id="us_login" name="us_login" value="<?= esc($user['us_login']) ?>" required>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Salvar</button>
        <a href="<?= base_url('/admin/users') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
