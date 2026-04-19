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
                <form method="get" action="https://zeus.ufsc.br:8443/cgi-bin/wxis.exe/iah/?IsisScript=iah/iah.xis&base=livros&lang=p">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($itemInfo['i_identifier'] ?? '') ?>">
                    <button type="submit" class="btn btn-outline-success">
                        <i class="bi bi-cloud-download me-1"></i> Buscar no Z39.50 (Zeus-UFSC)
                    </button>
                </form>
                <form method="post" action="#">
                    <input type="hidden" name="import_marc21" value="1">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Importar Marc21
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>