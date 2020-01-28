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

    function zera()
    {
        $sql = "TRUNCATE find_authors;
        TRUNCATE find_expression;
        TRUNCATE find_manifestation;
        TRUNCATE find_manifestation_url;
        TRUNCATE find_work;
        TRUNCATE find_work_authors;
        TRUNCATE rdf_concept;
        TRUNCATE rdf_data;
        TRUNCATE rdf_name;
        TRUNCATE find_item;
        TRUNCATE find_classification_item;
        TRUNCATE find_classification;";
        $ln = splitx(';',$sql);
        for ($r=0;$r < count($ln);$r++)
            {
                $sql = $ln[$r];
                echo '<tt>'.$sql.'</tt><br/>';
                $this->db->query($sql);
            }
        
        redirect(base_url(PATH));
    }     

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

    function label()
        {
            $this->load->model("labels");
            $this->labels->label_pdf();
        }

    function preparation($path='',$id='',$sta='')
    {
        $this->load->model("book_preparations");
        $this->cab();
        $sx = $this->breadcrumb();
        $sx .= $this->book_preparations->main($path,$id,$sta);
        $data['content'] = $sx;
        $this -> load -> view('content', $data);        

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

    function ajax($path='',$id='')
    {
        switch($path)
        {
            /**************** Cover Link *******/
            case 'cover_link':
            $this->load->model('covers');
            $url = get("url");
            $this->covers->ajax_update($id,$url);
            break;

            case 'cover_upload':
            $this->load->model('covers');
            $this->covers->ajax_cover_upload($id);
            break;            


            case 'viaf_autocomplete':
            $term = get("term");
            $ak = array('label' => 'Alaska');
            $al = array('label' => 'Alabama');
            $ar = array('label' => 'Arkansas');
            $az = array('label' => $term);

            $arr[0] = $ak;
            $arr[1] = $al;
            $arr[2] = $ar;
            $arr[3] = $az;

            echo json_encode($arr);
            break;

            default:
            $sx = '[{ value: "1463", label: "dealer 5"}, { value: "269", label: "dealer 6" }]';
            echo $sx;
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

        $this->load->model("books");
        $this->load->model("covers");
        $rdf = new rdf;

        $data['logo'] = LOGO;
        $tela = '';
        $tela .= $this->breadcrumb();
        $tela .= $this -> load -> view('welcome_brapci', $data,true);        
        $tela .= $this -> load -> view('find/search/search_simple', $data,true);

        /*************************** find */
        $gets = array_merge($_POST, $_GET);        
        $tela .= $rdf -> search($gets);
        //$tela .= $this->frbr->bookcase();

        $tela .= $this->books->vitrine();

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
        $this -> load -> view("content", $data);
        $this -> foot();
    }

    function admin($path='', $id = '', $act = '') {
        $this -> load -> model("admin");

        $this -> cab();
        $sx = $this->breadcrumb();
        $sx .= $this -> admin -> index($path, $id, $act);
        
        $data['content'] = $sx;
        $this -> load -> view("content", $data);
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
        eval($cmd);

        $data['content'] = '<h1>' . $title . '</h1>' . $tela;
        $this -> load -> view('content', $data);
    }    
    public function setup($tools = '', $ac = '', $id='') {
        $this->load->model("setups");
        $this -> cab();

        if (!perfil("#ADM")) {
            redirect(base_url(PATH));
        }

        $tela = $this->setups->main($tools,$ac,$id); 
        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    function about()
    {
        $this -> load -> model('libraries');
        $this -> cab();
        $data['content'] = '<br/><br/>'.$this -> libraries->about();  
        $data['content'] .= $this -> libraries->about_places();     
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

    function catalog($act='',$id='')
    {
        $this->load->model("catalog");
        $rdf = new rdf;
        $this -> cab();
        $data['content'] = $this->catalog->index($act,$id);
        
        $data['title'] = msg('Cataloging');
        $this -> load -> view('content', $data);

        $this -> foot();




    }
    function indice($type = '', $lt = '') {
        $rdf = new rdf;
        $this -> cab();
        switch ($type) {
            case 'author' :
            $title = msg('index') . ': ' . msg('index_authority');
            $sx = $rdf -> index_author($lt);

            break;
            case 'serie' :
            $title = msg('index') . ': ' . msg('index_serie');
            $sx = $rdf -> index_other($lt, 'hasSerieName');

            break;
            case 'editor' :
            $title = msg('index') . ': ' . msg('index_editor');
            $sx = $rdf -> index_other($lt, 'isPublisher');

            break;
            case 'title' :
            $title = msg('index') . ': ' . msg('index_title');
            $sx = $rdf -> index_work($lt, 'hasTitle');

            break;
        }
        $data['content'] = '<h1>' . $title . '</h1>' . $sx;
        $this -> load -> view('content', $data);

        $this -> foot();
    }    
    function m($id)
    {
        $this->load->model("covers");
        $this->load->model("books");
        $this->load->model("isbn");
        $this->load->model("authors");
        $this->load->model("classifications");
        $dt = $this->books->le_m($id);
        $this->cab();
        $sx = $this->breadcrumb();
        $sx .= $this->books->show($dt,1);

        $data['content'] = $sx;
        $this -> load -> view('content', $data);

        $this->foot();
    }
    function breadcrumb()
        {
            $sx = '<div class="container">';
            $sx .= breadcrumb();
            $sx .= '</div>';
            return($sx);
        }
}
?>