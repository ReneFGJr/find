<?php
class books extends CI_model
{
	function search()
	{
		$form = new form;
		$cp = array();
		array_push($cp,array('$H8','','',false,false));
		array_push($cp,array('$A','',msg('search_ISBN'),true,true));
		array_push($cp,array('$S50','','ISBN',true,true));
		array_push($cp,array('$B8','',msg('search'),false,true));
		$sx = $form->editar($cp,'');
		
		return($sx);
	}
	
	function place($p)
	{
		$p = trim($p);
		$sql = "select * from library_place where lp_name = '$p'";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) == 0)
		{
			print_r($rlt);
			echo "<hr>OPS, Library not found - ".$p;
			echo '<hr>';
			exit;
		}
		
		$id = $rlt[0]['id_lp'];
		return($id);
	}
	
	function save_urls($m,$url)
	{
		if (strlen($url) > 0)
		{
			$sql = "insert into find_manifestation_url
			(mu_m, mu_url)
			values
			($m,'$url')";
			$this->db->query($sql);
		}
		return(1);
	}
	
	function recover_urls($m)
	{
		$sql = "select * from find_manifestation_url where mu_m = $m";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		return($rlt);
	}

	function join_content($field,$dt)
		{
			
			if ($marc['totalItems'] > 0)
				{
					if (strlen($marc['descricao'] == 0)) { $marc['descricao'] = $vlr; }
				}
		}
	
	function locate($isbn,$id)
	{
		//https://isbndb.com/apidocs/v2/book/9788585637354
		//https://openlibrary.org/api/books?bibkeys=ISBN:9788585637354&callback=mycallback
		//https://openlibrary.org/api/books?bibkeys=ISBN:9788585637354&format=json
		$sx = '<div style="margin-top: 20px">';
		$sx .= '<h2>Importação de dados</h2>';
		$sx .= '<ul>';
		/* Google */
		$marc = $this->marc_api->book($isbn,$id);
		$google = $this->google_api->book($isbn,$id);
		$amazon = $this->amazon_api->book($isbn,$id);		
		$mercado = $this->mercadoeditorial_api->book($isbn,$id);
		$find = $this->find_rdf->book($isbn,$id);
		$total = 0;

		if (isset($google['descricao']) and (strlen($google['descricao']) > 0))
			{

			}

		/********************************
		 * Capa do mercado editorial 
		 * */
		if ((isset($mercado['cover'])) and (strlen($mercado['cover']) > 0))
			{
				$marc['cover'] = $mercado['cover'];
				$google['cover'] = $mercado['cover'];
				$find['cover'] = $mercado['cover'];
			}

		if ($marc['totalItems'] > 0)	
		{				
			$dt = $marc;
			$sx .= 'Marc21 imported<br>';
			$dt['item'] = $id;
			$this->process_register($isbn,$dt,'MARC2');
			$sx .= '<li style="color: green">Marc21 Metadata '.msg('imported').'</li>';	
			$total++;
		} else {
			$sx .= '<li style="color: grey">Marc21 '.msg('not_locate').'</li>';	
			/************************************** Processar Mercado Editorial ***********/
			if ($mercado['totalItems'] > 0)	
			{				
				$dt = $mercado;
				$sx .= 'Mercado Editorial imported<br>';
				$dt['item'] = $id;
				$this->process_register($isbn,$dt,'MERCE');
				$sx .= '<li style="color: green">Mercado Editorial '.msg('imported').'</li>';	
				$total++;
			} else {
				$sx .= '<li style="color: grey">Mercado Editorial '.msg('not_locate').'</li>';	
				
				/************************************** Processar Google ************/
				if ($find['totalItems'] > 0)
				{
					$dt = $find;	
					$dt['item'] = $id;
					$this->process_register($isbn,$dt,'FIND');
					$sx .= '<li style="color: green">Find Book '.msg('imported').'</li>';				
					$total++;
				} else {
					$sx .= '<li  style="color: grey">Find Book '.msg('not_locate').'</li>';	
				}
				/************************************** Processar Google ************/
				if ($google['totalItems'] > 0)
				{
					$dt = $google;	
					$dt['item'] = $id;
					$this->process_register($isbn,$dt,'GOOGL');
					$sx .= '<li style="color: green">Google Book '.msg('imported').'</li>';	
					$total++;
				} else {
					$sx .= '<li  style="color: grey">Google Book '.msg('not_locate').'</li>';	
					
					/************************************** Processar Amazon ***********/
					if ($amazon['totalItems'] > 0)	
					{				
						$dt = $amazon;
						$sx .= 'Amazon Book imported<br>';
						$dt['item'] = $id;
						$this->process_register($isbn,$dt,'AMAZO');
						$sx .= '<li style="color: green">Amazon Book '.msg('imported').'</li>';	
						$total++;
					} else {
						$sx .= '<li style="color: grey">Amazon Book '.msg('not_locate').'</li>';	
					}		
				}
			}
		}
		$sx .= '</ul>';
		if ($total == 0)
		{
			$m = 'Nenhum metadado localizado. ';
			$m .= 'Selecione abaixo o tipo de catalogação manual.';
			$sx .= message($m,4);
			
			$sx .= '<ul>';
			$sx .= '<li><a href="'.base_url(PATH.'preparation/tombo/'.$id.'/marc').'">Importar MARC</a></li>';
			$sx .= '<li><a href="'.base_url(PATH.'preparation/tombo/'.$id.'/manual').'">Manual Formulário</a></li>';
			$sx .= '</ul>';
			
			$this->load->model('sourcers');
			$sx .= $this->sourcers->show($isbn);
		}
		$sx .= '</div>';
		return($sx);
	}
	
	function update_item_title($isbn,$dt)
	{
		$dt['title'] = troca($dt['title'],'"',"´");
		$dt['title'] = troca($dt['title'],"'","´");
		$title = $dt['title'];
		
		$sql = "update find_item set
		i_titulo = '$title'
		where i_identifier = '$isbn'
		and i_library = '".LIBRARY."'";
		$rlt = $this->db->query($sql);
		return($sql);
	}
	
	
	function process_register($isbn,$dt,$type)
	{
		$sx = '';
		$debug = 0;
		/********************************** F.R.B.R. */
		$dt['isbn13'] = $isbn;
		$idioma = trim($dt['expressao']['idioma']);
		$genere = trim($dt['expressao']['genere']);
		$dt['title'] = troca($dt['title'],'"',"´");
		$dt['title'] = troca($dt['title'],"'","´");
		$title = $dt['title'];
		
		$rdf = new rdf;
		
		/* WORK */
		$idw = $this->work($dt['title']);
		$this->authors->authors_save($dt,$idw);			
		if ($debug) { echo '<br>Work'; }
		
		/* EXPRESSION */
		$language = $dt['expressao']['idioma'];
		$type = $dt['expressao']['genere'];
		$ide = $this->expression($dt['isbn13'],$language,$type);		
		$rdf->set_propriety($idw,'isAppellationOfExpression',$ide);
		if ($debug) { echo '<br>EXPRESSION'; }
		
		/* MANIFESTATION */
		$idm = $this->manifestation($dt['isbn13']);
		$rdf->set_propriety($ide,'isAppellationOfManifestation',$idm);
		if ($debug) { echo '<br>MANIFESTATION'; }
		
		/* Registro do Work ************************************/
		$this->save_urls($idm,$dt['url']);
		$this->covers->save($dt['cover'],$isbn);
		if ($debug) { echo '<br>URL'; }		
		
		/* ASSOCIA ITEM COM A MANIFESTACAO ****************************/
		$sql = "update find_item set 
		i_manitestation = $idm,
		i_titulo = '$title' 
		where id_i = ".$dt['item'];
		$this->db->query($sql);
		
		/* Manifestation - Pages *********************************************/	
		$pags = sonumero($dt['pages']);
		if ($pags > 0)
		{
			$pags .= ' p.';
			
			/* Manifestation - PrePages ******************************/	
			if (isset($dt['pages_pre']))
			{
				$ppags = trim($dt['pages_pre']);
				if (strlen($ppags) > 0)
				{
					$pags = strtolower(trim($ppags)).', '.$pags;
				}
			}
			$idc = $rdf->rdf_concept_create('Pages', $pags, '', $idioma);
			$rdf->set_propriety($idm,'hasPage',$idc);				
		}			
		
		/* Manifestation - Editora */	
		$editora = $dt['editora'];
		if (strlen($editora) > 0)
		{
			if (strpos($editora,';') > 0)
			{
				$editora = substr($editora,0,strpos($editora,';'));
			}
			$idc = $rdf->rdf_concept_create('Editora', $editora, '', $idioma);
			$rdf->set_propriety($idm,'isPublisher',$idc);
		}
		
		/* Manifestation - Editora */	
		if (isset($dt['place']))
		{
			$place = $dt['place'];
			$place = troca($place,'[','');
			$place = troca($place,']','');
			if (strlen($place) > 0)
			{
				if (strpos($place,';') > 0)
				{
					$place = substr($place,0,strpos($place,';'));
				}
				$idc = $rdf->rdf_concept_create('Place', $place, '', $idioma);
				$rdf->set_propriety($idm,'isPlaceOfPublication',$idc);
			}
		}		
		
		/* Manifestation - Descriptions */	
		$txt = trim(troca($dt['descricao'],"'","´"));
		if (strlen($txt) > 0)
		{
			$idn = $rdf->rdf_name($txt);
			$rdf->set_propriety($idm,'dc:description',0,$idn);
		}
		
		/* Manifestatiion - Peso */
		
		if (isset($dt['weight']))
		{
			$txt = trim($dt['weight']);
			if (strlen($txt) > 0)
			{
				$idc = $rdf->rdf_concept_create('Weight', $txt, '', $idioma);
				$rdf->set_propriety($idm,'hasWeight',$idc);
			}
		}			
		
		/* Manifestation - Serie e Volume */	
		if (isset($dt['serie']))
		{
			$txt = trim(troca($dt['serie'],"'","´"));
			if (strlen($txt) > 0)
			{
				$idc = $rdf->rdf_concept_create('SerieName', $txt, '', $idioma);
				$rdf->set_propriety($idm,'hasSerieName',$idc);
			}
		}	
		
		if (isset($dt['volume']))
		{
			$txt = $dt['volume'];
			$idc = $rdf->rdf_concept_create('Number', $txt, '', $idioma);
			$rdf->set_propriety($idm,'hasVolumeNumber',$idc);
			
		}
		
		if (isset($dt['data']))
		{
			$txt = $dt['data'];
			$ori = '';
			if (strpos($txt,'%') > 0)
			{
				$ori = substr($txt,strpos($txt,'%')+1,strlen($txt));
				$txt = substr($txt,0,strpos($txt,'%'));				
			}
			$idc = $rdf->rdf_concept_create('Date', $txt, $ori, $idioma);
			$rdf->set_propriety($idm,'dateOfPublication',$idc);
			
		}		
		
		/* Manifestation - Subject */	
		if (isset($dt['subject']) and (count($dt['subject']) > 0))
		{
			for ($r=0;$r < count($dt['subject']);$r++)
			{
				$txt = Nbr_author($dt['subject'][$r],18);
				$idc = $rdf->rdf_concept_create('Subject', $txt, '', $idioma);
				$rdf->set_propriety($idm,'hasSubject',$idc);	
			}
		}
		
		/************ Atualiza Item ******************************/
		$this->book_preparations->link_book($idm,$dt['item']);
		$this->books_item->item_status($dt['item'],1);
		return($idm);
	}
	
	/*************************************************************************** WORK ***/
	function work($title)
	{
		/* Limpa titulo */
		while (strpos('_'.$title,'  ') > 0)
		{
			$title = troca($title,'  ',' ');
		}
		$title = troca($title,"'","´");
		
		/******************************* SALVA EXPRESSAO ************/
		$rdf = new rdf;
		$idt = $rdf->frbr_name($title);
		$idw = $rdf->rdf_concept($idt, 'Work');
		$rdf->set_propriety($idw,'prefLabel',0,$idt);
		return($idw);		
	}
	
	
	/***************************************************************************** EXPRESSION ***********/
	function expression($isbn,$language,$type)
	{
		$lang = $this->languages->code($language);
		if (strlen($lang) == 0)
		{
			//echo "OPS NOT LINGUAGE [".$language."]";
			$lang = 'pt';
			/* exit; */
		}
		
		$gen = $this->generes->code($type);
		if (strlen($gen) == 0)
		{
			echo "OPS NOT GENERE [".$gen."]";
			exit;
		}
		
		$term = 'ISBN:'.$isbn.':'.$gen.':'.$lang;
		
		/******************************* SALVA EXPRESSAO ************/
		$rdf = new rdf;
		$idt = $rdf->frbr_name($term);
		$ide = $rdf->rdf_concept($idt, 'Expression');
		
		/************************************************************/
		$idgen = $rdf->frbr_name($gen);
		$idg = $rdf->rdf_concept($idt, 'FormExpression');
		$rdf->set_propriety($ide,'hasFormExpression',$idg);
		
		$idlan = $rdf->frbr_name($lang);
		$idl = $rdf->rdf_concept($idlan, 'Linguage');
		$rdf->set_propriety($ide,'hasFormExpression',$idl);
		
		return($ide);
	}
	
	/********************************************** manifestation ***********/
	function manifestation($isbn13)	
	{
		$rdf = new rdf;
		$term = 'ISBN:'.$isbn13;
		$idt = $rdf->frbr_name($term);
		$idm = $rdf->rdf_concept($idt, 'Manifestation');
		return($idm);
	}
	
	function showx($dt,$type)
	{
		$sx = '';
		$sx .= '<div class="container">';	
		if (count($dt) == 0)
		{
			return("Sem informações");
		}		
		$class = 'img_cover';
		$ed = '<a href="'.base_url(PATH.'preparation/edit/'.$dt['id_m'].'/5').'"><sup>[ed]</sup></a>';
		
		switch($type)
		{
			case '2':
				$img = '<img src="'.$dt['img'].'" class="img-fluid '.$class.'">';
				$sx .= '<div class="row">';
				$sx .= '<div class="col-md-1">'.$img.'</div>';
				$sx .= '<div class="col-md-11">';
				$sx .= '<div class="s1_title">'.$dt['w_title'].$ed.'</div>';
				$sx .= '$[BODY]';
				$sx .= '</div>';
				$sx .= '</div>';
			break;
			
			/******************************** Default *******************/
			default:				
			if (strpos($dt['img'],'no_cover') or (perfil("#ADM")))
			{ 
				$class = '' ; 
				$compl = $this->covers->btn_seek_cover($dt['m_isbn13']);
				$compl .= ' | '.cr().$this->covers->btn_upload($dt['m_isbn13']).cr();
				$compl .= ' | '.cr().$this->covers->btn_upload_link($dt['m_isbn13']).cr();
			}
			else {				
				$compl = '';				
			}
			$img = '<img src="'.$dt['img'].'" class="img-fluid '.$class.'">';
			$sx .= '<div class="row">';
			$sx .= '<div class="col-md-3">'.$img.$compl.'</div>';
			$sx .= '<div class="col-md-9">';
			$sx .= '<div class="s1_title">'.$dt['w_title'].$ed.'</div>';
			
			/* Authors */
			$sx .= '<div class="s1_authors"><i>';
			for ($r=0;$r < count($dt['authors']);$r++)
			{
				if ($r > 0) { $sx .= '; ';}
				$sx .= $dt['authors'][$r]['a_name'];
			}
			$sx .= '</i></div>';
			
			$sx .= '<div class="s1_isbn13">';
			$sx .= 'ISBN13: <b>'.$dt['isbn']['isbn13f'].'</b>';
			$sx .= '</div>';
			$sx .= '<div class="s1_isbn10">';
			$sx .= 'ISBN10: <b>'.$dt['isbn']['isbn10f'].'</b>';
			$sx .= '</div>';
			
			$sx .= '<div class="s1_year">';
			$year = $dt['m_year'];
			if ($year == 0) { $year = '&nbsp;-&nbsp;'; }
			$sx .= 'Edição: <b>'.$year.'</b>';
			$sx .= '</div>';
			
			$dts = $dt['rdf'];
			for ($r=0;$r < count($dts);$r++)
			{
				$class = $dts[$r]['c_class'];
				$sx .= '<div class="'.$class.'">';
				$sx .= msg($class);
				$sx .= ': ';
				$sx .= $dts[$r]['n_name'];
				$sx .= '</div>';
			}
			
			/* Links */
			$sx .= '<div class="s1_links"><i>';
			for ($r=0;$r < count($dt['links']);$r++)
			{
				$line = $dt['links'][$r];
				if ($r > 0) { $sx .= '<br/>'; }
				$sx .= '<a href="'.$line['mu_url'].'" target="_blank">';
				$sx .= $line['mu_url'];
				$sx .= '</a>'.cr();
			}				
			$sx .= '</div>';
			
			
			/************* Itens ***********************/
			$exemplares = $this->books->itens($dt['id_m']);
			$sx .= '<h4>'.msg('Exemplares').'</h4>';
			$sx .= '<div>'.$exemplares.'</div>';
			
			/************ Classificação ****************/
			$ed = '<a href="'.base_url(PATH.'preparation/tombo/'.$dt['id_m'].'/2').'"> <sup>[ed]</sup></a>';
			$classifications = $this->classifications->itens_classification($dt['id_m']);
			$sx .= '<h4>'.msg('Classification').$ed.'</h4>';
			$sx .= '<div>'.$classifications.'</div>';
			
			/************* Assuntos ********************/
			$ed = '<a href="'.base_url(PATH.'preparation/tombo/'.$dt['id_m'].'/3').'"> <sup>[ed]</sup></a>';
			$subjects = $this->subjects->itens_subjects($dt['id_m']);
			$sx .= '<h4>'.msg('Subjects').$ed.'</h4>';
			$sx .= '<div>'.$subjects.'</div>';
			
			$sx .= '</div>';
			$sx .= '</div>';
			$sx .= '</div>';
			
		break;
	}
	
	return($sx);
}

