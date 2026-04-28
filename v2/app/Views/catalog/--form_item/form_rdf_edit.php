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

                foreach ($form as $w):

                    // Ignora itens sem classe
                    if (empty($w['c_class'])) continue;

                    /* ===============================
                       GRUPO
                    =============================== */
                    if ($lastGroup !== $w['form_group']):
                        if (!empty($w['form_group'])):
                ?>
                            <tr class="table-secondary">
                                <td colspan="3" class="bg-secondary text-center">
                                    <strong><?= htmlspecialchars($w['form_group']) ?></strong>
                                </td>
                            </tr>
                        <?php
                        endif;
                        $lastGroup = $w['form_group'];
                    endif;

                    /* ===============================
                       NOVA PROPRIEDADE
                    =============================== */
                    if ($prop !== $w['c_class']):
                        $prop = $w['c_class'];
                        ?>
                        <tr>
                            <!-- Propriedade -->
                            <td class="text-end">
                                <?= htmlspecialchars($w['c_class']) ?>
                            </td>

                            <!-- Ações -->
                            <td class="text-end align-top">
                                <?php if ($w['n_type'] === 'TEXT'): ?>

                                    <?php if (empty($w['n_name'])): ?>
                                        <button type="button"
                                            class="btn btn-outline-success btn-sm btn-adicionar-literal"
                                            title="Adicionar valor literal"
                                            data-bs-toggle="tooltip"
                                            data-prop="<?= htmlspecialchars($w['c_class']) ?>"
                                            data-group="<?= htmlspecialchars($w['form_group']) ?>"
                                            data-type="<?= htmlspecialchars($w['n_type']) ?>"
                                            data-range="<?= htmlspecialchars($w['form_range']) ?>">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            title="Excluir valor"
                                            data-bs-toggle="tooltip"
                                            data-id_d="<?= htmlspecialchars($w['id_d']) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm ms-1 btn-editar-literal"
                                            title="Editar valor literal"
                                            data-bs-toggle="tooltip"
                                            data-idn="<?= htmlspecialchars($w['id_n']) ?>"
                                            data-value="<?= htmlspecialchars($w['n_name']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    <?php endif; ?>

                                <?php elseif ($w['n_type'] === 'CONCEPT'): ?>
                                    <button type="button"
                                        class="btn btn-outline-success btn-sm btn-adicionar-atributo"
                                        title="Adicionar atributo"
                                        data-bs-toggle="tooltip"
                                        data-idc="<?= htmlspecialchars($id ?? '') ?>"
                                        data-prop="<?= htmlspecialchars($w['c_class']) ?>"
                                        data-group="<?= htmlspecialchars($w['form_group']) ?>"
                                        data-type="<?= $w['n_type'] ?? '' ?>"
                                        data-value="<?= $w['n_name'] ?? '' ?>"
                                        data-range="<?= htmlspecialchars($w['form_range']) ?>">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </td>

                            <!-- Valores -->
                            <td class="align-top">
                                <?php
                                $total_i = 0;

                                foreach ($form as $w2):
                                    if ($prop === $w2['c_class'] && !empty($w2['n_name'])):

                                        if ($total_i > 0) echo '<br>';
                                        $total_i++;
                                ?>

                                        <button type="button"
                                            class="btn btn-outline-danger btn-sm me-2 mb-1 btn-excluir-conceito"
                                            title="Excluir conceito"
                                            onclick="excluirConceito(<?= $w2['id_d'] ?>);">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <strong><?= htmlspecialchars($w2['n_name']) ?></strong>

                                        <?php if (!empty($w2['n_lang'])): ?>
                                            <span class="badge bg-secondary ms-2">
                                                <?= htmlspecialchars($w2['n_lang']) ?>
                                            </span>
                                        <?php endif; ?>

                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </td>
                        </tr>
                <?php
                    endif;

                endforeach;
                ?>
            </tbody>
        <?php endif; ?>
    </table>
</form>

<!-- Offcanvas para adicionar atributo (conceito) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAtributo" aria-labelledby="offcanvasAtributoLabel" style="width:600px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasAtributoLabel">Adicionar Atributo</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body p-0" style="height:100%;">
        <iframe id="iframeAtributo" src="" style="border:0;width:100%;height:100%;min-height:400px;"></iframe>
    </div>
</div>

<script>
    //////////////////////////////////////////////////////////////////////////// Adicionar conceito
    $(function() {
        // Handler para abrir o painel lateral ao clicar no botão de adicionar atributo
        $(document).on('click', '.btn-adicionar-atributo', function() {
            var $btn = $(this);
            // Pega todos os data-attributes
            var params = {
                idc: $btn.data('idc'),
                prop: $btn.data('prop'),
                group: $btn.data('group'),
                type: $btn.data('type'),
                value: $btn.data('value'),
                range: $btn.data('range')
            };
            // Monta a query string
            var query = $.param(params);
            // Seta o src do iframe
            $('#iframeAtributo').attr('src', '/catalog/rdf/concept_add?' + query);
            // Abre o offcanvas
            var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAtributo'));
            offcanvas.show();
        });
    });
</script>

<script>
    //////////////////////////////////////////////////////////////////////////// Excluir conceito
    // Função para capturar o clique no botão Excluir conceito
    function excluirConceito(id_d) {
        if (confirm('Tem certeza que deseja excluir este conceito (' + id_d + ')?')) {
            $.ajax({
                url: '<?= base_url('/rdf/form/excluir_rdf_data'); ?>',
                type: 'POST',
                data: {
                    id_d: id_d
                },
                success: function(response) {
                    // Sucesso: recarrega a página ou atualiza a tabela
                    // Salva a aba ativa antes de recarregar
                    var activeTab = $('.nav-tabs .nav-link.active').attr('id');
                    if (activeTab) {
                        localStorage.setItem('activeTab', activeTab);
                    }
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Erro ao excluir conceito: ' + error);
                }
            });
        }
    }
</script>