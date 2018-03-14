<?php
class Bibliometric extends CI_controller {
	function __construct() {
		parent::__construct();
		$this -> lang -> load("login", "portuguese");
		//$this -> lang -> load("skos", "portuguese");
		//$this -> load -> library('form_validation');
		$this -> load -> database();
		$this -> load -> helper('form');
		$this -> load -> helper('form_sisdoc');
		$this -> load -> helper('url');
		$this -> load -> library('session');
		$this -> load -> library('curl');
		$this -> load -> library('zip');
		$this -> load -> helper('xml');

		date_default_timezone_set('America/Sao_Paulo');
		/* Security */
		//		$this -> security();
	}

	public function cab($navbar = 1) {
	    $this -> load -> model("socials");
		$data['title'] = ':: ReDD - Bibliometric Labs ::';
		$data['pag'] = 2;
		$this -> load -> view('labs/header', $data);       
		if ($navbar == 1) {
			$this -> load -> view('labs/navbar', null);
		}
	}
    
    public function corpus()
        {
        $this->load->model('labs');
        $id = $this->labs->corpus();
        
        $this -> cab(1);
        $data = $this->labs->le($id);
        
        $tela = '<h1>'.$data['a_name'].'</h1>';
        $data['content'] = $tela;
        $this->load->view('content',$data);
                    
        }

	public function index() {
		$this -> cab(0);
        $this->load->view('labs/welcome');
	}
    
    public function main($id='') {
        $this->load->model('labs');
        $this -> cab(1);
        
        $tela = $this->labs->select_corpus();
        $data['content'] = $tela;
        $this->load->view('content',$data); 

    }    

}
?>
