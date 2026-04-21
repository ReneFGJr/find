<div class="card" id="catalog-status-card">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-check me-2"></i>Status da Catalogação</span>
        <button type="button" class="btn btn-sm btn-light" title="Atualizar" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
    </div>
    <div class="card-body">
        <h6 class="mb-2">Resumo dos Status na Biblioteca</h6>
        <?php if (!empty($statusResumo)) : ?>
            <ul class="list-group">
                <?php foreach($statusResumo as $sr): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('/catalog/catalogar/phase/'.urlencode($sr['id'])); ?>" class="text-decoration-none">
                            <?= htmlspecialchars($sr['status']) ?>
                        </a>
                        <span class="badge bg-primary rounded-pill"><?= $sr['qtd'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">Nenhum status encontrado para esta biblioteca.</p>
        <?php endif; ?>
    </div>
</div>
