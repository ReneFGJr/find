<?php
$type = $xdata['n_type'];
$formID = $xdata['id_form'];
$idP = $xdata['id_c'];
$rangeRaw = (string) ($xdata['form_range'] ?? '');
$range = str_replace(['"','[',']'], '', $rangeRaw);
$range = str_replace(',', '|', $range);

// Quando o RANGE for TEXT (ou classe literal 132), força inclusão de literal.
$rangeTokens = array_filter(array_map('trim', explode('|', $range)));
$rangeTokensUpper = array_map('strtoupper', $rangeTokens);
$isTextRange = in_array('132', $rangeTokens, true)
	|| in_array('TEXT', $rangeTokensUpper, true)
	|| in_array('LITERAL', $rangeTokensUpper, true);

$addType = $isTextRange ? 'TEXT' : $type;
?>
<span class="btn btn-outline-primary supersmall p-1" onclick="formAddNewData('<?= $idC; ?>','<?= $idP; ?>','<?= $addType ?>','<?= $formID ?>','<?= $range; ?>');">+</span>