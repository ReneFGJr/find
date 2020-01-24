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

		function marc_import($t)
		{
			$dt = $this->marc_api->book($t);
			$isbn = $dt['isbn']['isbn13'];
			$this->process_register($isbn,$dt,'MARC2');
			$sx = 'Marc 21 imported<br>';

			$this->locate($isbn);

			return($sx);
		}

		function locate($isbn)
		{
			$sx = '';

			/* Google */
			$google = $this->google_api->book($isbn);
			$amazon = $this->amazon_api->book($isbn);

			
			/************************************** Processar Google ************/
			if ($google['totalItems'] > 0)
			{
				$dt = $google;	
				$this->process_register($isbn,$dt,'GOOGL');
				$sx .= 'Google Book imported<br>';	
			}

			/************************************** Processar Amazon ***********/
			if ($amazon['totalItems'] > 0)	
			{				
				$dt = $amazon;
				$sx .= 'Amazon Book imported<br>';
				$this->process_register($isbn,$dt,'AMAZO');
			}
			return($sx);
		}


		function process_register($isbn,$dt,$type)
		{
			$sx = '';
			/********************************** F.R.B.R. */
			$this->query_isbn($isbn);

			$dt['isbn13'] = $isbn;
			$idioma = trim($dt['expressao']['idioma']);
			$genere = trim($dt['expressao']['genere']);

			$dt['work'] = $this->work($dt['title'],$idioma);
			$dt['expression'] = $this->expression($dt['work'],$idioma,$genere);
			$dt['manifestation'] = $this->manifestation($dt['expression'],$dt);
			$this->save_urls($dt['manifestation'],$dt['url']);
			$this->authors->authors_save($dt);
			$this->covers->save($dt['cover'],$dt['manifestation']);		

			/* WORK */
			$rdf = new rdf;
			$idn = $rdf->rdf_name('W'.$isbn);
			$iddw = $rdf->rdf_concept($idn,'Work');
			$this->update_id($dt['work'],$iddw,'w');

			/* EXPRESSION */
			$idn = $rdf->rdf_name('E'.strzero($dt['expression'],9));
			$idde = $rdf->rdf_concept($idn,'Expression');
			$this->update_id($dt['expression'],$idde,'e');

			/* MANIFESTATION */
			$idn = $rdf->rdf_name('M'.strzero($dt['manifestation'],9));
			$iddm = $rdf->rdf_concept($idn,'Manifestation');
			$this->update_id($dt['manifestation'],$iddm,'m');	

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

					$idc = $rdf->rdf_concept_create('Pages', $pags, '', $idioma);
					$rdf->set_propriety($iddm,'hasPage',$idc);				
				}
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
				$rdf->set_propriety($iddm,'isPublisher',$idc);
			}

			/* Manifestation - Descriptions */	
			$txt = trim(troca($dt['descricao'],"'","´"));
			if (strlen($txt) > 0)
			{
				$idn = $rdf->rdf_name($txt);
				$rdf->set_propriety($iddm,'dc:description',0,$idn);
			}

			/* Manifestatiion - Peso */
			
			if (isset($dt['weight']))
			{
				$txt = trim($dt['weight']);
				if (strlen($txt) > 0)
				{
					$idc = $rdf->rdf_concept_create('Weight', $txt, '', $idioma);
					$rdf->set_propriety($iddm,'hasWeight',$idc);
				}
			}			

			/* Manifestation - Serie e Volume */	
			if (isset($dt['serie']))
			{
				$txt = trim(troca($dt['serie'],"'","´"));
				if (strlen($txt) > 0)
				{
					$idc = $rdf->rdf_concept_create('SerieName', $txt, '', $idioma);
					$rdf->set_propriety($iddm,'hasSerieName',$idc);
				}
			}	

			if (isset($dt['volume']))
			{
				$txt = $dt['volume'];
				$idc = $rdf->rdf_concept_create('Number', $txt, '', $idioma);
				$rdf->set_propriety($iddm,'hasVolumeNumber',$idc);

			}

			/* Manifestation - Subject */	
			if (isset($dt['subject']) and (count($dt['subject']) > 0))
			{
				for ($r=0;$r < count($dt['subject']);$r++)
				{
					$txt = Nbr_author($dt['subject'][$r],18);
					$idc = $rdf->rdf_concept_create('Subject', $txt, '', $idioma);
					$rdf->set_propriety($iddm,'hasSubject',$idc);	
				}
			}
			

			/* Mostra Item */
			$d = $this->le_m($dt['manifestation']);
			$sx .= $this->show($d,1);
			return($sx);
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

				case 'e':
				$sql = "update find_expression set e_id = $iddw where id_e = $idw";
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
		function work($title,$language)
		{
			/* Limpa titulo */
			while (strpos('_'.$title,'  ') > 0)
			{
				$title = troca($title,'  ',' ');
			}
			$title = troca($title,"'","´");

			$sql = "select * from find_work where w_title = '".$title."'";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) == 0)
			{
				$xsql = "insert into find_work
				(w_title)
				values
				('$title')";
				$xrlt = $this->db->query($xsql);					
				sleep(1);
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();
			}

			if (count($rlt) == 0)
			{
				echo "OPS - ERRO DE GRAVAÇÃO";
			}
			$id = $rlt[0]['id_w'];
			return($id);
		}


		/***************************************************************************** EXPRESSION ***********/
		function expression($w,$language,$type)
		{

			$lang = $this->languages->code($language);
			if (strlen($lang) == 0)
			{
				echo "OPS NOT LINGUAGE ".$language;
				exit;
			}

			$gen = $this->generes->code($type);
			if (strlen($gen) == 0)
			{
				echo "OPS NOT GENERE ".$gen;
				exit;
			}

			$sql = "select * from find_expression where e_work = '".$w."' and e_language = '$lang' and e_type = '$gen' ";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) == 0)
			{
				$xsql = "insert into find_expression
				(e_work, e_language, e_type)
				values
				('$w','$lang','$gen')";

				$xrlt = $this->db->query($xsql);					
				sleep(1);
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();			
			}

			if (count($rlt) == 0)
			{
				echo "OPS - ERRO DE GRAVAÇÃO";
			}
			$id = $rlt[0]['id_e'];
			return($id);
		}

		/***************************************************************************** EXPRESSION ***********/
		function manifestation($e,$d)	
		{
			$isbn13 = $d['isbn13'];
			$data = $d['data'];

			/********************** WHERE *********/
			$wh = '';
			if ($data > 1900)
			{
				$wh = "and (m_year = '$data')";	
			}

			/********************** CONSULTA ************/
			$sql = "select * from find_manifestation where m_isbn13 = '$isbn13' $wh";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) == 0)
			{
				$xsql = "insert into find_manifestation
				(m_isbn13, m_expression, m_year)
				values
				('$isbn13','$e','$data')";

				$xrlt = $this->db->query($xsql);					
				sleep(1);
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();			
			}

			if (count($rlt) == 0)
			{
				echo "OPS - ERRO DE GRAVAÇÃO";
			}
			$id = $rlt[0]['id_m'];
			return($id);
		}

		function le_m($id)
		{
			$sql = "select * from find_manifestation
			INNER JOIN find_expression ON m_expression = id_e
			INNER JOIN find_work ON e_work = id_w 
			where id_m = $id";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			$line = $rlt[0];
			$line['isbn'] = $this->isbn->isbns($line['m_isbn13']);
			$line['img'] = $this->covers->img($id);
			$line['authors'] = $this->authors->le_authors($line['id_w']);

			$rdf = new rdf;
			$line['rdf'] = $rdf->le_data($line['m_id']);
			$line['links'] = $this->recover_urls($id);
			return($line);
		}

		function show($dt,$type)
		{
			$sx = '';
			switch($type)
			{
				default:				
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
					if ($r > 0) { echo '<br/>'; }
					$sx .= '<a href="'.$line['mu_url'].'" target="_blank">';
					$sx .= $line['mu_url'];
					$sx .= '</a>'.cr();
				}				

				$sx .= '</div>';
				$sx .= '</div>';
				$sx .= '</div>';
				//$sx .= '<style> div { border: 1px solid #000000; } </style>';
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
			INNER JOIN find_expression ON m_expression = id_e
			INNER JOIN find_work ON e_work = id_w";

			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			$sx = '<div class="row">';
			for ($r=0;$r < count($rlt);$r++)
			{
				$line = $rlt[$r];
				$id = $line['id_m'];
				$img = $this->covers->img($id);
				$link = '<a href="'.base_url(PATH.'m/'.$id).'">';
				$linka = '</a>';
				$sx .= '<div class="col-md-2">';
				$sx .= 'Book '.($r+1);
				$sx .= $link;
				$sx .= '<img src="'.$img.'" class="img-fluid">';
				$sx .= $linka;
				$sx .= $line['w_title'];
				$sx .= '</div>';
			}
			$sx .= '</div>';
			return($sx);
		}

		function query_isbn($isbn)
		{
			if (strlen($isbn) > 0)
			{
				$sql = "select * from _queue_isbn where q_isbn13 = '$isbn' ";
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();
				if (count($rlt) == 0)
				{
					$sql = "insert into _queue_isbn
							(q_isbn13)
							values
							('$isbn')";
					$rlt = $this->db->query($sql);							
				}				
			}
			return(1);
		}
	}

/*
TRUNCATE find_authors;
TRUNCATE find_expression;
TRUNCATE find_manifestation;
TRUNCATE find_manifestation_url;
TRUNCATE find_work;
TRUNCATE find_work_authors;
TRUNCATE rdf_concept;
TRUNCATE rdf_data;
TRUNCATE rdf_name;
TRUNCATE _queue_isbn;
*/
?>