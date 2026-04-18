<?php include(APPPATH.'Views/layout/header.php'); ?>
<?php include(APPPATH.'Views/layout/navbar.php'); ?>
<div class="container my-5">
    <h1>FAQ - Perguntas Frequentes</h1>
    <div class="accordion" id="faqAccordion">
        <?php if (!empty($faqs)): ?>
            <?php foreach ($faqs as $i => $faq): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?= $i ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>" aria-expanded="false" aria-controls="collapse<?= $i ?>">
                            <?= esc($faq['faq_question']) ?>
                        </button>
                    </h2>
                    <div id="collapse<?= $i ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $i ?>" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <?= esc($faq['faq_answer']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">Nenhuma FAQ cadastrada.</div>
        <?php endif; ?>
    </div>
</div>
<?php include(APPPATH.'Views/layout/footer.php'); ?>