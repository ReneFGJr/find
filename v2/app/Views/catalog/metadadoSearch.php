<?php include(APPPATH.'Views/layout/header.php'); ?>
<?php include(APPPATH.'Views/layout/navbar.php'); ?>
<?php include(APPPATH.'Views/components/catalog_breadcrumbs.php'); ?>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-search me-2"></i>Buscar Metadados (Status 4)</h2>
    <div class="card mb-4">
        <div class="card-body">
            <?php include(APPPATH.'Views/components/item_info.php'); ?>
            <form method="get" action="">
                <div class="mb-3">
                    <label for="busca" class="form-label">Buscar por título, autor ou ISBN</label>
                    <input type="text" class="form-control form-control-lg" id="busca" name="busca" placeholder="Digite o termo de busca...">
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Buscar</button>
            </form>
        </div>
    </div>
    <!-- Resultados da busca -->
    <?php if (isset($resultados) && is_array($resultados)): ?>
        <div class="card">
            <div class="card-header bg-info text-white"><i class="bi bi-list-ul me-2"></i>Resultados</div>
            <div class="card-body">
                <?php if (count($resultados) > 0): ?>
                    <ul class="list-group">
                        <?php foreach($resultados as $item): ?>
                            <li class="list-group-item">
                                <b><?= htmlspecialchars($item['titulo'] ?? '') ?></b><br>
                                <small>Autor: <?= htmlspecialchars($item['autor'] ?? '') ?> | ISBN: <?= htmlspecialchars($item['isbn'] ?? '') ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">Nenhum resultado encontrado.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include(APPPATH.'Views/layout/footer.php'); ?>
