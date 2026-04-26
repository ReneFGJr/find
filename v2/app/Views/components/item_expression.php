<?php
if (!empty($meta['Langage'])):
/**
 * Componente para exibir as expressões (idiomas/expressões) de um item
 * Espera receber ['Langage'] como array em $meta
 * Uso: view('components/item_expression', ['meta' => $book['meta'] ?? []])
 */

$langages = [];
foreach ($meta['Langage'] as $l) {
    $langage = $l['name'];
    while (str_contains($langage, ':')) {
        $langage = substr($langage, strpos($langage, ':') + 1);
    }
    $langages[$langage] = 1;
}
?>
<?php  ?>
    <?php foreach ($langages as $l=>$vl): ?>
        <span class="badge bg-light text-dark me-1"><?= esc($l); ?></span>
    <?php endforeach; ?>
<?php else: ?>
    <span class="text-muted">Não informado</span>
<?php endif; ?>
