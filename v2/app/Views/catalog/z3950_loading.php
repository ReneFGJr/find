<?php include(APPPATH . 'Views/layout/header.php'); ?>
<div class="container py-5 text-center">
    <h3 class="mb-3"><i class="bi bi-cloud-download me-2"></i>Consultando Z39.50...</h3>
    <div class="mb-3">
        <span id="timer" class="fs-4 fw-bold">0</span> segundos
    </div>
    <div class="alert alert-info mx-auto" style="max-width: 500px;">
        Aguarde enquanto buscamos os dados do livro na base Z39.50.<br>
        Isso pode levar alguns segundos dependendo da conexão e do serviço externo.
    </div>
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
</div>
<script>
let seconds = 0;
const timerEl = document.getElementById('timer');
setInterval(() => {
    seconds++;
    timerEl.textContent = seconds;
}, 1000);
</script>
<?php include(APPPATH . 'Views/layout/footer.php'); ?>
