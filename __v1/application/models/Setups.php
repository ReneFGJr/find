<?php
class setups extends CI_model
{
	function main($path,$id)
	{

		$this->load->model('isbn');
		$sx = '<div class="row">';
		$sx .= '<div class="col-3">';
		$sx .= $this->menu($path);
		$sx .= '</div>';
		$sx .= '<div class="col-9">';
		switch($path)
		{
			case 'loan':
			$sx .= '<h1>'.msg($path).'</h1>';
			$sx .= $this->library_loan_setup($id);
			break;

			case 'library_edit':
			$sx .= '<h1>'.msg($path).'</h1>';
			$sx .= $this->library_edit($id);
			break;

			/******************** Library Place **********************/
			case 'library_place':
			$sx .= '<h1>'.msg($path).'</h1>';
			$sx .= $this->library_place();
			break;

			case 'library_place_edit':
			$sx .= '<h1>'.msg($path).'</h1>';
			$sx .= $this->library_place_edit($id);
			break;

			/******************** Library Place **********************/
			case 'classifications':
			$sx .= '<h1>'.msg($path).'</h1>';
			$sx .= $this->classification();
			break;

			/******************** Library Place **********************/
			case 'subject':
			$sx .= '<h1>'.msg($path).'</h1>';
			$sx .= $this->subject_skos();
			break;			

			case 'library_place_edit':
			$sx .= '<h1>'.msg($path).'</h1>';
			$sx .= $this->library_place_edit($id);
			break;		

			case 'setup_library_logo':
			$sx .= '<h1>'.msg($path).'</h1>';
			$sx .= $this->library_logos($id);
			break;							

			/******************** Default ****************************/
			default:
			$sx .= $this->library();
			break;
		}
		$sx .= '</div>';
		$sx .= '</div>';
		return($sx);		
	}
	function menu($op='')
	{
		$m = array();
		$m['setup_library'] = 'setup/library_edit';
		$m['setup_library_logo'] = 'setup/setup_library_logo';
		$m['setup_library_place'] = 'setup/library_place';
		$m['setup_classifications'] = 'setup/classifications';
		$m['setup_subject'] = 'setup/subject';
		$m['setup_loan_parameters'] = 'setup/loan';
		
		$sx = '<div class="btn btn-secondary">';
		$sx .= '<h3>'.msg('menu_setup').'</h3>';
		$sx .= '<ul class="list-group text-left">';
		foreach ($m as $key => $value) {
			$ac = '';
			if ($value == 'setup/'.$op)
				{
					$ac = 'active';
				}
			$sx .= '<li class="'.$ac.'">';
			$sx .= '<a href="'.base_url(PATH.$value).'" class="list-group-item '.$ac.'">';
			$sx .= msg($key);
			$sx .= '</a>';
			$sx .= '</li>';
		}
		$sx .= '</ul>';
		$sx .= '</div>';
		return($sx);
	}

	function library_logos()
		{
			$types = array('','Logo - Mini','Logo - Middle','Banner top');
            $size_w = array(0,150,600,1600);
            $size_h = array(0,150,600,210);
			$sx = '';

			for ($r=1;$r < count($types);$r++)
				{
					$sx .= '<h5>'.msg($types[$r]).'</h5>';
					$sx .= '<sup>'.$size_w[$r].'x'.$size_h[$r].'</sup>';
					$sx .= '<br>';

					/* Imagem - logos */
					$editar = 1;
					$type = $r;
					$sx .= $this->libraries->logo(LIBRARY,$type,$size_w[$r],$size_h[$r],$editar);
					$sx .= '<hr>';
				}
			return($sx);
		}

	/****************** Classifications  *************************/
	function classification()
		{
			$this->load->model("classifications");
			$sx = $this->classifications->row_type();
			return($sx);
		}

	function subject_skos()
		{
			$this->load->model("skos");
			$sx = $this->skos->row_type();
			return($sx);			
		}


	/****************** Library **********************************/
	function library()
	{
		$this->load->model("libraries");
		$sx = $this->libraries->about();
		$sx .= '<a href="'.base_url(PATH.'setup/library_edit').'" class="btn btn-outline-primary">'.msg('edit').'</a>';
		return($sx);
	}	
	function library_edit()
	{
		$this->load->model("libraries");
		$dt = $this->libraries->le_id(LIBRARY);
		$id = $dt['id_l'];
		$rsp = $this->libraries->edit('setup',$id);
		$sx = $rsp[0];
		$form = $rsp[1];
		if ($form->saved > 0)
		{
			redirect(base_url(PATH.'setup/library'));
		}
		return($sx);
	}

	function library_loan_setup($d1='')
	{
		$sx = '';
		$this->load->model("libraries");
		$cp = array();
		array_push($cp,array('$H8','id_l','',false,false));
		array_push($cp,array('$[1-30]','l_loan',msg('l_loan'),true,true));
		array_push($cp,array('$[1-30]','l_loan_plus',msg('l_loan_plus'),true,true));
		array_push($cp,array('$[1-5]','l_loan_renovations',msg('l_loan_renovations'),true,true));
		array_push($cp,array('$SN','l_loan_reserve',msg('l_loan_reserve'),true,true));
		$form = new form;
		$form->id = $d1;
		$sx = $form->editar($cp,'library');
		if ($form->saved > 0)
		{
			redirect(base_url(PATH.'setup/library'));
		}
		return($sx);
	}	

	/****************** Library **********************************/
	function library_place()
	{
		$this->load->model("libraries");
		$dt['edit'] = 'setup/library_place_edit/';
		$sx = $this->libraries->about_places($dt);

		$sx .= '<hr>';
		$sx .= '<a href="'.base_url(PATH.$dt['edit'].'0').'" class="btn btn-outline-primary">'.msg('new').' '.msg('place').'</a>';
		return($sx);
	}

	function library_place_edit($id)
	{
		$this->load->model("libraries");
		$rsp = $this->libraries->edit_places($id);
		$sx = $rsp[0];
		$form = $rsp[1];
		if ($form->saved > 0)
		{
			redirect(base_url(PATH.'setup/library_place'));
		}
		return($sx);
	}			
}