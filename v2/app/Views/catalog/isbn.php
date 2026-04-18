<?php include(APPPATH.'Views/layout/header.php'); ?>
<?php include(APPPATH.'Views/layout/navbar.php'); ?>
<?php include(APPPATH.'Views/components/catalog_breadcrumbs.php'); ?>
<?php
include(APPPATH.'Views/layout/header.php'); ?>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-upc-scan me-2"></i>Catalogar Livro com ISBN</h2>
    <div class="row">
        <div class="col-md-6">
            <?php if (!empty($msg)) { echo $msg; } ?>
            <form method="post" action="">
                <div class="mb-3">
                    <label for="isbn" class="form-label">Informe o ISBN do livro</label>
                    <input type="text" class="form-control form-control-lg" id="isbn" name="isbn" placeholder="Digite o ISBN" required autofocus value="<?= htmlspecialchars($isbn ?? '') ?>">
                </div>
                <div class="mb-3 border rounded p-3 bg-light">
                    <label class="form-label mb-2"><b>Número do patrimônio</b></label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="auto_gerar" name="auto_gerar" value="1" <?= (!isset($auto_gerar) || $auto_gerar) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="auto_gerar">Gerar automaticamente</label>
                    </div>
                    <input type="text" class="form-control" id="patrimonio" name="patrimonio" placeholder="Definir manualmente" value="<?= htmlspecialchars($patrimonio ?? '') ?>" <?= (isset($auto_gerar) && $auto_gerar) ? 'readonly' : '' ?> >
                </div>
                <div class="mb-3">
                    <label for="local" class="form-label">Local de catalogação</label>
                    <select class="form-select" id="local" name="local" required>
                        <option value="">Selecione o local</option>
                        <?php if (!empty($places)) : foreach($places as $p) : ?>
                            <option value="<?= $p['id_lp'] ?>" <?= (isset($local) && $local == $p['id_lp']) ? 'selected' : '' ?>><?= htmlspecialchars($p['lp_name']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-search me-2"></i>Buscar Livro</button>
            </form>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-info-circle me-2"></i>Status da Catalogação
                </div>
                <div class="card-body">
                    <?php if (isset($item) && $item) : ?>
                        <h5 class="card-title">Livro encontrado</h5>
                        <p><b>Título:</b> <?= htmlspecialchars(isset($item['i_titulo']) && $item['i_titulo'] !== null ? $item['i_titulo'] : '') ?></p>
                        <p><b>ISBN:</b> <?= htmlspecialchars(isset($item['i_identifier']) && $item['i_identifier'] !== null ? $item['i_identifier'] : '') ?></p>
                        <p><b>Número do Tombo:</b> <?= htmlspecialchars(isset($item['i_tombo']) && $item['i_tombo'] !== null ? $item['i_tombo'] : '') ?></p>
                        <p><b>Exemplar:</b> <?= htmlspecialchars(isset($item['i_exemplar']) && $item['i_exemplar'] !== null ? $item['i_exemplar'] : '') ?></p>
                    <?php else: ?>
                        <p>Nenhuma catalogação realizada ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php include(APPPATH.'Views/components/catalog_status.php'); ?>
        </div>
    </div>
</div>
<?php include(APPPATH.'Views/layout/footer.php'); ?>
