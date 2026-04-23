<?= view('layout/header', ['title' => 'Índice de Títulos • FIND']) ?>
<?= view('layout/navbar') ?>

<main class="container py-5">
    <h1 class="h3 fw-bold mb-4">
        <i class="bi bi-journal-text me-2"></i>Índice de Títulos
    </h1>

    <p class="text-secondary mb-4">
        Lista alfabética dos títulos disponíveis na biblioteca.
    </p>


    <?php
    // --- Filtro de local (library_place) ---
    $libraryCode = $_GET['library'] ?? ($_COOKIE['library_code'] ?? $_COOKIE['library'] ?? null);
    $selectedPlace = $_GET['place'] ?? '';
    $places = [];
    if ($libraryCode) {
        $LibraryPlaceModel = new \App\Models\Find\Library\LibraryPlace();
        $places = $LibraryPlaceModel->listByLibrary($libraryCode);
    }
    ?>

    <?php if (!empty($places)): ?>
    <form method="get" class="mb-3 row g-2 align-items-center">
        <input type="hidden" name="library" value="<?= htmlspecialchars($libraryCode) ?>">
        <div class="col-auto">
            <label for="place" class="form-label mb-0">Local:</label>
        </div>
        <div class="col-auto">
            <select name="place" id="place" class="form-select" onchange="this.form.submit()">
                <option value="">Todos os locais</option>
                <?php foreach ($places as $place): ?>
                    <option value="<?= $place['id_lp'] ?>" <?= $selectedPlace == $place['id_lp'] ? 'selected' : '' ?>>
                        <?= esc($place['lp_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <?php endif; ?>

    <?php if (!empty($indexes) && is_array($indexes)): ?>

        <!-- 🔍 Busca + Letras (vertical) -->
        <div class="row mb-3">
            <div class="col-md-2 col-12 mb-3 mb-md-0">
                <div class="nav flex-md-column flex-row nav-pills gap-1" role="tablist" id="tabIndex">
                    <?php $first = true; ?>
                    <?php foreach ($indexes as $letra => $titulos): ?>
                        <?php $letra_id = preg_replace('/[^a-zA-Z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $letra)); ?>
                        <button
                            class="btn btn-outline-primary letra-tab<?= $first ? ' active' : '' ?>"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-pane-<?= esc($letra_id) ?>"
                            type="button"
                            style="width:100%;min-width:38px;">
                            <?= esc($letra) ?>
                        </button>
                        <?php $first = false; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-10 col-12">
                <!-- Busca removida -->
                <!-- Conteúdo das tabs e listas será renderizado abaixo -->
                <div class="tab-content border rounded shadow-sm p-3 bg-white">
                    <?php $first = true; ?>
                    <?php foreach ($indexes as $letra => $titulos): ?>
                        <?php $letra_id = preg_replace('/[^a-zA-Z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $letra)); ?>
                        <div class="tab-pane fade<?= $first ? ' show active' : '' ?>" id="tab-pane-<?= esc($letra_id) ?>">
                            <ul class="list-group title-list">
                                <?php foreach ($titulos as $item): ?>
                                    <li class="list-group-item">
                                        <a href="<?= base_url('item/' . esc($item['ID'])) ?>">
                                            <strong><?= esc($item['label']) ?></strong>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <!-- 📌 Paginação -->
                            <nav class="mt-3">
                                <ul class="pagination justify-content-center mb-0"></ul>
                                <select class="form-select d-none page-select mt-2" style="max-width:120px;margin:auto;"></select>
                            </nav>
                        </div>
                        <?php $first = false; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 📄 Conteúdo -->
        <div class="tab-content border rounded shadow-sm p-3 bg-white">

            <?php $first = true; ?>
            <?php foreach ($indexes as $letra => $titulos): ?>

                <div class="tab-pane fade<?= $first ? ' show active' : '' ?>"
                     id="tab-pane-<?= esc($letra) ?>">

                    <ul class="list-group title-list">
                        <?php foreach ($titulos as $item): ?>
                            <li class="list-group-item-2">
                                <a href="<?= base_url('item/' . esc($item['ID'])) ?>" target="_blank">
                                    <strong><?= esc($item['label']) ?></strong>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- 📌 Paginação -->
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center mb-0"></ul>
                    </nav>

                </div>

                <?php $first = false; ?>
            <?php endforeach; ?>

        </div>

        <!-- ================= JS ================= -->
        <script>
        const ITEMS_PER_PAGE = 20;
        const state = new WeakMap();

        function paginateList(ul, page = 1, searchTerm = '') {

            const items = Array.from(ul.querySelectorAll('li'));
        <script>
        const ITEMS_PER_PAGE = 20;
        const state = new WeakMap();

        function paginateList(ul, page = 1, searchTerm = '') {
            const items = Array.from(ul.querySelectorAll('li'));
            state.set(ul, { page, searchTerm });
            let filtered = items;
            if (searchTerm) {
                filtered = items.filter(li =>
                    li.textContent.toLowerCase().includes(searchTerm)
                );
            }
            items.forEach(li => li.style.display = 'none');
            const start = (page - 1) * ITEMS_PER_PAGE;
            const end = start + ITEMS_PER_PAGE;
            filtered.slice(start, end).forEach(li => {
                li.style.display = '';
            });
            // mensagem
            let msg = ul.parentElement.querySelector('.no-results-msg');
            if (filtered.length === 0) {
                if (!msg) {
                    msg = document.createElement('div');
                    msg.className = 'alert alert-warning mt-3 no-results-msg';
                    msg.innerText = 'Nenhum título encontrado.';
                    ul.parentElement.appendChild(msg);
                }
            } else if (msg) {
                msg.remove();
            }
            // paginação
            const nav = ul.parentElement.querySelector('.pagination');
            nav.innerHTML = '';
            const select = ul.parentElement.querySelector('.page-select');
            select.innerHTML = '';
            const totalPages = Math.ceil(filtered.length / ITEMS_PER_PAGE);
            if (totalPages <= 1) {
                select.classList.add('d-none');
                return;
            }
            // anterior
            const prev = document.createElement('li');
            prev.className = 'page-item' + (page === 1 ? ' disabled' : '');
            prev.innerHTML = `<a class="page-link" href="#">Anterior</a>`;
            prev.onclick = (e) => {
                e.preventDefault();
                if (page > 1) paginateList(ul, page - 1, searchTerm);
            };
            nav.appendChild(prev);
            // páginas (máx 5 visíveis)
            let startPage = Math.max(1, page - 2);
            let endPage = Math.min(totalPages, page + 2);
            if (page <= 3) endPage = Math.min(5, totalPages);
            if (page >= totalPages - 2) startPage = Math.max(1, totalPages - 4);
            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = 'page-item' + (i === page ? ' active' : '');
                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = '#';
                a.innerText = i;
                a.onclick = (e) => {
                    e.preventDefault();
                    paginateList(ul, i, searchTerm);
                };
                li.appendChild(a);
                nav.appendChild(li);
            }
            // selectbox para todas as páginas se > 5
            if (totalPages > 5) {
                select.classList.remove('d-none');
                for (let i = 1; i <= totalPages; i++) {
                    const opt = document.createElement('option');
                    opt.value = i;
                    opt.text = 'Página ' + i;
                    if (i === page) opt.selected = true;
                    select.appendChild(opt);
                }
                select.onchange = function() {
                    paginateList(ul, parseInt(this.value), searchTerm);
                };
            } else {
                select.classList.add('d-none');
            }
            // próximo
            const next = document.createElement('li');
            next.className = 'page-item' + (page === totalPages ? ' disabled' : '');
            next.innerHTML = `<a class="page-link" href="#">Próximo</a>`;
            next.onclick = (e) => {
                e.preventDefault();
                if (page < totalPages) paginateList(ul, page + 1, searchTerm);
            };
            nav.appendChild(next);
        }
        // inicialização
        document.querySelectorAll('.title-list').forEach(ul => {
            paginateList(ul, 1, '');
        });
        // Busca removida
        // troca de aba
        document.querySelectorAll('.letra-tab').forEach(btn => {
            btn.addEventListener('click', function () {
                setTimeout(() => {
                    const activePane = document.querySelector('.tab-pane.active');
                    const ul = activePane.querySelector('.title-list');
                    paginateList(ul, 1, '');
                }, 100);
            });
        });
        </script>
            font-weight: bold;
        <style>
        .letra-tab.active {
            background: #0d6efd;
            color: #fff;
            font-weight: bold;
        }
        .letra-tab {
            min-width: 38px;
            margin-bottom: 2px;
            margin-right: 0;
        }
        @media (min-width: 768px) {
            .letra-tab { width: 100%; }
        }
        .title-list a {
            text-decoration: none;
            color: #0d6efd;
        }
        .title-list a:hover {
            text-decoration: underline;
        }
        .page-select { max-width: 120px; margin: auto; }
        </style>
    <?php else: ?>
        <div class="alert alert-info">Nenhum título encontrado.</div>
    <?php endif; ?>

</main>

<?= view('layout/footer') ?>