<form id="formAdicionarAtributo" method="post" action="#" autocomplete="off">
    <div class="mb-3">
        <label for="atributo-nome" class="form-label">Nome do atributo</label>
        <input type="text" class="form-control" id="atributo-nome" name="atributo_nome" required readonly>
    </div>
    <div class="mb-3">
        <label for="atributo-valor" class="form-label">Autoridade (buscar)</label>
        <input type="text" class="form-control" id="atributo-valor" name="atributo_valor" required>
    </div>
    <div>
        <input type="hidden" id="atributo-range" name="atributo_range">
        <input type="hidden" id="atributo-idc" name="atributo_idc">
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-outline-danger me-2" id="btn-novo-conceito" data-bs-dismiss="offcanvas" disabled>
            <i class="bi bi-plus-circle"></i> Conceito
        </button>
        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="offcanvas">Cancelar</button>
        <button type="submit" class="btn btn-outline-success" id="btn-adicionar-conceito" disabled>Adicionar</button>
    </div>

    <div class="mb-3" id="autocomplete-candidatos" style="display:none;">
        <label for="atributo-candidatos" class="form-label">Selecione um valor</label>
        <select class="form-select" id="atributo-candidatos" name="atributo_candidatos" size="10"></select>
    </div>

</form>
<div class="alert alert-info p-2 mt-2" id="atributo-debug" style="font-size:0.95em; display:none;"></div>
<script>
    // Exemplo de envio AJAX (ajuste conforme backend)
    document.getElementById('formAdicionarAtributo').onsubmit = function(e) {
        e.preventDefault();
        // Pegue os dados do formulário
        var idC = <?= $idC; ?>;
        var prop = document.getElementById('atributo-nome').value;
        var range = document.getElementById('atributo-range').value;
        var selectCandidatos = document.getElementById('atributo-candidatos');
        var valor;
        if (selectCandidatos && selectCandidatos.style.display !== 'none' && selectCandidatos.selectedIndex >= 0) {
            valor = selectCandidatos.value;
        } else {
            valor = document.getElementById('atributo-valor').value;
        }

        // Envio AJAX tradicional (form-urlencoded)
        var url = '<?= base_url(); ?>/rdf/concept/add_link_concept';
        var params = new URLSearchParams();
        params.append('property', prop);
        params.append('value', valor);
        params.append('idc', idC);

        console.log('Enviando dados:', {
            property: prop,
            value: valor,
            idc: idC
        });

        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao adicionar 2: ' + (data.message || 'Erro desconhecido.'));
                    }
                } catch (err) {
                    let msg = 'Erro ao adicionar 3: falha na requisição.';
                    if (err) {
                        if (err.message) msg += '\nMensagem: ' + err.message;
                        if (err.stack) msg += '\nStack: ' + err.stack;
                        msg += '\nObjeto: ' + JSON.stringify(err);
                    }
                    alert(msg + '\nResposta: ' + xhr.responseText);
                }
            }
        };
        xhr.send(params.toString());

    };

    // Função para mostrar os dados recebidos no painel
    function mostrarDebugAtributo(dados) {
        if (1 ==2)
        {
            var debug = document.getElementById('atributo-debug');
            if (debug) {
                debug.innerHTML = '';
                for (var k in dados) {
                    debug.innerHTML += '<strong>' + k + ':</strong> ' + dados[k] + '<br>';
                }
                debug.style.display = 'block';
            }
        }
    }
    // Torna a função global para ser chamada pelo script principal
    window.mostrarDebugAtributo = mostrarDebugAtributo;

    // Autocomplete para o campo valor
    var inputValor = document.getElementById('atributo-valor');
    var selectCandidatos = document.getElementById('atributo-candidatos');
    var divCandidatos = document.getElementById('autocomplete-candidatos');
    var inputRange = document.getElementById('atributo-range');
    var inputIDc = document.getElementById('atributo-idc');

    inputValor.addEventListener('input', function() {
        var range = document.getElementById('atributo-range').value;
        var termo = inputValor.value;
        var range = inputRange.value;
        var idc = inputIDc.value;
        var url = '<?= base_url(); ?>/rdf/searchConcept?term=' + encodeURIComponent(termo) + '&range=' + encodeURIComponent(range);

        if (termo.length < 5 || !range) {
            divCandidatos.style.display = 'none';
            selectCandidatos.innerHTML = '';
            mostrarDebugAtributo({
                Info: 'Digite ao menos 5 letras e selecione o range.',
                URL: url
            });
            return;
        }
        mostrarDebugAtributo({
            Info: 'Consultando...',
            URL: url
        });
        fetch(url)
            .then(resp => resp.json().catch(() => ({
                error: 'Resposta inválida da API.'
            })))
            .then(data => {
                selectCandidatos.innerHTML = '';
                if (data && data.error) {
                    divCandidatos.style.display = 'none';
                    mostrarDebugAtributo({
                        Erro: data.error,
                        URL: url
                    });
                    return;
                }
                if (data && Array.isArray(data.results) && data.results.length > 0) {
                    data.results.forEach(function(item) {
                        var opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.label || item.name || item.id;
                        selectCandidatos.appendChild(opt);
                    });
                    divCandidatos.style.display = 'block';
                    mostrarDebugAtributo({
                        Info: 'Selecione um valor.',
                        URL: url,
                        Total: data.results.length
                    });
                } else {
                    divCandidatos.style.display = 'none';
                    if (data && data.message) {
                        mostrarDebugAtributo({
                            Info: data.message,
                            URL: url
                        });
                    } else {
                        mostrarDebugAtributo({
                            Info: 'Nenhum resultado encontrado.',
                            URL: url
                        });
                    }
                }
            })
            .catch((err) => {
                divCandidatos.style.display = 'none';
                mostrarDebugAtributo({
                    Erro: 'Falha na requisição: ' + (err && err.message ? err.message : 'erro desconhecido'),
                    URL: url
                });
            });
    });
    // Ao selecionar um candidato, preenche o campo valor

    // Habilita o botão Adicionar só se houver item selecionado
    var btnAdicionar = document.getElementById('btn-adicionar-conceito');
    selectCandidatos.addEventListener('change', function() {
        var opt = selectCandidatos.options[selectCandidatos.selectedIndex];
        if (opt) {
            inputValor.value = opt.textContent;
            btnAdicionar.disabled = false;
        } else {
            btnAdicionar.disabled = true;
        }
    });

    // Garante que o botão está desabilitado se não houver opções
    selectCandidatos.addEventListener('input', function() {
        if (selectCandidatos.options.length > 0 && selectCandidatos.selectedIndex >= 0) {
            btnAdicionar.disabled = false;
        } else {
            btnAdicionar.disabled = true;
        }
    });


    // Permite setar o range externamente
    window.setAtributoRange = function(range) {
        if (!inputRange) {
            inputRange = document.getElementById('atributo-range');
        }
        if (Array.isArray(range)) {
            inputRange.value = JSON.stringify(range);
        } else if (typeof range === 'string') {
            try {
                var arr = JSON.parse(range);
                if (Array.isArray(arr)) {
                    inputRange.value = range;
                } else {
                    inputRange.value = range;
                }
            } catch (e) {
                inputRange.value = range;
            }
        } else {
            inputRange.value = String(range);
        }
        var evt = new Event('input', {
            bubbles: true
        });
        inputRange.dispatchEvent(evt);
        mostrarDebugAtributo({
            Info: 'Range carregado',
            Range: inputRange.value
        });
    };

    // Permite setar o idc externamente (igual ao range)
    window.setAtributoIdc = function(idc) {
        // Garante que o campo existe e está visível
        var inputIdc = document.getElementById('atributo-idc');
        if (inputIdc) {
            inputIdc.value = idc !== undefined && idc !== null ? String(idc) : '';
            mostrarDebugAtributo({
                Info: 'IDC carregado',
                IDC: inputIdc.value
            });
        } else {
            console.warn('Campo atributo-idc não encontrado!');
        }
    };

    // Permite setar range e idc juntos (caso queira facilitar chamada externa)
    window.setAtributoRangeIdc = function(range, idc) {
        window.setAtributoRange(range);
        window.setAtributoIdc(idc);
    };

    // Ativa o botão "Conceito" se o campo valor tiver mais de 4 caracteres
    var btnNovoConceito = document.getElementById('btn-novo-conceito');
    inputValor.addEventListener('input', function() {
        if (inputValor.value.length > 4) {
            btnNovoConceito.disabled = false;
        } else {
            btnNovoConceito.disabled = true;
        }
    });
</script>