<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>
<div class="container mt-4">

    <div class="row">
        <div class="col-md-12">
            <label for="tomboInput" class="form-label h4">Tombos para imprimir</label>
            <form novalidate class="row gy-2 gx-3 align-items-top" method="post" action="<?= base_url('/catalog/label/'); ?>">
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <textarea id="tomboID" name="tomboID" placeholder="Digite o tombo" class="form-control border border-secondary" style="width: 100%; height: 300px;"></textarea>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary full"><span>Enviar para Impressão</span></button>
                    <a href="<?= base_url('/catalog/label/P'); ?>" target="_blank" class="btn btn-danger full mt-2" <?php if ($nr_etiquetas == 0) echo 'disabled'; ?>><span><?= $nr_etiquetas; ?> etiquetas para imprimir</span></a>
                    <a href="<?= base_url('/catalog/label/Z'); ?>" class="btn btn-warning full mt-2"><span>Limpar etiquetas (para imprimir)</span></a>
                </div>
            </form>
            <hr>
            <!-- Show labels to print -->
            <div id="resulta">
                <?php if (isset($messages) && is_array($messages)): ?>
                    <div class="alert alert-info">
                        <?php if (isset($messages['msg'])): ?>
                            <strong><?= esc($messages['msg']) ?></strong><br>
                        <?php endif; ?>
                        <?php if (isset($messages['status'])): ?>
                            Status: <?= esc($messages['status']) ?><br>
                        <?php endif; ?>
                        <?php if (isset($messages['total'])): ?>
                            Total: <?= esc($messages['total']) ?><br>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-9 col-9 mt-5"></div>
        <div class="col-md-3 col-3 mt-5">
            <app-book-label-library>
                <div class="book-label mb-2"></div>
            </app-book-label-library>
            <app-tombo-show></app-tombo-show>
            <app-book-label></app-book-label>
            <app-book-status></app-book-status>
        </div>
    </div>
</div>

<?php include(APPPATH . 'Views/layout/footer.php'); ?>