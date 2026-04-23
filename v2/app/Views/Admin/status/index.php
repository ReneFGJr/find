<?= view('layout/header', ['title' => 'Status • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <h1 class="h3 fw-bold mb-4"><i class="bi bi-list-check me-2"></i>Status dos Itens</h1>
    <a href="<?= base_url('admin/status/create'); ?>" class="btn btn-success mb-3"><i class="bi bi-plus-circle me-1"></i> Novo Status</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Cor</th>
                <th>Ordem</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($statusList as $status): ?>
                <tr>
                    <td><?= esc($status['id_is']) ?></td>
                    <td><?= esc($status['is_name']) ?></td>
                    <td><?= esc($status['is_description']) ?></td>
                    <td><span style="background:<?= esc($status['is_color']) ?>;padding:2px 10px;border-radius:4px;display:inline-block;"></span> <?= esc($status['is_color']) ?></td>
                    <td><?= esc($status['is_order']) ?></td>
                    <td><?= esc($status['is_active']) ? 'Sim' : 'Não' ?></td>
                    <td>
                        <a href="<?= base_url('admin/status/edit/'.$status['id_is']) ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                        <a href="<?= base_url('admin/status/delete/'.$status['id_is']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este status?');"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?= view('layout/footer'); ?>
