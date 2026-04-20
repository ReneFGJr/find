<!-- Componente: Painel de edição de RANGE -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditarRange" aria-labelledby="offcanvasEditarRangeLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasEditarRangeLabel">Editar Range</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="range-classes-selecionadas" class="mb-4">
            <h6>Classes Selecionadas</h6>
            <div id="range-classes-list" class="mb-2"></div>
        </div>
        <div id="range-classes-nao-selecionadas">
            <h6>Classes Não Selecionadas</h6>
            <select id="range-classes-nao-list" class="form-select mb-2" size="8" multiple></select>
            <button class="btn btn-outline-primary btn-sm mt-1" id="btn-adicionar-range-select" type="button">Adicionar selecionadas</button>
        </div>
        <button class="btn btn-success mt-3" id="btn-salvar-range">Salvar Range</button>
    </div>
</div>

<?php
pre($allClasses);
?>