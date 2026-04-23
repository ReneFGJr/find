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
        <h2 class="h4 fw-bold mb-4">Obras mais recentes</h2>
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
    <?php endif; ?>
</main>

<?= view('layout/footer'); ?>
