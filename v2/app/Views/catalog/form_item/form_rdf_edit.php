<h3><?= htmlspecialchars($f) ?></h3>

<div class="card-header bg-primary text-white">
    <i class="bi bi-pencil-square me-2"></i> Edição de Metadados RDF (<?= htmlspecialchars($idC) ?>)
</div>

<?php
$xgroup = null;
$xpróp = null;
echo '<table class="table table-bordered table-striped">';
echo '<tr class="th text-center small"><td width="20%">Propriedade</td><td width="5%">#</td><td width="75%">Valor</td></tr>';
foreach ($form as $key => $xdata) {
    $group = $xdata['form_group'] ?? null;
    if ($group != $xgroup)
        {
            echo '<tr><td colspan=4 class="text-start">'.$group. '</td></tr>';
            $xgroup = $group;
        }

    $prop = $xdata['c_class'];
    if ($prop != $xpróp)
        {
            echo '<tr>';
            // Propriedade
            echo '<td class="text-end">';
            echo $prop;
            echo '</td>';

            // Botão incluir
            echo '<td class="text-center">';
            require("form_rdf_plus.php");
            echo '</td>';

            // Botão mostrar dados
            echo '<td>';
            require("form_rdf_data.php");
            echo '</td>';

            echo '</tr>';
            $xpróp = $prop;
        }


    //echo "<p><strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars($value) . "</p>";
}
echo '</table>';
pre($form, false);

if (!isset($loadRDFPlus)) {
    require("form_rdf_plus_apoio.php");
    $loadRDFPlus = true;
}
?>