<div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">Encaminhar para ($ID):</div>
    <div class="card-body d-grid gap-2">
        <?php
        $statusModel = new \App\Models\Find\Items\Status();
        $statusList = $statusModel->orderBy('id_is','asc')
            ->whereIn('id_is',[0,1,2,3,4,5])
            ->findAll();
        foreach ($statusList as $status): ?>
            <a href="<?= base_url('catalog/status/' . ($itemInfo['id_i'] ?? '')) . '?status=' . $status['id_is'] ?>" class="btn btn-outline-primary w-100 mb-1" onclick="return confirm('Deseja realmente encaminhar para o status <?= htmlspecialchars($status['is_name']) ?>?');">
                <i class="bi bi-arrow-right-circle me-1"></i> <?= htmlspecialchars($status['is_name']) ?><sup><?= $status['id_is']; ?></sup>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<div class="card mt-4">
    <div class="card-header bg-light fw-bold">Funções por Status</div>
    <div class="card-body">
        [Informações sobre o processo de catalogação]
    </div>
</div>
