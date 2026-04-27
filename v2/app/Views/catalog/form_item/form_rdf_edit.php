<h3><?= htmlspecialchars($f) ?></h3>
<form method="get" action="">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th width="20%">Propriedade</th>
                <th style="width:20px;"></th>

                <?php
                // Exibe o formulário RDF se existir
                if (!empty($form)) {
                    echo '<form method="get" action="">';
                    echo '<table class="table table-bordered align-middle">';
                    echo '<thead class="table-light">';
                    echo '<tr><th width="20%">Propriedade</th><th style="width:20px;"></th><th width="76%">Valor</th></tr>';
                    echo '</thead><tbody>';
                    $lastGroup = null;
                    $propLabel = '';
                    foreach ($form as $i => $w) {
                        if (($w['n_type'] === 'CONCEPT') && ($w['n_name'] != '')) {
                            $w['n_type'] = 'CONCEPT:EXIST';
                        }
                        if (empty($w['c_class'])) continue;
                        if ($lastGroup !== $w['form_group']) {
                            if ($w['form_group'] != '') {
                                echo '<tr class="table-secondary"><td colspan="3" class="bg-secondary text-center"><strong>' . htmlspecialchars($w['form_group']) . '</strong></td></tr>';
                            }
                            $lastGroup = $w['form_group'];
                        }
                        echo '<tr>';
                        echo '<td>';
                        $xpropLabel = $w['c_class'] ?? '';
                        if ($xpropLabel !== $propLabel) {
                            echo '<span title="' . htmlspecialchars($w['c_class']) . '">';
                            echo htmlspecialchars($w['c_class']);
                            echo '</span>';
                            $propLabel = $xpropLabel;
                        }
                        echo '</td>';
                        // Botões de ação
                        echo '<td class="text-center"><nobr>';
                        switch ($w['n_type']) {
                            case 'TEXT':
                                if ($w['n_name'] == '') {
                                    echo '<button type="button" class="btn btn-outline-success btn-sm btn-adicionar-literal" title="Adicionar" data-prop="' . htmlspecialchars($w['c_class'] ?? '') . '" data-group="' . htmlspecialchars($w['form_group'] ?? '') . '" data-type="' . htmlspecialchars($w['n_type'] ?? '') . '" data-range="' . htmlspecialchars($w['form_range'] ?? '') . '"><i class="bi bi-plus"></i></button>';
                                }
                                if ($w['n_name'] != '') {
                                    echo '<button type="button" class="btn btn-outline-danger btn-sm" title="Excluir" data-id_d="' . htmlspecialchars($w['id_d'] ?? '') . '"><i class="bi bi-trash"></i></button>';
                                    echo '<button type="button" class="btn btn-sm btn-outline-primary ms-1 btn-editar-literal" id="btn-editar-literal-' . htmlspecialchars($w['id_n'] ?? '') . '" data-idn="' . htmlspecialchars($w['id_n'] ?? '') . '" data-value="' . htmlspecialchars($w['n_name'] ?? '') . '" title="Editar texto"><i class="bi bi-pencil"></i></button>';
                                }
                                break;
                            case 'CONCEPT':
                                echo '<button type="button" class="btn btn-outline-success btn-sm btn-adicionar-atributo" title="Adicionar" data-idc="' . htmlspecialchars($id ?? '') . '" data-prop="' . htmlspecialchars($w['c_class'] ?? '') . '" data-group="' . htmlspecialchars($w['form_group'] ?? '') . '" data-type="' . htmlspecialchars($w['n_type'] ?? '') . '" data-value="' . htmlspecialchars($w['n_name'] ?? '') . '" data-range="' . htmlspecialchars($w['form_range'] ?? '') . '"><i class="bi bi-plus"></i></button>';
                                break;
                            case 'CONCEPT:EXIST':
                                echo '<button type="button" class="btn btn-outline-danger btn-sm" title="Excluir" data-id_d="' . htmlspecialchars($w['id_d'] ?? '') . '"><i class="bi bi-trash"></i></button>';
                                break;
                        }
                        echo '</nobr></td>';
                        echo '<td>' . htmlspecialchars($w['n_name'] ?? '');
                        if (!empty($w['n_lang'])) {
                            echo ' <span class="badge bg-secondary ms-1">' . htmlspecialchars($w['n_lang']) . '</span>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table></form>';
                }
                ?>
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarLiteral">Salvar</button>
                </div>
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