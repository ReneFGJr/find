<?php

namespace App\Models\Book;

use CodeIgniter\Entity\Cast\ObjectCast;
use CodeIgniter\Model;

class Itens extends Model
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
		'id_i', 'i_tombo', 'i_manifestation', 'i_identifier',
		'i_type', 'i_library_classification', 'i_aquisicao',
		'i_year', 'i_status', 'i_usuario',
		'i_titulo', 'i_status', 'i_library', 'i_library_place',
		'i_exemplar'
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

	function le($id)
	{
		$dt = $this->Find($id);
		return $dt;
	}

	function harvesting_metadata($id, $id2)
	{
		$dt = $this->Find($id);
		$isbn = $dt['i_identifier'];
		if (substr($isbn, 0, 3) == '978') {
		} else {
			$sx = bsmessage('ISBN inválido - ' . $isbn, 3);
			return $sx;
		}

		/* Find */
		$Find = new \App\Models\API\Find();
		$dd['FIND'] = $Find->book($isbn, $id);

		/* OCLC */
		$OCLC = new \App\Models\API\OCLC();
		$dd['OCLC'] = $OCLC->book($isbn, $id);

		/* Google */
		$Google = new \App\Models\API\Google();
		$dd['GOOGLE'] = $Google->book($isbn, $id);

		/* Mercado Editorial */
		$MecadoEditorial = new \App\Models\API\MercadoEditorial();
		$dd['MERCA'] = $MecadoEditorial->book($isbn, $id);

		echo h($isbn);
		pre($dd);
		return $dd;
	}

	function resume()
	{
		$dt = $this->select("i_status, count(*) as total")
			->where('i_tombo > 0')
			->where('i_library', LIBRARY)
			->groupBy('i_status')
			->findAll();
		return $dt;
	}

	function showItens($dt)
	{
		$Covers = new \App\Models\Book\Covers();

		$dt = $dt['data'];
		$wh = array();

		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$class = $line['c_class'];

			switch ($class) {
				case 'isPublisher':
					array_push($wh, $line['d_r1']);
					break;
				default:
					echo '<br>===>' . $class;
					break;
			}
		}
		$sx = '';
		for ($r = 0; $r < count($wh); $r++) {
			$sx .= bsc('<a href="' . PATH . 'v/' . $wh[$r] . '">' . $Covers->get_Nail($wh[$r]) . '</a>', 2);
		}
		$sx = bs($sx);
		return $sx;
	}
	function showCoverItem($id)
	{
		$RDFExport = new \App\Models\RDF\RDFExport();
		$RDFExport->exportNail($id);
		exit;
	}

	function itens_list($sta)
	{
		$dt = $this
			->where('i_status', $sta)
			->where('i_library', LIBRARY)
			->orderBy('i_tombo', 'asc')
			->findAll();
		$sx = '<table class="table">';
		$sx .= $this->table_header();
		for ($r = 0; $r < count($dt); $r++) {
			$line = $dt[$r];
			$sx .= $this->show_item_line($line);
		}
		$sx .= '</table>';
		return $sx;
	}

	function table_header()
	{
		$sx = '';
		$sx .= '<tr>';
		$sx .= '<th width="7%">' . lang('find.i_tombo') . '</th>';
		$sx .= '<th width="7%">' . lang('find.i_exemplar') . '</th>';
		$sx .= '<th width="21%">' . lang('find.i_identifier') . '</th>';
		$sx .= '<th width="44%">' . lang('find.i_titulo') . '</th>';
		$sx .= '<th width="7%">' . lang('find.i_ln1') . '</th>';
		$sx .= '<th width="7%">' . lang('find.i_ln2') . '</th>';
		$sx .= '<th width="7%">' . lang('find.i_ln3') . '</th>';
		$sx .= '<th width="7%">' . lang('find.i_created') . '</th>';
		$sx .= '</tr>';
		return $sx;
	}
	function show_item_line($dt, $link = '')
	{
		$link = '';
		$linka = '';
		$st = $dt['i_status'];
		switch ($st) {
			case '0':
				$link = '<a href="' . (PATH . MODULE . 'tech/prepare_0/' . $dt['id_i']) . '">';
				$linka = '</a>';
				break;
		}


		$sx = '';
		$sx .= '<tr>';
		$sx .= '<td>' . $link . $dt['i_tombo'] . $linka . '</td>';
		$sx .= '<td>Ex.:' . $dt['i_exemplar'] . '</td>';
		$sx .= '<td>' . $dt['i_identifier'] . '</td>';
		$sx .= '<td>' . $dt['i_titulo'] . '</td>';
		$sx .= '<td>' . $dt['i_ln1'] . '</td>';
		$sx .= '<td>' . $dt['i_ln2'] . '</td>';
		$sx .= '<td>' . $dt['i_ln3'] . '</td>';
		$sx .= '<td>' . stodbr(sonumero($dt['i_created'])) . '</td>';
		$sx .= '</tr>';
		return $sx;
	}

	/************************************************************************** BUSCA METADADOS */
	function prepare_0($d1, $d2, $d3)
	{
		$sx = 'xxx';
		$sx = h(lang('find.tech_0'), 2);

		switch ($d1) {
			default:
				$sx .= $this->itens_list(0);
		}
		return $sx;
	}

	/******************************************************************************** NOVO ITEM */
	function new($d1, $d2, $d3)
	{
		$sx = h(lang('find.tech_I'), 2);

		switch ($d1) {
			case 'isbn':
				/***************** Formulário Novo Item */
				$sx = $this->item_new_form();
				break;

			default:
				$sx .= bsc(lang('find.tech_IA'), 2, 'text-end small');
				$link = '<a href="' . PATH . MODULE . '/tech/prepare_I/isbn/edit/0' . '">';
				$linka = '</a>';
				$sx .= bsc($link . '<b>' . lang('find.tech_IA1') . '</b>' . $linka, 10);

				$sx .= bsc('<center><hr style="width: 50%;"/></center>', 12);

				$sx .= bsc(lang('find.tech_IB'), 2, 'text-end small');
				$link = '<a href="' . PATH . MODULE . '//marc' . '">';
				$linka = '</a>';
				$sx .= bsc($link . '<b>' . lang('find.tech_IB1') . '</b>' . $linka, 10);

				$sx .= bsc('<center><hr style="width: 50%;"/></center><div style="height: 400px;"', 12, 'mb-5');
		}

		$sx = bs($sx);
		return $sx;
	}

	function item_new_form()
	{
		$sx = '';
		$this->allowedFields = array(
			'',
			'tech_IA1_form1' . '*',
			'',
			'tech_IA1_form2',
			'',
			'tech_IA1_form3' . '*',
			'tech_IA1_form4',
		);

		$sql = 'sql:id_bs:bs_name:library_place_bookshelf:bs_LIBRARY = \'' . LIBRARY . '\'';
		$this->typeFields = array('hidden', $sql, 'hr', 'checkbox', 'hr', 'text:5', 'string:50');
		$this->table = '*';
		$this->id = 0;
		$this->pre = 'find.';
		$this->path = PATH . MODULE . '/tech/prepare_I/isbn';
		$sx .= h(lang('find.tech_IA'), 2);
		$sx .= h(lang('find.tech_IA1'), 4);

		/* Regra se não for automático */
		$auto = get("tech_IA1_form2");
		if ($auto != 1) {
			$this->allowedFields[6] = 'tech_IA1_form4*';
		}
		$sx .= form($this);

		if ($this->saved == 1) {
			$place = get("tech_IA1_form1");

			$isbn = explode(chr(10), get("tech_IA1_form3"));
			$tomboNr = (get("tech_IA1_form5"));
			$Tombo = new \App\Models\Book\Tombo();

			$sx .= '<table class="table">';
			$sx .= '<tr>';
			$sx .= '</tr>';


			for ($r = 0; $r < count($isbn); $r++) {
			/******************************** Exemplares */
				$ex = $Tombo->exemplar($isbn[$r]);
				echo '===>'.$sx;


				$dd['i_identifier'] = $isbn[$r];
				$dd['i_library_place'] = $place;
				$dd['i_library'] = LIBRARY;
				$dd['i_type'] = 0;
				$dd['i_exemplar'] = $ex;
				$dd['i_manitestation'] = 0;
				$dd['i_library_classification'] = 0;
				$dd['i_aquisicao'] = 0;
				$dd['i_year'] = 0;
				$dd['i_status'] = 0;
				$dd['i_usuario'] = 0;
				$dd['i_ip'] = ip();
				$dd['i_manitestation'] = 0;

				if ($auto == 1) {
					$tomboNr = $Tombo->next();
					$dd['i_tombo'] = $tomboNr;
				} else {
					$dd['i_tombo'] = $tomboNr;
					$tomboNr++;
				}
				$Tombo->insert($dd);

				$sx .= '<tr>';
				$sx .= '<td>' . ($r + 1) . '</td>';
				$sx .= '<td>' . $isbn[$r] . '</td>';
				$sx .= '<td>' . $tomboNr . '</td>';
				$sx .= '<td>' . 'Ex: '.$ex . '</td>';
				$sx .= '<td>' . LIBRARY . '</td>';
				$sx .= '</tr>';
			}
			$sx .= '</table>';
		}
		return $sx;
	}

	function recomendations($m)
	{
		$Cover = new \App\Models\Book\Covers();
		$tela = '';
		$offset = round(date("s"));
		$limit = 6;
		$Item = new \App\Models\Book\Itens();
		$sql = "select i_manitestation,i_titulo,i_identifier";
		$sql .= " from " . $this->table . " ";
		$sql .= "where i_library = " . LIBRARY . " ";
		$sql .= "group by i_manitestation,i_titulo,i_identifier ";
		$sql .= "LIMIT $limit OFFSET $offset";

		$dt = $Item->query($sql)->getResult();

		$tela .= '<div class="row">';
		$tela .= '<div class="find.recomendations supersmall">' . lang('find.recomendations') . '</div>';
		for ($r = 0; $r < count($dt); $r++) {
			$line = (array)$dt[$r];
			$mani = $line['i_manitestation'];
			if ($mani > 0) {
				$isbn = $line['i_identifier'];
				$cover = $Cover->get_cover($isbn);
				$img = '<img src="' . $cover . '" class="img-fluid shadow-lg p-1 mb-2 bg-body rounded">';
				$tela .= '<a href="' . PATH . 'v/' . $mani . '">';
				$tela .= $img;
				$tela .= '</a>';
			}
		}
		$tela .= '</div>';
		return $tela;
	}
}
