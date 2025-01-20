    <div class="d-flex">
        <!-- Menu Lateral -->
        <div class="d-flex flex-column flex-shrink-0 bg-dark text-white" style="width: 250px; height: 100vh;">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none p-3">
                <span class="fs-4">Menu</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="#" class="nav-link text-white active" aria-current="page">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('bibliofind');?>" class="nav-link text-white">
                        <i class="bi bi-person"></i> BiblioFind
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-gear"></i> Configurações
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-box-arrow-left"></i> Sair
                    </a>
                </li>
            </ul>
            <hr>
            <div class="text-center">
                <small class="text-white-50">© 2025 Seu Sistema</small>
            </div>
        </div>
        <?php echo $content; ?>
    </div>