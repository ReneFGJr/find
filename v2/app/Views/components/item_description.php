<?php
/**
 * Componente para exibir a descrição do item
 * Espera receber ['Description'] como array ou string em $meta
 * Uso: view('components/item_description', ['meta' => $book['meta'] ?? []])
 */
?>
<?php
if (!empty($meta['Description'])) {
    if (is_array($meta['Description'])) {
        foreach ($meta['Description'] as $desc) {
            echo esc($desc['name'] ?? $desc) . '<br>';
        }
    } else {
        echo esc($meta['Description']);
    }
} else {
    echo '<span class="text-muted">---</span>';
}
?>
