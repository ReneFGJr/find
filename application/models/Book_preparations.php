<?php
class book_preparations extends CI_model
{
	function main($path,$id)
	{
		$this->load->model('isbn');
		$this->load->model('books');
		$sx = '';
		switch($path)
		{
			case 'acquisition':
			$sx .= $this->acquisition();
			break;

			case 'tombo':
			$sx .= $this->tombo_editar($id);
			break;			

			case 'acquisition_in':
			$sx = $this->acquisition_in();
			break;

			case 'itens':
			if ($id == 0) { $id = array(0,5); }
			$sx = $this->preparation_itens($id);
			break;

			default:
			/******************************************/
			$itens = $this->in_status(1);
			$sx .= '<div class="row">';
			$sx .= $this->preparation_menu('Preparo Técnico','','preparation/acquisition',0);

			if ($itens > 0)
			{
				$txt = '<div style="margin-top: 40px;">total de</div>
				<span style="font-size: 500%; font-weight: bold;">'.$itens.'</span>';
				$sx .= $this->preparation_menu('Representação temática',$txt,'preparation/itens/1',1);
			}

			/******************************************/			
			$sta = array(0,5);
			$itens = $this->in_status($sta);			
			if ($itens > 0)
			{
				$txt = '<div style="margin-top: 40px;">total de</div>
				<span style="font-size: 500%; font-weight: bold;">'.$itens.'</span>';
				$sx .= $this->preparation_menu('Processamento físico',$txt,'preparation/itens/0',1);
			}

			$sx .= '</div>';

			break;			
		}
		return($sx);
	}

	function preparation_menu($name,$txt,$link,$tp=0)
	{
		$ig = array('icone_processament_tecnico_256.jpg','');
		$sx = '';
		$sx .= '<div class="col-md-4 col-6 col-sm-5 col-lg-3 col-xl-3 text-center">';
		$sx .= '<a href="'.base_url(PATH.$link).'" style="text-decoration: none;">';
		if (strlen($ig[$tp]) > 0)
		{
			$sx .= '<img src="'.base_url('img/icon/'.$ig[$tp]).'" class="img_menu img-fluid" alt="'.$name.'"  title="'.$name.'">';
		} else {
			$sx .= '<div class="img_menu" style="width: 256px; height: 256px;">';
			$sx .= $txt;
			$sx .= '</div>';			
		}
		$sx .= $name.'';
		$sx .= '</a>';
		$sx .= '</div>';
		return($sx);	
	}

	function preparation_itens($sta)
	{
		$itens = $this->in_status($sta);
		$sx = '';
		$sx .= '<h1>'.$itens.' '.msg('preparation_itens').'</h1>';
		$sx .= '<p>'.msg('preparation_itens_action').'</p>';
		$sx .= $this->tombo_list($sta);

		return($sx);
	}

	function in_status($st)
	{
		if (is_array($st))
		{
			$wh = '';
			for($r=0;$r < count($st);$r++)
			{
				if ($r > 0) { $wh .= ' OR '; }
				$wh .= "(i_status = ".round($st[$r]).")";
			}
		} else {
			$wh = "(i_status = $st) ";
		}

		$sql = "select count(*) as total 
		from find_item 
		where $wh ";		
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$line = $rlt[0];
		return($line['total']);
	}

