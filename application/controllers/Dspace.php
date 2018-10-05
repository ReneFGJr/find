<?php
class Dspace extends CI_controller {

    function __construct() {
        parent::__construct();

        $this -> lang -> load("app", "portuguese");
        $this -> load -> database();
        $this -> load -> helper('form');
        $this -> load -> helper('form_sisdoc');
        $this -> load -> helper('email');
        $this -> load -> helper('url');
        $this -> load -> library('session');

        date_default_timezone_set('America/Sao_Paulo');
        /* Security */
        //      $this -> security();

    }

    function login() {
        $_SESSION['user'] = 'FINDS';
        redirect(base_url('index.php/main'));
    }

    private function cab($navbar = 1) {
        $this -> load -> model("socials");
        $data['title'] = 'DSPace - Catalog ::::';
        $this -> load -> view('header/header', $data);
        if ($navbar == 1) {
            $this -> load -> view('dspace/header/navbar', null);
        }
    }

	function tools_dir_created()
		{
			$this->cab();
			$this -> load -> model('dspaces');
			
			$tela = $this->dspaces->tools_directory_create();	
        	$data['content'] = $tela;
        	$this->load->view('content',$data);
        	$this -> foot();					
		}

    /******************************************************************** LOGIN DO SISTEMA */
    function social($path = '', $d1 = '', $d2 = '') {
        $this -> load -> model('socials');
        $this -> socials -> action($path, $d1, $d2);
    }

    private function foot() {
        $this -> load -> view('dspace/header/footer');
    }

    public function index() {
        $this -> load -> model('frbr');
        $this -> load -> model('dspaces');

        $this -> cab();
        $this -> load -> view('dspace/welcome');
        $tela = '';
        
        $path = $this->dspaces->path;
        $data = $this->dspaces->le("1");
        $tela .= $this->load->view('dspace/sample/view',$data,true);
		
		$tela .= $this->dspaces->tools($path);
		
        $tela .= $this->dspaces->directory($path);        
        $tela .= $this->dspaces->le_content($path);
        $tela .= $this->dspaces->le_handle($path);
        $tela .= $this->dspaces->le_licence($path);
        $tela .= $this->dspaces->le_dublic_core($path);
        
        
        

        $data['content'] = $tela;
        $this->load->view('content',$data);
        $this -> foot();
    }
    function thumbnail($ac,$id)
        {
            $this -> load -> model('dspaces');
            switch($ac)
                {
                case 'created':
                    $this->dspaces->le_content($id);
                    print_r($this->dspaces->files);
                    $this->dspaces->thumbnail_create($id);
                    redirect(base_url('index.php/dspace'));
                    break;
                }
        }
}
?>
