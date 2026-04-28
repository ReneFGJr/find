<?php
if ($ydata['n_name'] != '') {
?>
    <i class="bi bi-trash btn btn-outline-warning p-1 supersmall mb-2" onclick="deleteItem(<?= $ydata['id_d']; ?>);"></i>
    <i class="bi bi-pencil btn btn-outline-danger p-1 supersmall mb-2" onclick="editItem(<?= $ydata['id_d']; ?>);"></i>
    <?= $ydata['n_name'] ?>
    <span class="text-muted supersmall mb-2"><sup class="small">[<?= $ydata['n_lang'] ?>]</sup></span>
    <br>
<?php } ?>
