<?= view('layout/header', ['title' => 'Cadastro da Autoridade • FIND']); ?>
<?php
$rangeClasses = isset($rangeClassNames) && is_array($rangeClassNames) ? $rangeClassNames : [];
if (empty($rangeClasses) && isset($range)) {
    if (is_array($range)) {
        $rangeClasses = $range;
    } else {
        $rangeRaw = (string) $range;
        $rangeRaw = str_replace(['[', ']', '"', "'"], '', $rangeRaw);
        $rangeRaw = str_replace(['|', ';'], ',', $rangeRaw);
        $parts = array_map('trim', explode(',', $rangeRaw));
        $rangeClasses = array_values(array_filter($parts, static function ($v) {
            return $v !== '';
        }));
    }
}
$rangeClasses = array_values(array_unique($rangeClasses));
?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <div class="input-group m-2">
                <input type="text" id="searchConcept" class="form-control" placeholder="Buscar conceito..." oninput="searchConcepts(this.value)">
                <button class="btn btn-outline-secondary" type="button" id="btnSearchConcept" title="Buscar" onclick="searchConcepts(document.getElementById('searchConcept').value)">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <div class="input-group m-2 mb-3">
                <span class="input-group-text">Classe</span>
                <select class="form-select" id="conceptClass">
                    <option value="">Selecione a classe</option>
                    <?php foreach ($rangeClasses as $className) { ?>
                        <option value="<?= esc($className) ?>"><?= esc($className) ?></option>
                    <?php } ?>
                </select>
                <button class="btn btn-outline-success" type="button" id="btnCreateConcept" title="Criar conceito" onclick="createConcept()">
                    <i class="bi bi-node-plus"></i> Criar conceito
                </button>
            </div>

            <select class="form-select mb-3 mt-3" id="conceptResults" size="8" onchange="showConceptDetails(this.value)">
                <option value="">Selecione um conceito</option>
            </select>

            <button class="btn btn-outline-secondary mb-3" type="button" id="btnAddConcept" title="Adicionar Novo Conceito" onclick="formAddNewData(document.getElementById('conceptResults').value,'<?= $idC; ?>','CONCEPT','<?= $prop ?>','<?= $formID; ?>', true)">
                <i class="bi bi-plus"></i> Adicionar Novo Conceito
            </button>

            <button class="btn btn-outline-primary mb-3" type="button" id="btnAddConceptContinue" title="Adicionar e continuar" onclick="formAddNewData(document.getElementById('conceptResults').value,'<?= $idC; ?>','CONCEPT','<?= $prop ?>','<?= $formID; ?>', false)">
                <i class="bi bi-plus-circle"></i> Adicionar e continuar
            </button>

            <button class="btn btn-outline-secondary mb-3" type="button" id="btnBack" title="Voltar" onclick="closePanelAndReload()">
                <i class="bi bi-arrow-left"></i> Voltar
            </button>
        </div>
    </div>


</div>

<script>
    function closePanelAndReload() {
        // Tenta recarregar a janela pai (quando aberto em modal/iframe/popup).
        var targetWindow = (window.opener && !window.opener.closed) ? window.opener : window.parent;

        if (targetWindow && targetWindow !== window) {
            try {
                if (targetWindow.location && typeof targetWindow.location.reload === 'function') {
                    targetWindow.location.reload();
                }
            } catch (e) {
                // Ignora erro de mesma-origem e segue com fechamento local.
            }
        }

        // Fecha componentes Bootstrap, se existirem.
        try {
            var modalEl = document.querySelector('.modal.show');
            if (modalEl && window.bootstrap && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
            }

            var offcanvasEl = document.querySelector('.offcanvas.show');
            if (offcanvasEl && window.bootstrap && bootstrap.Offcanvas) {
                var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                offcanvas.hide();
            }
        } catch (e) {
            // Se não houver Bootstrap disponível, continua fluxo.
        }

        // Se for popup, tenta fechar.
        try {
            window.close();
        } catch (e) {
            // Ignora falha de fechamento.
        }

        // Garantia final: recarrega a própria tela.
        window.location.reload();
    }

    function resetConceptForm() {
        var searchInput = document.getElementById('searchConcept');
        var select = document.getElementById('conceptResults');

        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }

        if (select) {
            select.innerHTML = '<option value="">Selecione um conceito</option>';
            select.value = '';
        }
    }

    function createConcept() {
        var termInput = document.getElementById('searchConcept');
        var classSelect = document.getElementById('conceptClass');

        var term = termInput ? termInput.value.trim() : '';
        var conceptClass = classSelect ? classSelect.value : '';

        if (!term) {
            alert('Informe o termo para criar o conceito.');
            if (termInput) termInput.focus();
            return;
        }

        if (!conceptClass) {
            alert('Selecione a classe do conceito.');
            if (classSelect) classSelect.focus();
            return;
        }

        $.ajax({
            url: '<?= base_url(); ?>/rdf/concept/create_concept',
            method: 'POST',
            dataType: 'json',
            data: {
                term: term,
                class: conceptClass
            },
            success: function(response) {
                if (response && response.success) {
                    alert(response.message || 'Conceito criado com sucesso.');
                    searchConcepts(term);
                } else {
                    alert('Erro: ' + ((response && response.message) ? response.message : 'Não foi possível criar o conceito.'));
                }
            },
            error: function(xhr, status, error) {
                alert('Erro na requisição: ' + error);
            }
        });
    }

    function formAddNewData(conceptId, idC, type, prop, formID, closeAfterAdd) {
        var select = document.getElementById('conceptResults');
        var idS = select ? select.value : '';
        if (type == 'CONCEPT') {
            var url = '<?= base_url(); ?>/rdf/concept/add_link_concept?idc=' + encodeURIComponent(idC) + '&property=' + encodeURIComponent(prop) + '&value=' + encodeURIComponent(idS);

            // AJAX para adicionar o conceito
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (closeAfterAdd === false) {
                            resetConceptForm();
                        } else {
                            closePanelAndReload();
                        }
                    } else {
                        alert('Erro: ' + (response.message || 'Não foi possível vincular o conceito.'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erro na requisição: ' + error);
                }
            });
        }
    }

    function searchConcepts(termo) {
        var range = '';
        <?php if (isset($range)) { ?>
            range = <?= json_encode($range) ?>;
        <?php } ?>
        var url = '<?= base_url(); ?>/rdf/searchConcept?term=' + encodeURIComponent(termo) + '&range=' + encodeURIComponent(range);

        $.get(url, function(data) {
            if (typeof data === 'string') {
                try {
                    data = JSON.parse(data);
                } catch (e) {
                    $('#conceptResults').html(data);
                    return;
                }
            }
            if (data && Array.isArray(data.results)) {
                var html = '<option value="">Selecione um conceito</option>';
                data.results.forEach(function(item) {
                    html += '<option value="' + item.id + '">' +
                        item.label +
                        (item.lang ? ' [' + item.lang + ']' : '') +
                        (item.class ? ' (' + item.class + ')' : '') +
                        '</option>';
                });
                $('#conceptResults').html(html);
            } else {
                $('#conceptResults').html('<option value="">Nenhum conceito encontrado</option>');
            }
        });
    }

    function showConceptDetails(conceptId) {
        // Função placeholder para evitar erro. Implemente conforme necessário.
        // Exemplo: console.log('Conceito selecionado:', conceptId);
    }
</script>