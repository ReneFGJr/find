<?php include(APPPATH.'Views/layout/header.php'); ?>
<?php include(APPPATH.'Views/layout/navbar.php'); ?>

<div class="container my-4">
    <h2 class="mb-4">Usuários do Sistema</h2>
    <form class="row g-2 mb-3" method="get" action="">
        <div class="col-auto">
            <input type="text" class="form-control" name="q" value="<?= esc($q ?? '') ?>" placeholder="Buscar usuário...">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Buscar</button>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <!-- <th>Login</th> -->
                    <th>Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (($users ?? []) as $user): ?>
                <tr>
                    <td><?= esc($user['id_us']) ?></td>
                    <td><?= esc($user['us_nome']) ?></td>
                    <td><?= esc($user['us_email']) ?></td>
                    <!-- <td><?= esc($user['us_login']) ?></td> -->
                    <td>
                        <?php
                        $dt = $user['us_cadastro'] ?? '';
                        if (empty($dt) || $dt == '0000-00-00' || $dt == '0000-00-00 00:00:00') {
                            echo '(sem registro)';
                        } else {
                            $d = new DateTime($dt);
                            echo $d->format('d/m/Y');
                        }
                        ?>
                    </td>
                    <td>
                        <a href="/admin/user/profile/<?= esc($user['id_us']) ?>" class="btn btn-outline-info btn-sm" title="Ver perfil"><i class="bi bi-person"></i></a>
                        <a href="/admin/user/edit/<?= esc($user['id_us']) ?>" class="btn btn-outline-primary btn-sm" title="Editar"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($users)): ?>
        <div class="alert alert-warning">Nenhum usuário encontrado.</div>
    <?php endif; ?>
    <div class="form-text">Mostrando até 50 usuários por vez.</div>
</div>

<?php include(APPPATH.'Views/layout/footer.php'); ?>
