    <form method="post">
        <div class="mb-3">
            <label for="marc21" class="form-label">Insira o texto MARC21:</label>
            <textarea id="marc21" name="marc21" class="form-control bg-secondary text-light border-secondary"
                style="height: 600px; font-family: monospace;"><?= get("marc21"); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>