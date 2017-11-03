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
        $this -> load -> view('find/search_simple',null);
        $this -> foot();
    }
    
    public function authority() {
        $this -> cab();
        $this -> load -> view('authority');
        $this -> load -> view('find/search_authority',null);
        $tela = '<a href="'.base_url('index.php/main/authority_inport').'" class="btn btn-secundary">';
        $tela .= 'Importar MARC21';
        $tela .= '</a>';
        $data['content'] = $tela;
        $data['title'] = '';
        $this->load->view('content',$data);
        $this -> foot();
    } 
    
    public function authority_inport() {
        $this -> load -> model('agents');
        $this -> load -> model('frbr');
        
        $this -> cab();     
        $this -> load -> view('authority');
        
        $form = new form;
        $cp = array();
        array_push($cp,array('$H8','','',False,False));
        array_push($cp,array('$T80:12','','MARC21',True,True));
        array_push($cp,array('$B8','','Import Marc >>>',False,False));
        $tela = $form->editar($cp,'');
        
        /********************/
        $txt = get("dd1");
        $marc21 = $this->agents->inport_marc21($txt);
        
        $marc21 = $this->frbr->marc_to_frbr($marc21);        
        
        $data['content'] = $tela;
        $data['title'] = '';
        $this->load->view('content',$data);        
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
