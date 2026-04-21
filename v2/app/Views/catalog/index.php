<?php include(APPPATH.'Views/layout/header.php'); ?>
<?php include(APPPATH.'Views/layout/navbar.php'); ?>
<?php include(APPPATH.'Views/components/catalog_breadcrumbs.php'); ?>
<div class="container my-5">
    <h1 class="mb-4"><i class="bi bi-journal-text me-2"></i>Catalogação</h1>
    <div class="row mb-4">
        <div class="col-md-8">
            <?php include(APPPATH.'Views/components/catalog_status.php'); ?>
        </div>
        <div class="col-md-4 d-flex flex-column gap-3">
            <a href="<?= base_url('/catalog/catalogar') ?>" class="btn btn-success btn-lg w-100"><i class="bi bi-plus-circle me-2"></i>Catalogar</a>
            <a href="<?= base_url('/catalog/etiquetas') ?>" class="btn btn-secondary btn-lg w-100"><i class="bi bi-printer me-2"></i>Impressão de Etiquetas</a>
        </div>
    </div>
</div>
<?php include(APPPATH.'Views/layout/footer.php'); ?>
