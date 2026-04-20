<?php
include(APPPATH . 'Views/layout/header.php');
// Espera-se que $id_form, $ClassesMap, $ClassesNoMap estejam disponíveis
?>
<div class="container my-4">
    <h3>Editar Range do Formulário <span class="text-secondary" style="font-size:0.9em;">(ID: <?= htmlspecialchars($id_form) ?>)</span></h3>
    <div class="d-flex align-items-start gap-3">
        <div>
            <label><strong>Classes Selecionadas</strong></label>
            <select id="classes-map" class="form-select" size="12" multiple style="min-width:180px;">
                <?php
                // Ordena por nome
                $sortedMap = $ClassesMap;
                usort($sortedMap, function($a, $b) {
                    return strcoll($a['c_class'], $b['c_class']);
                });
                foreach ($sortedMap as $class):
                ?>
                    <option value="<?= htmlspecialchars($class['id_c']) ?>"><?= htmlspecialchars($class['c_class']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="d-flex flex-column justify-content-center align-items-center gap-2" style="margin-top: 1.7em;">
            <button id="btn-remover" class="btn btn-outline-secondary" type="button">&gt;</button>
            <button id="btn-adicionar" class="btn btn-outline-secondary" type="button">&lt;</button>
        </div>
        <div>
            <label><strong>Classes Não Selecionadas</strong></label>
            <select id="classes-nomap" class="form-select" size="12" multiple style="min-width:180px;">
                <?php
                // Ordena por nome
                $sortedNoMap = $ClassesNoMap;
                usort($sortedNoMap, function($a, $b) {
                    return strcoll($a['c_class'], $b['c_class']);
                });
                foreach ($sortedNoMap as $class):
                ?>
                    <option value="<?= htmlspecialchars($class['id_c']) ?>"><?= htmlspecialchars($class['c_class']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Espaço reservado para alinhamento visual -->
    </div>
    <div class="mt-4 text-center">
        <button id="btn-salvar-range" class="btn btn-success" style="min-width:90px;">Salvar</button>
    </div>
</div>
<script>
// Mover opções entre os selects (botão)
document.getElementById('btn-adicionar').onclick = function() {
    moverSelecionados('classes-nomap', 'classes-map');
};
document.getElementById('btn-remover').onclick = function() {
    moverSelecionados('classes-map', 'classes-nomap');
};

function moverSelecionados(origemId, destinoId) {
    var origem = document.getElementById(origemId);
    var destino = document.getElementById(destinoId);
    var selecionados = Array.from(origem.selectedOptions);
    selecionados.forEach(function(opt) {
        destino.appendChild(opt);
    });
    ordenarSelect(destino);
}
// Função para ordenar options de um select por texto
function ordenarSelect(select) {
    var opts = Array.from(select.options);
    opts.sort(function(a, b) {
        return a.text.localeCompare(b.text, 'pt-BR');
    });
    opts.forEach(function(opt) { select.appendChild(opt); });
}
// Ordena ao mover por duplo clique também
function moverOption(origemId, destinoId, value) {
    var origem = document.getElementById(origemId);
    var destino = document.getElementById(destinoId);
    var opt = Array.from(origem.options).find(function(o) { return o.value === value; });
    if (opt) destino.appendChild(opt);
    ordenarSelect(destino);
}

// Mover com duplo clique
document.getElementById('classes-map').ondblclick = function(e) {
    if (e.target && e.target.tagName === 'OPTION') {
        moverOption('classes-map', 'classes-nomap', e.target.value);
    }
};
document.getElementById('classes-nomap').ondblclick = function(e) {
    if (e.target && e.target.tagName === 'OPTION') {
        moverOption('classes-nomap', 'classes-map', e.target.value);
    }
};
function moverOption(origemId, destinoId, value) {
    var origem = document.getElementById(origemId);
    var destino = document.getElementById(destinoId);
    var opt = Array.from(origem.options).find(function(o) { return o.value === value; });
    if (opt) destino.appendChild(opt);
}

// Salvar Range
document.getElementById('btn-salvar-range').onclick = function() {
    var selecionadas = Array.from(document.getElementById('classes-map').options).map(function(opt) {
        return opt.value;
    });
    fetch('/index.php/rdf/form/salvar_range', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id_form: '<?= htmlspecialchars($id_form) ?>',
            form_range: JSON.stringify(selecionadas)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fecha o painel lateral (offcanvas) e recarrega a página principal
            if (window.parent && window.parent.document) {
                var offcanvas = window.parent.document.getElementById('offcanvasEditarRange');
                if (offcanvas && window.parent.bootstrap) {
                    var instance = window.parent.bootstrap.Offcanvas.getOrCreateInstance(offcanvas);
                    instance.hide();
                }
                window.parent.location.reload();
            } else {
                alert('Range atualizado com sucesso!');
                location.reload();
            }
        } else {
            alert('Erro ao salvar: ' + (data.message || 'Erro desconhecido.'));
        }
    })
    .catch(() => {
        alert('Erro ao salvar: falha na requisição.');
    });
};
</script>
