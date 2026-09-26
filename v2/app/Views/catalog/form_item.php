<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php
$activeTab = $activeTab ?? 'item';
$isbn = $book['i_identifier'] ?? '';
$coverSrc = function_exists('cover_image') ? cover_image($isbn) : base_url('assets/img/no_cover.png');
?>

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

<!-- Painel lateral para procurar ou enviar a capa -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="coverPanel" aria-labelledby="coverPanelLabel" style="width:100%;max-width:700px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="coverPanelLabel">Procurar Capa</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body">
        <iframe src="<?= base_url('/catalog/upload_cover') ?>?isbn=<?= rawurlencode($isbn) ?>" title="Procurar Capa" style="width:100%;height:70vh;border:0;"></iframe>
        <div class="mt-3 text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Fechar</button>
        </div>
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
        <li class="nav-item" role="analitic">
            <button class="nav-link <?= $activeTab === 'analitic' ? 'active' : '' ?>" id="tab-analitic" data-bs-toggle="tab" data-bs-target="#tabAnalitic" type="button" role="tab" aria-controls="tabAnalitic" aria-selected="<?= $activeTab === 'analitic' ? 'true' : 'false' ?>">Analitic</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'rdf' ? 'active' : '' ?>" id="tab-rdf" data-bs-toggle="tab" data-bs-target="#tabRdf" type="button" role="tab" aria-controls="tabRdf" aria-selected="<?= $activeTab === 'rdf' ? 'true' : 'false' ?>">RDF</button>
        </li>
    </ul>
    <div class="tab-content border border-top-0 p-4 bg-white" id="itemTabContent">
        <div class="tab-pane fade <?= $activeTab === 'item' ? 'show active' : '' ?>" id="tabItem" role="tabpanel" aria-labelledby="tab-item">
            <div class="py-2">
                <div class="row g-3 align-items-start">
                    <div class="col-lg-9 col-md-8">
                        <?= view('Libraries/item_simple', ['book' => $book]); ?>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <aside class="card border-0 shadow-sm overflow-hidden">
                            <div class="card-header bg-dark text-white border-0 py-3">
                                <i class="bi bi-image me-2"></i>Capa da obra
                            </div>
                            <div class="card-body text-center p-4">
                                <div class="bg-light border rounded-3 p-3 mb-3 d-flex align-items-center justify-content-center" style="min-height:300px;">
                                    <img id="itemCoverImage" src="<?= esc($coverSrc) ?>" alt="Capa de <?= esc($book['i_titulo'] ?? 'obra') ?>" class="img-fluid rounded shadow-sm" style="max-height:320px;object-fit:contain;">
                                </div>
                                <div class="small text-muted text-break mb-3">
                                    ISBN: <?= esc($isbn !== '' ? $isbn : 'Não informado') ?>
                                </div>
                                <div class="mt-3 d-grid">
                                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#coverPanel" aria-controls="coverPanel">
                                        <i class="bi bi-search me-1"></i> Procurar capa
                                    </button>
                                </div>
                                <?php if (!empty($canCatalogItem) && !empty($book['id_i'])) { ?>
                                    <div class="mt-3 d-grid">
                                        <a href="<?= base_url('item/' . (int) $book['id_i']) ?>" class="btn btn-primary">
                                            <i class="bi bi-journal-check me-1"></i> Catalogar
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </aside>
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
        <div class="tab-pane fade <?= $activeTab === 'analitic' ? 'show active' : '' ?>" id="tabAnalitic" role="tabpanel" aria-labelledby="tab-analitic">
            <div class="mb-3">
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $analitic, 'f' => 'ANALITIC', 'idC' => $i_analitic]); ?>
            </div>
        </div>
        <div class="tab-pane fade <?= $activeTab === 'rdf' ? 'show active' : '' ?>" id="tabRdf" role="tabpanel" aria-labelledby="tab-rdf">
            <div class="mb-3">
                <div class="card">
                    <div class="card-header bg-light">RDF do Item</div>
                    <div class="card-body">
                        <pre class="mb-0" style="max-height:400px;overflow:auto;white-space:pre-wrap;word-break:break-word;"><?= htmlspecialchars($rdf ?? ''); ?></pre>

                        <h3>Work</h3>
                        <?php pre($work, false); ?>
                        <h3>Expression</h3>
                        <?php pre($expression, false); ?>
                        <h3>Manifestation</h3>
                        <?php pre($manifestation, false); ?>
                        <h3>Analitic</h3>
                        <?php pre($analitic, false); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        var coverPanel = document.getElementById('coverPanel');
        if (coverPanel) {
            coverPanel.addEventListener('hidden.bs.offcanvas', function() {
                var coverImage = document.getElementById('itemCoverImage');
                if (coverImage) {
                    var coverUrl = new URL(coverImage.src, window.location.href);
                    coverUrl.searchParams.set('_cover_updated', Date.now());
                    coverImage.src = coverUrl.toString();
                }
            });
        }

        var updateItemData = document.getElementById('updateItemData');
        if (updateItemData) {
            updateItemData.addEventListener('click', function() {
                var button = this;
                var originalContent = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Atualizando...';

                fetch(button.dataset.updateUrl, {
                    method: 'GET',
                    credentials: 'same-origin'
                }).then(function(response) {
                    if (!response.ok) throw new Error('Não foi possível atualizar os dados.');
                    window.location.reload();
                }).catch(function(error) {
                    button.disabled = false;
                    button.innerHTML = originalContent;
                    alert(error.message);
                });
            });
        }

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
