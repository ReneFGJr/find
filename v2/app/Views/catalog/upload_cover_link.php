<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <form action="<?= base_url('catalog/upload_cover_link') ?>" method="post" enctype="multipart/form-data">
                <!-- Botão de ícone para link -->
                <button class="btn btn-outline-primary mb-3" type="button" id="btnShowUrlInput" title="Adicionar Link">
                    <i class="bi bi-link-45deg"></i> Adicionar Link
                </button>

                <!-- Área oculta para input de URL e upload -->
                <div id="urlInputArea" style="display:none;">
                    <div class="input-group mb-2">
                        <input type="hidden" name="isbn" value="<?= esc($isbn); ?>">
                        <input type="text" class="form-control" id="inputUrl" name="inputUrl" placeholder="Informe a URL do link" required>
                        <button class="btn btn-success" type="submit" id="btnUploadUrl">Upload</button>
                    </div>
                    <div id="msgArea" class="alert alert-info" style="display:none;"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Mostra/oculta área de input ao clicar no botão
    $('#btnShowUrlInput').on('click', function() {
        $('#urlInputArea').toggle();
        $('#msgArea').hide();
    });

    // Ação do botão de upload
    $('#btnUploadUrl').on('click', function() {
        var url = $('#inputUrl').val().trim();
        if (!url) {
            $('#msgArea').removeClass('alert-info').addClass('alert-danger').text('Por favor, informe uma URL.').show();
            return;
        }
        // Aqui você pode implementar a lógica de upload via AJAX ou outra ação
        $('#msgArea').removeClass('alert-danger').addClass('alert-info').text('URL enviada: ' + url).show();
    });
</script>