<?php

/**
 * Ficha resumida de um exemplar do acervo.
 * Espera um array $book.
 */
$displayValue = static function ($value): string {
    $value = trim((string) ($value ?? ''));
    return $value !== '' ? esc($value) : '<span class="text-muted">Não informado</span>';
};

$classification = trim(implode(' ', array_filter([
    $book['i_ln1'] ?? '',
    $book['i_ln2'] ?? '',
    $book['i_ln3'] ?? '',
    $book['i_ln4'] ?? '',
], static fn($value) => trim((string) $value) !== '')));

$statusName = '';
if (!empty($book['i_status'])) {
    try {
        $status = (new \App\Models\Find\Items\Status())->find($book['i_status']);
        $statusName = trim((string) ($status['is_name'] ?? ''));
    } catch (\Throwable $e) {
        $statusName = '';
    }
}
?>

<article class="card h-100 border-0 shadow-sm overflow-hidden">
    <header class="card-header bg-primary text-white border-0 p-4">
        <div class="d-flex align-items-start gap-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25 flex-shrink-0" style="width:48px;height:48px;">
                <i class="bi bi-book fs-4"></i>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                    <div>
                        <div class="small text-white-50 text-uppercase fw-semibold mb-1">Registro bibliográfico</div>
                        <h3 class="h5 mb-1"><?= $displayValue($book['i_titulo'] ?? '') ?></h3>
                    </div>
                    <?php if ($statusName !== '' || !empty($book['i_status'])): ?>
                        <span class="badge rounded-pill bg-light text-primary px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i><?= esc($statusName !== '' ? $statusName : 'Status ' . $book['i_status']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <p class="mb-0 mt-2 text-white-50">
                    <i class="bi bi-person me-1"></i><?= $displayValue($book['i_autores'] ?? '') ?>
                </p>
            </div>
        </div>
    </header>

    <div class="card-body p-4">
        <section aria-labelledby="copy-data-title">
            <h4 id="copy-data-title" class="h6 text-uppercase text-secondary fw-bold mb-3">
                <i class="bi bi-upc-scan me-2"></i>Identificação do exemplar
            </h4>
            <div class="row g-3">
                <?php
                $summaryFields = [
                    ['Tombo', $book['i_tombo'] ?? '', 'fs-5'],
                    ['Exemplar', $book['i_exemplar'] ?? '', 'fs-5'],
                    ['ISBN / Identificador', $book['i_identifier'] ?? '', 'text-break'],
                    ['Ano', $book['i_year'] ?? '', 'fs-5'],
                ];
                foreach ($summaryFields as [$label, $value, $class]):
                ?>
                    <div class="col-sm-6 col-xl-3">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <div class="small text-muted mb-1"><?= esc($label) ?></div>
                            <div class="fw-semibold <?= esc($class) ?>"><?= $displayValue($value) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <hr class="my-4">

        <div class="row g-4">
            <section class="col-md-6" aria-labelledby="location-title">
                <h4 id="location-title" class="h6 text-uppercase text-secondary fw-bold mb-3">
                    <i class="bi bi-bookshelf me-2"></i>Localização no acervo
                </h4>
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted fw-normal">Classificação</dt>
                    <dd class="col-sm-7 fw-semibold"><?= $displayValue($classification) ?></dd>
                    <dt class="col-sm-5 text-muted fw-normal">Localização</dt>
                    <dd class="col-sm-7"><?= $displayValue($book['i_localization'] ?? '') ?></dd>
                    <dt class="col-sm-5 text-muted fw-normal">Biblioteca</dt>
                    <dd class="col-sm-7"><?= $displayValue($book['i_library'] ?? '') ?></dd>
                </dl>
            </section>

            <section class="col-md-6" aria-labelledby="rdf-links-title">
                <h4 id="rdf-links-title" class="h6 text-uppercase text-secondary fw-bold mb-3">
                    <i class="bi bi-diagram-3 me-2"></i>Vínculos bibliográficos
                </h4>
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted fw-normal">Obra</dt>
                    <dd class="col-sm-7"><?= $displayValue($book['i_work'] ?? '') ?></dd>
                    <dt class="col-sm-5 text-muted fw-normal">Expressão</dt>
                    <dd class="col-sm-7"><?= $displayValue($book['i_expression'] ?? '') ?></dd>
                    <dt class="col-sm-5 text-muted fw-normal">Manifestação</dt>
                    <dd class="col-sm-7"><?= $displayValue($book['i_manifestation'] ?? '') ?></dd>
                </dl>
            </section>
        </div>
    </div>

    <footer class="card-footer bg-white border-top p-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <small class="text-muted">
            <i class="bi bi-hash me-1"></i>ID do item: <?= $displayValue($book['id_i'] ?? '') ?>
        </small>
        <button type="button" class="btn btn-outline-primary btn-sm" id="updateItemData" data-update-url="<?= base_url('/catalog/check?isbn=' . rawurlencode((string) ($book['i_identifier'] ?? ''))) ?>">
            <i class="bi bi-arrow-clockwise me-1"></i>Atualizar dados
        </button>
    </footer>
</article>
