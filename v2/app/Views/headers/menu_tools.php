    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/bibliofind/'); ?>">FIND SERVER</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#supportMenu" aria-controls="supportMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="supportMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/bibliofind'); ?>">Pesquisar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/bibliofind/zerar'); ?>">Zerar Base (Consulta)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/bibliofind/reindex'); ?>">Reindexar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/relatorios'); ?>">Relatórios</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="submenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Configurações
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="submenu">
                            <li><a class="dropdown-item" href="<?= base_url('/configuracoes/gerais'); ?>">Gerais</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/configuracoes/usuarios'); ?>">Usuários</a></li>
                        </ul>
                    </li>
                </ul>
                X
            </div>
        </div>
    </nav>