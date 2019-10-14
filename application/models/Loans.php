<?php
class loans extends CI_model {
	var $table = 'users';
	function user($id='',$id2='')
	{
		$sx = msg('not_implemented');
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

	function loan_user($user = 0, $chk = '') {
		$this -> load -> model("users");
		$this -> load -> model("barcodes");
		$data = $this -> users -> le($user);
		if (count($data) > 0) {
			$tela = $this -> load -> view('auth_social/user', $data, true);
		} else {
			redirect(base_url(PATH));
		}

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

	function le_tombo($tombo) {
		if (strlen($tombo) < 8) {
			$tombo = LIBRARY . strzero($tombo, 7);
			$tombo = $tombo;
			$tombo = $tombo . $this -> barcodes -> ean13($tombo);
		}
		$sql = "select * from itens where i_tombo = '$tombo' ";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0)
		{
			$line = $rlt[0];
		} else {
			$line = array();
		}
		return($line);
	}	

	function loan_tombo($tombo, $user) {
		$rdf = new rdf;
		$tela = '';
		$data = $this -> le_tombo($tombo);
		if (isset($data['i_status']))
		{
			switch($data['i_status'])
			{
				case '5':
				$tela = '<div class="alert alert-danger" role="alert">';
				$tela = 'Obra não disponível para empréstimo';
				$tela .= ' =x=> '.$data['i_status'];
				$tela .= '</div>';				
				break;

				case '2':
				$tela = '<div class="alert alert-danger" role="alert">';
				$tela = 'Livro emprestados';
				$tela .= ' =x=> '.$data['i_status'];
				$tela .= '</div>';				
				break;				

				default:
				$manifestation = $data['i_manifestation'];
				$tombo = $data['i_tombo'];
				$tela = $rdf ->  related($manifestation,0);

				$this->loan_historico_add($tombo,$user,2);
				$this->loan_emprestimo($tombo,$user,$data);			
				break;				
			}
			$tela .= $this->loan_historico($tombo,$user);
		} else {
			$tela = 'Número tombo inválido!';
		}
		return ($tela);
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
