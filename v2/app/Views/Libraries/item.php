<?= view('layout/header', ['title' => esc($book['title'] ?? 'Item') . ' • FIND']); ?>
<?= view('layout/navbar'); ?>

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
                 class="img-fluid rounded item-cover">
        </div>

        <!-- Detalhes -->
        <div class="col-md-9">
            <h1 class="h3 fw-bold mb-3"><?= esc($book['title'] ?? ''); ?></h1>

            <table class="table table-sm table-borderless mb-4" style="max-width:600px;">
                <?php if (!empty($book['meta']['Authors'])): ?>
                <tr>
                    <th class="text-secondary" style="width:140px;">Autor(es)</th>
                    <td>
                        <?php foreach ($book['meta']['Authors'] as $a): ?>
                            <span class="badge bg-light text-dark me-1"><?= esc($a['name']); ?></span>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endif; ?>

                <?php if (!empty($book['meta']['Publisher'])): ?>
                <tr>
                    <th class="text-secondary">Editora</th>
                    <td>
                        <?php foreach ($book['meta']['Publisher'] as $p): ?>
                            <?= esc($p['name']); ?>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endif; ?>

                <?php if (!empty($book['meta']['PublisherPlace']) || !empty($book['meta']['Place'])): ?>
                <tr>
                    <th class="text-secondary">Local</th>
                    <td>
                        <?php foreach (($book['meta']['PublisherPlace'] ?? $book['meta']['Place'] ?? []) as $p): ?>
                            <?= esc($p['name']); ?>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endif; ?>

                <?php if (!empty($book['isbn'])): ?>
                <tr>
                    <th class="text-secondary">ISBN</th>
                    <td><?= esc($book['isbn']); ?></td>
                </tr>
                <?php endif; ?>

                <?php if (!empty($book['meta']['Page'])): ?>
                <tr>
                    <th class="text-secondary">Páginas</th>
                    <td>
                        <?php foreach ($book['meta']['Page'] as $p): ?>
                            <?= esc($p['name']); ?>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endif; ?>

                <?php if (!empty($book['meta']['CDD'])): ?>
                <tr>
                    <th class="text-secondary">CDD</th>
                    <td>
                        <?php foreach ($book['meta']['CDD'] as $c): ?>
                            <?= esc($c['name']); ?>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endif; ?>

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

                <?php if (!empty($book['meta']['Subject'])): ?>
                <tr>
                    <th class="text-secondary">Assuntos</th>
                    <td>
                        <?php foreach ($book['meta']['Subject'] as $s): ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary me-1"><?= esc($s['name']); ?></span>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endif; ?>

                <?php if (!empty($book['meta']['Langage'])): ?>
                <tr>
                    <th class="text-secondary">Idioma</th>
                    <td>
                        <?php foreach ($book['meta']['Langage'] as $l): ?>
                            <?= esc($l['name']); ?>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>

            <!-- Exemplares -->
            <?php if (!empty($book['items'])): ?>
            <h2 class="h5 fw-bold mb-3">Exemplares</h2>
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

<?= view('layout/footer'); ?>
