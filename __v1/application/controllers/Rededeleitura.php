<?php
define('LIBRARY', '1003');
define('PATH', 'index.php/rededeleitura/');
define('LOGO', 'img/logo-biblioteca-girasol.png');
define('LIBRARY_NAME', 'Rede de Leitura');
define('LIBRARY_LEMA', 'Incentivando a Leitura');

class Rededeleitura extends CI_controller {
    var $lib = 10030000000;

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

        date_default_timezone_set('America/Sao_Paulo');
        /* Security */
        //      $this -> security();

    }

    function login() {
        $_SESSION['user'] = 'FINDS';
        redirect(base_url('index.php/biblio'));
    }


    private function cab($navbar = 1) {
        $this -> load -> model("socials");
        $data['title'] = LIBRARY_NAME;
        $data['logo'] = LOGO;
        $data['url'] = PATH;
        $this -> load -> view('header/books_header', $data);
        if ($navbar == 1) {
            $this -> load -> view('header/books_navbar', $data);
        }
        $_SESSION['id'] = 1;             
    }

    /******************************************************************** LOGIN DO SISTEMA */
    /* LOGIN */
    function social($act = '') {
        $this -> cab();
        if ($act == 'user_password_new') { $act = 'npass';
        }
        switch($act) {
            case 'perfil' :
                break;
            case 'pwsend' :
                $this -> socials -> resend();
                break;
            case 'signup' :
                $this -> socials -> signup();
                break;
            case 'logoff' :
                $this -> socials -> logout();
                break;
            case 'logout' :
                $this -> socials -> logout();
                break;                
            case 'forgot' :
                $this -> socials -> forgot();
                break;
            case 'npass' :
                $email = get("dd0");
                $chk = get("chk");
                $chk2 = checkpost_link($email . $email);
                $chk3 = checkpost_link($email . date("Ymd"));

                if ((($chk != $chk2) AND ($chk != $chk3)) AND (!isset($_POST['dd1']))) {
                    $data['content'] = 'Erro de Check';
                    $this -> load -> view('show', $data);
                } else {
                    $dt = $this -> socials -> le_email($email);
                    if (count($dt) > 0) {
                        $id = $dt['id_us'];
                        $data['title'] = '';
                        $tela = '<br><br><h1>' . msg('change_password') . '</h1>';
                        $new = 1;
                        // Novo registro
                        $data['content'] = $tela . $this -> socials -> change_password($id, $new);
                        $this -> load -> view('show', $data);
                        //redirect(base_url("index.php/thesa/social/login"));
                    } else {
                        $data['content'] = 'Email não existe!';
                        $this -> load -> view('error', $data);
                    }
                }

                $this -> footer();
                break;
            case 'login' :
                $this -> socials -> login();
                break;
            case 'login_local' :
                $ok = $this -> socials -> login_local();
                if ($ok == 1) {
                    redirect(base_url(PATH));
                } else {
                    redirect(base_url(PATH . 'social/login/') . '?erro=ERRO_DE_LOGIN');
                }
                break;
            default :
                echo "Function not found";
                break;
        }
    }

    private function foot() {
        $dir = troca($_SERVER['SCRIPT_FILENAME'], 'index.php', 'application/');
        $footer_file = $dir . 'views/header/footer_' . LIBRARY . '.php';
        $footer = 'header/footer_' . LIBRARY . '.php';
        if (file_exists($footer_file)) {

            $this -> load -> view($footer);
        } else {
            $this -> load -> view('header/footer');
        }
    }

    public function index() {
        $this -> load -> model('frbr');

        $this -> cab();
        $data['logo'] = LOGO;
        $this -> load -> view('welcome_brapci', $data);
        $this -> load -> view('find/search/search_simple', $data);

        /*************************** find */
        $gets = array_merge($_POST, $_GET);
        $tela = $this -> frbr -> search($gets);
        //$tela .= $this->frbr->bookcase();

        if (get("action") == '') {
            $data['li'] = $this -> frbr -> show_works();
            $data['title_rs'] = msg('acquisitions');
            $data['title_cp'] = msg('last_buy');
            $tela .= $this -> load -> view('find/bookself/bookself_h', $data, true);
        }

        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function config($tools = '', $ac = '') {
        $this -> load -> model("frbr");

        /********************* EXPORTS ************************/
        switch($tools) {
            case 'class_export' :
                /* acao */
                $this -> load -> model("frbr_clients");
                $this -> frbr_clients -> export_class($ac);
                return ('');
                exit ;
        }

        $cab = 1;
        $this -> cab();

        if (!perfil("#ADM")) {
            redirect(base_url(PATH));
        }

        $this -> load -> view('welcome');
        $tela = '';

        switch($tools) {
            case 'class_export' :
                /* acao */
                $this -> load -> model("frbr_clients");
                $tela .= $this -> frbr_clients -> export_class();
                break;
            case 'class' :
                /* acao */
                if (strlen($ac) > 0) {
                    //$tela .= msg('MAKE_MESSAGES');
                } else {
                    $tela .= $this -> frbr -> classes_lista();
                }
                break;
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
                        $tela .= '<br><a href="' . base_url(PATH . 'config/authority/update') . '" class="btn btn-secondary">' . msg('authority_update') . '</a>';
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

        redirect(base_url(PATH . 'a/' . $id));
    }

    function a($id = '') {
        $this -> load -> model('frbr');
        $data = $this -> frbr -> le($id);

        $this -> cab();

        //$tela = $this -> frbr -> show($id);
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
        $tela .= $this -> frbr -> form($id, $data);

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
                    $type = "image";
                    $short = 'img_' . $ext . '_';
                    break;
                case 'png' :
                    $type = "image";
                    $short = 'img_' . $ext . '_';
                    break;
                case 'gif' :
                    $type = "image";
                    $short = 'img_' . $ext . '_';
                    break;
            }
            $dd1 = get("dd1");
            if (strlen($dd1) == 0) {
                $dd1 = $_FILES['fileUpload']['name'];
            }

            /**********************************************/
            $width = '';
            $height = '';
            if ($type == 'image') {
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
                redirect(base_url(PATH . 'a/' . $id));
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
                $tela = '';
                $valx = substr($val,strlen($val),1);
                $val = sonumero($val);
                if (strlen($val) <= 10)
                    {
                        $val = isbn10to13($val);
                        $tela .= '====>'. $val;
                        $dv = $this -> barcodes -> isbn13($val);
                        $isbn10 = isbn13to10($val);
                    } else {
                        $dv = $this -> barcodes -> isbn13($val);
                        $isbn10 = isbn13to10($val);        
                    }
                
                if (substr($val, strlen($val), 1) != $dv) {
                    $tela .= '
                                <div class="alert alert-danger" role="alert">
                                  <strong>Error! (130C)</strong> Número do ISBN inválido "' . $val . '"
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

    public function ajax5($path = '', $id = '') {
        $this -> load -> model('frbr');
        $text = get("q");
        if (strlen($text) > 0) {
            $id_t = $this -> frbr -> frbr_name($text);
            $this -> frbr -> set_propriety($id, $path, 0, $id_t);
            echo '<meta http-equiv="refresh" content="0;">';
        } else {
            $tela = '<textarea class="form-control" style="height: 150px;" name="dd51" id="dd51"></textarea>';
            echo $tela;
        }
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
            redirect(base_url(PATH . 'vocabulary'));
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
        $tela .= $this -> vocabularies -> modal_th($id);
        $tela .= $this -> vocabularies -> list_vc($id);

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
                redirect(base_url(PATH . 'a/' . $idt));
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

                redirect(base_url(PATH . 'v/' . $id));
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

                redirect(base_url(PATH . 'v/' . $id));
            }
        }

        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);

        $this -> foot();
    }

    public function item_create($idt = '', $idw = '') {
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
                redirect(base_url(PATH . 'v/' . $idw));
            }
        }
        /* save file */
        if (strlen(get("acao")) > 0) {
            $idt = $this -> frbr -> item_add_file($idt);
            redirect(base_url(PATH . 'v/' . $idw));
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
        $tela = '<a href="' . base_url(PATH . 'bibliographic_inport') . '" class="btn btn-secundary">';
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
        $dd1 = get("search");
        if (strlen($dd1) > 0) {
            $tela = $this -> frbr -> recupera_nomes($dd1);
        } else {

            $tela = '<br>';
            $tela .= '<div class="row">' . cr();
            $tela .= '  <div class="col-md-2">';
            msg('find_viaf') . '</div>' . cr();
            $tela .= '      <a href="' . base_url(PATH . 'authority_inport') . '" class="btn btn-secondary">';
            $tela .= '      Importar MARC21';
            $tela .= '      </a> ';
            $tela .= '  </div>' . cr();
            /***************/
            $tela .= '  <div class="col-md-2">';
            msg('find_viaf') . '</div>' . cr();
            $tela .= '      <a href="' . base_url(PATH . 'authority_create') . '" class="btn btn-secondary">' . cr();
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
            $tela .= '          <form method="post" action="' . base_url(PATH . "authority/") . '">' . cr();
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

            /***********************************/
            $tela .= '<div class="row" style="margin-top: 30px;">' . cr();
            $tela .= '      <div class="col-md-2">';
            $tela .= '          <a href="http://www.geonames.org/" target="_new_geonames_' . date("dhs") . '" class="btn btn-secondary">
                            <img src="' . base_url('img/logo/logo_geonames.jpg') . '" class="img-fluid"></a>' . cr();
            $tela .= '      </div>' . cr();

            $tela .= '      <div class="col-md-10">' . cr();
            $tela .= msg('find_geonames');
            $tela .= '          <form method="post" action="' . base_url(PATH . "authority/") . '">' . cr();
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
        $cols = 3;
        $lins = 10;
        $cols = 4;
        $lins = 20;
        $mar_left = 1.3;
        $mar_right = 1.3;
        $etq_sz = 4.2;
        $etq_sp = 0.3;
        $nm = 1;
        $this -> load -> library('tcpdf');
        $icc = 1;

        $et = get("dd23");
        $style = array('position' => '', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => false, 'hpadding' => 'auto', 'vpadding' => 'auto', 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
        'text' => true, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4);

        switch($et) {
            case '0' :
                $cols = 4;
                $lins = 20;
                $mar_left = 1.8;
                $mar_right = 1.3;
                $mar_top = 1.3;
                $etq_sz = 4.2;
                $etq_sp = 0.45;
                $etq_hg = 1.275;
                $style = array('position' => '', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => false, 'hpadding' => 'auto', 'vpadding' => 'auto', 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
                'text' => true, 'font' => 'helvetica', 'fontsize' => 4, 'stretchtext' => 4);
                $bar_px = 0.3;
                $bar_sz = 12;
                $nm = 2;
                $icc = 2;
                break;
        }

        $this -> load -> model('barcodes');
        // create new PDF document
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);

        // set document information
        $pdf -> SetCreator(PDF_CREATOR);

        // set font
        $pdf -> SetFont('helvetica', '', 11);

        // -----------------------------------------------------------------------------

        $pdf -> SetFont('helvetica', '', 10);

        // define barcode style
        $nr = 1;
        $pg = 1;
        if (strlen($tp) > 0) {
            $nr = round($tp);
        }
        if (get("dd21")) {
            $nr = round(get("dd21"));
        }
        if (get("dd22")) {
            $pg = round(get("dd22"));
        }
        $lib = $this -> lib;
        $ic = 0;
        for ($pp = 0; $pp < $pg; $pp++) {
            // add a page
            $pdf -> AddPage();
            for ($r = 0; $r < $lins; $r++) {
                /************************** coluna ************/
                for ($q = 0; $q < $cols; $q++) {

                    /*********** positions ************/
                    $x = ($q * $etq_sz * 10) + ($q * $etq_sp * 10) + ($mar_left * 10);

                    $y = ($r * $etq_hg * 10) + ($mar_top * 10);

                    $nrz = strzero(round($nr) + $lib, 11);
                    $nrz = $nrz . $this -> barcodes -> ean13($nrz);

                    $pdf -> SetXY($x, $y);
                    $pdf -> write1DBarcode($nrz, 'EAN13', '', '', '', $bar_sz, $bar_px, $style, 'N');
                    $pdf -> SetFont('helvetica', '', 9);

                    if ($nm == 1) {
                        $pdf -> SetXY($x, $y + 16);
                        $pdf -> Cell(48, 0, 'Sala de Leitura PROPEL', 0, 0, 'L', 0, '', 0);
                    }

                    if ($nm == 1) {
                        $pdf -> SetXY($x, $y + 16);
                        $pdf -> Cell(48, 0, 'Nr.:' . round($nr), 0, 0, 'R', 0, '', 0);
                    }
                    /************ VERTICAL ************/
                    if ($nm == 2) {
                        $pdf -> SetXY($x + 32, $y + 11);
                        $pdf -> StartTransform();
                        $pdf -> Rotate(90);
                        $pdf -> MultiCell(10, 5, round($nr), 0, 'C', false, 0, "", "", true, 0, false, true, 0, "T", false, true);
                        $pdf -> StopTransform();
                        //$pdf -> Cell(48, 0, 'Nr.:' . round($nr),1,1,'C',0,'');
                    }
                    $ic++;
                    if ($ic >= $icc) {
                        $ic = 0;
                        $nr = $nr + 1;
                    }
                }
                $pdf -> SetXY(0, 8);
                $pdf -> Cell(48, 0, '------', 0, 0, 'L', 0, '', 0);

                $pdf -> SetXY(0, 242);
                $pdf -> Cell(48, 0, '------', 0, 0, 'L', 0, '', 0);

            }

            // EAN 13
            $pdf -> Ln();
        }
        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf -> Output('example_027.pdf', 'I');
    }

    public function label_bok($tp = '') {
        $cols = 3;
        $lins = 10;
        $cols = 4;
        $lins = 20;
        $mar_left = 1.3;
        $mar_right = 1.3;
        $etq_sz = 4.2;
        $etq_sp = 0.3;
        $nm = 1;
        $this -> load -> library('tcpdf');
        $icc = 1;

        $et = get("dd23");
        $style = array('position' => '', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => false, 'hpadding' => 'auto', 'vpadding' => 'auto', 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
        'text' => true, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4);

        switch($et) {
            default :
                $cols = 3;
                $lins = 10;
                $mar_left = 1.8;
                $mar_right = 1.3;
                $mar_top = 1.3;
                $etq_sz = 6.3;
                $etq_sp = 0.45;
                $etq_hg = 2.6;
                $style = array('position' => '', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => false, 'hpadding' => 'auto', 'vpadding' => 'auto', 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
                'text' => true, 'font' => 'helvetica', 'fontsize' => 4, 'stretchtext' => 4);
                $bar_px = 0.3;
                $bar_sz = 12;
                $nm = 2;
                $icc = 2;
                break;
        }

        /*****/
        $sql = "select * from itens 
                    where (i_status = 2 or i_status = 4)
                    and i_tombo like '" . substr($this -> lib, 0, 4) . "%'  
                    order by i_tombo";
        $rlt = $this -> db -> query($sql);
        $xrlt = $rlt -> result_array();
        if (count($xrlt) == 0) {
            echo 'Nenhum registro encontrado';
            exit ;
        }

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
        $pdf -> SetCreator(PDF_CREATOR);

        // set font
        $nr = 1;
        $pg = 1;

        $lib = $this -> lib;
        $ic = 0;
        $pdf -> AddPage();

        $totpg = $lins * $cols;
        $i = 0;
        for ($z = 0; $z < count($xrlt); $z++) {
            $line = $xrlt[$z];

            $r = (int)($i / $cols);
            $q = $i - ($r * $cols);
            $i++;

            /*********** positions ************/
            $x = ($q * $etq_sz * 10) + ($q * $etq_sp * 10) + ($mar_left * 10);

            $y = ($r * $etq_hg * 10) + ($mar_top * 10);

            $pdf -> SetXY($x, $y);
            $pdf -> SetFont('helvetica', 'B', 14);
            $pdf -> Cell(48, 0, $line['i_label_1'], 0, 0, 'L', 0, '', 0);

            $pdf -> SetXY($x, $y + 6);
            $pdf -> SetFont('helvetica', 'B', 14);
            $pdf -> Cell(48, 0, $line['i_label_2'], 0, 0, 'L', 0, '', 0);

            $pdf -> SetXY($x, $y + 12);
            $pdf -> SetFont('helvetica', '', 8);
            $pdf -> Cell(48, 0, $line['i_label_3'], 0, 0, 'L', 0, '', 0);

            $totpg--;
            if ($totpg == 0) {
                $totpg = $lins * $cols;
                $pdf -> AddPage();
                $i = 0;
            }
        }
        $pdf -> Ln();

        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf -> Output('example_027.pdf', 'I');
    }

    public function label($tp = '') {
        $this -> load -> library('tcpdf');
        $lib = $this -> lib;
        $this -> load -> model('barcodes');
        $acao = get("dd0");
        switch($acao) {
            case 'pr' :
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

        $tela .= '<div class="col-md-2"><span style="font-size: 75%">tipo de etiqueta</span><br>';
        $tela .= '  <select name="dd23" class="form-control">';
        $dd13 = get("dd23");
        $etq = array();
        array_push($etq, 'Pimaco 6287 12,7mm x 44,45mm');
        array_push($etq, 'Pimaco 6180 25,4mm x 66,7mm');

        for ($r = 0; $r < count($etq); $r++) {
            $sel = '';
            if ($r == $dd13) { $sel = 'selected';
            }
            $tela .= '  <option value="' . $r . '" ' . $sel . '>' . $etq[$r] . '</option>' . cr();
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
        $tela .= '<form method="get" action="' . base_url(PATH . 'label_pdf/') . '">';
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
        $sql = "select count(*) as total from itens where i_status = 2 and i_tombo like '" . substr($this -> lib, 0, 4) . "%'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line = $rlt[0];
        $total = $line['total'];
        if ($total > 0) {
            $itens = $total . ' etiqueta(s)';
        } else {
            $itens = 'Sem etiquetas';
        }
        $tela .= '<br><br>';
        $tela .= '<h4>Etiqueta lombada</h4>';
        $tela .= '<form method="get">';
        $tela .= '<div class="row">';
        $tela .= '<div class="col-md-2"><span style="font-size: 75%"></span><br>';
        $tela .= '<b>' . $itens . '</b>';
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
        $tela .= '  <a href="' . base_url(PATH . 'label_bok') . '" class="btn btn-primary">Imprimir</a>';
        $tela .= '</div>';
        $tela .= '</div>';
        $tela .= '</form> ';

        $data['content'] = $tela;
        $this -> load -> view('content', $data);
    }

    public function label_book() {
        $this -> load -> model('frbr');
        $this -> load -> model('cutters');
        $this -> cab();
        $lt = '';

        $data['content'] = $this -> frbr -> label_book($lt, 'hasRegisterId');
        $this -> load -> view('content', $data);

        $this -> foot();

    }

    public function catalog() {
        $this -> cab();
        $data['title'] = 'Preparo técnico';
        $this -> load -> view('find/title', $data);
        $this -> load -> view('find/labels', $data);

        $tela = '';

        $tela .= '<a href="' . base_url(PATH . 'catalog_work') . '" class="btn btn-secondary">';
        $tela .= 'Incorporar novo trabalho';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url(PATH . 'catalog_item') . '" class="btn btn-secondary">';
        $tela .= 'Incorporar novo item';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url(PATH . 'inventario/3') . '" class="btn btn-secondary">';
        $tela .= 'Remover um Item';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url(PATH . 'label') . '" class="btn btn-secondary">';
        $tela .= 'Gerar Etiquetas';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url(PATH . 'label_book') . '" class="btn btn-secondary">';
        $tela .= 'Gerar Etiquetas de Lombada';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url(PATH . 'inventario') . '" class="btn btn-secondary">';
        $tela .= 'Etiquetas Inventário';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url(PATH . 'inventario/1') . '" class="btn btn-secondary">';
        $tela .= 'Etiquetas Reimpressao';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url(PATH . 'inventario/2') . '" class="btn btn-secondary">';
        $tela .= 'Consultar Etiquetas';
        $tela .= '</a> ';

        $tela .= '<a href="' . base_url(PATH . 'inventario/3') . '" class="btn btn-secondary">';
        $tela .= 'Remover Itens';
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
                redirect(base_url(PATH . 'a/' . $p_id));
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
            $tela .= '<meta http-equiv="refresh" content="0;url=' . base_url(PATH . 'v/' . $id) . '" />';
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
        $this -> cab();
        $data = array();
        $data['title'] = 'Sobre';
        $data['content'] = '<h1>Sobre a Biblioteca</h1>';
        $this -> load -> view('content', $data);
    }

    public function v($id) {
        $this -> load -> model('frbr');
        $this -> cab();

        $tela = $this -> frbr -> vv($id);

        $data['content'] = $tela;
        $this -> load -> view('content', $data);

        $this -> foot();
    }

    function cutter() {
        $this -> load -> model('cutters');
        $this -> cab();

        $tela = $this -> cutters -> form();

        $name = get("dd2");
        if (strlen($name) > 0) {
            $tela .= $this -> cutters -> find_cutter($name);
        }

        $data['content'] = $tela;
        $data['title'] = '';
        $this -> load -> view('content', $data);

        $this -> foot();
    }

    function authority_cutter($id) {
        $this -> load -> model('cutters');
        $this -> load -> model('frbr');

        $data = $this -> frbr -> le($id);
        $name = $data['n_name'];

        $n = $this -> cutters -> find_cutter($name);

        $item_nome = $this -> frbr -> frbr_name($n);

        $p_id = $this -> frbr -> rdf_concept($item_nome, 'Cutter');
        $this -> frbr -> set_propriety($id, 'hasCutter', $p_id, 0);
        redirect(base_url(PATH . 'v/' . $id));
    }

    function indice($type = '', $lt = '') {
        $this -> load -> model('frbr');
        $this -> cab();
        switch ($type) {
            case 'author' :
                $title = msg('index') . ': ' . msg('index_authority');
                $sx = $this -> frbr -> index_author($lt);

                break;
            case 'serie' :
                $title = msg('index') . ': ' . msg('index_serie');
                $sx = $this -> frbr -> index_other($lt, 'hasSerieName');

                break;
            case 'editor' :
                $title = msg('index') . ': ' . msg('index_editor');
                $sx = $this -> frbr -> index_other($lt, 'isPublisher');

                break;
            case 'title' :
                $title = msg('index') . ': ' . msg('index_title');
                $sx = $this -> frbr -> index_work($lt, 'hasTitle');

                break;
        }
        $data['content'] = '<h1>' . $title . '</h1>' . $sx;
        $this -> load -> view('content', $data);

        $this -> foot();
    }

    function mod($mod = '', $act = '', $id = '', $id2 = '', $id3 = '') {
        $this -> load -> model('frbr');

        $this -> cab(1);
        $title = '<sup>mod:</sup>' . UpperCase($mod);

        /*** load module ********/
        $this -> load -> model($mod);
        $cmd = '$tela = $this->' . $mod . '->' . $act . "('$id','$id2','$id3');";
        //echo $cmd;
        eval($cmd);

        $data['content'] = '<h1>' . $title . '</h1>' . $tela;
        $this -> load -> view('content', $data);
    }

    function inventario($tipo = '0') {
        $this -> load -> model('frbr');
        $this -> load -> model('barcodes');
        $this -> cab();
        $tela = '';

        $cp = array();
        /***************************************** INVENTARIO ***************/
        if ($tipo == '0') {
            array_push($cp, array('$H8', '', '', false, false));
            array_push($cp, array('$S15', '', 'N. tombo', True, True));
            array_push($cp, array('$C', '', 'Marcar como erro', False, True));
            array_push($cp, array('$B8', '', 'Inventariar >>', false, false));

            $form = new form;
            $tela = $form -> editar($cp, '');

            if ($form -> saved > 0) {
                $tela .= '<script> $("#dd1").val(""); $("#dd1").focus(); </script>';
                $tombo = get("dd1");
                $erro = get("dd2");
                if (strlen($erro) > 0) {
                    $tela .= $this -> frbr -> inventario_erro($tombo);
                } else {
                    $tela .= $this -> frbr -> inventario($tombo);
                }

            }
        }
        /***************************************** REIMPRESSAO ***************/
        if ($tipo == '1') {
            array_push($cp, array('$H8', '', '', false, false));
            array_push($cp, array('$S15', '', 'N. tombo', True, True));
            array_push($cp, array('$B8', '', 'Marcar para reimpressão', false, false));

            $form = new form;
            $tela = $form -> editar($cp, '');

            if ($form -> saved > 0) {
                $tela .= '<script> $("#dd1").val(""); $("#dd1").focus(); </script>';
                $tombo = get("dd1");
                $tela .= $this -> frbr -> etiqueta_reimpressao($tombo);
            }
        }
        /***************************************** REIMPRESSAO ***************/
        if ($tipo == '2') {
            array_push($cp, array('$H8', '', '', false, false));
            array_push($cp, array('$S15', '', 'N. tombo', True, True));
            array_push($cp, array('$B8', '', 'Consultar >>', false, false));

            $form = new form;
            $tela = $form -> editar($cp, '');

            if ($form -> saved > 0) {
                $tela .= '<script> $("#dd1").val(""); $("#dd1").focus(); </script>';
                $tombo = get("dd1");
                $tela .= $this -> frbr -> etiqueta_consulta($tombo);
            }
        }
        /***************************************** REIMPRESSAO ***************/
        if ($tipo == '3') {
            array_push($cp, array('$H8', '', '', false, false));
            array_push($cp, array('$S15', '', 'N. tombo', True, True));
            array_push($cp, array('$B8', '', 'Consultar >>', false, false));

            $form = new form;
            $tela = '<h1>Remover um item</h1>' . cr();
            $tela .= $form -> editar($cp, '');

            if ($form -> saved > 0) {
                $tela .= '<script> $("#dd1").val(""); $("#dd1").focus(); </script>';
                $tombo = get("dd1");
                $tela .= $this -> frbr -> etiqueta_consulta($tombo);
                $tela .= $this -> frbr -> item_remove($tombo);
            }
        }
        $data['content'] = $tela;
        $data['title'] = 'Inventário';
        $this -> load -> view('content', $data);
    }

    function d($id, $chk = '') {
        $this -> cab();
        if (perfil("#ADM#CAT")) {
            $this -> load -> model("frbr");
            $this -> frbr -> remove_concept($id);
        }
        redirect(PATH);
    }

    function labels($pg = '') {
        $this -> load -> model('frbr');
        $this -> cab();
        $this -> frbr -> labels($pg);
        $this -> foot();
    }

    function labels_ed($id = '', $chk = '', $close = 0) {
        $this -> load -> model('frbr');
        $this -> cab();
        $this -> frbr -> labels_ed($id, $chk, $close);
        $this -> foot();
    }

    function bookshelf($id = '') {
        $this -> load -> model('frbr');
        $tela = '';
        $this -> cab();
        $data['content'] = $this -> frbr -> show_bookshelf();
        $data['title'] = msg('Bookshelf');
        $this -> load -> view('content', $data);

        $this -> foot();
    }

    function superadmin($id='',$act='')
        {
            $this->cab();
            $this->load->model("superadmin");
            $this->superadmin->index($id,$act);
            $this->foot();
        }
        
    function help($id='',$act='')
        {
            $this->load->model('manuals');
            $this->manuals->index($id,$act);
        }

}
?>
