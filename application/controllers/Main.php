<?php
/*
 define('LIBRARY', '1001');
 define('PATH', 'index.php/main/');
 define('LOGO', 'img/logo_library.png');
 define('LIBRARY_NAME', 'Rede de Leitura');
 define('LIBRARY_LEMA', 'Incentivando a Leitura');
 */
 define("SYSTEM_ID", 1);
 class Main extends CI_controller {
    var $lib = 10010000000;

    function import()
    {
        $this->load->model('find_rdf');
        $this->load->model('isbn');
        $this->load->model("covers");
        $this->load->model("books");
        $this->load->model("authors");
        $this->load->model("classifications");
        $this->load->model("subjects");
        $this->load->model("languages");
        $this->load->model("generes");
        $this->load->model("book_preparations");
        $this->load->model("books_item");        
        
        $sx = $this->find_rdf->import('1000');
        $this->cab();
        $data['content'] = $sx;
        $this->load->view('content',$data);
    }

    function zera()
    {
        $this->cab();

        $sql = "TRUNCATE find_authors;
        TRUNCATE find_manifestation_url;
        TRUNCATE rdf_concept;
        TRUNCATE rdf_data;
        TRUNCATE rdf_name;
        TRUNCATE find_item;";
        $ln = splitx(';',$sql);
        $sx = '';
        for ($r=0;$r < count($ln);$r++)
        {
            $sql = $ln[$r];
            $sx .= '<tt>'.$sql.'</tt><br/>';
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
        $this -> load -> helper('socials');

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
        if (perfil("#ADM#CAT#GER"))
        {
        $this->load->model("books");
        $this->load->model("book_preparations");
        $this->load->model("marc_api");
        $this->load->model("sourcers");
        $this->cab();
        $sx = $this->breadcrumb();
        $sx .= $this->book_preparations->main($path,$id,$sta);
        $data['content'] = $sx;
        $this -> load -> view('content', $data);  
        $this->foot();  
        } else {
            redirect(base_url(PATH));
        }
    }

    private function cab($navbar = 1) {

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

    function rdf($path='',$id='',$form='',$idx=0,$idy='')
    {
        $link = 'x';
        $rdf = new rdf;
        $sx = $rdf->index($link,$path,$id,$form,$idx,$idy);

        if (strlen($sx) > 0)
        {
            $data['nocab'] = true;
            $this -> cab($data);

            $data['content'] = $sx;
            $this -> load -> view("content", $data);
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

    function social($act = '',$id='',$chk='') {
        $this -> cab();
        $socials = new socials;
        $socials->social($act,$id,$chk);
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
        $tela .= $this -> load -> view('welcome', $data,true);
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
        $this->load->model("books");
        $this->load->model("covers");
        $this->load->model("isbn");
        $this -> cab();
        $tela = $this -> load -> view('welcome', null,true);

        $rdf = new rdf;
        $tela .= $rdf->show_data($id);
        /* complemento das informações */
        //$tela .= $rdf->view_data($id);
        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
    } 

    public function i($id) {
        $this->load->model("books");
        $this -> cab();
        $tela = $this -> books -> i($id);
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
        $tela .= $this -> load -> view('welcome', null, true);
        $tela .= $this -> libraries->highlights('bookself');     
        $data['content'] = $tela;
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
    function m($id='')
    {
        if (strlen($id) == 0)
        {
            redirect(base_url(PATH));
        }
        $this->load->model("covers");
        $this->load->model("books");
        $this->load->model("isbn");
        $this->load->model("authors");
        $this->load->model("classifications");
        $this->load->model("subjects");
        $dt = $this->books->le_m($id);
        $this->cab();
        $sx = $this->breadcrumb();
        $sx .= $this->books->show($dt,1);

        $data['content'] = $sx;
        $this -> load -> view('content', $data);

        $this->foot();
    }

    function config($ac='',$id='',$chk='',$chk2='',$chk3='') {
        /* cab */
        $nocab = get("nocab");
        $data  = array();
        if (strlen($nocab) > 0) { $data['nocab'] = True; }  
        if (count($data) > 0)
        {
            $this -> cab($data);
        } else {
            $this -> cab();
        }
        
        $sx = '<div class="container"><div class="row">';
        switch($ac)
        {
            case 'class':
            /* Classes */
            $rdf = new rdf;
            $sx .= $rdf->index($ac,$id,$chk,$chk2,$chk3);
            break;                

            default:
            $rdf = new rdf;
            $sx .= $ac.'?????';
        }
        $sx .= '</div></div>';        


        $data['title'] = '';
        $data['content'] = $sx;
        $this->load->view('content',$data);

    } 

    function a($id = '') {
        $rdf = new rdf;
        $data = $rdf -> le($id);

        $this -> cab();
        $this->load->view('welcome');

        $tela = '';
        $linkc = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="middle">';
        $linkca = '</a>';

        if (strlen($data['n_name']) > 0) {
            $tela .= '<h2>' . $linkc . $data['n_name'] . $linkca . '</h2>';
        }
        $linkc = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="btn btn-secondary">';
        $linkca = '</a>';

        $linkd = '<a href="' . base_url(PATH . 'd/' . $id) . '" class="btn btn-danger">';
        $linkda = '</a>';

        $tela .= '
        <div class="container">
        <div class="row">
        <div class="col-md-8">
        <h5>' . msg('class') . ': ' . $data['c_class'] . '</h5>
        </div>

        <div class="col-md-4 text-right">';
        if ((perfil("#ADM") > 0)) {
            $tela .= $linkd . msg('delete') . $linkda . ' ';
        }
        $tela .= $linkc . msg('return') . $linkca;

        $tela .= '</div></div>';
        $tela .= $rdf -> form($id, $data);

        switch($data['c_class']) {
            case 'Person' :
            $tela .= $this -> frbr -> show($id);
            break;
            case 'Family' :
            $tela .= $this -> frbr -> show($id);
            break;
            case 'Corporate Body' :
            $tela .= $this -> frbr -> show($id);
            break;
            default :
            break;
        }
        $tela .= '</div>';
        $data['title'] = '';
        $data['content'] = $tela;

        $this -> load -> view('content', $data);
        $this -> foot();
    }           

    function a1($id=0,$act='')
    {
        $this->cab();
        if (perfil("#ADM"))
        {
            $rdf = new rdf;
            $data = $rdf->le($id);
            $data['action'] = $act;
            $class = $data['c_class'];
            switch($class)
            {
                default:
                $sx = $rdf->form($id,$data);
                break;
            }
            $sx = '<div class="container"><div class="row"><div class="col-12">'.$sx.'</div></div></div>';
            $data['content'] = $sx;
            $data['title'] = 'Form';
            $this->load->view('content',$data);        

        } else {
            redirect(base_url(PATH));
        }
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