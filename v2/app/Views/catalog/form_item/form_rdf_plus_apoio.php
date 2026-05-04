<script>
    function formAddNewData(idC, idP, type, formID, range) {

        if (type == 'TEXT') {
            var url = '<?= base_url('/catalog/rdf/text_add'); ?>?idC=' + idC + '&prop=' + idP + '&type=' + encodeURIComponent(type) + '&formID=' + encodeURIComponent(formID) + '&range=' + encodeURIComponent(range);

            document.getElementById('iframeAddData').src = url;
            // Abre o painel lateral
            var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddData'));
            offcanvas.show();
            return;
        }

        if (type == 'CONCEPT') {
            var url = '<?= base_url('/catalog/rdf/concept_add'); ?>?idC=' + idC + '&prop=' + idP + '&type=' + encodeURIComponent(type) + '&formID=' + encodeURIComponent(formID) + '&range=' + encodeURIComponent(range);
            document.getElementById('iframeAddData').src = url;
            // Abre o painel lateral
            var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddData'));
            offcanvas.show();
        }
        // Monta a URL do iframe
    }

    function editItem(id_d) {
        var url = '<?= base_url('/catalog/rdf/text_edit'); ?>?idD=' + id_d;
        document.getElementById('iframeAddData').src = url;
        // Abre o painel lateral
        var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddData'));
        offcanvas.show();
    }

    /****************************** Excluir */
    function deleteItem(id_d) {
        if (confirm('Tem certeza que deseja excluir este dado? ('+id_d+')')) {
            $.post('<?= base_url(); ?>/rdf/form/excluir_rdf_data', {
                id_d: id_d
            }, function(response) {
                alert(response.message);
                // Recarrega a tela na mesma aba (mesmo se estiver em iframe/painel).
                try {
                    if (window.top && window.top.location) {
                        window.top.location.reload();
                        return;
                    }
                } catch (e) {
                    // Ignora e faz fallback local.
                }
                window.location.reload();
            });
        }
    }
</script>

####### Carga do JavaScript para o formulário RDF Plus (form_rdf_plus.php) #######