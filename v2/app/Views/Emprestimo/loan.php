<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>

<div class="container my-4">
    <h2 class="mb-3">Empréstimo</h2>

    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?> mb-3">
            <?= esc(session()->getFlashdata('msg')); ?>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Obras para empréstimo</h5>

                    <p class="mb-2"><strong>Usuário:</strong> <?= esc($user['us_nome'] ?? ''); ?></p>
                    <p class="mb-2"><strong>E-mail:</strong> <?= esc($user['us_email'] ?? ''); ?></p>
                    <p class="mb-3"><strong>Biblioteca:</strong> <?= esc($library['name'] ?? $library['l_name'] ?? 'Não identificada'); ?></p>

                    <div class="alert <?= !empty(($loanSummary['overdue'] ?? 0)) ? 'alert-warning' : 'alert-success'; ?> py-2">
                        <div><strong>Empréstimos ativos:</strong> <?= esc((string) ($loanSummary['total'] ?? 0)); ?></div>
                        <?php if (!empty(($loanSummary['overdue'] ?? 0))): ?>
                            <div><strong>Atrasados:</strong> <?= esc((string) ($loanSummary['overdue'] ?? 0)); ?></div>
                        <?php else: ?>
                            <div>Nenhum empréstimo atrasado.</div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($activeLoans ?? [])): ?>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tombo</th>
                                        <th>Obra</th>
                                        <th>Prev. devolução</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($activeLoans ?? []) as $loan): ?>
                                        <tr>
                                            <td><?= esc($loan['i_tombo'] ?? ''); ?></td>
                                            <td><?= esc($loan['i_titulo'] ?? ''); ?></td>
                                            <td>
                                                <?php
                                                    $due = (int) ($loan['i_dt_prev'] ?? 0);
                                                    echo $due > 0 ? esc(substr((string) $due, 6, 2) . '/' . substr((string) $due, 4, 2) . '/' . substr((string) $due, 0, 4)) : '-';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($loan['is_overdue'])): ?>
                                                    <span class="badge bg-warning text-dark">Atrasado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Em dia</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($cart ?? [])): ?>
                        <div class="alert alert-light border">Nenhuma obra adicionada ao empréstimo.</div>
                    <?php else: ?>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Tombo</th>
                                        <th>Obra</th>
                                        <th class="text-end">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($cart ?? []) as $item): ?>
                                        <tr>
                                            <td><?= esc($item['i_tombo'] ?? ''); ?></td>
                                            <td><?= esc($item['i_titulo'] ?? ''); ?></td>
                                            <td class="text-end">
                                                <form method="post" action="<?= base_url('/emprestimo/loan'); ?>" class="d-inline">
                                                    <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                                                    <input type="hidden" name="id_us" value="<?= esc($user['id_us'] ?? 0); ?>">
                                                    <input type="hidden" name="action" value="remove_tombo">
                                                    <input type="hidden" name="tombo" value="<?= esc($item['i_tombo'] ?? 0); ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover da lista">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <form method="post" action="<?= base_url('/emprestimo/loan'); ?>" class="row g-2 align-items-end">
                            <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                            <input type="hidden" name="id_us" value="<?= esc($user['id_us'] ?? 0); ?>">
                            <div class="col-12 col-md-4">
                                <label class="form-label">Data de devolução</label>
                                <input type="date" class="form-control" name="due_date" value="<?= date('Y-m-d', strtotime('+7 days')); ?>" min="<?= date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-12 col-md-8 d-flex gap-2 justify-content-md-end flex-wrap">
                                <button type="submit" name="action" value="cancel_loan" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancelar
                                </button>
                                <button type="submit" name="action" value="finalize_loan" class="btn btn-success">
                                    <i class="bi bi-check2-circle me-1"></i>Finalizar Empréstimo
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Adicionar por Tombo</h5>

                    <?php if (empty($linked)): ?>
                        <div class="alert alert-warning mb-0">
                            Este usuário não está vinculado à biblioteca atual. Vincule-o antes de registrar empréstimos.
                        </div>
                    <?php else: ?>
                        <form method="post" action="<?= base_url('/emprestimo/loan'); ?>" class="row g-2">
                            <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                            <input type="hidden" name="id_us" value="<?= esc($user['id_us'] ?? 0); ?>">
                            <input type="hidden" name="action" value="add_tombo">

                            <div class="col-12">
                                <label class="form-label">Número Tombo</label>
                                <input type="number" class="form-control" name="tombo" required min="1" placeholder="Digite o tombo da obra" autofocus>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i>Adicionar à lista
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <a href="<?= base_url('/emprestimo'); ?>" class="btn btn-outline-secondary mt-3">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<?php include(APPPATH . 'Views/layout/footer.php'); ?>
