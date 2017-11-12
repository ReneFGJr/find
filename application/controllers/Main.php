<?php
class Main extends CI_controller {
    var $lib = 90000000000;
    
    function __construct() {
        parent::__construct();

        $this -> lang -> load("app", "portuguese");
        $this -> lang -> load("find", "portuguese");
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
        $this -> load -> model('frbr');
        
        $this -> cab();
        $this -> load -> view('welcome');
        $this -> load -> view('find/search_simple', null);
        
        /*************************** find */
        $gets = array_merge($_POST,$_GET);
        $tela = $this->frbr->search($gets);
        $data['content'] = $tela;
        $this->load->view('content',$data);
        $this -> foot();
    }

    public function config($tools = '') {
        $this -> cab();
        $this -> load -> view('welcome');
        $tela = '';
        $tela .= '<ul>';
        $tela .= '<li>';
        $tela .= '<a href="'.base_url('index.php/main/config/msg').'">Compilar Mensagens</a>';
        $tela .= '</li>';
        $tela .= '</ul>';
        
        switch($tools)
            {
                case 'msg':
                    $tela .= msg('MAKE_MESSAGES');
                    break;
            }
        $data['content'] = $tela;
        $this->load->view('content',$data);
        $this -> foot();
    }

    function i($id='',$cl='', $ac='', $term = 0)
        {
            $this -> load -> model('frbr');

            $idm = $this->frbr->rdf_concept($term, $cl);
            $idp = $this->frbr->set_propriety($id, $ac, $idm, $lit = 0);
            /***************************************************/
            
            redirect(base_url('index.php/main/a/'.$id));
        } 