function export($id)
{
	$dt = $this->le_m($id);
	$img = '<img src="'.$dt['img'].'" class="img-fluid">';
	$sx .= '<div class="row">';
	$sx .= '<div class="col-md-3">'.$img.'</div>';
	$sx .= '<div class="col-md-9">';
	$sx .= '<div class="s1_title">'.$dt['w_title'].'</div>';
	
	/* Authors */
	$sx .= '<div class="s1_authors"><i>';
	for ($r=0;$r < count($dt['authors']);$r++)
	{
		if ($r > 0) { $sx .= '; ';}
		$sx .= $dt['authors'][$r]['a_name'];
	}
	$sx .= '</i></div>';
	
	$sx .= '<div class="s1_isbn13">';
	$sx .= 'ISBN13: <b>'.$dt['isbn']['isbn13f'].'</b>';
	$sx .= '</div>';
	$sx .= '<div class="s1_isbn10">';
	$sx .= 'ISBN10: <b>'.$dt['isbn']['isbn10f'].'</b>';
	$sx .= '</div>';
	
	$sx .= '<div class="s1_isbn10">';
	$year = $dt['m_year'];
	if ($year == 0) { $year = '&nbsp;-&nbsp;'; }
	$sx .= 'Edição: <b>'.$year.'</b>';
	$sx .= '</div>';
	
	$dts = $dt['rdf'];
	for ($r=0;$r < count($dts);$r++)
	{
		$class = $dts[$r]['c_class'];
		$sx .= '<div class="'.$class.'">';
		$sx .= msg($class);
		$sx .= ': ';
		$sx .= $dts[$r]['n_name'];
		$sx .= '</div>';
	}
	$sx .= '</div>';
	$sx .= '</div>';
}

