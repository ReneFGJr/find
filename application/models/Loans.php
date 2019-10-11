<?php
class loans extends CI_model {
	var $table = 'users';
	function user($id='',$id2='')
	{
		$sx = msg('not_implemented');
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
		$this -> load -> model("frbr");
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
		ECHO $sql;
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
			if ($data['i_status'] == 3) {
				$manifestation = $data['i_namifestation'];
				$tombo = $data['i_tombo'];
				$tela = $rdf ->  related($manifestation,0);

				$this->loan_historico($tombo,$user,2);
				$this->loan_emprestimo($tombo,$user,$data);
			} else {
				$tela = 'Obra não disponível para empréstimo';
			}
		} else {
			$tela = 'Número tombo inválido!';
		}
		return ($tela);
	}

	function loan_emprestimo($tombo,$user,$data)
	{
		$dias = strtotime("+7 days");
		$dateSrc = date('d/m/Y')+$dias;
		echo date("d/m/Y",$dateSrc);
		exit;

	}
	function loan_historico($tombo,$user,$type)
	{
		print_r($_SESSION);
		$log = $_SESSION['id'];
		$sql = "insert into itens_historico (ih_tombo, ih_type, ih_user, ih_log)
		values
		('$tombo','$type','$user','$log')";
		echo $sql;
	}
}
?>