    function a($id = '') {
        $this -> load -> model('frbr');
        $data = $this -> frbr -> le($id);

        $this -> cab();

        $tela = $this -> frbr -> show($id);
        $tela .= '<hr>';
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

    public function ajax2($path = '', $id = '', $type = '') {
        $this -> load -> model('frbr');
        $tela = $path;
        if (strlen($type)) {
            $path = $type;
        }

        switch($path) {
            case 'Agent' :
                $tela .= $this -> frbr -> ajax2($path, $id, $type);
                break;
            case 'FormWork' :
                $tela .= $this -> frbr -> ajax2($path, $id, $type);
                break;
            case 'Date' :
                $tela .= $this -> frbr -> ajax2($path, $id, $type);
                break;
            case 'hasFormWork' :
                $tela .= $this -> frbr -> ajax2($path, $id, $type);
                break;
            case 'hasOrganizator' :
                $tela .= $this -> frbr -> ajax2($path, $id, $type);
                break;
            case 'hasAuthor' :
                $tela .= $this -> frbr -> ajax2($path, $id, $type);
                break;
            default :
                $tela .= $this -> frbr -> ajax2($path, $id, $type);
                $tela .= '
                    <div class="alert alert-danger" role="alert">
                      <strong>Error (545)!</strong> Método não implementado "' . $path . '".
                    </div>
                    ';
                break;
        }
        echo $tela;
    }

    public function ajax3($path = '', $id = '') {
        $this -> load -> model('frbr');
        $val = get("q");
        $this -> frbr -> set_propriety($id, $path, $val, 0);
        echo '<meta http-equiv="refresh" content="0;">';
        return ("");
    }

    public function vocabulary($id = '') {
        $this -> load -> model('frbr');
        $this -> load -> model('vocabularies');
        $this -> cab();

        $dd1 = get("dd1");
        if (strlen($dd1) > 0) {
            $class = $id;
            $id_s = $this -> frbr -> frbr_name($dd1);
            $p_id = $this -> frbr -> rdf_concept($id_s, $class);
            $this -> frbr -> set_propriety($p_id, 'prefLabel', 0, $id_s);
        }

        $tela = $this -> vocabularies -> list_vc($id);
        $tela .= $this -> vocabularies -> modal_vc($id);

        $tela = $data['title'] = '<h3>Classe: ' . msg($id) . '</h3>' . $tela;

        $data['content'] = $tela;
        $this -> load -> view('content', $data);

    }

    public function work_create() {
        $this -> load -> model('frbr');

        $this -> cab();
        $this -> load -> view('find/bibliographic');

        $data = array();
        $data['form'] = $this -> frbr -> data_class('FormWork');

        $this -> load -> view('find/cat_work', $data);

        /* save work */
        if (strlen(get("action")) > 0) {
            $title = trim(get("dd1"));
            $subtitle = trim(get("dd2"));
            $form = trim(get("dd3"));
            if ((strlen($title) > 0) and (strlen($form) > 0)) {
                $idt = $this -> frbr -> work($title, $subtitle, $form);
                redirect(base_url('index.php/main/a/' . $idt));
            }
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
        /***************/
        $tela = '<a href="' . base_url('index.php/main/authority_inport') . '" class="btn btn-secundary">';
        $tela .= 'Importar MARC21';
        $tela .= '</a> ';
        /***************/
        $tela .= '<a href="' . base_url('index.php/main/authority_create') . '" class="btn btn-secundary">';
        $tela .= 'Criar autoridade';
        $tela .= '</a> ';
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
    
    public function catalog_item()
        {
            $this -> load -> model('frbr');
            $this -> load -> model('vocabularies');
            
            $this->cab();
            
            /***************************/
            $dd1 = get("dd1");
            $dd2 = get("dd2");
            $dd3 = get("dd3");
            $dd4 = get("dd4");
            /****************************************************************************/
            if ((strlen($dd1) > 0) and (strlen($dd3) > 0) and (strlen($dd4) > 0))
                {
                    $item = $this->frbr->frbr_name($dd1);
                    $class = 'Item';
                    $p_id = $this->frbr->rdf_concept($item,$class);
                    $this -> frbr -> set_propriety($p_id, 'hasIdRegister', 0, $item);
                    redirect(base_url('index.php/main/a/' . $p_id));
                    exit;
                }
            
            $data['form'] = $this->vocabularies->list_vc_attr(83);
            $data['acqu'] = $this->vocabularies->list_vc_type('TypeOfAcquisition');
            $this->load->view('find/cat_item',$data);
            
        }

    public function label($tp='')
        {
            $lib = $this->lib;
            $acao = get("dd0");
            switch($acao)
                {
                    case '1':
                            $data['dd1'] = round(get("dd1")) + $lib;
                            $data['dd2'] = get("dd2");
                            $data['dd3'] = get("dd3");
                            $data['col'] = get("dd4");
                            //$data['label'] = 'Sala de Leitura Propel';
                            $data['label'] = 'Rene e Viviane';
                            $tela = $this->load->view('find/label/tombro_1',$data,true);                            
                            echo $tela; 
                        exit;
                        break;
                    default:
                        break;
                }
            $this->cab();
            $data['title'] = 'Etiquetagem';
            $this -> load -> view('find/title',$data);
            
            /****************************************************************/
            $tela = '<br><br>';
            $tela .= '<h4>Etiqueta de tombamento</h4>';
            $tela .= '<form method="get">';
            $tela .= '<div class="row">';
            $tela .= '<div class="col-md-2"><span style="font-size: 75%">n. inicial da etiqueta</span><br>';
            $tela .= '  <input name="dd1" value="'.get("dd1").'" class="form-control">';
            $tela .= '</div>';

            $tela .= '<div class="col-md-2"><span style="font-size: 75%">total de páginas</span><br>';
            $tela .= '<input type="hidden" value="1" name="dd0">'.cr();
            $tela .= '<select class="form-control" name="dd2">'.cr();
            $dd2 = get("dd2");
            
            for ($r=1;$r < 10;$r++)
                {
                    $sel = '';
                    if ($r == $dd2) { $sel = 'selected'; }
                    $tela .= '  <option value="'.$r.'" '.$sel.'>'.$r.'</option>'.cr();        
                }
            $tela .= '</select>'.cr();
            $tela .= '</div>';
            
            $tela .= '<div class="col-md-2"><span style="font-size: 75%">n. etiquetas por tombo</span><br>';
            $tela .= '<select class="form-control" name="dd3">'.cr();
            $dd3 = get("dd3");
            
            for ($r=1;$r < 10;$r++)
                {
                    $sel = '';
                    if ($r == $dd3) { $sel = 'selected'; }
                    $tela .= '  <option value="'.$r.'" '.$sel.'>'.$r.'</option>'.cr();        
                }
            $tela .= '</select>'.cr();
            $tela .= '</div>';   
            /*********************************************************/
            $tela .= '<div class="col-md-2"><span style="font-size: 75%">n. etiquetas por linha</span><br>';
            $tela .= '<select class="form-control" name="dd4">'.cr();
            $dd4 = get("dd4");
            if (strlen($dd4) == 0) { $dd4 = '4'; }
            for ($r=1;$r < 10;$r++)
                {
                    $sel = '';
                    if ($r == $dd4) { $sel = 'selected'; }
                    $tela .= '  <option value="'.$r.'" '.$sel.'>'.$r.'</option>'.cr();        
                }
            $tela .= '</select>'.cr();
            $tela .= '</div>';                      

            $tela .= '<div class="col-md-2"><span style="font-size: 75%">total de páginas</span><br>';
            $tela .= '  <input type="submit" name="acao" value="Imprimir" class="btn btn-primary">';
            $tela .= '</div>';
            $tela .= '</div>';
            $tela .= '</form> ';
            
            /****************************************************************/
            $tela .= '<br><br>';
            $tela .= '<h4>Etiqueta lombada</h4>';
            $tela .= '<form method="get">';
            $tela .= '<div class="row">';
            $tela .= '<div class="col-md-2"><span style="font-size: 75%"></span><br>';
            $tela .= '<b>Sem etiquetas</b>';
            $tela .= '</div>';

            $tela .= '<div class="col-md-2"><span style="font-size: 75%">total de páginas</span><br>';
            $tela .= '<select class="form-control" name="dd12">'.cr();
            $dd12 = get("dd12");            
            for ($r=1;$r < 10;$r++)
                {
                    $sel = '';
                    if ($r == $dd12) { $sel = 'selected'; }
                    $tela .= '  <option value="'.$r.'" '.$sel.'>'.$r.'</option>'.cr();        
                }
            $tela .= '</select>'.cr();
            $tela .= '</div>';

            $tela .= '<div class="col-md-2"><span style="font-size: 75%">total de páginas</span><br>';
            $tela .= '  <input type="submit" name="acao" value="Imprimir" class="btn btn-primary">';
            $tela .= '</div>';
            $tela .= '</div>';
            $tela .= '</form> ';            
            
            $data['content'] = $tela;
            $this->load->view('content',$data);                        
        }
    
    public function catalog() {
            $this->cab();
            $data['title'] = 'Preparo técnico';
            $this -> load -> view('find/title',$data);
            
            $tela = '<a href="'.base_url('index.php/main/catalog_item').'" class="btn btn-secondary">';
            $tela .= 'Incorporar novo item';
            $tela .= '</a> ';
            
            $tela .= '<a href="'.base_url('index.php/main/label').'" class="btn btn-secondary">';
            $tela .= 'Gerar Etiquetas';
            $tela .= '</a> ';
            
            $data['content'] = $tela;
            $this->load->view('content',$data);
        }    

    public function authority_create() {
        $this -> load -> model('frbr');

        $this -> cab();
        $data['title'] = 'Autoridade';
        $this -> load -> view('find/title',$data);

        $data = array();
        $data['form'] = $this -> frbr -> data_class('Agent');

        $this -> load -> view('find/cat_agent', $data);

        /* save work */
        if (strlen(get("action")) > 0) {
            $title = trim(get("dd1"));
            $subtitle = trim(get("dd2"));
            $form = trim(get("dd3"));
            if ((strlen($title) > 0) and (strlen($form) > 0)) {
                $id_t = $this -> frbr -> frbr_name($title);
                echo '<br>==>'.$id_t.'--'.$title;
                $p_id = $this -> frbr -> rdf_concept($id_t, $form);
                $this -> frbr -> set_propriety($p_id, 'prefLabel', 0, $id_t);
                redirect(base_url('index.php/main/a/' . $p_id));
            }
        }

        $tela = '';
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
    public function v($id)
        {
            $this->load->model('frbr');
            $this->cab();
            
            $data = $this->frbr->le($id);
            $class = trim($data['c_class']);
            $tela = '';
            switch ($class)
                {
                case 'Person':
                    $tela = $this->frbr->person_show($id);
                    break;
                case 'Work':
                    $tela = $this->frbr->work_show($id);
                    break;
                }
            $data['content'] = $tela;
            $this->load->view('content',$data);
            $this->foot();
        }
}
?>
