<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <h2>Editor RDF - Conceito #<?= htmlspecialchars($concept['id'] ?? '') ?></h2>
    <!----------------------- Work ----------------------->

    <?php if (!empty($Work)) : ?>
        <h3>Work</h3>
        <form method="get" action="">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Propriedade</th>
                        <th>Valor</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $lastGroup = null;
                foreach ($Work as $i => $w):
                        if (empty($w['c_class'])) continue;
                        if ($lastGroup !== $w['form_group']) {
                            echo '<tr class="table-secondary"><td colspan="3"><strong>' . htmlspecialchars($w['form_group']) . '</strong></td></tr>';
                            $lastGroup = $w['form_group'];
                        }
                    ?>
                        <tr>
                            <td>
                                <span title="<?= htmlspecialchars($w['c_class']) ?>">
                                    <?= htmlspecialchars($w['c_class']) ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($w['n_name'] ?? '') ?>
                                <?php if (!empty($w['n_lang'])): ?>
                                    <span class="badge bg-secondary ms-2"><?= htmlspecialchars($w['n_lang']) ?></span>
                                <?php endif; ?>
                                <?php pre($w,false); ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-success btn-sm" title="Adicionar"><i class="bi bi-plus"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">Salvar Work</button>
        </form>
    <?php endif; ?>

    <?php pre($Expression); ?>
    <?php pre($Manifestation); ?>

</div>
<?= $this->endSection() ?>