<?php
$resourceUrls = [];
foreach (($meta ?? []) as $entity) {
    foreach (($entity['data'] ?? []) as $property) {
        if (($property['Property'] ?? '') !== 'hasURL') {
            continue;
        }
        $url = trim((string) ($property['Caption'] ?? ''));
        if (preg_match('~^https?://~i', $url) && filter_var($url, FILTER_VALIDATE_URL)) {
            $resourceUrls[$url] = $url;
        }
    }
}
?>
<?php if ($resourceUrls): ?>
    <div class="ms-auto d-flex gap-2 flex-shrink-0">
        <?php foreach ($resourceUrls as $resourceUrl): ?>
            <a href="<?= esc($resourceUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer"
                class="btn btn-outline-danger" title="Acessar recurso (nova aba)" aria-label="Acessar recurso em PDF (nova aba)">
                <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
