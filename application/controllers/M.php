<?php
/*
define('LIBRARY', '1001');
define('PATH', 'index.php/main/');
define('LIBRARY_NAME', 'Rede de Leitura');
define('LIBRARY_LEMA', 'Incentivando a Leitura');
*/
define("SYSTEM_ID", 1);
class M extends CI_controller
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

    function server($d1 = '', $d2 = '', $d3 = '', $d4 = '')
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

        //$this->load->model('libraries');

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
        $url = 'https://ufrgs.br/find/v2/public/index.php/find/labels/show/' . LIBRARY;

        $sx = $this->cab();
        $sx .= '<a href="' . $url . '" target="_new">ETIQUETAS</a>';

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
        if (substr($file, 0, 4) == 'FILE') {
            $file = substr($file, 5, strlen($file));
            $filename = '_repositorio/books/' . $file;

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




    function j($id)
    {

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


}
