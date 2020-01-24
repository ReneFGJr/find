<?php
class book_preparations extends CI_model
{

	function main($path,$id)
	{
		$this->load->model('isbn');

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

			case 'tombo':
			$sx .= "TOMBO";
			break;			

			case 'acquisition_in':
			$sx = $this->acquisition_in();
			break;

			default:
			$sx = '';
			$sx .= '<div class="col-md-4 col-6 col-sm-5  col-lg-3 col-xl-3 text-center">';
			$sx .= '<a href="'.base_url(PATH.'preparation/acquisition').'">';
			$sx .= '<img src="'.base_url('img/icon/icone_processament_tecnico_256.jpg').'" class="img_menu img-fluid" alt="Preparo Técnico"  title="Preparo técnico">';
			$sx .= '<br>Preparo téncico';
			$sx .= '</a>';
			$sx .= '</div>';
			break;			
		}
		return($sx);
	}

	function acquisition_in()
	{	
		$msgs = '';
		$form = new form;
		$tombo = get("dd1");
		if (strlen($tombo) == 0)
		{
			$_GET['dd1'] = $this->tombo_next();
		}

		$isbn = get("dd2");		
		if ((strlen($isbn) == 13) or (strlen($isbn) == 10))
		{
			$isbn = $this->isbn->isbns($isbn);
			$isbn_o = $isbn['isbn13'];
			$isbn = sonumero(substr($isbn_o,0,12));
			$isbn_dv = genchksum13($isbn);	
			$isbn_or = substr($isbn_o,12,1);
			
			if ($isbn_dv == $isbn_or)
			{
				$msgs = "OK";
				$rs = $this->tombo_insert($tombo,$isbn_o,1);
				if ($rs[0] == 1)
				{
					redirect(base_url(PATH.'preparation/acquisition_in'));
				} else {
					$msgs = $rs[1];
				}
			} else {
				$msgs = 'ISBN Inválido';
			}
		}

		
		$cp = array();
		array_push($cp,array('$H8','','',false,false));
		array_push($cp,array('$S40','',msg('nr_tombo'),true,true));
		array_push($cp,array('$S40','',msg('isbn'),true,true));
		array_push($cp,array('$C','',msg('without_isbn'),false,true));
		array_push($cp,array('$M','',$msgs,false,false));
		$sx = $form->editar($cp,'');

		$sx .= $this->tombo_list();
		return($sx);

	}

	function tombo_list($limit = 10)
		{
		$sql = "select * from find_item 
		where i_status = 0 and i_library = ".LIBRARY."
		limit ".$limit;
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();

		$sx = '<table class="table">'.cr();
		$sx .= '<tr>';
		$sx .= '<th>Tombo</th>';
		$sx .= '<th>ISBN/ID</th>';
		$sx .= '<th>Dt. Criação</th>';
		$sx .= '<th>Situação</th>';
		$sx .= '<th>Tipo</th>';
		$sx .= '</tr>';

		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$link = '<a href="'.base_url(PATH.'preparation/tombo/'.$line['id_i']).'">';
			$linka = '</a>';
			$sx .= '<tr>';
			$sx .= '<td>'.$link.strzero($line['i_tombo'],7).$linka.'</td>';
			$sx .= '<td>'.$link.$line['i_identifier'].$linka.'</td>';
			$sx .= '<td>'.$link.stodbr($line['i_created']).$linka.'</td>';
			$sx .= '<td>'.$link.msg('item_status_'.$line['i_status']).$linka.'</td>';
			$sx .= '<td>'.$link.msg('item_aquisicao_'.$line['i_aquisicao']).$linka.'</td>';
			$sx .= '</tr>';
		}
		$sx .= '</table>';
		return($sx);
		}

	function tombo_insert($tombo, $isbn, $tipo)
	{
		$sql = "select * from find_item 
		where i_tombo = $tombo and 	i_identifier = '$isbn' and i_library = ".LIBRARY;
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) == 0)
		{
			$sql = "insert into find_item
					(i_tombo,i_identifier, i_library, i_ip, i_aquisicao)
					values
					('$tombo','$isbn', '".LIBRARY."','".ip()."',$tipo)";
			$rlt = $this->db->query($sql);					
			return(array(1,'OK! Item inserido com sucesso!'));
		} else {
			return(array(-1,'ERRO! Número tombo já cadastrado!'));
		}
		
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


	function acquisition()
	{
		$sx = 'Forma de Aquisição
		<ul>
		<li>Compra</li>
		<li>Doação</li>
		<li>Permuta</li>
		<li><a href="'.base_url(PATH.'preparation/acquisition_in/').'">Incorporação no acervo</a></li>
		</ul>';
		return($sx);
	}            
}
?>