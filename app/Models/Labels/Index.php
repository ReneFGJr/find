<?php

namespace App\Models\Labels;

use CodeIgniter\Model;

class Index extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'find_item';
	protected $primaryKey           = 'id_i';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_i','i_ln1','i_ln2','i_ln3'
	];
	protected $typeFields        = [
		'hidden','string:50','string:50','string:50'
	];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	function index($d1, $d2, $d3, $d4)
	{
		$sx = '';
		$LIBRARY = $d2;
		switch ($d1) {
			case 'edit':
				$sx = $this->edit($d2,$d3,$d4);
				break;
			case 'show':
				$sx = $this->show($d2, $d3, $d4);
				break;
			case 'print':
				$sx = $this->print($d2, $d3, $d4);
				break;
			default:
				$sx .= $this->menu($d2);
				break;
		}
		return $sx;
	}

	function edit($d1,$d2,$d3)
		{
			$this->path = PATH.MODULE.'labels/';
			$this->path_back  = 'wclose';
			$this->id = $d1;
			$sx = form($this);

			return $sx;
		}
	function menu($d2)
	{
		if ($d2 == '') {
			$d2 = LIBRARY;
		}
		$this->where('i_library', $d2);
		$this->where('i_status', 2);
		$dt = $this->findAll();
		$sx = lang('Found') . ': ' . count($dt);

		$sx .= '<ul>';
		if (count($dt) > 0) {
			$sx .= '<li>' . anchor(PATH . MODULE . 'labels/print/' . $d2, lang('find.label_print')) . '</li>';
			$sx .= '<li>' . anchor(PATH . MODULE . 'labels/show/' . $d2, lang('find.label_show')) . '</li>';
		}
		$sx .= '</ul>';
		return $sx;
	}
	function print($d2, $d3, $d4)
	{
		$this->where('i_library', $d2);
		$this->where('i_status', 2);
		$dt = $this->findAll();
		$col = 4;
		$rows = 10;

		/*********************************** SIZES */
		$xcol = round(12 / $col);
		$wrow = 600 / $rows;
		$label_for_page = 40;
		$lbs = 0;
		$class = 'border border-secondary mb-1';
		$sa = '';
		$pag = 0;

		$hd = h('Impressão de etiquetas' . ' - ' . count($dt), 2);
		$sx = $hd;
		for ($r = 0; $r < count($dt); $r++) {
			$line = (array)$dt[$r];
			$sa .= bsc($this->label($line, $wrow), $xcol, $class);

			$lbs++;
			if ($lbs >= $label_for_page) {
				$pag++;
				$sx .= bs($sa);
				if ($r <= count($dt)) {
					$sx .= '<div style="page-break-inside:avoid;page-break-after:always">PAGE ' . $pag . '</div>';
					$sx .= $hd;
				}
				$sa = '';
				$lbs = 0;
			}
		}
		$sx .= bs($sa);
		$sx .= '<script>window.print();</script>';
		return $sx;
	}
	function show($d2, $d3, $d4)
	{
		$sx = '';
		$this->where('i_library', $d2);
		$this->where('i_status', 2);
		$dt = $this->findAll();
		$col = 4;
		$rows = 10;

		$sx .= '<a href="'.PATH. MODULE .'labels/print/'.$d2.'" class="btn btn-primary">' . lang('find.label_print') . '</a>';

		/*********************************** SIZES */
		$xcol = round(12 / $col);
		$wrow = 600 / $rows;
		$label_for_page = 40;
		$lbs = 0;
		$class = 'border border-secondary mb-1';
		$sa = '';
		$pag = 0;

		$hd = h('Impressão de etiquetas' . ' - ' . count($dt), 2);
		$sx .= $hd;
		for ($r = 0; $r < count($dt); $r++) {
			$line = (array)$dt[$r];
			
			$title = trim($line['i_titulo']);
			if (strlen($title) == 0) { $title = lang('find.blank_title'); }
			$title .= ' '.onclick(PATH.MODULE.'labels/edit/'.$line['id_i']);
			$title .= bsicone('edit').'</span>';
			$sa = bsc($title,12);
			$sa .= bsc($this->label($line, $wrow), $xcol, $class);
			$sx .= bs($sa);
			}
		//$sx .= '<script>window.print();</script>';
		return $sx;
	}
	function label($dt, $height = 80)
	{
		$blank = '&nbsp;';
		//$blank .= 'Blank';
		$sx = '<div style="font-family: \'Courier new\'; padding: 5px 0px 5px 30px; line-spacing: 70%; line-height: 1; height: ' . $height . 'px;">';
		$sx .= '<tt style="">';
		if ($dt['i_ln1'] == '') {
			$sx .= '<b>' . $blank . '</b>';
		} else {
			$sx .= '<b>' . $dt['i_ln1'] . '</b>';
		}
		$sx .= '<br/></tt>';
		$sx .= '<tt>';

		if ($dt['i_ln2'] == '') {
			$sx .= $blank;
		} else {
			$sx .= $dt['i_ln2'];
		}

		$sx .= '<br/></tt>';
		$sx .= '<tt>';

		if ($dt['i_ln3'] == '') {
			$sx .= $blank;
		} else {
			$sx .= $dt['i_ln3'];
		}

		$sx .= '<br/></tt>';


		if (strlen($dt['i_ln4']) > 0) {
			$sx .= '<tt>';
			$sx .= $dt['i_ln4'];
			$sx .= '</tt>';
		}
		$sx .= '</tt>';
		$sx .= '</div>';
		return $sx;
	}
}
