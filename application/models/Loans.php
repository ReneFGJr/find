<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* CodeIgniter RDF Helpers
*
* @package     CodeIgniter
* @subpackage  Loan
* @category    Model
* @author      Rene F. Gabriel Junior <renefgj@gmail.com>
* @link        http://www.sisdoc.com.br/
* @version     v0.21.01.31
*/
class loans extends CI_model {
	var $table = 'users';
	var $tempo = 10;
	function title()
		{
			$title = '<div class="'.bscol(12).'" style="border-bottom: 1px solid #000000";>';
			$title .= '<sup>'.msg('module').'</sup>';
			$title .= ' ';
			$title .= '<span class="big"><b>';
			$title .= msg("LOANS");
			$title .= '</b></span>';
			$title .= '</div>';
			return($title);
		}
	function user_exists($email)
		{
			echo '==>'.$email;
			$sql = "select * from users where us_email = '$email' ";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) > 0)
				{

				} else {
					return(0);
				}
		}
	function user($id='',$id2='')
	{
		$this->load->helper('webcam');
		$webcam = new webcam;
		$socials = new socials;
		$form = new form;
		$form->id = $id;
		$cp = $socials->cp($id);
		$table = 'users';
		$valid = '';
		$msg = '';
		//$cp[6][0] = '$HV';		
		//$cp[6][2] = '1';

		if ($id==0)
			{
				if ($this->user_exists(get("dd2")) != 0)
				{
					$msg = message(msg('user_alreday_insered'),3);
				} else {
					$valid = 1;
				}
			} else {
				$valid = 1;
			}
		array_push($cp,array('$HV','',$valid,True,True));
		array_push($cp,array('$M','',$msg,false,false));

		$img = $socials->user_image($id);
		$sx = $form->editar($cp,$table);
		//echo '=saved=>'.$form->saved.'=='.$valid;

		if ($id > 0)
			{
				$sx .= $webcam->photo('user_'.strzero($id,7),'_repositorio/users');
			}
		if ($form->saved > 0)
			{
				redirect(base_url(PATH.'mod/loans/loan_user/'.$id));
			}
		return($sx);
	}

	function reports($ac='',$id='')
	{
		$sx = '';
		switch ($ac)
		{
			case 'loan1':
				$sx = $this->r_loan_1();
				break;
			default:
			$sx .= '<ul>';
			$sx .= '<li>'.'<a href="'.base_url(PATH.'mod/loans/reports/loan1').'">'.msg('load_report_1').'</a>';
			$sx .= '</ul>';
			break;
		}
		return($sx);
	}

	function r_loan_1()
		{
			$sx = '<h1>'.msg("report_loan_1").'</h1>';
			$sql = "select * from itens where i_status = 2";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();

			$sx = '<table class="table">'.cr();
			$sx .= '<tr>';
			$sx .= '<th width="10%">'.msg('transaction').'</th>';
			$sx .= '<th width="20%">'.msg('date').'</th>';
			$sx .= '<th width="40%">'.msg('user').'</th>';
			$sx .= '<th width="30%">'.msg('operator').'</th>';
			$sx .= '</tr>';

			for ($r=0;$r < count($rlt);$r++)
				{
					$line = $rlt[$r];
					$sx .= '<tr>';
					$sx .= '<td>'.($line['i_tombo']).'</td>';
					$sx .= '<td>'.stodbr($line['i_date_return']).'</td>';
					//$sx .= '<td>'.$line['us_nome_usuario'].'</td>';
					//$sx .= '<td>'.$line['us_nome_operador'].'</td>';
					$sx .= '</tr>';
				}
			$sx .= '</table>'.cr();

			return($sx);
		}

	function books()
	{
		$sx = msg('not_implemented');
		return($sx);
	}

	function index()
	{
		$sx = '<h1>'.msg('loans').'</h1>';
		$sx .= '<ul>';
		$sx .= '<li><a href="'.base_url(PATH.'mod/loans/users').'">'.msg("loan_users").'</a>'.cr();
		$sx .= '</ul>';
		return($sx);
	}

	function renove()
	{
		$sx = 'Renovação';
		return($sx);
	}
	function users() {
		$this -> load -> model("users");
		$tela = $this ->  row();
		return ($tela);
	}

	function loan($id)
	{
		$sx = '';
		$sx .= '<a href="#" class="btn btn-danger">'.msg("Loan").'</a>';
		$sx .= ' ';
		$sx .= '<a href="#" class="btn btn-warning">'.msg("Reserve").'</a>';
		return($sx);
	}

	function row($id = '') {
		if (perfil("#ADM") > 0)
		{
		$form = new form;

		$form -> fd = array('id_us', 'us_nome', 'us_email', 'us_badge', 'us_ativo');
		$form -> lb = array('id', msg('us_name'), msg('us_email'),msg('us_badge'), msg('us_ativo'));
		$form -> mk = array('', 'L', 'L', 'A');

		$form -> tabela = $this -> table;
		$form -> see = True;
		$form -> novo = perfil("#ADM");
		$form -> edit = perfil("#ADM");

		$form -> row_edit = base_url(PATH.'mod/loans/user');
		$form -> row_view = base_url(PATH.'mod/loans/loan_user');
		$form -> row = base_url(PATH.'mod/loans/users');

		return (row($form, $id));
		}
		redirect(base_url(PATH));
	}

	function loan_user($user = 0, $chk = '') {
		$data = array();
		if (perfil("#ADM") > 0)
		{
			$this -> load -> model("users");
			$this -> load -> model("barcodes");
			$socials = new socials;
			$data = $this -> users -> le($user);
		}
		if (count($data) > 0) {
			$tela = $socials->user_tela($data);
			$tela .= $this->user_endereco($user);
			$tela .= $this->user_etnia($user);
			$tela .= $this->emprestimo($user);
		} else {
			redirect(base_url(PATH));
		}
		return($tela);
	}

	function le_tombo($id)
		{
			$sql = "select * from find_item 
						where i_tombo = $id
						and i_library = ".LIBRARY;
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) > 0)
				{
					$line = $rlt[0];
				} else {
					$line = array();
				}
			return($rlt);
		}

	function emprestimo($id)
		{
			$this->load->model("books_item");
			$tombo = get("tombo");
			$msg = '';
			$act = get("action");
			if (strlen($tombo) > 0)
				{
					$dt = $this->le_tombo($tombo);
					if (count($dt) > 0)
						{

						} else {
							$msg = message(msg('Item_no_found'),3);
						}
					print_r($dt);
				}
			$sx = '';
			$sx .= '<div class="'.bscol(6).'" style="border: 1px solid #000000;">';
			$sx .= '<form method="post">';
			$sx .= msg('tombo');
			$sx .= '<input type="text" name="tombo" class="form-control">';
			$sx .= '<input type="submit" name="action" class="btn btn-outline-primary" value="'.msg('loan').'">';
			$sx .= '</form>';
			$sx .= $msg;
			$sx .= '</div>';
			return($sx);
		}

	function le_endereco($id)
		{
			$sql = "select * from users_add where ua_us = ".$id;
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) == 0)
				{
					$sqlx = "insert into users_add (ua_us) values ($id)";
					$rlt = $this->db->query($sqlx);
					sleep(1);
					$rlt = $this->db->query($sql);
					$rlt = $rlt->result_array();
				}
			$line = $rlt[0];
			return($line);
		}
	function user_etnia($id)
		{
			$dt = $this->le_endereco($id);
			$sx = '';
			$sx .= '<div class="'.bscol(4).'">';
			$sx .= '<div class="bold big">'.msg('ETNIA').'</div>';

			$sx .= '<span class="small">'.msg('ua_nasc').'</span>';
			$sx .= '<div class="bold">'.stodbr($dt['ua_nasc']).'&nbsp;</div>';

			$sx .= '<span class="small">'.msg('us_escolaridade').'</span>';
			$sx .= '<div class="bold">'.$this->tab_escolaridade($dt['us_escolaridade'],1);
			if ($dt['us_escolaridade'] > 0)
			{
				$sx .= $this->tab_escolaridade_st($dt['us_escolaridade_st'],1);
			}
			$sx .= '&nbsp;</div>';

			$sx .= '<span class="small">'.msg('us_genero').'</span>';
			$sx .= '<div class="bold">'.$this->tab_genero($dt['us_genero'],1).'&nbsp;</div>';

			$sx .= '<span class="small">'.msg('us_raca').'</span>';
			$sx .= '<div class="bold">'.$this->tab_raca($dt['us_raca'],1).'&nbsp;</div>';

			if (perfil("#ADM"))
			{
				$sx .= '<div>';
				$sx .= '<a href="'.base_url(PATH."mod/loans/user_etnia_ed/".$id).'" class="btn btn-outline-primary">'.msg('btn_etnia_edit').'</a>';
				$sx .= '</div>';
			}

			$sx .= '</div>';

			$sx .= '<div class="'.bscol(4).'" style="border-bottom: 1px solid #000000;">';
			$sx .= '</div>';									
			return($sx);
		}

	function user_etnia_ed($id)
		{
			$sx = '<div class="'.bscol(12).'">';
			$form = new form;
			$form->id = $id;
			$cp = array();
			array_push($cp,array('$H8','ua_us','',false,false));
			array_push($cp,array('$D8','ua_nasc',msg('ua_nasc'),false,TRUE));
			$rw = $this->tab_genero('',2);
			array_push($cp,array('$R '.$rw,'us_genero',msg('us_genero'),false,TRUE));
			$rw = $this->tab_raca('',2);
			array_push($cp,array('$R '.$rw,'us_raca',msg('us_raca'),false,TRUE));
			$rw = $this->tab_escolaridade('',2);
			array_push($cp,array('$R '.$rw,'us_escolaridade',msg('us_escolaridade'),false,TRUE));
			$rw = $this->tab_escolaridade_st('',2);
			array_push($cp,array('$R '.$rw,'us_escolaridade_st',msg('us_escolaridade_st'),false,TRUE));
			$sx .= $form->editar($cp,'users_add');
			$sx .= '</div>';

			if ($form->saved > 0)
				{
					redirect(base_url(PATH.'mod/loans/loan_user/'.$id));
				}

			return($sx);	
		}
	function tab_escolaridade($id='',$t=1)
		{
			$g = array();
			$g[0] = 'Não informado';
			$g[1] = 'sem escolaridade';
			$g[2] = 'Educação Infantil';
			$g[3] = 'Ensino Fundamental (1º ao 5º ano)';
			$g[4] = 'Ensino Fundamental (6º ao 9º ano)';
			$g[6] = 'Ensino Médio';
			$g[7] = 'Ensino Superior';
			$g[8] = 'Pós-graduação Especialização';
			$g[9] = 'Mestrado';
			$g[10] = 'Doutorado';
			return($this->tab($id,$t,$g));
		}

	function tab_escolaridade_st($id='',$t=1)
		{
			$g = array();
			$g[0] = 'Não informado';
			$g[1] = 'cursando';
			$g[2] = 'incompleto';
			$g[3] = 'completo';
			return($this->tab($id,$t,$g));
		}

	function tab_raca($id='',$t=1)
		{
			$g = array();
			$g[0] = 'Não informado';
			$g[1] = 'Preta(o)';
			$g[2] = 'Parda(o)';
			$g[3] = 'Branca(o)';
			$g[4] = 'Amarela(o)';
			$g[5] = 'Indígena';
			return($this->tab($id,$t,$g));
		}

	function tab_genero($id='',$t=1)
		{
			$g = array();
			$g[0] = 'Não informado';
			$g[1] = 'Mulher';
			$g[2] = 'Mulher trans';
			$g[3] = 'Homem';
			$g[4] = 'Homens trans';
			$g[5] = 'Não-binário';
			return($this->tab($id,$t,$g));
		}
	function tab($id,$t,$g)
		{
			switch ($t)
				{
					case '1':
					$id = round($id);
					$g = $g[$id];
					break;

					case '2':
					$gr = $g;
					$g = '';
					foreach($gr as $i => $v)
						{
							if ($g != '') { $g .= '&'; }
							$g .= $i.':'.$v;
						}
					break;					

					default:
					/* Array */
					break;
				}
			return($g);			
		}		

	function user_address($id)
		{
			$sx = '';			
			$socials = new socials;
			$data = $socials -> le($id);
			$sx = $socials->user_tela($data);			

			$cep = get("cep");
			if (strlen($cep) > 0)
				{
					$data = cep($cep);
					$data = (array)json_decode($data);

					if (isset($data['logradouro']))
						{
							$sql = "update users_add set ";
							$sql .= "us_cep = '".$data['cep']."',";
							$sql .= "us_logradouro = '".$data['logradouro']."',";
							$sql .= "us_complemento = '".$data['complemento']."',";
							$sql .= "us_bairro = '".$data['bairro']."',";
							$sql .= "us_localidade = '".$data['localidade']."',";
							$sql .= "us_uf = '".$data['uf']."',";
							$sql .= "us_ibge = '".$data['ibge']."',";
							$sql .= "us_gia = '".$data['ddd']."',";
							$sql .= "us_siafi = '".$data['siafi']."'";
							$sql .= "where ua_us = $id";
							$rlt = $this->db->query($sql);
							redirect(base_url(PATH.'mod/loans/user_address/'.$id));
							exit;
						}

				}

			$sx .= '<div class="'.bscol(12).'">';
			$sx .= '<form method="get">';
			$sx .= msg('input_CEP').': ';
			$sx .= '<input type="text" name="cep">';
			$sx .= '<input type="submit" name="action" value="'.msg('search').'">';
			$sx .= '</form>';
			$sx .= '</div>';

			$sx .= '<div class="'.bscol(12).'">';
			$form = new form;
			$form->id = $id;
			$cp = array();
			array_push($cp,array('$H8','ua_us','',false,false));
			array_push($cp,array('$S10','us_cep',msg('us_cep'),false,TRUE));
			array_push($cp,array('$S100','us_logradouro',msg('us_logradouro'),false,TRUE));
			array_push($cp,array('$S20','ua_number',msg('ua_number'),false,TRUE));
			array_push($cp,array('$S20','us_complemento',msg('us_complemento'),false,TRUE));
			array_push($cp,array('$S50','us_bairro',msg('us_bairro'),false,TRUE));
			array_push($cp,array('$S50','us_localidade',msg('us_localidade'),false,TRUE));
			array_push($cp,array('$S5','us_uf',msg('us_uf'),false,TRUE));
			$sx .= $form->editar($cp,'users_add');
			$sx .= '</div>';

			if ($form->saved > 0)
				{
					redirect(base_url(PATH.'mod/loans/loan_user/'.$id));
				}

			return($sx);
		}		

	function user_endereco($id)
		{
			$dt = $this->le_endereco($id);
			$sx = '';
			$sx .= '<div class="'.bscol(4).'">';
			$sx .= '<div class="bold big">'.msg('ADDRESS').'</div>';

			$sx .= '<span class="small">'.msg('us_address').'</span>';
			$sx .= '<div class="bold">'.$dt['us_logradouro'].'&nbsp;</div>';

			$sx .= '<span class="small">'.msg('us_complemento').'</span>';
			$sx .= '<div class="bold">'.$dt['us_complemento'].'&nbsp;</div>';

			$sx .= '<span class="small">'.msg('us_bairro').'</span>';
			$sx .= '<div class="bold">'.$dt['us_bairro'].'&nbsp;';
			$sx .= ' - <span class="bold">'.trim($dt['us_localidade']).'</span>';
			$sx .= ', <span class="bold">'.$dt['us_uf'].'&nbsp;</span>';
			$sx .= '</div>';

			$sx .= '<span class="small">'.msg('us_cep').'</span>';
			$sx .= '<div class="bold">'.$dt['us_cep'].'&nbsp;</div>';

			if (perfil("#ADM"))
			{
				$sx .= '<div>';
				$sx .= '<a href="'.base_url(PATH."mod/loans/user_address/".$id).'" class="btn btn-outline-primary">'.msg('btn_address_edit').'</a>';
				$sx .= '</div>';
			}
			$sx .= '</div>';
			return($sx);
		}

	function load_book($user=0,$chk='')
		{
		if (strlen(get("dd3")) == 0)
		{
			$data = date("d/m/Y");
			$data = DateTime::createFromFormat('d/m/Y', $data);
			$data->add(new DateInterval('P7D')); 
			$DT = $data->format('d/m/Y');
			$_POST['dd3'] = $DT;
		}
		$form = new form;
		$cp = array();
		array_push($cp, array('$H8', '', '', False, False));
		array_push($cp, array('$A', '', msg('loan_module'), False, False));
		array_push($cp, array('$S50', '', 'Patrimonio da obra', True, True));
		array_push($cp, array('$D8', '', 'Preisão de devolução', True, True));
		$tela1 = $form -> editar($cp, '');
		$tela2 = '';
		if ($form -> saved > 0) {
			$tombo = get("dd2");
			$tela2 .= $this -> loan_tombo($tombo, $user);
		}
		$tt = $tela;
		$tt .= '<style> .form_string { font-size: 250%; width: 300px; } </style>'.cr();
		$tt .= '<div class="container">';
		$tt .= '<div class="row">';
		$tt .= '<div class="col-10">'.$tela1.'</div>';
		$tt .= '<div class="col-12">'.$tela2.'</div>';
		$tt .= '</div>';
		$tt .= '</div>';
		return ($tt);
	}



	function load_email($user)
	{

	}

	function loan_emprestimo($tombo,$user,$data)
	{
		$dias = strtotime("+7 days");
		$dateSrc = date('d/m/Y')+$dias;
		$dater = date("d/m/Y",$dateSrc);

		$sql = "update itens set i_status = 2, i_user = $user, i_date_return = $dater where i_tombo = '".$tombo."'";
		$rlt = $this->db->query($sql);
		return(1);

	}

	function loan_historico($tombo,$user)
		{
			$sx = '<table class="table">'.cr();
			$sx .= '<tr>';
			$sx .= '<th width="10%">'.msg('transaction').'</th>';
			$sx .= '<th width="20%">'.msg('date').'</th>';
			$sx .= '<th width="40%">'.msg('user').'</th>';
			$sx .= '<th width="30%">'.msg('operator').'</th>';
			$sx .= '</tr>';

			$sql = "select logs.us_nome as us_nome_operador, 
						   usr.us_nome as us_nome_usuario, 
							id_ih, ih_created
					from itens_historico 
					inner join users as logs on logs.id_us = ih_log
					inner join users as usr on usr.id_us = ih_user
							where ih_user = ".$user." 
							order by ih_created desc";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			for ($r=0;$r < count($rlt);$r++)
				{
					$line = $rlt[$r];
					$sx .= '<tr>';
					$sx .= '<td>'.($line['id_ih']).'</td>';
					$sx .= '<td>'.stodbr($line['ih_created']).' '.substr($line['ih_created'],11,8).'</td>';
					$sx .= '<td>'.$line['us_nome_usuario'].'</td>';
					$sx .= '<td>'.$line['us_nome_operador'].'</td>';
					$sx .= '</tr>';
				}
			$sx .= '</table>'.cr();
			return($sx);
		}

	function loan_historico_add($tombo,$user,$type)
	{
		print_r($_SESSION);
		$log = $_SESSION['id'];
		$sql = "insert into itens_historico (ih_tombo, ih_type, ih_user, ih_log, ih_operador)
		values
		('$tombo','$type',$user,'$log',$user_id)";
		$this->db->query($sql);
	}
}
?>
