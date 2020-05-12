<?php
class libraries extends CI_model {
    var $table = 'library';
    var $table_place = 'library_place';
    function __construct() {
        if (!isset($_SESSION['library']) or (round($_SESSION['library']) == 0)) {
            $ck = $_COOKIE;
            foreach ($ck as $key => $value) {
                if ($key == 'library') {
                    $id = $value;
                    $this->select($id);
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
            $this->select($id);
        }

        if (!isset($id))
        {
            echo "OPS - ".$page;
            if (strpos($page, '/library')) { 
                define('PATH', 'index.php/main/');
                /* Nothing */
            } else {
                redirect(base_url('index.php/main/library/'));    
            }

        } else {
            $data = $this -> le($id);
            define('LIBRARY', $data['l_id']);
            define('PATH', 'index.php/main/');
            define('LOGO', $data['l_logo']);
            define('LIBRARY_NAME', $data['l_name']);
            define('LIBRARY_LEMA', $data['l_about']);                  
        }

        return ('');
    }

    function contact()
    {
        $sx = 'Contato';
        return($sx);            
    }

    function about_places($dt=array())
    {
        if (isset($_SESSION['library']))
        {
            $id = $_SESSION['library'];
        } else {
            redirect(PATH);
        }
        $sx = '<h3>'.msg('library_places').'</h3>';
        $sql = "select * from library_place where lp_LIBRARY = '".LIBRARY."' ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        for ($r=0;$r < count($rlt);$r++)
        {
            $line = $rlt[$r];
            $sx .= '<hr>';
            $sx .= '<h3>'.$line['lp_name'].'</h3>';
            if (isset($dt['edit']))
            {
                $sx .= '<a href="'.base_url(PATH.$dt['edit'].$line['id_lp']).'" class="btn btn-outline-primary">';
                $sx .= msg('edit');
                $sx .= '</a>';
            }

            /***/
            $sx .= '<div class="info1">'.mst($line['lp_address']).'</div>'.cr();
            $sx .= '<div class="info1">'.$line['lp_email'].'</div>'.cr();
            $sx .= '<div class="info1">'.$line['lp_contato'].'</div>'.cr();
            $sx .= '<div class="info1">'.$line['lp_responsavel'].'</div>'.cr();
            $sx .= '<div class="info1">'.$line['lp_telefone'].'</div>'.cr();
            $sx .= '<div class="info1">'.$line['lp_site'].'</div>'.cr();
            $sx .= '<div class="info1">'.mst($line['lp_obs']).'</div>'.cr();
        }
        return($sx);
    }

    function about()
    {
        if (isset($_SESSION['library']))
        {
            $id = $_SESSION['library'];
        } else {
            redirect(PATH);
        }
        $dt = $this->le($id);
        $logo = $dt['l_logo'];
        if (file_exists($logo))
        {
            $logo = base_url($logo);
        } else {
            $logo = base_url('img/logo/no_logo.png');
        }
        $sx = '<div class="row">';
        $sx .= '<div class="col-md-10">'.cr();
        $sx .= '<h3>'.msg("About").'</h3>'.cr();

        $sx .= '<span class="small">'.msg('library_name').'</span><br/>'.cr();
        $sx .= '<h2>'.$dt['l_name'].'</h2>'.cr();
        $sx .= '<p>'.mst($dt['l_about']).'</p>'.cr();

        $sx .= '</div>'.cr();
        $sx .= '<div class="col-md-2">'.cr();
        $sx .= '<img src="'.$logo.'" class="img-fluid">'.cr();
        $sx .= '</div>'.cr();
        $sx .= '</div>'.cr();
        return($sx);
    }

    function select($id) {
        $data = $this -> le($id);
        $s = array('library' => $id, 'LIBRARY' => $data['l_id'], 'LIBRARY_LOGO' => $data['l_logo'], 'LIBRARY_NAME' => $data['l_name'], 'LIBRARY_LEMA' => $data['l_about']);
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

    function le_id($id) {
        $sql = "select * from library where l_id = '" . $id."'";
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

    function edit($id = '') {
        $cp = array();
        array_push($cp, array('$H8', 'id_l', '', false, true));
        array_push($cp, array('$S100', 'l_name', msg('library_name'), true, true));
        array_push($cp, array('$S30', 'l_code', msg('library_code'), false, false));
        array_push($cp, array('$H8', 'l_id', msg('library_id'), false, true));
        array_push($cp, array('$S100', 'l_logo', msg('library_logo'), false, true));
        array_push($cp, array('$T80:5', 'l_about', msg('library_about'), false, true));
        $form = new form;
        $form -> id = $id;
        $sx = $form -> editar($cp, $this -> table);
        return ( array($sx, $form));
    }

    function edit_places($id = '') {
        $cp = array();
        array_push($cp, array('$H8', 'id_lp', '', false, true));
        array_push($cp, array('$S100', 'lp_name', msg('library_name'), true, true));
        array_push($cp, array('$HV', 'lp_LIBRARY ', LIBRARY, false, true));
        array_push($cp, array('$T80:5', 'lp_address', msg('l_endereco'), false, true));
        array_push($cp, array('$T80:5', 'lp_obs', msg('lp_obs'), false, true));
        array_push($cp, array('$S80', 'lp_email', msg('lp_email'), false, true));
        array_push($cp, array('$S80', 'lp_responsavel', msg('lp_responsavel'), false, true));
        array_push($cp, array('$S80', 'lp_site', msg('lp_site'), false, true));
        array_push($cp, array('$S80', 'lp_coord_x', msg('lp_coord_x'), false, true));
        array_push($cp, array('$S80', 'lp_coord_y', msg('lp_coord_y'), false, true));


        array_push($cp, array('$SN', 'lp_active', msg('lp_active'), true, true));        
        
        $form = new form;
        $form -> id = $id;
        $sx = $form -> editar($cp, $this -> table_place);
        return ( array($sx, $form));
    }

    function highlights($tp='',$force=0)
    {
        $rdf = new rdf;
        if (strlen(get("force")) > 0)
        {
            $force = 1;
        }
        $lib = $_SESSION['library'];
        $sql = "select i_identifier, min(i_manitestation) as i_manitestation, i_ln1, i_ln2
                    from find_item 
                    where i_library = '".LIBRARY."' 
                    group by i_manitestation, i_identifier, i_ln1, i_ln2
                    order by i_ln1, i_ln2";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '';
        $sx .= '<div class="container">';
        $sx .= '<div class="row">';
        
        for ($r=0;$r < count($rlt);$r++)
            {
                $line = $rlt[$r];
                $link = '<a href="'.base_url(PATH.'v/'.$line['i_manitestation']).'">';
                $sx .= '<div class="col-md-2" style="margin-bottom: 20px";>';
                $sx .= $link;
                $sx .= '<img src="'.$this->image($line['i_identifier']).'" class="img-fluid">';
                $sx .= '</a>';
                $sx .= '</div>';
            } 
        $sx .= '</div></div>';
        return($sx);
    }

    function image($isbn)
        {
            $img = '_covers/image/'.$isbn.'.jpg';
            if (file_exists($img))
                {
                    $img = base_url($img);
                } else {
                    $img = 'xx';
                }
            return($img);
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
                /******************* RECUPERA ITENS ***********/
                $its = $this -> itens_show_resume($idm);
                $data['itens'] = $its;
                $data['chapter'] = $this -> chapters($idm);
                $data['id_cc'] = $id;

                $sx .= $this -> load -> view('find/view/work_3', $data, true);

            }
        }
        return ($sx);
    }    

    /********************** ITENS ************************************/
    function library_place_bookshelf($id=0,$name='')
    {
        if ($id == 0)
        {
            $sql = "select * from library_place_bookshelf where bs_name = '".$name."' and bs_LIBRARY = ".LIBRARY;
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            if (count($rlt) == 0)
            {
                $sql = "insert into library_place_bookshelf
                (bs_name, bs_image, bs_bs, bs_LIBRARY)
                values
                ('$name','','',".LIBRARY.")";
                $rlt = $this->db->query($sql);
            }
            $sql = "select * from library_place_bookshelf where bs_name = '".$name."' and bs_LIBRARY = ".LIBRARY;
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
        } else {
            $sql = "select * from library_place_bookshelf where id_bs = '".$id."'";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();            
        }
        $line = $rlt[0];
        return($line);        
    }
    function library_place($id,$name='')
    {
        if ($id == 0)
        {
            $sql = "select * from library_place where lp_name = '".$name."' and lp_LIBRARY = ".LIBRARY;
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            if (count($rlt) == 0)
            {
                $sql = "insert into library_place
                (lp_name, lp_address, lp_coord_x, lp_coord_y, lp_email, lp_LIBRARY)
                values
                ('$name','',0,0,'',".LIBRARY.")";
                $rlt = $this->db->query($sql);
            }
            $sql = "select * from library_place where lp_name = '".$name."' and lp_LIBRARY = ".LIBRARY;
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
        } else {
            $sql = "select * from library_place where id_lp = '".$id."'";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();            
        }
        $line = $rlt[0];
        return($line);
    }
    function itens_check($id) { 
        $place = 0;

        $rdf = new rdf;
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
            $place = 0;
            $shelf = 0;
            $ex = 0;
            $fl = '';
            for ($r = 0; $r < count($items); $r++) {
                $line = $items[$r];
                $type = trim($line['c_class']);
                $value = $line['n_name'];
                //echo '<br>'.$type.' => '.$value;
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
                        $place = $this->library_place(0,$owner);
                    }
                    break;
                    case 'hasLocatedIn' :
                    $local = $line['n_name'];
                    if ($xlocal != $local) {
                        $sx .= '<td>' . $local . '</td>';
                        $xlocal = $local;
                        $ex = 0;
                        $shelf = $this->library_place_bookshelf(0,$local);
                    }
                    break;
                    case 'hasRegisterId' :
                    if ($ex > 0) 
                    { 
                        $sx .= '; ';
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

            /**************************************************/
            $sql = "select * from itens where i_tombo = '".$tombo."'";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $date = date("Y-m-d");
            $mani = $id;
            if (count($rlt) == 0)
            {
                $sql = "insert into itens
                (i_tombo, i_library, i_place,
                i_bookshelf, i_shelf, i_status,
                i_update, i_user, i_date_return, 
                i_label_1, i_label_2, i_label_3,
                i_manifestation, i_inventario, i_consulta_local
                ) values (
                '$tombo',".LIBRARY.",".$place['id_lp'].",
                ".$shelf['id_bs'].",0,9,
                '$date',0,'$date',
                '','','',
                $mani,0,0)
                ";
                $rlt = $this->db->query($sql);
            }
        }
        $sx .= '</table>';
    }
    function tombo($t)
    {
        $ta = '';
        if (strlen($t) > 10)
        {
            $ta = substr($t,0,4);
            $tn = round(substr($t,4,7));
            $tt = $tn;
            $td = substr($t,11,1);
            $tn = '<span style="text-decoration: underline">'.$tn.'</span>';
            while (strlen($tt) <= 7)
            {
                $tt .= '0';
                $tn = '0'.$tn;
            }
            $t = $ta.'.'.$tn.'-'.$td;
        }
        return($t);
    }
    function itens_show_resume($id) {
        $this->itens_check($id);
        /****************************************************** versaõ 2 ****************************/
        $si = '';
        $sql = "select * from itens 
        inner join library_place ON i_place = id_lp
        inner join library_place_bookshelf ON i_bookshelf = id_bs
        where i_manifestation = ".round($id);
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        for ($r=0;$r < count($rlt);$r++)
        {
            $line = $rlt[$r];
            $si .= $this->label($line);
        }
        return ($si);
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
                $tela = $this ->  person_show($id);

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
                $tela = $this ->  person_show($id);

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
        $rdf = new rdf;
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
    function show_works($id = '') {
        $rdf = new rdf;
        $class = $rdf -> find_class('work');
        $sql = "select id_cc as w 
        from rdf_concept 
        where cc_class = " . $class . "
        AND cc_library = " . LIBRARY . "
        ORDER BY id_cc desc
        limit 18
        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $sx .= '<li>';
            $sx .= $this -> show_manifestation_by_works($line['w']);
            $sx .= '</il>' . cr();
        }
        return ($sx);
    }    
    function show_manifestation_by_works($id = '', $img_size = 200, $mini = 0) {
        $img = base_url('img/no_cover.png');
        $rdf = new rdf;
        $data = $rdf -> le_data($id);
        $year = '';

        $title = '';
        $autor = '';
        $nautor = '';
        for ($r = 0; $r < count($data); $r++) {
            $line = $data[$r];
            $class = $line['c_class'];
            //echo '<br>'.$class;
            switch($class) {
                case 'hasTitle' :
                $title = $line['n_name'];
                break;
                case 'hasOrganizator' :
                if (strlen($autor) > 0) {
                    $autor .= '; ';
                    $nautor .= '; ';
                }
                $link = '<a href="' . base_url(PATH . 'v/' . $line['d_r2']) . '" class="small">';
                $autor .= $link . $line['n_name'] . ' (org.)' . '</a>';
                break;
                case 'hasAuthor' :
                if (strlen($autor) > 0) {
                    $autor .= '; ';
                    $nautor .= '; ';
                }

                if (isset($line['d_r1'])) { $idx = $line['d_r1']; } else { $idx = $line['id_cc']; }
                $link = '<a href="' . base_url(PATH . 'v/' . $idx) . '" class="small">';
                $autor .= $link . $line['n_name'] . '</a>';
                $nautor .= $line['n_name'];
                break;
            }
        }
        /* expression */
        $class = "isRealizedThrough";
        $id_cl = $rdf -> find_class($class);
        $sql = "select * from rdf_data 
        WHERE d_r1 = $id and
        d_p = $id_cl ";
        $xrlt = $this -> db -> query($sql);
        $xrlt = $xrlt -> result_array();

        if (count($xrlt) > 0) {
            $ide = $xrlt[0]['d_r2'];
            /************************************ manifestation ********/
            $class = "isEmbodiedIn";
            $id_cl = $rdf -> find_class($class);
            $sql = "select * from rdf_data 
            WHERE d_r1 = $ide and
            d_p = $id_cl ";
            $xrlt = $this -> db -> query($sql);
            $xrlt = $xrlt -> result_array();
            if (count($xrlt) > 0) {
                $idm = $xrlt[0]['d_r2'];

                /* Image */
                $dt2 = $rdf -> le_data($idm);
                //print_r($dt2);
                //echo '<hr>';
                for ($r = 0; $r < count($dt2); $r++) {
                    $line = $dt2[$r];
                    $class = $line['c_class'];
                    if ($class == 'hasCover') {
                        $img = base_url('_repositorio/image/' . $line['n_name']);
                    }
                    if ($class == 'dateOfPublication') {
                        $year = '<br>' . $line['n_name'];
                    }
                }
            }
        }

        $sx = '';
        $link = '<a href="' . base_url(PATH . 'v/' . $id) . '" style="line-height: 120%;">';
        $sx .= $link;
        $title_nr = $title;
        $sz = 45;
        if (strlen($title_nr) > $sz) {
            $title_nr = substr($title_nr, 0, $sz);
            while (substr($title_nr, strlen($title_nr) - 1, 1) != ' ') {
                $title_nr = substr($title_nr, 0, strlen($title_nr) - 1);
            }
            $title_nr = trim($title_nr) . '...';
        }

        if ($mini == 1) {
            $sx .= '<img src="' . $img . '" height="' . $img_size . '" style="box-shadow: 5px 5px 8px #888888; margin-bottom: 10px;" title="' . $title_nr . cr() . $nautor . cr() . troca($year, '<br>', '') . '">' . cr();
            $sx .= '</a>';
        } else {
            $sx .= '<img src="' . $img . '" height="200" style="box-shadow: 5px 5px 8px #888888; margin-bottom: 10px;"><br>' . cr();
            $sx .= '<span>' . $title_nr . '</span>';
            $sx .= '</a>';
            $sx .= '<br>';
            $sx .= '<i>' . $autor . '</i>';
            $sx .= $year;
        }
        //echo $line['c_class'].'<br>';
        return ($sx);
    }    
    function label($line=array())
    {
        $sx = '';
        $status = $line['i_status'];
        switch($status)
        {
            case '1':
            $corc = 'alert-primary';
            break;
            case '2':
            $corc = 'alert-secondary';
            break;
            case '3':
            $corc = 'alert-success';
            break;
            case '4':
            $corc = 'alert-warning';
            break;
            case '5':
            $corc = 'alert-info';
            break;                                                                                               
            default:
            $corc = 'alert-primary';
            break;
        }
        $sx .= '<div class="alert '.$corc.'">';
        $tombo = $this->tombo($line['i_tombo']);
        $sx .= $tombo;
        $sx .= '<br/>';
        $sx .= $line['lp_name'];
        $sx .= '<br/>';
        $sx .= $line['bs_name'];
        $sx .= '<br/>'.msg('status').': '.msg('situacao_exemplar_'.$status); 
        $sx .= '</div>';
        return($sx);
    }
    function person_show($id) {
        $rdf = new rdf;
        $data = array();
        $sx = '';

        $data = $rdf -> le($id);
        $data['person'] = $rdf -> le_data($id);
        $data['id'] = $id;

        $sx = $this -> load -> view('find/view/person', $data, true);
        return ($sx);
    }

    function show_bookshelf($id = '') {
        $rdf = new rdf;
        $class_1 = $rdf -> find_class('CDU');
        $class_2 = $rdf -> find_class('CDD');
        $class_3 = $rdf -> find_class('ClassificacaoCromatica');
        $class_work = $rdf -> find_class('work');

        $sql = "select id_cc as c, n_name 
        from rdf_concept
        inner join rdf_name ON id_n = cc_pref_term 
        where (cc_class = " . $class_1 . " or cc_class = " . $class_2 . " or cc_class = " . $class_3 . ")
        /* AND cc_library = " . LIBRARY . " */
        ORDER BY n_name
        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];

            $sql = "select n_name, id_cc as w from rdf_data as data1 
            inner join rdf_data as data2 ON data1.d_r1 = data2.d_r2 
            inner join rdf_data as data3 ON data2.d_r1 = data3.d_r2
            inner join rdf_concept ON data3.d_r1 = id_cc and cc_class= $class_work
            inner join rdf_name ON id_n = cc_pref_term
            where data1.d_r2 = " . $line['c'] . " AND cc_library = " . LIBRARY . "
            order by n_name
            ";
            $rlt2 = $this -> db -> query($sql);
            $rlt2 = $rlt2 -> result_array();

            if (count($rlt2) > 0) {
                $name = $line['n_name'];
                if (strpos($name,'#') > 0)
                {
                    $name = substr($name,0,strpos($name,'#'));
                }
                $sx .= '<div class="col-md-12"><h3>' . $name . '</h3></div>';
            }
            for ($y = 0; $y < count($rlt2); $y++) {
                $ln = $rlt2[$y];
                $sx .= '<div class="col-md-2" style="height: 190px; padding: 2px 2px 2px 2px; margin-right: 15px; margin-bottom: 15px;">' . cr();
                $sx .= $this -> show_manifestation_by_works($ln['w'], 180, 1) . cr();
                $sx .= '</div>' . cr();
            }
        }
        $sx = '<div class="row">' . $sx . '</div>';
        return ($sx);
    }
}
?>
