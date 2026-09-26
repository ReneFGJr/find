<?= view('layout/header', ['title' => 'Cadastro da Literal • FIND']); ?>
<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <h1>Editar Literal</h1>
            <form method="post" action="<?= base_url('/catalog/rdf/text_edit') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="textValue" class="form-label">Valor da Literal</label>
                    <textarea class="form-control" id="textValue" rows="6" name="textValue" required><?= esc($textValue ?? '') ?></textarea>
                </div>
                <input type="hidden" name="idD" value="<?= (int) $idD ?>">
                <input type="hidden" name="idN" value="<?= (int) $id_n ?>">
                <button type="submit" class="btn btn-primary" name="action" value="save">Salvar Alterações</button>
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancelar</button>
                <hr>
                <?php require('language_radio.php'); ?>
            </form>
        </div>
    </div>
</div>
