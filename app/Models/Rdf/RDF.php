<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDF extends Model
{
	var $DBGroup              		= 'rdf';
	protected $table                = PREFIX.'rdf_concept';
	protected $primaryKey           = 'id_cc';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

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

	/*
		function rdf($d1='',$d2='',$d3='')
		{
			$RDF = new \App\Models\Rdf\RDF();
			$tela = $RDF->index($d1,$d2,$d3);
			return $tela;
		}
	*/

	function index($d1, $d2, $d3 = '', $d4 = '', $d5 = '', $cab = '')
	{
		$sx = '';
		$type = get("type");
		switch ($d1) {
			case 'search':
				$RDFFormVC = new \App\Models\Rdf\RDFFormVC();
				$sx = '';
				$sx .= $RDFFormVC->search($d1,$d2,$d3);
				echo $sx;
				exit;
				break;
			case 'exclude':				
				$RDFForm = new \App\Models\Rdf\RDFForm();
				$sx = $cab;
				$sx .= $RDFForm->exclude($d2,$d3);
				break;				
			case 'form':
				$RDFForm = new \App\Models\Rdf\RDFForm();
				$sx = $cab;				
				$sx .= $RDFForm->edit($d1,$d2,$d3,$d4,$d5);
				break;
			case 'text':
				$RDFFormText = new \App\Models\Rdf\RDFFormText();
				$sx = $cab;
				$sx .= $RDFFormText->edit($d2);
				break;
			case 'inport':
				$sx = $cab;
				switch ($type) {
					case 'prefix':
						$this->RDFPrefix = new \App\Models\RDFPrefix();
						$sx .= $this->RDFPrefix->inport();
						break;					

					case 'class':
						$this->RDFClass = new \App\Models\RDFClass();
						$sx .= $this->RDFClass->inport();
						break;
				}
				break;
				/************* Default */
			default:
				$sx = $cab;
				$sx .= lang('command not found') . ': ' . $d1;
				$sx .= '<ul>';
				$sx .= '<li><a href="' . base_url(PATH . 'rdf/inport?type=prefix') . '">' . lang('Inport Prefix') . '</a></li>';
				$sx .= '<li><a href="' . base_url(PATH . 'rdf/inport?type=class') . '">' . lang('Inport Class') . '</a></li>';
				$sx .= '</ul>';
		}
		$sx = bs($sx);
		return $sx;
	}

	function form($id)
		{
			$RDFForm = new \App\Models\Rdf\RDFForm();
			$dt = $this->le($id);
			$class = $dt['concept']['c_class'];
			switch($class)
				{
					case 'brapci_author':
						$tela = 'x';
					default:
						$tela = $RDFForm->form($id,$dt['concept']);
						$tela .= '<h1>'.$class.'</h1>';
						break;
				}
			return $tela;			
		}

	function recovery($dt, $fclass = '')
	{
		$rsp = array();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$class = trim($line['c_class']);
			if ($class == $fclass) {
				array_push($rsp, array($line['d_r1'], $line['d_r2'], $line['n_name']));
			}
		}
		return $rsp;
	}

	function directory($id)
	{
		$IO = new \App\Models\IO\Files();
		return $IO->directory($id);
	}

	function content($id)
	{
		$dir = $this->directory($id);
		$file = $dir . 'name.nm';
		if (file_exists($file)) {
			$tela = file_get_contents($file);
		} else {
			$tela = 'Content not found: ' . $id . '==' . $file . '<br>';
			$RDFExport = new \App\Models\Rdf\RDFExport();
			$RDFExport->export($id);
			$tela = file_get_contents($file);
		}
		return $tela;
	}

	function le_content($id)
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();
		$dt = $RDFConcept->le($id);
		$name = $dt['n_name'];
		return $name;
	}

	function le($id, $simple = 0)
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();

		$dt['concept'] = $RDFConcept->le($id);
		
		if ($simple == 0) {
			$RDFData = new \App\Models\Rdf\RDFData();
			$dt['data'] = $RDFData->le($id);
		}

		return ($dt);
	}

	function le_data($id)
	{
		$RDFData = new \App\Models\Rdf\RDFData();
		$dt['data'] = $RDFData->le($id);

		return ($dt);
	}
	
	function find($sr='', $class = '')
		{
			echo '<h1>'.$class.'</h1>';
			$RDFClass = new \App\Models\Rdf\RDFClass();
			$RDFLiteral = new \App\Models\Rdf\RDFLiteral();
			$prop = $RDFClass->class($class);
			$id = $RDFLiteral->name($sr);
			return $id;
		}

	function recover($dt = array(), $class = '')
	{
		$rst = array();
		$id = $dt['concept']['id_cc'];
		$dt = $dt['data'];
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			if ($line['c_class'] == $class) {
				if (trim($line['n_name']) != '') {
					array_push($rst, $line['n_name']);
				} else {
					if ($line['d_r1'] == $id) {
						array_push($rst, $line['d_r2']);
					} else {
						array_push($rst, $line['d_r1']);
					}
				}
			}
		}
		return $rst;
	}

	function info($id)
	{
		$sx = '';
		$id = round($id);
		$file = '.c/' . round($id) . '/.name';

		if (file_exists(($file))) {
			return file_get_contents($file);
		} else {
			return $this->export($id);
		}
		return '';
	}

	function export_index($class_name, $file = '')
	{
		$RDFData = new \App\Models\Rdf\RDFData();
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$RDFConcept = new \App\Models\Rdf\RDFConcept();

		$class = $RDFClass->Class($class_name);
		$rlt = $RDFConcept
			->join('rdf_name', 'cc_pref_term = rdf_name.id_n', 'LEFT')
			->select('id_cc, n_name, cc_use')
			->where('cc_class', $class)
			->where('cc_library', LIBRARY)
			->orderBy('n_name')
			->findAll();

		$flx = 0;
		$fi = array();
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$name = $line['n_name'];

			$upper = ord(substr(mb_strtoupper(ascii($name)), 0, 1));
			if ($flx != $upper) {
				$flx = $upper;
				$fi[$flx] = '';
			}
			$link = '<a href="' . base_url(URL . 'v/' . $line['id_cc']) . '">';
			$linka = '</a>';
			$fi[$flx] .= $link . $name . $linka . '<br>';
		}
		$s_menu = '<div id="list-example" class=""  style="position: fixed;">';
		$s_menu .= '<h5>' . lang($class_name) . '</h5>';
		$s_cont = '<div data-spy="scroll" data-target="#list-example" data-offset="0" class="scrollspy-example">';
		$cols = 0;
		foreach ($fi as $id_fi => $content) {
			//$s_menu .= '<a class="list-group-item list-group-item-action" href="#list-item-'.$id_fi.'">'.chr($id_fi).'</a>';
			//$s_menu .= '<a class="border-left" href="#list-item-'.$id_fi.'">'.chr($id_fi).'</a> ';

			$s_menu .= '<a class="border-left" href="#list-item-' . $id_fi . '"><tt>' . chr($id_fi) . '</tt></a> ';
			if (($cols++) > 6) {
				$cols = 0;
				$s_menu .= '<br>';
			}

			$s_cont .= '<h4 id="list-item-' . $id_fi . '">' . chr($id_fi) . '</h4>
						<p>' . $content . '</p>';
		}
		$s_menu .= '</div>';
		$s_cont .= '</div>';

		$sx = bsc('<div style="width: 100%;">' . $s_menu . '</div>', 1);
		$sx .= bsc($s_cont, 11);
		$sx .= '<style> body {  position: relative; } </style>';
		file_put_contents($file, $sx);
	}

	function export_all($d1 = '', $d2 = 0, $d3 = '')
	{
		$RDFConcept = new \App\Models\Rdf\RDFConcept();

		$sx = '';
		$d2 = round($d2);
		$limit = 50;
		$offset = round($d2) * $limit;

		$sx .= '<h3>D1=' . $d1 . '</h3>';
		$sx .= '<h3>D2=' . $offset . '</h3>';

		$dt = $RDFConcept->select('id_cc')
			->where('cc_library', LIBRARY)
			->orderBy('id_cc')
			->limit($limit, $offset)
			->findAll($limit, $offset);
		$sx .= '<ul>';
		for ($r = 0; $r < count($dt); $r++) {
			$idc = $dt[$r]['id_cc'];
			$sx .= '<li>' . $this->export_id($idc) . '</li>';
		}
		$sx .= '</ul>';
		if (count($dt) > 0) {
			$sx .= metarefresh(base_url(PATH . 'export/rdf/' . (round($d2) + 1)), 2);
		} else {
			$sx .= bsmessage(lang('Export_Finish'));
		}
		$sx = bs(bsc($sx, 12));
		return $sx;
	}

	function export($id)
	{
		$sx = '';
		$id = round($id);
		if ($id > 0) {
			$dir = '.c/';
			if (!is_dir($dir)) {
				mkdir($dir);
			}
			$dir = '.c/' . round($id) . '/';
			if (!is_dir($dir)) {
				mkdir($dir);
			}
		} else {
			$sx .= 'ID [' . $id . '] inválido<br>';
		}

		/*************************************************************** EXPORT */
		$RDFData = new \App\Models\Rdf\RDFData();
		$RDFConcept = new \App\Models\Rdf\RDFConcept();

		$dt = $this->le($id);

		$class = $dt['concept']['c_class'];
		$txt_name = $dt['concept']['n_name'];

		/******************************************************* ARQUIVOS ********/
		$file_name = $dir . '.name';

		/********************************************************** VARIÁVEIS ****/
		$txt_journal = '';
		$txt_author = '';

		/********************************************************** WORK *********/
		switch ($class) {
			case 'Work':
				for ($w = 0; $w < count($dt['data']); $w++) {
					$dd = $dt['data'][$w];
					$dclass = $dd['c_class'];
					switch ($dclass) {
						case 'title':
							$txt_title = $dd['n_name'];
							break;

						case 'isWorkOf':
							$x = $this->le($dd['d_r2']);
							$txt_journal = $x['concept']['n_name'];
							break;

						case 'creator':
							$x = $this->le($dd['d_r2']);
							if (strlen($txt_author) > 0) {
								$txt_author .= '; ';
							}
							$txt_author .= $x['concept']['n_name'];
							break;
					}
				}
				break;
		}


		/*************************************************************** HTTP ****/
		if (substr($txt_name, 0, 4) == 'http') {
			$txt_name = '<a href="' . $txt_name . '" target="_new">' . $txt_name . '</a>';
		}

		/******************************************************** JOURNAL NAME  */
		if (strlen($txt_author) > 0) {
			$txt_name = $txt_author . '. ' . $txt_title . '. <b>[Anais...]</b> ' . $txt_journal . '.';
		}

		/******************************************************* SALVAR ARQUIVOS */
		if (strlen($txt_name) > 0) {
			file_put_contents($file_name, $txt_name);
		}

		$sx = $txt_name;
		return $sx;
	}

	function view_data($dt)
	{
		if (!is_array($dt))
			{
				$dt = $this->le($dt);
			}
		$RDFdata = new \App\Models\Rdf\RDFData();
		$tela = $RDFdata->view_data($dt);
		return $tela;
	}



	function show_class($dt)
		{
			$prefix = '';
			$class = '';
			$prefix_url = '';
			if (strlen($dt['prefix_url']) > 0) {
				$prefix_url = $dt['prefix_url'];
			}
			if (strlen($dt['prefix_ref']) > 0)
				{
					$prefix = $dt['prefix_ref'].':';
				}
			if (strlen($dt['c_class']) > 0)
				{
					$class = $prefix.$dt['c_class'];
					$prefix_url .= '#'.$dt['c_class'];
				}
			if (strlen($prefix_url) > 0)
				{
					$class .= '<a href="' . $prefix_url . '" target="_new"><sup>(#)</sup></a>';
				}
			return $class;
		}


	function conecpt($name,$class)
		{
			return $this->RDP_concept($name,$class);
		}

	function RDP_concept($name, $class)
	{
		$RDPConcept = new \App\Models\Rdf\RDFConcept();

		$dt['Class'] = $class;
		$dt['Literal']['skos:prefLabel'] = $name;
		$idc = $RDPConcept->concept($dt);
		$tela = $idc;
		return $tela;
	}

	function literal($name,$lang,$idp, $prop)
		{
			return $this->RDF_literal($name,$lang,$idp, $prop);
		}

	function RDF_literal($name,$lang, $idp, $prop)
	{
		$idn = 0;
		$RDPLiteral = new \App\Models\Rdf\RDFLiteral();
		if (($prop != '') and ($idp > 0))
			{
				$RDFData = new \App\Models\Rdf\RDFData();
				$idn = $RDFData->literal($idp,$prop,$name,$lang);
			}
		return $idn;
	}

	/* Igual ao propriety */
	function assoc($idp, $idt, $prop='')
		{
			return $this->RDP_property($idp, $prop, $idt);
		}

	function propriety($idp, $prop='', $resource=0)
		{
			return $this->RDP_property($idp, $prop, $resource);
		}

	function RDP_property($idp, $prop='', $resource=0)
	{
		$RDFClass = new \App\Models\Rdf\RDFClass();
		$RDFData = new \App\Models\Rdf\RDFData();
		$d = array();

		if ($resource > 0)
			{
				if (sonumero($prop) != $prop)
					{
						$prop = $RDFClass->class($prop);
					}
				$d['d_r1'] = $idp;
				$d['d_r2'] = $resource;
				$d['d_p'] = $prop;
				$d['d_library'] = LIBRARY;
				$d['d_literal'] = 0;			
			}

			$rst = $RDFData->where('d_r1', $idp)->where('d_r2', $resource)->findAll();
			if (count($rst) == 0)
				{
					$RDFData->insert($d);
					return 1;
				}
		return 0;
	}

}
