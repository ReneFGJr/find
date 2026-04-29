<?= view('layout/header', ['title' => 'Cadastro da Autoridade • FIND']); ?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <div class="input-group m-2">
                <input type="text" id="searchConcept" class="form-control" placeholder="Buscar conceito..." oninput="searchConcepts(this.value)">
                <button class="btn btn-outline-secondary" type="button" id="btnSearchConcept" title="Buscar" onclick="searchConcepts(document.getElementById('searchConcept').value)">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <select class="form-select mb-3 mt-3" id="conceptResults" size="8" onchange="showConceptDetails(this.value)">
                <option value="">Selecione um conceito</option>
            </select>

            <button class="btn btn-outline-secondary mb-3" type="button" id="btnAddConcept" title="Adicionar Novo Conceito" onclick="formAddNewData(document.getElementById('conceptResults').value,'<?= $idC; ?>','CONCEPT','<?= $prop ?>','<?= $formID; ?>')">
                <i class="bi bi-plus"></i> Adicionar Novo Conceito
            </button>

            <button class="btn btn-outline-secondary mb-3" type="button" id="btnBack" title="Voltar" onclick="window.history.back()">
                <i class="bi bi-arrow-left"></i> Voltar
            </button>
        </div>
    </div>


</div>

<script>
    function formAddNewData(conceptId, idC, type, prop, formID) {
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
                        return true;
                        // Aqui você pode atualizar a interface, fechar painel, etc.
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