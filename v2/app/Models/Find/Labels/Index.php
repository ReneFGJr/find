<?php

namespace App\Models\Find\Labels;

use CodeIgniter\Model;
use App\Libraries\Fpdf\Fpdf;
#use TCPDF;



class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'find_item';
    protected $primaryKey       = 'id_i';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'i_status'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function print($d1 = '', $ord = 'i', $d3 = '')
    {

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0, 0);

        $dt = [];
        $da = [];

        $lib = 1016;
        $ord = 'id_i';
        if ($d1 != '') {
            $lib = $d1;
        }
        $limit = 9999;
        $offset = 0;
        $dt['labels'] = $this
            ->select('i_ln1 as ln1, i_ln2 as ln2, i_ln3 as ln3, i_ln4 as ln4, i_tombo')
            ->where('i_library', $lib)
            ->where('i_titulo <> ""')
            ->where('i_ln1 <> ""')
            ->orderBy($ord)
            //->orderBy('ln1')
            ->findAll($limit, $offset);
        //echo $this->getlastquery();

        $posXini = 23;
        $posX = $posXini;
        $labelSpace = 25.5;
        $labelLine = 6;

        $posYini = 14;
        $posY = $posYini;
        $labelCols = 3;
        $labelCol = 0;
        $labelWidth = 70;

        $pdf->setX($posX);
        $pdf->setY($posY);

        $pdf->SetFont('Arial', 'B');
        $pdf->SetFontSize(11);
        $pdf->SetLineWidth(0.13);


        foreach ($dt['labels'] as $idl => $linel) {
            $pdf->SetXY($posX, $posY); // 160 mm da borda esquerda
            $pdf->Cell(40, 10, $linel['ln1'], 0);

            $pdf->SetXY($posX, $posY + $labelLine); // 160 mm da borda esquerda
            $pdf->Cell(40, 10, $linel['ln2'], 0);

            if ($linel['ln3'] != '') {
                $pdf->SetXY($posX, $posY + $labelLine * 2); // 160 mm da borda esquerda
                $pdf->Cell(40, 10, $linel['ln3'].'   '. $linel['i_tombo'], 0);
            } else {
                $pdf->SetXY($posX, $posY + $labelLine * 2); // 160 mm da borda esquerda
                $pdf->Cell(40, 10, $linel['i_tombo'], 0);
            }


            if ($labelCol >= ($labelCols - 1)) {
                $posY = $posY + $labelSpace;
                $posX = $posXini;
                $labelCol = 0;

                if ($posY > 260) {
                    $pdf->AddPage();
                    $posX = $posXini;
                    $posY = $posYini;
                }
            } else {
                $posX = $posX + $labelWidth;
                $labelCol++;
            }
        }
        //exit;
        $pdf->Output();
        exit;
    }

    function print3($d1='', $ord='i', $d3='')
    {

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetMargins(0,0,0,0);

        $dt = [];
        $da = [];

        $lib = 1016;
        $ord = 'id_i';
        if ($d1 != '')
            {
                $lib = $d1;
            }
        $limit = 9999;
        $offset = 0;
        $dt['labels'] = $this
            ->select('i_ln1 as ln1, i_ln2 as ln2, i_ln3 as ln3, i_ln4 as ln4, i_tombo')
            ->where('i_library', $lib)
            ->where('i_titulo <> ""')
            ->where('i_ln1 <> ""')
            ->orderBy($ord)
            //->orderBy('ln1')
            ->findAll($limit, $offset);
            //echo $this->getlastquery();

            $posXini = 20;
            $posX = $posXini;
            $labelSpace = 31;
            $labelLine = 6;

            $posYini = 12;
            $posY = $posYini;
            $labelCols = 3;
            $labelCol = 0;
            $labelWidth = 70;

            $pdf->setX($posX);
            $pdf->setY($posY);

            $pdf->SetFont('Arial','B');
            $pdf->SetFontSize(11);
            $pdf->SetLineWidth(0.13);


        foreach($dt['labels'] as $idl=>$linel)
            {
                $pdf->SetXY($posX, $posY); // 160 mm da borda esquerda
                $pdf->Cell(40, 10, $linel['ln1'], 0);

                $pdf->SetXY($posX, $posY + $labelLine); // 160 mm da borda esquerda
                $pdf->Cell(40, 10, $linel['ln2'], 0);

                $pdf->SetXY($posX, $posY + $labelLine*2); // 160 mm da borda esquerda
                $pdf->Cell(40, 10, $linel['i_tombo'], 0);


                if ($labelCol >= ($labelCols-1))
                    {
                        $posY = $posY + $labelSpace;
                        $posX = $posXini;
                        $labelCol = 0;

                        if ($posY > 250)
                            {
                                $pdf->AddPage();
                                $posX = $posXini;
                                $posY = $posYini;
                            }
                    } else {
                        $posX = $posX + $labelWidth;
                        $labelCol++;
                    }


            }
            //exit;
        $pdf->Output();
        exit;
    }

    function print2($lib = '')
    {
        $lib = 1016;
        $limit = 1000;
        $offset = 0;
        $dt = $this
            ->where('i_library', $lib)
            ->where('i_titulo <> ""')
            ->where('i_ln1 <> ""')
            ->orderBy('id_i')
            ->findAll($limit, $offset);

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // ---------------------------------------------------------

        // set default font subsetting mode

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        //$pdf->SetFont('dejavusans', '', 14, '', true);
        $pdf->SetFont('courier', '',  12, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.

        // set cell padding
        $pdf->setCellPaddings(3, 1, 3, 1);

        // set color for background
        $pdf->SetFillColor(255, 255, 127);

        // set cell margins
        $pdf->setCellMargins(0, 0, 1, 1);
        $txt = "";

        $y = 12.7; /* altura */
        $x = 44.45; /* largura */

        $align = 'L';
        $border = 0;
        $fill = 0;
        $crln = 0;
        $cols = 4;

        $et = 0;
        $ln = 99;
        for($r=0;$r < count($dt);$r++)
            {
                if ($ln >= 16)
                    {
                        $pdf->AddPage();
                        $ln = 0;
                    }
                $line = $dt[$r];
                $txt = $line['i_ln1'].chr(13).chr(10). $line['i_ln2'];
                $et++;
                if ($et >= $cols)
                    {
                        $crln = 1;
                        $et = 0;
                        $ln++;
                    }
                $pdf->MultiCell($x, $y, $txt, $border, $align,  $fill, $crln, '', '', true);
                $crln = 0;
            }


        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('', 'I');
        exit;
    }
}
