<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Formulário do Item</h2>
    <ul class="nav nav-tabs" id="itemTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-item" data-bs-toggle="tab" data-bs-target="#tabItem" type="button" role="tab" aria-controls="tabItem" aria-selected="true">Item</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-work" data-bs-toggle="tab" data-bs-target="#tabWork" type="button" role="tab" aria-controls="tabWork" aria-selected="false">Work</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-expression" data-bs-toggle="tab" data-bs-target="#tabExpression" type="button" role="tab" aria-controls="tabExpression" aria-selected="false">Expression</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-manifestation" data-bs-toggle="tab" data-bs-target="#tabManifestation" type="button" role="tab" aria-controls="tabManifestation" aria-selected="false">Manifestation</button>
        </li>
    </ul>
    <div class="tab-content border border-top-0 p-4 bg-white" id="itemTabContent">
        <div class="tab-pane fade show active" id="tabItem" role="tabpanel" aria-labelledby="tab-item">
            <h5>Item</h5>
            <div class="mb-3">
                <label class="form-label">ID do Item</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($item ?? '') ?>" readonly>
            </div>
        </div>
        <div class="tab-pane fade" id="tabWork" role="tabpanel" aria-labelledby="tab-work">
            <h5>Work</h5>
            <div class="mb-3">
                <label class="form-label">ID do Work</label>
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $work, 'f' => 'WORK', 'idC' => $i_work]); ?>
            </div>
        </div>
        <div class="tab-pane fade" id="tabExpression" role="tabpanel" aria-labelledby="tab-expression">
            <h5>Expression</h5>
            <div class="mb-3">
                <label class="form-label">ID do Expression</label>
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $expression, 'f' => 'EXPRESSION', 'idC' => $i_expression]); ?>
            </div>
        </div>
        <div class="tab-pane fade" id="tabManifestation" role="tabpanel" aria-labelledby="tab-manifestation">
            <h5>Manifestation</h5>
            <div class="mb-3">
                <label class="form-label">ID do Manifestation</label>
                <?= view('catalog/form_item/form_rdf_edit', ['form' => $manifestation, 'f' => 'MANIFESTATION', 'idC' => $i_manifestation]); ?>
            </div>
        </div>
    </div>
</div>



<script>
    var triggerTabList = [].slice.call(document.querySelectorAll('#itemTab button'));
    triggerTabList.forEach(function(triggerEl) {
        triggerEl.addEventListener('shown.bs.tab', function(event) {
            event.target.classList.add('active');
        });
    });

    // Garante que o Bootstrap está disponível
    window.addEventListener('DOMContentLoaded', function() {
        // Abrir painel lateral ao clicar em adicionar literal
        document.querySelectorAll('.btn-adicionar-literal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Passa dados do $w para o painel
                var prop = this.getAttribute('data-prop') || '';
                var group = this.getAttribute('data-group') || '';
                var type = this.getAttribute('data-type') || '';
                var range = this.getAttribute('data-range') || '';
                document.getElementById('add-prop').value = prop;
                document.getElementById('add-group').value = group;
                document.getElementById('add-type').value = type;
                document.getElementById('add-range').value = range;
                document.getElementById('add-n-value').value = '';
                if (window.bootstrap && bootstrap.Offcanvas) {
                    var offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvasAdicionarLiteral'));
                    offcanvas.show();
                } else {
                    alert('Bootstrap JS não carregado!');
                }
            });
        });

        // Salvar novo literal via AJAX
        var btnSalvarAdicionar = document.getElementById('btnSalvarAdicionarLiteral');
        if (btnSalvarAdicionar) {
            btnSalvarAdicionar.onclick = function() {
                var IDc = document.getElementById('add-idC').value;
                var value = document.getElementById('add-n-value').value;
                var prop = document.getElementById('add-prop').value;
                var params = new URLSearchParams();

                params.append('n_name', value);
                params.append('idc', IDc);
                params.append('property', prop);

                var url = '<?= base_url(); ?>/rdf/form/adicionar_literal';
                alert(url);

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: params.toString()
                    })
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao adicionar: ' + (data.message || 'Erro desconhecido.'));
                        }
                    })
                    .catch(() => alert('Erro ao adicionar: falha na requisição.'));
            };
        }
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
                var url = '<?= base_url(); ?>/rdf/concept/salvar_literal';
                fetch(url, {
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

        // Excluir registro rdf_data
        document.querySelectorAll('.btn-outline-danger.btn-sm[title="Excluir"]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tr = btn.closest('tr');
                var id_d = tr && tr.dataset && tr.dataset.idd ? tr.dataset.idd : (btn.getAttribute('data-id_d') || btn.getAttribute('data-idd'));
                if (!id_d) {
                    alert('ID do registro não encontrado!');
                    return;
                }
                if (!confirm('Tem certeza que deseja excluir este registro?')) return;
                fetch('<?= base_url(); ?>/rdf/form/excluir_rdf_data', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id_d=' + encodeURIComponent(id_d)
                    })
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao excluir: ' + (data.message || 'Erro desconhecido.'));
                        }
                    })
                    .catch(() => alert('Erro ao excluir: falha na requisição.'));
            });
        });
    });
</script>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>