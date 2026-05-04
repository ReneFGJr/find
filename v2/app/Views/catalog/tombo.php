<?= view('layout/header', ['title' => esc($book['title'] ?? 'Item') . ' • FIND']); ?>
<?= view('layout/navbar'); ?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <h3 class="mb-4">Consulta por Tombo</h3>
        </div>
        <div class="col-md-2">
            <form method="get" action="">
                <div class="mb-3">
                    <label for="numeroTombo" class="form-label">Número do Tombo</label>
                    <input type="text" class="form-control" id="numeroTombo" name="numeroTombo" placeholder="Digite o número do tombo" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </form>

        </div>
        <div class="col-md-10">
            <?php
            if (isset($library)) {
                echo  view('Libraries/item_details', [
                    'book' => $book,
                    'library' => $library,
                    'itemInfo' => $row,
                    'meta' => $meta
                ]);
            }
            ?>
            <?php
            echo $msg;
            ?>
        </div>
    </div>
</div>
<?= view('layout/footer'); ?>

<script>
    document.getElementById('numeroTombo').focus();
</script>