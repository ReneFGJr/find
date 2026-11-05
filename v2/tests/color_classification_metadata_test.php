<?php

namespace CodeIgniter { class Model {} }

namespace {
    require __DIR__ . '/../app/Models/Find/Metadata/Index.php';

    $rows = [];
    foreach (['hasColorclassification', 'hasColorClassification2', 'hasColorClassification3', 'hasColorClassification4', 'hasColorClassification12', 'hasColorClassification'] as $index => $property) {
        $rows[] = ['Property' => $property, 'ID' => $index + 1, 'Caption' => 'Cor ' . $index . ' #ffffff#000000', 'Lang' => 'pt_BR'];
    }
    $rows[] = $rows[1]; // A mesma cor não deve aparecer duas vezes.
    $rows[] = ['Property' => 'hasColorClassificationNote', 'ID' => 99, 'Caption' => 'Nota', 'Lang' => 'pt_BR'];
    $rows[] = ['Property' => 'hasTitle', 'ID' => 100, 'Caption' => 'Título', 'Lang' => 'pt_BR'];

    $meta = (new \App\Models\Find\Metadata\Index())->metadata(['data' => $rows]);
    if (count($meta['ColorClassification']) !== 6
        || array_column($meta['ColorClassification'], 'ID') !== [1, 2, 3, 4, 5, 6]
        || $meta['ColorClassification'][1]['name'] !== $rows[1]['Caption']
        || $meta['ColorClassification'][1]['lang'] !== 'pt'
        || count($meta['hasColorClassificationNote']) !== 1
        || $meta['Title'][0]['name'] !== 'Título'
        || isset($meta['hasColorClassification2'])) {
        throw new \RuntimeException('Falha no agrupamento das classificações de cores.');
    }
    echo "Classificações de cores: OK\n";
}
