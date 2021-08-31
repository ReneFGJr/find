<?php
class labels extends CI_model
{
	public function label_pdf($tp = '') {
		$cols = 3;
		$lins = 10;
		$cols = 4;
		$lins = 20;
		$mar_left = 1.3;
		$mar_right = 1.3;
		$etq_sz = 4.2;
		$etq_sp = 0.3;
		$nm = 1;
		$this -> load -> library('tcpdf');

		$icc = 1;
		$etq_sz = 4.2;
		$etq_sp = 0.45;
		$etq_hg = 1.275;	
		
		$mar_left = 1.8;
		$mar_right = 1.3;
		$mar_top = 1.3;	

		$bar_px = 0.3;
		$bar_sz = 12;
		$nm = 2;
		$icc = 2;

        $style = array('position' => '', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => false, 'hpadding' => 'auto', 'vpadding' => 'auto', 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
        	'text' => true, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4);
        $et = 0;
        switch($et) {
        	case '0' :
        	/* Margens */
        	$mar_left = 1.8;
        	$mar_right = 1.3;
        	$mar_top = 0.6;

        	/* Tipos da barra */
        	$bar_px = 0.3;
        	$bar_sz = 12;
        	$nm = 2;
        	$icc = 2;

        	/* Etiquetas */
        	$etq_sz = 4.5;
        	$etq_sp = 0.45;
        	$etq_hg = 1.330;



        	break;
        }

        $this -> load -> model('barcodes');
        // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);

        // set document information
        $pdf -> SetCreator(PDF_CREATOR);

        // set font
        $pdf -> SetFont('helvetica', '', 11);

        // -----------------------------------------------------------------------------

        $pdf -> SetFont('helvetica', '', 10);

        // define barcode style
        $nr = 1;
        $pg = 1;
        if (strlen($tp) > 0) {
        	$nr = round($tp);
        }
        if (get("dd21")) {
        	$nr = round(get("dd21"));
        }
        if (get("dd22")) {
        	$pg = round(get("dd22"));
        }
        $lib = $this -> lib;
        $ic = 0;
        for ($pp = 0; $pp < $pg; $pp++) {
            // add a page
        	$pdf -> AddPage();
        	for ($r = 0; $r < $lins; $r++) {
        		/************************** coluna ************/
        		for ($q = 0; $q < $cols; $q++) {

        			/*********** positions ************/
        			$x = ($q * $etq_sz * 10) + ($q * $etq_sp * 10) + ($mar_left * 10);

        			$y = ($r * $etq_hg * 10) + ($mar_top * 10);

        			$nrz = strzero(round($nr) + $lib, 11);
        			$nrz = $nrz . $this -> barcodes -> ean13($nrz);

        			$pdf -> SetXY($x, $y);
        			$pdf -> write1DBarcode($nrz, 'EAN13', '', '', '', $bar_sz, $bar_px, $style, 'N');
        			$pdf -> SetFont('helvetica', '', 9);

        			if ($nm == 1) {
        				$pdf -> SetXY($x, $y + 16);
        				$pdf -> Cell(48, 0, 'Sala de Leitura PROPEL', 0, 0, 'L', 0, '', 0);
        			}

        			if ($nm == 1) {
        				$pdf -> SetXY($x, $y + 16);
        				$pdf -> Cell(48, 0, 'Nr.:' . round($nr), 0, 0, 'R', 0, '', 0);
        			}
        			/************ VERTICAL ************/
        			if ($nm == 2) {
        				$pdf -> SetXY($x + 32, $y + 11);
        				$pdf -> StartTransform();
        				$pdf -> Rotate(90);
        				$pdf -> MultiCell(10, 5, round($nr), 0, 'C', false, 0, "", "", true, 0, false, true, 0, "T", false, true);
        				$pdf -> StopTransform();
                        //$pdf -> Cell(48, 0, 'Nr.:' . round($nr),1,1,'C',0,'');
        			}
        			$nr = $nr + 1;
        		}
        		$pdf -> SetXY(0, $mar_top);
        		$pdf -> Cell(48, 0, '------', 0, 0, 'L', 0, '', 0);

        		$pdf -> SetXY(0, 262);
        		$pdf -> Cell(48, 0, '------', 0, 0, 'L', 0, '', 0);

        	}

            // EAN 13
        	$pdf -> Ln();
        }
        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf -> Output('example_027.pdf', 'I');
    }        
}
?>