<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>

<div class="container my-4">
    <h2 class="mb-3">Usuários vinculados à biblioteca</h2>

    <form class="row g-2 mb-3" method="get" action="">
        <div class="col-12 col-md-6">
            <input type="text" class="form-control" name="q" value="<?= esc($q ?? ''); ?>" placeholder="Pesquisar por nome ou e-mail">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Pesquisar</button>
        </div>
    </form>

    <div class="alert alert-light border mb-3">
        <strong>Biblioteca:</strong>
        <?= esc($library['name'] ?? $library['l_name'] ?? $libraryCode ?? 'Não identificada'); ?>
    </div>

    <?php if (empty($users)): ?>
        <div class="alert alert-warning">Nenhum usuário vinculado à biblioteca selecionada.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfis</th>
                        <th class="text-center">Vínculo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user['id_us']); ?></td>
                            <td>
                                <a href="<?= base_url('/emprestimo/loan?id_us=' . (int) $user['id_us']); ?>">
                                    <?= esc($user['us_nome']); ?>
                                </a>
                            </td>
                            <td><?= esc($user['us_email']); ?></td>
                            <td><?= esc(implode(', ', $user['grupos'] ?? [])); ?></td>
                            <td class="text-center">
                                <?php if (!empty($user['vinculado'])): ?>
                                    <form method="post" action="<?= base_url('/emprestimo/unbind-library'); ?>" class="d-inline">
                                        <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                                        <input type="hidden" name="id_us" value="<?= esc($user['id_us']); ?>">
                                        <input type="hidden" name="q" value="<?= esc($q ?? ''); ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover vínculo com a biblioteca">
                                            <i class="bi bi-dash-lg"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="<?= base_url('/emprestimo/bind-library'); ?>" class="d-inline">
                                        <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                                        <input type="hidden" name="id_us" value="<?= esc($user['id_us']); ?>">
                                        <input type="hidden" name="q" value="<?= esc($q ?? ''); ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Vincular usuário à biblioteca">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include(APPPATH . 'Views/layout/footer.php'); ?>
