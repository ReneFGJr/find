<?php
if (($ydata['n_name'] ?? '') !== '') {
?>
    <div class="d-flex align-items-start gap-2 mb-2">
        <div class="btn-group btn-group-sm flex-shrink-0" role="group" aria-label="Ações do texto">
            <button type="button" class="btn btn-outline-primary" onclick="editItem(<?= (int) $ydata['id_d'] ?>);" title="Editar texto" aria-label="Editar texto">
                <i class="bi bi-pencil"></i>
            </button>
            <button type="button" class="btn btn-outline-danger" onclick="deleteItem(<?= (int) $ydata['id_d'] ?>);" title="Excluir texto" aria-label="Excluir texto">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div>
            <span><?= esc($ydata['n_name']) ?></span>
            <?php if (!empty($ydata['n_lang'])): ?>
                <span class="badge bg-light text-secondary border ms-1"><?= esc($ydata['n_lang']) ?></span>
            <?php endif; ?>
        </div>
    </div>
<?php } ?>
