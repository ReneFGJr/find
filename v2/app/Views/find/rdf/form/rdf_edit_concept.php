<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>

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
                                    <span class="badge bg-secondary ms-1"><?= htmlspecialchars($w['n_lang']) ?></span>
                                <?php endif; ?>
                                <?php pre($w, false); ?>
                            </td>
                            <td class="text-center">
                                <nobr>

                                    <?php switch ($w['n_type']) {
                                        case 'TEXT': ?>
                                            <button type="button" class="btn btn-outline-success btn-sm" title="Adicionar"><i class="bi bi-plus"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-1 btn-editar-literal"
                                                id="btn-editar-literal-<?= htmlspecialchars($w['id_n'] ?? '') ?>"
                                                data-idn="<?= htmlspecialchars($w['id_n'] ?? '') ?>"
                                                data-value="<?= htmlspecialchars($w['n_name'] ?? '') ?>"
                                                title="Editar texto">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        <?php break;
                                        case 'CONCEPT': ?>
                                            <button type="button" class="btn btn-outline-success btn-sm" title="Adicionar"><i class="bi bi-plus"></i></button>
                                    <?php break;
                                    } ?>
                                </nobr>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">Salvar Work</button>
        </form>
    <?php endif; ?>

</div>

<!-- Offcanvas (painel lateral) para editar literal - FORA do loop -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditarLiteral" aria-labelledby="offcanvasEditarLiteralLabel" style="width:400px;max-width:100vw;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasEditarLiteralLabel">Editar valor literal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="formEditarLiteral">
            <input type="hidden" id="edit-idn" name="id_n" value="">
            <div class="mb-3">
                <label for="edit-n-value" class="form-label">Valor literal</label>
                <textarea class="form-control" id="edit-n-value" name="n_name" rows="5"></textarea>
            </div>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarLiteral">Salvar</button>
            </div>
        </form>
    </div>
</div>

<?php include(APPPATH . 'Views/layout/footer.php'); ?>

<script>
    // Garante que o Bootstrap está disponível
    window.addEventListener('DOMContentLoaded', function() {
        // Abrir painel lateral ao clicar em editar
        document.querySelectorAll('.btn-editar-literal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var idn = this.getAttribute('data-idn');
                var value = this.getAttribute('data-value');
                document.getElementById('edit-idn').value = idn;
                document.getElementById('edit-n-value').value = value;
                if (window.bootstrap && bootstrap.Offcanvas) {
                    var offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvasEditarLiteral'));
                    offcanvas.show();
                } else {
                    alert('Bootstrap JS não carregado!');
                }
            });
        });
        // Salvar literal via AJAX
        var btnSalvar = document.getElementById('btnSalvarLiteral');
        if (btnSalvar) {
            btnSalvar.onclick = function() {
                var form = document.getElementById('formEditarLiteral');
                var idn = form.querySelector('#edit-idn').value;
                var value = form.querySelector('#edit-n-value').value;
                fetch('/rdf/concept/salvar_literal', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id_n: idn,
                            n_name: value
                        })
                    })
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao salvar: ' + (data.message || 'Erro desconhecido.'));
                        }
                    })
                    .catch(() => alert('Erro ao salvar: falha na requisição.'));
            };
        }
    });
</script>
</div>