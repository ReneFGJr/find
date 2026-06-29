<style>
    .item-cover {
        max-height: 380px;
        object-fit: contain;
        box-shadow: 2px 4px 12px rgba(0, 0, 0, 0.25);
        transition: transform 0.2s ease;
    }

    .item-cover:hover {
        transform: scale(1.05);
    }
</style>


<main class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/library'); ?>">Biblioteca</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($book['title'] ?? ''); ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Capa -->
        <div class="col-md-3 text-center">
            <img src="<?= esc($book['cover'] ?? ''); ?>"
                alt="<?= esc($book['title'] ?? ''); ?>"
                class="img-fluid rounded item-cover mb-2">
            <?php if ($perfil[2] == 1) { ?>
                <a href="<?= base_url('catalog/item/form?item=' .$id); ?>" class="btn btn-primary w-100">
                    <i class="bi bi-journal-check me-1"></i> Catalogar
                </a>
            <?php } ?>
        </div>

        <!-- Detalhes -->
        <div class="col-md-9">

            <div class="d-flex align-items-center mb-3">
                <h1 class="h3 fw-bold mb-0 me-2"><?= esc($book['title'] ?? ''); ?></h1>
                <button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#itemInfoPanel" aria-controls="itemInfoPanel" title="Mais informações">
                    <i class="bi bi-info-circle"></i>
                </button>
            </div>

            <!-- Painel lateral de informações do item -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="itemInfoPanel" aria-labelledby="itemInfoPanelLabel" style="width:800px;max-width:100vw;">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="itemInfoPanelLabel">Informações do Item</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
                </div>
                <div class="offcanvas-body">
                    <?= view('components/item_info', ['$itemInfo' => $book]); ?>
                    <?= view('components/item_meta', ['$meta' => $meta]); ?>
                    <?= view('components/item_meta_book', ['$meta' => $book['meta'] ?? []]); ?>
                </div>
            </div>

            <table class="table table-sm table-borderless mb-4" style="max-width:100%;">
                <tr>
                    <th width="140px"></th>
                    <th width="400px"></th>
                    <th width="140px"></th>
                    <th></th>
                </tr>
                <?php if (!empty($book['meta']['Authors'])): ?>
                    <tr>
                        <th class="text-secondary" style="width:140px;">Autor(es)</th>
                        <td colspan="3">
                            <?php foreach ($book['meta']['Authors'] as $a): ?>
                                <span class="badge bg-light text-dark me-1"><?= esc($a['name']); ?></span>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if (!empty($book['meta']['Publisher'])): ?>
                    <tr>
                        <th class="text-secondary">Editora</th>
                        <td colspan="1">
                            <?php foreach ($book['meta']['Publisher'] as $p): ?>
                                <?= esc($p['name']); ?>
                            <?php endforeach; ?>
                        </td>

                        <th class="text-secondary">Local</th>
                        <td>
                            <?php foreach (($book['meta']['PublisherPlace'] ?? $book['meta']['Place'] ?? []) as $p): ?>
                                <?= esc($p['name']); ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <?php if (!empty($book['isbn'])): ?>

                        <th class="text-secondary">ISBN</th>
                        <td><?= esc($book['isbn']); ?></td>
                    <?php endif; ?>

                    <?php if (!empty($book['meta']['Page'])): ?>
                        <th class="text-secondary">Páginas</th>
                        <td>
                            <?php foreach ($book['meta']['Page'] as $p): ?>
                                <?= esc($p['name']); ?>
                            <?php endforeach; ?>
                        </td>
                    <?php endif; ?>
                </tr>

                <tr>
                    <th class="text-secondary">Idioma</th>
                    <td colspan="3">
                        <?php echo view('components/item_subject', ['book' => $book]); ?>
                    </td>
                </tr>

                <?php if (!empty($book['meta']['Langage'])): ?>
                    <tr>
                        <th class="text-secondary">Expressão</th>
                        <td colspan="3">
                            <?= view('components/item_expression', ['meta' => $book['meta'] ?? []]); ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <!--  Classification -->
                <tr>


                    <?php if (!empty($book['meta']['CDU'])): ?>
                <tr>
                    <th class="text-secondary">CDU</th>
                    <td>
                        <?php foreach ($book['meta']['CDU'] as $c): ?>
                            <?= esc($c['name']); ?>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endif; ?>

            <tr>
                <th class="text-secondary">Classificação</th>
                <!-- Color Classification -->
                <?php if (!empty($book['meta']['ColorClassification'])) { ?>
                    <td width="300px;">
                        <div class="p-1" style="border:2px solid #ddd;">
                            <?= view('components/item_color_classification', ['meta' => $book['meta'] ?? []]); ?>
                        </div>
                    </td>
                <?php } ?>
                <?php if (!empty($book['meta']['CDD'])) { ?>
                    <td><span class="small">CDD:</span><?= view('components/item_cdd', ['meta' => $book['meta'] ?? []]); ?></td>
                <?php } ?>
                <?php if (!empty($book['meta']['CDU'])) { ?>
                    <td><span class="small">CDU:</span><?= view('components/item_cdd', ['meta' => $book['meta'] ?? []]); ?></td>
                <?php } ?>
            </tr>

            <!-- Description -->
            <?php if (!empty($book['meta']['Description'])) { ?>
                <tr>
                    <td class="text-secondary">Descrição</td>
                    <td colspan="3" class="small"><?= view('components/item_description', ['meta' => $book['meta'] ?? []]); ?></td>
                </tr>
            <?php } ?>

            </table>

            <!-- Exemplares -->
            <?php if (!empty($book['items'])): ?>
                <h2 class=" h5 fw-bold mb-3">Exemplares</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tombo</th>
                                <th>Exemplar</th>
                                <th>Localização</th>
                                <th>Local</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $itemCount = 0; ?>
                            <?php foreach ($book['items'] as $ex): ?>
                                <tr>
                                    <td><?= esc(++$itemCount); ?></td>
                                    <td><?= esc($ex['tombo'] ?? ''); ?></td>
                                    <td><?= esc($ex['exemplar'] ?? ''); ?></td>
                                    <td><?= esc($ex['local'] ?? ''); ?></td>
                                    <td><?= esc($ex['place'] ?? ''); ?></td>
                                    <td>
                                        <?php
                                        $status = $ex['status'] ?? '';
                                        $badge = 'bg-secondary';
                                        if ($status === 'Disponível' || $status === 'Disponivel') $badge = 'bg-success';
                                        if (($ex['atrasado'] ?? 0) == 1) $badge = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge; ?>"><?= esc($status); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>