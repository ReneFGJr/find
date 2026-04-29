<?php
if (!empty($meta['Langage'])):
    /**
     * Componente para exibir as expressões (idiomas/expressões) de um item
     * Espera receber ['Langage'] como array em $meta
     * Uso: view('components/item_expression', ['meta' => $book['meta'] ?? []])
     */

    $langages = [];
    if (isset($meta['Langage']) && is_array($meta['Langage'])) {
        foreach ($meta['Langage'] as $l) {
            $langage = $l['name'];
            if ($langage) {
                while (strpos($langage, ':') !== false) {
                    $langage = substr($langage, strpos($langage, ':') + 1);
                }
                $langages[$langage] = 1;
            }
        }
        foreach ($langages as $l => $vl) {
            echo '<span class="badge bg-light text-dark me-1">' . esc($l) . '</span>';
        }
    } else {
        echo '<span class="text-muted">Não informado</span>';
    }
endif;
