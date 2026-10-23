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
            <?php include(APPPATH . 'Views/components/status_list.php'); ?>
        </div>
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-body">
                    <?php include(APPPATH . 'Views/components/item_info.php'); ?>
                </div>
            </div>


            <?php
            $semWork = (int)($itemInfo['i_work'] ?? 0) === 0 && (int)($itemInfo['i_expression'] ?? 0) === 0;
            $isbnProcessavel = strpos((string)($itemInfo['i_identifier'] ?? ''), '100') === 0;
            ?>
            <!-- Botoes de ação -->
            <div class="mb-4 d-flex gap-2">

                <?php if ($semWork && $isbnProcessavel): ?>
                    <a href="<?= base_url('catalog/catalogar/processar_obra/' . (int)$itemInfo['id_i']) ?>" class="btn btn-outline-success" type="button">
                        <i class="bi bi-gear me-1"></i> Processar obra
                    </a>
               <?php endif; ?>
                    <a href="<?= base_url('catalog/item/form') ?>?item=<?= $itemInfo['id_i']; ?>"
                        class="btn btn-outline-warning<?= $semWork ? ' disabled' : '' ?>"
                        <?= $semWork ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
                        <i class="bi bi-file-earmark-arrow-down me-1"></i>
                        <?= empty(trim((string)($itemInfo['i_titulo'] ?? ''))) ? 'Cadastrar obra' : 'Catalogar' ?>
                    </a>
                    <!------------------------ Importar Z39.50 ------------------------>
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
                            <div class="alert alert-secondary small text-break">
                                <strong>Endpoint da consulta (provisório):</strong>
                                <code class="d-block mt-1"><?= esc(\App\Models\Z3950\Index::consultationEndpoint((string) ($isbn ?? ''))) ?></code>
                            </div>
                            <div id="z3950Loading" class="d-none text-center py-5" role="status" aria-live="polite">
                                <div class="spinner-border text-primary mb-3" aria-hidden="true"></div>
                                <p class="fw-semibold mb-1">Consultando e importando metadados Z39.50...</p>
                                <p class="text-muted">Aguarde. Tempo decorrido: <span id="z3950Elapsed">0</span> s</p>
                            </div>
                            <iframe id="z3950Iframe" title="Resultado da importação Z39.50" style="width:100%;height:70vh;border:0;"></iframe>
                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Fechar</button>
                            </div>
                        </div>
                    </div>

                    <!------------------------ Importar MARC21 ------------------------>
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#marc21Panel" aria-controls="marc21Panel" <?= $semWork ? 'disabled' : '' ?>>
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Importar MARC21
                    </button>
                    <!-- Painel lateral para Importar MARC21 -->
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="marc21Panel" aria-labelledby="marc21PanelLabel" style="width:100%;max-width:700px;">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="marc21PanelLabel">Importar MARC21</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
                        </div>
                        <div class="offcanvas-body">
                            <iframe id="marc21Iframe" style="width:100%;height:70vh;border:0;"></iframe>
                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Fechar</button>
                            </div>
                        </div>
                    </div>
            </div>
            <!---------- Botões para ações adicionais ---------->
            <div class="mb-4 d-flex gap-2">
                <!-- Botão para Procurar Capa -->
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#coverPanel" aria-controls="coverPanel" <?= $semWork ? 'disabled' : '' ?>>
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Procurar Capa
                </button>
                <!-- Botão para Verificar Dados -->
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#checkPanel" aria-controls="checkPanel" <?= $semWork ? 'disabled' : '' ?>>
                    <i class="bi bi-arrow-clockwise"></i> Verificar Dados
                </button>
                <!-- Botão para Editar Etiqueta -->
                <button class="btn btn-outline-warning" type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#labelEditPanel"
                    aria-controls="labelEditPanel"
                    <?= $semWork ? 'disabled' : '' ?>
                    style="display: <?php if ($itemInfo['i_titulo'] == '') {
                                        echo 'none';
                                    } else {
                                        echo 'block';
                                    } ?>;">
                    <i class="bi bi-pencil-square"></i> Editar Etiqueta
                </button>

                <!-- Offcanvas para Editar Etiqueta -->
                <?php include(APPPATH . 'Views/components/label_edit.php'); ?>
            </div>

            <!-- Offcanvas Panel -->

            <!------------------------ Verificação de Dados ------------------------>
            <!-- Painel lateral para Verificação de Dados -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="checkPanel" aria-labelledby="checkPanelLabel" style="width:100%;max-width:700px;">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="checkPanelLabel">Verificação de Dados</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
                </div>
                <div class="offcanvas-body">
                    <iframe id="checkIframe" src="" style="width:100%;height:70vh;border:0;"></iframe>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var coverPanel = document.getElementById('coverPanel');
        if (coverPanel) {
            coverPanel.addEventListener('hidden.bs.offcanvas', function() {
                location.reload();
            });
        }

        var checkPanel = document.getElementById('checkPanel');
        var checkIframe = document.getElementById('checkIframe');
        var checkUrl = "<?= base_url('/catalog/check'); ?>?isbn=<?= $isbn; ?>";

        if (checkPanel && checkIframe) {
            checkPanel.addEventListener('show.bs.offcanvas', function() {
                checkIframe.src = checkUrl;
            });
            checkPanel.addEventListener('hidden.bs.offcanvas', function() {
                checkIframe.src = '';
                location.reload();
            });
        }

        var marc21Panel = document.getElementById('marc21Panel');
        var marc21Iframe = document.getElementById('marc21Iframe');
        var marc21Url = "<?= base_url('/catalog/import_marc21'); ?>?isbn=<?= $isbn; ?>";
        if (marc21Panel && marc21Iframe) {
            marc21Panel.addEventListener('show.bs.offcanvas', function() {
                marc21Iframe.src = marc21Url;
            });
            marc21Panel.addEventListener('hidden.bs.offcanvas', function() {
                marc21Iframe.src = '';
                location.reload();
            });
        }

        var z3950Panel = document.getElementById('z3950Panel');
        var z3950Iframe = document.getElementById('z3950Iframe');
        var z3950Url = <?= json_encode(base_url('/catalog/import_z3950') . '?isbn=' . rawurlencode((string) ($isbn ?? '')), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        if (z3950Panel && z3950Iframe) {
            var z3950Loading = document.getElementById('z3950Loading');
            var z3950Elapsed = document.getElementById('z3950Elapsed');
            var z3950Timer;
            function finishZ3950Loading() {
                clearInterval(z3950Timer);
                z3950Loading.classList.add('d-none');
                z3950Iframe.classList.remove('d-none');
                z3950Panel.setAttribute('aria-busy', 'false');
            }
            z3950Iframe.addEventListener('load', function() {
                if (z3950Iframe.getAttribute('src')) finishZ3950Loading();
            });
            z3950Panel.addEventListener('show.bs.offcanvas', function() {
                clearInterval(z3950Timer);
                z3950Loading.classList.remove('d-none');
                z3950Iframe.classList.add('d-none');
                z3950Panel.setAttribute('aria-busy', 'true');
                var startedAt = Date.now();
                z3950Elapsed.textContent = '0';
                z3950Timer = setInterval(function() {
                    z3950Elapsed.textContent = Math.floor((Date.now() - startedAt) / 1000);
                }, 1000);
                z3950Iframe.src = z3950Url;
            });
            z3950Panel.addEventListener('hidden.bs.offcanvas', function() {
                clearInterval(z3950Timer);
                z3950Iframe.src = '';
                location.reload();
            });
        }
    });
</script>
