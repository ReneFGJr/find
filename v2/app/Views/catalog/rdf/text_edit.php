<?= view('layout/header', ['title' => 'Cadastro da Literal • FIND']); ?>
<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <h1>Editar Literal</h1>
            <form method="post" action="/catalog/rdf/text_edit">
                <div class="mb-3">
                    <label for="textValue" class="form-label">Valor da Literal</label>
                    <textarea class="form-control" id="textValue" rows="6" name="textValue" required><?= isset($textValue) ? $textValue : ''; ?></textarea>
                </div>
                <input type="hidden" name="idD" value="<?= $idD; ?>">
                <input type="hidden" name="idN" value="<?= $id_n; ?>">
                <button type="submit" class="btn btn-primary" name="action" value="save">Salvar Alterações</button>
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancelar</button>
                <hr>
                <?php require('language_radio.php'); ?>
            </form>
        </div>
    </div>
</div>
