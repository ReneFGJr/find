<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>

<div class="container my-4">
    <h2 class="mb-3">Relatório de Empréstimos</h2>

    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?> mb-3">
            <?= esc(session()->getFlashdata('msg')); ?>
        </div>
    <?php endif; ?>

    <p class="text-muted mb-4">
        Biblioteca: <strong><?= esc($library['name'] ?? $library['l_name'] ?? 'Não identificada'); ?></strong>
    </p>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">Itens emprestados</div>
                    <div class="display-6 fw-semibold"><?= esc((string) ($totalEmprestados ?? 0)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">Atrasados</div>
                    <div class="display-6 fw-semibold text-danger"><?= esc((string) ($totalAtrasados ?? 0)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">Em dia</div>
                    <div class="display-6 fw-semibold text-success"><?= esc((string) ($totalEmDia ?? 0)); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Empréstimos por período</h5>
                    <canvas id="periodChart" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Situação atual</h5>
                    <canvas id="statusChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Usuário que mais empresta</h5>
            <?php if (empty($topBorrowers ?? [])): ?>
                <div class="alert alert-light border mb-0">Sem dados de histórico para exibir ranking.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Usuário</th>
                                <th class="text-end">Total de empréstimos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($topBorrowers ?? []) as $index => $row): ?>
                                <tr>
                                    <td><?= esc((string) ($index + 1)); ?></td>
                                    <td><?= esc($row['us_nome'] ?? ''); ?></td>
                                    <td class="text-end"><?= esc((string) ($row['total'] ?? 0)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <a href="<?= base_url('/emprestimo'); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const loanPeriods = {
            d7: <?= (int) ($loanPeriods['7'] ?? 0); ?>,
            d14: <?= (int) ($loanPeriods['14'] ?? 0); ?>,
            d28: <?= (int) ($loanPeriods['28'] ?? 0); ?>
        };

        const totals = {
            emprestados: <?= (int) ($totalEmprestados ?? 0); ?>,
            atrasados: <?= (int) ($totalAtrasados ?? 0); ?>,
            emDia: <?= (int) ($totalEmDia ?? 0); ?>
        };

        const periodCtx = document.getElementById('periodChart');
        if (periodCtx) {
            new Chart(periodCtx, {
                type: 'bar',
                data: {
                    labels: ['Últimos 7 dias', 'Últimos 14 dias', 'Últimos 28 dias'],
                    datasets: [{
                        label: 'Empréstimos',
                        data: [loanPeriods.d7, loanPeriods.d14, loanPeriods.d28],
                        backgroundColor: ['#0d6efd', '#198754', '#6f42c1'],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }

        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Em dia', 'Atrasados'],
                    datasets: [{
                        data: [totals.emDia, totals.atrasados],
                        backgroundColor: ['#198754', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    })();
</script>

<?php include(APPPATH . 'Views/layout/footer.php'); ?>
