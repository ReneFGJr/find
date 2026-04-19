<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-person-lines-fill me-2"></i>Meu Perfil</h2>
    <div class="card mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Nome</dt>
                <dd class="col-sm-9"><?= esc(session()->get('us_nome') ?? ''); ?></dd>
                <dt class="col-sm-3">E-mail</dt>
                <dd class="col-sm-9"><?= esc(session()->get('us_email') ?? ''); ?></dd>
                <dt class="col-sm-3">Usuário</dt>
                <dd class="col-sm-9"><?= esc(session()->get('us_nickname') ?? ''); ?></dd>
                <dt class="col-sm-3">Grupo(s)</dt>
                <dd class="col-sm-9"><?= esc(session()->get('us_groups') ?? ''); ?></dd>
            </dl>
        </div>
    </div>
    <h4 class="mt-4 mb-3"><i class="bi bi-person-badge me-2"></i>Meus Perfis nas Bibliotecas</h4>
    <?php
    // Agrupa por perfil (grupo)
    $grupos = [];
    if (!empty($perfis)) {
        foreach ($perfis as $p) {
            $grupo = $p['gr_name'] ?? 'Sem perfil';
            $grupos[$grupo][] = $p['l_name'] ?? 'Sem biblioteca';
        }
    }
    ?>
    <?php if (!empty($grupos)): ?>
        <div class="row mb-4">
            <?php foreach ($grupos as $grupo => $bibliotecas): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 shadow-sm border-primary">
                        <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                            <i class="bi bi-person-badge"></i>
                            <span class="fw-bold">Perfil: <?= esc($grupo) ?></span>
                        </div>
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Bibliotecas vinculadas:</h6>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($bibliotecas as $biblioteca): ?>
                                        <li class="d-flex align-items-center justify-content-between mb-2">
                                            <span><i class="bi bi-building me-1 text-secondary"></i> <?= esc($biblioteca) ?></span>
                                            <form method="post" action="<?= base_url('/bibliotecas/select'); ?>" class="mb-0 ms-2 biblioteca-form">
                                                <input type="hidden" name="library_id" value="<?= esc($biblioteca, 'attr'); ?>">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-acessar"><i class="bi bi-box-arrow-in-right me-1"></i> Acessar</button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <!-- Modal de seleção -->
                                <div class="modal fade" id="modalSelecionarBiblioteca" tabindex="-1" aria-labelledby="modalSelecionarBibliotecaLabel" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="modalSelecionarBibliotecaLabel"><i class="bi bi-building me-2"></i>Selecione uma biblioteca</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                      </div>
                                      <div class="modal-body text-center">
                                        <p>Confirme a seleção da biblioteca para acessar seus recursos.</p>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-primary" id="btnConfirmarAcesso">Acessar</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    let formToSubmit = null;
                                    const modal = new bootstrap.Modal(document.getElementById('modalSelecionarBiblioteca'));
                                    document.querySelectorAll('.btn-acessar').forEach(function(btn) {
                                        btn.addEventListener('click', function(e) {
                                            e.preventDefault();
                                            formToSubmit = btn.closest('form');
                                            modal.show();
                                        });
                                    });
                                    document.getElementById('btnConfirmarAcesso').addEventListener('click', function() {
                                        if (formToSubmit) {
                                            formToSubmit.submit();
                                            formToSubmit = null;
                                            modal.hide();
                                        }
                                    });
                                });
                                </script>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Nenhum perfil encontrado em bibliotecas.</div>
    <?php endif; ?>
    <a href="<?= base_url('/admin/configuration'); ?>" class="btn btn-outline-primary"><i class="bi bi-gear me-1"></i> Configurações</a>
</div>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>
