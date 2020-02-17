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

	function recover_id_for_isbn($isbn)
	{
		$sql = "select * from find_manifestation where m_isbn13 = '$isbn'";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) > 0)
		{
			$id = $rlt[0]['id_m'];
		} else {
			$id = 0;
		}
		return($id);
	}
	function recover_urls($m)
	{
		$sql = "select * from find_manifestation_url where mu_m = $m";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		return($rlt);
	}

	function marc_import($t,$isbn)
	{
		$dt = $this->marc_api->book($t);
		$dt['isbn'] = $this->isbn->isbns($isbn);
		$isbn = $dt['isbn']['isbn13'];
		$this->process_register($isbn,$dt,'MARC2');
		$sx = 'Marc 21 imported<br>';

		$this->locate($isbn);

		return($sx);
	}

	function locate($isbn,$id)
	{
		$sx = '';
		/* Google */
		$google = $this->google_api->book($isbn,$id);
		$amazon = $this->amazon_api->book($isbn,$id);

		/************************************** Processar Google ************/
		if ($google['totalItems'] > 0)
		{
			$dt = $google;	
			$dt['item'] = $id;
			$this->process_register($isbn,$dt,'GOOGL');
			$sx .= 'Google Book imported<br>';	
		}
		/************************************** Processar Amazon ***********/
		if ($amazon['totalItems'] > 0)	
		{				
			$dt = $amazon;
			$sx .= 'Amazon Book imported<br>';
			$dt['item'] = $id;
			$this->process_register($isbn,$dt,'AMAZO');
		}
		return($sx);
	}


	function process_register($isbn,$dt,$type)
	{
		$sx = '';
		$debug = 1;
		/********************************** F.R.B.R. */
		$dt['isbn13'] = $isbn;
		$idioma = trim($dt['expressao']['idioma']);
		$genere = trim($dt['expressao']['genere']);
		$dt['title'] = troca($dt['title'],'"',"´");
		$dt['title'] = troca($dt['title'],"'","´");

		$rdf = new rdf;

		/* WORK */
		$idw = $this->work($dt['title']);
		$this->authors->authors_save($dt,$idw);			
		if ($debug) { echo '<br>Work'; }

		/* EXPRESSION */
		$language = $dt['expressao']['idioma'];
		$type = $dt['expressao']['genere'];
		$ide = $this->expression($language,$type);
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
		$sql = "update find_item set i_manitestation = $idm where id_i = ".$dt['item'];
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
			$idc = $rdf->rdf_concept_create('Number', $txt, $ori, $idioma);
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
	function update_id($idw,$iddw,$t)
	{
		switch($t)
		{
			case 'w':
			$sql = "update find_work set w_id = $iddw where id_w = $idw";
			$this->db->query($sql);
			break;

			case 'm':
			$sql = "update find_manifestation set m_id = $iddw where id_m = $idw";
			$this->db->query($sql);
			break;

			default:
			echo "OPS. Update_id Error";
			exit;
			break;
		}				

		return(1);
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
	function expression($language,$type)
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
			echo "OPS NOT GENERE ".$gen;
			exit;
		}

		/******************************* SALVA EXPRESSAO ************/
		$rdf = new rdf;
		$term = $gen.':'.$lang;
		$idt = $rdf->frbr_name($term);
		$ide = $rdf->rdf_concept($idt, 'Expression');
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

	function le_m($id)
	{
		if ($id == 0)
		{
			$dt = array();
			return($dt);
		}
		$sql = "select * from find_manifestation
		where id_m = $id";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) > 0)
		{
			$line = $rlt[0];
			$line['isbn'] = $this->isbn->isbns($line['m_isbn13']);
			$line['img'] = $this->covers->img($line['m_isbn13']);
			$line['authors'] = $this->authors->le_authors($line['id_w']);

			$rdf = new rdf;
			$line['rdf'] = $rdf->le_data($line['m_id']);
			$line['links'] = $this->recover_urls($id);
		} else {
			$line = array();
		}

		return($line);
	}

	function show($dt,$type)
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
		$sql = "select * from find_manifestation
		INNER join find_item ON id_m = i_manitestation
		where i_library = '".LIBRARY."' ";

		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$sx = '<div class="container"><div class="row">';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$id = $line['id_m'];
			$isbn = $line['m_isbn13'];
			$img = $this->covers->img($isbn);
			$link = '<a href="'.base_url(PATH.'m/'.$id).'">';
			$linka = '</a>';
			$sx .= '<div class="col-3 col-lg-2 col-md-2">';
			$sx .= $link;
			$class = ' img_cover ';
			if (strpos($img,'no_cover.png'))
				{ $class = ''; }
			$sx .= '<img src="'.$img.'" class="img-fluid '.$class.'" style="margin: 20px 10px 10px 10px;">';
			$sx .= $linka;
			$title = trim($line['w_title']);
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

	function isbn_exists($isbn)
	{
		$sql = "select * from find_manifestation where m_isbn13 = '$isbn'";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) > 0)
		{
			return($rlt[0]['id_m']);
		} else {
			return(0);
		}
	}



	function itens($id)
	{		
		$wh = '(i_library = '.LIBRARY.') and ';
		$sql = "select count(*) as total, lp_name, i_status 
		FROM find_manifestation 
		INNER JOIN find_item ON id_m = i_manitestation
		INNER JOIN library_place ON i_library_place = id_lp
		where $wh (id_m = ".$id.")
		group by lp_name, i_status
		order by lp_name, i_status desc ";

		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		$sx = '<ul class="libraries">';
		$xp = '';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$p = $line['lp_name'];
			if ($xp != $p)
			{
				$sx .= '<li>';
				$sx .= $line['lp_name'];
				$sx .= '</li>';
			}
			
			$sx .= '<ul class="libraries_item_status"><li>';
			$sx .= msg('item_status_'.$line['i_status']).' ('.$line['total'].') ';
			$sx .= '</li></ul>';
		}
		$sx .= '</ul>';
		return($sx);
	}
	function exemplar($isbn)
	{
		$sql = "select max(i_exemplar) as i_exemplar
		from find_item
		where i_identifier = '$isbn'
		and i_library = '".LIBRARY."'";

		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) > 0)
		{
			$line = $rlt[0];
			return($line['i_exemplar']);
		} else {
			return(0);
		}
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
		print_r($dt);
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
		$rdf = new rdf;
		$CI = &get_instance();
		$d = array();
		$d['title'] = $dt['n_name'];
		$d['authors'] = '';

		$authors = '';
		$au = 0;
		$expression = array();
		$manif = array();

		$dts = $rdf->le_data($dt['id_cc']);
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

			switch($prop)
			{
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


		/********************************** Expressoes ***********/
		if (count($expression) > 0)
		{
			$ide = $expression[0];
			$dt = $rdf->le_data($ide);
			$manif = $rdf->extract_id($dt,'isAppellationOfManifestation');
		}

		if (count($manif) > 0)
		{
			$idm = $manif[0];				
			$sx = manifestation($idm,$d);
		}

		return($sx);
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
		$img = $CI->covers->img(sonumero($isbn));


		$dt = $rdf->le_data($id);
		for ($r=0;$r < count($dt);$r++)
		{
			$prop = $dt[$r]['c_class'];
			$vlr = $dt[$r]['n_name'];
			switch($prop)
			{
				case 'description':
				$desc = $vlr;
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
		$sx .= '</div>';
		$sx .= '<div class="col-10">';
		/* Title */
		echo '<pre>';
		print_r($dt);
		echo '</pre>';
		$sx .= '<div class="work_title">'.$d['title'].'</div>';
		$sx .= '<div class="work_author">'.$d['authors'].'</div>';
		$isbn = $CI->isbn->isbns(sonumero($isbn));
		$sx .= '<div class="manifestation_date">'.$date.'</div>';
		$sx .= '<div class="manifestation_isbn">ISBN10: '.$isbn['isbn10f'].'</div>';
		$sx .= '<div class="manifestation_isbn">ISBN13: '.$isbn['isbn13f'].'</div>';
		$sx .= '<div class="manifestation_pags">'.$pags.'</div>';
		$sx .= '<div class="manifestation_descrition">'.msg('description').': '.$desc.'</div>'.cr();
		$sx .= '</div>';
		return($sx);
	}
	?>