function vitrine()
{
	$sx = '<h1>Vitrine</h1>';
	$sql = "select i_manitestation, i_identifier, i_titulo 
	from find_item 
	where i_library = '".LIBRARY."' and i_manitestation <> 0
	group by i_manitestation, i_identifier, i_titulo ";
	
	$rlt = $this->db->query($sql);
	$rlt = $rlt->result_array();
	$sx = '<div class="container"><div class="row">';
	for ($r=0;$r < count($rlt);$r++)
	{
		$line = $rlt[$r];
		$id = $line['i_manitestation'];
		$isbn = $line['i_identifier'];
		$img = $this->covers->img($isbn);
		$link = '<a href="'.base_url(PATH.'v/'.$id).'">';
		$linka = '</a>';
		$sx .= '<div class="col-3 col-lg-2 col-md-2">';
		$sx .= $link;
		$class = ' img_cover ';
		if (strpos($img,'no_cover.png'))
		{ $class = ''; }
		$sx .= '<img src="'.$img.'" class="img-fluid '.$class.'" style="margin: 20px 10px 10px 10px;">';
		$sx .= $linka;
		$title = trim($line['i_titulo']);
		if (strlen($title) > 60)
		{
			$title = substr($title,0,60);
			$ch = substr($title,strlen($title)-1,1);
			while (($ch != ' ') and (strlen($title) > 2))
			{
				$title = substr($title,0,strlen($title)-1);
				$ch = substr($title,strlen($title)-1,1);
			}
			$title .= ' ...';
		}
		$sx .= $title;
		$sx .= '</div>';
	}
	$sx .= '</div></div>';
	return($sx);
}

