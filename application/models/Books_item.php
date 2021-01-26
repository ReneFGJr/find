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
		$sx .= '<div class="col-1">';
		$sx .= '<img src="'.$img.'" class="img-fluid">';
		$sx .= '</div>';
		
		$sx .= '<div class="col-8">';
		$sx .= '<h2>'.$title.'</h2>';
		$sx .= 'ISBN: '.$dt['i_identifier'];
		$sx .= '</div>';
		
		$sx .= '<div class="col-2 small">';
		$sx .= '<div>'.msg('Status').': ';
		$sx .= '<span class="bold">'.msg('item_status_'.$dt['i_status']).'</span>';
		$sx .= '</div>';
		$sx .= 'Tombo: '.strzero($dt['i_tombo'],4);
		$sx .= '<br/>';
		$sx .= 'Ex:'.$dt['i_exemplar'];
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
		return(1);
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
	
	function le_tombo($id)
	{
		$sql = "select * from find_item 		
		where id_i = ".$id;		
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
		$sql = "select * from find_item
		where i_manitestation = $idm";
		
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		/************* Container *****************************/
		
		$sx = '<div class="container">';
		$sx .= '<div class="row">';

		/************* Items *********************************/
		$sx .= '<div class="col-md-12">';
		$sx .= '<h4>'.msg('books_status').'</h4>';
		$sx .= '</div>';

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
					$ex .= 'Ex: DIGITAL<br/>';
					$img = 'img/icon/icone_pdf.png';
					$link = '<a href="'.base_url('_repositorio/books/'.substr($line['i_uri'],5,200)).'" target="new_'.date("Hmis").'">';
					$linka = '</a>';
					break;
				case '98':
					$ex .= 'Ex: URL<br/>';
					$img = 'img/icon/icone_link.png';
					break;					
				case '1':
					$ex .= 'Ex:'.$line['i_tombo'].'<br/>';
					$sta = msg('in_prepare');
					$link = '<a href="'.base_url(PATH.'preparation/tombo/'.$line['id_i']).'">';
					$linka = '';
					$img = 'img/books/books_'.$line['i_status'].'.png';	
				break; 
				
				default:
				$sta = '->'.$st;
			break;
			}

			$sx .= '<div class="col-md-1 text-center">';
			$sx .= '<span class="small">'.$ex.'</span>';
			$sx .= $link;	
				

			/* Exemplar n. ********************************/	
			$lb = $line['i_localization'];
			
			if (strlen($lb) > 0)
			{
				print_r($line);
				$sx .= '<br>'.$line['i_ln1'];
				$sx .= '<br>'.$line['i_ln2'];
				$sx .= '<br>'.$line['i_ln3'];
				$sx .= '<br>'.$line['i_ln4'];
			}
			$sx .= '<img src="'.base_url($img).'" class="img-fluid">';
			
			$sx .= $lb;
			$sx .= $linka;
			$sx .= '</div>';
		}
		if (count($rlt) == 0)
		{
			$sx .= '<div class="col-12">'.message('Nenhum item localizado',2).'</div>';
		}
	$sx .= '</div>';
	$sx .= '</div>';
	return($sx);
}	

function tombo_insert($tombo, $isbn, $tipo, $status=9, $place, $manifestation=0,$exemplar=1)
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
			i_status, i_library_place, i_exemplar
		)
			values
		(
		'$tombo',$manifestation, '$isbn', 
		'".$lib."','".ip()."',$acq,
		$status,$place,$exemplar
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
			array_push($cp,array('$S40','',msg('nr_tombo'),true,true));
			array_push($cp,array('$T80:5','',msg('isbn_list'),true,true));
			array_push($cp,array('$C','',msg('without_isbn'),false,true));
			$sql = "select * from library_place where lp_LIBRARY = ".LIBRARY."";
			array_push($cp,array('$Q id_lp:lp_name:'.$sql,'',msg('library_place'),true,true));
			$sx = '<div class="container"><div class="row">';
			$sx .= '<div class="col-12">';
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
				$btn_to_prepar = '<a href="'.base_url(PATH.'preparation/alter_status/'.$id.'/2/'.checkpost_link($id.'2')).'" class="btn btn-primary">'.msg('send_to_print').'</a>';
				$btn_marc_import = '<a href="'.base_url(PATH.'preparation/tombo/'.$id.'/marc/'.checkpost_link($id.'2')).'" class="btn btn-primary">'.msg('marc_import').'</a>';
				switch($status)
					{
						case '1':
							$sx .= '<div class="border1">';
							$sx .= $btn_to_prepar;
							$sx .= $btn_marc_import;
							$sx .= '</div>';
							break;
						case '2':
							$sx .= '<div class="border1">';
							$sx .= '<a href="'.base_url(PATH.'preparation/alter_status/'.$id.'/5/'.checkpost_link($id.'3')).'" class="btn btn-primary">'.msg('send_to_3').'</a>';
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
			$sx .= '<div class="col-10">';
			
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
					$dt = $this->le_tombo($id);
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
								$sx .= '<td '.$st.' >'.msg('status_'.$li['i_status']).'</td>';
								$sx .= '<td '.$st.' class="small text-center">'.$lk.'</td>';
								$sx .= '<td '.$st.' class="small text-center">'.$link.$up.$linka.'</td>';
								$sx .= '</tr>';
							}
						$sx .= '</table>';
					}
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
				
				case 'X3':
					$view = 4;
				break;
				
				
				case 'X0':
					$sx .= $this->marc_api->form($id);
					$sx .= $this->books->manual_form($id);
					$isbn = $dt['i_identifier'];
					$sx .= $this->sourcers->show($isbn);
					
					$sx .= '';
				break;
				
				
				case 'X1':
					$view = 2;
					$sx .= $this->books->locate($isbn,$id);
					$dt = $this->le_tombo($id);
					
					if (strlen($dt['w_title']) > 0)
					{
						$this->item_status($id,2);
						redirect(base_url(PATH.'preparation/tombo/'.$id));	
					} else {
						/* Envia para catalogação manual / MARC21 */
						$this->item_status($id,5);
						redirect(base_url(PATH.'preparation/tombo/'.$id));	
					}
					exit;
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
			}
			
			if (strlen($sta) > 0)
			{
				
			} else {
				$sx .= $actions;
			}
			$sx .= '</div>'.cr();
			
			/**************** WORKFLOW *****************************/
			$sx .= '<div class="col-2">';
			$dt = array('pos'=>$view,'id'=>$id);
			$sx .= $this->load->view('tools/workflow',$dt,true);
			
			$sx .= '</div>';
			
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
						$sx .= '<div class="col-md-12">';
						$sx .= '<span class="small">'.msg('upload_item_info').'</span></div>';
						$sx .= '<div class="col-md-12">';
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
		
	}
	?>