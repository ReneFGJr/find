<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>


<div class="container my-4">
    <h2>Editor RDF - Conceito #<?= htmlspecialchars($concept['id'] ?? '') ?></h2>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3" id="tabEditConcept" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-form-tab" data-bs-toggle="tab" data-bs-target="#tab-form" type="button" role="tab" aria-controls="tab-form" aria-selected="true">Formulário</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-data-tab" data-bs-toggle="tab" data-bs-target="#tab-data" type="button" role="tab" aria-controls="tab-data" aria-selected="false">Dados brutos</button>
        </li>
    </ul>
    <div class="tab-content" id="tabEditConceptContent">
        <div class="tab-pane fade show active" id="tab-form" role="tabpanel" aria-labelledby="tab-form-tab">
            <!-- Conteúdo do formulário original -->
            <?php if (!empty($Work)) : ?>
                <h3>Work</h3>
                <form method="get" action="">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="20%">Propriedade</th>
                                <th style="width:20px;"></th>
                                <th width="76%">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $lastGroup = null;
                            $propLabel = '';
                            foreach ($Work as $i => $w):
                                if (($w['n_type'] === 'CONCEPT') and ($w['n_name'] != '')) {
                                    $w['n_type'] = 'CONCEPT:EXIST';
                                }
                                if (empty($w['c_class'])) continue;
                                if ($lastGroup !== $w['form_group']) {
                                    echo '<tr class="table-secondary"><td colspan="3"><strong>' . htmlspecialchars($w['form_group']) . '</strong></td></tr>';
                                    $lastGroup = $w['form_group'];
                                }
                            ?>
                                <tr>
                                    <td>
                                        <?php
                                        $xpropLabel = $w['c_class'] ?? '';
                                        if ($xpropLabel !== $propLabel) {
                                            echo '<span title="'.htmlspecialchars($w['c_class']).'">';
                                            echo htmlspecialchars($w['c_class']);
                                            echo '</span>';
                                            $propLabel = $xpropLabel;
                                        }
                                        ?>
                                    </td>
                                    <!-- Botoes -->
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
                                                    <button type="button" class="btn btn-outline-success btn-sm btn-adicionar-atributo" title="Adicionar"
                                                        data-idc="<?= htmlspecialchars($id ?? '') ?>"
                                                        data-prop="<?= htmlspecialchars($w['c_class'] ?? '') ?>"
                                                        data-group="<?= htmlspecialchars($w['form_group'] ?? '') ?>"
                                                        data-type="<?= htmlspecialchars($w['n_type'] ?? '') ?>"
                                                        data-value="<?= htmlspecialchars($w['n_name'] ?? '') ?>"
                                                        data-range="<?= htmlspecialchars($w['form_range'] ?? '') ?>">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                <?php break;
                                                case 'CONCEPT:EXIST': ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm btn-remover-atributo" title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <?php break; ?>
                                            <?php } ?>
                                        </nobr>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($w['n_name'] ?? '') ?>
                                        <?php if (!empty($w['n_lang'])): ?>
                                            <span class="badge bg-secondary ms-1"><?= htmlspecialchars($w['n_lang']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Salvar Work</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade" id="tab-data" role="tabpanel" aria-labelledby="tab-data-tab">
            <h4>Dados brutos ($data)</h4>
            <pre style="max-height:400px;overflow:auto;"><?php echo htmlspecialchars(print_r($data, true)); ?></pre>
        </div>
    </div>
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


<!-- Offcanvas para adicionar atributo (fora do loop) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAdicionarAtributo" aria-labelledby="offcanvasAdicionarAtributoLabel" style="width:400px;max-width:100vw;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasAdicionarAtributoLabel">Adicionar atributo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php include(APPPATH . 'Views/find/rdf/form/rdf_concept_attribute.php'); ?>
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

        // Abrir painel de adicionar atributo ao clicar no botão de adicionar
        document.querySelectorAll('.btn-adicionar-atributo').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Passa dados do $w para o painel
                var nome = this.getAttribute('data-prop') || '';
                var valor = this.getAttribute('data-value') || '';
                var group = this.getAttribute('data-group') || '';
                var type = this.getAttribute('data-type') || '';
                var idc = this.getAttribute('data-idc') || '';
                var range = this.getAttribute('data-range') || '';

                // Preenche campos do formulário
                var nomeInput = document.getElementById('atributo-nome');
                var valorInput = document.getElementById('atributo-valor');
                var idcInput = document.getElementById('atributo-idc');
                if (nomeInput) nomeInput.value = nome;
                if (valorInput) valorInput.value = '';
                if (idcInput) idcInput.value = idc;
                // Passa o range corretamente para o campo e debug
                if (window.setAtributoRange) {
                    window.setAtributoRange(range);
                }
                // Mostra os dados recebidos no painel
                if (window.mostrarDebugAtributo) {
                    window.mostrarDebugAtributo({
                        idc: idc,
                        prop: nome,
                        group: group,
                        type: type,
                        value: valor,
                        range: range
                    });
                }
                var offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvasAdicionarAtributo'));
                offcanvas.show();
            });
        });
    });
</script>
</div>