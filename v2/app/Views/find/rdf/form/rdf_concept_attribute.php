<form id="formAdicionarAtributo" method="post" action="#" autocomplete="off">
    <div class="mb-3">
        <label for="atributo-nome" class="form-label">Nome do atributo</label>
        <input type="text" class="form-control" id="atributo-nome" name="atributo_nome" required readonly>
    </div>
    <div class="mb-3">
        <label for="atributo-valor" class="form-label">Autoridade (buscar)</label>
        <input type="text" class="form-control" id="atributo-valor" name="atributo_valor" required>
    </div>
    <input type="hidden" id="atributo-range" name="atributo_range" value="">
    XXXXXXXXX
    <div class="mb-3" id="autocomplete-candidatos" style="display:none;">
        <label for="atributo-candidatos" class="form-label">Selecione um valor</label>
        <select class="form-select" id="atributo-candidatos" name="atributo_candidatos"></select>
    </div>
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancelar</button>
        <button type="submit" class="btn btn-success">Adicionar</button>
    </div>
</form>
<div class="alert alert-info p-2" id="atributo-debug" style="font-size:0.95em; display:none;"></div>
<script>
// Exemplo de envio AJAX (ajuste conforme backend)
document.getElementById('formAdicionarAtributo').onsubmit = function(e) {
    e.preventDefault();
    // Pegue os dados do formulário
    var nome = document.getElementById('atributo-nome').value;
    var valor = document.getElementById('atributo-valor').value;
    var range = document.getElementById('atributo-range').value;
    // TODO: Ajuste a URL e payload conforme sua API
    fetch('/rdf/concept/adicionar_atributo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nome: nome, valor: valor, range: range })
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
// Função para mostrar os dados recebidos no painel
function mostrarDebugAtributo(dados) {
    var debug = document.getElementById('atributo-debug');
    if (debug) {
        debug.innerHTML = '';
        for (var k in dados) {
            debug.innerHTML += '<strong>' + k + ':</strong> ' + dados[k] + '<br>';
        }
        debug.style.display = 'block';
    }
}
// Torna a função global para ser chamada pelo script principal
window.mostrarDebugAtributo = mostrarDebugAtributo;

// Autocomplete para o campo valor
var inputValor = document.getElementById('atributo-valor');
var selectCandidatos = document.getElementById('atributo-candidatos');
var divCandidatos = document.getElementById('autocomplete-candidatos');
var inputRange = document.getElementById('atributo-range');

inputValor.addEventListener('input', function() {
    var termo = inputValor.value;
    var range = inputRange.value;
    if (termo.length < 2 || !range) {
        divCandidatos.style.display = 'none';
        selectCandidatos.innerHTML = '';
        return;
    }
    fetch('/rdf/searchConcept?term=' + encodeURIComponent(termo) + '&range=' + encodeURIComponent(range))
        .then(resp => resp.json())
        .then(data => {
            selectCandidatos.innerHTML = '';
            if (data && Array.isArray(data.results) && data.results.length > 0) {
                data.results.forEach(function(item) {
                    var opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.label || item.name || item.id;
                    selectCandidatos.appendChild(opt);
                });
                divCandidatos.style.display = 'block';
            } else {
                divCandidatos.style.display = 'none';
            }
        })
        .catch(() => {
            divCandidatos.style.display = 'none';
        });
});
// Ao selecionar um candidato, preenche o campo valor
selectCandidatos.addEventListener('change', function() {
    var opt = selectCandidatos.options[selectCandidatos.selectedIndex];
    if (opt) {
        inputValor.value = opt.textContent;
    }
});
// Permite setar o range externamente
window.setAtributoRange = function(range) {
    inputRange.value = range;
};
</script>
