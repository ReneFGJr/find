<?php include(APPPATH.'Views/layout/header.php'); ?>
<?php include(APPPATH.'Views/layout/navbar.php'); ?>
<div class="container my-5">
    <h1 class="mb-4"><i class="bi bi-journal-text me-2"></i>Catalogação</h1>
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong>Resumo dos Itens da Biblioteca</strong>
                </div>
                <div class="card-body">
                    <?php if (!empty($resumo)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($resumo as $status => $qtd): ?>
                                    <tr>
                                        <td><?= esc($status) ?></td>
                                        <td><?= esc($qtd) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">Nenhum item encontrado.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex flex-column gap-3">
            <a href="/catalog/catalogar" class="btn btn-success btn-lg w-100"><i class="bi bi-plus-circle me-2"></i>Catalogar</a>
            <a href="/catalog/etiquetas" class="btn btn-secondary btn-lg w-100"><i class="bi bi-printer me-2"></i>Impressão de Etiquetas</a>
        </div>
    </div>
</div>
<?php include(APPPATH.'Views/layout/footer.php'); ?>
