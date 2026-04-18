<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
    <?php
    $segments = service('uri')->getSegments();
    $base = base_url();
    $path = '';
    $total = count($segments);
    ?>
    <li class="breadcrumb-item"><a href="<?= base_url('/catalog/index') ?>"><i class="bi bi-journal-text me-1"></i>Catálogo</a></li>
    <?php for ($i = 2; $i <= $total; $i++):
      $path .= '/' . $segments[$i-1];
      $isLast = $i === $total;
      $label = ucfirst(str_replace(['_', '-'], ' ', $segments[$i-1]));
    ?>
      <li class="breadcrumb-item<?= $isLast ? ' active' : '' ?>"<?= $isLast ? ' aria-current="page"' : '' ?>>
        <?php if (!$isLast): ?>
          <a href="<?= $base . '/catalog' . $path ?>"><?= $label ?></a>
        <?php else: ?>
          <?= $label ?>
        <?php endif; ?>
      </li>
    <?php endfor; ?>
  </ol>
</nav>
