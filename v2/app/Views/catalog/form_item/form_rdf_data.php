<?php
$xclass = $xdata['c_class'];
foreach ($form as $key => $ydata) {
    if ($ydata['c_class'] == $xclass) {
        /********** Mostra Concept */
        if ($ydata['n_type'] === 'CONCEPT') {
            require("rdf_show_concept.php");
            /********** Mostra Text */
        } elseif ($ydata['n_type'] === 'TEXT') {
            require("rdf_show_text.php");
        }
    }
}
