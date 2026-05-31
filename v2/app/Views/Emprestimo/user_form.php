<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>

<div class="container my-4">
    <h2 class="mb-3"><?= !empty($isEdit) ? 'Editar usuário' : 'Novo usuário'; ?></h2>

    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?> mb-3">
            <?= esc(session()->getFlashdata('msg')); ?>
        </div>
    <?php endif; ?>

    <div class="alert alert-light border mb-3">
        <strong>Biblioteca:</strong>
        <?= esc($library['name'] ?? $library['l_name'] ?? $libraryCode ?? 'Não identificada'); ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= base_url('/emprestimo/user/save'); ?>" class="row g-3">
                <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                <input type="hidden" name="id_us" value="<?= esc(old('id_us', $user['id_us'] ?? 0)); ?>">

                <div class="col-12 col-md-6">
                    <label class="form-label">Nome</label>
                    <input type="text" class="form-control" name="us_nome" required value="<?= esc(old('us_nome', $user['us_nome'] ?? '')); ?>">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">E-mail</label>
                    <input type="email" class="form-control" name="us_email" required value="<?= esc(old('us_email', $user['us_email'] ?? '')); ?>">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Login</label>
                    <input type="text" class="form-control" name="us_login" value="<?= esc(old('us_login', $user['us_login'] ?? '')); ?>" placeholder="Se vazio, usa o e-mail">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Senha <?= !empty($isEdit) ? '(deixe em branco para manter)' : ''; ?></label>
                    <input type="password" class="form-control" name="us_password" <?= empty($isEdit) ? 'required' : ''; ?>>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="<?= base_url('/emprestimo'); ?>" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i><?= !empty($isEdit) ? 'Atualizar' : 'Criar usuário'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(APPPATH . 'Views/layout/footer.php'); ?>
