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
		
		$sx .= '<div class="col-2">';
		$sx .= '<div>'.msg('Status').': '.msg('item_status_'.$dt['i_status']).'</div>';
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
		
		$sx = '<div class="container">';
		$sx .= '<div class="row">';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$cor = 'background-color: #D0FFD0;';
			$st = trim($line['i_status']);
			$link = '';
			$linka = '';
			switch($st)
			{
				case '1':
					$sta = msg('in_prepare');
					$cor = 'background-color: #F0F0D0;';
					$link = '<a href="'.base_url(PATH.'preparation/tombo/'.$line['id_i']).'">';
					$linka = '';
				break; 
				
				default:
				$sta = '->'.$st;
			break;
		}
		
		$sx .= '<div class="col-md-2">';			
		$sx .= '<div class="book_label" style="'.$cor.'">';
		$lb = $line['i_localization'];
		
		
		if (strlen($lb) == 0)
		{
			$sx .= $sta;
			$sx .= '<br/>';
			$sx .= '<br/>';
		} 
		else
		{
			print_r($line);
			$sx .= '<br>'.$line['i_ln1'];
			$sx .= '<br>'.$line['i_ln2'];
			$sx .= '<br>'.$line['i_ln3'];
			$sx .= '<br>'.$line['i_ln4'];
		}
		$sx .= $link;
		$sx .= '<br/>ex:'.strzero($line['i_tombo'],7);
		$sx .= $linka;
		$sx .= '</div>';
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
			array_push($cp,array('$S40','',msg('isbn'),true,true));
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
			where $wh and i_library = ".LIBRARY."
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
						
						
						$dta = $rdf -> le($work[0]);
						$sx .= '<h1>WORK</h1>';
						$sx .= $rdf -> form($work[0], $dta);
						
						$sx .= '<h1>MANIFESTATION</h1>';
						$dta = $rdf -> le($idm);
						$sx .= $rdf -> form($idm, $dta);
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
	}
	?>