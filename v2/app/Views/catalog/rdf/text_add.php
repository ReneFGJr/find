<?= view('layout/header', ['title' => 'Cadastro da Literal • FIND']); ?>
<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <h1>Cadastro da Literal</h1>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert"><?= esc($error) ?></div>
            <?php endif; ?>
            <form method="post" action="<?= base_url('catalog/rdf/text_add') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="textValue" class="form-label">Valor da Literal</label>
                    <textarea class="form-control" id="textValue" rows="6" name="textValue" required><?= esc($textValue ?? '') ?></textarea>
                </div>
                <input type="hidden" name="idC" value="<?= esc($idC ?? '', 'attr') ?>">
                <input type="hidden" name="prop" value="<?= esc($prop ?? '', 'attr') ?>">
                <input type="hidden" name="formID" value="<?= esc($formID ?? '', 'attr') ?>">
                <button type="submit" class="btn btn-primary" name="action">Adicionar Literal</button>
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancelar</button>
                <hr>
                <?php require('language_radio.php'); ?>
            </form>
        </div>
    </div>
</div>
