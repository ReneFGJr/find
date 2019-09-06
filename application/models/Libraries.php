<?php
class libraries extends CI_model {
    var $table = 'library';
    function __construct() {
        /*
         define('LIBRARY', '1002');
         define('PATH', 'index.php/books/');
         define('LOGO', 'img/logo-brapci_livros_mini.png');
         define('LIBRARY_NAME', 'Brapci Livros');
         define('LIBRARY_LEMA', 'Memória da Ciência da Informação');
         define('BARS', LIBRARY . '0000000');
         *
         */
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
        $form -> row_view = base_url(PATH . 'superadmin/own');
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
