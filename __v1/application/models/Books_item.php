<?php
class Books_item extends CI_model
{
	function header($dt)
	{			
		$isbn = $dt['i_identifier'];
		$img = $this->covers->img($isbn);
		$mani = $dt['i_manitestation'];
		$title = trim($dt['i_titulo']);	
		if ($title == '') { $title = msg('no_title'); }
		if ($mani > 0)
		{
			$title = '<a href="'.base_url(PATH.'v/'.$mani).'">'.$title.'</a>';
		}
		$sx = '';
		$sx .= '<div class="container" style="border-bottom: 1px solid #000000;">';
		
		$sx .= '<div class="row">';
		$sx .= '<div class="'.bscol(1).'">';
		$sx .= '<img src="'.$img.'" class="img-fluid img_cover">';
		$sx .= '</div>';
		
		$sx .= '<div class="'.bscol(8).'">';
		$sx .= '<h2>'.$title.'</h2>';
		$sx .= 'ISBN: '.$dt['i_identifier'];
		$sx .= '</div>';
		
		$sx .= '<div class="'.bscol(3).'">';
		$sx .= '<div class="small">'.msg('Status').': ';
		$sx .= '<span class="bold">'.msg('item_status_'.$dt['i_status']).'</span>';
		$sx .= '</div>';
		$sx .= '<div class="small">'.'Tombo: '.strzero($dt['i_tombo'],4).'</div>';
		$sx .= '<div class="small">'.'Ex:'.$dt['i_exemplar'].'</div>';
		$sx .= '</div>';
		
		$sx .= '</div>';
		$sx .= '</div>';
		return($sx);
	}

	function item_status($id,$status)
	{
		$sql = "update find_item set i_status = $status where id_i = ".round($id);
		$rlt = $this->db->query($sql);
		
		/* Inserir Histórico */
		$this->item_historico_insert($id,$status);
		return(1);
	}

	function item_historico($id)
		{
			$dt = array();
			$sql = "		select h_date, h_ip,
							us_nome as h_user,
							is_name as h_status
							from find_item_historic 
							inner join users ON h_user = id_us
							left join find_item_status ON h_status = id_is
							where h_item = $id 
							order by id_h desc						
							";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			$sx = '<table width="100%">';
			$sx .= '<tr style="border-top: 1px solid #808080">';
			$sx .= '<th width="10%">'.msg('h_date').'</th>';
			$sx .= '<th width="10%">'.msg('h_hora').'</th>';
			$sx .= '<th width="15%">'.msg('h_status').'</th>';
			$sx .= '<th width="64%">'.msg('h_user').'</th>';
			$sx .= '<th width="1%">#</th>';
			$sx .= '</tr>';

			for ($r=0;$r < count($rlt);$r++)
				{
					$line = $rlt[$r];
					$sx .= '<tr>';
					$sx .= '<td>'.stodbr($line['h_date']).'</td>';
					$sx .= '<td>'.substr($line['h_date'],11,5).'</td>';
					$sx .= '<td>'.$line['h_status'].'</td>';					
					$sx .= '<td>'.$line['h_user'].'</td>';
					$inf = 'IP Adress:'.$line['h_ip'];
					$sx .= '<td><a href="#" title="'.$inf.'">[i]</a></td>';
					$sx .= '</tr>';
				}
			$sx .= '</table>';				
			return($sx);
		}

	function item_historico_insert($id,$status)
		{
			$ip = ip();
			$user = $_SESSION['id'];
			$sql = "insert into find_item_historic
				(h_item, h_status, h_ip, h_user)
				values
				($id,$status,'$ip',$user)";
			$rlt = $this->db->query($sql);
		}
	
	function tombo_next()
	{
		$sql = "select max(i_tombo) as tombo from find_item 
		where 	i_library = '".LIBRARY."' limit 1";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$tombo = round($rlt[0]['tombo']);
		$tombo++;
		return($tombo);
	}

