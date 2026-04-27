<h3><?= htmlspecialchars($f) ?></h3>
<div class="card-header bg-primary text-white">
    <i class="bi bi-pencil-square me-2"></i> Edição de Metadados RDF
</div>
<form method="get" action="">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th width="20%">Propriedade</th>
                <th style="width:20px;"></th>
                <th width="80%">Valor</th>
            </tr>
        </thead>

        <?php if (!empty($form)): ?>
            <tbody>
                <?php
                $lastGroup = null;
                $prop = null;
                $propLabel = '';

                foreach ($form as $i => $w):

                    if (empty($w['c_class'])) continue;

                    if ($lastGroup !== $w['form_group']):
                        if ($w['form_group'] != ''):
                            echo '<tr class="table-secondary">';
                            echo '<td colspan="3" class="bg-secondary text-center"><strong>';
                            echo htmlspecialchars($w['form_group']);
                            echo '</strong></td>';
                            echo '</tr>';
                        endif;
                        $lastGroup = $w['form_group'];
                    endif;

                    /***************************************************  */
                    if ($prop != $w['c_class']) {
                        echo '<tr>';
                        echo '  <td  class="text-end">' . $w['c_class'] . '</td>';

                        echo '<td class="text-end">';

                        switch ($w['n_type']) {
                            case 'TEXT':
                                if ($w['n_name'] == ''): ?>
                                    <button type="button" class="btn btn-outline-success btn-sm btn-adicionar-literal" title="Adicionar valor literal" data-bs-toggle="tooltip" data-prop="<?= htmlspecialchars($w['c_class'] ?? '') ?>" data-group="<?= htmlspecialchars($w['form_group'] ?? '') ?>" data-type="<?= htmlspecialchars($w['n_type'] ?? '') ?>" data-range="<?= htmlspecialchars($w['form_range'] ?? '') ?>">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                <?php endif;
                                if ($w['n_name'] != ''): ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm" title="Excluir valor" data-bs-toggle="tooltip" data-id_d="<?= htmlspecialchars($w['id_d'] ?? '') ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm ms-1 btn-editar-literal" id="btn-editar-literal-<?= htmlspecialchars($w['id_n'] ?? '') ?>" data-idn="<?= htmlspecialchars($w['id_n'] ?? '') ?>" data-value="<?= htmlspecialchars($w['n_name'] ?? '') ?>" title="Editar valor literal" data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                <?php endif;
                                break;
                            case 'CONCEPT': ?>
                                <nobr>
                                    <?php if ($prop != $w['c_class']): ?>
                                        <button type="button" class="btn btn-outline-success btn-sm btn-adicionar-atributo" title="Adicionar atributo" data-bs-toggle="tooltip" data-idc="<?= htmlspecialchars($id ?? '') ?>" data-prop="<?= htmlspecialchars($w['c_class'] ?? '') ?>" data-group="<?= htmlspecialchars($w['form_group'] ?? '') ?>" data-type="<?= htmlspecialchars($w['n_type'] ?? '') ?>" data-value="<?= htmlspecialchars($w['n_name'] ?? '') ?>" data-range="<?= htmlspecialchars($w['form_range'] ?? '') ?>">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </nobr>
                <?php break;
                        }
                        echo '</td>';
                        echo '<td>';

                        /******************************** Mostra as subclasses */
                        $prop  = $w['c_class'];
                        $total_i = 0;

                        foreach ($form as $i2 => $w2) {
                            if ($prop == $w2['c_class']) {
                                if ($w2['n_name'] != '') {
                                    if ($total_i > 0) {
                                        echo '<br>';
                                    }
                                    $total_i++;
                                    echo '
                                    <button type="button"
                                                class="btn btn-outline-danger btn-sm me-2 btn-excluir-conceito"
                                                title="Excluir conceito"
                                                onclick="excluirConceito('.$w2['id_d'].');">
                                        <i class="bi bi-trash"></i>
                                    </button>';
                                    echo '<strong>' . htmlspecialchars($w2['n_name'] ?? '') . '</strong>';
                                    if (!empty($w2['n_lang'])):
                                        echo '<span class="badge bg-secondary ms-2">' . htmlspecialchars($w2['n_lang']) . '</span>';
                                    endif;
                                }
                            }
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                endforeach; ?>
            </tbody>
            <tr>
                <td colspan=3><?php pre($form, false); ?></td>
            </tr>
    </table>
</form>
<?php endif; ?>

</form>


<!-- Offcanvas para adicionar atributo (fora do loop) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAdicionarAtributo" aria-labelledby="offcanvasAdicionarAtributoLabel" style="width:400px;max-width:100vw;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasAdicionarAtributoLabel">Adicionar atributo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php include(APPPATH . 'Views/find/rdf/form/rdf_concept_attribute.php'); ?>
    </div>
</div>

<?php if (!isset($load_scripts)) {
    $load_scripts = true; // Evita recarregar os scripts se a view for incluída várias vezes
?>
    <script>
        // Função para capturar o clique no botão Excluir conceito
        function excluirConceito(id_d) {
            if (confirm('Tem certeza que deseja excluir este conceito ('+id_d+')?')) {
                $.ajax({
                    url: '<?= base_url('/rdf/form/excluir_rdf_data'); ?>',
                    type: 'POST',
                    data: {
                        id_d: id_d
                    },
                    success: function(response) {
                        // Sucesso: recarrega a página ou atualiza a tabela
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Erro ao excluir conceito: ' + error);
                    }
                });
            }
        }
    </script>
<?php } ?>