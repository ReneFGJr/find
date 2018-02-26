<?php
class Main extends CI_controller {
    var $lib = 10010000000;

    function __construct() {
        parent::__construct();

        $this -> lang -> load("app", "portuguese");
        $this -> lang -> load("find", "portuguese");
        //$this -> load -> library('tcpdf');
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
        $data['title'] = 'Find - Library ::::';
        $this -> load -> view('header/header', $data);
        if ($navbar == 1) {
            $this -> load -> view('header/navbar', null);
        }
        $_SESSION['id'] = 1;
    }

    /******************************************************************** LOGIN DO SISTEMA */
    function social($path = '', $d1 = '', $d2 = '') {
        $this -> load -> model('socials');
        $this -> socials -> action($path, $d1, $d2);
    }

    private function foot() {
        $this -> load -> view('header/footer');
    }

    public function index() {
        $this -> load -> model('frbr');

        $this -> cab();
        $this -> load -> view('welcome');
        $this -> load -> view('find/search/search_simple', null);

        /*************************** find */
        $gets = array_merge($_POST, $_GET);
        $tela = $this -> frbr -> search($gets);
        //$tela .= $this->frbr->bookcase();
        
        if (get("action") == '')
            {
            $tela .= $this -> frbr -> show_works();
            }

        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function config($tools = '', $ac = '') {
        $this -> load -> model("frbr");

        $this -> cab();
        $this -> load -> view('welcome');
        $tela = '';

        switch($tools) {
            case 'msg' :
                /* acao */
                if (strlen($ac) > 0) {
                    $tela .= msg('MAKE_MESSAGES');
                } else {
                    $tela .= msg_lista();
                }

                break;
            case 'forms' :
                $tela .= msg('FORMS');
                $tela .= $this -> frbr -> form_class();
                break;
            case 'authority' :
                if (perfil("#ADM") == 1) {
                    if ($ac == 'update') {
                        $tela .= $this -> frbr -> viaf_update();
                    } else {
                        $tela .= '<br><a href="' . base_url('index.php/main/config/authority/update') . '" class="btn btn-secondary">' . msg('authority_update') . '</a>';
                    }
                    $tela .= '<br><br><h3>' . msg('Authority') . ' ' . msg('viaf') . '</h3>';
                    $tela .= $this -> frbr -> authority_class();
                }
                break;
        }
        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function pop_config($tools = '', $id = '') {
        $this -> load -> model("frbr");

        $this -> cab(0);
        $tela = '';
        $tela .= $tools;
        switch($tools) {
            case 'msg' :
                $tela .= $this -> frbr -> form_msg_ed($id);
                break;
            case 'forms' :
                $tela .= msg('FORMS');
                $tela .= $this -> frbr -> form_class_ed($id);
                break;
        }
        $data['content'] = $tela;
        $this -> load -> view('content', $data);
    }

    function i($id = '', $cl = '', $ac = '', $term = 0) {
        $this -> load -> model('frbr');

        $idm = $this -> frbr -> rdf_concept($term, $cl);
        $idp = $this -> frbr -> set_propriety($id, $ac, $idm, $lit = 0);
        /***************************************************/

        redirect(base_url('index.php/main/a/' . $id));
    }

    function a($id = '') {
        $this -> load -> model('frbr');
        $data = $this -> frbr -> le($id);

        $this -> cab();

        //$tela = $this -> frbr -> show($id);
        $tela = '';
        $linkc = '<a href="'.base_url('index.php/main/v/'.$id).'" class="middle">';
        $linkca = '</a>';
        
        if (strlen($data['n_name']) > 0)
            {
                $tela .= '<h2>'.$linkc.$data['n_name'].$linkca.'</h2>';
            } 
        $linkc = '<a href="'.base_url('index.php/main/v/'.$id).'" class="btn btn-secondary">';
        $linkca = '</a>';

                $tela .= '
                    <div class="row">
                    <div class="col-md-11">
                    <h5>'.msg('class').': '.$data['c_class'].'</h5>
                    </div>
                    <div class="col-md-1 text-right">
                    '.$linkc.msg('return').$linkca.'
                    </div>
                    </div>';            
        //$tela .= $this -> frbr -> form($id, $data);
        $tela .= $this -> frbr -> form($id, $data);

        $data['title'] = '';
        $data['content'] = $tela;

        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function upload($path = '', $id = '') {
        $this -> load -> model('frbr');
        if (isset($_FILES['fileUpload'])) {
            $ext = strtolower(substr($_FILES['fileUpload']['name'], -4));
            $ext = troca($ext, '.', '');
            $type = '';
            //Pegando extensão do arquivo
            $new_name = date("Ymd-His") . '.' . $ext;
            //Definindo um novo nome para o arquivo
            $dir = 'uploads/';
            //Diretório para uploads
            switch ($ext) {
                case 'jpg' :
                    $type = "Image";
                    $short = 'img_' . $ext . '_';
                    break;
                case 'png' :
                    $type = "Image";
                    $short = 'img_' . $ext . '_';
                    break;
                case 'gif' :
                    $type = "Image";
                    $short = 'img_' . $ext . '_';
                    break;
            }
            $dd1 = get("dd1");
            if (strlen($dd1) == 0) {
                $dd1 = $_FILES['fileUpload']['name'];
                //print_r($_FILES);
                //exit;
            }

            /**********************************************/
            $width = '';
            $height = '';
            if ($type == 'Image') {
                $dm = getimagesize($_FILES['fileUpload']['tmp_name']);
                $dms = $dm[3];
                $dms = troca($dms, ' ', ';');
                $dms = troca($dms, '"', '');
                $dms = troca($dms, 'width', ';');
                $dms = troca($dms, 'height', ';');
                $dms = troca($dms, '=', ';');
                $dms = splitx(';', $dms);
                $width = $dms[0];
                $height = $dms[1];
                $bits = $dm['bits'];
                $chan = $dm['channels'];
            }
            if (($type != '') and ($dd1 != '')) {
                if (!is_dir('_repositorio')) { mkdir('_repositorio');
                }
                if (!is_dir('_repositorio/' . $type)) { mkdir('_repositorio/' . $type);
                }
                $dir = '_repositorio/' . $type . '/';
                $filename = $short . $new_name;
                move_uploaded_file($_FILES['fileUpload']['tmp_name'], $dir . $short . $new_name);

                /****/
                $term = $this -> frbr -> frbr_name($filename);
                $cl = $type;
                $idm = $this -> frbr -> rdf_concept($term, $cl);

                if (strlen($dd1) > 0) {
                    $term = $this -> frbr -> frbr_name($dd1);
                    $ac = 'hasImageDescription';
                    $idp = $this -> frbr -> set_propriety($idm, $ac, 0, $term);
                }
                /* propriedades da imagem */
                $ftype = $_FILES['fileUpload']['type'];
                $term = $this -> frbr -> frbr_name($ftype);
                $cl = 'FileType';
                $idn = $this -> frbr -> rdf_concept($term, $cl);
                $class = 'hasFileType';
                $idp = $this -> frbr -> set_propriety($idm, $class, $idn, 0);

                $checksum = md5_file($dir . $filename);
                $term = $this -> frbr -> frbr_name($checksum);
                $cl = 'Checksum';
                $idn = $this -> frbr -> rdf_concept($term, $cl);
                $class = 'hasImageChecksum';
                $idp = $this -> frbr -> set_propriety($idm, $class, $idn, 0);

                $checksum = filesize($dir . $filename);
                $term = $this -> frbr -> frbr_name($checksum);
                $cl = 'Number';
                $idn = $this -> frbr -> rdf_concept($term, $cl);
                $class = 'hasImageSize';
                $idp = $this -> frbr -> set_propriety($idm, $class, $idn, 0);

                $storage = $dir . $filename;
                $term = $this -> frbr -> frbr_name($storage);
                $cl = 'FileStorage';
                $idn = $this -> frbr -> rdf_concept($term, $cl);
                $class = 'hasFileStorage';
                $idp = $this -> frbr -> set_propriety($idm, $class, $idn, 0);

                if (strlen($width) > 0) {
                    $size = $width;
                    $term = $this -> frbr -> frbr_name($size);
                    $cl = 'Number';
                    $idn = $this -> frbr -> rdf_concept($term, $cl);
                    $class = 'hasImageWidth';
                    $idp = $this -> frbr -> set_propriety($idm, $class, $idn, 0);
                }
                if (strlen($height) > 0) {
                    $size = $height;
                    $term = $this -> frbr -> frbr_name($size);
                    $cl = 'Number';
                    $idn = $this -> frbr -> rdf_concept($term, $cl);
                    $class = 'hasImageHeight';
                    $idp = $this -> frbr -> set_propriety($idm, $class, $idn, 0);
                }

                /************************************************ relaciona objetos ****/
                $class = $path;
                $idp = $this -> frbr -> set_propriety($id, $class, $idm, 0);

                echo "OK";
                redirect(base_url('index.php/main/a/' . $id));
            } else {
                echo "Descrição não realizada";
            }

            //Fazer upload do arquivo
        }
    }

    public function ajax($path = '', $id = '') {
        $this -> load -> model('frbr');
        $tela = '';
        $tela .= $this -> frbr -> ajax($path, $id);
        echo $tela;
    }

    function ajax_action($ac = '', $id = '') {
        $this -> load -> model('frbr');
        switch($ac) {
            case 'setPrefTerm' :
                $idc = get("q");
                $idt = get("t");
                $sx = $this -> frbr -> changePrefTerm($idc, $idt);
                break;
            case 'exclude' :
                $idc = get("q");
                $this -> frbr -> data_exclude($idc);
                $sx = '<div class="alert alert-success" role="alert">
                                  <strong>Sucesso!</strong> Item excluído da base.
                                </div>';
                $sx .= '<meta http-equiv="refresh" content="1">';
                break;
            case 'inport' :
                $cl = $this -> frbr -> le_class($id);
                if (count($cl) == 0) {
                    echo "Erro de classe";
                    exit ;
                }
                $url = $cl['c_url'];
                $t = read_link($url);
                $this -> frbr -> inport_rdf($t, $id);
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

    public function ajax4($path = '', $id = '') {
        $this -> load -> model('barcodes');
        $this -> load -> model('frbr');
        $val = get("q");
        switch ($path) {
            case 'hasPage' :
                $val = sonumero($val);
                if (strlen($val) > 0) {
                    $name_pref = $val . ' p.';
                    $id_t = $this -> frbr -> frbr_name($name_pref);
                    $p_id = $this -> frbr -> rdf_concept($id_t, 'Pages', '');
                    $this -> frbr -> set_propriety($p_id, 'prefLabel', 0, $id_t);
                    $this -> frbr -> set_propriety($id, $path, $p_id, 0);
                    echo '<meta http-equiv="refresh" content="0;">';
                } else {
                    $tela = '
                                <div class="alert alert-danger" role="alert">
                                  <strong>Error! (131)</strong> Numeração inválida "' . get("q") . '"
                                </div>
                                ';
                    echo $tela;  
                }
                break;
            case 'hasISBN' :
                $val = sonumero($val);
                $dv = $this -> barcodes -> isbn13($val);
                if (substr($val, strlen($val), 1) != $dv) {
                    $tela = '
                                <div class="alert alert-danger" role="alert">
                                  <strong>Error! (130)</strong> Número do ISBN inválido "' . $val . '"
                                </div>
                                ';
                    echo $tela;
                } else {
                    $name_pref = $val;
                    $id_t = $this -> frbr -> frbr_name($name_pref);
                    $p_id = $this -> frbr -> rdf_concept($id_t, 'ISBN', '');
                    $this -> frbr -> set_propriety($p_id, 'prefLabel', 0, $id_t);
                    $this -> frbr -> set_propriety($id, 'hasISBN', $p_id, 0);
                    echo '<meta http-equiv="refresh" content="0;">';
                }
                break;
            case 'hasTitleAlternative' :
                $val = trim($val);
                $dv = $this -> barcodes -> isbn13($val);
                if (strlen($val) == 0) {
                    $tela = '
                                <div class="alert alert-danger" role="alert">
                                  <strong>Error! (132)</strong> Título inválido "' . $val . '"
                                </div>
                                ';
                    echo $tela;
                } else {
                    $name_pref = $val;
                    $id_t = $this -> frbr -> frbr_name($name_pref);
                    //$p_id = $this -> frbr -> rdf_concept($id_t, 'ISBN', '');
                    //$this -> frbr -> set_propriety($p_id, 'prefLabel', 0, $id_t);
                    $this -> frbr -> set_propriety($id, 'hasTitleAlternative', 0, $id_t);
                    echo '<meta http-equiv="refresh" content="0;">';
                }
                break;                
        }

        //$this -> frbr -> set_propriety($id, $path, $val, 0);
        //echo '<meta http-equiv="refresh" content="0;">';
        //echo '==>' . $path . '=' . $id . '=' . $val;
        return ("");
    }

    function vocabulary_ed($id = '') {
        $this -> cab();
        $cp = array();
        array_push($cp, array('$H8', 'id_c', '', false, true));
        array_push($cp, array('$S100', 'c_class', 'Classe', true, true));
        array_push($cp, array('$O : &C:Classe&P:Propriety', 'c_type', 'Tipo', true, true));
        array_push($cp, array('$O 1:SIM&0:NÃO', 'c_find', 'Busca', true, true));
        array_push($cp, array('$O 1:SIM&0:NÃO', 'c_vc', 'Vocabulário Controlado', true, true));
        array_push($cp, array('$S100', 'c_url', 'URL', false, true));
        array_push($cp, array('$B8', '', 'Gravar', false, true));
        $form = new form;
        $form -> id = $id;
        $tela = $form -> editar($cp, 'rdf_class');
        if ($form -> saved > 0) {
            redirect(base_url('index.php/main/vocabulary'));
        }

        $data['content'] = '<h1>Classes e Propriedades</h1>' . $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
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

        $t1 = $this -> vocabularies -> list_vc($id);
        $t1 .= $this -> vocabularies -> modal_vc($id);
        $t1 = '<h3>Classe: ' . msg($id) . '</h3>' . $t1;

        $t2 = $this -> vocabularies -> list_thesa($id);
        $t2 = '<h3>Classe Thesa: ' . msg($id) . '</h3>' . $t2;

        $tela = '
                <div class="row">
                    <div class="col-md-6">' . $t1 . '</div>
                    <div class="col-md-6">' . $t2 . '</div>
                </div>';

        $data['content'] = $tela;
        $this -> load -> view('content', $data);

    }

    public function thesa($id = '') {
        $this -> load -> model('frbr');
        $this -> load -> model('vocabularies');
        $this -> cab();

        $datac = $this -> frbr -> le_class($id);

        $tela = $this -> load -> view('find/view/class', $datac, true);
        $tela .= $this -> vocabularies -> list_vc($id);
        $tela .= $this -> vocabularies -> modal_th($id);

        $data['content'] = $tela;
        $this -> load -> view('content', $data);

    }

    public function work_create() {
        $this -> load -> model('frbr');

        $this -> cab();
        $data = array();
        $data['form'] = $this -> frbr -> data_classes('FormWork');

        $this -> load -> view('find/form/cat_work', $data);

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

    public function expression_create($id = '') {
        $this -> load -> model('frbr');

        $this -> cab();

        /****************************************************** WORK *******/
        $tela = '';
        $tela .= $this -> frbr -> work_show($id);

        $data = array();
        $data['form'] = $this -> frbr -> data_classes('FormWork');
        $data['linguage'] = $this -> frbr -> data_classes('Linguage');

        $tela .= $this -> load -> view('find/form/cat_expression', $data, true);

        /* save work */
        if (strlen(get("action")) > 0) {
            $linguage = trim(get("dd3"));
            $form = trim(get("dd2"));
            if ((strlen($form) > 0) and (strlen($id) > 0)) {
                /* cria conceito */
                $class = "Expression";
                $idc = $this -> frbr -> rdf_concept(0, $class);
                $prop = 'isRealizedThrough';

                /* associa expressao ao trabalho */
                $this -> frbr -> set_propriety($id, $prop, $idc, 0);

                /* nome da expressão */
                $prop = 'hasLanguageExpression';
                $this -> frbr -> set_propriety($idc, $prop, $linguage, 0);

                /* nome da expressão */
                $prop = 'hasFormExpression';
                $this -> frbr -> set_propriety($idc, $prop, $form, 0);

                redirect(base_url('index.php/main/v/' . $id));
            }
        }

        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);

        $this -> foot();
    }

    public function manifestation_create($id = '') {
        $this -> load -> model('frbr');

        $this -> cab();

        /****************************************************** WORK *******/
        $tela = '';
        $tela .= $this -> frbr -> work_show($id);

        $data = array();
        $data['form'] = $this -> frbr -> data_expression($id);
        $tela .= $this -> load -> view('find/form/cat_manifestation', $data, true);

        /* save work */

        if (strlen(get("action")) > 0) {
            $expression = trim(get("dd2"));

            if ((strlen($expression) > 0) and (strlen($id) > 0)) {
                /* cria manifestacao */
                $class = "Manifestation";
                $idc = $this -> frbr -> rdf_concept(0, $class);
                $prop = 'isEmbodiedIn';

                /* associa expressao ao trabalho */
                $this -> frbr -> set_propriety($expression, $prop, $idc, 0);

                redirect(base_url('index.php/main/v/' . $id));
            }
        }

        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);

        $this -> foot();
    }

    public function item_create($idt = '',$idw='') {
        $this -> load -> model('barcodes');
        $this -> load -> model('frbr');

        $this -> cab();
        $data = array();
        $data['form'] = $this -> frbr -> data_classes('Library');
        $data['bookcase'] = $this -> frbr -> data_classes('Bookcase');
        $data['acqu'] = $this -> frbr -> data_classes('TypeOfAcquisition');
        $data['idt'] = $idt;
		$data['idw'] = $idw;
		
        $this -> load -> view('find/form/cat_item', $data);

        /* save work */
        if (strlen(get("action")) > 0) {
            $tombo = trim(get("dd1"));
            $biblioteca = trim(get("dd2"));
            $bookcase = trim(get("dd3"));
            $aquisicao = trim(get("dd4"));
            if ((strlen($tombo) > 0) and (strlen($tombo) > 0) and (strlen($bookcase) > 0)) {
                $idt = $this -> frbr -> item_add($idt, $tombo, $biblioteca, $bookcase, $aquisicao);
                redirect(base_url('index.php/main/v/' . $idw));
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
        $this -> load -> view('find/search/search_bibliographic', null);
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
        $this -> load -> view('find/search/search_authority', null);
        /***************/
        $tela = '<br>';
        $tela .= '<div class="row">' . cr();
        $tela .= '  <div class="col-md-2">';
        msg('find_viaf') . '</div>' . cr();
        $tela .= '      <a href="' . base_url('index.php/main/authority_inport') . '" class="btn btn-secondary">';
        $tela .= '      Importar MARC21';
        $tela .= '      </a> ';
        $tela .= '  </div>' . cr();
        /***************/
        $tela .= '  <div class="col-md-2">';
        msg('find_viaf') . '</div>' . cr();
        $tela .= '      <a href="' . base_url('index.php/main/authority_create') . '" class="btn btn-secondary">' . cr();
        $tela .= '      Criar autoridade' . cr();
        $tela .= '      </a> ' . cr();
        $tela .= '  </div>' . cr();
        $tela .= '</div>' . cr();

        /***************/
        $tela .= '<div class="row" style="margin-top: 30px;">' . cr();
        $tela .= '      <div class="col-md-2">';
        $tela .= '          <a href="https://viaf.org/" target="_new_viaf_' . date("dhs") . '" class="btn btn-secondary">
                            <img src="' . base_url('img/logo/logo_viaf.jpg') . '" class="img-fluid"></a>' . cr();
        $tela .= '      </div>' . cr();
        $tela .= '      <div class="col-md-10">' . cr();
        $tela .= msg('find_viaf');
        $tela .= '          <form method="post" action="' . base_url("index.php/main/authority/") . '">' . cr();
        $tela .= '          ' . cr();
        $tela .= '          <div class="input-group">
                              <input type="text" name="ulr_viaf" value="" class="form-control">
                              <input type="hidden" name="action" value="viaf_inport">
                              <span class="input-group-btn">
                                <input type="submit" name="acao"  class="btn btn-danger" value="' . msg('inport') . '">
                              </span>
                              
                            </div>';
        $tela .= '          </form>' . cr();
        $tela .= '          <span class="small">Ex: https://viaf.org/viaf/122976/#Souza,_Herbert_de</span>';
        $tela .= '      </div>' . cr();
        $tela .= '  </div>' . cr();

        //https:
        //viaf.org/viaf/170358043/#Silva,_Rubens_Ribeiro_Gonçalves_da
        /* recupera */
        $dd1 = get("search");
        if (strlen($dd1) > 0) {
            $tela .= $this -> frbr -> recupera_nomes($dd1);
        }

        /***********************************/
        $tela .= '<div class="row" style="margin-top: 30px;">' . cr();
        $tela .= '      <div class="col-md-2">';
        $tela .= '          <a href="http://www.geonames.org/" target="_new_geonames_' . date("dhs") . '" class="btn btn-secondary">
                            <img src="' . base_url('img/logo/logo_geonames.jpg') . '" class="img-fluid"></a>' . cr();
        $tela .= '      </div>' . cr();

        $tela .= '      <div class="col-md-10">' . cr();
        $tela .= msg('find_geonames');
        $tela .= '          <form method="post" action="' . base_url("index.php/main/authority/") . '">' . cr();
        $tela .= '          ' . cr();
        $tela .= '          <div class="input-group">
                              <input type="text" name="ulr_geonames" value="" class="form-control">
                              <input type="hidden" name="action" value="geonames_inport">
                              <span class="input-group-btn">
                                <input type="submit" name="acao"  class="btn btn-danger" value="' . msg('inport') . '">
                              </span>
                              
                            </div>';
        $tela .= '          </form>' . cr();
        $tela .= '          <span class="small">Ex: http://www.geonames.org/3448439/sao-paulo.html</span>';
        $tela .= '      </div>' . cr();
        $tela .= '  </div>' . cr();
        $tela .= '</div>' . cr();
        /// http://www.geonames.org/3448439/sao-paulo.html
        /// http://sws.geonames.org/3448439/about.rdf
        /* recupera */
        $dd1 = get("search");
        if (strlen($dd1) > 0) {
            //$tela .= $this -> frbr -> recupera_geonames($dd1);
        }

        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);

        /***************** inport VIAF ***********/
        $acao = get("action");
        switch ($acao) {
            /***************** inport VIAF ***********/
            case 'viaf_inport' :
                $url = get("ulr_viaf");
                $data['content'] = $this -> frbr -> viaf_inport($url);
                $this -> load -> view('content', $data);
                break;
            /***************** inport GEONames ***********/
            case 'geonames_inport' :
                $url = get("ulr_geonames");
                $data['content'] = $this -> frbr -> geonames_inport($url);
                $this -> load -> view('content', $data);
                break;
            default :
                echo $acao;
        }

        $this -> foot();
    }

    public function catalog_item() {
        $this -> load -> model('frbr');
        $this -> load -> model('vocabularies');

        $this -> cab();
        $data['content'] = $this -> frbr -> item_catalog();
        $this -> load -> view('content', $data);
        $this -> foot();

    }

    public function catalog_work() {
        $this -> load -> model('frbr');
        $this -> load -> model('vocabularies');

        $this -> cab();
        $data['content'] = $this -> frbr -> work_catalog();
        $this -> load -> view('content', $data);
        $this -> foot();

    }

    public function label_pdf($tp = '') {
        $this -> load -> library('tcpdf');
        $this -> load -> model('barcodes');
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf -> SetCreator(PDF_CREATOR);
        $pdf -> SetAuthor('Find');
        $pdf -> SetTitle('Label EAN13 - Find');
        $pdf -> SetSubject('Label');
        $pdf -> SetKeywords('Label');

        // set font
        $pdf -> SetFont('helvetica', '', 11);

        // add a page
        $pdf -> AddPage();
        // -----------------------------------------------------------------------------

        $pdf -> SetFont('helvetica', '', 10);

        // define barcode style
        $style = array('position' => '', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => true, 'hpadding' => 'auto', 'vpadding' => 'auto', 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
        'text' => true, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4);
        $nr = 1;
        if (strlen($tp) > 0) {
            $nr = round($tp);
        }
        if (get("dd21")) {
            $nr = round(get("dd21"));
        }
        $lib = $this -> lib;
        for ($r = 0; $r < 5; $r++) {
            for ($q = 0; $q < 4; $q++) {
                $y = $r * 55 + 15;
                $x = $q * 50 + 10;
                $pdf -> SetXY($x, $y);

                $nrz = strzero(round($nr) + $lib, 11);
                $nrz = $nrz . $this -> barcodes -> ean13($nrz);

                $pdf -> Image('img/logo_library.jpg', '', '', '45', '', 'JPG', '', '');

                $pdf -> SetXY($x, $y + 18);
                $pdf -> write1DBarcode($nrz, 'EAN13', '', '', '', 18, 0.4, $style, 'N');

                $pdf -> SetXY($x - 10, $y + 36);
                $pdf -> Cell(48, 0, 'Nr. tombo:' . round($nr), 0, 0, 'C', 0, '', 0);

                $nr = $nr + 1;
            }
        }
        // EAN 13
        $pdf -> Ln();

        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf -> Output('example_027.pdf', 'I');
    }

    public function label($tp = '') {
        $lib = $this -> lib;
        $this -> load -> model('barcodes');
        $acao = get("dd0");
        switch($acao) {
            case '1' :
                $data['nrtombo'] = round(get("dd1")) + $lib;
                $data['pages'] = get("dd2");
                $data['repetir'] = get("dd3");
                $data['cols'] = get("dd4");
                //$data['label'] = 'Sala de Leitura Propel';
                $data['label'] = 'Propel - IFRGS';
                $tela = $this -> load -> view('find/label/tombo_1', $data, true);

                $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $obj_pdf -> SetCreator(PDF_CREATOR);
                $title = "Etiqueta Bibliográfica";
                $obj_pdf -> SetTitle('Bibliográfica');
                //  $obj_pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, "Monthly Report");
                //  $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                //  $obj_pdf -> setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                //  $obj_pdf -> SetDefaultMonospacedFont('helvetica');
                //  $obj_pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                //  $obj_pdf -> SetFooterMargin(PDF_MARGIN_FOOTER);
                //  $obj_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                //$obj_pdf -> SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                $obj_pdf -> SetFont('helvetica', '', 9);
                $obj_pdf -> setFontSubsetting(false);
                $obj_pdf -> AddPage();
                $obj_pdf -> writeHTML($tela, true, false, true, false, '');
                $obj_pdf -> Output();
                exit ;
                break;
            default :
                break;
        }
        $this -> cab();
        $data['title'] = 'Etiquetagem';
        $this -> load -> view('find/title', $data);

        /****************************************************************/
        $tela = '<br><br>';
        $tela .= '<h4>Etiqueta de tombamento</h4>';
        $tela .= '<form method="get">';
        $tela .= '<div class="row">';
        $tela .= '<div class="col-md-2"><span style="font-size: 75%">n. inicial da etiqueta</span><br>';
        $tela .= '  <input name="dd1" value="' . get("dd1") . '" class="form-control">';
        $tela .= '</div>';

        $tela .= '<div class="col-md-2"><span style="font-size: 75%">total de páginas</span><br>';
        $tela .= '<input type="hidden" value="1" name="dd0">' . cr();
        $tela .= '<select class="form-control" name="dd2">' . cr();
        $dd2 = get("dd2");

        for ($r = 1; $r < 10; $r++) {
            $sel = '';
            if ($r == $dd2) { $sel = 'selected';
            }
            $tela .= '  <option value="' . $r . '" ' . $sel . '>' . $r . '</option>' . cr();
        }
        $tela .= '</select>' . cr();
        $tela .= '</div>';

        $tela .= '<div class="col-md-2"><span style="font-size: 75%">n. etiquetas por tombo</span><br>';
        $tela .= '<select class="form-control" name="dd3">' . cr();
        $dd3 = get("dd3");

        for ($r = 1; $r < 10; $r++) {
            $sel = '';
            if ($r == $dd3) { $sel = 'selected';
            }
            $tela .= '  <option value="' . $r . '" ' . $sel . '>' . $r . '</option>' . cr();
        }
        $tela .= '</select>' . cr();
        $tela .= '</div>';
        /*********************************************************/
        $tela .= '<div class="col-md-2"><span style="font-size: 75%">n. etiquetas por linha</span><br>';
        $tela .= '<select class="form-control" name="dd4">' . cr();
        $dd4 = get("dd4");
        if (strlen($dd4) == 0) { $dd4 = '4';
        }
        for ($r = 1; $r < 10; $r++) {
            $sel = '';
            if ($r == $dd4) { $sel = 'selected';
            }
            $tela .= '  <option value="' . $r . '" ' . $sel . '>' . $r . '</option>' . cr();
        }
        $tela .= '</select>' . cr();
        $tela .= '</div>';

        $tela .= '<div class="col-md-2"><span style="font-size: 75%">total de páginas</span><br>';
        $tela .= '  <input type="submit" name="acao" value="Imprimir" class="btn btn-primary">';
        $tela .= '</div>';
        $tela .= '</div>';
        $tela .= '</form> ';

        /****************************************************************/
        $tela .= '<br><br>';
        $tela .= '<h4>Etiqueta de tombo</h4>';
        $tela .= '<form method="get" action="' . base_url('index.php/main/label_pdf/') . '">';
        $tela .= '<div class="row">';
        $tela .= '<div class="col-md-2"><span style="font-size: 75%"></span><br>';
        $tela .= '<b>Sem etiquetas</b>';
        $tela .= '</div>';

        $tela .= '<div class="col-md-2"><span style="font-size: 75%">N. tombo incial</span><br>';
        $tela .= '<input type="text" class="form-control" name="dd21">' . cr();
        $tela .= '</div>';

        $tela .= '<div class="col-md-2"><span style="font-size: 75%">total de páginas</span><br>';
        $tela .= '<select class="form-control" name="dd22">' . cr();
        $dd12 = get("dd22");
        for ($r = 1; $r < 100; $r++) {
            $sel = '';
            if ($r == $dd12) { $sel = 'selected';
            }
            $tela .= '  <option value="' . $r . '" ' . $sel . '>' . $r . '</option>' . cr();
        }
        $tela .= '</select>' . cr();
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
        $tela .= '<select class="form-control" name="dd12">' . cr();
        $dd12 = get("dd12");
        for ($r = 1; $r < 10; $r++) {
            $sel = '';
            if ($r == $dd12) { $sel = 'selected';
            }
            $tela .= '  <option value="' . $r . '" ' . $sel . '>' . $r . '</option>' . cr();
        }
        $tela .= '</select>' . cr();
        $tela .= '</div>';

        $tela .= '<div class="col-md-2"><span style="font-size: 75%">total de páginas</span><br>';
        $tela .= '  <input type="submit" name="acao" value="Imprimir" class="btn btn-primary">';
        $tela .= '</div>';
        $tela .= '</div>';
        $tela .= '</form> ';

        $data['content'] = $tela;
        $this -> load -> view('content', $data);
    }

    public function catalog() {
        $this -> cab();
        $data['title'] = 'Preparo técnico';
        $this -> load -> view('find/title', $data);

        $tela = '';

        $tela .= '<a href="' . base_url('index.php/main/catalog_work') . '" class="btn btn-secondary">';
        $tela .= 'Incorporar novo trabalho';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url('index.php/main/catalog_item') . '" class="btn btn-secondary">';
        $tela .= 'Incorporar novo item';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url('index.php/main/label') . '" class="btn btn-secondary">';
        $tela .= 'Gerar Etiquetas';
        $tela .= '</a> ';

        $data['content'] = $tela;
        $this -> load -> view('content', $data);
    }

    public function authority_create() {
        $this -> load -> model('frbr');

        $this -> cab();
        $data['title'] = 'Autoridade';
        $this -> load -> view('find/title', $data);

        $data = array();
        $data['form'] = $this -> frbr -> data_class('Agent');

        $this -> load -> view('find/form/cat_agent', $data);

        /* save work */
        if (strlen(get("action")) > 0) {
            $title = trim(get("dd1"));
            $subtitle = trim(get("dd2"));
            $form = trim(get("dd3"));
            if ((strlen($title) > 0) and (strlen($form) > 0)) {
                $id_t = $this -> frbr -> frbr_name($title);
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

    public function authority_inport_rdf($id = '') {
        $this -> load -> model('frbr');

        $data = $this -> frbr -> le($id);
        $this -> cab();
        $tela = '';

        if (strlen($data['cc_origin']) > 0) {
            $tela .= '<h4>' . $data['cc_origin'] . '</h4>' . cr();
            $url = $this -> frbr -> rdf_prefix($data['cc_origin']);
            $url .= $this -> frbr -> rdf_sufix($data['cc_origin']) . '/#';
            $tela = $this -> frbr -> viaf_inport($url);
            $tela .= '<meta http-equiv="refresh" content="0;url=' . base_url('index.php/main/v/' . $id) . '" />';
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
        $this -> cab();
        $data = array();
        $tela = '<h1>Contato</h1>';
        $tela .= 'Rene F. Gabriel junior &lt;renefgj@gmail.com&gt;';
        $data['title'] = '';
        $data['content'] = $tela;
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

    public function v($id) {
        $this -> load -> model('frbr');
        $this -> cab();
        $data = $this -> frbr -> le($id);
        if (count($data) == 0) {
            $this -> load -> view('error', $data);
        } else {
        $tela = '';
        if (strlen($data['n_name']) > 0)
            {
                $tela .= '<div class="row">';
                $tela .= '<div class="col-md-12">';
                $linkc = '<a href="'.base_url('index.php/main/v/'.$id).'" class="middle">';
                $linkca = '</a>';
                $tela .= '<h2>'.$linkc.$data['n_name'].$linkca.'</h2>';
                $tela .= '</div>';
                $tela .= '</div>';
                
            }
            /******** line #2 ***********/
            $tela .= '<div class="row">';
            $tela .= '<div class="col-md-11">';            
            $tela .= '<h5>'.msg('Class').': '.$data['c_class'].'</h5>';
            $tela .= '</div>';             
            $tela .= '<div class="col-md-1 text-right">';
            if (perfil("#ADMIN"))
                {
                    $tela .= '<a href="'.base_url('index.php/main/a/'.$id).'" class="btn btn-secondary">'.msg('edit').'</a>';
                }                
            $tela .= '</div>';
            $tela .= '</div>';
            
            $tela .= '<hr>';
            $class = trim($data['c_class']);
           //echo '==>'.$class;
            switch ($class) {
                case 'Corporate Body' :
                    $tela = $this -> frbr -> person_show($id);

                    if (perfil("#ADM")) {
                        $tela .= $this -> frbr -> btn_editar($id);
                        if (strlen($data['cc_origin']) > 0) {
                            $tela .= ' ';
                            $tela .= $this -> frbr -> btn_update($id);
                        }
                    }
                    /********* WORK **/
                    $tela .= $this -> frbr -> related($id);

                    break;            	
                case 'Person' :
                    $tela = $this -> frbr -> person_show($id);

                    if (perfil("#ADM")) {
                        $tela .= $this -> frbr -> btn_editar($id);
                        if (strlen($data['cc_origin']) > 0) {
                            $tela .= ' ';
                            $tela .= $this -> frbr -> btn_update($id);
                        }
                    }
                    /********* WORK **/
                    $wks = $this -> frbr -> person_work($id);
                    if (count($wks) > 0) {
                        $tela .= '<br><br>';
                        $tela .= '<h4>' . msg('Works') . '</h4>' . cr();
                        $tela .= '<div class="container"><div class="row">'.cr();
                        $tela .= $this -> frbr -> show_class($wks);
                        $tela .= '</div></div>'.cr();
                    }

                    break;
                case 'Work' :
                    /* Modo 2 */                    
                    $tela .= $this -> frbr -> work_show_2($id);
                    
                    /****************************************************** WORK *******/
                    //$tela .= $this -> frbr -> work_show($id);

                    /************************************************** EXPRESSION ***/
                    //$tela .= $this -> frbr -> expression_show($id);
                    //$exp = $this -> frbr -> expressions($id);
                    //for ($r = 0; $r < count($exp); $r++) {
                    //    $ide = $exp[$r];
                        /************************************************** MANIFESTACAO ***/
                    //    $tela .= $this -> frbr -> manifestation_show($ide, 0, $id);
                        //$tela .= $this -> frbr -> itens_show($ide);
                    //}
                    /****************************************************** ITENS *****/                    
                    
                    break;
                case 'Item' :
                    $data = array();
                    $tela = '';

                    /**************************************/
                    $data['id'] = $id;
                    $data['item'] = $this -> frbr -> le_data($id);
                    $work = $this -> frbr -> recupera($data['item'], 'isAppellationOfWork');
                    for ($r = 0; $r < count($work); $r++) {
                        $idw = $work[$r];
                        $data['work'] = $this -> frbr -> le_data($idw);
                        $tela .= $this -> load -> view('find/view/work', $data, true);
                    }

                    /************************** EXPRESSION ***/
                    $data['id'] = $id;
                    $tela .= $this -> frbr -> manifestation_show($id);

                    /************************** MANIFESTATION ***/
                    //$data['id'] = $id;
                    //$tela .= $this -> frbr -> manifestation_show($id);

                    /*********************************** ITEM ***/
                    $tela .= $this -> load -> view('find/view/item', $data, true);
                    break;
                default :
                    $tela .= $this -> frbr -> related($id);
                    break;
            }
            $data['content'] = $tela;
            $this -> load -> view('content', $data);
        }
        $this -> foot();
    }

	function cutter()
		{
			$this->load->model('cutters');
			$this->cab();
			
			$tela = $this->cutters->form();
			
			$name = get("dd2");
			if (strlen($name) > 0)
				{
					$tela .= $this->cutters->find_cutter($name);
				}
			
			$data['content'] = $tela;
			$data['title'] = '';
			$this->load->view('content',$data);

			$this->foot();
		}
	function authority_cutter($id)
		{
			$this->load->model('cutters');
			$this->load->model('frbr');
			
			$data = $this->frbr->le($id);
			$name = $data['n_name'];
			$n = $this->cutters->find_cutter($name);
			
			$item_nome = $this -> frbr -> frbr_name($n);
			$p_id = $this -> frbr -> rdf_concept($item_nome, 'Cutter');
        	$this -> frbr -> set_propriety($id, 'hasCutter', $p_id, 0);	
			redirect(base_url('index.php/main/v/'.$id));		
		}	
	function indice($type='',$lt='')
		{
			$this->load->model('frbr');
			$this->cab();
			switch ($type)
				{
				case 'author':
					$title = msg('index').': '.msg('index_authority');
					$sx = $this->frbr->index_author($lt);
					
					break;
				case 'editor':
					$title = msg('index').': '.msg('index_editor');
					$sx = $this->frbr->index_other($lt,'isPublisher');
					
					break;
					
				}
			$data['content'] = '<h1>'.$title.'</h1>'.$sx;
			$this->load->view('content',$data);
			
			$this->foot();
		}
}
?>