	function le_tombo($id,$tp='id_i')
	{
		$sql = "select * 
		from find_item 		
		left join library_place ON i_library_place = id_lp
		where $tp = '".$id."' and i_library = '".LIBRARY."'";		

		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) > 0)
		{
			$line = $rlt[0];	
		} else {
			$line = array();
		}
		return($line);
	}	
	
	function mainfestation_item($idm)
	{
		$coll = 5;
		$colr = 7;
		$sql = "select * from find_item
		LEFT JOIN library_place on i_library_place = id_lp
		where i_status <> 9 and i_manitestation = $idm";
		
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		$sp = '<table width="100%">';;

		/************* Container *****************************/
		
		$sx = '';
		$sx .= '<div class="row" style="margin-top: 40px;">';

		/************* Items *********************************/
		$sx .= '<div class="'.bscol($coll).'">';
		$sx .= '<h4>'.msg('books_status').'</h4>';
		$sx .= '</div>';

		$sx .= '<div class="'.bscol($colr).'">';
		$sx .= '<h4>'.msg('books_place').'</h4>';
		$sx .= '</div>';


		$sx .= '<div class="'.bscol($coll).'">';
		$sx .= '<div class="row">';

		/************* Lista de Itens ************************/
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$st = trim($line['i_status']);
			if (substr($line['i_uri'],0,5) == 'FILE:')
				{ $st = '99'; }
			if (substr($line['i_uri'],0,4) == 'URL:')
				{ $st = '98'; }

			$link = '';
			$linka = '';
			$ex = '';
			
			switch($st)
			{
				case '99':
					$sta = '<span class="small">online</span>';
					$ex .= 'Ex: DIGITAL<br/>';
					$img = 'img/icon/icone_pdf.png';
					$link = '<a href="'.base_url('_repositorio/books/'.substr($line['i_uri'],5,200)).'" target="new_'.date("Hmis").'">';
					$linka = '</a>';
					break;
				case '98':
					$sta = '<span class="small">URL</span>';
					$ex .= 'Ex: URL<br/>';
					$img = 'img/icon/icone_link.png';
					break;					
				case '1':
					$sta = '<span class="small">'.msg('item_status_'.$line['i_status']).'</span>';
					$ex .= 'Ex:'.$line['i_tombo'].'<br/>';
					$sta = msg('in_prepare');
					if (perfil("#ADM") > 0)
					{
						$link = '<a href="'.base_url(PATH.'preparation/tombo/'.$line['id_i']).'">';
					} else {
						$link = '<a href="'.base_url(PATH.'item/view/'.$line['id_i']).'">';
					}
					
					$linka = '';
					$img = 'img/books/books_'.$line['i_status'].'.png';	
				break; 
				
				default:
				$sta = '<span class="small">'.msg('item_status_'.$line['i_status']).'</span>';
				$ex .= 'Ex:'.$line['i_tombo'].'<br/>';
				$link = '<a href="'.base_url(PATH.'item/view/'.$line['id_i']).'">';
				$linka = '';
				$img = 'img/books/books_'.$line['i_status'].'.png';	
			break;
			}

			/********************* SP */
			$PLACE = trim($line['lp_name']);
			if (strlen($PLACE) == 0) { $PLACE = msg('place_no_informed'); }
			$sp .= '<tr><td style="border-top: 1px solid #000000;">'.$link.$PLACE.$linka.'</td>';
			$sp .= '<td width="35%" style="border-top: 1px solid #000000;" align="right"><b>'.$link.msg('item_status_'.$line['i_status']).$linka.'</b></td>';
			$sp .= '</tr>';

			/********************* ICONE */
			$sx .= '<div class="'.bscol('2*').' text-center">';
			$sx .= '<span class="small">'.$ex.'</span>';
			$sx .= $link;					

			/* Exemplar n. ********************************/	
			$lb = $line['i_localization'];
			
			$sx .= '<img src="'.base_url($img).'" class="img-fluid">';
			
			$sx .= $lb;
			$sx .= $linka;
			$sx .= $sta;
			$sx .= '</div>';
		}
		$sx .= '</div></div>';
		if (count($rlt) == 0)
		{
			$sx .= '<div class="'.bscol($colr).'">'.message('Nenhum item localizado',2).'</div>';
		}

		/******************* sp */
		$sp .= '</table>';

		$sx .= '<div class="'.bscol($colr).'">';
		$sx .= $sp;
		$sx .= '</div>';
	$sx .= '</div>';
	return($sx);
}	

