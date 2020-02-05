<?php
class Books_item extends CI_model
{
	function header($dt)
		{
			$sx = '';
			$sx .= '<div class="container" style="border-bottom: 1px solid #000000;">';
			$sx .= '<div class="row">';
			$sx .= '<div class="col-2">';
			$sx .= strzero($dt['i_tombo'],4);
			$sx .= '<br/>';
			$sx .= 'Ex:'.$dt['i_exemplar'];
			$sx .= '</div>';
			$sx .= '<div class="col-2">';
			$sx .= $dt['i_library'];
			$sx .= '</div>';

			$sx .= '<div class="col-2">';
			$sx .= $dt['i_library_place'];
			$sx .= '</div>';


			$sx .= '<div class="col-2">';
			$sx .= $dt['i_library_classification'];
			$sx .= '</div>';

			$sx .= '<div class="col-2">';
			$sx .= 'ISBN: '.$dt['i_identifier'];
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
		LEFT JOIN find_manifestation ON i_manitestation = id_m
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

	function tombo_insert($tombo, $isbn, $tipo, $status=9, $place, $manifestation=0,$exemplar=1)
	{
		$sql = "select * from find_item 
		where i_tombo = $tombo 
		and i_identifier = '$isbn' 
		and i_library_place = $place
		and i_library = ".LIBRARY;
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
			'".LIBRARY."','".ip()."',$tipo,
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
		echo $sql;

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
			$sx .= '<style>
					.container { 
						border: 1px solid #00FF00; 
					} 
					.row { 
						border: 1px solid #0000FF; 
					} 
					</style>';
			$sx .= 'Coletar Metadados';
			$dt = $this->le_tombo($id);
			$isbn = $dt['i_identifier'];
			$sx .= $this->books->locate($isbn,$id);

			$sx .= $this->marc_api->form($id);
			$sx .= $this->books->manual_form($id);
			$sx .= $this->sourcers->show();

			$sx .= '';
			break;


			case '1':
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
			case '5':
			$idm = $dt['i_manitestation'];
			//$sx .= $this->books->catalog_edit($idm);

			$sx .= $this->marc_api->form();
			$sx .= $this->sourcers->show();
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

			/************************** CLASSIFICACAO - 1 ***/
			case '2':
			$view = 3;
			$this->load->model("classifications");
			$sx .= $this->classifications->classification_item($dt['id_i']);
			$sx .= $this->classifications->classified_item($dt['id_i']);
			$actions = $this->actions(3,$dt['id_i']);

			//$sx .= $this->classification_item_show($id);
			break;

			case '3':
			$view = 4;
			$this->load->model("subjects");
			$sx .= $this->subjects->indexing_item($dt['id_i']);
			$sx .= $this->subjects->indexed_item($dt['id_i']);
			$actions = $this->actions(4,$dt['id_i']);			
			break;

			/*************************** Preparo físico *****************/
			case '4':
			$view = 5;
			$actions = $this->actions(8,$dt['id_i']);			
			break;			

		}

		if (strlen($sta) > 0)
		{
			$sx .= '<a href="'.base_url(PATH.'m/'.$id).'" class="btn btn-outline-primary">';
			$sx .= msg("return");
			$sx .= '</a>';
		} else {
			$sx .= $actions;
		}
		$sx .= '</div>'.cr();

		/**************** WORKFLOW *****************************/
		$sx .= '<div class="col-2">';
		$dt = array('pos'=>$view);

		/************ Habilita botão do Workflow ***************/		
		$sx .= $this->load->view('tools/workflow',$dt,true);
		
		$sx .= '</div>';

		return($sx);
	}	
}
?>