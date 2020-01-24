<?php
class book_preparations extends CI_model
{

	function main($path,$id)
	{
		$sx = '
		<nav aria-label="breadcrumb" style="margin-top: 20px;">
		<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="'.base_url(PATH.'/').'">Home</a></li>
		<li class="breadcrumb-item"><a href="'.base_url(PATH.'/preparation/').'">'.msg("Preparation").'</a></li>'.cr();
		if (strlen($path) > 0)
		{
			$sx .= '<li class="breadcrumb-item"><a href="'.base_url(PATH.'/preparation/'.$path).'">'.msg("Preparation_".$path).'</a></li>'.cr();
		}
		$sx .= '
		</ol>
		</nav>
		';
		switch($path)
		{
			case 'acquisition':
			$sx .= $this->acquisition();
			break;

			default:
			$sx = '';
			$sx .= '<div class="col-md-2 text-center">';
			$sx .= '<a href="'.base_url(PATH.'preparation/acquisition').'">';
			$sx .= '<img src="'.base_url('img/icon/icone_processament_tecnico_256.jpg').'" class="img_menu img-fluid" alt="Preparo Técnico"  title="Preparo técnico">';
			$sx .= '<br>Preparo téncico';
			$sx .= '</a>';
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
		</ul>';
		return($sx);
	}            
}
?>