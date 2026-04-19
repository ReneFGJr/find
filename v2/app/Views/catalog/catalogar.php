<?php include(APPPATH.'Views/layout/header.php'); ?>
<?php include(APPPATH.'Views/layout/navbar.php'); ?>
<?php include(APPPATH.'Views/components/catalog_breadcrumbs.php'); ?>
<?php
// v2/app/Views/catalog/catalogar.php
include(APPPATH.'Views/layout/header.php'); ?>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-journal-plus me-2"></i>Catalogar Livro</h2>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="card-title"><i class="bi bi-upc-scan me-2"></i>Livros com ISBN</h4>
                        <p class="card-text">Utilize esta opção para catalogar livros que possuem ISBN. O sistema buscará automaticamente os dados do livro pelo código informado.</p>
                    </div>
                    <a href="/catalog/catalogar/isbn" class="btn btn-primary mt-3 w-100">Catalogar com ISBN</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="card-title"><i class="bi bi-journal-x me-2"></i>Livros sem ISBN</h4>
                        <p class="card-text">Para livros que não possuem ISBN, utilize esta opção para inserir os dados manualmente.</p>
                    </div>
                    <a href="/catalog/catalogar/no_isbn" class="btn btn-secondary mt-3 w-100">Catalogar sem ISBN</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <?php include(APPPATH . 'Views/components/catalog_status.php'); ?>
        </div>
    </div>
</div>
<?php include(APPPATH.'Views/layout/footer.php'); ?>
