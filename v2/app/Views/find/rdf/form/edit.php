<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Editor de Estrutura de Formulário RDF</h2>
        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNovaPropriedade" aria-controls="offcanvasNovaPropriedade">
            <i class="bi bi-plus-circle"></i> Nova Propriedade
        </button>
    </div>
    <!-- Offcanvas: Nova Propriedade -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNovaPropriedade" aria-labelledby="offcanvasNovaPropriedadeLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNovaPropriedadeLabel">Nova Propriedade</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="novaPropriedadeForm">
                <input type="hidden" id="id_form" name="id_form" value="">
                <div class="mb-3">
                    <label for="form_group" class="form-label">Grupo</label>
                    <input type="text" class="form-control" id="form_group" name="form_group">
                </div>
                <div class="mb-3">
                    <label for="form_frbr" class="form-label">FRBR</label>
                    <select class="form-select" id="form_frbr" name="form_frbr" required>
                        <option value="">Selecione...</option>
                        <option value="W">Work</option>
                        <option value="E">Expression</option>
                        <option value="M">Manifestation</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="form_property" class="form-label">Propriedade</label>
                    <select class="form-select" id="form_property" name="form_property" required>
                        <option value="">Selecione...</option>
                        <?php if (isset($allProperties) && is_array($allProperties)): ?>
                            <?php foreach ($allProperties as $prop): ?>
                                <option value="<?= htmlspecialchars($prop['id_c']) ?>">
                                    <?= htmlspecialchars($prop['c_class']) ?> (<?= htmlspecialchars($prop['id_c']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="form_range" class="form-label">Range (IDs separados por vírgula)</label>
                    <input type="text" class="form-control" id="form_range" name="form_range">
                </div>
                <div class="mb-3">
                    <label for="form_group_subgroup" class="form-label">Subgrupo</label>
                    <input type="text" class="form-control" id="form_group_subgroup" name="form_group_subgroup">
                </div>
                <div class="mb-3">
                    <label for="form_library" class="form-label">Biblioteca</label>
                    <select class="form-select" id="form_library" name="form_library">
                        <option value="1000">Global</option>
                        <option value="1001">Biblioteca 1001</option>
                        <option value="1002">Biblioteca 1002</option>
                        <!-- Adicione mais opções conforme necessário -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="form_order" class="form-label">Ordem</label>
                    <select class="form-select" id="form_order" name="form_order">
                        <option value="">Selecione...</option>
                        <?php for ($i = 1; $i <= 99; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
            </form>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('novaPropriedadeForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(form);
                fetch('/index.php/rdf/form/salvar', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (form.querySelector('#id_form').value) {
                            alert('Propriedade atualizada com sucesso!');
                        } else {
                            alert('Nova propriedade salva com sucesso!');
                        }
                        var offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvasNovaPropriedade'));
                        offcanvas.hide();
                        form.reset();
                        location.reload();
                    } else {
                        alert('Erro ao salvar: ' + (data.message || 'Erro desconhecido.'));
                    }
                })
                .catch(() => {
                    alert('Erro ao salvar: falha na requisição.');
                });
            });
        }

        // Edição: ao clicar em editar, preenche o painel e abre
        document.querySelectorAll('.btn-editar-propriedade').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var tr = this.closest('tr');
                if (!tr) return;
                form.querySelector('#id_form').value = id;
                form.querySelector('#form_group').value = tr.children[3].textContent.trim();
                form.querySelector('#form_frbr').value = tr.getAttribute('data-frbr');
                // Propriedade (seleciona pelo value, não pelo texto)
                var propSelect = form.querySelector('#form_property');
                if (propSelect) propSelect.value = tr.children[1].getAttribute('data-prop-id') || '';
                form.querySelector('#form_range').value = tr.children[2].textContent.trim();
                form.querySelector('#form_group_subgroup').value = tr.children[4].textContent.trim();
                form.querySelector('#form_library').value = tr.children[5].textContent.trim();
                form.querySelector('#form_order').value = tr.children[6].textContent.trim();
                var offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvasNovaPropriedade'));
                offcanvas.show();
            });
        });

        // Limpar id_form ao abrir para novo
        var btnNova = document.querySelector('[data-bs-target="#offcanvasNovaPropriedade"]');
        if (btnNova) {
            btnNova.addEventListener('click', function() {
                if (form.querySelector('#id_form')) {
                    form.querySelector('#id_form').value = '';
                }
            });
        }
        // Exclusão: garantir binding mesmo após reload/renderização
        function bindDeleteButtons() {
            document.querySelectorAll('.btn-deletar-propriedade').forEach(function(btn) {
                btn.onclick = function() {
                    var id = this.getAttribute('data-id');
                    if (!id) return;
                    if (!confirm('Tem certeza que deseja excluir este registro? Esta ação não poderá ser desfeita.')) return;
                    fetch('/index.php/rdf/form/excluir', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id=' + encodeURIComponent(id)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao excluir: ' + (data.message || 'Erro desconhecido.'));
                        }
                    })
                    .catch(() => {
                        alert('Erro ao excluir: falha na requisição.');
                    });
                }
            });
        }
        bindDeleteButtons();
    });
    </script>
    <?php if (!empty($form)): ?>
        <?php
        // Agrupar por form_frbr
        $groups = [];
        foreach ($form as $f) {
            $groups[$f['form_frbr']][] = $f;
        }
        ?>
        <?php foreach ($groups as $frbr => $fields): ?>
            <h4 class="mt-4">Grupo: <?= htmlspecialchars($frbr) ?></h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Propriedade</th>
                            <th>Range</th>
                            <th>Grupo</th>
                            <th>Subgrupo</th>
                            <th>Biblioteca</th>
                            <th>Ordem</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($fields as $f): ?>
                            <tr data-frbr="<?= htmlspecialchars($f['form_frbr'] ?? '') ?>">
                            <td><?= htmlspecialchars($f['id_form'] ?? '') ?></td>
                            <td data-prop-id="<?= htmlspecialchars($f['form_property'] ?? '') ?>">
                                <?php
                                $pid = $f['form_property'] ?? '';
                                if ($pid !== '' && isset($propertyNames[$pid])) {
                                    echo htmlspecialchars($propertyNames[$pid]);
                                } else {
                                    echo htmlspecialchars($pid);
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $ranges = $f['form_range'] ?? '';
                                if (is_string($ranges)) {
                                    $ranges = trim($ranges, '[]');
                                    $ranges = $ranges ? explode(',', $ranges) : [];
                                }
                                if (!empty($ranges)) {
                                    foreach ($ranges as $rid) {
                                        $rid = trim($rid);
                                        if ($rid !== '') {
                                            if (isset($classNames[$rid])) {
                                                echo '<span class="badge bg-info text-dark me-1">' . htmlspecialchars($classNames[$rid]) . '</span>';
                                            } else {
                                                echo '<span class="badge bg-info text-dark me-1">' . htmlspecialchars($rid) . '</span>';
                                            }
                                        }
                                    }
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($f['form_group'] ?? '') ?></td>
                            <td><?= htmlspecialchars($f['form_group_subgroup'] ?? '') ?></td>
                            <td><?= htmlspecialchars($f['form_library'] ?? '') ?></td>
                            <td><?= htmlspecialchars($f['form_order'] ?? '') ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-warning btn-editar-propriedade" data-id="<?= htmlspecialchars($f['id_form'] ?? '') ?>" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-deletar-propriedade ms-1" data-id="<?= htmlspecialchars($f['id_form'] ?? '') ?>" title="Deletar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">Nenhum campo de formulário RDF encontrado.</div>
    <?php endif; ?>
</div>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>