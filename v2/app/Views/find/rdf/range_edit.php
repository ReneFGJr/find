<?php
// Página dedicada para edição de Range
include(APPPATH . 'Views/layout/header.php');
include(APPPATH . 'Views/layout/navbar.php');
include(APPPATH.'Views/components/catalog_breadcrumbs.php');

$id_form = isset($id_form) ? $id_form : ($_GET['id_form'] ?? '');
?>
<div class="container my-4">
    <h2>Editar Range <span class="text-secondary" style="font-size:0.9em;">(ID: <?= htmlspecialchars($id_form) ?>)</span></h2>
    <div class="row">
        <div class="col-md-6">
            <?php include(APPPATH . 'Views/components/range_edit_panel.php'); ?>
        </div>
        <div class="col-md-6">
            <iframe src="/rdf/range_id?id_form=<?= urlencode($id_form) ?>" width="100%" height="600" frameborder="0" style="border:1px solid #ccc;"></iframe>
        </div>
    </div>
</div>
<script>
// Chama o painel já com o id_form
if (window.setRangeIdForm) window.setRangeIdForm('<?= htmlspecialchars($id_form) ?>');
</script>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>
