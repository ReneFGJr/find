<?php
/****************** Security login ****************/
function perfil($p, $trava = 0) {
    $ac = 0;
    if (isset($_SESSION['perfil'])) {
        $perf = $_SESSION['perfil'];
        for ($r = 0; $r < strlen($p); $r = $r + 4) {
            $pc = substr($p, $r, 4);
            //echo '<BR>'.$pc.'='.$perf.'=='.$ac;
            if (strpos(' ' . $perf, $pc) > 0) { $ac = 1;
            }
        }
    } else {
        $ac = 0;
    }
    return ($ac);
}

class users extends CI_model {
    var $table = 'users';

    function cp($id) {
        $cp = array();
        array_push($cp, array('$H8', 'id_us', '', False, True));
        array_push($cp, array('$S80', 'us_nome', 'Nome', True, True));
        if ($id == 0) {
            array_push($cp, array('$S80', 'us_email', 'login/email', True, True));
            array_push($cp, array('$P20', '', 'Senha', True, True));
        }
        array_push($cp, array('$O 1:SIM&0:NÃO', 'us_ativo', 'Ativo', True, True));
        return ($cp);
    }

    function cp_sign() {
        $cp = array();
        array_push($cp, array('$H8', 'id_us', '', False, True));
        array_push($cp, array('$S80', 'us_com_nome', 'Nome comercial', True, True));
        array_push($cp, array('$T80:6', 'us_com_ass', 'Assinatura', True, True));
        array_push($cp, array('$B8', '', 'Atualizar', False, True));
        return ($cp);
    }

    function create_admin_user() {
        $dt = array();
        $dt['us_nome'] = 'Super User Admin';
        $dt['us_email'] = 'admin';
        $dt['us_password'] = md5('admin');
        $dt['us_autenticador'] = 'MD5';
        $this -> insert_new_user($dt);
    }

    function row($id = '') {
        $form = new form;

        $form -> fd = array('id_us', 'us_nome', 'us_login', 'us_ativo');
        $form -> lb = array('id', msg('us_name'), msg('us_login'), msg('us_ativo'));
        $form -> mk = array('', 'L', 'L', 'A');

        $form -> tabela = $this -> table;
        $form -> see = true;
        $form -> novo = true;
        $form -> edit = true;

        $form -> row_edit = base_url('index.php/admin/user_edit');
        $form -> row_view = base_url('index.php/admin/user');
        $form -> row = base_url('index.php/admin/users');

        return (row($form, $id));
    }

    function editar($id, $chk) {
        $form = new form;
        $form -> id = $id;
        $cp = $this -> cp($id);
        $data['title'] = '';
        $data['content'] = $form -> editar($cp, $this -> table);
        $this -> load -> view('content', $data);
        return ($form -> saved);
    }

