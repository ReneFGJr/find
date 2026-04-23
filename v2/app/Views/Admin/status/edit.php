<?= view('layout/header', ['title' => 'Editar Status • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <h1 class="h3 fw-bold mb-4"><i class="bi bi-pencil me-2"></i>Editar Status</h1>
    <form method="post">
        <div class="mb-3">
            <label for="is_name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="is_name" name="is_name" value="<?= esc($status['is_name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="is_description" class="form-label">Descrição</label>
            <input type="text" class="form-control" id="is_description" name="is_description" value="<?= esc($status['is_description'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="is_color" class="form-label">Cor</label>
            <input type="color" class="form-control form-control-color" id="is_color" name="is_color" value="<?= esc($status['is_color'] ?? '#ffffff') ?>">
        </div>
        <div class="mb-3">
            <label for="is_order" class="form-label">Ordem</label>
            <input type="number" class="form-control" id="is_order" name="is_order" value="<?= esc($status['is_order'] ?? 0) ?>">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?= !empty($status['is_active']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Ativo</label>
        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Salvar</button>
        <a href="<?= base_url('admin/status'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</main>

<?= view('layout/footer'); ?>
