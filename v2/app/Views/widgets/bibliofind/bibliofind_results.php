<div class="container">
    <div class="row">
        <div class="col-12">
        Recuperado :<?=count($data);?> documentos.</div>

        <?php
        foreach ($data as $id => $line) {
            echo bsc($line['d_doc'], 1,'text-center');
            echo bsc($line['w_TITLE'], 10);
            echo bsc(number_format($line['score'],4,','), 1);
        }
        ?>
    </div>
</div>