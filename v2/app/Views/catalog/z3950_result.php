<?php include(APPPATH . 'Views/layout/header.php'); ?>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-cloud-download me-2"></i>Resultado da consulta Z39.50</h2>
    <?php if (!empty($result) && is_array($result)): ?>
        <div class="card mb-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Property</th>
                                <th>Valor</th>
                                <th>Classe</th>
                                <th>Idioma</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($result as $row): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['ID'] ?? '') ?></span></td>
                                <td><strong><?= htmlspecialchars($row['property'] ?? '') ?></strong></td>
                                <td><?= htmlspecialchars($row['literal'] ?? '') ?></td>
                                <td><span class="text-primary fw-bold"><?= htmlspecialchars($row['Class'] ?? '') ?></span></td>
                                <td><?= htmlspecialchars($row['lang'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Nenhum resultado retornado da consulta Z39.50.</div>
    <?php endif; ?>
</div>
<div class="text-center text-muted small mt-4">FIND • Plataforma de descoberta, organização e acesso ao conhecimento.</div>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>
