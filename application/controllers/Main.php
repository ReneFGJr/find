<?php
class Main extends CI_controller {
    var $lib = 10010000000;

    function __construct() {
        parent::__construct();

        $this -> lang -> load("app", "portuguese");
        $this -> lang -> load("find", "portuguese");
        $this -> load -> library('tcpdf');
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
        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
    }

    public function config($tools = '') {
        $this -> cab();
        $this -> load -> view('welcome');
        $tela = '';
        $tela .= '<ul>';
        $tela .= '<li>';
        $tela .= '<a href="' . base_url('index.php/main/config/msg') . '">Compilar Mensagens</a>';
        $tela .= '</li>';
        $tela .= '</ul>';

        switch($tools) {
            case 'msg' :
                $tela .= msg('MAKE_MESSAGES');
                break;
        }
        $data['content'] = $tela;
        $this -> load -> view('content', $data);
        $this -> foot();
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

        $tela = $this -> frbr -> show($id);
        $tela .= '<hr>';
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
        $tela = $this -> frbr -> ajax($path, $id);
        echo $tela;
    }

    function ajax_action($ac = '', $id = '') {
        $this -> load -> model('frbr');
        switch($ac) {
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
                $t = file_get_contents($url);
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
        $tela .= '<a href="' . base_url('index.php/main/authority_inport') . '" class="btn btn-secondary">';
        $tela .= 'Importar MARC21';
        $tela .= '</a> ';
        /***************/
        $tela .= '<a href="' . base_url('index.php/main/authority_create') . '" class="btn btn-secondary">';
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

    public function catalog_item() {
        $this -> load -> model('frbr');
        $this -> load -> model('vocabularies');

        $this -> cab();
        $data['content'] = $this -> frbr -> item_catalog();
        $this -> load -> view('content', $data);
        $this -> foot();

    }

    public function label_pdf($tp = '') {
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
        $lib = $this -> lib;
        for ($r = 0; $r < 13; $r++) {
            for ($q = 0; $q < 4; $q++) {
                $y = $r * 20 + 15;
                $x = $q * 50 + 10;
                $pdf -> SetXY($x, $y);

                $nrz = strzero($nr, 11);
                $nrz = $nrz . $this -> barcodes -> ean13($nrz + $lib);
                $nr = $nr + 1;
                $pdf -> write1DBarcode($nrz, 'EAN13', '', '', '', 18, 0.4, $style, 'N');
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
        $acao = get("dd0");
        switch($acao) {
            case '1' :
                $data['dd1'] = round(get("dd1")) + $lib;
                $data['dd2'] = get("dd2");
                $data['dd3'] = get("dd3");
                $data['col'] = get("dd4");
                //$data['label'] = 'Sala de Leitura Propel';
                $data['label'] = 'Propel - IFRGS';
                $tela = $this -> load -> view('find/label/tombro_1', $data, true);

                $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $obj_pdf -> SetCreator(PDF_CREATOR);
                $title = "Etiqueta Bibliográfica";
                $obj_pdf -> SetTitle('Bibliográfica');
                //	$obj_pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, "Monthly Report");
                //	$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                //	$obj_pdf -> setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                //	$obj_pdf -> SetDefaultMonospacedFont('helvetica');
                //	$obj_pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                //	$obj_pdf -> SetFooterMargin(PDF_MARGIN_FOOTER);
                //	$obj_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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

        $tela = '<a href="' . base_url('index.php/main/catalog_item') . '" class="btn btn-secondary">';
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
                echo '<br>==>' . $id_t . '--' . $title;
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
            $class = trim($data['c_class']);
            $tela = '';
            //echo "===>" . $class;
            switch ($class) {
                case 'Person' :
                    $tela = $this -> frbr -> person_show($id);

                    /********* WORK **/
                    $wks = $this -> frbr -> person_work($id);

                    break;
                case 'Work' :
                    /****************************************************** WORK *******/
                    $tela .= $this -> frbr -> work_show($id);

                    /************************************************** MANIFESTACAO ***/
                    $ma = $this -> frbr -> recupera_manifestacao($id);
                    $hd = 0;
                    $tela .= '<div class="container"><div class="row">';
                    for ($r = 0; $r < count($ma); $r++) {
                        $id = $ma[$r];
                        $tela .= $this -> frbr -> manifestation_show($ma[$r], $hd);
                        $hd = 1;
                    }
                    $tela .= '</div></div>';

                    /****************************************************** ITENS *****/
                    $tela .= $this -> frbr -> itens_show($id);

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
                    
                    
                    /************************** MANIFESTATION ***/
                    $data['id'] = $id;
                    echo "==>".$id;
                    $tela .= $this -> frbr -> manifestation_show($id);
                    
                    /*********************************** ITEM ***/
                    $tela .= $this -> load -> view('find/view/item', $data, true);

                    break;
            }
            $data['content'] = '<h1>' . $class . '</h1>' . $tela;
            $this -> load -> view('content', $data);
        }
        $this -> foot();
    }

}
?>
