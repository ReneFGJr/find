<script>
    document.addEventListener('DOMContentLoaded', function() {
        var coverPanel = document.getElementById('coverPanel');
        if (coverPanel) {
            coverPanel.addEventListener('hidden.bs.offcanvas', function() {
                location.reload();
            });
        }
        var z3950Panel = document.getElementById('z3950Panel');
        if (z3950Panel) {
            z3950Panel.addEventListener('hidden.bs.offcanvas', function() {
                location.reload();
            });
        }
    });
</script>
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

                <a href="<?= base_url('catalog/item/form') ?>?item=<?= $itemInfo['id_i']; ?>" class="btn btn-outline-warning" type="button">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Catalogar
                </a>

                <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#z3950Panel" aria-controls="z3950Panel">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Importar Z39.50
                </button>
                <!-- Painel lateral para Importar Z39.50 -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="z3950Panel" aria-labelledby="z3950PanelLabel" style="width:100%;max-width:700px;">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="z3950PanelLabel">Importar Z39.50</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
                    </div>
                    <div class="offcanvas-body">
                        <iframe src="<?= base_url('/catalog/import_z3950'); ?>?isbn=<?= $isbn; ?>" style="width:100%;height:70vh;border:0;"></iframe>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Fechar</button>
                        </div>
                    </div>
                </div>

                <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#marc21Panel" aria-controls="marc21Panel" disabled>
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Importar Marc21
                </button>
                <!-- Painel lateral para Importar Marc21 -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="marc21Panel" aria-labelledby="marc21PanelLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="marc21PanelLabel">Importar Marc21</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
                    </div>
                    <div class="offcanvas-body">
                        <iframe src="<?= base_url('/catalog/import_marc21'); ?>" style="width:100%;height:70vh;border:0;"></iframe>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Fechar</button>
                        </div>
                    </div>
                </div>

            </div>
            <!---------- Botões para ações adicionais ---------->
            <div class="mb-4 d-flex gap-2">
                <!-- Botão para Procurar Capa -->
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#coverPanel" aria-controls="coverPanel">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Procurar Capa
                </button>
                <!-- Botão para Verificar Dados -->
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#checkPanel" aria-controls="checkPanel">
                    <i class="bi bi-arrow-clockwise"></i> Verificar Dados
                </button>
                <!-- Botão para Editar Etiqueta -->
                <button class="btn btn-outline-warning" type="button" data-bs-toggle="offcanvas" data-bs-target="#labelEditPanel" aria-controls="labelEditPanel">
                    <i class="bi bi-pencil-square"></i> Editar Etiqueta
                </button>
                <!-- Offcanvas para Editar Etiqueta -->
                <?php include(APPPATH . 'Views/components/label_edit.php'); ?>
            </div>

            <!-- Offcanvas Panel -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="checkPanel" aria-labelledby="checkPanelLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="checkPanelLabel">Verificação de Dados</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
                </div>
                <div class="offcanvas-body">
                    <iframe src="<?= base_url('/catalog/check'); ?>" style="width:100%;height:70vh;border:0;"></iframe>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Fechar</button>
                    </div>
                </div>
            </div>

            <!-- Painel lateral para Procurar Capa -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="coverPanel" aria-labelledby="coverPanelLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="coverPanelLabel">Procurar Capa</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
                </div>
                <div class="offcanvas-body">
                    <iframe src="<?= base_url('/catalog/upload_cover'); ?>?isbn=<?= $isbn; ?>" style="width:100%;height:70vh;border:0;"></iframe>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Fechar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>