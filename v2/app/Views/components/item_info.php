<?php
// Busca nome do status se possível
$statusName = '';
if (!empty($itemInfo['i_status'])) {
    try {
        $statusModel = new \App\Models\Find\Items\Status();
        $status = $statusModel->find($itemInfo['i_status']);
        $statusName = $status['is_name'] ?? '';
    } catch (\Throwable $e) {
        $statusName = '';
    }
}
?>
<?php if (!empty($itemInfo)): ?>
    <div class="mb-4">
        <div class="card shadow-sm border-primary mb-3">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="bi bi-book me-2"></i>
                <span>Informações do Item</span>
                <?php if ($statusName): ?>
                    <span class="badge bg-info ms-auto">Status: <?= htmlspecialchars($statusName) ?></span>
                <?php elseif (!empty($itemInfo['i_status'])): ?>
                    <span class="badge bg-secondary ms-auto">Status: <?= htmlspecialchars($itemInfo['i_status']) ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <th width="10%" class="text-end">Título</th>
                            <td width="70%"><?= htmlspecialchars($itemInfo['i_titulo'] ?? '') ?></td>

                            <th width="10%" class="text-end">Tombo</th>
                            <td width="10%"><?= htmlspecialchars($itemInfo['i_tombo'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Autor</th>
                            <td><?= htmlspecialchars($itemInfo['i_autor'] ?? '') ?></td>
                            <th class="text-end">ID</th>
                            <td><?= htmlspecialchars($itemInfo['id_i'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">ISBN</th>
                            <td><?= htmlspecialchars($itemInfo['i_identifier'] ?? '') ?></td>
                            <th class="text-end">Exemplar</th>
                            <td><?= htmlspecialchars($itemInfo['i_exemplar'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Cadastrado</th>
                            <td><?= htmlspecialchars($itemInfo['i_created'] ?? '') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>