<?php
class setups extends CI_model
{
	function main($path,$id)
	{

		$this->load->model('isbn');
		$sx = '<div class="row">';
		$sx .= '<div class="col-3">';
		$sx .= $this->menu();
		$sx .= '</div>';
		$sx .= '<div class="col-9">';
		switch($path)
		{
			case 'library':
			$sx .= $this->library();
			break;

			case 'library_edit':
			$sx .= $this->library_edit();
			break;

			/******************** Library Place **********************/
			case 'library_place':
			$sx .= $this->library_place();
			break;

			case 'library_place_edit':
			$sx .= $this->library_place_edit($id);
			break;

			/******************** Library Place **********************/
			case 'classifications':
			$sx .= $this->classification();
			break;

			case 'library_place_edit':
			$sx .= $this->library_place_edit($id);
			break;						

			/******************** Default ****************************/
			default:
			$sx .= 'Hello World!';
			break;
		}
		$sx .= '</div>';
		$sx .= '</div>';
		return($sx);		
	}
	function menu()
	{
		$m = array();
		$m['library'] = 'setup/library';
		$m['library_place'] = 'setup/library_place';
		$m['classifications'] = 'setup/classifications';
		$m['loan_parameters'] = 'setup/loan';
		$sx = '';
		$sx .= '<h1>'.msg('setup').'</h1>';
		$sx .= '<ul class="menu_setup">';
		foreach ($m as $key => $value) {
			$sx .= '<li>';
			$sx .= '<a href="'.base_url(PATH.$value).'">';
			$sx .= UpperCase(msg($key));
			$sx .= '</a>';
			$sx .= '</li>';
		}
		$sx .= '</ul>';
		return($sx);
	}

	/****************** Classifications  *************************/
	function classification()
		{
			$this->load->model("classifications");
			$sx = $this->classifications->row_type();
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
		$rsp = $this->libraries->edit($dt['id_l']);
		$sx = $rsp[0];
		$form = $rsp[1];
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
?>	