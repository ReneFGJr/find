<?php
/*
 define('LIBRARY', '1001');
 define('PATH', 'index.php/main/');
 define('LOGO', 'img/logo_library.png');
 define('LIBRARY_NAME', 'Rede de Leitura');
 define('LIBRARY_LEMA', 'Incentivando a Leitura');
 */
 class Main extends CI_controller {
    var $lib = 10010000000;

    function __construct() {
        parent::__construct();

        $this -> lang -> load("app", "portuguese");
        $this -> lang -> load("find", "portuguese");
        $this -> load -> database();
        $this -> load -> helper('form');
        $this -> load -> helper('form_sisdoc');
        $this -> load -> helper('email');
        $this -> load -> helper('url');
        $this -> load -> library('session');
        $this -> load -> helper('cookie');
        $this -> load -> helper('rdf');

        $this -> load -> model('libraries');

        date_default_timezone_set('America/Sao_Paulo');
        /* Security */
        //      $this -> security();

    }

    function login() {
        $_SESSION['user'] = 'FINDS';
        redirect(base_url('index.php/biblio'));
    }

    private function cab($navbar = 1) {

        $this -> load -> model("socials");
        $data['title'] = LIBRARY_NAME;
        $data['logo'] = LOGO;
        $data['url'] = PATH;
        $this -> load -> view('header/header', $data);
        if ($navbar == 1) {
            $this -> load -> view('header/books_navbar', $data);
        }
    }

    function library($id = '') {        
        $this -> load -> model('libraries');
        $data = array();
        $data['title'] = ':: ' . msg('Libraries');
        $this -> load -> view('header/header', $data);

        if ((strlen($id) > 0) and (round($id) > 0)) {
            $this -> libraries -> select($id);
            redirect(base_url('index.php/main/'));
        }
        
        $sx = '<h1>'.msg('Libraries').'</h1><hr>';
        $data['content'] = $sx . $this -> libraries -> list_libraries($id);
        $this -> load -> view('content', $data);

    }

    function social($act = '') {
        $this -> cab();
        $this->socials->social($act);
    }

    private function foot() {
        $this -> load -> view('header/footer');
    }

    public function index() {
        $this -> cab();
        $rdf = new rdf;

        $data['logo'] = LOGO;
        $this -> load -> view('welcome_brapci', $data);
        $this -> load -> view('find/search/search_simple', $data);

        /*************************** find */
        $gets = array_merge($_POST, $_GET);
        $tela = $rdf -> search($gets);
        //$tela .= $this->frbr->bookcase();

        if (get("action") == '') {
            $tela .= $this -> libraries -> highlights('sc');
        }

        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function v($id) {
        $this -> cab();
        $tela = $this -> libraries -> v($id);
        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
    }    
    function superadmin($id = '', $act = '') {
        $this -> cab();
        $this -> load -> model("superadmin");
        $data['content'] = $this -> superadmin -> index($id, $act);
        $data['title'] = msg('libraries_row');
        $this -> load -> view("show", $data);
        $this -> foot();
    }
    function tools($act='')
    {
        $this -> cab();
        switch($act)
        {
            case 'images':
            $tela = $this->libraries->image_check();
            break;
            default:
            $tela = '<ul>';
            $tela .= '<li><a href="'.base_url(PATH.'library').'">Select other library</a></li>';
            $tela .= '<li><a href="'.base_url(PATH.'tools/images').'">Check Imagens</a></li>';
            $tela .= '<li><a href="'.base_url(PATH.'superadmin').'">SuperAdmin</a></li>';
            $tela .= '</ul>';
            break;
        }
        $data['content'] = $tela;
        $data['title'] = msg('tools');
        $this -> load -> view("show", $data);
        $this -> foot();

    }    
    function mod($mod = '', $act = '', $id = '', $id2 = '', $id3 = '') {
        $this -> cab(1);
        $title = '<sup>mod:</sup>' . UpperCase($mod);

        /*** load module ********/
        $this -> load -> model($mod);
        $cmd = '$tela = $this->' . $mod . '->' . $act . "('$id','$id2','$id3');";
        //echo $cmd;
        eval($cmd);

        $data['content'] = '<h1>' . $title . '</h1>' . $tela;
        $this -> load -> view('content', $data);
    }    
    public function config($tools = '', $ac = '', $id='') {
        $rdf = new rdf;
        $this -> cab();

        if (!perfil("#ADM")) {
            redirect(base_url(PATH));
        }

        $tela = $rdf->config($tools,$ac,$id); 
        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    function about()
    {
        $this -> load -> model('libraries');
        $tela = '';
        $this -> cab();
        $data['content'] = $this -> libraries->about();     
        $data['title'] = msg('About');
        $this -> load -> view('content', $data);

        $this -> foot();        
    }

    function contact()
    {
        $this -> load -> model('libraries');
        $tela = '';
        $this -> cab();
        $data['content'] = $this -> libraries->about();     
        $data['title'] = msg('About');
        $this -> load -> view('content', $data);

        $this -> foot();        
    }    

    function bookshelf($id = '') {
        $this -> load -> model('libraries');
        $tela = '';
        $this -> cab();
        $data['content'] = $this -> libraries->highlights('bookself');     
        $data['title'] = msg('Bookshelf');
        $this -> load -> view('content', $data);

        $this -> foot();
    }
}
?>
