
<?php foreach ($book['meta']['Subject'] as $s): ?>
    <span class="badge bg-primary bg-opacity-10 text-primary me-1"><?= esc($s['name']); ?></span>
<?php endforeach; ?>
