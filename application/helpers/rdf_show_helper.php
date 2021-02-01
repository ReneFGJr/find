<?php
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

function show_works($id)
    {
    	$CI = &get_instance();

        $sx = '';
        $rdf = new rdf;
        $dt = $rdf->le_data($id);
        $w = array();

        for ($r=0;$r < count($dt); $r++)
            {
                $l = $dt[$r];
                switch($l['c_class'])
                    {
                        case 'hasSubject':
                        array_push($w,$l['d_r1']);
                        $sx .= $l['d_r1'];
                        $sx .= ' ';
                    }
                
            }
        return($sx);
    }



function rdf_show_data_generic($line = array())
	{
		$CI = &get_instance();
		$id = $line['id_cc'];

		$class = trim($line['c_class']);
		$sx = '<div class="'.bscol(12).'"><sup>'.$class.':</sup> '.$line['n_name'].'</h1></div>';

		/* Manifestation */
		$sql = "SELECT 
				distinct d_r1 as dr1, d_r2 as dr2,
				i_manitestation, i_titulo, i_identifier 
				FROM rdf_data
				LEFT JOIN find_item ON i_manitestation = d_r1
				where (d_r1 = $id or d_r2 = $id)
					and i_manitestation > 0
					and i_library = '".LIBRARY."'
				order by i_titulo";							

		/********** Pessoa */
		if ($class == 'Person')
		{
		/* Author */
		$sql = "SELECT 
				distinct
				d1.d_r1 as dr1 , d1.d_r2  as dl1, 
				d2.d_r1 as dr2 , d2.d_r2  as dl2, 
				d3.d_r1 as dr3 , d3.d_r2  as dl3, 
				c_class, i_manitestation, i_titulo, i_identifier 
				FROM rdf_data as d1
				INNER JOIN rdf_data as d2 ON d1.d_r1 = d2.d_r1
				INNER JOIN rdf_data as d3 ON d2.d_r2 = d3.d_r1

				INNER JOIN find_item ON i_manitestation = d3.d_r2

				LEFT JOIN rdf_class ON d3.d_p = id_c 

				where (d1.d_r1 = $id or d1.d_r2 = $id)
					and (d1.d_r1 <> 0 or d1.d_r2 <> 0)
					and i_manitestation > 0
					and i_library = '".LIBRARY."'
				order by i_titulo";
		}

		$rlt = $CI->db->query($sql);
		$rlt = $rlt->result_array();
		for ($r=0;$r < count($rlt);$r++)
			{
					$l = $rlt[$r];
					$isbn = $l['i_identifier'];
					$img = $CI->covers->img($isbn);
					$link = '<a href="'.base_url(PATH.'/v/'.$l['dr1']).'">';
					$linka = '</a>';
					$sx .= '<div class="'.bscol(2).' text-center">';
					$sx .= $link;
					$sx .= '<img src="'.$img.'" class="img-fluid">';
					$sx .= $linka;
					$sx .= $l['i_titulo'];
					$sx .= '</div>';
			}
		return($sx);
	}

function rdf_show_Person($line = array())
	{
        $sx = '<div class="col-md-12">';
		$sx .= '<h2>'.$line['n_name'].'</h2>';
        $sx .= '</div>';

		$sx .= rdf_show_data_generic($line);
		return($sx);
	}

