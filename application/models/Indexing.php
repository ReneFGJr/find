<?php
class Indexing extends CI_model
{
	var $table_type = 'find_indexing_type';
	var $table = 'find_indexing';
	var $html_type = 'admin/indexing/';

	function cpr($id=0)
	{
		$cp = array();
		array_push($cp,array('$H8',"id_ct","id_ct",false,false,false)); 
		array_push($cp,array('$S100',"ct_name","ct_name",True,True,True)); 
		array_push($cp,array('$S100',"ct_description","ct_description",false,True,false)); 
		array_push($cp,array('$SN',"ct_active","ct_active",True,True,True));
		array_push($cp,array('$S100',"ct_rdf_url","ct_rdf_url",false,True,false));  
		return($cp);
	}

	function indexing_item($id)
	{
		/************************************** SAVE ********/
		$idc = get("indexing_add");
		if (strlen($idc) > 0)
		{
			$this->indexing_item_insert($id,$idc);
		}

		$sql = "select * from find_indexing 
		where il_type = 1
		order by il_concept";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		$sx = '';
		$sx .= '<form method="post">';

		$sx .= '<select class="form_control" style="width: 100%;" name="indexing_add" id="indexing" size=10>';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];					
			$op = $line['il_concept'];
			$sx .= '<option value="'.$line['id_il'].'">';
			$sx .= $op;
			$sx .= '</option>';
		}
		$sx .= '</select>';

		$sx .= '<input type="submit" class="btn btn-outline-primary" value="'.msg('indexing_include').'>>">';
		$sx .= '</form>';

		$sx .= '<hr>';

		return($sx);
	}

	function classified_item($id)
		{
		/************************************** SAVE ********/
		$idc = get("indexing_remove");
		if (strlen($idc) > 0)
		{
			$this->indexing_item_remove($id,$idc);
		}			
		$sql = "select * from find_indexing_item
					INNER JOIN find_indexing ON ii_indexing = id_il
					where ii_item = $id
					order by ii_order";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		$sx = '';
		$sx .= '<form method="post">';

		$sx .= '<select class="form_control" style="width: 100%;" name="indexing_remove" id="indexing" size=10>';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];					
			$op = $line['il_concept'];
			$sx .= '<option value="'.$line['id_il'].'">';
			$sx .= $op;
			$sx .= '</option>';
		}
		$sx .= '</select>';

		$sx .= '<input type="submit" class="btn btn-outline-primary" value="'.msg('indexing_remove').'>>">';
		$sx .= '</form>';
		return($sx);		
		}

	function indexing_item_remove($id,$idc)
		{
			$sql = "delete from find_indexing_item 
					where ii_item = $id and ii_indexing = $idc";
			$rlt = $this->db->query($sql);
			return(1);
		}

	function indexing_item_insert($id,$idc)
	{
		$ok = 1;
		$sql = "select * from find_indexing_item 
		where ii_item = $id ";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$ord = 0;
		
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			if ($line['ii_order'] >= $ord)
			{
				$ord = ($line['ii_order']+1);
			}
			/* Verifica se ja existe classificação cadastrada *************/
			if ($line['ii_indexing'] == $idc) 
				{ $ok = 0; }
		}

		if ($ok == 1)
		{
			$sql = "insert into find_indexing_item
			(ii_item, ii_indexing, ii_order)
			values
			($id, $idc, $ord)";
			$rlt = $this->db->query($sql);
		}

	}
	function indexing_item_show($id)
	{
		$sql = "select * from find_indexing_item
				INNER JOIN find_indexing ON il_indexing  = id_il
		where ii_item = $id
		order by ii_order";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		$sx = '';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$des = $line['il_concept'];
			$sx .= '<span class="item_status item_status_7">'.$des.'</span>';	
			$sx .= ' ';
		}		
		return($sx);
	}

	function indexing_list($id)
	{
		$sql = "select * from ".$this->table." 
		where il_type  = $id
		order by il_concept ";

		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$sx = '<table class="table">';
		$sx .= '<tr>';
		$sx .= '<th width="80%">Notação</th>';
		$sx .= '<th>LinkedData</th>';
		$sx .= '</tr>';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$sx .= '<tr>';
			$sx .= '<td>'.$line['il_concept'].'</td>';
			$thesa = $line['il_rdf'];
			if (strlen($thesa) > 0)
			{
				$link = troca($thesa,'thesa:c','https://www.ufrgs.br/tesauros/index.php/thesa/c/');
				$link = '<a href="'.$link.'" target="_blank">';
				$linka = '</a>';
				$thesa = $link.$thesa.$linka;
			}
			$sx .= '<td>'.$thesa.'</td>';
			
			$sx .= '</tr>'.cr();
		}
		$sx .= '</table>';
		return($sx);
	}

	function thesa_import($id)
	{
		if (round($id) == 0)
			{
				return("ERRO");
			}
		$sx = '<ul>';
		$dt = $this->le($id);
		if (strlen(trim($dt['ct_rdf_url'])) > 0)
		{
			$url = trim($dt['ct_rdf_url']);
			$url = troca($url,'/terms/','/terms_from_to/').'/xml';
			$t = file_get_contents($url);

			$xml = simplexml_load_string($t);
			foreach ($xml as $key => $value) {
				$term = (string)$value->name;
				$sigla = (string)$value->sigla;
				$rdf = (string)$value->id;
				$img = (string)$value->image;
				$sx .= '<li><tt>'.$term.'</tt>';
				if (strlen($term) > 0)
				{
					$sx .= $this->insert_indexing_term($id,$term,'',$rdf,$img);
				} else {
					$sx .= '<span class="item_status item_status_0">'.msg('ignored').'</span>';
				}
				$sx .= '</li>';
			}
		}
		return($sx);
	}

	function insert_indexing_term($id,$term,$descrition,$rdf,$image='')
	{
		if (strlen($rdf) > 0)
		{
			$sql = "select * from find_indexing
			where il_rdf = '$rdf'
			and il_type  = $id";

			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) == 0)
			{
				$sql = "insert into find_indexing
				(il_type, il_concept,
				il_rdf, il_image ) 
				values 
				($id,'$term',
				'$rdf','$image')	";
				$xrlt = $this->db->query($sql);
				return('<span class="item_status item_status_7">'.msg('new').'</span>');
			} else {
				$sql = "update find_indexing
				set 
				il_concept = '$term', 
				il_image = '$image'
				where id_il = ".$rlt[0]['id_il'];
				$xrlt = $this->db->query($sql);
				return('<span class="item_status item_status_8">'.msg('update').'</span>');
			}		
			return(1);
		}

	}


	function le($id)
	{
		$id = round($id);
		$sql = "select * from ".$this->table_type." 
		where id_it = ".$id;

		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) > 0)
		{
			return($rlt[0])	;
		} else {
			return(array());
		}
	}

	function show($dt)
		{
			if (!isset($dt['id_it'])) { return(""); }
			$id = $dt['id_it'];
			$sx = '';
			$sx .= '<div class="col-8 big" style="padding: 10px;">';
			$sx .= $dt['ct_name'];
			$sx .= '</div>';

			$sx .= '<div class="col-4 text-right">';
			if ((perfil("#ADM") > 0) and (strpos($dt['ct_rdf_url'],'ufrgs.br/tesauros') > 0))
			{
				$sx .= '<a href="'.base_url(PATH.'admin/indexing/thesa/'.$id).'" class="btn btn-light">
				<img src="'.base_url('img/logo/logo_thesa.png').'" height="20">
				Import from Thesa</a>';
			}			
			$sx .= '</div>';
			return($sx);
		}
	function action($act,$id)
	{
		$sx = '<div class="row">';
		switch($act)
		{
			case 'thesa':
			$dt = $this->le($id);
			$sx .= $this->show($dt);			
			$sx .= '<div class="col-12" style="padding: 5px;"><span class="big">Importation from Thesa</span></div>';
			$sx .= $this->thesa_import($id);
			break;

			case 'view':
			$dt = $this->le($id);
			$sx .= $this->show($dt);
			$sx .= $this->indexing_list($id);
			break;

			default:
			$sx .= '<div class="col-12">';
			$sx .= '<div class="big" style="padding: 5px;">'.msg('indexing_list').'</div>';
			$sx .= $this->classes();
			$sx .= '</div>';
			break;
		}
		$sx .= '</div>';
		return($sx);
	}

	function classes()
	{
		$sql = "select * from find_indexing_type 
		where ct_active = 1
		order by ct_name";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$sx = '<table class="table">';
		$sx .= '<tr>';
		$sx .= '<th >Classificação</th>';
		$sx .= '<th >Link</th>';
		$sx .= '</tr>';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$sx .= '<tr>';
			$link = '<a href="'.base_url(PATH.'admin/indexing/view/'.$line['id_it']).'">';
			$linka = '</a>';
			$sx .= '<td>'.$link.$line['ct_name'].$linka.'</td>';
			$thesa = $line['ct_rdf_url'];
			if (strlen($thesa) > 0)
			{
				$link = '<a href="'.$thesa.'" target="_new">'.msg("link").'</a>';
			} else {
				$link = '';
			}
			$sx .= '<td>'.$link.'</td>';			
		}						
		$sx .= '</table>';
		return($sx);
	}
	function row_type($path='',$id=0)
	{
		$dt = array();
		$dt['table'] = $this->table_type;
		$dt['path'] = base_url(PATH.$this->html_type);		
		$dt['cp'] = $this->cpr();
		//$dt['where'] = 'p_ativo = 1 ';

		switch($path)
		{
			/* Edit */
			case 'edit':
			$dt['id'] = $id;
			$dd = $this->le($id);
					//$sx = $this->load->view("product/view",$dd,true);
			$form = new form;
			$form->id = $id;
			$sx = $form->editar($dt['cp'],$dt['table']);
			if ($form->saved > 0)
			{
				if ($id == 0)
				{
					redirect(base_url(PATH.$this->html_type));
				} else {
					redirect(base_url(PATH.$this->html_type.'/view/'.$id));	
				}

			}
			break;

			/* Row */
			default:					
			$sx = row2($dt);
			break;
		}		

		$cpr['fluid'] = false;
		return($sx);
		//view($sx,$cpr);
	}
}
?>