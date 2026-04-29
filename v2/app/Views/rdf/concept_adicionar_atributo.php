<?php
// Página: Adicionar Atributo ao Conceito RDF
// Caminho: app/Views/rdf/concept_adicionar_atributo.php
?>
<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<div class="container mt-4">
    <h2>Adicionar Atributo ao Conceito RDF</h2>
    <form method="post" action="<?= site_url('rdf/concept/adicionar_atributo') ?>">
        <div class="mb-3">
            <label for="conceito" class="form-label">Conceito</label>
            <input type="text" class="form-control" id="conceito" name="conceito" required>
        </div>
        <div class="mb-3">
            <label for="atributo" class="form-label">Atributo</label>
            <input type="text" class="form-control" id="atributo" name="atributo" required>
        </div>
        <div class="mb-3">
            <label for="valor" class="form-label">Valor</label>
            <input type="text" class="form-control" id="valor" name="valor" required>
        </div>
        <button type="submit" class="btn btn-primary">Adicionar</button>
    </form>
</div>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>