function show_books($l)
	{
		$books = '';
		switch($l['c_class'])
			{
				case 'hasAuthor':
					$link = '<a href="'.base_url(PATH.'v/'.$l['d_r1']).'">';
					$linka = '</a>';
					$books .= '<div class="col-md-2"></div>';
					$books .= '<div class="col-md-10">'.$link.$l['n_name'].$linka.'</div>';
				break;
			}
		return($books);

		$line = $rlt[$r];
		$id = $line['i_manitestation'];
		$isbn = $line['i_identifier'];
		$img = $this->covers->img($isbn);
		$link = '<a href="'.base_url(PATH.'v/'.$id).'">';
		$linka = '</a>';
		$sx .= '<div class="col-3 col-lg-2 col-md-2 books text-center">';
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
    $class = '';
	$serie = '';
	$vol = '';
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
			case 'hasVolumeNumber':
				if (strlen($vol) > 0) { $vol .= '; '; }
				$vol .= msg('Volume').' '.$vlr;
			break;

			case 'hasSerieName':
				if (strlen($serie) > 0) { $serie .= '; '; }
				$link = '<a href="'.base_url(PATH.'v/'.$idx).'">';
				$linka = '</a>';
				$serie .= $link.$vlr.$linka;
			break;

			case 'hasSubject':
            if (strlen($vlr) > 0)
				$subsj .= $link. '<span class="btn-outline-primary pad5">'.trim($vlr).'.'.'</span>'.$linka.'';
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
				$date = msg('Year').': '.$link.$vlr.$linka;
			break;	

            case 'hasColorclassification':
                if (strpos($vlr,'#'))
                    {
						$corl = '#000000';                     
						/* Cores */   
                        $cor = substr($vlr,strpos($vlr,'#')+1,strlen($vlr));
						if (strpos($cor,'#') > 0)
							{
								$corl = substr($cor,strpos($cor,'#'),7);
								$cor = substr($cor,0,6);
							}
						$cor = '#'.$cor;
						$vlr = substr($vlr,0,strpos($vlr,'#'));
						$link = '<a href="'.base_url(PATH.'v/'.$idx).'" style="color: '.$corl.'; padding: 2px 5px;">';
                        $vlrx = '<div style="width: 100%; padding: 3px; background-color: '.$cor.';">';
                        $vlrx .= $link.$vlr.$linka;
                        $vlrx .= '</div>';
						
						
                    } else {
						$vlrx = '<b>'.$vlr.'</b>';
					}									
                $class .= $vlrx;
            break;

            case 'hasClassificationCDU':
                $link = '<a href="'.base_url(PATH.'v/'.$idx).'">';
				$class .= $link.$vlr.$linka;
            break;			

            case 'hasClassificationCDD':
                $link = '<a href="'.base_url(PATH.'v/'.$idx).'">';
				$class .= $link.$vlr.$linka;
            break;			
			
		}
	}

    /* Classification */
    if (strlen($class) > 0)
    {
        $sxx = '<table style="margin-top: 20px; width: 100%;">';
        $sxx .= '<tr>';
        $sxx .= '<td width="10%">'.msg('Classification').':</td>';
        $sxx .= '<td width="90%">'.$class.'</td>';
        $sxx .= '</tr>';
        $sxx .= '</table>';

        $class = $sxx;
    }

	/****************** Imagem */
	$sx .= '<div class="'.bscol(2).'">';
	$sx .= '<img src="'.$img.'" class="img-fluid img_cover">';
	if (perfil("#ADM"))
	{
		$sx .= $CI->covers->btn_seek_cover($isbn);
		$sx .= ' | ';
		$sx .= $CI->covers->btn_upload($isbn);
	}
	$sx .= '</div>';

	/****************** Descrição */
	$sx .= '<div class="'.bscol(10).'">';
	/* Title */
	$sx .= '<div class="work_title">'.$d['title'].'</div>';
	$sx .= '<div class="work_author">'.$d['authors'].'</div>';


	/****************** Volume e Serie */
	if (strlen($serie.$vol) > 0)
		{
			$sx .= '<div class="manifestation_serie">';
			/* Serie */
			if (strlen($serie) > 0) 
			{
				$sx .= msg('Serie').': ';
				$sx .= '<b>';
				$sx .= trim($serie);
				$sx .= '</b>';
			}
			/* Volume */
			if ((strlen($serie) > 0) and (strlen($vol) > 0))
				{
					$sx .= '. ';
				}
			$sx .= trim($vol);
			
			$sx .= '</div>';
		}
	

	$isbn = $CI->isbn->isbns(sonumero($isbn));
	$sx .= '<div class="manifestation_date">'.$date.'</div>';
	$sx .= '<div class="manifestation_editora">'.$editora.'</div>';
	$sx .= '<hr>';
	$sx .= '<div class="manifestation_isbn">ISBN10: '.$isbn['isbn10f'].'</div>';
	$sx .= '<div class="manifestation_isbn">ISBN13: '.$isbn['isbn13f'].'</div>';
	$sx .= '<div class="manifestation_pags">'.$pags.'</div>';
	if (strlen($subsj) > 0)
	{
		$sx .= '<div class="manifestation_subject" style="margin-top: 20px;">Assuntos: '.$subsj.'</div>';
	}

	$sx .= '<hr>';
    $sx .= '<div class="manifestation_classification">'.$class.'</div>'.cr();

	if (strlen($desc) > 0)
	{
		$sx .= '<div class="manifestation_descrition">'.msg('description').': '.$desc.'</div>'.cr();
	}
	
	$sx .= '</div>';
	return($sx);
}    