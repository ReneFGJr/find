<?php
$xclass = $xdata['c_class'];
$rangeRaw = $xdata['form_range'] ?? '';
$rangeValues = is_array($rangeRaw) ? $rangeRaw : json_decode((string) $rangeRaw, true);
if (!is_array($rangeValues)) {
    $rangeValues = preg_split('/[|,;]/', str_replace(['[', ']', '"', "'"], '', (string) $rangeRaw));
}
$rangeValues = array_map(static fn($value) => strtoupper(trim((string) $value)), $rangeValues ?: []);
$isLiteralRange = in_array('132', $rangeValues, true)
    || in_array('TEXT', $rangeValues, true)
    || in_array('LITERAL', $rangeValues, true);

foreach ($form as $key => $ydata) {
    if ($ydata['c_class'] == $xclass) {
        /********** Mostra Text */
        if ($isLiteralRange || $ydata['n_type'] === 'TEXT') {
            require("rdf_show_text.php");
        /********** Mostra Concept */
        } elseif ($ydata['n_type'] === 'CONCEPT') {
            require("rdf_show_concept.php");
        }
    }
}
