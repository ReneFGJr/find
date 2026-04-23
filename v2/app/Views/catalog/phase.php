<?php include(APPPATH . 'Views/layout/header.php'); ?>
<?php include(APPPATH . 'Views/layout/navbar.php'); ?>
<?php include(APPPATH . 'Views/components/catalog_breadcrumbs.php'); ?>
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-list-check me-2"></i>Obras para Catalogar - Status: <?= htmlspecialchars($status) ?></h2>
    <?php if (session('msg')): ?>
        <div class="alert alert-info"> <?= htmlspecialchars(session('msg')) ?> </div>
    <?php endif; ?>
    <?php if ($status == 5): ?>
        <form method="get" class="mb-4 d-flex gap-2 align-items-end">
            <input type="hidden" name="status" value="5">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por Tombo, Título, Autor ou ISBN" value="<?= htmlspecialchars($busca ?? '') ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
    <?php endif; ?>
    <?php if (!empty($obras)) : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th width="3%">#</th>
                        <th>Título</th>
                        <th>ISBN</th>
                        <th>Tombo</th>
                        <th>Exemplar</th>
                        <th>Data de Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $itemCount = 0; ?>
                    <?php foreach ($obras as $obra): ?>
                        <tr>
                            <td><?= esc(++$itemCount);  ?></td>
                            <td><?= htmlspecialchars($obra['i_titulo'] ?? '') ?></td>
                            <td><?= htmlspecialchars($obra['i_identifier'] ?? '') ?></td>
                            <td><?= htmlspecialchars($obra['i_tombo'] ?? '') ?></td>
                            <td><?= htmlspecialchars($obra['i_exemplar'] ?? '') ?></td>
                            <td><?= htmlspecialchars($obra['i_created'] ?? '') ?></td>
                            <td>
                                <?php
                                switch ($obra['i_status'] ?? '') {
                                    case 1:
                                        $url = base_url('/catalog/catalogar/metadadoSearch/' . urlencode($obra['id_i'] ?? ''));
                                        break;
                                    case 2:
                                        $badgeClass = 'bg-info';
                                        $url = base_url('/catalog/catalogar/metadadoSearch/' . urlencode($obra['id_i'] ?? ''));
                                        break;
                                    case 3:
                                        $url = base_url('/catalog/catalogar/metadadoSearch/' . urlencode($obra['id_i'] ?? ''));
                                        $badgeClass = 'bg-warning';
                                        break;
                                    case 4:
                                        $url = base_url('/catalog/catalogar/metadadoSearch/' . urlencode($obra['id_i'] ?? ''));
                                        $badgeClass = 'bg-primary';
                                        break;
                                    case 0:
                                        $url = base_url('/catalog/catalogar/metadadoSearch/' . urlencode($obra['id_i'] ?? ''));
                                        break;
                                    default:
                                        $url = base_url('/catalog/catalogar/no_action/' . urlencode($obra['id_i'] ?? ''));
                                }
                                ?>
                                <a href="<?= $url; ?>" class="btn btn-sm btn-outline-success mb-1" title="Continuar"><i class="bi bi-arrow-right-circle"></i></a>
                                <?php if (isset($obra['i_status']) && $obra['i_status'] < 5): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="excluirExemplar(<?= (int)$obra['id_i'] ?>, this)"><i class="bi bi-trash"></i></button>
                                <?php endif; ?>
                                <script>
                                    function excluirExemplar(id, btn) {
                                        if (!confirm('Tem certeza que deseja excluir este exemplar?')) return;
                                        btn.disabled = true;
                                        fetch('/catalog/catalogar/excluir', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded',
                                                    'X-Requested-With': 'XMLHttpRequest',
                                                },
                                                body: 'id=' + encodeURIComponent(id)
                                            })
                                            .then(response => response.text())
                                            .then(html => {
                                                // Recarrega a página para atualizar a lista e mensagens
                                                window.location.reload();
                                            })
                                            .catch(() => {
                                                alert('Erro ao excluir exemplar.');
                                                btn.disabled = false;
                                            });
                                    }
                                </script>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Nenhuma obra encontrada para este status.</div>
    <?php endif; ?>
    <a href="<?= base_url('/catalog/catalogar/isbn') ?>" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left me-2"></i>Voltar</a>
</div>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>