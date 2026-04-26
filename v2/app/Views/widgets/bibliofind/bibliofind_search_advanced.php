<?php

/**
 * View: Componente de Busca Avançada com seleção de Local
 *
 * Espera variáveis:
 * - $libraryCode: código da biblioteca para filtrar locais
 * - $places: array de locais filtrados pelo Model LibraryPlace
 */
?>
<div class="search-component">
    <form method="get" action="<?= base_url('busca/resultado') ?>">
        <div class="row g-2 align-items-end">
            <div class="col-md-7">
                <h2 class="col-md-12 h3">O que quer ler hoje?</h2>
                <input type="text" name="q" id="searchTerm" class="form-control" placeholder="Digite o termo de busca...">
            </div>
            <div class="col-md-3">
                <select name="place" id="placeSelect" class="form-control">
                    <option value="">Todos os locais</option>
                    <?php foreach ($places as $place): ?>
                        <option value="<?= $place['id_lp'] ?>"><?= esc($place['lp_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <label class="form-label" style="visibility:hidden">Buscar</label>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>
</div>