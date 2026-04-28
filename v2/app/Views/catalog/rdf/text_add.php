<?= view('layout/header', ['title' => 'Cadastro da Literal • FIND']); ?>
<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <h1>Cadastro da Literal</h1>
            <form method="post" action="/catalog/rdf/text_add">
                <div class="mb-3">
                    <label for="textValue" class="form-label">Valor da Literal</label>
                    <textarea class="form-control" id="textValue" rows="6" name="textValue" required><?= isset($textValue) ? $textValue : ''; ?></textarea>
                </div>
                <input type="hidden" name="idC" value="<?= $idC; ?>">
                <input type="hidden" name="prop" value="<?= $prop; ?>">
                <input type="hidden" name="formID" value="<?= $formID; ?>">
                <button type="submit" class="btn btn-primary" name="action">Adicionar Literal</button>
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancelar</button>
                <hr>
                <?php require('language_radio.php'); ?>
            </form>
        </div>
    </div>
</div>

<?= $idC; ?>