<?= view('layout/header', ['title' => 'Biblioteca selecionada • FIND']); ?>
<?= view('layout/navbar'); ?>

<style>
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
}
.cover-img {
    max-height: 200px;
    object-fit: contain;
    box-shadow: 2px 4px 12px rgba(0, 0, 0, 0.25);
    transition: transform 0.2s ease;
}
.cover-img:hover {
    transform: scale(1.05);
}
</style>

<main class="container py-5">

    <?php if (!empty($searchComponent)): ?>
        <div class="mb-4">
            <?= $searchComponent ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($vitrine)): ?>
    <div>
        <h2 class="h4 fw-bold mb-4"><?= !empty($isSearch) ? 'Resultados da busca' : 'Obras mais recentes' ?></h2>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            <?php foreach ($vitrine as $book): ?>
            <div class="col">
                <a href="<?= base_url('/item/' . esc($book['ID'], 'url')); ?>" class="text-decoration-none text-dark">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="p-2">
                        <img src="<?= esc($book['cover']); ?>"
                             alt="<?= esc($book['title']); ?>"
                             class="img-fluid rounded cover-img"
                             loading="lazy">
                    </div>
                    <div class="card-body p-2 pt-0">
                        <p class="card-text text-truncate-2 mb-0" style="font-size:0.7rem;" title="<?= esc($book['title']); ?>">
                            <?= esc($book['title']); ?>
                        </p>
                    </div>
                </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php elseif (!empty($isSearch)): ?>
        <section class="card border-0 shadow-sm text-center mx-auto" style="max-width:720px;" aria-labelledby="empty-search-title">
            <div class="card-body px-4 py-5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-primary mb-4" style="width:80px;height:80px;">
                    <i class="bi bi-search fs-1"></i>
                </div>
                <h2 id="empty-search-title" class="h4 fw-bold mb-3">Nenhuma obra encontrada</h2>
                <p class="text-muted mb-2">
                    Não encontramos resultados<?php if (!empty($searchTerm)): ?> para <strong>“<?= esc($searchTerm) ?>”</strong><?php endif; ?> nesta biblioteca.
                </p>
                <p class="text-muted mb-4">Verifique a escrita, tente usar menos palavras ou escolha outro local do acervo.</p>
                <a href="<?= base_url('/library') ?>" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i>Voltar para a biblioteca
                </a>
            </div>
        </section>
    <?php endif; ?>
</main>

<?= view('layout/footer'); ?>
