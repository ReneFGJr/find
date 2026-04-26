<?= view('layout/header', ['title' => 'Detalhes da Autoridade • FIND']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-4">
    <h1 class="mb-4">Detalhes da Autoridade</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Autoridade</h4>
            <?php if (!empty($Authority['concept'])): ?>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>ID:</strong> <?= esc($Authority['concept']['id'] ?? '') ?></li>
                    <li class="list-group-item"><strong>Nome:</strong> <?= esc($Authority['concept']['name'] ?? '') ?></li>
                    <li class="list-group-item"><strong>Classe:</strong> <?= esc($Authority['concept']['Class'] ?? '') ?></li>
                    <li class="list-group-item"><strong>Idioma:</strong> <?= esc($Authority['concept']['lang'] ?? '') ?></li>
                    <li class="list-group-item"><strong>Tipo:</strong> <?= esc($Authority['concept']['type'] ?? '') ?></li>
                    <li class="list-group-item"><strong>Uso:</strong> <?= esc($Authority['concept']['use'] ?? '') ?></li>
                </ul>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h4>Remissiva</h4>
            <button class="btn btn-sm btn-primary mb-2" type="button" id="btnRemissive">Incluir remissiva</button>

            <!-- Offcanvas painel lateral -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRemissive" aria-labelledby="offcanvasRemissiveLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasRemissiveLabel">Incluir Remissiva</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar" id="closeRemissive"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <iframe id="iframeRemissive" src="" style="border:0;width:100%;height:80vh;"></iframe>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var btn = document.getElementById('btnRemissive');
                    var offcanvas = document.getElementById('offcanvasRemissive');
                    var iframe = document.getElementById('iframeRemissive');
                    var closeBtn = document.getElementById('closeRemissive');
                    btn && btn.addEventListener('click', function() {
                        // Recupera o id_cc da autoridade principal
                        var id_cc = <?= json_encode($Authority['concept']['id'] ?? '') ?>;
                        iframe.src = '/catalog/authority/form_remissive?id_cc=' + id_cc;
                        var bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
                        bsOffcanvas.show();
                    });
                    closeBtn && closeBtn.addEventListener('click', function() {
                        location.reload();
                    });
                });
            </script>
            <?php
            // Se AuthorityRemissive['concept'] for uma lista de arrays, exibe todos
            if (!empty($AuthorityRemissive)) {
                echo '<ul class="list-group mb-3">';
                foreach ($AuthorityRemissive as $rem): ?>
                        <li class="list-group-item"><?= esc($rem['name'] ?? '') ?> <sup> <?= esc($rem['id'] ?? '') ?></sup></li>
            <?php endforeach;
            echo '</ul>';
            }
            ?>
        </div>
    </div>

    <h4>Dados Relacionados</h4>
    <?php if (!empty($Authority['data'])): ?>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Propriedade</th>
                    <th>Tipo</th>
                    <th>ID</th>
                    <th>Uso</th>
                    <th>Idioma</th>
                    <th>Legenda</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Authority['data'] as $d): ?>
                    <tr>
                        <td><?= esc($d['Property'] ?? '') ?></td>
                        <td><?= esc($d['type'] ?? '') ?></td>
                        <td><?= esc($d['ID'] ?? '') ?></td>
                        <td><?= esc($d['use'] ?? '') ?></td>
                        <td><?= esc($d['Lang'] ?? '') ?></td>
                        <td><?= esc($d['Caption'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">Nenhum dado relacionado encontrado.</div>
    <?php endif; ?>
</main>

<?= view('layout/footer'); ?>