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

function rdf_show_Subject($line = array())
    {
        $sx = '<div class="col-md-12">';
		$sx .= '<h2>'.$line['n_name'].'</h2>';
        $sx .= '</div>';

        $sx .= '<div class="col-md-12">';
        $sx .= show_works($line['id_cc']);
        $sx .= '</div>';
        return($sx);
    }

function rdf_show_Person($line = array())
	{
        $sx = '<div class="col-md-12">';
		$sx .= '<h2>'.$line['n_name'].'</h2>';
        $sx .= '</div>';
        
        $id = $line['id_cc'];

        $rdf = new rdf;
        $dt = $rdf->le_data($id);


        $books = '';

        for ($r=0;$r < count($dt);$r++)
            {
                $l = $dt[$r];
                switch($l['c_class'])
                    {
                        case 'hasAuthor':
                            $link = '<a href="'.base_url(PATH.'v/'.$l['d_r1']).'">';
                            $linka = '</a>';
                            $books .= '<div class="col-md-2"></div>';
                            $books .= '<div class="col-md-10">'.$link.$l['n_name'].$linka.'</div>';
                        break;
                    }
            }
        $sx .= '<div class="col-md-12"><h3>'.msg('books_author_titles').'</h3></div>'.$books;
        //id_cc cc_use
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
    $class = '';
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

        //echo '==>'.$prop.'<br>';
		switch($prop)
		{
			
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
				$date = msg('Year').': '.$vlr;
			break;	

            case 'hasColorclassification':
                if (strpos($vlr,'#'))
                    {
                        $link = '<a href="'.base_url(PATH.'v/'.$idx).'" style="color: white; padding: 2px 5px;">';
                        $cor = substr($vlr,strpos($vlr,'#'),strlen($vlr));
                        $vlr = substr($vlr,0,strpos($vlr,'#'));
                        $vlrx = '<div style="width: 400px; color: white; background-color: '.$cor.'; border: 1px #000 solid;">';
                        $vlrx .= $link.$vlr.$linka;
                        $vlrx .= '</div>';
                    }
                $class .= $vlrx;
            break;
			
		}
	}

    /* Classification */
    if (strlen($class) > 0)
    {
        $sxx = '<table style="margin-top: 20px;">';
        $sxx .= '<tr>';
        $sxx .= '<td>'.msg('Classification').'</td>';
        $sxx .= '<td>'.$class.'</td>';
        $sxx .= '</tr>';
        $sxx .= '</table>';

        $class = $sxx;
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
		$sx .= '<div class="manifestation_subject" style="margin-top: 20px;">Assuntos: '.$subsj.'</div>';
	}

    $sx .= '<div class="manifestation_classification">'.$class.'</div>'.cr();

	$sx .= '<div class="manifestation_descrition">'.msg('description').': '.$desc.'</div>'.cr();
	
	$sx .= '</div>';
	return($sx);
}    