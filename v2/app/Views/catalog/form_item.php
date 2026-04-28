<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Formulário do Item</h2>
    <ul class="nav nav-tabs" id="itemTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-item" data-bs-toggle="tab" data-bs-target="#tabItem" type="button" role="tab" aria-controls="tabItem" aria-selected="true">Item</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-work" data-bs-toggle="tab" data-bs-target="#tabWork" type="button" role="tab" aria-controls="tabWork" aria-selected="false">Work</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-expression" data-bs-toggle="tab" data-bs-target="#tabExpression" type="button" role="tab" aria-controls="tabExpression" aria-selected="false">Expression</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-manifestation" data-bs-toggle="tab" data-bs-target="#tabManifestation" type="button" role="tab" aria-controls="tabManifestation" aria-selected="false">Manifestation</button>
        </li>
    </ul>
    <div class="tab-content border border-top-0 p-4 bg-white" id="itemTabContent">
        <div class="tab-pane fade show active" id="tabItem" role="tabpanel" aria-labelledby="tab-item">
            <div class="mb-3">
                <input type="text" class="form-control" value="<?= htmlspecialchars($item ?? '') ?>" readonly>
                <?= view('Libraries/item_simple', ['book' => $book]); ?>
            </div>
        </div>
        <div class="tab-pane fade" id="tabWork" role="tabpanel" aria-labelledby="tab-work">
            <div class="mb-3">
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $work, 'f' => 'WORK', 'idC' => $i_work]); ?>
            </div>
        </div>
        <div class="tab-pane fade" id="tabExpression" role="tabpanel" aria-labelledby="tab-expression">
            <div class="mb-3">
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $expression, 'f' => 'EXPRESSION', 'idC' => $i_expression]); ?>
            </div>
        </div>
        <div class="tab-pane fade" id="tabManifestation" role="tabpanel" aria-labelledby="tab-manifestation">
            <div class="mb-3">
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $manifestation, 'f' => 'MANIFESTATION', 'idC' => $i_manifestation]); ?>
            </div>
        </div>
    </div>
</div>

<?php include(APPPATH . 'Views/layout/footer.php'); ?>