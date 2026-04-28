<div class="card mt-4">
    <div class="card-header bg-light fw-bold">Funções por Status</div>
    <div class="card-body">
        <a href="<?= base_url('catalog/catalogar/phase/' . ($itemInfo['i_status'] ?? '')) ?>" class="btn btn-secondary w-100 mb-1">
            <i class="bi bi-arrow-left-circle me-1"></i>
        Voltar para lista (<?= $itemInfo['i_status']; ?>)
        </a>
    </div>
</div>