    function insert_new_user($data) {
        $email = $data['us_email'];
        $nome = $data['us_nome'];
        $senha = $data['us_password'];
        $auth = $data['us_autenticador'];

        $sql = "select * from " . $this -> table . " where us_email = '$email' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sql = "insert into " . $this -> table . " 
					(us_nome, us_email, us_password, us_ativo, us_autenticador,
					us_perfil, us_perfil_check)
					values
					('$nome','$email','$senha','1', '$auth',
					'','')
					";
            $this -> db -> query($sql);
            $this -> updatex();
            $this -> update_perfil_check($data);
        }
    }

    function le($id, $fld = 'id') {
        $sql = "select * from " . $this -> table;
        switch($fld) {
            case 'id' :
                $sql .= ' where id_us = ' . round($id);
                break;
            case 'login' :
                $sql .= " where us_email = '$id' ";
                break;
            default :
                $sql .= ' where id_us = ' . round($id);
                break;
        }
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            return ( array());
        } else {
            return ($rlt[0]);
        }
    }

    function user_list($id='')
        {
           $sql = "select * from users
                        LEFT JOIN user_drh on id_us = usd_id_us
                        LEFT JOIN _filiais ON id_fi = usd_empresa
                        WHERE us_ativo = 1
                    ORDER BY fi_nome_fantasia, us_nome  
                    ";
           $rlt = $this->db->query($sql);
           $rlt = $rlt->result_array();
           $sx = '<table width="100%" class="table middle">';
           $xemp = '';
           $nr = 1;
           for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $emp = $line['fi_razao_social'];
                    
                    if ($xemp != $emp)
                        {
                            $nr = 1;
                            $sx .= '<tr><td colspan=5 class="big">'.$emp.'</td></tr>';
                            $xemp = $emp;                            
                        }
                    
                    $sx .= '<tr>';
                    $sx .= '<td align="center">'.$nr.'</td>';
                    $sx .= '<td>'.$line['us_nome'].'</td>';
                    
                    $nr++;
                } 
           $sx .= '</table>';
                   
           return($sx);
        }

    function picture($id = '') {
        $us_badge = strzero($id, 5);
        $pict = 'img/picture/photo-' . $us_badge . '.jpg';
        if (!file_exists($pict)) {
            $picture = base_url('img/picture/img-no-picture.png');
        } else {
            $picture = base_url($pict);
        }
        return ($picture);
    }

    function updatex() {
        $sql = "update " . $this -> table . " set us_badge = lpad(id_us,5,0) where us_badge = '' or us_badge is null ";
        $this -> db -> query($sql);
    }

    function update_perfil_check($data) {
        if (isset($data['us_email'])) {
            $usr = $this -> le($data['us_email'], 'login');
            $id = $usr['id_us'];
            $pass = $usr['us_password'];
            $perfil = $usr['us_perfil'];
            $check = md5($id . $perfil);
            $sql = "update " . $this -> table . " set us_perfil_check = '$check' where id_us = $id ";
            $rlt = $this -> db -> query($sql);
            return ('1');
        }
        if (isset($data['id_us'])) {
            $usr = $this -> le($data['id_us'], 'id');
            $id = $usr['id_us'];
            $pass = $usr['us_password'];
            $perfil = $usr['us_perfil'];
            $check = md5($id . $perfil);
            $sql = "update  " . $this -> table . " set us_perfil_check = '$check' where id_us = $id ";
            $rlt = $this -> db -> query($sql);
            return ('1');
        }
    }

    /****************** Security login ****************/
    function security() {
        $ok = 0;
        if (isset($_SESSION['id'])) {
            $id = round($_SESSION['id']);
            if ($id > 0) {
                return ('');
            }
        }
        redirect(base_url('index.php/social/login'));
    }

    function security_logout() {
        $data = array('id' => '', 'user' => '', 'email' => '', 'image' => '', 'perfil' => '');
        $this -> session -> set_userdata($data);
    }

    function security_login($login = '', $pass = '') {
        $sql = "select * from " . $this -> table . " where us_email = '$login' OR us_login = '$login' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        if (count($rlt) > 0) {
            $line = $rlt[0];

            $dd2 = $this -> password_cripto($pass, $line['us_autenticador']);
            $dd3 = $line['us_password'];

            if ($dd2 == $dd3) {
                /* Salva session */
                $ss_id = $line['id_us'];
                $ss_user = $line['us_nome'];
                $ss_email = $line['us_email'];
                $ss_image = $line['us_image'];
                $ss_perfil = $line['us_perfil'];
                $data = array('id' => $ss_id, 'user' => $ss_user, 'email' => $ss_email, 'image' => $ss_image, 'perfil' => $ss_perfil);
                $this -> session -> set_userdata($data);
                return (1);
            } else {
                return (0);
            }
        }
    }

    function my_account($id) {
        $this -> load -> model('user_drh');

        $data1 = $this -> le($id);
        $data2 = $this -> user_drh -> le($id);
        $data = array_merge($data1, $data2);

        $tela = $this -> load -> view('auth_social/myaccount', $data, true);
        return ($tela);
    }

    function password_cripto($pass, $tipo) {
        switch ($tipo) {
            case 'TXT' :
                $dd2 = trim($pass);
                break;
            default :
                $dd2 = md5($pass);
                break;
        }
        return ($dd2);
    }

    function change_password($id) {
        $this -> load -> model('user_drh');

        $data1 = $this -> le($id);
        $data2 = $this -> user_drh -> le($id);
        $data = array_merge($data1, $data2);

        $tela = $this -> load -> view('auth_social/change_password', $data, true);

        /* REGRAS DE VALIDACAO */
        $data = $this -> le($id);
        $pass = get("dd1");
        $dd3 = $data['us_password'];
        $p1 = get("dd2");
        $p2 = get("dd3");

        $dd2 = $this -> password_cripto($pass, $data['us_autenticador']);
        if (strlen($p1) > 0) {
            if ($dd2 == $dd3) {
                if ($p1 == $p2) {
                    $sql = "update " . $this -> table . " set us_password = '" . md5($p1) . "', us_autenticador = 'MD5' where id_us = " . $id;
                    $this -> db -> query($sql);
                    $data['erro'] = '<div class="alert alert-success" role="alert">
  										<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  										<span class="sr-only">Sucesso:</span>
  										Senhas alterada com sucesso!
									</div>';
                } else {
                    $data['erro'] = '<div class="alert alert-danger" role="alert">
  										<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  										<span class="sr-only">Error:</span>
  										Senhas não conferem!
									</div>';
                }
            } else {
                $data['erro'] = '<div class="alert alert-danger" role="alert">
  										<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  										<span class="sr-only">Error:</span>
  										Senhas atual não confere!
									</div>';
            }
        }

        $tela = $this -> load -> view('auth_social/change_password', $data, true);
        $data['content'] = $tela;
        $tela = $this -> load -> view('auth_social/account', $data, true);
        return ($tela);
    }

    function change_picture($id) {
        $this -> load -> model('user_drh');

        $data1 = $this -> le($id);
        $data2 = $this -> user_drh -> le($id);
        $data = array_merge($data1, $data2);

        $erro = '';
        $msg = '';
        $typ = '$H8';
        if (isset($_FILES['fileToUpload']['type'])) {
            $type = $_FILES['fileToUpload']['type'];
            switch($type) {
                case 'image/jpeg' :
                    $msg = 'Arquivo salvo com sucesso!';
                    $typ = '$SUCESSO';
                    break;
                default :
                    $msg = 'Formato não suportado, deve ser JPG';
                    $msg .= '</br>format: ' . $type;
                    $typ = '$ERRO';
                    break;
            }

            if ($typ == '$SUCESSO') {
                $file = $_FILES['fileToUpload'];
                $name = 'photo-' . strzero($id, 5) . '.jpg';
                $dir = 'img/picture/';
                if (!(file_save_post($dir, $name, $file) == 1))
                    {
                        $msg = 'Erro ao salvar o arquiv';
                        $msg .= '</br>format: ' . $name;
                        $typ = '$ERRO';                        
                    } else {
                        $msg .= '<br>'.$dir.$name;
                    }
            }
        }

        $tela = $this -> load -> view('auth_social/view_picture', $data, true);
        $form = new form;
        $cp = array();
        array_push($cp, array('$SFILE', '', '', false, false));
        array_push($cp, array($typ, '', $msg, false, false));
        array_push($cp, array('$B', '', 'Enviar imagem', false, false));
        $tela .= $form -> editar($cp, '');

        $data['content'] = $tela;
        $tela = $this -> load -> view('auth_social/account', $data, true);

        return ($tela);
    }

    function change_email($id) {
        $this -> load -> model('user_drh');

        $data1 = $this -> le($id);
        $data2 = $this -> user_drh -> le($id);
        $data = array_merge($data1, $data2);

        $cp = array();
        array_push($cp, array('$H8', 'id_us', '', False, False));
        array_push($cp, array('$A1', '', 'Atualizar e-mail', False, True));
        array_push($cp, array('$S80', 'us_email', 'e-mail', True, True));
        array_push($cp, array('$B8', '', 'Atualizar', False, False));
        $form = new form;
        $form -> id = $id;
        $tela = $form -> editar($cp, 'users');
        $data['content'] = $tela;
        return ($tela);
    }

    function change_sign($id) {
        $this -> load -> model('user_drh');

        $data1 = $this -> le($id);
        $data2 = $this -> user_drh -> le($id);
        $data = array_merge($data1, $data2);

        $cp = $this -> cp_sign();
        $form = new form;
        $form -> id = $id;
        $table = $this -> table;
        $tela = $form -> editar($cp, $table);
        if ($form -> saved > 0) {
            $tela .= $this -> load -> view('success', null, true);
        }
        $data['content'] = $tela;
        $tela = $this -> load -> view('auth_social/account', $data, true);
        return ($tela);
    }

    function reset_password($id) {
        $this -> load -> model('user_drh');

        $data1 = $this -> le($id);
        $data2 = $this -> user_drh -> le($id);
        $data = array_merge($data1, $data2);

        $tela = $this -> load -> view('auth_social/change_password', $data, true);

        /* REGRAS DE VALIDACAO */
        $data = $this -> le($id);
        $pass = get("dd1");
        $dd3 = $data['us_password'];
        $p1 = get("dd2");
        $p2 = get("dd3");

        $dd2 = $this -> password_cripto($pass, $data['us_autenticador']);
        if (strlen($p1) > 0) {
            if ($p1 == $p2) {
                $sql = "update " . $this -> table . " set us_password = '" . md5($p1) . "', us_autenticador = 'MD5' where id_us = " . $id;
                $this -> db -> query($sql);
                $data['erro'] = '<div class="alert alert-success" role="alert">
  										<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  										<span class="sr-only">Sucesso:</span>
  										Senhas alterada com sucesso!
									</div>';
            } else {
                $data['erro'] = '<div class="alert alert-danger" role="alert">
  										<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  										<span class="sr-only">Error:</span>
  										Senhas não conferem!
									</div>';
            }
        }

        $tela = $this -> load -> view('auth_social/reset_password', $data, true);
        $data['content'] = $tela;
        $tela = $this -> load -> view('auth_social/account', $data, true);
        return ($tela);
    }

}
?>
