<?= view('layout/header', ['title' => 'Conceito #' . ($concept['id'] ?? '')]) ?>
<?= view('layout/navbar') ?>

<div class="container mt-4">
    <h2>Conceito #<?= $concept['id'] ?> - <?= htmlspecialchars($concept['concept']['name'] ?? '') ?></h2>
    <div class="card mb-3">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9"><?= $concept['concept']['id'] ?? '' ?></dd>
                <dt class="col-sm-3">Nome</dt>
                <dd class="col-sm-9"><?= htmlspecialchars($concept['concept']['name'] ?? '') ?></dd>
                <dt class="col-sm-3">Classe</dt>
                <dd class="col-sm-9"><?= $concept['concept']['Class'] ?? '' ?></dd>
                <dt class="col-sm-3">Idioma</dt>
                <dd class="col-sm-9"><?= $concept['concept']['lang'] ?? '' ?></dd>
                <dt class="col-sm-3">Tipo</dt>
                <dd class="col-sm-9"><?= $concept['concept']['type'] ?? '' ?></dd>
            </dl>
        </div>
    </div>

    <?php pre($concept, false); ?>

    <h4>Dados Relacionados</h4>
    <?php if (!empty($concept['data'])): ?>
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>Propriedade</th>
                    <th>Legenda</th>
                    <th>Classe</th>
                    <th>Tipo</th>
                    <th>Idioma</th>
                    <th>ID</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($concept['data'] as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['Property'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['Caption'] ?? '') ?></td>
                    <td><?= $d['IDClass'] ?? '' ?></td>
                    <td><?= $d['type'] ?? '' ?></td>
                    <td><?= $d['Lang'] ?? '' ?></td>
                    <td><?= $d['IDd'] ?? '' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">Nenhum dado relacionado encontrado.</div>
    <?php endif; ?>

    <h4>Itens Relacionados</h4>
    <?php if (!empty($concept['items'])): ?>
        <ul>
            <?php foreach ($concept['items'] as $item): ?>
                <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">Nenhum item relacionado.</div>
    <?php endif; ?>
</div>

<?= view('layout/footer') ?>