function le_item($id)
{
	$sql = "select * from find_item where id_i = ".round($id);
	$rlt = $this->db->query($sql);
	$rlt = $rlt->result_array();
	if (count($rlt) > 0)
	{
		return($rlt[0]);	
	} else {
		return(array());
	}
	
}

function i($id)
{
	$dt = $this->le_item($id);
	$sx = '';
	$sx .= '<div class="container"><div class="row"><div class="col-12">';
	$sx .= '<table class="table">';
	foreach ($dt as $key => $value) {
		$sx .= '<tr>';
		$sx .= '<td width="25%" align="right">'.$key;
		$sx .= '<td>'.'<b>'.$value.'</b>';
		$sx .= '</tr>';
	}
	$sx .= '</table>';
	$sx .= '</div></div></div>';
	return($sx);
}

function manual_api($id)
{
	$title = get("q");
	if (strlen($title) > 0)
	{
		$title = nbr_author($title,18);
		$dti = $this->le_item($id);
		$isbn = $dti['i_identifier'];				
		$dt['expressao'] = array('idioma'=>'pt','genere'=>'books');
		$dt['title'] = $title;
		$dt['data'] = '';
		$dt['url'] = '';
		$dt['authors'] = array();
		$dt['cover'] = '';
		$dt['editora'] = '';
		$dt['descricao'] = '';
		$dt['pages'] = '';
		$dt['w_title'] = $title;
		$dt['item'] = $id;
		$this->process_register($isbn,$dt,'MANUA');
		$dt = $this->le_item($id);
		redirect(base_url(PATH.'v/'.$dt['i_manitestation']));
	} 	
}

