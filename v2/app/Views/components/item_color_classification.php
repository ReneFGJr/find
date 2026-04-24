<?php foreach ($meta['ColorClassification'] as $c): ?>
    <div
        class="me-1 full p-2 text-start rounded-3 mb-1"
        style="background-color: <?= esc($c['background']); ?>; color: <?= esc($c['textcolor']); ?>;">

        [<?= esc($c['name']); ?>]
    </div>
<?php endforeach; ?>