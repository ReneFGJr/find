<?php
class Classifications extends CI_model
{
	var $table_type = 'find_classification_type';
	var $table = 'find_classification';
	var $html_type = 'admin/classification/';

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

	function classification_item($id)
	{
		/************************************** SAVE ********/
		$idc = get("classification");
		if (strlen($idc) > 0)
		{
			$this->classification_item_insert($id,$idc);
		}

		$sql = "select * from find_classification 
		where cl_type = 1
		order by cl_class, cl_description";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		$sx = '';
		$sx .= '<form method="post">';

		$sx .= '<select class="form_control" style="width: 100%;" name="classification" id="classification" size=10>';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];					
			$op = $line['cl_class'] . ' - '.$line['cl_description'];
			$sx .= '<option value="'.$line['id_cl'].'">';
			$sx .= $op;
			$sx .= '</option>';
		}
		$sx .= '</select>';

		$sx .= '<input type="submit" class="btn btn-outline-primary" value="'.msg('Classification').'>>">';
		$sx .= '</form>';

		$sx .= '<hr>';
		$sx .= $this->classification_item_show($id);

		return($sx);
	}


	function classification_item_insert($id,$idc)
	{
		$ok = 1;
		$sql = "select * from find_classification_item 
		where ci_item = $id ";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$ord = 0;
		
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			if ($line['ci_order'] >= $ord)
			{
				$ord = ($line['ci_order']+1);
			}
			/* Verifica se ja existe classificação cadastrada *************/
			if ($line['ci_classification'] == $idc) 
				{ $ok = 0; }
		}

		if ($ok == 1)
		{
			$sql = "insert into find_classification_item
			(ci_item, ci_classification, ci_order)
			values
			($id, $idc, $ord)";
			$rlt = $this->db->query($sql);
		}

	}
	function classification_item_show($id)
	{
		$sql = "select * from find_classification_item
		INNER JOIN find_classification ON ci_classification  = id_cl
		where ci_item = $id
		order by ci_order";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		$sx = '';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$cod = $line['cl_class'];
			$des = $line['cl_description'];
			$sx .= '<span class="item_status item_status_7">'.$cod.' - '.$des.'</span>';	
			$sx .= ' ';
		}		
		return($sx);
	}

	function classification_list($id)
	{
		$sql = "select * from ".$this->table." 
		where cl_type  = $id
		order by cl_class ";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$sx = '<table class="table">';
		$sx .= '<tr>';
		$sx .= '<th width="20%">Notação</th>';
		$sx .= '<th>Descrição</th>';
		$sx .= '</tr>';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$sx .= '<tr>';
			$sx .= '<td>'.$line['cl_class'].'</td>';
			$sx .= '<td>'.$line['cl_description'].'</td>';
			$thesa = $line['cl_rdf'];
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
		$sx = '<ul>';
		$dt = $this->le($id);
		if (strlen(trim($dt['ct_rdf_url'])) > 0)
		{
			$url = trim($dt['ct_rdf_url']);
			$url = troca($url,'/terms/','/terms_from_to/').'/xml';
			$t = file_get_contents($url);
			$xml = simplexml_load_string($t);
			foreach ($xml as $key => $value) {
				$termo = (string)$value->name;
				if (strpos($termo,'-') > 0)
				{
					$descrition = trim(substr($termo,strpos($termo,'- ')+1,strlen($termo)));
				} else {
					$descrition = '';
				}
				$term = trim(substr($termo,0,strpos($termo,'- ')-1));
				$sigla = (string)$value->sigla;
				$rdf = (string)$value->id;
				$img = (string)$value->image;
				$sx .= '<li><tt>'.$term.'</tt> - '.$descrition;
				if ((strlen($descrition) > 0) and (strpos($termo,'-') > 0))
				{
					$sx .= $this->insert_classification_term($id,$term,$descrition,$rdf,$img);
				} else {
					$sx .= '<span class="item_status item_status_0">'.msg('ignored').'</span>';
				}
				$sx .= '</li>';
			}
		}
		return($sx);
	}

	function insert_classification_term($id,$term,$descrition,$rdf,$image='')
	{
		if (strlen($rdf) > 0)
		{
			$sql = "select * from find_classification
			where cl_rdf = '$rdf'
			and cl_type  = $id";

			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) == 0)
			{
				$sql = "insert into find_classification
				(cl_type, cl_class, cl_description,
				cl_rdf, cl_image ) 
				values 
				($id,'$term','$descrition',
				'$rdf','$image')	";
				$xrlt = $this->db->query($sql);
				return('<span class="item_status item_status_7">'.msg('new').'</span>');
			} else {
				$sql = "update find_classification
				set 
				cl_class = '$term', 
				cl_description = '$descrition',
				cl_image = '$image'
				where id_cl = ".$rlt[0]['id_cl'];
				$xrlt = $this->db->query($sql);
				return('<span class="item_status item_status_8">'.msg('update').'</span>');
			}		
			return(1);
		}

	}


	function le($id)
	{
		$sql = "select * from ".$this->table_type." 
		where id_ct = ".$id;

		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) > 0)
		{
			return($rlt[0])	;
		} else {
			result(array());
		}
	}
	function action($act,$id)
	{
		$sx = '<div class="row">';
		switch($act)
		{
			case 'thesa':
			$sx .= '<div class="col-12"><h1>Importation from Thesa</h1></div>';
			$sx .= $this->thesa_import($id);
			break;

			case 'view':
			$sx = $this->classification_list($id);
			return($sx);
			break;

			default:
			$sx = $this->classes();
			break;
			break;
		}
		$sx .= '</div>';
		return($sx);
	}

	function classes()
	{
		$sql = "select * from find_classification_type 
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
			$link = '<a href="'.base_url(PATH.'admin/classification/view/'.$line['id_ct']).'">';
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