function exemplar($isbn)
{
	$sql = "select max(i_exemplar) as i_exemplar, max(i_manitestation) as i_manitestation
	from find_item
	where i_identifier = '$isbn'
	and i_library = '".LIBRARY."'";
	
	$rlt = $this->db->query($sql);
	$rlt = $rlt->result_array();
	if (count($rlt) > 0)
	{
		$line = $rlt[0];
		return(array($line['i_exemplar'],$line['i_manitestation']));
	} else {
		return(0);
	}
}	

function manual_form($id)
{
	/* Grava dados */
	$this->manual_api($id);
	
	/* Formulário */
	$sx = '';
	$sx .= '<button class="btn btn-outline-primary" id="cat_manual">';
	$sx .= msg('cat_manual');
	$sx .= '</button>';
	
	$sx .= '<div class="" style="display: none;" id="cat_manual_form">';
	$sx .= '<span class="small">'.msg('title').'</span>';
	$sx .= '<textarea id="manual_title" class="form-control" rows=2>';
	$sx .= get("manual_title");
	$sx .= '</textarea>';
	$sx .= '<button id="manual_submit">'.msg("submit").'</button>';
	$sx .= '</div>';
	
	$sx .= '
	<script> 
	$("#cat_manual").click(function()
	{ 
		$("#cat_manual_form").toggle("slow"); 
	}
);
$("#manual_submit").click(function()
{ 
	var title = $("#manual_title").val();
	if (title.length != 0)
	{
		window.location.href ="?q="+title; 
	} else {
		alert("ERRO");
	}
}
); 
</script>'.cr();			
return($sx);
}
}

