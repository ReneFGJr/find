<?php
class loans extends CI_model {
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
		$tela = $this -> users -> row();
		return ($tela);
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

		$form = new form;
		$cp = array();
		array_push($cp, array('$H8', '', '', False, False));
		array_push($cp, array('$A', '', msg('loan_module'), False, False));
		array_push($cp, array('$S10', '', 'Patrimonio da obra', True, True));
		$tela1 = $form -> editar($cp, '');
		$tela2 = '';
		
		if ($form -> saved > 0) {
			$tombo = get("dd2");
			$tela2 .= $this -> loan_tombo($tombo, $user);
		}
		$tt = $tela;
		$tt .= '<div class="container">';
		$tt .= '<div class="row">';
		$tt .= '<div class="col-10">'.$tela1.'</div>';
		$tt .= '<div class="col-2">'.$tela2.'</div>';
		$tt .= '</div>';
		$tt .= '</div>';
		return ($tt);
	}

	function loan_tombo($tombo, $user) {
		$tela = '';
		$data = $this -> frbr -> le_tombo($tombo);

		if ($data['i_status'] == 3) {
			$manifestation = $data['i_namifestation'];
			$tombo = $data['i_tombo'];
			$tela = $this -> frbr -> related($manifestation,0);
			
			$this->loan_historico($tombo,$user,2);
			$this->loan_emprestimo($tombo,$user,$data);
		} else {
			$tela = 'Obra não disponível para empréstimo';
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
					
			//$rlt = $this->db->query($sql);
		}


}
?>
