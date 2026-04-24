<?php
/**
 * Exibe os metadados do livro ($book['meta']) em formato amigável
 * Espera $meta como array
 */
?>
<div class="row g-3">
<?php pre($book['meta'] ?? [],false); ?>
    <?php foreach ($book['meta'] ?? [] as $field => $values): ?>
        <?php if (!empty($values)): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card mb-2 shadow-sm">
                    <div class="card-header bg-secondary text-white py-1 px-2">
                        <?= esc($field) ?>
                    </div>
                    <div class="card-body py-2 px-2">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($values as $v): ?>
                                <li>
                                    <span class="fw-semibold"><?= esc($v['name'] ?? '') ?></span>
                                    <?php if (!empty($v['lang'])): ?>
                                        <span class="badge bg-info ms-1"><?= esc($v['lang']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($v['ID'])): ?>
                                        <span class="text-muted ms-1 small">#<?= esc($v['ID']) ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
