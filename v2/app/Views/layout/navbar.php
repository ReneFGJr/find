<?php
$isLoggedIn = (bool) session()->get('logged_in');
$firstName = session()->get('first_name') ?: 'Usuário';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="<?= base_url('/'); ?>">
                <img src="<?= base_url('img/logo_find.png'); ?>" alt="FIND" height="20">
            </a>

            <a class="btn btn-outline-light btn-sm rounded-pill d-none d-lg-inline-flex align-items-center gap-2" href="<?= base_url('/bibliotecas'); ?>">
                <i class="bi bi-buildings"></i>
                <span>Bibliotecas</span>
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#findNavbar" aria-controls="findNavbar" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="findNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item"><a class="nav-link active" href="<?= base_url('/'); ?>">Início</a></li>
                <li class="nav-item d-lg-none"><a class="nav-link" href="<?= base_url('/bibliotecas'); ?>"><i class="bi bi-buildings me-1"></i> Bibliotecas</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/'); ?>#recursos">Recursos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/'); ?>#parceiros">Parceiros</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/catalog/index'); ?>" title="Catalogação">
                        Catalogação
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sobre
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= base_url('/about/us'); ?>"><i class="bi bi-info-circle me-2"></i>Sobre o FIND</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/about/faq'); ?>"><i class="bi bi-question-circle me-2"></i>FAQ</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/about/contact'); ?>"><i class="bi bi-envelope me-2"></i>Contato</a></li>
                    </ul>
                </li>

                <!-- Botão Dark Mode -->
                <li class="nav-item ms-lg-2">
                    <button id="darkModeToggle" class="btn btn-outline-secondary rounded-circle" title="Alternar modo escuro" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                        <i id="darkModeIcon" class="bi bi-moon"></i>
                    </button>
                </li>

                <?php if ($isLoggedIn): ?>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link" href="<?= base_url('/admin/configuration'); ?>" title="Configurações">
                            <i class="bi bi-gear"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle nav-user-pill" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= esc($firstName); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-muted"><?= esc(session()->get('us_email') ?? ''); ?></span></li>
                            <li><a class="dropdown-item" href="<?= base_url('/perfil'); ?>"><i class="bi bi-person-lines-fill me-2"></i>Perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?= base_url('/logout'); ?>">Sair</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-light rounded-pill px-3" href="<?= base_url('/login'); ?>">
                            <i class="bi bi-person-circle me-1"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>