function check()
	{
		$sql = "select * from find_item 
					where i_titulo = ''
					and i_manitestation > 0";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		for ($r=0;$r < count($rlt);$r++)
			{
				$line = $rlt[$r];
				$im = $line['i_manitestation'];
				$sql = "select * from find_item 
							where i_manitestation = $im
							and i_titulo <> ''";
				$xrlt = $this->db->query($sql);
				$xrlt = $xrlt->result_array();
				if (count($xrlt) > 0)
					{
						$ln = $xrlt[0];
						$tit = $ln['i_titulo'];
						$ln1 = $ln['i_ln1'];
						$ln2 = $ln['i_ln2'];
						$ln3 = $ln['i_ln3'];
						$ln4 = $ln['i_ln4'];
						$idi = $line['id_i'];

						if (strlen($tit) > 0)
						{
						$sqlu = "update find_item set
								i_titulo = '$tit'
								where id_i = $idi";
						$this->db->query($sqlu);
						}
					}
			}
	}

function cutter($id)
	{
		//find_cutter
	}

function tombo_edit($id)
	{
		$cp = array();
		array_push($cp,array('$H8',"id_i","",True,True,True));
		array_push($cp,array('$S100',"i_tombo",msg("i_tombo"),False,False,True));
		array_push($cp,array('$S100',"i_titulo",msg("i_titulo"),True,True,True));		
		array_push($cp,array('$S20',"i_ln1",msg("i_ln1"),False,True,True));
		array_push($cp,array('$S20',"i_ln2",msg("i_ln2"),False,True,True));
		array_push($cp,array('$S20',"i_ln3",msg("i_ln3"),False,True,True));
		array_push($cp,array('$S20',"i_ln4",msg("i_ln4"),False,True,True));
		
		$sql = "select * from find_item_status";
		array_push($cp,array('$Q id_is:is_name:'.$sql,"i_status",msg("i_status"),True,True,True));
		
		$sql = "select * from library_place where lp_LIBRARY = ".LIBRARY;
		array_push($cp,array('$Q id_lp:lp_name:'.$sql,"i_library_place",msg("i_library_place"),True,True,True));
		//array_push($cp,array('$D8',"i_dt_prev",msg("i_dt_prev"),True,True,True));
		//array_push($cp,array('$D8',"i_dt_renovavao",msg("i_dt_renovavao"),True,True,True));
		$form = new form;
		$form->id = $id;
		
		$sx = '';
		$sx .= '<div class="container">';
		$sx .= '<div class="row">';
		$sx .= '<div class="'.bscol(12).'">';
		$sx .= '<h1>'.msg('ITEM_EDIT').'</h1>';
		$sx .= '</div>';
		$sx .= '<div class="'.bscol(12).'">';
		$sx .= $form->editar($cp,'find_item');
		$sx .= '</div>';
		$sx .= '</div>';
		$sx .= '</div>';
		if ($form->saved > 0)
			{
				$this->atualizar_item_excluidos();
				redirect(base_url(PATH.'item/view/'.$id));
			}
		return($sx);
	}

function atualizar_item_excluidos()
{
	$sql = "select * from find_item
	where i_status = 99 and i_library >= 0 and i_library_place >= 0
	";
	$rlt = $this->db->query($sql);
	$rlt = $rlt->result_array();
	for ($r=0;$r < count($rlt);$r++)
	{
		$line = $rlt[$r];
		$id = $line['id_i'];
		$lib = $line['i_library']* (-1);
		$pla = $line['i_library_place']* (-1);
		$status = $line['i_status'];

		$sql = "update find_item
		set i_library = $lib,
		i_library_place = $pla
		where id_i = ".$id;
		$rrr = $this->db->query($sql);
		$this->item_historico_insert($id,$status);
	}
}	

function tombo_view($id)
	{
		$this->load->model("covers");
		$id = round($id);
		$dt = $this->le_tombo($id);
		if (count($dt) == 0)
			{
				echo "ERRO NO ITEM";
				exit;
			}
		$idv = $dt['i_manitestation'];
		$sx = $this->books_item->header($dt);

	    $sx .= '<div class="container" style="margin-top: 10px;">';
		$sx .= '<div class="row">';
		$sx .= '<div class="'.bscol(2).'">';
		$sx .= $this->etiqueta($dt);
		$sx .= '</div>';

		$sx .= '<div class="'.bscol(8).'">';
		$sx .= msg('status').': <b>'.msg('item_status_'.$dt['i_status']).'</b>';

		$sx .= '<br>Previsão de devolução:'.stodbr($dt['i_dt_prev']);
		$sx .= '<br>Data empréstimo:'.stodbr($dt['i_dt_emprestimo']);
		$sx .= '<br>Renovação:'.$dt['i_dt_renovavao'];
		$sx .= '<br>Localização:'.$dt['lp_name'];
		
		$sx .= '</div>';
		$sx .= '</div>';
		$sx .= '</div>';

		if (perfil("#ADM#CAT#BER#BEP") > 0)
		{
	    $sx .= '<div class="container" style="margin-top: 10px;">';
		$sx .= '<div class="row">';
		$sx .= '<div class="'.bscol(2).'">';		
		$sx .= '<a href="'.base_url(PATH.'/item/edit/'.$dt['id_i']).'" class="btn btn-outline-warning">'.msg('edit').'</a>';
		$sx .= '</div>';
		
		$sx .= '<div class="'.bscol(10).'">';		
		$sx .= $this->item_historico($id);
		$sx .= '</div>';

		$sx .= '</div>';
		$sx .= '</div>';

		}
		return($sx);
	}