/* RDF SHOW */
function rdf_show_Work($dt)
{
	$CI = &get_instance();
	$CI->load->model("books_item");
	
	$rdf = new rdf;		
	$manif = array();
	$idc = $dt['id_cc'];
	$dt = works($idc);
	$sx = '';
	
	/********************************** Expressoes ***********/
	if (count($dt['expression']) > 0)
	{
		$ide = $dt['expression'][0];
		$dte = $rdf->le_data($ide);
		$manif = $rdf->extract_id($dte,'isAppellationOfManifestation');
	}
	if (count($manif) > 0)
	{
		for ($r=0;$r < count($manif);$r++)
		{
			$idm = $manif[$r];
			$sx = manifestation($idm,$dt);
			$sx .= $CI->books_item->mainfestation_item($idm);
		}
	}
	
	return($sx);
}

function rdf_show_Date($dt)
{
	$sx = '';
	$sx .= '<h1>'.$dt['n_name'].'</h1>';
	return($sx);
}

function rdf_show_Manifestation($dt)
{
	$CI = &get_instance();
	$CI->load->model("books_item");
	
	$sx = 'Nothing';
	$rdf = new rdf;
	$CI = &get_instance();
	$expression = array();
	$work = array();
	$idcc = $dt['id_cc'];
	
	$dts = $rdf->le_data($dt['id_cc']);
	/********************************** Expressoes ***********/		
	$expression = $rdf->extract_id($dts,'isAppellationOfManifestation',$idcc);
	
	if (count($expression) > 0)
	{
		$ide = $expression[0];
		$dt = $rdf->le_data($ide);
		$work = $rdf->extract_id($dt,'isAppellationOfExpression',$ide);
	}
	
	if (count($work) > 0)
	{
		$idw = $work[0];
		$dtw = works($idw);
		$sx = manifestation($idcc,$dtw);
	}
	
	$sx .= $CI->books_item->mainfestation_item($idcc);
	
	return($sx);
}

