<?php include(APPPATH.'Views/layout/header.php'); ?>
<?php include(APPPATH.'Views/layout/navbar.php'); ?>

<div class="container my-4">
    <h2 class="mb-4">Perfil do Usuário</h2>
    <div class="card mb-3">
        <div class="card-body">
            <h4 class="card-title mb-2">Nome: <?= esc($user['us_nome']) ?></h4>
            <p class="mb-1"><strong>Email:</strong> <?= esc($user['us_email']) ?></p>
            <p class="mb-1"><strong>Login:</strong> <?= esc($user['us_login']) ?></p>
            <p class="mb-1"><strong>ID:</strong> <?= esc($user['id_us']) ?></p>
            <p class="mb-1"><strong>Cadastro:</strong>
                <?php
                $dt = $user['us_cadastro'] ?? '';
                if (empty($dt) || $dt == '0000-00-00' || $dt == '0000-00-00 00:00:00') {
                    echo '(sem registro)';
                } else {
                    $d = new DateTime($dt);
                    echo $d->format('d/m/Y');
                }
                ?>
            </p>
            <!-- Adicione mais campos conforme necessário -->
        </div>
    </div>
    <a href="<?= base_url('/admin/users') ?>" class="btn btn-secondary">Voltar para lista</a>
</div>

<?php include(APPPATH.'Views/layout/footer.php'); ?>
