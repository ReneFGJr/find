<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-search me-2"></i>Buscar Metadados (Status 4)</h2>
    <div class="row">
        <div class="col-md-3 mb-4">
            <?php include(APPPATH . 'Views/components/status_actions.php'); ?>
        </div>
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-body">
                    <?php include(APPPATH . 'Views/components/item_info.php'); ?>
                </div>
            </div>


            <!-- Botoes de ação -->
            <div class="mb-4 d-flex gap-2">
                <form method="post" action="#">
                    <input type="hidden" name="import_z39_50" value="1">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Importar Z39.50
                    </button>
                </form>
                <form method="post" action="#">
                    <input type="hidden" name="import_marc21" value="1">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Importar Marc21
                    </button>
                </form>
            </div>

            <?php if (!empty($z3950_result)): ?>
                <div class="alert alert-success">
                    <h5 class="mb-2"><i class="bi bi-cloud-download me-2"></i>Resultado da consulta Z39.50</h5>
                    <pre class="bg-light p-2 border rounded small" style="max-height:300px;overflow:auto;">
                    <?= htmlspecialchars(print_r($z3950_result, true)) ?>
                    </pre>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>