function works($id)
{
	$CI = &get_instance();
	$au = 0;
	$d = array();
	$d['title'] = '';
	$d['authors'] = '';
	$expression = array();
	
	$authors = '';
	$rdf = new rdf;
	$dts = $rdf->le_data($id);
	/******************************************* WORKS ********************/
	for ($r=0;$r < count($dts);$r++)
	{
		$line = $dts[$r];
		$prop = $line['c_class'];
		$vlr = $line['n_name'];
		$lng = $line['n_lang'];
		$idr1 = $line['d_r1'];
		$idr2 = $line['d_r2'];
		
		/* Links */
		$link = '<a href="'.base_url(PATH.'v/'.$idr2).'" class="'.$prop.'">';
		$linka = '</a>';
		
		$rlink = '<a href="'.base_url(PATH.'v/'.$idr1).'" class="'.$prop.'">';
		$rlinka = '</a>';
		
		switch($prop)
		{
			case 'prefLabel':
				$d['title'] = $rlink.$vlr.$rlinka;
			break;
			
			case 'hasAuthor':
				if ($au > 0) { $authors .= '; '; }
				$authors .= $link.$vlr.$linka;
				$au++;
			break;
			
			case 'isAppellationOfExpression':
				array_push($expression, $idr2);
			break;
		}
	}
	
	/********************************** Autores **************/
	if ($au > 0) 
	{ 
		$d['authors'] = $authors;
	}
	$d['expression'] = $expression;
	return($d);		
}

