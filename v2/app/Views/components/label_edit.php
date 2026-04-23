<!-- components/label_edit.php -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="labelEditPanel" aria-labelledby="labelEditPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="labelEditPanelLabel">Edição de Etiqueta</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body">
        <div class="book-label mb-2">
            <form id="labelEditForm" novalidate class="d-flex flex-column gap-2">
                <input type="hidden" id="id_i" name="id_i" value="<?= $itemInfo['id_i'] ?? '' ?>">--<?= $itemInfo['id_i'] ?? '' ?>--
                <input id="ln1" name="i_ln1" class="form-control p-0" style="width: 80%;" placeholder="Classificação" value="<?= $itemInfo['i_ln1'] ?? '' ?>">
                <input id="ln2" name="i_ln2" class="form-control p-0" style="width: 80%;" placeholder="Cutter" value="<?= $itemInfo['i_ln2'] ?? '' ?>">
                <input id="ln3" name="i_ln3" class="form-control p-0" style="width: 80%;" placeholder="Ano/Exemplar" value="<?= $itemInfo['i_ln3'] ?? '' ?>">
                <input id="ln4" name="i_ln4" class="form-control p-0" style="width: 80%;" placeholder="Ano/Exemplar" value="<?= $itemInfo['i_ln4'] ?? '' ?>">
                <button type="submit" class="btn btn-outline-success align-self-start mt-2">
                    <i class="bi bi-arrow-right-square"></i> Atualizar
                </button>
            </form>
            <div id="labelEditMsg" class="mt-2"></div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('labelEditForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(form);
                        fetch('<?= base_url('catalog/label/save') ?>', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    document.getElementById('labelEditMsg').innerHTML = '<div class="alert alert-success">' + (data.message || 'Salvo com sucesso!') + '</div>';
                                    setTimeout(function() { location.reload(); }, 800);
                                } else {
                                    document.getElementById('labelEditMsg').innerHTML = '<div class="alert alert-danger">' + (data.message || 'Erro ao salvar!') + '</div>';
                                }
                            })
                            .catch(() => {
                                document.getElementById('labelEditMsg').innerHTML = '<div class="alert alert-danger">Erro ao salvar!</div>';
                            });
                    });
                }
            });
        </script>
    </div>
</div>
</div>