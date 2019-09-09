<?php
class libraries extends CI_model {
    var $table = 'library';
    function __construct() {

        if (!isset($_SESSION['library'])) {
            $ck = $_COOKIE;
            foreach ($ck as $key => $value) {
                echo '<br>'.$key.'='.$value;
                if ($key == 'library') {
                    $id = $value;
                    redirect(base_url('index.php/main/library/' . $id));
                }
            }
            /* Recupera */
            $page = $_SERVER['PATH_INFO'];
            if (strpos($page, '/library')) {
                // PAGE
                define('PATH', 'index.php/main/');
            } else {
                redirect(base_url('index.php/main/library'));
            }
        } else {
            $id = round($_SESSION['library']);
            $data = $this -> le($id);
            define('LIBRARY', $data['l_code']);
            define('PATH', 'index.php/main/');
            define('LOGO', $data['l_logo']);
            define('LIBRARY_NAME', $data['l_name']);
            define('LIBRARY_LEMA', 'Memória da Ciência da Informação');
            define('BARS', LIBRARY . '0000000');
            $s = array('liberary' => $id, 'LIBRARY' => LIBRARY, 'LIBRARY_LOGO' => LOGO, 'LIBRARY_NAME' => LIBRARY_NAME, 'LIBRARY_LEMA' => LIBRARY_LEMA);
            $this -> session -> set_userdata($s);
        }
        return ('');
    }

    function select($id) {
        $data = $this -> le($id);
        $s = array('library' => $id, 'LIBRARY' => LIBRARY, 'LIBRARY_LOGO' => LOGO, 'LIBRARY_NAME' => LIBRARY_NAME, 'LIBRARY_LEMA' => LIBRARY_LEMA);
        $this -> session -> set_userdata($s);
        $unexpired_cookie_exp_time = 2147483647 - time();
        foreach ($s as $key => $value) {
            setcookie($key, $value, $unexpired_cookie_exp_time);
            $this -> input -> set_cookie($key, $value, $unexpired_cookie_exp_time);
        }
        return (1);
    }

    function le($id) {
        $sql = "select * from library where id_l = " . round($id);
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line = $rlt[0];
        return ($line);
    }

    function list_libraries($id = '') {
        $sql = "select * from library order by l_name";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<div class="row">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $link = '<a href="' . base_url(PATH . 'library/' . $line['id_l']) . '" border=0>';
            $sx .= '<div class="col-md-4 col-lg-3 text-center" style="border: 2px solid #00000; margin-bottom: 50px;">';
            $sx .= $link;
            $sx .= '<img src="' . base_url($line['l_logo']) . '" class="img-fluid">';
            $sx .= '<br>';
            $sx .= $line['l_name'];
            $sx .= '</a>';
            $sx .= '</div>';
            $sx .= '<div class="col-md-1 text-center">';
            $sx .= '</div>';
        }
        $sx .= '</div>';
        return ($sx);
    }

    function row($id = '') {
        $form = new form;

        $form -> fd = array('id_l', 'l_name', 'l_code', 'l_id', 'l_logo');
        $form -> lb = array('id_l', msg('l_name'), msg('l_code'), msg('l_id'), msg('l_logo'));
        $form -> mk = array('', 'L', 'L', 'A');

        $form -> tabela = $this -> table;
        $form -> see = True;
        $form -> novo = perfil("#ADMIN");
        $form -> edit = perfil("#ADMIN");

        $form -> row_edit = base_url(PATH . 'superadmin/library_edit');
        $form -> row_view = base_url(PATH . 'library');
        $form -> row = base_url(PATH . 'superadmin/own');

        return (row($form, $id));
    }

    function edit($id = '', $ac = '') {
        $cp = array();
        array_push($cp, array('$H8', 'id_l', '', false, true));
        array_push($cp, array('$S100', 'l_name', msg('library_name'), true, true));
        array_push($cp, array('$S10', 'l_code', msg('library_code'), false, true));
        array_push($cp, array('$H8', 'l_id', msg('library_id'), false, true));
        array_push($cp, array('$S100', 'l_logo', msg('library_logo'), false, true));
        array_push($cp, array('$T80:5', 'l_about', msg('library_about'), false, true));
        $form = new form;
        $form -> id = $ac;
        $sx = $form -> editar($cp, $this -> table);
        return ( array($sx, $form));
    }

}
?>
