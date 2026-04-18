<div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">Encaminhar para:</div>
    <div class="card-body d-grid gap-2">
        <?php
        $statusModel = new \App\Models\Find\Items\Status();
        $statusList = $statusModel->orderBy('id_is','asc')->findAll();
        foreach ($statusList as $status): ?>
            <form method="post" action="">
                <input type="hidden" name="novo_status" value="<?= $status['id_is'] ?>">
                <button type="submit" class="btn btn-outline-primary w-100 mb-1">
                    <i class="bi bi-arrow-right-circle me-1"></i> <?= htmlspecialchars($status['is_name']) ?>
                </button>
            </form>
        <?php endforeach; ?>
    </div>
</div>
<div class="card mt-4">
    <div class="card-header bg-light fw-bold">Funções por Status</div>
    <div class="card-body">
        [Informações sobre o processo de catalogação]
    </div>
</div>
