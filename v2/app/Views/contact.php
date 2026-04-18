<?php include(APPPATH.'Views/layout/header.php'); ?>
<?php include(APPPATH.'Views/layout/navbar.php'); ?>
<div class="container my-5" style="max-width:600px;">
    <h1>Contato</h1>
    <form method="post" action="">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="mensagem" class="form-label">Mensagem</label>
            <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
    <?php if (isset($enviado) && $enviado): ?>
        <div class="alert alert-success mt-3">Mensagem enviada com sucesso!</div>
    <?php endif; ?>
</div>
<?php include(APPPATH.'Views/layout/footer.php'); ?>