<?php
function esc($value, $context = 'html') {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
function renderResourceLinks($meta) {
    ob_start();
    include __DIR__ . '/../app/Views/components/item_resource_links.php';
    return ob_get_clean();
}
$meta = [];
foreach (['work', 'expression', 'manifestation'] as $entity) {
    $meta[$entity]['data'] = [
        ['Property' => 'hasURL', 'Caption' => ' https://example.org/book?x=1&y=2 '],
        ['Property' => 'hasURL', 'Caption' => 'http://example.org/' . $entity],
        ['Property' => 'hasURL', 'Caption' => 'javascript:alert(1)'],
        ['Property' => 'hasURL', 'Caption' => 'http-invalid'],
        ['Property' => 'hasURL', 'Caption' => 'Texto sem link'],
        ['Property' => 'hasTitle', 'Caption' => 'https://example.org/title'],
    ];
}
$html = renderResourceLinks($meta);
if (substr_count($html, 'target="_blank"') !== 4
    || substr_count($html, 'rel="noopener noreferrer"') !== 4
    || substr_count($html, 'bi-file-earmark-pdf') !== 4
    || strpos($html, 'https://example.org/book?x=1&amp;y=2') === false
    || strpos($html, 'javascript:') !== false
    || strpos($html, 'http-invalid') !== false
    || strpos($html, 'https://example.org/title') !== false) {
    throw new RuntimeException('Falha nos links de recursos do item.');
}
if (trim(renderResourceLinks([])) !== ''
    || trim(renderResourceLinks(['work' => ['data' => [['Property' => 'hasURL', 'Caption' => 'Sem URL']]]])) !== '') {
    throw new RuntimeException('Exibiu ícone sem recurso HTTP/HTTPS.');
}
echo "OK: links HTTP/HTTPS, entidades W/E/M, duplicados, nova aba, escape e valores inválidos.\n";