function manifestation($id,$d)
{
	$CI = &get_instance();
	$sx = '';
	$desc = '';
	$pags = '';
	$date = '';
	$rdf = new rdf;
	$dd = $rdf->le($id);				
	$isbn = $dd['n_name'];
	$editora = '';
	$img = $CI->covers->img(sonumero($isbn));
	
	
	$dt = $rdf->le_data($id);
	$subsj = '';		
	for ($r=0;$r < count($dt);$r++)
	{
		$prop = trim($dt[$r]['c_class']);
		$vlr = $dt[$r]['n_name'];
		$idx = $dt[$r]['d_r2'];
		if ($idx == $id)
		{
			$idx = $dt[$r]['d_r1'];
		}
		$link = '<a href="'.base_url(PATH.'v/'.$idx).'">';
		$linka = '</a>';
		switch($prop)
		{
			
			case 'hasSubject':
				if (strlen($subsj) > 0)
				{
					$subsj .= '. ';
				}
				$subsj .= $link. '<span class="btn-outline-primary">'.$vlr.'</span>'.$linka.'';
			break;
			
			case 'description':
				$desc = $vlr;
			break;
			
			case 'isPublisher':
				$editora = msg('Editora').': '.$link.$vlr.$linka;
			break;
			
			case 'hasPage':
				$pags = msg('Pages').': '.$vlr;
			break;				
			
			case 'dateOfPublication':
				$date = msg('Year').': '.$vlr;
			break;	
			
		}
	}
	$sx .= '<div class="col-2">';
	$sx .= '<img src="'.$img.'" class="img-fluid">';
	if (perfil("#ADM"))
	{
		$sx .= $CI->covers->btn_seek_cover($isbn);
		$sx .= ' | ';
		$sx .= $CI->covers->btn_upload($isbn);
	}
	$sx .= '</div>';
	$sx .= '<div class="col-10">';
	/* Title */
	$sx .= '<div class="work_title">'.$d['title'].'</div>';
	$sx .= '<div class="work_author">'.$d['authors'].'</div>';
	$isbn = $CI->isbn->isbns(sonumero($isbn));
	$sx .= '<div class="manifestation_date">'.$date.'</div>';
	$sx .= '<div class="manifestation_editora">'.$editora.'</div>';
	$sx .= '<div class="manifestation_isbn">ISBN10: '.$isbn['isbn10f'].'</div>';
	$sx .= '<div class="manifestation_isbn">ISBN13: '.$isbn['isbn13f'].'</div>';
	$sx .= '<div class="manifestation_pags">'.$pags.'</div>';
	if (strlen($subsj) > 0)
	{
		$sx .= '<div class="manifestation_subject" style="margin-top: 20px;">Assuntos: '.$subsj.'.</div>';
	}
	$sx .= '<div class="manifestation_descrition">'.msg('description').': '.$desc.'</div>'.cr();
	
	$sx .= '</div>';
	return($sx);
}
?>
