<?php
/*
define('LIBRARY', '1001');
define('PATH', 'index.php/main/');
define('LIBRARY_NAME', 'Rede de Leitura');
define('LIBRARY_LEMA', 'Incentivando a Leitura');
*/
define("SYSTEM_ID", 1);
class Main extends CI_controller
{
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
        $this->load->view('content', $data);
    }

    function server($d1='',$d2='',$d3='',$d4='')
    {

    }

    function __construct()
    {
        parent::__construct();

        $this->lang->load("app", "portuguese");
        $this->lang->load("find", "portuguese");
        $this->lang->load("socials", "portuguese");

        $this->load->database();
        $this->load->helper('form');
        $this->load->helper('form_sisdoc');
        $this->load->helper('email');
        $this->load->helper('url');
        $this->load->helper('bootstrap');
        $this->load->library('session');
        $this->load->helper('cookie');
        $this->load->helper('rdf');
        $this->load->helper('email');
        $this->load->helper('socials');
        $this->load->helper("knowland");

        $this->load->model('libraries');

        date_default_timezone_set('America/Sao_Paulo');
        /* Security */
        //      $this -> security();

    }

    function marc($id = 0)
    {
        $this->load->model("marc_api");
        //$this->marc_api->marc_export($id);



        //'1000 - CI'
        //'1001 - PROPEL'
        //'1002 - CASA'
        //'1001 - PROPEL'

        $sql = "
            SELECT * FROM propel.rdf_concept
            INNER JOIN propel.rdf_name on cc_pref_term = id_n
            where cc_library = 1003 and cc_class = 16
            and cc_status >= 0
            limit 1000
            ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();


        $its = '';

        $its .= '### START' . cr();
        $its .= '### TOTAL REGISTERS ' . count($rlt) . cr();

        for ($r = 0; $r < count($rlt); $r++) {
            $ln = $rlt[$r];
            $link = '<a href="' . base_url(PATH . 'marc/' . $ln['id_cc']) . '">';
            $linka = '</a>';
            $its .= '### NEW REGISTER ' . $ln['id_cc'] . cr();
            $its .= $this->marc_api->marc_export($ln['id_cc']);
            //echo $link.$ln['id_cc'].$linka.', ';
        }
        $its .= '### FINISH' . cr();
        echo $its;
        exit;
    }

    function preparation($path = '', $id = '', $sta = '')
    {
        if (perfil("#ADM#CAT#GER")) {
            $this->load->model("books");
            $this->load->model("book_preparations");
            $this->load->model("marc_api");
            $this->load->model("sourcers");
            $this->cab();
            $sx = $this->breadcrumb();
            $sx .= $this->book_preparations->main($path, $id, $sta);
            $data['content'] = $sx;
            $this->load->view('content', $data);
            $this->foot();
        } else {
            redirect(base_url(PATH));
        }
    }

    private function cab($navbar = 1)
    {

        $data['title'] = LIBRARY_NAME;
        $data['url'] = PATH;
        $this->load->view('header/header', $data);
        if ($navbar == 1) {
            $this->load->view('header/books_navbar', $data);
        }
    }

    function ajax($path = '', $id = '')
    {
        switch ($path) {
                /**************** Cover Link *******/
            case 'cover_link':
                $this->load->model('covers');
                $url = get("url");
                $this->covers->ajax_update($id, $url);
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

    function label()
    {
        $this->load->model("labels");
        $url = 'https://ufrgs.br/find/v2/public/index.php/find/labels/show/'.LIBRARY;

        $sx = $this->cab();
        $sx .= '<a href="'.$url.'" target="_new">ETIQUETAS</a>';

        $data['content'] = $sx;
        $this->load->view("content", $data);

    }

    function upload($type = '', $id = '', $tp = '')
    {
        if (perfil("#ADM") > 0) {
            $this->cab(0);
            switch ($type) {
                case 'item':
                    $this->load->model("books_item");
                    $this->books_item->upload_item($id, $tp);
                    break;

                default:
                    echo $type;
                    break;
            }
        }
    }

    function download($id)
        {
            $sql = "select * from find_item
            LEFT JOIN library_place on i_library_place = id_lp
            where i_status <> 9 and id_i = $id";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            $line = $rlt[0];
            $file = $line['i_uri'];
            if (substr($file,0,4) == 'FILE')
                {
                    $file = substr($file,5,strlen($file));
                    $filename = '_repositorio/books/'.$file;

                    header("Content-type:application/pdf");
                    header("Content-Disposition:inline;filename='$file");
                    readfile($filename);
                    exit;
                }
            echo 'ERRO';
        }

    function rdf($path = '', $id = '', $form = '', $idx = 0, $idy = '')
    {
        $link = 'x';
        $rdf = new rdf;
        $sx = $rdf->index($link, $path, $id, $form, $idx, $idy);

        if (strlen($sx) > 0) {
            $data['nocab'] = true;
            $this->cab($data);

            $data['content'] = $sx;
            $this->load->view("content", $data);
        }
    }

    function library($id = '')
    {
        $this->load->model('libraries');
        $data = array();
        $data['title'] = ':: ' . msg('Libraries');
        $this->load->view('header/header', $data);

        if ($id == 'admin') {
            $this->cab();
            $data['content'] = '<div class="container" style="margin-top: 40px;">' . $this->libraries->row($id) . '</div>';
        } else {
            if ((strlen($id) > 0) and (round($id) > 0)) {
                $this->libraries->select($id);
                redirect(base_url('index.php/main/'));
            }
            $sx = '';
            $sx .= '<div class="container">';
            $sx .= '<div class="row">';
            $sx .= '<div class="' . bscol(12) . '">';
            $sx .= '    <div class="big">';
            $sx .= '        <h1 class="text-center">' . msg('Libraries') . '</h1>';
            $sx .= '    </div>';
            $sx .= '    <div class="big">';
            $sx .= '        <p class="text-left">' . msg('Find_welcome') . '</p>';
            $sx .= '    </div>';
            $sx .= '</div>';
            $sx .= $this->libraries->list_libraries($id);
            $sx .= '</div>';
            $sx .= '</div>';
            $data['content'] = $sx;
        }
        $this->load->view('content', $data);
    }

    function social($act = '', $id = '', $chk = '')
    {
        $this->cab();
        $socials = new socials;
        $socials->social($act, $id, $chk);
    }

    private function foot()
    {
        $this->load->view('header/footer');
    }

    public function index()
    {
        $this->cab();

        $this->load->model("books");
        $this->load->model("books_item");
        $this->load->model("covers");
        $rdf = new rdf;

        $data['logo'] = $this->libraries->logo(0, 2);
        $tela = '';
        $tela .= $this->load->view('welcome', $data, true);
        $tela .= $this->load->view('welcome_brapci', $data, true);
        $tela .= $this->load->view('find/search/search_simple', $data, true);

        /*************************** find */
        $gets = array_merge($_POST, $_GET);

        if (count($gets) > 0) {
            $this->load->helper("ai");
            $ai = new ia_index;
            $rst = $ai->search($gets['dd1']);
            $tela .= $this->books->vitrine($rst);
        } else {
            $tela .= $this->books->vitrine();
        }

        $data['content'] = $tela;
        $this->load->view('content', $data);
        $this->foot();
    }

    function reports($act = '', $d1 = '', $d2 = '', $d3 = '')
    {
        $this->cab();

        $this->load->model("books");
        $this->load->model("books_item");
        $this->load->model("covers");
        $this->load->model("reports");
        $rdf = new rdf;

        $data['logo'] = $this->libraries->logo(0, 2);
        $tela = '';
        $tela .= $this->load->view('welcome', $data, true);
        $tela .= $this->reports->index($act, $d1, $d2, $d3);
        $data['content'] = $tela;
        $this->load->view('content', $data);
    }

    function manual()
    {
        $this->cab();
        $data['title'] = msg('Manuais');
        $data['content'] = '<h1>Manuais</h1><p>Em construção</p>';
        $this->load->view('welcome', null);
        $this->load->view('content', $data);
        $this->foot();
    }

    public function item($act = '', $id = 0, $d2 = '')
    {
        $this->load->model("books");
        $this->load->model("books_item");
        $this->cab();
        $tela = $this->load->view('welcome', null, true);
        $tela .= $this->breadcrumb();
        switch ($act) {
            case 'edit':
                if (perfil("#ADM#CAT#BER#BEP")) {
                    $tela .= $this->books_item->tombo_edit($id, $d2);
                }
                break;
            default:
                $tela .= $this->books_item->tombo_view($id, $d2);
        }

        $data['content'] = $tela;
        $this->load->view('content', $data);
        $this->foot();
    }

    function j($id)
    {
        echo "OK";
        exit;
        $this->load->model("books");
        $this->load->model("covers");
        $this->load->model("isbn");
        $this->load->helper("rdf_show");

        $rdf = new rdf;
        $tela = [];
        $tela['c'] = $rdf->le($id);
        $tela['data'] = $rdf->le_data($id);

        echo (json_encode($tela));
    }

    public function v($id)
    {
        $this->load->model("books");
        $this->load->model("covers");
        $this->load->model("isbn");
        $this->load->helper("rdf_show");

        $this->cab();
        $tela = $this->load->view('welcome', null, true);

        $rdf = new rdf;
        $tela .= $rdf->show_data($id);
        /* complemento das informações */
        //$tela .= $rdf->view_data($id);
        $data['content'] = $tela;
        $this->load->view('content', $data);
        $this->foot();
    }

    public function i($id)
    {
        $this->load->model("books");
        $this->cab();
        $tela = $this->books->i($id);
        $data['content'] = $tela;
        $this->load->view('content', $data);
        $this->foot();
    }

    function superadmin($id = '', $act = '')
    {
        $this->cab();
        $this->load->model("superadmin");
        $data['content'] = $this->superadmin->index($id, $act);
        $this->load->view("content", $data);
        $this->foot();
    }

    function admin($path = '', $id = '', $act = '')
    {
        $this->load->model("admin");

        $this->cab();
        $sx = $this->breadcrumb();
        $sx .= $this->admin->index($path, $id, $act);

        $data['content'] = $sx;
        $this->load->view("content", $data);
        $this->foot();
    }

    function tools($act = '')
    {
        $this->cab();
        switch ($act) {
            case 'images':
                $tela = $this->libraries->image_check();
                break;
            default:
                $tela = '<ul>';
                $tela .= '<li><a href="' . base_url(PATH . 'library') . '">Select other library</a></li>';
                $tela .= '<li><a href="' . base_url(PATH . 'tools/images') . '">Check Imagens</a></li>';
                $tela .= '<li><a href="' . base_url(PATH . 'superadmin') . '">SuperAdmin</a></li>';
                $tela .= '</ul>';
                break;
        }
        $data['content'] = $tela;
        $data['title'] = msg('tools');
        $this->load->view("show", $data);
        $this->foot();
    }

    function mod($mod = '', $act = '', $id = '', $id2 = '', $id3 = '')
    {
        $tela = '';
        $this->cab(1);

        /*** load module ********/
        $this->load->model($mod);
        $cmd = '$tela = $this->' . $mod . '->' . $act . "('$id','$id2','$id3');";
        eval($cmd);

        /***** Header Mod */
        $mod_title = $this->$mod->title();

        $tt = $mod_title . $tela;
        $data['content'] = $tt;
        $this->load->view('content', $data);
    }
    public function setup($tools = '', $ac = '', $id = '')
    {
        $this->load->model("setups");
        $this->cab();
        $this->load->view('welcome', null);
        if (!perfil("#ADM")) {
            redirect(base_url(PATH));
        }


        $tela = $this->setups->main($tools, $ac, $id);
        $data['content'] = '<div class="container">' . $tela . '</div>';
        $this->load->view('content', $data);
        $this->foot();
    }

    function about()
    {
        $this->load->model('libraries');
        $this->cab();
        $data['content'] = '<br/><br/><div class="container"><div class="row"><div class="col-md-12">';
        $data['content'] .= $this->libraries->about();
        $data['content'] .= $this->libraries->about_places();
        $data['content'] .= '</div></div></div>';
        $data['title'] = msg('About');
        $this->load->view('content', $data);

        $this->foot();
    }

    function bookshelf($id = '')
    {
        $this->load->model('libraries');
        $tela = '';
        $this->cab();
        $tela .= $this->load->view('welcome', null, true);
        $tela .= $this->libraries->highlights('bookself');
        $data['content'] = $tela;
        $data['title'] = msg('Bookshelf');
        $this->load->view('content', $data);

        $this->foot();
    }

    function catalog($act = '', $id = '')
    {
        $this->load->model("catalog");
        $rdf = new rdf;
        $this->cab();
        $data['content'] = $this->catalog->index($act, $id);

        $data['title'] = msg('Cataloging');
        $this->load->view('content', $data);

        $this->foot();
    }
    function indice($type = '', $lt = '')
    {
        $rdf = new rdf;
        $this->cab();
        switch ($type) {
            case 'author':
                $title = msg('index') . ': ' . msg('index_authority');
                $sx = $rdf->index_author($lt);

                break;
            case 'serie':
                $title = msg('index') . ': ' . msg('index_serie');
                $sx = $rdf->index_other($lt, 'hasSerieName');

                break;
            case 'editor':
                $title = msg('index') . ': ' . msg('index_editor');
                $sx = $rdf->index_other($lt, 'isPublisher');

                break;
            case 'title':
                $title = msg('index') . ': ' . msg('index_title');
                $sx = $rdf->index_work($lt, 'hasTitle');

                break;
        }
        $data['content'] = '<h1>' . $title . '</h1>' . $sx;
        $this->load->view('content', $data);

        $this->foot();
    }
    function m($id = '')
    {
        if (strlen($id) == 0) {
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
        $sx .= $this->books->show($dt, 1);

        $data['content'] = $sx;
        $this->load->view('content', $data);

        $this->foot();
    }

    function config($ac = '', $id = '', $chk = '', $chk2 = '', $chk3 = '')
    {
        /* cab */
        $nocab = get("nocab");
        $data  = array();
        if (strlen($nocab) > 0) {
            $data['nocab'] = True;
        }
        if (count($data) > 0) {
            $this->cab($data);
        } else {
            $this->cab();
        }

        $sx = '<div class="container"><div class="row">';
        switch ($ac) {
            case 'class':
                /* Classes */
                $rdf = new rdf;
                $sx .= $rdf->index($ac, $id, $chk, $chk2, $chk3);
                break;

            case 'forms':
                /* Classes */
                $this->load->model('catalog');
                $sx .= $this->catalog->forms($ac, $id, $chk, $chk2, $chk3);
                break;

            default:
                $rdf = new rdf;
                $sx .= $ac . '?????';
        }
        $sx .= '</div></div>';


        $data['title'] = '';
        $data['content'] = $sx;
        $this->load->view('content', $data);
    }

    function a($id = '')
    {
        $this->load->model("Agents");

        $rdf = new rdf;
        $data = $rdf->le($id);

        $this->cab();
        $this->load->view('welcome');

        $tela = '<div class="container">
    <div class="row">
    <div class="col-md-8">';
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
    <span class="large">' . msg('class') . ': ' . $data['c_class'] . '</span><br>
    <a href="#" onclick="newxy(\'' . base_url(PATH . 'rdf/class_change/' . $id) . '\',800,400);" class="small">' . msg('change') . '</a>
    </div>

    <div class="col-md-4 text-right">';
        if ((perfil("#ADM") > 0)) {
            //$tela .= $linkd . msg('delete') . $linkda . ' ';
        }
        $tela .= $linkc . msg('return') . $linkca;

        $tela .= '</div></div>';
        $tela .= $rdf->form($id, $data);

        switch ($data['c_class']) {
            case 'Person':

                //$tela .= $this->Agents -> show($id);
                break;
            case 'Family':
                $tela .= $this->frbr->show($id);
                break;
            case 'Corporate Body':
                $tela .= $this->frbr->show($id);
                break;
            default:
                break;
        }
        $tela .= '</div>';
        $data['title'] = '';
        $data['content'] = $tela;

        $this->load->view('content', $data);
        $this->foot();
    }

    function a1($id = 0, $act = '')
    {
        $this->cab();
        if (perfil("#ADM")) {
            $rdf = new rdf;
            $data = $rdf->le($id);
            $data['action'] = $act;
            $class = $data['c_class'];
            switch ($class) {
                default:
                    $sx = $rdf->form($id, $data);
                    break;
            }
            $sx = '<div class="container"><div class="row"><div class="col-12">' . $sx . '</div></div></div>';
            $data['content'] = $sx;
            $data['title'] = 'Form';
            $this->load->view('content', $data);
        } else {
            redirect(base_url(PATH));
        }
    }

    function find()
        {
            $this->cab();
            $data = array();
            $sx = $this->load->view('welcome', $data, true);
            $sx .= '<div class="container"><div class="row"><div class="'.bscol(12).'">';
            $sx .= breadcrumb();
            $file = 'find_about.rd';
            if (file_exists($file))
            {
                $txt = file_get_contents($file);
                $sx .= $txt;
            }
            $sx .= '</div></div></div>';
            $data['content'] = $sx;
            $this->load->view('content',$data);
            $this->foot();
        }


    function breadcrumb()
    {
        $sx = '<div class="container">';
        $sx .= breadcrumb();
        $sx .= '</div>';
        return ($sx);
    }

    public function indexes($type='')
    {
        $this->cab();

        $this->load->model("books");
        $this->load->model("books_item");
        $this->load->model("covers");
        $rdf = new rdf;

        $data['logo'] = $this->libraries->logo(0, 2);
        $tela = '';
        $tela .= $this->load->view('welcome', $data, true);
        $tela .= '<div class="container">';
        $tela .= '<div class="row">';
        $tela .= '<div class="'.bscol(12).'">';

        if (strlen($type) == 0)
            {
                    $tp = array('authors','hassubject');
                    $ok=0;
                    $dir = 'application/views/index/'.LIBRARY.'/';
                    $f = scandir($dir);

                    $idx = array();
                    $tela .= '<ul>';
                    for ($r=0;$r < count($f);$r++)
                        {
                            $index = $f[$r];
                            $index = substr($index,0,strpos($index,'_'));
                            if (!isset($idx[$index]))
                                {
                                    if ((strlen($index) > 0) and ($index != 'index'))
                                    {
                                        $idx[$index] = 1;
                                        $link = '<a href="'.base_url(PATH.'indexes/'.$index).'">';
                                        $linka = '</a>';
                                        $tela .= '<li>'.$link.msg($index).$linka.'</li>';
                                        $ok = 1;
                                    }
                                }

                        }
                    $tela .= '</ul>';
                    if ($ok == 0)
                        {
                            $tela .= message('Não foram gerados os índices',3);
                        }
            } else {
                $data = array();
                $tela .= '<a href="'.base_url(PATH.'indexes/'.$type).'">Índice dos '.msg($type).'</a>';
                for ($r=65;$r <= 90;$r++)
                {
                    $file = 'application/views/index/'.LIBRARY.'/'.$type.'_'.chr($r).'.php';
                    if (file_exists($file))
                    {
                    $t = $this->load->view('index/'.LIBRARY.'/'.$type.'_'.chr($r),$data,true);
                    if (strlen($t) > 0)
                        {
                            $tela .= $t.'<hr style="width="50%">';
                        }
                    }
                }
            }
        $tela .= '</div></div></div>';
        $data['content'] = $tela;
        $this->load->view('content', $data);
        $this->foot();
    }

    public function thesa($id = '') {
//        $this -> load -> model('frbr');
        $this -> load -> model('vocabularies');
        $this -> cab();
        $id = round($id);
        $rdf = new rdf;

        $datac = $rdf -> le_class($id);

        $tela = $this -> load -> view('find/view/class', $datac, true);
        $tela .= $this -> vocabularies -> modal_th($id);
        //$tela .= $this -> vocabularies -> list_vc($id);

        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this->foot();
    }

    function ajax_action($ac = '', $id = '') {
        $rdf = new rdf;
        switch($ac) {
            case 'setPrefTerm' :
                $idc = get("q");
                $idt = get("t");
                $sx = $rdf -> changePrefTerm($idc, $idt);
                break;
            case 'exclude' :
                $idc = get("q");
                $rdf -> data_exclude($idc);
                $sx = '<div class="alert alert-success" role="alert">
                                  <strong>Sucesso!</strong> Item excluído da base.
                                </div>';
                $sx .= '<meta http-equiv="refresh" content="1">';
                break;
            case 'inport' :
                $cl = $rdf -> le_class($id);
                if (count($cl) == 0) {
                    echo "Erro de classe";
                    exit ;
                }
                $url = $cl['c_url'];
                $t = read_link($url);
                $rdf -> inport_rdf($t, $id);
                $sql = "update rdf_class set c_url_update = '" . date("Y-m-d") . "' where id_c = " . $cl['id_c'];
                $rlt = $this -> db -> query($sql);
                $sx = '';
                break;
            default :
                $sx = 'Metodo não localizado - ' . $ac;
                break;
        }
        echo $sx;
    }
}