function etiqueta($dt)
	{
		$sx = '<div style="width: 100%; border: 2px solid #000000; border-radius: 10px; padding: 5px;">';
		$sx .= $dt['i_ln1'].'<br>';
		$sx .= $dt['i_ln2'].'<br>';
		$sx .= $dt['i_ln3'].'<br>';
		$sx .= $dt['i_ln4'].'<br>';
		$sx .= 'Ex: '.$dt['i_exemplar'].'<br>';
		$sx .= '</div>';
		return($sx);
	}

function tombo_insert($tombo, $isbn, $tipo, $status=9, $place, $manifestation=0,$exemplar=1,$title='')
{
	if (is_array($tipo))
	{
		$acq = 4;
		$lib = $tipo['library'];
	} else {
		$acq = $tipo;
		$lib = LIBRARY;
	}
	
	$sql = "select * from find_item 
	where i_tombo = $tombo 
	and i_identifier = '$isbn' 
	and i_library_place = $place
	and i_library = ".$lib;
	
	$rlt = $this->db->query($sql);
	$rlt = $rlt->result_array();
	if (count($rlt) == 0)
	{
		$sql = "insert into find_item
		(
			i_tombo,i_manitestation, i_identifier, 
			i_library, i_ip, i_aquisicao,
			i_status, i_library_place, i_exemplar,
			i_titulo
		)
			values
		(
		'$tombo',$manifestation, '$isbn', 
		'".$lib."','".ip()."',$acq,
		$status,$place,$exemplar,
		'$title'
		)";
		$rlt = $this->db->query($sql);					
		return(array(1,'OK! Item inserido com sucesso!'));
	} else {
		return(array(-1,'ERRO! Número tombo já cadastrado!'));
	}
}	
		/****************** Item Formulário *********************************************/
		function form_item_aquisition()
		{
			$form = new form;
			$cp = array();
			array_push($cp,array('$H8','','',false,false));
			array_push($cp,array('$C','',msg('nr_tombo_automatic'),false,true));
			array_push($cp,array('$T80:5','',msg('isbn_list'),true,true));
			array_push($cp,array('$C','',msg('without_isbn'),false,true));			
			$sql = "select * from library_place where lp_LIBRARY = ".LIBRARY."";
			array_push($cp,array('$Q id_lp:lp_name:'.$sql,'',msg('library_place'),true,true));
			array_push($cp,array('$S40','',msg('nr_tombo_manual'),false,true));
			$sx = '<div class="container"><div class="row">';
			$sx .= '<div class="'.bscol(12).'">';
			$sx .= $form->editar($cp,'');
			$sx .= '<script> $("#dd2").focus(); </script>'.cr();
			return($sx);			
		}
		
		/******************************************* Lista de Itens com status *****/
		
		function tombo_list($status = 0)
		{
			if (is_array($status))
			{
				$wh = '';
				for ($r=0;$r < count($status);$r++)
				{
					if ($r > 0) { $wh .= ' or '; }
					$wh .= '(i_status = '.$status[$r].') ';
				}
				
			} else {
				$wh = ' i_status = '.$status;
			}
			$sql = "select * from find_item 
			INNER JOIN library_place on i_library_place = id_lp
			where ($wh) and i_library = ".LIBRARY."
			order by lp_name, id_i desc";
			
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			
			$sx = cr().cr().'<!--- Lista --->'.cr().cr();
			$sx .= '<table class="table">'.cr();
			$sh = '<tr>';
			$sh .= '<th>Tombo</th>';
			$sh .= '<th>ISBN/ID</th>';
			$sh .= '<th>Dt. Criação</th>';
			$sh .= '<th>Situação</th>';
			$sh .= '<th>Tipo</th>';
			$sh .= '<th>Exemplar</th>';
			$sh .= '</tr>'.cr();
			
			$xplace = '';
			for ($r=0;$r < count($rlt);$r++)
			{
				$line = $rlt[$r];
				$place = $line['lp_name'];
				if ($xplace != $place)
				{
					$sx .= '<tr><td colspan=10 class="big">'.$place.'</td></tr>'.cr();
					$sx .= $sh;
					$xplace = $place;
				}
				$link = '<a href="'.base_url(PATH.'preparation/tombo/'.$line['id_i']).'">';
				$linka = '</a>';
				$sx .= '<tr>';
				$sx .= '<td>'.$link.strzero($line['i_tombo'],7).$linka.'</td>';
				$sx .= '<td>'.$link.$line['i_identifier'].$linka.'</td>';
				$sx .= '<td>'.$link.stodbr($line['i_created']).$linka.'</td>';
				$sx .= '<td>'.$link.msg('item_status_'.$line['i_status']).$linka.'</td>';
				$sx .= '<td>'.$link.msg('item_aquisicao_'.$line['i_aquisicao']).$linka.'</td>';
				$sx .= '<td>'.$link.$line['i_exemplar'].'</td>';
				$sx .= '</tr>'.cr();
			}
			$sx .= '</table>'.cr();
			return($sx);
		}	
		
		function le_item($id)
		{
			$sql = "select * from find_item where id_i = ".$id;
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			$line = $rlt[0];
			return($line);
		}

		function item_edit($id,$status)
			{
				$sx = '';
				$btn_sep = '&nbsp;';
				$btn_to_prepar = '<a href="'.base_url(PATH.'preparation/alter_status/'.$id.'/2/'.checkpost_link($id.'2')).'" class="btn btn-primary">'.msg('send_to_print').'</a>';
				$btn_to_catalog = '<a href="'.base_url(PATH.'preparation/alter_status/'.$id.'/1/'.checkpost_link($id.'1')).'" class="btn btn-outline-warning">'.msg('send_to_catalog').'</a>';
				$btn_marc_import = '<a href="'.base_url(PATH.'preparation/tombo/'.$id.'/marc/'.checkpost_link($id.'2')).'" class="btn btn-outline-primary">'.msg('marc_import').'</a>';
				$btn_to_acervo = '<a href="'.base_url(PATH.'preparation/alter_status/'.$id.'/5/'.checkpost_link($id.'3')).'" class="btn btn-primary">'.msg('send_to_acervo').'</a>';				
				switch($status)
					{
						case '1':
							$sx .= '<div class="border1">';
							$sx .= $btn_to_prepar;
							$sx .= $btn_sep;
							$sx .= $btn_to_acervo;							
							$sx .= $btn_sep;
							$sx .= $btn_marc_import;
							$sx .= '</div>';
							break;
						case '2':
							$sx .= '<div class="border1">';							
							$sx .= $btn_to_acervo;
							$sx .= $btn_sep;
							$sx .= $btn_to_catalog;							
							$sx .= '</div>';
							break;
						}
				return($sx);
			}

		function link_item($dt)
			{
				$file = $dt['i_uri'];
				$sx = '';
				if (substr($file,0,5) == 'FILE:')
					{
						$file = substr($file,5,strlen($file));
						$file = base_url('_repositorio/books/'.$file);
						$sx .= '<a href="'.$file.'" target="_new">';
						$sx .= '<img src="'.base_url('img/icon/icone_pdf.png').'" style="height: 24px;">';
						$sx .= '</a>';
					}
				if (substr($file,0,4) == 'URL:')
					{
						$sx .= '<a href="'.substr($file,4,strlen($file)).'" target="_new">';
						$sx .= '<img src="'.base_url('img/icon/icone_link.png').'" style="height: 24px;">';
						$sx .= '</a>';
					}
				return($sx);
			}

		/**************************************** EDITAR ***************************/
		function editar($dt,$sta='')
		{		
			$id = $dt['id_i'];	
			$isbn = trim($dt['i_identifier']);
			$status = $dt['i_status'];
			$view = 1;
			
			$actions = '';
			/****************************** Modo edição */
			if (strlen($sta) > 0)
			{
				$status = $sta;
			}
			$sx = '';
			$sx .= '<div class="'.bscol(10).'">';
						
			switch($status)
			{
				/************************* COLETA METADADOS ***/
				case '0':
					$dt = $this->le_tombo($id);
					$isbn = $dt['i_identifier'];
					$sx .= $this->books->locate($isbn,$id);
					
					$dt = $this->books_item->le_tombo($id);
					$sx = $this->books_item->header($dt) .$sx;
					$sx .= '<a href="'.base_url(PATH.'preparation/tombo/'.$id).'" class="btn btn-primary">'.msg('continue').'</a>';
					$view = 2;
				break;

				case '2':
					$dt = $this->le_tombo($id);
					$isbn = $dt['i_identifier'];
					
					$dt = $this->books_item->le_tombo($id);
					$sx = $this->books_item->header($dt) .$sx;

					/* Item */
					$sx .= '<span class="big bold">'.msg('ITEM').'</span>';						
					$sx .= $this->item_edit($id,$status);					
					$view = 3;
				break;	
			
				/************************* Catalogação ***/
				case '1':
					$sx .= $this->books_item->header($dt);
					$sx .= $this->catalog($id);
					$view = 3;
				break;				
				
				/************************************* EDITAR MARC ****/
				case 'marc':
					$sx .= $this->books_item->header($dt);
					$dt = $this->le_tombo($id);
					$sr = $this->marc_api->form();	
					
					$marc = get("marc21");
					if (strlen($marc) > 0)
					{
						$dt = $this->marc_api->book($marc);
						$dt['item'] = $id;
						$this->marc_api->save_marc($isbn,$marc);
						$this->books->process_register($isbn,$dt,'MARC2');
						$sr = message($dt['error_msg'],5);
					}
					$sx .= $sr;
					$sx .= '<a href="'.base_url(PATH.'preparation/tombo/'.$id).'" class="btn btn-outline-primary">'.msg('return').'</a>';
				break;
				
				/************************************* EDITAR MARC ****/
				case 'manual':
					$sx .= $this->books_item->header($dt);
					$dt = $this->le_tombo($id);
					$sr = $this->marc_api->manual($id);	
					$sx .= $sr;
				break;		
						
				/************************** Catalogação Manual ******************/
				case 'X5':
					$idm = $dt['i_manitestation'];
					//$sx .= $this->books->catalog_edit($idm);
					
					$sx .= $this->marc_api->form();
					$isbn = $dt['i_identifier'];
					$sx .= $this->sourcers->show($isbn);
					$view = 2;
					$marc = get("marc21");
					if (strlen($marc) > 0)
					{
						$sx .= $this->books->marc_import($marc,$dt['i_identifier']);
						$this->link_book();
						$dt = $this->le_tombo($id);				
						if (strlen($dt['i_manitestation'] > 0))
						{
							$this->item_status($idm,2);					
						}
						redirect(base_url(PATH.'preparation/tombo/'.$id));
					}					
				break;

				default:
					$view = 5;
					$sx = $this->books_item->header($dt) .$sx;
					$sx .= $this->	item_historico($id);
					$sx .= 'FIM';
				break;			
			}
			
			if (strlen($sta) > 0)
			{
				
			} else {
				$sx .= $actions;
			}
			$sx .= '</div>'.cr();
			/**************** WORKFLOW *****************************/
			$sx .= '<div class="'.bscol(2).'">';
			if ($view < 5)
			{
				$dt = array('pos'=>$view,'id'=>$id);
				$sx .= $this->load->view('tools/workflow',$dt,true);			
			} else {
				$dt = $this->le_tombo($id);
				$isbn = $dt['i_identifier'];
					
				$dt = $this->books_item->le_tombo($id);				
				$sx .= 'No acervo';
			}
			$sx .= '</div>';
			
			return($sx);
		}

		function catalog($id)
			{
				$sx = '';
					$dt = $this->le_tombo($id);
					$status = $dt['i_status'];
					$idm = $dt['i_manitestation'];
					if ($idm > 0)
					{
						$rdf = new rdf;
						//$dtw = $book->work_by_manifestion($idm);
						$dt = $rdf->le_data($idm);
						$expression = $rdf->extract_id($dt,'isAppellationOfManifestation',$idm);
						$dt = $rdf->le_data($expression[0]);
						$work = $rdf->extract_id($dt,'isAppellationOfExpression',$expression[0]);

						/* Item */
						$sx .= '<span class="big bold">'.msg('ITEM').'</span>';						
						$sx .= $this->item_edit($id,$status);
						
						$dta = $rdf -> le($work[0]);
						$sx .= '<span class="big bold">'.msg('WORK').'</span>';
						$sx .= $rdf -> form($work[0], $dta);
						
						$sx .= '<span class="big bold">'.msg('MANIFESTATION').'</span>';
						$dta = $rdf -> le($idm);
						$sx .= $rdf -> form($idm, $dta);

						$sx .= '<span class="big bold">'.msg('ITEM').'</span>';
						$sql = "select * 
									from find_item 
									INNER JOIN library_place ON i_library_place = id_lp
									where i_manitestation = $idm 
										and i_library = ".LIBRARY;
						$rlt = $this->db->query($sql);
						$rlt = $rlt->result_array();

						$st = 'style = "border-top: 1px solid #000000; border-bottom: 1px solid #000000;"';
						$sx .= '<table width="100%">';
						$sx .= '<tr class="small text-center">';
						$sx .= '<th '.$st.' width="25%"></th>';
						$sx .= '<th '.$st.' width="5%" class="text-center">#</th>';
						$sx .= '<th '.$st.' width="10%">'.msg('tombo').'</th>';
						$sx .= '<th '.$st.' width="25%">'.msg('place').'</th>';
						$sx .= '<th '.$st.' width="25%">'.msg('status').'</th>';
						$sx .= '<th '.$st.' width="5%" class="text-center">'.msg('file').'</th>';
						$sx .= '<th '.$st.' width="5%" class="text-center">'.msg('up').'</th>';
						$sx .= '</tr>';
						
						for ($r=0;$r < count($rlt);$r++)
							{
								$li = $rlt[$r];
								$link = '<a onclick="newxy(\''.base_url(PATH).'upload/item/'.$li['id_i'].'\',800,300);" style="cursor: pointer; color: white;" class="btn-primary br5">';
								$linka = '</a>';
								$up = $link.'&nbsp;+&nbsp;'.$linka;

								$lk = $this->link_item($li);

								$sx .= '<tr>';
								$sx .= '<td width="25%"></td>';
								$sx .= '<td '.$st.' class="text-center">'.($r+1).'</td>';
								$sx .= '<td '.$st.' class="text-center">'.$li['i_tombo'].'</td>';
								$sx .= '<td '.$st.'>'.$li['lp_name'].'</td>';
								$sx .= '<td '.$st.'>'.msg('item_status_'.$li['i_status']).'</td>';
								$sx .= '<td '.$st.' class="small text-center">'.$lk.'</td>';
								$sx .= '<td '.$st.' class="small text-center">'.$link.$up.$linka.'</td>';
								$sx .= '</tr>';
							}
						$sx .= '</table>';
					}
					return($sx);				
			}

		function upload_item($id,$tp='')
			{
				$sx = '';
				switch ($tp)
					{
						case 'FILE':
						$sx .= $this->upload_pdf($id);
						break;

						case 'URL':
						$sx .= $this->upload_url($id);
						break;

						case 'EDIT':
						$sx .= $this->upload_url($id);
						break;						

						default:
						$sx .= '<style> div { border: 1px solid #000; } </style>';
						$sx .= '<div class="'.bscol(12).'">';
						$sx .= '<span class="small">'.msg('upload_item_info').'</span></div>';
						$sx .= '<div class="'.bscol(12).'">';
						$sx .= '<a href="'.base_url(PATH.'upload/item/'.$id.'/FILE').'" class="btn btn-outline-primary">'.msg('item_FILE').'</a>';
						$sx .= '&nbsp;';
						$sx .= '<a href="'.base_url(PATH.'upload/item/'.$id.'/URL').'" class="btn btn-outline-primary">'.msg('item_URL').'</a>';
						$sx .= '&nbsp;';
						$sx .= '<a href="'.base_url(PATH.'upload/item/'.$id.'/EDIT').'" class="btn btn-outline-primary">'.msg('item_EDIT').'</a>';
						$sx .= '</div>';
					}
				$data['content'] = $sx;
				$data['title'] = msg('upload_item');
				$this->load->view('show',$data);

			}

		function upload_url($id)
			{
				$form = new form;
				$cp = array();
				array_push($cp,array('$H8','id_i','',false,false));
				array_push($cp,array('$S100','i_uri',msg('url'),false,true));
				$form->id = $id;
				$sx = $form->editar($cp,'find_item');			

				if ($form->saved > 0)
					{
						$sx = cr().'<script> wclose(); </script>';
					}
				return($sx);
			}

		function upload_pdf($id)
			{
				$form = new form;
				$cp = array();
				array_push($cp,array('$H8','','',false,false));
				array_push($cp,array('$FILE','','',true,true));
				$sx = $form->editar($cp,'');

				if (isset($_FILES['fileToUpload']))
					{
						check_dir('_repositorio');
						check_dir('_repositorio/books');
						$dir = '_repositorio/books/';
						$tmp = $_FILES['fileToUpload']['tmp_name'];
						$file = LIBRARY.'_'.strzero($id,10).'.pdf';
						move_uploaded_file($tmp,$dir.$file);
						$sql = "update find_item set i_uri = 'FILE:$file' where id_i = ".round($id);
						$rlt = $this->db->query($sql);
						$sx = cr().'<script> wclose(); </script>';
					}
				return($sx);
			}	
		function search($g)
			{
				/* Termos */
				$t = $g['dd1'];
				$t = explode(' ',$t);
				$wh2 = "(n_name like '%".$t[0]."%')";

				/************* FASE I da busca */
				$wh = '';
				$sql = "select DISTINCT i_manitestation from find_item 
						where i_library = '".LIBRARY."'
						";
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();

				for ($r=0;$r < count($rlt);$r++)
					{
						$l = $rlt[$r];
						if (strlen($wh) > 0) { $wh .= ' OR '; }
						$wh .= '(d_r1 = '.$l['i_manitestation'].')';
						$wh .= ' OR ';
						$wh .= '(d_r2 = '.$l['i_manitestation'].')';
					}
				/************** Fase II da busca */
				$sql = "select DISTINCT d_r1, d_r2 
							from rdf_data
							where $wh";
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();
				for ($r=0;$r < count($rlt);$r++)
					{
						$l = $rlt[$r];
						if (strlen($wh) > 0) { $wh .= ' OR '; }
						$wh .= '(d_r1 = '.$l['d_r1'].')';
						$wh .= ' OR ';
						$wh .= '(d_r2 = '.$l['d_r1'].')';
						$wh .= ' OR ';
						$wh .= '(d_r1 = '.$l['d_r2'].')';
						$wh .= ' OR ';
						$wh .= '(d_r2 = '.$l['d_r2'].')';
					}				
				
				$sql = "select n_name, d_r1, d_r2, 
							class1.c_class as c1, class2.c_class as c2
							from rdf_data
							inner join rdf_name ON d_literal = id_n 
							/************************************/
							left join rdf_concept as c1 ON d_r1 = c1.id_cc
							left join rdf_class as class1 ON c1.cc_class = class1.id_c

							/************************************/
							left join rdf_concept as c2 ON d_r2 = c2.id_cc
							left join rdf_class as class2 ON c2.cc_class = class2.id_c

							where ($wh) and ($wh2)
							order by class1.c_class desc, n_name
							";
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();


				/* Show Lista */
				$sx = '<div class="container">';
				$sx .= '<div class="row">';	
				$sx .= '<div class="'.bscol(2).' text-right">';
				$sx .= '<b>'.msg('Type').'</b>';
				$sx .= '</div>';			
				$sx .= '<div class="'.bscol(10).'">';
				$sx .= '<b>'.msg('description').'</b>';
				$sx .= '</div>';			

				for ($r=0;$r < count($rlt);$r++)
					{
						$l = $rlt[$r];
						$idv = $l['d_r1'];
						$link = '<a href="'.base_url(PATH.'v/'.$idv).'">';
						$linka = '</a>';

						$sx .= '<div class="'.bscol(2).' text-right">';
						$sx .= msg($l['c1']);
						$sx .= '</div>';

						$sx .= '<div class="'.bscol(10).'" style="border-bottom: 1px solid #000000; margin-bottom: 5px;">';
						$sx .= $link.$l['n_name'].$linka;						
						$sx .= '</div>';
					}
				$sx .= '</div>';
				$sx .= '</div>';
				$sx .= '</div>';
				return($sx);
			}
		
	}
	?>