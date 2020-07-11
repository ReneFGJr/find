<?php
class book_preparations extends CI_model
{
	function main($path,$id,$sta)
	{
		$this->load->model('isbn');
		$this->load->model('books');
		$this->load->model('books_item');
		$this->load->model("covers");
		$this->load->model("languages");		
		$this->load->model("generes");		
		$this->load->model("authors");
		$this->load->model("google_api");		
		$this->load->model("amazon_api");
		$this->load->model("mercadoeditorial_api");
		$this->load->model("find_rdf");
		$this->load->model("oclc_api");		
		$this->load->model("marc_api");
		$this->load->model("catalog");
		$this->load->model("sourcers");
		$this->load->model("labels");
		
		$sx = '<div class="container"><div class="row">';
		switch($path)
		{
			case 'acquisition':
				$sx .= $this->acquisition();
			break;
			
			case 'tombo_status':
				$this->item_status($id,$sta);
				redirect(base_url(PATH.'preparation/tombo/'.$id));			
			break;
			
			case 'tombo':
				$dt = $this->books_item->le_tombo($id);
				$sx .= $this->books_item->editar($dt,$sta);			
			break;				
			
			case 'acquisition_in':
				$sx = $this->acquisition_in();
			break;
			
			case 'acquisition_marc':
				$sx = $this->acquisition_marc();
			break;
			
			case 'itens':
				if ($id == 0) { $id = array(0,5); }
				$sx = $this->preparation_itens($id);
			break;
			
			
			/********************************* MENU **/
			default:
			/******************************************/
			$sx .= $this->preparation_menu('Aquisição','','preparation/acquisition',0);
			
			/******************* Items para Catalogação ****/
			$sta = array(0);
			$itens = $this->in_status($sta);			
			if ($itens > 0)
			{
				$txt = '<div style="margin-top: 40px;">total de</div>
				<span style="font-size: 500%; font-weight: bold;">'.$itens.'</span>';
				$sx .= $this->preparation_menu('Catalogação '.$itens.' item(ns)',$txt,'preparation/itens/0',1);
			}
			$sta = 5;
			$itens = $this->in_status($sta);			
			if ($itens > 0)
			{
				$txt = '<div style="margin-top: 40px;">total de</div>
				<span style="font-size: 500%; font-weight: bold;">'.$itens.'</span>';
				$sx .= $this->preparation_menu('Catalogação Manual'.$itens.' item(ns)',$txt,'preparation/itens/5',1);
			}
			
			/******************* Items para Catalogação ****/
			$sta = 1;
			$itens = $this->in_status($sta);
			if ($itens > 0)
			{
				$txt = '<div style="margin-top: 40px;">total de</div>
				<span style="font-size: 500%; font-weight: bold;">'.$itens.'</span>';
				$sx .= $this->preparation_menu('Classificação '.$itens.' item(ns)',$txt,'preparation/itens/1',1);
			}	
			
			/******************* Items para Classificação ****/
			$sta = 2;
			$itens = $this->in_status($sta);
			if ($itens > 0)
			{
				$txt = '<div style="margin-top: 40px;">total de</div>
				<span style="font-size: 500%; font-weight: bold;">'.$itens.'</span>';
				$sx .= $this->preparation_menu('Classificação '.$itens.' item(ns)',$txt,'preparation/itens/2',1);
			}
			
			/******************* Items para Classificação ****/
			$sta = 4;
			$itens = $this->in_status($sta);
			if ($itens > 0)
			{
				$txt = '<div style="margin-top: 40px;">total de</div>
				<span style="font-size: 500%; font-weight: bold;">'.$itens.'</span>';
				$sx .= $this->preparation_menu('Preparação Fisica '.$itens.' item(ns)',$txt,'preparation/itens/4',1);
			}
			
			/******************* Items para Classificação ****/
			$txt = '<div style="margin-top: 40px;">total de</div>
			<span style="font-size: 500%; font-weight: bold;">'.$itens.'</span>';
			$sx .= $this->preparation_menu('Etiquetas',$txt,'label',1);
			
			//$sx .= '</div>';
			
		break;			
	}
	$sx .= '</div></div>';
	return($sx);
}

function preparation_menu($name,$txt,$link,$tp=0)
{
	$ig = array('icone_processament_tecnico_256.jpg','');
	$sx = '';
	$sx .= '<div class="col-md-4 col-6 col-sm-5 col-lg-3 col-xl-3 text-center">';
	$sx .= '<a href="'.base_url(PATH.$link).'" style="text-decoration: none;">';
	if (strlen($ig[$tp]) > 0)
	{
		$sx .= '<img src="'.base_url('img/icon/'.$ig[$tp]).'" class="img_menu img-fluid" alt="'.$name.'"  title="'.$name.'">';
	} else {
		$sx .= '<div class="img_menu" style="width: 256px; height: 256px;">';
		$sx .= $txt;
		$sx .= '</div>';			
	}
	$sx .= $name.'';
	$sx .= '</a>';
	$sx .= '</div>';
	return($sx);	
}

function preparation_itens($sta)
{
	$itens = $this->in_status($sta);
	$sx = '<div class="container"><div class="row">';
	$sx.= '<div class="col-12">';	
	$sx .= '<h1>'.$itens.' '.msg('preparation_itens');
	$sx .= ' ' ;
	$sx .= '<a class="btn btn-outline-primary" href="'.base_url(PATH.'/preparation/itens/0/auto').'">'.msg('process_automatic').'</a>';
	$sx .= '</h1>';
	$sx .= '<p>'.msg('preparation_itens_action').'</p>';
	$sx .= $this->books_item->tombo_list($sta);
	$sx .= '</div>';
	$sx .= '</div></div>';
	
	return($sx);
}

function in_status($st)
{
	if (is_array($st))
	{
		$wh = '';
		for($r=0;$r < count($st);$r++)
		{
			if ($r > 0) { $wh .= ' OR '; }
			$wh .= "(i_status = ".round($st[$r]).")";
		}
	} else {
		$wh = "(i_status = $st) ";
	}
	
	$sql = "select count(*) as total 
	from find_item 
	where ($wh) and i_library = '".LIBRARY."' ";		
	$rlt = $this->db->query($sql);
	$rlt = $rlt->result_array();
	$line = $rlt[0];
	return($line['total']);
}

function link_book($i1,$i2)
{
	//echo '===>'.$i1.'=='.$i2;
}





function actions($op,$id)
{
	if (is_array($op))
	{
		
	} else {
		$op = array($op);
	}
	$sx = '';
	for ($r=0;$r < count($op);$r++)
	{
		$sx .= '<a href="'.base_url(PATH.'preparation/tombo_status/'.$id.'/'.$op[$r]).'" class="btn btn-outline-primary">';
		$sx .= 'Enviar para '.msg('item_status_'.$op[$r]).'</a>';
	}
	return($sx);
}

function acquisition_marc()
{
	$this->load->model("iso2709");
	$msgs = '';
	$form = new form;
	
	$cp = array();
	array_push($cp,array('$H8','','',false,false));
	$m = msg('iso2709_info');
	array_push($cp,array('$M','',$m,false,false));
	array_push($cp,array('$FILE','','',false,false));
	
	$tela = '<div class="container"><div class="row"><div class="col-md-12">';
	$tela .= $form->editar($cp,'');
	$tela .= '</div></div></div>';
	
	if ((isset($_FILES['fileToUpload'])) and (strlen($_FILES['fileToUpload']['tmp_name']) > 0))
	{
		if (isset($_FILES['fileToUpload']['tmp_name']))
		{
			$file = $_FILES['fileToUpload']['tmp_name'];
			$tela .= $this->iso2709->import($file);
		}
	}		
	return($tela);
	
}

function acquisition_in($loop=1)
{	
	$msgs = '';
	$sx = '';
	$form = new form;
	
	/**************** RECUPERA NUMERO TOMBO ****/
	$tombo = get("dd1");
	$place = get("dd4");
	if (strlen($tombo) == 0)
	{
		$_GET['dd1'] = $this->books_item->tombo_next();
		if (isset($_SESSION['place']))
		{
			$_GET['dd4'] = $_SESSION['place'];
		}
	}
	
	/**************** PLACE SETADO *************/
	if (strlen($place) > 0)
	{
		$_SESSION['place'] = $place;
	}
	
	/******************* ISBN *****************/
	$isbns = get("dd2");	
	
	/************* SEM ISBN, GERA UM ISBN ****/
	if (get("dd3") == 1)
	{
		$tb = get("dd1");
		$isbns = LIBRARY.strzero($tb,8);
		$isbns	 .= genchksum13($isbn);
		$status = 5;
	}
	
	/***************************** Importa ISBN */
	if (strlen($isbns) > 0)	
	{
		$isbns = troca($isbns,chr(13),'');
		$isbns = troca($isbns,chr(10),';');
		$isbns = explode(';',$isbns);
		$xtombo = $tombo;
		$loop = 0;
		/* Só recarrega a página se tiver uma inserção */
		if (count($isbns) == 1) { $loop = 1; }
		
		for ($ri=0;$ri < count($isbns);$ri++)
		{
			$tombo = ($xtombo + $ri);
			$isbn = $isbns[$ri];		
			$isbn = troca($isbn,'-','');
			$isbn = troca($isbn,'.','');
			$isbn = trim($isbn);
			
			/************************************ FORMULARIO VALIDADO */
			$sx .= '<li class="big">'.msg('Add').' ISBN:'.$isbn;
			$sx .= '<ul>';
			if (((strlen($isbn) == 13) or (strlen($isbn) == 10)) and ($place > 0))
			{
				$isbn = $this->isbn->isbns($isbn);
				$isbn_o = $isbn['isbn13'];
				$isbn = sonumero(substr($isbn_o,0,12));
				$isbn_dv = genchksum13($isbn);	
				$isbn_or = substr($isbn_o,12,1);
				$manifestation = 0;
				
				if ($isbn_dv == $isbn_or)
				{
					/* Verifica Exemplares */
					$exes = $this->books->exemplar($isbn_o);
					$ex = $exes[0];
					$mn = $exes[1];				
					if ($mn > 0)
					{
						$status = 5;
					} else {
						$mn = 0;
						$status = 0;
					}
					$exemplar = $ex + 1;
					
					$tipo = 1;
					$rs = $this->books_item->tombo_insert($tombo,$isbn_o,$tipo,$status,$place,$mn,$exemplar);
					if ($rs[0] == 1)
					{
						if ($loop == 1)
						{
							redirect(base_url(PATH.'preparation/acquisition_in'));
						} else {
							$msgs .= message(msg('Item inserted').' '.$isbn,1);
						}						
					} else {
						$sx .= message(msg('validating_dv').' ISBN - '.$tombo.' - '.$rs[1],3);
					}
				} else {
					$sx .= message(msg('validating_dv').' ISBN - Invalid',3);					
				}
			} else {
				if (strlen($isbn) > 0)
				{
					$sx .= message(msg('validating_dv').' ISBN - Invalid',3);
				}
			}
			$sx .= '</ul>';
			$sx .= '</li>';
		}
		
	}
	
	if ($loop == 1)
	{
		$sx = $this->books_item->form_item_aquisition();
		$sx .= $this->books_item->tombo_list(array(0));
	} else {
		//$sx = '';
	}
	$sx = '<div class="container"><div class="row"><div class="col-md-12">'.$sx.'</div></div></div>';
	return($sx);
}

function book_header($dt)
{
	if (count($dt) == 0)
	{
		echo "OPS - Item não pertence a essa biblioteca";
		refresh(10,base_url(PATH));
		exit;
	}
	
	$img = $this->covers->img($dt['m_isbn13']);
	$sx = '';
	
	$sx .= '<div class="col-1">';
	$sx .= '<a href="'.base_url(PATH.'m/'.$dt['id_m']).'">';
	$sx .= '<img src="'.$img.'" class="img-fluid">';
	$sx .= '</a>';
	$sx .= '</div>';
	
	$sx .= '<div class="col-3">';
	$sx .= '<span>ISBN:'.$dt['i_identifier'].'</span></br>';
	$sx .= '<span>'.msg('item_status_'.$dt['i_status']).'</span>';
	$sx .= '</div>';			
	
	$sx .= '<div class="col-7">';
	$sx .= '<span class="find_title">'.$dt['w_title'].'</span>';
	$sx .= '</div>';
	
	$sx .= '<div class="col-1 text-right">';
	$sx .= '<span class="small">TOMBO</span><br/><span class="big">'.$dt['i_tombo'].'</span>';
	$sx .= '</div>';
	
	$sx .= '</div>';
	return($sx);
}

function show($dt,$tp=1)
{
	switch($tp)
	{
		case 1:
			$sx = '<div class="row">';
			$sx .= '<div class="col-10">';
			$sx .= '<h2>'.$dt['i_identifier'].'</h2>';
			$sx .= '<div>';
			$sx .= msg('item_status_'.$dt['i_status']);
			$sx .= '</div>';
			$sx .= '</div>';
			$sx .= '<div class="col-2 text-right">';
			$sx .= '<span class="alert alert-info"><sup>TOMBO</sup> '.$dt['i_tombo'].'</span>';
			$sx .= '</div>';
			$sx .= '</div>';
		break;
		
		case 2:
			$sx = '<div class="row">';
			$sx .= '<div class="col-10">';
			$sx .= '<h2>'.$dt['i_identifier'].'</h2>';
			$sx .= '<div>';
			$sx .= msg('item_status_'.$dt['i_status']);
			$sx .= '</div>';
			$sx .= '</div>';
			$sx .= '<div class="col-2 text-right">';
			$sx .= '<span class="alert alert-info"><sup>TOMBO</sup> '.$dt['i_tombo'].'</span>';
			$sx .= '</div>';
			$sx .= '</div>';			
		break;
	}
	return($sx);			
}



function acquisition()
{
	$sx = 'Forma de Aquisição
	<ul>
	<li>Compra</li>
	<li>Doação</li>
	<li>Permuta</li>
	<li><a href="'.base_url(PATH.'preparation/acquisition_marc/').'">Incorporação no Acervo MARC</a></li>
	<li><a href="'.base_url(PATH.'preparation/acquisition_in/').'">Incorporação no Acervo</a></li>
	</ul>';
	return($sx);
} 
}
?>