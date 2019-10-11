<?php
class libraries extends CI_model {
    var $table = 'library';
    function __construct() {

        if (!isset($_SESSION['library']) or (round($_SESSION['library']) == 0)) {
            $ck = $_COOKIE;
            foreach ($ck as $key => $value) {
                if ($key == 'library') {
                    $id = $value;
                    $this->select($id);
                    echo '=SELECAO==>'.$id;
                    exit;
                    redirect(base_url('index.php/main/library/' . $id));
                }
            }
            /* Biblioteca */

            if (isset($_SERVER['PATH_INFO']))
            {
                $page = $_SERVER['PATH_INFO'];
            } else {
                $page = 'root';
            }
            
            if (strpos($page, '/library')) {                // PAGE
                /* Selecionar a Library */
            } else {
                redirect(base_url('index.php/main/library/'));
            }
        } else {
            $id = round($_SESSION['library']);
            $data = $this -> le($id);
            define('LIBRARY', $data['l_code']);
            define('PATH', 'index.php/main/');
            define('LOGO', $data['l_logo']);
            define('LIBRARY_NAME', $data['l_name']);
            define('LIBRARY_LEMA', 'Memória da Ciência da Informação');
            define('BARS', LIBRARY . '0000000');
            $s = array('liberary' => $id, 'LIBRARY' => LIBRARY, 'LIBRARY_LOGO' => LOGO, 'LIBRARY_NAME' => LIBRARY_NAME, 'LIBRARY_LEMA' => LIBRARY_LEMA);
            $this -> session -> set_userdata($s);
        }
        return ('');
    }

    function select($id) {
        $data = $this -> le($id);
        $s = array('library' => $id, 'LIBRARY' => LIBRARY, 'LIBRARY_LOGO' => LOGO, 'LIBRARY_NAME' => LIBRARY_NAME, 'LIBRARY_LEMA' => LIBRARY_LEMA);
        $this -> session -> set_userdata($s);
        $unexpired_cookie_exp_time = 2147483647 - time();
        foreach ($s as $key => $value) {
            setcookie($key, $value, $unexpired_cookie_exp_time);
            $this -> input -> set_cookie($key, $value, $unexpired_cookie_exp_time);
        }
        return (1);
    }

    function le($id) {
        $sql = "select * from library where id_l = " . round($id);
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0)
        {
            $line = $rlt[0];            
        } else {
            $line = array();
        }
        
