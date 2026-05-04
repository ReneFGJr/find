<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php $activeTab = $activeTab ?? 'item'; ?>

<!-- Offcanvas lateral para adicionar dado -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddData" aria-labelledby="offcanvasAddDataLabel" style="width:600px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasAddDataLabel">Adicionar Dado</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body p-0" style="height:100%;">
        <iframe id="iframeAddData" src="" style="border:0;width:100%;height:100%;min-height:400px;"></iframe>
    </div>
</div>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Formulário do Item</h2>
    <ul class="nav nav-tabs" id="itemTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'item' ? 'active' : '' ?>" id="tab-item" data-bs-toggle="tab" data-bs-target="#tabItem" type="button" role="tab" aria-controls="tabItem" aria-selected="<?= $activeTab === 'item' ? 'true' : 'false' ?>">Item</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'work' ? 'active' : '' ?>" id="tab-work" data-bs-toggle="tab" data-bs-target="#tabWork" type="button" role="tab" aria-controls="tabWork" aria-selected="<?= $activeTab === 'work' ? 'true' : 'false' ?>">Work</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'expression' ? 'active' : '' ?>" id="tab-expression" data-bs-toggle="tab" data-bs-target="#tabExpression" type="button" role="tab" aria-controls="tabExpression" aria-selected="<?= $activeTab === 'expression' ? 'true' : 'false' ?>">Expression</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'manifestation' ? 'active' : '' ?>" id="tab-manifestation" data-bs-toggle="tab" data-bs-target="#tabManifestation" type="button" role="tab" aria-controls="tabManifestation" aria-selected="<?= $activeTab === 'manifestation' ? 'true' : 'false' ?>">Manifestation</button>
        </li>
    </ul>
    <div class="tab-content border border-top-0 p-4 bg-white" id="itemTabContent">
        <div class="tab-pane fade <?= $activeTab === 'item' ? 'show active' : '' ?>" id="tabItem" role="tabpanel" aria-labelledby="tab-item">
            <div class="mb-3">
                <input type="text" class="form-control mb-3" value="<?= htmlspecialchars($item ?? '') ?>" readonly>
                <?php
                $isbn = $book['i_identifier'] ?? '';
                $coverSrc = function_exists('cover_image') ? cover_image($isbn) : base_url('assets/img/no_cover.png');
                ?>
                <div class="row g-3 align-items-start">
                    <div class="col-lg-9 col-md-8">
                        <?= view('Libraries/item_simple', ['book' => $book]); ?>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="card">
                            <div class="card-header bg-light">Capa</div>
                            <div class="card-body text-center">
                                <img src="<?= esc($coverSrc) ?>" alt="Capa do item" class="img-fluid rounded border" style="max-height: 320px; object-fit: contain;">
                                <?php if (!empty($canCatalogItem) && !empty($book['id_i'])) { ?>
                                    <div class="mt-3 d-grid">
                                        <a href="<?= base_url('item/' . (int) $book['id_i']) ?>" class="btn btn-primary">
                                            <i class="bi bi-journal-check me-1"></i> Catalogar
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade <?= $activeTab === 'work' ? 'show active' : '' ?>" id="tabWork" role="tabpanel" aria-labelledby="tab-work">
            <div class="mb-3">
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $work, 'f' => 'WORK', 'idC' => $i_work]); ?>
            </div>
        </div>
        <div class="tab-pane fade <?= $activeTab === 'expression' ? 'show active' : '' ?>" id="tabExpression" role="tabpanel" aria-labelledby="tab-expression">
            <div class="mb-3">
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $expression, 'f' => 'EXPRESSION', 'idC' => $i_expression]); ?>
            </div>
        </div>
        <div class="tab-pane fade <?= $activeTab === 'manifestation' ? 'show active' : '' ?>" id="tabManifestation" role="tabpanel" aria-labelledby="tab-manifestation">
            <div class="mb-3">
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $manifestation, 'f' => 'MANIFESTATION', 'idC' => $i_manifestation]); ?>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        var tabMap = {
            'tab-item': 'item',
            'tab-work': 'work',
            'tab-expression': 'expression',
            'tab-manifestation': 'manifestation'
        };

        document.querySelectorAll('#itemTab button[data-bs-toggle="tab"]').forEach(function(btn) {
            btn.addEventListener('shown.bs.tab', function() {
                var tabKey = tabMap[btn.id] || 'item';
                $.post('<?= base_url('/catalog/item/tab'); ?>', {
                    tab: tabKey
                });
            });
        });
    })();
</script>

<?php include(APPPATH . 'Views/layout/footer.php'); ?>