<?php
class Main extends CI_controller {
    function __construct() {
        parent::__construct();

        $this -> lang -> load("login", "portuguese");
        //$this -> lang -> load("skos", "portuguese");
        //$this -> load -> library('form_validation');
        $this -> load -> database();
        $this -> load -> helper('form');
        $this -> load -> helper('form_sisdoc');
        $this -> load -> helper('email');
        $this -> load -> helper('url');
        $this -> load -> library('session');
        date_default_timezone_set('America/Sao_Paulo');
        /* Security */
        //		$this -> security();
    }

    function login() {
        $_SESSION['user'] = 'FINDS';
        redirect(base_url('index.php/main'));
    }

    private function cab($navbar = 1) {
        $data['title'] = 'Find - Library ::::';
        $this -> load -> view('header/header', $data);
        if ($navbar == 1) {
            $this -> load -> view('header/navbar', null);
        }
        $_SESSION['id'] = 1;
    }

    private function foot() {
        $this -> load -> view('header/footer');
    }

    public function index() {
        $this -> cab();
        $this -> load -> view('welcome');
        $this -> load -> view('find/search_simple', null);
        $this -> foot();
    }

    function a($id = '') {
        $this -> load -> model('frbr');
        $data = $this -> frbr -> le($id);

        $this -> cab();

        $tela = $this -> frbr -> show($id);
        $tela .= $this -> frbr -> form($id, $data);

        $data['title'] = '';
        $data['content'] = $tela;

        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function ajax($path = '', $id = '') {
        $this -> load -> model('frbr');
        $tela = $this -> frbr -> ajax($path, $id);
        echo $tela;
    }

    public function ajax2($path = '', $id = '') {
        $this -> load -> model('frbr');
        $tela = $path;
        switch($path) {
            case 'hasOrganizator':
                $tela .= $this->frbr->ajax2($path,$id);
                break;
            case 'hasAuthor' :
                $tela .= $this->frbr->ajax2($path,$id);             
                break;
            default :
                $tela .= '
                    <div class="alert alert-danger" role="alert">
                      <strong>Error!</strong> Método não implementado "' . $path . '".
                    </div>
                    ';
                break;
        }
        echo $tela;
    }

    public function ajax3($path = '', $id = '') {
        $this -> load -> model('frbr');
        $val = get("q");
        $this->frbr->set_propriety($id, $path, $val, 0);
        echo '<meta http-equiv="refresh" content="0;">';
        return("");        
    }
    public function catalog() {
        $this -> load -> model('frbr');

        $this -> cab();
        $this -> load -> view('find/bibliographic');

        $data = array();
        $this -> load -> view('find/cat_work', $data);

        /* save work */
        if (strlen(get("action")) > 0) {
            $title = trim(get("dd1"));
            $subtitle = trim(get("dd2"));
            $idt = $this -> frbr -> work($title, $subtitle);
            redirect(base_url('index.php/main/a/' . $idt));
        }

        $tela = '';
        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);

        $this -> foot();
    }

    public function bibliographic() {
        $this -> load -> model('frbr');

        $this -> cab();
        $this -> load -> view('find/bibliographic');
        $this -> load -> view('find/search_bibliographic', null);
        $tela = '<a href="' . base_url('index.php/main/bibliographic_inport') . '" class="btn btn-secundary">';
        $tela .= 'Importar MARC21';
        $tela .= '</a>';

        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);

        $this -> foot();
    }

    public function bibliographic_inport() {
        $this -> load -> model('agents');
        $this -> load -> model('frbr');

        $this -> cab();
        $this -> load -> view('find/bibliographic');

        $form = new form;
        $cp = array();
        array_push($cp, array('$H8', '', '', False, False));
        array_push($cp, array('$T80:12', '', 'MARC21', True, True));
        array_push($cp, array('$B8', '', 'Import Marc >>>', False, False));
        $tela = $form -> editar($cp, '');

        /********************/
        $txt = get("dd1");
        $marc21 = $this -> agents -> inport_marc21($txt);

        $marc21 = $this -> frbr -> marc_to_frbr($marc21);

        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function authority() {
        $this -> load -> model('frbr');

        $this -> cab();
        $this -> load -> view('find/authority');
        $this -> load -> view('find/search_authority', null);
        $tela = '<a href="' . base_url('index.php/main/authority_inport') . '" class="btn btn-secundary">';
        $tela .= 'Importar MARC21';
        $tela .= '</a>';

        /* recupera */
        $dd1 = get("search");
        if (strlen($dd1) > 0) {
            $tela .= $this -> frbr -> recupera_nomes($dd1);
        }

        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function authority_inport() {
        $this -> load -> model('agents');
        $this -> load -> model('frbr');

        $this -> cab();
        $this -> load -> view('find/authority');

        $form = new form;
        $cp = array();
        array_push($cp, array('$H8', '', '', False, False));
        array_push($cp, array('$T80:12', '', 'MARC21', True, True));
        array_push($cp, array('$B8', '', 'Import Marc >>>', False, False));
        $tela = $form -> editar($cp, '');

        /********************/
        $txt = get("dd1");
        $marc21 = $this -> agents -> inport_marc21($txt);

        $marc21 = $this -> frbr -> marc_to_frbr($marc21);

        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function contact() {
        $this -> load -> model('comgrads');
        $this -> cab();
        $data = array();
        $data['title'] = '';
        $data['content'] = $this -> comgrads -> contact();
        $this -> load -> view('content', $data);
    }

    public function about() {
        $this -> load -> model('comgrads');
        $this -> cab();
        $data = array();
        $data['title'] = '';
        $data['content'] = $this -> comgrads -> about();
        $this -> load -> view('content', $data);
    }

}
?>
