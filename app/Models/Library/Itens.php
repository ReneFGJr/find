<?php

namespace App\Models\Library;

use CodeIgniter\Model;

class Itens extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'itens';
    protected $primaryKey       = 'id_i';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_i','i_tombo','i_manitestation',
        'i_work','i_titulo','i_status',
        'i_aquisicao','i_year','i_localization',
        'i_ln1','i_ln2','i_ln3','i_ln4',
        'i_type','i_identifier','i_uri',
        'i_library','i_library_place','i_library_classification',
        'i_created','i_ip','i_usuario',
        'i_dt_emprestimo','i_dt_prev','i_dt_renovavao',
        'i_exemplar'
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

	function update_title($title,$id)
		{
			$dd['i_titulo'] = $title;
			$dt = $this
			->set($dd)
			->where('id_i',$id)
			->update();
			return true;
		}

	function status($id,$st)
		{
			$historic = new \App\Models\Library\ItensHistorico();
			$dd['i_status'] = $st;
			$dt = $this
			->set($dd)
			->where('id_i',$id)
			->update();

			$historic->add_historicy($id,700+$st);
			return $dt;
		}

		function duplicate_metadata($dt,$id)
			{
				if ($id == $dt['id_i'])
					{
						return lang('find.duplicate_metadata');
					}
				echo h("Duplicate ".$id);
				///pre($dt);
			}

		function save_metadata($dt,$id)	
		{
			$sx = '';
			$RDF = new \App\Models\Rdf\RDF();
			$Item = new \App\Models\Library\Itens();
			$di = $Item->find($id);
			$isbn = $di['i_identifier'];	
			$language = '';

			/****************************************** OCLC */
			if (isset($dt['editions']))
				{
					$editons = $dt['editions'];
					$editons = $editons['edition'];
					$tit = array();
					if (isset($editons[0]))
						{
							
						} else {
							$editons = array($editons);
						}
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
		
			if (isset($dt['i_titulo']))
				{					
					$title = trim($dt['i_titulo']);
					$dd['i_titulo'] = $title;
					$this->set($dd)->where('id_i',$id)->where('i_titulo','')->update();
				} else {
					
				}
			/************************** WORK ******/
			if (!isset($title))
				{
					return lang('find.title_not_proceess');
				}
			$IDW = $RDF->rdf_concept($title,'Work');
			if (isset($dt['i_titulo']))
				{
					$ttt = trim($dt['i_titulo']);
					$RDF->propriety($IDW,'hasTitle',$ttt);
				}

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
									case '[Author]':
										$prop = 'brapci:hasAuthor';
										break;	
									case '[Translator; Author]':
										$prop = 'brapci:hasAuthor';
										break;																			
									default:
										echo "OPS Itens - ".$prop;
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
					if (isset($dt['expressao']['idioma']))
						{
							$language = $dt['expressao']['idioma'];
						} else {
							$language = 'pt';
						}
					
				}			
			
			$name = 'ISBN:'.$isbn.':book';
			$IDE = $RDF->rdf_concept($name,'frbr:Expression');			
			$IDL = $RDF->rdf_concept($language,'brapci:Linguage');

			$prop = 'brapci:hasFormExpression';
			$prop = 'isAppellationOfExpression';
			$RDF->propriety($IDW,$prop,$IDE);

			/************************* Language */			
			$RDF->propriety($IDE,'brapci:hasFormExpression',$IDL);
			/************************** MANIFESTATION */
			$name = 'ISBN:'.$isbn;
			$IDM = $RDF->rdf_concept($name,'frbr:Manifestation');	
			$prop = 'isAppellationOfManifestation';
			$RDF->propriety($IDE,$prop,$IDM);
			/****************************** ISBN */
			$IDISBN = $IDM = $RDF->rdf_concept($name,'brapci:ISBN');
			$RDF->propriety($IDM,'brapci:hasISBN',$IDISBN);

			return 1;		
		}		

	function process_metadata($hv,$id)
		{
			$dt = $this->find($id);
			$sh = '';
			$sn = '<span class="label label-info btn-danger rounded ps-2 pe-2">&nbsp;X&nbsp;</span> ';		
			$ss = '<span class="label label-info btn-success rounded ps-2 pe-2">&nbsp;V&nbsp;</span> ';		
			$rst = 0;
			/********************************************************* METADATA - FIND */			
			if (count($hv['FIND']) > 0)
				{
					$Find = new \App\Models\API\Find();
					$rst = $this->duplicate_metadata($hv['FIND'],$id);
					$sh .= $ss.'FIND<br>';
				} else {
					$sh .= $sn.'FIND '.lang('find.metadata_not_found').'<br>';
				}

			/********************************************* METADATA - MercadoEditorial */			
			if ($hv['BMS']['count'] > 0)
				{			
					$Find = new \App\Models\API\Find();
					$rst = $this->save_metadata($hv['BMS'],$id);
					/************ Atualiza titulo */
					$this->update_title($hv['BMS']['title'],$id);
					$sh .= $ss.'BrapciMetadataSource<br>';

					$this->status($id,1);
					
					$dt['i_titulo'] = $hv['BMS']['title'];					
					$this->save_metadata($hv['BMS'],$id);
				} else {
					$sh .= $sn.'BrapciMetadataSource '.lang('find.metadata_not_found').'<br>';
				}						

			if ($rst > 0)
				{
					$this->status($id,1);
					//$sx = metarefresh(PATH.MODULE.'tech/prepare_1/'.$id);
				}
			$sx = $this->header_item($dt);
			/*************** Busca nos Metadados */
			$sx .= h(lang('find.metadata_proceesing'),3);	
			$sx .= $sh;			

			return $sx;
		}

	function btn_action($id,$st)
		{
			$sx = '';
			$sx .= '<a href="'.base_url(PATH.'tech/item_status/'.$id.'/'.$st).'" class="btn btn-primary btn-sm">';
			$sx .= lang('find.tech_'.$st);
			$sx .= '</a>';
			return($sx);
		}

	function header_item($dt)
		{
			$ISBN = new \App\Models\Isbn\Isbn();
			$title = trim($dt['i_titulo']);
			if ($title == '') { $title = lang('find.unknow'); }
			$isbn = $dt['i_identifier'];
			$tombo = $dt['i_tombo'];
			$date = stodbr(sonumero($dt['i_created']));
			$sx = '';
			$sx .= '<div class="row">';
			$sx .= '	<div class="col-md-12">';
			$sx .= '		<div class="card">';
			$sx .= '			<div class="card-header">';
			$sx .= '			<span class="small">'.lang('find.title').'</span>';
			$sx .= '			<h2 class="card-title">'.$title.'</h2>';			
			$sx .= '		</div>';
			$sx .= '	<div class="card-body">';
			$sx .= '		<div class="row">';
			$sx .= 			bsc('ISBN: <b>'.$ISBN->format($isbn).'</b>',6);
			$sx .= 			bsc(lang('find.created_at').': <b>'.$date.'</b>',6);
			$sx .= 			bsc('ISBN: <b>'.$ISBN->isbn13to10($isbn).'</b>',6);
			$sx .= 			bsc(lang('find.registration_number').': <b>'.$tombo.'</b>',6);
			$sx .= '		</div>';
			$sx .= '	</div>';
			$sx .= '</div>';			
			return($sx);
		}

	function actions($id,$or=0)
		{
			$menu = array();
			$dt = $this->le($id);
			$st = $dt['i_status'];
			if ($st == $or)
				{
					$sx =  bsmessage(lang('find.metadata_not_found'),3);

					if ($st == 0)
						{
							$sx .= h(lang('find.metadata_proceesing_actions'),3);
							$menu['1'] = lang('find.set_status_to').' <b>'.lang('find.tech_1').'</b>';
							$menu['9'] = lang('find.exlude_item').' <b>'.lang('find.exclude_item').'</b>';
						}
					$sx .= '<ul style="list-style:none;">';
					foreach ($menu as $link=>$label)
						{
							$sx .= '<li><a class="btn btn-outline-primary mb-2" href="'.(PATH.MODULE.'tech/item_status/'.$id.'/'.$link).'">'.$label.'</a></li>';
						}
					$sx .= '</ul>';
				} else {
					$sx = bsmessage('Processamento realizado',1);
				}
			return $sx;
		}

	function harvesting_metadata($id, $id2)
	{
		$ISBN = new \App\Models\Isbn\Isbn();
		$dt = $this->Find($id);
		$isbn = $dt['i_identifier'];
		$isbn_ok = 1;
		if (substr($isbn, 0, 3) == '978') {
			
		} else {
			$ISBN = $ISBN->isbns($isbn);
			$isbn = $ISBN['isbn13'];
		}

		/* Find */
		$Find = new \App\Models\API\Find();
		$dd['FIND'] = $Find->book($isbn, $id);

		if ($isbn_ok == 1)
		{
			/* BMS */
			$BMS = new \App\Models\API\BMS();
			$dd['BMS'] = $BMS->book($isbn, $id);
			$dd['status'] = '200';
		} else {
			$dd['status'] = '400';
			$dd['error'] = 'ISBN inválido';
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
			$sx .= bsc('<a href="' . PATH . MODULE. 'v/' . $wh[$r] . '">' . $Covers->get_Nail($wh[$r]) . '</a>', 2);
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
		$Style = new \App\Models\Style\MenuBtn();
		$sx = h(lang('find.tech_I'), 2);

		switch ($d1) {
			case 'isbn':
				/***************** Formulário Novo Item */
				$sx = $this->item_new_form();
				break;

			default:
			$its = array(
					'find.tech_IA1'=> PATH . MODULE . 'tech/prepare_I/isbn/edit/0',
					'find.tech_IB1'=> PATH . MODULE . 'tech/prepare_I/isbn/edit/0',
					'find.tech_IC1'=> PATH . MODULE . 'tech/prepare_I/isbn/edit/0',
					);
			$sx .= $Style->menuBtn($its);

			/******** Return */
			$url = PATH . MODULE . 'tech';
			$sx .= $Style->btnReturn($url);			

		}

		$sx = bs($sx);
		return $sx;
	}

	function show_item_link($line)
		{
			$sx = '';
			switch($line['i_status'])
				{
					case '0':
						$link = '<a href="' . (PATH . MODULE . 'tech/prepare_0/' . $line['id_i']) . '">';
						break;
					default:
						$link = '<a href="'.PATH.MODULE.'/'.$line['id_i'].'">';
						break;
				}
			$linka = '</a>';
			$sx .= $link.$line['i_identifier'].$linka;
			return $sx;
		}

	function last_aquisitions()
		{
			$sx = h(lang('find.last_aquisitions'), 6);
			$dt = $this->where('i_status',0)->orderBy('id_i desc')->limit(10)->findAll();
			$sx .= '<ul>';
			for($r=0;$r < count($dt);$r++)
				{
					$line = $dt[$r];
					$sx .= '<li>'.$this->show_item_link($line).'</li>';
				}
			$sx .= '</ul>';
			return $sx;
		}	

	function item_new_form()
	{
		$ISBN = new \App\Models\Isbn\Isbn();
		$ItensHistorico = new \App\Models\Library\ItensHistorico();
		$sx = '';
		$this->allowedFields = array(
			'',
			'tech_IA1_form1',
			'',	
			'tech_IA1_form2',
			'',
			'tech_IA1_form3',
			'tech_IA1_form4',
		);

		$sql = 'sql:id_bs:bs_name:library_place_bookshelf:bs_LIBRARY = \'' . LIBRARY . '\'';
		$this->typeFields = 
			array('hidden', 
			$sql.'*', 
			'hidden', 
			'hidden', 			
			'hidden', 
			'text:5'.'*', 
			'string:50'
			);

		$this->path_back = 'none';
		$this->table = '*';
		$this->id = 0;
		$this->pre = 'find.';
		$this->path = PATH . MODULE . 'tech/prepare_I/isbn';
		$sx .= h(lang('find.tech_IA'), 2);
		$sx .= h(lang('find.tech_IA1'), 4);

		/* Regra se não for automático */
		$sx .= form($this);

		$place = get("tech_IA1_form1");

		if ($this->saved == 1) 
			{	
			$isbn = explode(chr(10), get("tech_IA1_form3"));
			$tomboNr = (get("tech_IA1_form4"));
			$Tombo = new \App\Models\Book\Tombo();

			/* Atribuição automática de tombo */
			if ($tomboNr == '') { $auto = 1; } else { $auto = 0; }

			$sx .= '<table class="table">';
			$sx .= '<tr>';
			$sx .= '</tr>';


			for ($r = 0; $r < count($isbn); $r++) {
			/******************************** Exemplares */
				$nisbn = trim($isbn[$r]);

				/* Valida ISBN */				
				$nisbn = $ISBN->format($isbn[$r]);
				$ex = $Tombo->exemplar($nisbn);

				$dd['i_identifier'] = $nisbn;
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

				if (($dd['i_tombo'] == '') or ($dd['i_tombo'] == 0)) { $dd['i_tombo'] = 1; }

				$Tombo->insert($dd);
				$ItensHistorico->add_historicy($tomboNr,1);

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
		$Item = new \App\Models\Library\Itens();
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
				$tela .= '<a href="' . PATH . MODULE. 'v/' . $mani . '">';
				$tela .= $img;
				$tela .= '</a>';
			}
		}
		$tela .= '</div>';
		return $tela;
	}

}