        return ($line);
    }

    function list_libraries($id = '') {
        $sql = "select * from library order by l_name";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<div class="row">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $link = '<a href="' . base_url(PATH . 'library/' . $line['id_l']) . '" border=0>';
            $sx .= '<div class="col-md-4 col-lg-3 text-center" style="border: 2px solid #00000; margin-bottom: 50px;">';
            $sx .= $link;
            $sx .= '<img src="' . base_url($line['l_logo']) . '" class="img-fluid">';
            $sx .= '<br>';
            $sx .= $line['l_name'];
            $sx .= '</a>';
            $sx .= '</div>';
            $sx .= '<div class="col-md-1 text-center">';
            $sx .= '</div>';
        }
        $sx .= '</div>';
        return ($sx);
    }

    function row($id = '') {
        $form = new form;

        $form -> fd = array('id_l', 'l_name', 'l_code', 'l_id', 'l_logo');
        $form -> lb = array('id_l', msg('l_name'), msg('l_code'), msg('l_id'), msg('l_logo'));
        $form -> mk = array('', 'L', 'L', 'A');

        $form -> tabela = $this -> table;
        $form -> see = True;
        $form -> novo = perfil("#ADMIN");
        $form -> edit = perfil("#ADMIN");

        $form -> row_edit = base_url(PATH . 'superadmin/library_edit');
        $form -> row_view = base_url(PATH . 'library');
        $form -> row = base_url(PATH . 'superadmin/own');

        return (row($form, $id));
    }

    function edit($id = '', $ac = '') {
        $cp = array();
        array_push($cp, array('$H8', 'id_l', '', false, true));
        array_push($cp, array('$S100', 'l_name', msg('library_name'), true, true));
        array_push($cp, array('$S10', 'l_code', msg('library_code'), false, true));
        array_push($cp, array('$H8', 'l_id', msg('library_id'), false, true));
        array_push($cp, array('$S100', 'l_logo', msg('library_logo'), false, true));
        array_push($cp, array('$T80:5', 'l_about', msg('library_about'), false, true));
        $form = new form;
        $form -> id = $ac;
        $sx = $form -> editar($cp, $this -> table);
        return ( array($sx, $form));
    }
    function highlights($tp='',$force=0)
    {
        if (strlen(get("force")) > 0)
        {
            $force = 1;
        }
        $lib = $_SESSION['library'];
        dircheck('_temp');
        dircheck('_temp/view');
        $file = '_temp/view/highlight_'.$tp.'_'.$lib.'.php';
        if ((file_exists($file)) and ($force == 0))
        {
            $tela = file_get_contents($file);
        } else {
            $data['li'] = $rdf ->  show_works();
            switch($tp)
            {
                case 'bookself':
                $tela = $rdf ->  show_bookshelf();
                break;                 
                case 'sc':
                $data['li'] = $rdf ->  show_works();
                $data['title_rs'] = msg('showcase');
                $data['title_cp'] = msg('highlights');
                $data['id'] = $tp;
                $tela = $this -> load -> view('find/bookself/bookself_h', $data, true);
                break;                        
                default:
                $data['li'] = $rdf ->  show_works();
                $data['title_rs'] = msg('acquisitions');
                $data['title_cp'] = msg('last_buy');
                $data['id'] = $tp;
                $tela = $this -> load -> view('find/bookself/bookself_h', $data, true);
                break;
            }

            $rlt = fopen($file,'w+');
            fwrite($rlt,$tela);
            fclose($rlt);                
        }            
        return($tela);
    }
    function image_check()
    {
        $dir    = '_repositorio/Image/';
        $files = scandir($dir,0);
        $sx = '<table class="table">';
        for ($rq=0; $rq < count($files);$rq++)
        {
            $filename = $dir.$files[$rq];
            if (strlen(trim($files[$rq])) > 1)
            {
                $sx .= '<tr><td>'.$files[$rq].'</td>';
                if (is_file($filename))
                {
                    $size = getimagesize($filename);
                    $w = $size[0];
                    $h = $size[1];
                    $sx .= '<td align="center">'.$w.'px x '.$h.'px</td>';
                    $sx .= '<td align="right">'.number_format(filesize($filename)/1024,1,'.',',').'kbytes</td>';
                    if ($h > 450)
                    {
                        $sw = (int)($w * 400 / $h);
                        $type = $size['mime'];
                        $sh = 400;
                        if ($type == 'image/png')
                        {
                            $this->png2jpg($filename,$filename,100);
                        }
                        $this->image_resize($filename,$sw,$sh);                    
                    }
                }
            }
        }
        $sx .= '</table>';
        echo $sx;
        return($sx);
    }

    function image_resize($file, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        imagejpeg($dst, $file, 100);
        imagedestroy($dst);
        return 1;
    } 
    function png2jpg($originalFile, $outputFile, $quality) {
        $image = imagecreatefrompng($originalFile);
        imagejpeg($image, $outputFile, $quality);
        imagedestroy($image);
    }

    function work_show($id, $act = '') {
        $rdf = new rdf;
        $this -> load -> model('avaliations');
        $this -> load -> model('loans');
        $data = array();

        /************** actions *************/
        $act = get("action");
        $nri = round(get("dd1"));
        $idm = round(get("dd2"));
        $title = trim(get("dd50"));
        if (perfil("#CAT#ADM")) {
            switch($act) {
                case 'chapter' :
                    $idc = $this -> book_new_chapter($id, $idm, ($nri + 1), $title);
                    redirect(base_url(PATH . 'v/' . $id));
                    break;
            }
        }

        $prop_expression = $rdf ->  find_class('isRealizedThrough');
        $prop_manifestation = $rdf ->  find_class('isEmbodiedIn');
        $prop_item = $rdf ->  find_class('isExemplifiedBy');
        $sx = '';

        $data['id'] = $id;

        /* LE DADOS SOBRE WORK */
        $data['work'] = $rdf ->  le_data($id);

        /* recupera expression */
        $sql = "select * from rdf_data where d_r1 = " . $id . " and d_p = " . $prop_expression;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        if ((count($rlt) == 0) and (perfil("#ADMIN"))) {
            $data['id'] = $id;
            $sx .= $this -> load -> view('find/view/expression_void', $data, true);
        }

        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $ide = $line['d_r2'];
            $data['expression'] = $rdf ->  le_data($ide);

            /* recupera manifestation */
            $sql = "select * from rdf_data where d_r1 = " . $ide . " and d_p = " . $prop_manifestation;
            $rltm = $this -> db -> query($sql);
            $rltm = $rltm -> result_array();

            /*************************************************************/
            if ((count($rltm) == 0) and (perfil("#ADMIN"))) {
                $sx .= '| ' . $this -> load -> view('find/view/expression_void', $data, true);
                $sx .= '| ' . $this -> load -> view('find/view/work_2', $data, true);
                $mani['id'] = $ide;
                $sx .= ' | ' . $this -> load -> view('find/view/manifestation_void', $data, true);
            }

            for ($y = 0; $y < count($rltm); $y++) {
                $line = $rltm[$y];
                $idm = $line['d_r2'];
                $data['idm'] = $idm;
                $data['manifestation'] = $rdf ->  le_data($idm);

                /************************************************************ RECUPERA ITEM **********/
                $sql = "select * from rdf_data where d_r2 = " . $idm . " and d_p = " . $prop_item;
                $rlti = $this -> db -> query($sql);
                $rlti = $rlti -> result_array();
                $itens = array();

                $its = $this -> itens_show_resume($idm);
                $data['itens'] = $its;
                $data['chapter'] = $this -> chapters($idm);

                $sx .= $this -> load -> view('find/view/work_3', $data, true);

            }
        }
        return ($sx);
    }    

   function itens_show_resume($id) {

        $sql = "select d_r1 as item, d_r2 as manitestation, d_p as prop from rdf_data
                    INNER JOIN rdf_class ON d_p = id_c 
                    where c_class = 'isExemplifiedBy' and d_r2 = " . $id . "
                    order by c_order
                    ";

        $rlt = $this -> db -> query($sql);
        $man = $rlt -> result_array();
        $sx = '<img src="' . base_url('img/icon/icone_bookcase.jpg') . '" height="32" title="' . msg('see_copies') . '" id="bookcase' . $id . '">' . cr();
        $sx .= '<script> $("#bookcase' . $id . '").click(function() { $("#samples' . $id . '").toggle(500); }); </script>' . cr();
        $sx .= '<table border=0 width="100%" id="samples' . $id . '" style="display: block;">';
        $sx .= '<tr class="small" style="background: #c0c0c0;">
                    <th width="5%">Act</th>
                    <th width="25%">Biblioteca</th>
                    <th width="25%">local</th>
                    <th width="20%">exemplar</th>
                    <th width="25%">situação</th>
                </tr>' . cr();
        /********************* digital ***********/
        for ($y = 0; $y < count($man); $y++) {
            $idm = $man[$y]['item'];
            $data['id'] = $idm;
            $items = $rdf -> le_data($idm);
            $xlocal = '';
            $xowner = '';
            $ex = 0;
            $fl = '';

            for ($r = 0; $r < count($items); $r++) {
                $line = $items[$r];
                $type = trim($line['c_class']);
                //echo '<br>'.$type;
                switch ($type) {
                    case 'isOwnedBy' :
                        $owner = $line['n_name'];
                        if ($xowner != $owner) {
                            $sx .= '<tr>';
                            $linked = '<a href="' . base_url(PATH . 'a/' . $line['d_r1']) . '">';
                            $sx .= '<td>' . $linked . '[ed]</a></td>';
                            $sx .= '<td>' . $owner . '</td>';
                            $xowner = $owner;
                            $ex = 0;
                        }
                        break;
                    case 'hasLocatedIn' :
                        $local = $line['n_name'];
                        if ($xlocal != $local) {
                            $sx .= '<td>' . $local . '</td>';
                            $xlocal = $local;
                            $ex = 0;
                        }
                        break;
                    case 'hasRegisterId' :
                        if ($ex > 0) { $sx .= '; ';
                        } else {
                            $sx .= '<td>';
                        }
                        $sx .= substr($line['n_name'], 0, 15);
                        $tombo = $line['n_name'];
                        break;
                    case 'hasFileName' :
                        $sx .= '<td>';
                        $link = '<a href="' . base_url($line['n_name']) . '" target="_new">';
                        $sx .= $link . msg('download') . '</a>';
                        $fl++;
                        $sx .= '</td>';
                        break;
                }
            }
            $sx .= '<td>' . msg('situacao_exemplar_' . $this -> exemplar_situacao($tombo)) . '</td>';
        }
        $sx .= '</table>';
        if (count($man) == 0) {$sx = '';
        }
        return ($sx);
    }    
   function v($id) {
        $tela = '';
        $rdf = new rdf;
        $data = $rdf -> le($id);
        if (count($data) == 0) {
            $this -> load -> view('error', $data);
        } else {
            $tela = '';
            if (strlen($data['n_name']) > 0) {
                $tela .= '<div class="row">';
                $tela .= '<div class="col-md-12">';
                $linkc = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="middle">';
                $linkca = '</a>';
                $tela .= '<h2>' . $linkc . $data['n_name'] . $linkca . '</h2>';
                $tela .= '</div>';
                $tela .= '</div>';

            }
            /******** line #2 ***********/
            $tela .= '<div class="row">';
            $tela .= '<div class="col-md-11">';
            $tela .= '<h5>' . msg('Class') . ': ' . $data['c_class'] . '</h5>';
            $tela .= '</div>';
            $tela .= '<div class="col-md-1 text-right">';
            if (perfil("#ADMIN")) {
                $tela .= '<a href="' . base_url(PATH . 'a/' . $id) . '" class="btn btn-secondary">' . msg('edit') . '</a>';
            }
            $tela .= '</div>';
            $tela .= '</div>';

            $tela .= '<hr>';
            $class = trim($data['c_class']);
            switch ($class) {
                case 'Corporate Body' :
                    $tela = $rdf ->  person_show($id);

                    if (perfil("#ADM")) {
                        $tela .= $rdf ->  btn_editar($id);
                        if (strlen($data['cc_origin']) > 0) {
                            $tela .= ' ';
                            $tela .= $rdf ->  btn_update($id);
                        }
                    }
                    /********* WORK **/
                    $tela .= $rdf ->  related($id);

                    break;
                case 'Person' :
                    $tela = $rdf ->  person_show($id);

                    if (perfil("#ADM")) {
                        $tela .= $rdf ->  btn_editar($id);
                        if (strlen($data['cc_origin']) > 0) {
                            $tela .= ' ';
                            $tela .= $rdf ->  btn_update($id);
                        }
                    }
                    /********* WORK **/
                    $wks = $rdf ->  person_work($id);
                    if (count($wks) > 0) {
                        $tela .= '<br><br>';
                        $tela .= '<h4>' . msg('Works') . '</h4>' . cr();
                        $tela .= '<div class="container"><div class="row">' . cr();
                        $tela .= $rdf ->  show_class($wks);
                        $tela .= '</div></div>' . cr();
                    }

                    break;
                case 'Work' :
                    /* Modo 2 */
                    $tela = $this -> work_show($id);
                    break;
                case 'Item' :
                    $data = array();
                    $tela = '';

                    /**************************************/
                    $data['id'] = $id;
                    $data['item'] = $rdf ->  le_data($id);
                    $work = $rdf ->  recupera($data['item'], 'isAppellationOfWork');
                    for ($r = 0; $r < count($work); $r++) {
                        $idw = $work[$r];
                        $data['work'] = $rdf ->  le_data($idw);
                        $tela .= $this -> load -> view('find/view/work', $data, true);
                    }

                    /************************** EXPRESSION ***/
                    $data['id'] = $id;
                    $tela .= $rdf ->  manifestation_show($id);

                    /************************** MANIFESTATION ***/
                    //$data['id'] = $id;
                    //$tela .= $rdf ->  manifestation_show($id);

                    /*********************************** ITEM ***/
                    $tela .= $this -> load -> view('find/view/item', $data, true);
                    break;
                default :
                    $tela .= $rdf ->  related($id);
                    break;
            }
        }
        return ($tela);
    }    
    function exemplar_situacao($tb) {
        $sql = "select * from itens where i_tombo = '$tb' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line['i_status']);
        }
        return (0);
    }    
    function chapters($id = 0) {
        $cap = array();
        $a = $rdf ->  le_data($id);
        for ($r = 0; $r < count($a); $r++) {
            $ln = $a[$r];
            $class = $ln['c_class'];
            $d = $ln['d_r2'];
            if ($class == 'hasChapterOf') {
                $cap[$d] = $rdf ->  le_data($d);
            }
        }
        return ($cap);
    }    
}
?>
