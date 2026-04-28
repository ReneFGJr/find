<!-- Offcanvas lateral para adicionar dado -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddData" aria-labelledby="offcanvasAddDataLabel" style="width:600px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasAddDataLabel">Adicionar Dado</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body p-0" style="height:100%;">
        <iframe id="iframeAddData" src="" style="border:0;width:100%;height:100%;min-height:400px;"></iframe>
    </div>
</div>

<script>
    function formAddNewData(idC, type, formID) {

        if (type == 'TEXT') {
            var url = '/catalog/rdf/text_add?idC='+idC+'&type=' + encodeURIComponent(type) + '&formID=' + encodeURIComponent(formID);
            document.getElementById('iframeAddData').src = url;
            // Abre o painel lateral
            var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddData'));
            offcanvas.show();
            return;
        }

        if (type == 'CONCEPT') {
            alert('Adicionar conceito: ' + type + ' para o formID: ' + formID);
            var url = '/catalog/rdf/concept_add?type=' + encodeURIComponent(type) + '&formID=' + encodeURIComponent(formID);
            document.getElementById('iframeAddData').src = url;
            // Abre o painel lateral
            var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasAddData'));
            offcanvas.show();
        }
        // Monta a URL do iframe
    }

    /****************************** Excluir */
    function deleteItem(id_d) {
        if (confirm('Tem certeza que deseja excluir este dado?')) {
            $.post('/rdf/form/excluir_rdf_data', { id_d: id_d }, function(response) {
                alert(response.message);
                // Recarrega a página para atualizar os dados
                location.reload();
            });
        }
    }
</script>

####### Carga do JavaScript para o formulário RDF Plus (form_rdf_plus.php) #######