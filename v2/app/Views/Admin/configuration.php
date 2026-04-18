<?= view('layout/header', ['title' => 'Configurações • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">

    <h1 class="h3 fw-bold mb-2"><i class="bi bi-gear me-2"></i>Configurações</h1>
    <?php if (!empty($library)): ?>
        <p class="text-secondary mb-4">Biblioteca: <strong><?= esc($library['name'] ?? ''); ?></strong></p>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        <!-- Nome da Biblioteca -->
        <div class="col">
            <a href="<?= base_url('/admin/library'); ?>" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-building fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">Nome da Biblioteca</h5>
                            <p class="card-text small text-secondary mb-0">Alterar nome e informações da biblioteca</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Logotipo -->
        <div class="col">
            <a href="<?= base_url('/admin/logo'); ?>" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-image fs-3 text-success"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">Logotipo</h5>
                            <p class="card-text small text-secondary mb-0">Gerenciar o logotipo da biblioteca</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Sublocais -->
        <div class="col">
            <a href="<?= base_url('/admin/places'); ?>" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-geo-alt fs-3 text-warning"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">Sublocais</h5>
                            <p class="card-text small text-secondary mb-0">Gerenciar locais e setores da biblioteca</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Gerenciamento de Usuários -->
        <div class="col">
            <a href="<?= base_url('/admin/users'); ?>" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-people fs-3 text-info"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">Usuários</h5>
                            <p class="card-text small text-secondary mb-0">Gerenciar usuários da biblioteca</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Gerenciamento de Papéis -->
        <div class="col">
            <a href="<?= base_url('/admin/roles'); ?>" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-person-badge fs-3 text-danger"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">Papéis de Usuários</h5>
                            <p class="card-text small text-secondary mb-0">Gerenciar grupos e permissões</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

</main>

<?= view('layout/footer'); ?>