	function link_book($id,$isbn)
	{
		$sql = "select * from find_manifestation where m_isbn13 = '$isbn' ";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) > 0)
		{
			$line = $rlt[0];
			$manifestation = $line['id_m'];
			$sql = "update find_item 
			set i_manitestation = $manifestation,
			i_status = 1
			where id_i = ".$id;
			$rlt = $this->db->query($sql);
		}
	}

	function tombo_editar($id)
	{		
		$this->load->model("covers");
		$this->load->model("books");		
		$this->load->model("isbn");		
		$this->load->model("languages");		
		$this->load->model("generes");		
		$this->load->model("authors");
		$this->load->model("google_api");		
		$this->load->model("amazon_api");		
		$this->load->model("oclc_api");		
		$this->load->model("marc_api");
		$this->load->model("catalog");

		$dt = $this->le_tombo($id);
		$sx = $this->show($dt,2);
		$isbn = trim($dt['i_identifier']);
		$status = $dt['i_status'];
		$view = 1;

		switch($status)
		{
			case '5':
			$sx .= $this->marc_api->form();
			$marc = get("dd2");
			if (strlen($marc) > 0)
			{
				$sx .= $this->books->marc_import($marc);
			}					
			break;		

			/************************************************/
			case '0':
			$sx .= $this->books->locate($isbn);
			$sx .= $this->link_book($id,$isbn);	
			redirect(base_url(PATH.'preparation/tombo/'.$id));
			exit;

			/************************************************/
			case '1':
			$this->load->model("classifications");
			$sx .= '<h2>Classification</h2>';
			$sx .= $this->classifications->classification_item($id);
			$view = 2;
			break;
		}

		/******************************** Mostra dados **********/
		if (isset($dt['i_manitestation']))
		{
			$dt = $this->books->le_m($dt['i_manitestation']);
			$ss = $sx;
			$sx = $this->books->show($dt,$view);		
			$sx = troca($sx,'$[BODY]',$ss);
		}

		return($sx);
	}

	function acquisition_in()
	{	
		$msgs = '';
		$status = 0;
		$form = new form;
		$tombo = get("dd1");
		$place = get("dd4");
		if (strlen($tombo) == 0)
		{
			$_GET['dd1'] = $this->tombo_next();
			if (isset($_SESSION['place']))
			{
				$_GET['dd4'] = $_SESSION['place'];
			}
		}

		/**************** PLACE SETADO *************/
		if (strlen($place) > 0)
		{
			$_SESSION['place'] = $place;
		}

		/******************* ISBN *****************/
		$isbn = get("dd2");
		$isbn = troca($isbn,'-','');
		$isbn = troca($isbn,'.','');
		$isbn = trim($isbn);

		/************************************** SEM ISBN */
		if (get("dd3") == 1)
		{
			$isbn = LIBRARY.strzero(get("dd1"),8);
			$isbn .= genchksum13($isbn);
			$status = 5;
		}

		/************************************ FORMULARIO VALIDADO */
		if (((strlen($isbn) == 13) or (strlen($isbn) == 10)) and ($place > 0))
		{
			$isbn = $this->isbn->isbns($isbn);
			$isbn_o = $isbn['isbn13'];
			$isbn = sonumero(substr($isbn_o,0,12));
			$isbn_dv = genchksum13($isbn);	
			$isbn_or = substr($isbn_o,12,1);
			$manifestation = 0;

			if ($isbn_dv == $isbn_or)
			{
				/* Verifica se já existe na base */
				$manifestation = $this->books->isbn_exists($isbn_o);
				if ($manifestation > 0)
				{
					$status = 1;
				}
				$msgs = "OK";
				$rs = $this->tombo_insert($tombo,$isbn_o,1,$status,$place,$manifestation);
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
		$sql = "select * from library_place where lp_LIBRARY = ".LIBRARY."";
		array_push($cp,array('$Q id_lp:lp_name:'.$sql,'',msg('library_place'),true,true));
		array_push($cp,array('$M','',$msgs,false,false));
		$sx = $form->editar($cp,'');
		$sx .= $this->tombo_list(array(0,1,5));

		$sx .= '<script> $("#dd2").focus(); </script>'.cr();
		return($sx);

	}

	function tombo_list($status = 0, $limit = 10)
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
		order by id_i desc
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
		$sx .= '<th>Local</th>';
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
			$sx .= '<td>'.$link.$line['lp_name'].'</td>';
			$sx .= '</tr>';
		}
		$sx .= '</table>';
		return($sx);
	}

	function tombo_insert($tombo, $isbn, $tipo, $status=9, $place, $manifestation=0)
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
			(i_tombo,i_manitestation, i_identifier, 
			i_library, i_ip, i_aquisicao, i_status, 
			i_library_place)
			values
			('$tombo',$manifestation, '$isbn', 
			'".LIBRARY."','".ip()."',$tipo,
			$status,$place)";
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

	function show($dt,$tp=1)
	{
		switch($tp)
		{
			case 1:
			$sx = '<div class="row">';
			$sx .= '<div class="col-10">';
			$sx .= '<h2>'.$dt['i_identifier'].'</h2>';
			$sx .= '<div>';
			$sx .= msg('item_status_'.$dt['i_status']);
			$sx .= '</div>';
			$sx .= '</div>';
			$sx .= '<div class="col-2 text-right">';
			$sx .= '<span class="alert alert-info"><sup>TOMBO</sup> '.$dt['i_tombo'].'</span>';
			$sx .= '</div>';
			$sx .= '</div>';
			break;

			case 2:
			$sx = '<div class="row">';
			$sx .= '<div class="col-10">';
			$sx .= '<h2>'.$dt['i_identifier'].'</h2>';
			$sx .= '<div>';
			$sx .= msg('item_status_'.$dt['i_status']);
			$sx .= '</div>';
			$sx .= '</div>';
			$sx .= '<div class="col-2 text-right">';
			$sx .= '<span class="alert alert-info"><sup>TOMBO</sup> '.$dt['i_tombo'].'</span>';
			$sx .= '</div>';
			$sx .= '</div>';			
			break;
		}
		return($sx);			
	}

	function le_tombo($id)
	{
		$sql = "select * from find_item 
		where 	i_library = '".LIBRARY."' and id_i = ".$id;
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