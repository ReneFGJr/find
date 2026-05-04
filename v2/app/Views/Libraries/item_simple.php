<?php
/**
 * View simples para exibir dados de um livro
 * Espera um array $book
 */
?>
<div class="card mb-3">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-book me-2"></i> Livro: <strong><?= htmlspecialchars($book['i_titulo'] ?? '') ?></strong>
    <div class="small mt-1 fst-italic">
      <i class="bi bi-person me-1"></i><?= htmlspecialchars($book['i_autores'] ?? '') ?>
    </div>
  </div>
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">Tombo</dt>
      <dd class="col-sm-9"><?= htmlspecialchars($book['i_tombo'] ?? '') ?></dd>

      <dt class="col-sm-3">Autores</dt>
      <dd class="col-sm-9"><?= htmlspecialchars($book['i_autores'] ?? '') ?></dd>

      <dt class="col-sm-3">Ano</dt>
      <dd class="col-sm-9"><?= htmlspecialchars($book['i_year'] ?? '') ?></dd>

      <dt class="col-sm-3">ISBN</dt>
      <dd class="col-sm-9"><?= htmlspecialchars($book['i_identifier'] ?? '') ?></dd>

      <dt class="col-sm-3">Classificação</dt>
      <dd class="col-sm-9">
        <?= htmlspecialchars($book['i_ln1'] ?? '') ?> <?= htmlspecialchars($book['i_ln2'] ?? '') ?> <?= htmlspecialchars($book['i_ln3'] ?? '') ?> <?= htmlspecialchars($book['i_ln4'] ?? '') ?>
      </dd>

      <dt class="col-sm-3">Exemplar</dt>
      <dd class="col-sm-9"><?= htmlspecialchars($book['i_exemplar'] ?? '') ?></dd>

      <dt class="col-sm-3">Manifestation</dt>
      <dd class="col-sm-9"><?= htmlspecialchars($book['i_manifestation'] ?? '') ?></dd>

      <dt class="col-sm-3">Work</dt>
      <dd class="col-sm-9"><?= htmlspecialchars($book['i_work'] ?? '') ?></dd>

      <dt class="col-sm-3">Expression</dt>
      <dd class="col-sm-9"><?= htmlspecialchars($book['i_expression'] ?? '') ?></dd>
    </dl>
  </div>
</div>
