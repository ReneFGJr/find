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

	function create_rdf_work($id)
	{
		$sx = '';
		$RDF = new \App\Models\Rdf\RDF();
		$Tombo = new \App\Models\Book\Tombo();
		$dt = $this->Find($id);
		$work_title = 'work-'.$dt['i_identifier'];
		$expression_title = 'expression-'.$dt['i_identifier'];
		$manifestation_title = 'manifestation-'.$dt['i_identifier'];
		$IDW = $RDF->conecpt($work_title,'frbr:Work');
		$IDE = $RDF->conecpt($expression_title,'frbr:Expression');
		$IDM = $RDF->conecpt($manifestation_title,'frbr:Manifestation');
		$RDF->propriety($IDW,'isAppellationOfExpression',$IDE);
		$RDF->propriety($IDE,'isAppellationOfManifestation',$IDM);
		$dd['i_manitestation'] = $IDM;
		$Tombo->set($dd)->where('id_i',$id)->update();

		return $IDM;
	}

	function header($id)
		{
			$dt = $this->le($id);
			$sx = '';
			$title = $dt['i_titulo'];
			if (strlen($title) == 0) { $title = lang('find.no_title').' - '.$dt['i_identifier']; }
			$sa = h($title);
			$sb = '<tt>';

			for ($r=1;$r <= 4;$r++)
				{
					if (strlen($dt['i_ln'.$r]) > 0) $sb .= $dt['i_ln'.$r].'<br>';
				}

			$sx = bs(bsc($sa,9).bsc($sb,3));
			return $sx;
		}

	function status($id,$st)
		{
			$dd['i_status'] = $st;
			$dt = $this
			->set($dd)
			->where('id_i',$id)
			->update();
			return $dt;
		}

		function save_metadata($dt,$id)	
		{
			$sx = '';
			$RDF = new \App\Models\Rdf\RDF();
			$Item = new \App\Models\Book\Itens();
			$di = $Item->find($id);
			$isbn = $di['i_identifier'];	
			$language = '';

			/****************************************** OCLC */
			if (isset($dt['editions']))
				{
					$editons = $dt['editions'];
					$editons = $editons['edition'];
					$tit = array();
					for($r=0;$r < count($editons);$r++)
						{
							$line = $editons[$r];
							$attr = $line['@attributes'];
							$lang = $attr['language'];
							$title = $attr['title'];
							$title = troca($title,' :',':');
							$tit[$lang] = $title;
							$format = $attr['format'];
							if (isset($attr['author']))
								{
									$author = $attr['author'];
								}							
						}
					if (isset($tit['por']))
						{
							$dt['title'] = $tit['por'];
							$language = 'pt';
						} else {
							if (isset($tit['eng']))
								{
									$dt['title'] = $tit['eng'];
									$language = 'en';
								} else {
									if (isset($tit['spa']))
									{
										$dt['title'] = $tit['spa'];
										$language = 'es';
									} else {
										if (isset($tit['fre']))
										{
											$dt['title'] = $tit['fre'];
											$language = 'fr';
										} else {
											
										}
									}
								}
						}

				}
			
			if (isset($dt['title']))
				{					
					$title = trim($dt['title']);
					$dd['i_titulo'] = $title;
					$this->set($dd)->where('id_i',$id)->where('i_titulo','')->update();
					$sx .= '<hr><span>Title: <b>'.$title.'</b></span><br>';
				} else {
					
				}
			/************************** WORK ******/
			if (!isset($title))
				{
					return lang('find.title_not_proceess');
				}
			$IDW = $RDF->rdf_concept($title,'Work');

			/************************** AUTHORES */
			/************************************ OCLS */
			if (isset($dt['authors']))
				{
					$auth = $dt['authors'];
					if (isset($auth['author']))
						{
							$dta = $auth['author'];
							$dt['authors'] = $dta;
						}
				}

			/************************************ AUTHORS */
			if (isset($dt['authors']))
					{
					if (!is_array($dt['authors']))
						{
							$name = $dt['authors'];
							$ano1 = '';
							$ano2 = '';
							for ($r=1800;$r < date("Y");$r++)
								{
									if ($pos=strpos($name,$r.'-')) { $ano1 = substr($name, $pos,4); }
									if ($pos=strpos($name,'-'.$r)) { $ano2 = substr($name, $pos+1,4); }
								}
							if ($pos = strpos($name,','))
								{
									$name1 = trim(substr($name,$pos+2,strlen($name)));
									if (strpos($name1,','))
										{
											$name1 = substr($name1,0,strpos($name1,','));
										}
									$name2 = substr($name,0,strpos($name,','));
									$name = trim($name1).' '.trim($name2);
									
								}
							$dt['authors'] = array($name);
						}			
				} else {
					$dt['authors'] = array();
				}

			for($r=0;$r < count($dt['authors']);$r++)
				{
					$name = trim($dt['authors'][$r]);
					$prop = 'brapci:hasAuthor';
					if (strpos($name,'[') > 0)
						{
							$prop = trim(substr($name,strpos($name,'['),strlen($name)));
							$name = substr($name,0,strpos($name,'['));
							switch($prop)
								{
									case '[Translator]':
										$prop = 'brapci:hasTranslator';
										break;
									default:
										echo "OPS - ".$prop;
										exit;
										break;
								}
						}
					$name = nbr_author($name,7);
					$IDA = $RDF->rdf_concept($name,'Person');
					$RDF->propriety($IDW,$prop,$IDA);
				}

			/************************** EXPRESSAO */
			if ($language == '')
				{
					$language = $dt['expressao']['idioma'];
				}			
			
			$name = 'ISBN:'.$isbn.':book';
			$IDE = $RDF->rdf_concept($name,'frbr:Expression');			
			$IDL = $RDF->rdf_concept($language,'brapci:Linguage');

			$prop = 'brapci:hasFormExpression';
			$prop = 'isAppellationOfExpression';
			$RDF->propriety($IDW,$prop,$IDE);

			/************************* Language */			
			$RDF->propriety($IDE,'brapci:hasFormExpression',$IDL);
			$sx .= '<p>Language: '.$language.'</p>';				
							
			$sx .= '<p>Work:'.anchor(PATH.MODULE.'/v/'.$IDW,'Link').'</p>';

			/************************** MANIFESTATION */
			$name = 'ISBN:'.$isbn;
			$IDM = $RDF->rdf_concept($name,'frbr:Manifestation');	
			$prop = 'isAppellationOfManifestation';
			$RDF->propriety($IDE,$prop,$IDM);
			$sx .= '<p>Expression:'.anchor(PATH.MODULE.'/v/'.$IDE,'Link').'</p>';
			$sx .= '<p>Manifestation:'.anchor(PATH.MODULE.'/v/'.$IDM,'Link').'</p>';

			/****************************** ISBN */
			$IDISBN = $IDM = $RDF->rdf_concept($name,'brapci:ISBN');
			$RDF->propriety($IDM,'brapci:hasISBN',$IDISBN);

			return $sx;		
		}		

	function process_metadata($hv,$id)
		{
			$sx = h('find.metadata_proceesing',3);			

			/********************************************************* METADATA - FIND */			
			if (count($hv['FIND']) > 0)
				{
					$Find = new \App\Models\API\Find();
					$sx .= 'FIND '. $Find->process($hv['FIND'],$id).'<br>';
				} else {
					$sx .= 'FIND '.lang('find.metadata_not_found').'<br>';
				}

			/********************************************* METADATA - MercadoEditorial */			
			if (count($hv['MERCA']) > 0)
				{
					$Find = new \App\Models\API\Find();
					$sx .= 'MercadoEditorial '. $this->save_metadata($hv['MERCA'],$id).'<br>';
				} else {
					$sx .= 'MercadoEditorial '.lang('find.metadata_not_found').'<br>';
				}					
			
			/********************************************************* METADATA - OCLC */			
			if (count($hv['OCLC']) > 0)
				{
					$Find = new \App\Models\API\Find();
					$sx .= 'OCLC '.$this->save_metadata($hv['OCLC'],$id).'<br>';
				} else {
					$sx .= 'OCLC '.lang('find.metadata_not_found').'<br>';
				}

				/********************************************************* GOOGLE - OCLC */					
				if (count($hv['GOOGLE']) > 0)
				{
					$Find = new \App\Models\API\Find();
					$sx .= 'GOOGLE '. $this->save_metadata($hv['GOOGLE'],$id).'<br>';
				} else {
					$sx .= 'GOOGLE '.lang('find.metadata_not_found').'<br>';
				}
			return $sx;
		}

	function actions($id,$or=0)
		{
			$menu = array();
			$dt = $this->le($id);
			$st = $dt['i_status'];
			if ($st == $or)
				{
					$sx = bsmessage('Não foi possível processar a operação',3);

					if ($st == 0)
						{
							$menu['1'] = lang('find.set_status_to').' <b>'.lang('find.tech_1').'</b>';
						}
					$sx .= '<ul>';
					foreach ($menu as $link=>$label)
						{
							$sx .= '<li><a href="'.(PATH.MODULE.'tech/item_status/'.$id.'/'.$link).'">'.$label.'</a></li>';
						}
					$sx .= '</ul>';
				} else {
					$sx = bsmessage('Processamento realizado',1);
				}
			return $sx;
		}

	function harvesting_metadata($id, $id2)
	{
		$dt = $this->Find($id);
		$isbn = $dt['i_identifier'];
		$isbn_ok = 0;
		if (substr($isbn, 0, 3) == '978') {
			$isbn_ok = 1;
		}

		/* Find */
		$Find = new \App\Models\API\Find();
		$dd['FIND'] = $Find->book($isbn, $id);
		$dd['OCLC'] = array();
		$dd['GOOGLE'] = array();
		$dd['MERCA'] = array();

		if ($isbn_ok == 1)
		{
		/* OCLC */
		$OCLC = new \App\Models\API\OCLC();
		$dd['OCLC'] = $OCLC->book($isbn, $id);

		/* Google */
		$Google = new \App\Models\API\Google();
		$dd['GOOGLE'] = $Google->book($isbn, $id);

		/* Mercado Editorial */
		$MecadoEditorial = new \App\Models\API\MercadoEditorial();
		$dd['MERCA'] = $MecadoEditorial->book($isbn, $id);
		}
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
					echo '<br>=showItens==>' . $class;
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
		$sx = h(lang('find.total') . ': ' . count($dt).' '.lang('find.itens'),4);
		$sx .= '<table class="table">';
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
			case '1':
				$link = '<a href="' . (PATH . MODULE . 'tech/prepare_1/' . $dt['id_i']) . '">';
				$linka = '</a>';
				break;
			case '2':
				$link = '<a href="' . (PATH . MODULE . 'tech/prepare_2/' . $dt['id_i']) . '">';
				$linka = '</a>';
				break;
			case '3':
				$link = '<a href="' . (PATH . MODULE . 'tech/prepare_3/' . $dt['id_i']) . '">';
				$linka = '</a>';
				break;												
		}


		$sx = '';
		$sx .= '<tr>';
		$sx .= '<td>' . $link . $dt['i_tombo'] . $linka . '</td>';
		$sx .= '<td>Ex.:' . $dt['i_exemplar'] . $linka .'</td>';
		$sx .= '<td>' . $link. $dt['i_identifier'] . $linka .'</td>';
		$sx .= '<td>' . $link. $dt['i_titulo'] .$linka . '</td>';
		$sx .= '<td>' . $link. $dt['i_ln1'] .$linka . '</td>';
		$sx .= '<td>' . $link. $dt['i_ln2'] .$linka . '</td>';
		$sx .= '<td>' . $link. $dt['i_ln3'] .$linka . '</td>';
		$sx .= '<td>' . $link. stodbr(sonumero($dt['i_created'])) . $linka .'</td>';
		switch ($dt['i_status'])
			{
				case '0':
					$sx .= '<td>'.$link.bsicone('process').$linka.'</td>';
					break;
			}		
		$sx .= '</tr>';
		return $sx;
	}

	/************************************************************************** BUSCA METADADOS */
	function prepare($d1, $d2, $d3)
	{
		$sx = h(lang('find.tech_'.$d3), 2);

		switch ($d1) {
			default:
				$sx .= $this->itens_list($d3);
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
				$link = '<a href="' . PATH . MODULE . 'tech/prepare_I/isbn/edit/0' . '">';
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
		$this->path_back = 'none';
		$this->table = '*';
		$this->id = 0;
		$this->pre = 'find.';
		$this->path = PATH . MODULE . 'tech/prepare_I/isbn';
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
