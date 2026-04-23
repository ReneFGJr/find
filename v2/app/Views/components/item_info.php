<?php
// Busca nome do status se possível
$statusName = '';
if (!empty($itemInfo['i_status'])) {
    try {
        $statusModel = new \App\Models\Find\Items\Status();
        $status = $statusModel->find($itemInfo['i_status']);
        $statusName = $status['is_name'] ?? '';
    } catch (\Throwable $e) {
        $statusName = '';
    }
}

function linkRDF($id)
{
    if ($id > 0) {
        return '<a href="' . base_url('rdf/form/' . $id) . '" target="_blank" class="link link-secondary"><i class="bi bi-diagram-3 me-1"></i>' . $id . '</a>';
    } else {
        return '-';
    }
}
?>
<?php if (!empty($itemInfo)): ?>
    <div class="mb-4">
        <div class="card shadow-sm border-primary mb-3">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="bi bi-book me-2"></i>
                <span>Informações do Item</span>
                <?php if ($statusName): ?>
                    <span class="badge bg-info ms-auto">Status: <?= htmlspecialchars($statusName) ?></span>
                <?php elseif (!empty($itemInfo['i_status'])): ?>
                    <span class="badge bg-secondary ms-auto">Status: <?= htmlspecialchars($itemInfo['i_status']) ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <th width="10%" class="text-end">Título</th>
                            <td width="50%"><?= htmlspecialchars($itemInfo['i_titulo'] ?? '') ?></td>

                            <th width="10%" class="text-end">ID</th>
                            <td width="30%"><?= htmlspecialchars($itemInfo['id_i'] ?? '') ?></td>

                            <td rowspan="10" class="small">Capa:<br>
                                <img src="<?= cover_image($itemInfo['i_identifier'] ?? '') ?>" alt="Capa" style="width: 100px;">
                            </td>
                        </tr>
                        <tr>
                            <th class="text-end">Autor(es)</th>
                            <td><?= htmlspecialchars($itemInfo['i_autores'] ?? '') ?></td>

                            <th class="text-end">Tombo</th>
                            <td><?= htmlspecialchars($itemInfo['i_tombo'] ?? '') ?></td>


                        </tr>
                        <tr>
                            <th class="text-end" rowspan="5">Etiqueta</th>
                            <td rowspan="5">
                                <?= view('components/label_view', ['itemInfo' => $itemInfo]); ?>
                            </td>

                            <th class="text-end">Exemplar</th>
                            <td><?= htmlspecialchars($itemInfo['i_exemplar'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">ISBN</th>
                            <td><?= htmlspecialchars($itemInfo['i_identifier'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Cadastrado</th>
                            <td><?= htmlspecialchars($itemInfo['i_created'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">IDs</th>
                            <td class="small text-secondary">
                                Work: <?= linkRDF(htmlspecialchars($itemInfo['i_work'] ?? '')) ?></br>
                                Expression: <?= linkRDF(htmlspecialchars($itemInfo['i_expression'] ?? '')) ?></br>
                                Manifestation: <?= linkRDF(htmlspecialchars($itemInfo['i_manifestation'] ?? '')) ?></br>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>