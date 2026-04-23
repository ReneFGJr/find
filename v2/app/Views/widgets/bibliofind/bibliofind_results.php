<div class="container">
    <div class="row">
        <div class="col-12">
        Recuperado :<?=count($data);?> documentos.</div>

        <?php
        foreach ($data as $id => $line) {
            echo ($line['d_doc'] ?? 'ID: ' . $id) . ' - ';
            echo $line['w_TITLE'];
            echo number_format($line['score'], 4, ',', '.') ;
        }
        ?>
    </div>
</div>