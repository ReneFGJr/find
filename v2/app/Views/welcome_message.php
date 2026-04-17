<?= view('layout/header', ['title' => 'FIND • Início']); ?>
<?= view('layout/navbar'); ?>

<main class="container py-5">
    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-<?= esc(session()->getFlashdata('msg_type') ?? 'info'); ?> mb-4"><?= esc(session()->getFlashdata('msg')); ?></div>
    <?php endif; ?>

    <section class="hero-section p-4 p-lg-5 mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge text-bg-primary mb-3 px-3 py-2">Biblioteca digital • descoberta • acesso</span>
                <h1 class="display-5 fw-bold mb-3">Uma experiência mais bonita para o projeto FIND</h1>
                <p class="lead text-secondary mb-4">
                    Explore acervos, fortaleça redes de conhecimento e encontre conteúdos com uma interface moderna,
                    responsiva e preparada para crescer com o projeto.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#recursos" class="btn btn-primary btn-lg">Ver recursos</a>
                    <a href="#parceiros" class="btn btn-outline-primary btn-lg">Ver parceiros</a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <img src="<?= base_url('img/logo/logo_find_big.png'); ?>" alt="Logo FIND" class="hero-logo img-fluid">
            </div>
        </div>
    </section>

    <section id="recursos" class="mb-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body p-4">
                        <h3 class="h5">🔎 Busca intuitiva</h3>
                        <p class="text-secondary mb-0">Uma entrada clara para localizar coleções, obras e referências com mais agilidade.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body p-4">
                        <h3 class="h5">🏛️ Identidade visual</h3>
                        <p class="text-secondary mb-0">Os logos do projeto reforçam a presença institucional e a confiança da plataforma.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body p-4">
                        <h3 class="h5">📱 Layout responsivo</h3>
                        <p class="text-secondary mb-0">A navegação funciona melhor em desktop, tablet e celular com Bootstrap ativo.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="parceiros">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h2 class="h3 mb-1">Parceiros e referências</h2>
                <p class="text-secondary mb-0">Elementos visuais já presentes no projeto FIND.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6 col-md-3"><div class="logo-card"><img src="<?= base_url('img/logo/logo_find_big.png'); ?>" alt="FIND"></div></div>
            <div class="col-6 col-md-3"><div class="logo-card"><img src="<?= base_url('img/logo/logo_bn.png'); ?>" alt="BN"></div></div>
            <div class="col-6 col-md-3"><div class="logo-card"><img src="<?= base_url('img/logo/logo_lc.png'); ?>" alt="LC"></div></div>
            <div class="col-6 col-md-3"><div class="logo-card"><img src="<?= base_url('img/logo/logo_google.png'); ?>" alt="Google Books"></div></div>
        </div>
    </section>
</main>

<?= view('layout/footer'); ?>