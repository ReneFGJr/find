<!-- components/label_edit.php -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="labelEditPanel" aria-labelledby="labelEditPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="labelEditPanelLabel">Edição de Etiqueta</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body">
        <div class="book-label mb-2 full">
            <form id="labelEditForm" novalidate class="d-flex flex-column gap-2">
                <input type="hidden" id="id_i" name="id_i" value="<?= $itemInfo['id_i'] ?? '' ?>">--<?= $itemInfo['id_i'] ?? '' ?>--
                <input id="ln1" name="i_ln1" class="form-control p-0" style="width: 80%;" placeholder="Classificação" value="<?= $itemInfo['i_ln1'] ?? '' ?>">
                <div class="input-group mb-2" style="width: 80%;">
                    <input id="ln2" name="i_ln2" class="form-control p-0" placeholder="Cutter" value="<?= $itemInfo['i_ln2'] ?? '' ?>">
                    <button type="button" class="btn btn-outline-primary" id="generateCutterBtn" title="Gerar Cutter">
                        <i class="bi bi-magic"></i> Cutter
                    </button>
                </div>
                <?php if (!empty($itemInfo['i_autores'])): ?>
                    <?php
                        $authors = explode(';', $itemInfo['i_autores']);
                        $authorNames = array_map('trim', $authors);
                        if (!empty($authorNames[0])) {
                            $author = $authorNames[0];
                        } else {
                            $author = '';
                        }
                    ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const cutterBtn = document.getElementById('generateCutterBtn');
                            if (cutterBtn) {
                                cutterBtn.addEventListener('click', function() {
                                    // Busca o nome do autor principal
                                    const ln2 = document.getElementById('ln2');
                                    let author = '';
                                    <?php if (!empty($author)): ?>
                                        author = <?= json_encode($author) ?>;
                                    <?php else: ?>
                                        // Tenta pegar do campo ln1 caso não tenha vindo do PHP
                                        author = document.getElementById('ln1').value;
                                    <?php endif; ?>
                                    if (!author) {
                                        alert('Autor não informado!');
                                        return;
                                    }
                                    fetch('<?= base_url('api/cutter');?>?name=' + encodeURIComponent(author))
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data && data.cutter_code) {
                                                ln2.value = data.cutter_code;
                                            } else {
                                                alert('Cutter não encontrado para o autor informado.');
                                            }
                                        })
                                        .catch(() => {
                                            alert('Erro ao buscar o Cutter.');
                                        });
                                });
                            }
                        });
                    </script>
                <?php else: ?>
                    <?php $author = ""; ?>
                    <input id="ln3" name="i_ln3" class="form-control p-0" style="width: 80%;" placeholder="Ano/Exemplar" value="<?= $itemInfo['i_ln2'] ?? '' ?>">
                <?php endif; ?>

                <input id="ln3" name="i_ln3" class="form-control p-0" style="width: 80%;" placeholder="Ano/Exemplar" value="<?= $itemInfo['i_ln3'] ?? '' ?>">
                <input id="ln4" name="i_ln4" class="form-control p-0" style="width: 80%;" placeholder="Ano/Exemplar" value="<?= $itemInfo['i_ln4'] ?? '' ?>">
                <button type="submit" class="btn btn-outline-success align-self-start mt-2">
                    <i class="bi bi-arrow-right-square"></i> Atualizar
                </button>
            </form>
            <div id="labelEditMsg" class="mt-2">Autor princial:<?=  $author; ?></div>
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
                                    setTimeout(function() {
                                        location.reload();
                                    }, 800);
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