<?= view('layout/header', ['title' => 'Utilitários • FIND']) ?>
<?= view('layout/navbar') ?>

<main class="container py-5">
    <h1 class="h3 fw-bold mb-2"><i class="bi bi-tools me-2"></i>Utilitários</h1>
    <p class="text-secondary mb-4">Ferramentas e utilidades para catalogação.</p>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <!-- Card de Reindexação -->
        <div class="col">
            <button type="button" class="text-decoration-none btn p-0 w-100" onclick="openReindexPanel()">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-arrow-repeat fs-3 text-danger"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">Reindexação</h5>
                            <p class="card-text small text-secondary mb-0">Atualizar índices de busca dos itens</p>
                        </div>
                    </div>
                </div>
            </button>
        </div>
    </div>
    <!-- Painel lateral para reindexação -->
    <div id="reindexPanel" style="position:fixed;top:0;right:-600px;width:600px;max-width:100vw;height:100vh;z-index:1050;background:#fff;box-shadow:-2px 0 16px rgba(0,0,0,0.2);transition:right 0.3s;overflow:auto;">
        <div class="d-flex align-items-center p-3 border-bottom">
            <h5 class="mb-0 flex-grow-1 text-start">Reindexação</h5>
            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="closeReindexPanel()">&times;</button>
        </div>
        <iframe id="reindexIframe" src="" style="width:100%;height:calc(100vh - 56px);border:0;"></iframe>
    </div>
</main>

<script>
function openReindexPanel() {
    document.getElementById('reindexPanel').style.right = '0';
    document.getElementById('reindexIframe').src = "<?= base_url('catalog/reindex') ?>";
}
function closeReindexPanel() {
    document.getElementById('reindexPanel').style.right = '-600px';
    document.getElementById('reindexIframe').src = "";
}
</script>

<?= view('layout/footer') ?>
