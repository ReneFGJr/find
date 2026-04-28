<?php
$type = $xdata['n_type'];
$formID = $xdata['id_form'];
$idP = $xdata['id_c'];
$range = $xdata['form_range'];
$range = str_replace(['"','[',']'], '', $range);
$range = str_replace(',', '|', $range);
?>
<span class="btn btn-outline-primary supersmall p-1" onclick="formAddNewData('<?= $idC; ?>','<?= $idP; ?>','<?= $type ?>','<?= $formID ?>','<?= $range; ?>');">+</span>