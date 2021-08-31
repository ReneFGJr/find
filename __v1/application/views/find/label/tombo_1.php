<?php
/*********** label */
$lines = 14;
$nr = round($nrtombo);
$tet = $repetir;
$tto = 0;
//echo '<body style="margin-top: 0px; padding 0px;">';
/********** PAGES *********************/
for ($p = 1; $p <= $pages; $p++) {
	echo '<table width="100%" cellpaging=0 cellspacing=0 style="page-break-after: always;">' . cr();
	/********** LINES *********************/
	for ($l = 1; $l <= $lines; $l++) {
		echo '<tr>' . cr();
		/********** COLUNAS *********************/
		for ($c = 1; $c <= $cols; $c++) {
			//echo '<td colspan=1>' . $p . '</td>';
			//echo '<td colspan=1 align="center" style="height: 70px; font-size: 20px;" valign="middle">';
			//            if (strlen($label) > 0) {
			//echo '<span style="font-size: 14px">' . $label . '</span>' . cr();
			//echo '<br>' . cr();
			//}
			echo '<td colspan=1 style="height: 50px;">';
			echo strzero($nr, 11) . $this -> barcodes -> ean13(strzero($nr, 11));
			echo '</td>' . cr();
			$tto++;
			if ($tto >= $tet)
				{
					$nr++;
					$tto = 0;		
				}
			
			//        }
		}
		echo '</tr>' . cr();
	}
	echo '</table>' . cr();
}
?>