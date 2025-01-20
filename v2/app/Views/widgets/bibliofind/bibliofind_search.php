    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="<?= base_url('/bibliofind/buscar'); ?>" method="get">
                    <div class="input-group">
                        <input
                            type="text"
                            name="term"
                            class="form-control bg-dark text-light border-secondary"
                            placeholder="Digite o nome da obra"
                            aria-label="Nome da Obra"
                            value="<?= get("term"); ?>"
                            required>
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>