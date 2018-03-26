<?php
class frbr extends CI_model {
    var $limit = 12;
    function ajax($path = '', $id = '') {
        $tela = '
                 <div class="modal-header" >                    
                    <h4 class="modal-title" id="myModalLabel">Modal - ' . $path . '</h4>
                    <button type="button" class="close text-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  </div>
                  <div class="modal-body">';

        $sql = "select cl2.c_class as rg from rdf_class as cl1
                        LEFT JOIN rdf_form_class ON sc_propriety = cl1.id_c
                        LEFT JOIN rdf_class as cl2 ON cl2.id_c = sc_range
                        WHERE cl1.c_class = '" . $path . "' and cl1.c_type = 'P' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $type = $path;
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $type = $line['rg'];
        }
        /**********************************************************************************/
        $dt['type'] = $type;
        //echo '==>'.$type;
        switch($type) {
            case 'ISBN' :
                $tela .= $this -> cas_flex($path, $id, $dt);
                break;
            case 'Work' :
                $tela .= $this -> cas_flex($path, $id, $dt);
                break;
            case 'Pages' :
                $tela .= $this -> cas_flex($path, $id, $dt);
                break;
            case 'Image' :
                $tela .= $this -> upload_image($path, $id, $dt);
                break;
            default :
                $dt['type'] = $type;
                $tela .= $this -> cas_ajax($path, $id, $dt);
                break;
        }

        $tela .= '
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" style="display: none;" id="save">Incluir</button>
                    <button type="button" class="btn btn-primary" id="submt" disabled>Salvar</button>
                  </div>                  
            ';
        return ($tela);
    }

    function cas_flex($path, $id, $dt) {
        if (!isset($dt['label1'])) { $dt['label1'] = msg($path);
        }

        /* */
        $type = '';
        if (isset($dt['type'])) {
            $type = $dt['type'];
        }
        $tela = '';
        $tela .= '<span style="font-size: 75%">' . $dt['label1'] . '</span><br>';
        $tela .= '<input type="text" id="dd50" name="dd50" class="form-control">';
        $tela .= '<span style="font-size: 75%">selecione o [' . $dt['label1'] . ']</span><br>';
        $tela .= '<div id="dd51a"><select class="form-control" size=5 name="dd51" id="dd51"></select></div>';
        $tela .= '
                    <script>
                        /************ keyup *****************/
                        jQuery("#dd50").keyup(function() 
                            {
                                var $key = jQuery("#dd50").val();
                                
                                $.ajax({
                                    type: "POST",
                                    url: "' . base_url('index.php/main/ajax2/' . $path . '/' . $id . '/' . $type) . '",
                                    data:"q="+$key,
                                    success: function(data){
                                        $("#dd51a").html(data);
                                        $("#save").show();
                                    }
                                });                                            
                            });
                         /************ submit ***************/
                         jQuery("#submt").click(function() {
                            var $key = jQuery("#dd51").val();
                            $.ajax({
                                    type: "POST",
                                    url: "' . base_url('index.php/main/ajax3/' . $path . '/' . $id) . '",
                                    data: "q="+$key,
                                    success: function(data){
                                        $("#dd51a").html(data);
                                    }
                                });                           
                            /*
                            jQuery("#dialog").modal("toggle");
                            */
                         });
                         /************ insert ***************/
                         jQuery("#save").click(function() {                            
                            var $key = jQuery("#dd50").val();
                            $.ajax({
                                    type: "POST",
                                    url: "' . base_url('index.php/main/ajax4/' . $path . '/' . $id) . '",
                                    data: "q="+$key,
                                    success: function(data){
                                        $("#dd51a").html(data);
                                    }
                                });                           
                            /*
                            jQuery("#dialog").modal("toggle");
                            */
                         });                         
                    </script>';
        return ($tela);
    }

    function upload_image($path, $id, $dt) {
        $sx = '   <form action="' . base_url('index.php/main/upload/' . $path . '/' . $id) . '" method="POST" enctype="multipart/form-data">
                          Nome da imagem<br>
                          <input type="text" name="dd1" value="' . get("dd1") . '" class="form-control">
                          <br>
                          <input type="file" name="fileUpload">
                          <input type="submit" value="Enviar Imagems" class="btn btn-primary">
                       </form>';
        return ($sx);
    }

    function ajax2($path, $id, $type = '') {
        $tela = '<select name="dd51" id="dd51" size=5 class="form-control" onchange="change();">' . cr();
        $vlr = get("q");
        if (strlen($vlr) < 1) {
            $tela .= '<option></option>' . cr();
        } else {
            $vlr = troca($vlr, ' ', ';');
            $v = splitx(';', $vlr);
            $wh = '';
            for ($r = 0; $r < count($v); $r++) {
                if ($r > 0) {
                    $wh .= ' and ';
                }
                $wh .= "(n_name like '%" . $v[$r] . "%') ";
            }
            /* RANGE ***************************************************************/
            if (strlen($type) > 0) {
                $wh2 = '';
                $ww = $this -> frbr -> find_class($type);
                $wh2 = ' (cc_class = ' . $ww . ') ';

                $sql = "select * FROM rdf_class
                                        WHERE c_class_main = $ww";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $wh2 .= ' OR (cc_class = ' . $line['id_c'] . ') ';
                }
                $wh2 = ' AND (' . $wh2 . ')';
            } else {
                $wh2 = '';
            }
            /***********************************************************************/
            if (strlen($wh) > 0) {
                $sql = "select * from rdf_name
                                    INNER JOIN rdf_data ON id_n = d_literal
                                    INNER JOIN rdf_concept ON d_r1 = id_cc
                                    INNER JOIN rdf_class ON id_c = d_p 
                                    WHERE ($wh) and (n_name <> '') $wh2 
                                    LIMIT 50";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();

                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $tela .= '<option value="' . $line['id_cc'] . '">' . $line['n_name'] . '</option>' . cr();
                }

            }
        }
        $tela .= '</select>' . cr();
        $tela .= '
                    <script>                 
                        function change()
                            {
                                jQuery("#submt").removeAttr("disabled");
                            }
                            
                        jQuery("#submt").attr("disabled","disabled");
                    </script>';
        return ($tela);
    }

    function le($id) {
        $sql = "select * from rdf_concept 
                    INNER JOIN rdf_class ON cc_class = id_c
                    LEFT JOIN rdf_name ON cc_pref_term = id_n
                        WHERE id_cc = $id";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                return ($line);
            }
        } else {
            return ( array());
        }
    }

    function data_classes($d) {
        $id = $this -> find_class($d);
        $sql = "select * from rdf_concept 
                        INNER JOIN rdf_name ON cc_pref_term = id_N
                        WHERE cc_class = $id
                        ORDER BY n_name ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function data_recurso($d) {
        $id = $this -> find_class($d);
        $sql = "select d_r2 as id_c, 
                        from rdf_data 
                        INNER JOIN rdf_concept ON id_cc = d_r2
                        INNER JOIN rdf_name ON d_literal = id_n
                        WHERE d_p = $id
                        ORDER BY n_name ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function data_exclude($id) {
        $sql = "select * from rdf_data where id_d = " . $id;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $sql = "update rdf_data set
                                d_r1 = " . ((-1) * $line['d_r1']) . " ,
                                d_r2 = " . ((-1) * $line['d_r2']) . " ,
                                d_p  = " . ((-1) * $line['d_p']) . " 
                                where id_d = " . $line['id_d'];
            $rlt = $this -> db -> query($sql);
        }
    }

    function data_class($d) {
        $id = $this -> find_class($d);
        $sql = "SELECT c2.id_c as id_cc, c2.c_class as n_name, '1' as tp 
                        FROM rdf_class as c1
                        LEFT JOIN rdf_class as c2 ON c1.id_c = c2.c_class_main
                        where c1.c_class = '$d' or c2.c_class_main = '$d'
                    UNION
                    SELECT c1.id_c as C1, c1.c_class as cn1, '0' as tp
                        FROM rdf_class as c1
                        where c1.c_class = '$d'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function cas_ajax($path, $id, $dt = array()) {
        if (!isset($dt['label1'])) { $dt['label1'] = 'Nome do autor';
        }

        /* */
        $type = '';
        if (isset($dt['type'])) {
            $type = $dt['type'];
        }
        $tela = '';
        $tela .= '<span style="font-size: 75%">filtro do [' . $dt['label1'] . ']</span><br>';
        $tela .= '<input type="text" id="dd50" name="dd50" class="form-control">';
        $tela .= '<span style="font-size: 75%">selecione o [' . $dt['label1'] . ']</span><br>';
        $tela .= '<div id="dd51a"><select class="form-control" size=5 name="dd51" id="dd51"></select></div>';
        $tela .= '
                    <script>
                        /************ keyup *****************/
                        jQuery("#dd50").keyup(function() 
                            {
                                var $key = jQuery("#dd50").val();
                                
                                $.ajax({
                                    type: "POST",
                                    url: "' . base_url('index.php/main/ajax2/' . $path . '/' . $id . '/' . $type) . '",
                                    data:"q="+$key,
                                    success: function(data){
                                        $("#dd51a").html(data);
                                    }
                                });                                            
                            });
                         /************ submit ***************/
                         jQuery("#submt").click(function() {
                            var $key = jQuery("#dd51").val();
                            $.ajax({
                                    type: "POST",
                                    url: "' . base_url('index.php/main/ajax3/' . $path . '/' . $id) . '",
                                    data: "q="+$key,
                                    success: function(data){
                                        $("#dd51a").html(data);
                                    }
                                });                           
                            /*
                            jQuery("#dialog").modal("toggle");
                            */
                         });
                    </script>';
        return ($tela);
    }

    function recupera_nomes($n) {
        $sx = '';
        $n = troca($n, ' ', ';');
        $n = splitx(';', $n);
        $wh = '';
        for ($r = 0; $r < count($n); $r++) {
            if ($r > 0) { $wh .= ' AND ';
            }
            $wh .= " (n_name like '%" . $n[$r] . "%') ";
        }
        $sql = "select * from 
                        (select * from rdf_name 
                            WHERE $wh) as tabela
                     INNER JOIN rdf_concept on cc_pref_term = id_n                     
                     ORDER BY n_name ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx .= '<ul>' . cr();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $link = '<a href="' . base_url('index.php/main/a/' . $line['id_cc']) . '">';
            $sx .= '<li>';
            $sx .= '<tt>';
            $sx .= $link . $line['n_name'] . '</a>';
            $sx .= '</tt>';
            $sx .= '</li>' . cr();
        }
        $sx .= '</ul>' . cr();
        return ($sx);
    }

    function work($title = '', $subtitle = '', $form = '') {
        $id_t = $this -> frbr_name($title);
        $id_s = 0;
        if (strlen($subtitle) > 0) {
            $id_s = $this -> frbr_name($subtitle);
        }

        /* concept */
        $class = 'Work';
        $p_id = $this -> rdf_concept($id_t, $class);
        $this -> set_propriety($p_id, 'hasTitle', 0, $id_t);
        $this -> set_propriety($p_id, 'hasFormWork', $form, 0);
        if ($id_s > 0) {
            $this -> set_propriety($p_id, 'hasSubtitle', 0, $id_s);
        }
        return ($p_id);
    }

    function le_data_reverso($id) {
        $cp = '*';
        $sql = "select $cp from rdf_data as rdata
                        INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                        INNER JOIN rdf_concept ON d_r2 = id_cc 
                        INNER JOIN rdf_name on cc_pref_term = id_n
                        WHERE d_r2 = $id and d_r1 > 0";
        $sql .= ' union ';
        $sql .= "select $cp from rdf_data as rdata
                        LEFT JOIN rdf_class as prop ON d_p = prop.id_c 
                        LEFT JOIN rdf_concept ON d_r2 = id_cc 
                        LEFT JOIN rdf_name on d_literal = id_n
                        WHERE d_r2 = $id and d_r1 = 0";
        $sql .= " order by c_order, c_class, id_d";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function le_class($id) {
        $sql = "select * from rdf_class
                        WHERE c_class = '" . $id . "'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line);
        } else {
            $line = array();
            return ($line);
        }
    }

    function le_data($id) {
        $cp = '*';
        $sql = "select $cp from rdf_data as rdata
                        INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                        INNER JOIN rdf_concept ON d_r2 = id_cc 
                        INNER JOIN rdf_name on cc_pref_term = id_n
                        WHERE d_r1 = $id and d_r2 > 0";
        $sql .= ' union ';
        $sql .= "select $cp from rdf_data as rdata
                        LEFT JOIN rdf_class as prop ON d_p = prop.id_c 
                        LEFT JOIN rdf_concept ON d_r2 = id_cc 
                        LEFT JOIN rdf_name on d_literal = id_n
                        WHERE d_r1 = $id and d_r2 = 0";
        $sql .= " order by c_order, c_class, id_d";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function recupera($d, $class) {
        $rs = array();
        for ($r = 0; $r < count($d); $r++) {
            $line = $d[$r];
            if ($line['c_class'] == $class) {
                array_push($rs, $line['d_r2']);
            }
        }
        return ($rs);
    }

    function recupera_resource($id = '', $class = '') {
        $rs = array();
        $icl = $this -> frbr -> find_class($class);
        $sql = "select * from rdf_data 
                     WHERE (d_p = $icl AND d_r2 = $id) 
                     OR (d_p = $icl AND d_r1 = $id)";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $idl = round($line['d_r1']);
            if ($idl == $id) {
                $idl = round($line['d_r2']);
            }
            array_push($rs, $idl);
        }
        return ($rs);
    }

    function manifestation_show($id, $hd = 0, $ide) {
        $sx = '';
        $item = array();
        $item = $this -> frbr -> recupera_resource($id, 'isEmbodiedIn');

        $sx .= '<div class="container">' . cr();
        $sx .= '<div class="row">' . cr();
        if (count($item) > 0) {
            for ($r = 0; $r < count($item); $r++) {
                $idw = $item[$r];
                $data['id'] = $idw;
                $data['rlt'] = $this -> le_data($idw);

                $data['hd'] = $hd;
                $sx .= $this -> load -> view('find/view/manifestation', $data, true);
                /*** ITEM ****/
                $sx .= $this -> itens_show($idw);

                if (perfil("#ADM")) {
                    $sx .= '<div class="col-md-2">';
                    $link = base_url('index.php/main/item_create/' . $idw);
                    $sx .= '<a href="' . $link . '" class="btn btn-secondary">' . msg('item_add') . '</a>';
                    $sx .= '</div> ';
                }

                /**************/
            }
        } else {
            $data['id'] = $ide;
            $sx .= $this -> load -> view('find/view/manifestation_void', $data, true);
        }
        $sx .= '</div>';
        $sx .= '</div>';

        return ($sx);
    }

    function expression_show($id, $hd = 0) {
        $sx = '';
        $item = $this -> frbr -> recupera_resource($id, 'isRealizedThrough');
        if (count($item) > 0) {
            for ($r = 0; $r < count($item); $r++) {
                $idw = $item[$r];

                $data = array();
                $data['id'] = $idw;
                $data['expr'] = $this -> le_data($idw);
                $sx .= $this -> load -> view('find/view/expression', $data, true);
            }
        } else {

        }
        if (perfil("#ADM")) {
            $data = array();
            $data['id'] = $id;
            $sx .= $this -> load -> view('find/view/expression_void', $data, true);
        }
        return ($sx);
    }

    function recupera_manifestacao($id) {
        $item = $this -> frbr -> recupera_resource($id, 'isAppellationOfWork');
        $wh = '';
        for ($r = 0; $r < count($item); $r++) {
            if (strlen($wh) > 0) {
                $wh .= ' OR ';
            }
            $wh .= '(d_r1 = ' . $item[$r] . ') ';
        }
        if (strlen($wh) == 0) {
            return ( array());
        }
        $class = $this -> find_class('isAppellationOfManifestation');
        $sql = "select * from rdf_data where (" . $wh . ") and d_p = " . $class;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $dt = array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            array_push($dt, $line['d_r2']);
        }
        return ($dt);
    }

    function person_work($id) {
        $r = array();
        $sql = "select d_r1, d_p, d_r2 from rdf_data 
                    where (d_r1 = $id or d_r2 = $id)
                       AND NOT (d_r1 = 0 OR d_r2 = 0)
                ORDER BY d_r1, d_p, d_r2";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $wk = array();
        $ww = array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $p = $line['d_p'];
            $r1 = $line['d_r1'];
            if ($r1 != $id) {
                if (!isset($ww[$r1])) {
                    array_push($wk, $r1);
                }
            }
            $r1 = $line['d_r2'];
            if ($r1 != $id) {
                if (!isset($ww[$r1])) {
                    array_push($wk, $r1);
                }
            }
        }
        return ($wk);
    }

    function show_class($wk) {
        $sx = '';
        $ss = '<h5>' . msg('hasColaboration') . '</h5><ul>';
        $wks = array();
        for ($r = 0; $r < count($wk); $r++) {
            $id = $wk[$r];

            $data = $this -> le_data($id);
            for ($z = 0; $z < count($data); $z++) {
                $line = $data[$z];
                $cl = $line['c_class'];
                $vl = $line['n_name'];
                $id1 = $line['d_r1'];
                $id2 = $line['d_r2'];
                switch ($cl) {
                    case 'hasTitle' :
                        $link = '<a href="' . base_url('index.php/main/v/' . $id1) . '">';
                        array_push($wks, $id1);
                        break;
                    case 'hasAuthor' :
                        $link = '<a href="' . base_url('index.php/main/v/' . $id2) . '">';
                        $ss .= '<li>' . $link . $vl . ' (' . ($cl) . ')</a></li>' . cr();
                        break;
                    default :
                }
            }
        }
        $ss .= '</ul>';
        //$sx .= '<div class="row img-person" >' . cr();
        for ($r = 0; $r < count($wks); $r++) {
            $wk = $wks[$r];
            $sx .= '<div class="col-md-2 text-center" style="line-height: 80%; margin-top: 40px;">';
            $sx .= $this -> show_manifestation_by_works($wk);
            $sx .= '</div>';
        }
        //$sx .= '</div>' . cr();

        return ($sx);
    }

    function itens_show($id) {

        $sql = "select d_r1 as item, d_r2 as manitestation, d_p as prop from rdf_data
					INNER JOIN rdf_class ON d_p = id_c 
					where c_class = 'isExemplifiedBy' and d_r2 = " . $id;
        $rlt = $this -> db -> query($sql);
        $man = $rlt -> result_array();
        $sx = '<br>';
        $sx .= '<div class="container"><div class="row">';
        $sx .= '<div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">';
        $sx .= '<tt style="font-size: 100%;">' . msg('Item') . '</tt>';
        $sx .= '</div> ';
        for ($y = 0; $y < count($man); $y++) {
            $idm = $man[$y]['item'];
            $data['id'] = $idm;
            $data['item'] = $this -> le_data($idm);
            $sx .= $this -> load -> view('find/view/item', $data, true);
        }
        $sx .= '</div> ';
        $sx .= '</div> ';
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
        $sx .= '<table border=0 width="100%" id="samples' . $id . '" style="display: none;">';
        $sx .= '<tr class="small" style="background: #c0c0c0;">
					<th width="33%">Biblioteca</th>
					<th width="33%">local</th>
					<th width="33%">exemplar</th>
				</tr>' . cr();
        for ($y = 0; $y < count($man); $y++) {
            $idm = $man[$y]['item'];
            $data['id'] = $idm;
            $items = $this -> le_data($idm);
            $xlocal = '';
            $xowner = '';
            $ex = 0;
            $fl = '';
            for ($r = 0; $r < count($items); $r++) {
                $line = $items[$r];
                $type = $line['c_class'];

                switch ($type) {
                    case 'isOwnedBy' :
                        $owner = $line['n_name'];
                        if ($xowner != $owner) {
                            $sx .= '<tr><td>' . $owner . '</td>';
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
                        break;
                    case 'hasFileName' :
                        $sx .= '<td>';
                        $link = '<a href="'.base_url($line['n_name']).'" target="_new">';
                        $sx .= $link . msg('download') . '</a>';
                        $fl++;
                        $sx .= '</td>';
                        break;                        
                }

            }
        }
        $sx .= '</table>';
        if (count($man) == 0) {$sx = '';
        }
        return ($sx);
    }

    function work_show($id) {
        $data = array();
        $sx = '';

        $data['work'] = $this -> le_data($id);
        $data['id'] = $id;
        $sx = $this -> load -> view('find/view/work', $data, true);

        return ($sx);
    }

    function work_show_2($id) {
        $data = array();

        $prop_expression = $this -> find_class('isRealizedThrough');
        $prop_manifestation = $this -> find_class('isEmbodiedIn');
        $prop_item = $this -> find_class('isExemplifiedBy');
        $sx = '';

        $data['id'] = $id;

        /* LE DADOS SOBRE WORK */
        $data['work'] = $this -> le_data($id);

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
            $data['expression'] = $this -> le_data($ide);

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
                $data['manifestation'] = $this -> le_data($idm);

                /************************************************************ RECUPERA ITEM **********/
                $sql = "select * from rdf_data where d_r2 = " . $idm . " and d_p = " . $prop_item;
                $rlti = $this -> db -> query($sql);
                $rlti = $rlti -> result_array();
                $itens = array();

                $its = $this -> itens_show_resume($idm);
                $data['itens'] = $its;
                $sx .= $this -> load -> view('find/view/work_2', $data, true);
            }
        }
        return ($sx);
    }

    function show_rdf($url) {
        $pre = substr($url, 0, strpos($url, ':'));
        $pos = substr($url, strpos($url, ':') + 1, strlen($url));
        $uri = $this -> frbr -> rdf_prefix($url);
        $sx = '<a href="' . $uri . $pos . '" target="_new' . $url . '">' . $url . '</a>';
        return ($sx);
    }

    function rdf_prefix($url = '') {
        $pre = substr($url, 0, strpos($url, ':'));
        $pos = substr($url, strpos($url, ':') + 1, strlen($url));
        $sx = $pre;
        $sql = "select * from rdf_prefix where prefix_ref = '$pre' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line = $rlt[0];
        $uri = trim($line['prefix_url']);
        return ($uri);
    }

    function rdf_sufix($url = '') {
        $pre = substr($url, 0, strpos($url, ':'));
        $pos = substr($url, strpos($url, ':') + 1, strlen($url));
        return ($pos);
    }

    function person_show($id) {
        $data = array();
        $sx = '';

        $data = $this -> le($id);
        $data['person'] = $this -> le_data($id);
        $data['id'] = $id;

        $sx = $this -> load -> view('find/view/person', $data, true);
        return ($sx);
    }

    function form_work($id) {
        $cl = $this -> frbr -> find_class('isAppellationOfWork');

        $sx = '<h3>Work</h3>';
        $sql = "select * from rdf_data 
                        WHERE d_r1 = $id and d_p = $cl";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $work = $line['d_r2'];
            $sx .= $this -> frbr -> work_show($work);
        } else {
            $work = 0;
            $data['id'] = $id;
            $data['content'] = '<a href="#" class="btn btn-secondary" id="action_Work">Associar Work</a>';
            $sx .= $this -> load -> view('find/form/work', $data, true);
        }
        return ($sx);
    }

    function form_expression($id) {
        $cl = $this -> frbr -> find_class('isAppellationOfExpression');

        $sx = '<h3>Expression</h3>';
        $sql = "select * from rdf_data 
                        WHERE d_r1 = $id and d_p = $cl";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $work = $line['d_r2'];
            $sx .= $this -> frbr -> work_show($work);
        } else {
            $work = 0;
            $data['id'] = $id;
            $data['content'] = 'SHAME';
            /* isAppellationOfExpression */
            $sx .= $this -> load -> view('find/form/expression', $data, true);
        }
        return ($sx);
    }

    function form_manifestation($id) {
        $cl = $this -> frbr -> find_class('isAppellationOfManifestation');

        $sx = '<h3>Manifestation</h3>';

        $data['id'] = $id;
        $data['content'] = '<a href="' . base_url('index.php/main/i/' . $id . '/Manifestation/isAppellationOfManifestation') . '" class="btn btn-primary">Criar manifestação</a>';
        $sx .= $this -> load -> view('find/form/manifestation', $data, true);

        $sql = "select * from rdf_data 
                        WHERE d_r1 = $id and d_p = $cl";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $work = $line['d_r1'];
            $sx .= $this -> frbr -> manifestation_show($work, 0);
        } else {
            $work = 0;

        }
        return ($sx);
    }

    function form($id, $dt) {
        $class = $dt['cc_class'];
        $sx = '';
        /* complementos */
        switch($class) {
            default :
                $cp = 'n_name, cpt.id_cc as idcc, d_p as prop, id_d';
                $sqla = "select $cp from rdf_data as rdata
                                INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                                INNER JOIN rdf_concept as cpt ON d_r2 = id_cc 
                                INNER JOIN rdf_name on cc_pref_term = id_n
                                WHERE d_r1 = $id and d_r2 > 0";
                $sqla .= ' union ';
                $sqla .= "select $cp from rdf_data as rdata
                                LEFT JOIN rdf_class as prop ON d_p = prop.id_c 
                                LEFT JOIN rdf_concept as cpt ON d_r2 = id_cc 
                                LEFT JOIN rdf_name on d_literal = id_n
                                WHERE d_r1 = $id and d_r2 = 0";
                /*****************/
                $sql = "select * from rdf_form_class
                            INNER JOIN rdf_class ON id_c = sc_propriety
                            LEFT JOIN (" . $sqla . ") as table1 ON id_c = prop 
                        where sc_class = $class 
                        order by sc_ord, id_sc, c_order";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                $sx .= '<table width="100%" cellpadding=5>';
                $js = '';
                $xcap = '';
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $cap = msg($line['c_class']);
                    $link = '<a href="#" id="action_' . trim($line['c_class']) . '" data-toggle="modal" data-target=".bs-example-modal-lg">';
                    $link = '<a href="#" id="action_' . trim($line['c_class']) . '">';
                    $linka = '</a>';
                    $sx .= '<tr>';
                    $sx .= '<td width="25%" align="right">';
                    if ($xcap != $cap) {
                        $sx .= '<nobr><i>' . msg($line['c_class']) . '</i></nobr>';
                        $sx .= '<td width="1%">' . $link . '[+]' . $linka . '</td>';
                        $xcap = $cap;

                    } else {
                        $sx .= '&nbsp;';
                        $sx .= '<td>-</td>';
                    }
                    $sx .= '</td>';
                    $sx .= '<td style="border-bottom: 1px solid #808080;">';
                    if (strlen($line['n_name']) > 0) {
                        $linkc = '<a href="' . base_url('index.php/main/v/' . $line['idcc']) . '" class="middle">';
                        $linkca = '</a>';
                        $sx .= $linkc . $line['n_name'] . $linkca;
                        $link = ' <span id="ex' . $line['id_d'] . '" onclick="exclude(' . $line['id_d'] . ');" style="cursor: pointer;">';
                        $sx .= $link . '<font style="color: red;" title="Excluir lancamento">[X]</font>' . $linka;
                        $sx .= '</span>';
                    }
                    $sx .= '</td>';
                    $sx .= '</tr>';
                    $js .= 'jQuery("#action_' . trim($line['c_class']) . '").click(function() 
                      {
                          carrega("' . trim($line['c_class']) . '");
                          jQuery("#dialog").modal("show"); 
                      });' . cr();
                }
                $sx .= '</table>';
                break;
        }
        $sx .= '<script>
                    ' . $js . '
                    function carrega($id)
                    {
                        jQuery.ajax({
                          url: "' . base_url('index.php/main/ajax/') . '"+$id+"/"+' . $id . ',
                          context: document.body
                        })  .done(function( html ) {
                            jQuery( "#model_texto" ).html( html );
                        });
                    }                    
                </script>';
        $sx .= '   <div id="dialog" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                              <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content" id="model_texto">
                                </div>
                              </div>
                            </div>';
        $sx .= $this -> load -> view('find/modal/modal_exclude', null, true);
        return ($sx);
    }

    function form2($id, $dt) {
        $class = $dt['cc_class'];
        $sx = '';
        /* complementos */
        switch($class) {
            default :

                /*****************/
                $sql = "select * from rdf_form_class
                    INNER JOIN rdf_class ON id_c = sc_propriety 
                        where sc_class = $class 
                        order by sc_ord, c_order";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                $sx .= '<ul>';
                $js = '';
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $link = '<a href="#" id="action_' . trim($line['c_class']) . '" data-toggle="modal" data-target=".bs-example-modal-lg">';
                    $link = '<a href="#" id="action_' . trim($line['c_class']) . '">';
                    $linka = '</a>';
                    $sx .= '<li>';
                    $sx .= $link . msg($line['c_class']) . $linka;
                    $sx .= '</li>';
                    $js .= 'jQuery("#action_' . trim($line['c_class']) . '").click(function() 
                      {
                          carrega("' . trim($line['c_class']) . '");
                          jQuery("#dialog").modal("show"); 
                      });' . cr();
                }
                $sx .= '</ul>';

                break;
        }
        $sx .= '<script>
                    ' . $js . '
                    function carrega($id)
                    {
                        jQuery.ajax({
                          url: "' . base_url('index.php/main/ajax/') . '"+$id+"/"+' . $id . ',
                          context: document.body
                        })  .done(function( html ) {
                            jQuery( "#model_texto" ).html( html );
                        });
                    }                    
                </script>';
        $sx .= '   <div id="dialog" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                              <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content" id="model_texto">
                                </div>
                              </div>
                            </div>';
        return ($sx);
    }

    function show($r) {
        if (strlen($r) == 0) {
            return ('');
        }
        $sx = '';
        $sql = "select * from rdf_concept 
						INNER JOIN rdf_class as prop ON cc_class = id_c
						WHERE id_cc = " . $r;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        /****************************************** return if empty */
        if (count($rlt) == 0) {
            return ('');
        }
        /**************************************************** show **/
        $line = $rlt[0];
        $sx .= '<h3>class:' . $line['c_class'] . '</h3>';

        $cp = '*';
        $sql = "select $cp from rdf_data as rdata
						INNER JOIN rdf_class as prop ON d_p = prop.id_c 
						INNER JOIN rdf_concept ON d_r2 = id_cc 
						INNER JOIN rdf_name on cc_pref_term = id_n
						WHERE d_r1 = $r and d_r2 > 0";
        $sql .= ' union ';
        $sql .= "select $cp from rdf_data as rdata
                        LEFT JOIN rdf_class as prop ON d_p = prop.id_c 
                        LEFT JOIN rdf_concept ON d_r2 = id_cc 
                        LEFT JOIN rdf_name on d_literal = id_n
                        WHERE d_r1 = $r and d_r2 = 0";
        $sql .= " order by c_order, c_class";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $sx .= '<table width="100%" cellpadding=5>' . cr();
        $sx .= '<tr><th width=20%" class="text-right">propriety</th><th>value</th></tr>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $id = $line['id_cc'];
            $link = '<a href="' . base_url('index.php/main/a/' . $line['id_cc']) . '">';
            $linka = '</a>';
            if (strlen($line['id_cc']) == 0) {
                $link = '';
                $linka = '';
            }
            $sx .= '<tr>';
            $sx .= '<td class="text-right" style="font-size: 60%;">';
            $sx .= msg($line['c_class']);
            $sx .= '</td>';

            $sx .= '<td>';
            $sx .= $link . $line['n_name'] . $linka;
            $sx .= ' ';

            $link = '<span id="ex' . $line['id_d'] . '" onclick="exclude(' . $line['id_d'] . ');" style="cursor: pointer;">';
            $sx .= $link . '<font style="color: red;" title="Excluir lancamento">[X]</font>' . $linka;
            $sx .= '</span>';

            /********************* prefer */
            if ($line['c_class'] == 'altLabel') {
                $link = '<span id="ep' . $line['id_d'] . '" onclick="setPrefTerm(' . $line['id_d'] . ',' . $line['id_n'] . ');" style="cursor: pointer;">';
                $sx .= $link . '<font style="color: red;" title="Definir como preferencial">[pref]</font>' . $linka;
                $sx .= '</span>';
            }

            $sx .= '</td>';

            $sx .= '</tr>' . cr();
        }
        $sx .= '</table>';
        $sx .= $this -> load -> view('find/modal/modal_exclude', null, true);
        $sx .= $this -> load -> view('find/modal/modal_set_prefterm', null, true);
        return ($sx);
    }

    function item_exist($term) {
        $item = $this -> frbr -> frbr_name($term);

        $class = 'Item';
        $cl = $this -> find_class($class);
        $sql = "select * from rdf_concept
                     WHERE cc_class = $cl and cc_pref_term = $item";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line['id_cc']);
        } else {
            return (0);
        }
        exit ;
    }

    function inport_rdf($t, $class = '') {
        if (strlen($class) == 0) {
            echo 'Classe não definida na importação';
            retur('');
        }
        $ln = $t;
        $ln = troca($ln, ';', ':.');
        $ln = troca($ln, chr(13), ';');
        $ln = troca($ln, chr(10), ';');
        $lns = splitx(';', $ln);
        for ($r = 0; $r < count($lns); $r++) {
            $ln = $lns[$r];
            $ln = troca($ln, chr(9), ';');

            $l = splitx(';', $ln);
            if (count($l) == 3) {
                $prop = $l[1];
                $term = $l[2];
                $resource = $l[0];
                if ($prop == 'skosxl:is_synonymous') {
                    $prop = 'skos:altLabel';
                }
                if ($prop == 'skosxl:literalForm') {
                    $prop = 'skos:altLabel';
                }
                if ($prop == 'skosxl:isSingular') {
                    $prop = 'skos:altLabel';
                }
                switch($prop) {
                    case 'skos:prefLabel' :
                        $item = $this -> frbr -> frbr_name($term);
                        $p_id = $this -> frbr -> rdf_concept($item, $class, $resource);
                        $this -> frbr -> set_propriety($p_id, $prop, 0, $item);
                        break;
                    default :
                        $item = $this -> frbr -> frbr_name($term);
                        $p_id = $this -> frbr -> rdf_concept_find_id($resource);
                        if ($p_id > 0) {
                            $this -> frbr -> set_propriety($p_id, $prop, 0, $item);
                        }
                        break;
                }
            }
        }
        echo '<span style="color: #0000ff">Fim da importação</span>';
    }

    function rdf_concept_find_id($r) {
        $id = 0;
        $sql = "select * from rdf_concept where cc_origin = '$r'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line['id_cc']);
        }
        return ($id);
    }

    function item_add($idt, $tombo, $biblioteca, $bookcase, $aquisicao) {
        $lib = $this -> lib;
        $nrz = strzero(round($tombo) + $lib, 11);
        $nrz = $nrz . $this -> barcodes -> ean13($nrz);
        $tombo = $nrz;
        $item_nome = $this -> frbr -> frbr_name($tombo);

        $p_id = $this -> frbr -> rdf_concept($item_nome, 'Item');
        $this -> frbr -> set_propriety($p_id, 'hasRegisterId', 0, $item_nome);
        $this -> frbr -> set_propriety($p_id, 'isExemplifiedBy', $idt, 0);
        $this -> frbr -> set_propriety($p_id, 'isOwnedBy', $biblioteca, 0);
        $this -> frbr -> set_propriety($p_id, 'hasLocatedIn', $bookcase, 0);
        $this -> frbr -> set_propriety($p_id, 'wayOfAcquisition', $aquisicao, 0);
        return (true);
    }

    function item_catalog() {
        /***************************/
        $dd1 = get("dd1");
        $dd2 = get("dd2");
        $dd3 = get("dd3");
        $dd4 = get("dd4");
        /****************************************************************************/
        if ((strlen($dd1) > 0) and (strlen($dd3) > 0) and (strlen($dd4) > 0)) {

            $p_id = $this -> item_exist($dd1);
            if ($p_id == 0) {
                $item = $this -> frbr -> frbr_name($dd1);
                $class = 'Item';
                $p_id = $this -> frbr -> rdf_concept($item, $class);
                $this -> frbr -> set_propriety($p_id, 'hasIdRegister', 0, $item);

                /****************************************************** Biblioteca ****/
                $item = $dd3;
                $this -> frbr -> set_propriety($p_id, 'hasPlaceItem', $item, 0);

                /****************************************************** Aquisição *****/
                $item = $dd4;
                $this -> frbr -> set_propriety($p_id, 'hasWayOfAcquisition', 0, $item);

                /* Administrativo */

                /* quem */
                $name = 'user:' . $_SESSION['id'] . ' - ' . $_SESSION['user'];
                $item = $this -> frbr -> frbr_name($name);
                $class = 'Person';
                $p_id2 = $this -> frbr -> rdf_concept($item, $class);
                $this -> frbr -> set_propriety($p_id, 'hasIdRegister', $p_id2, 0);

                /* quando */
                $name = date("Ymd Hi");
                $item = $this -> frbr -> frbr_name($name);
                $class = 'DateTime';
                $p_id2 = $this -> frbr -> rdf_concept($item, $class);
                $this -> frbr -> set_propriety($p_id, 'hasDateTime', $p_id2, 0);

                /* status */
                $name = '[CATALOGING:1]';
                $item = $this -> frbr -> frbr_name($name);
                $class = 'ItemStatusCataloging';
                $p_id2 = $this -> frbr -> rdf_concept($item, $class);
                $this -> frbr -> set_propriety($p_id, 'hasItemStatusCataloging', $p_id2, 0);
            }
            redirect(base_url('index.php/main/a/' . $p_id));
            exit ;
        }
        //$cla2 = $this->frbr->le_class("ItemStatusCataloging");
        $cla2 = 49;
        $data['form'] = $this -> vocabularies -> list_vc_attr($cla2);
        $data['acqu'] = $this -> vocabularies -> list_vc_type('TypeOfAcquisition');
        $tela = $this -> load -> view('find/form/cat_item', $data, true);
        return ($tela);
    }

    function data_expression($id) {
        $class = "isRealizedThrough";
        $idc = $this -> frbr -> find_class($class);

        $sql = "select * from rdf_data where d_r1 = $id and d_p = $idc
                        order by d_r2, d_p";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $nz = array();
        $sx = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $id = $rlt[$r]['d_r2'];
            $d = $this -> frbr -> le_data($id);
            $nn = array();
            $n = '';
            $sx .= '<div class="col-md-2">';

            for ($y = 0; $y < count($d); $y++) {
                $line = $d[$y];
                $v = $line['c_class'];
                $n = $line['n_name'];
                $na = array();
                $na['class'] = $v;
                $na['name'] = $n;
                $na['id'] = $id;
                array_push($nn, $na);
            }
            $sx .= '</div>' . cr();
            array_push($nz, $nn);
        }
        return ($nz);
    }

    function manifestation_catalog($id) {
        /***************************/
        $dd1 = get("dd1");
        $dd2 = get("dd2");
        $dd3 = get("dd3");
        $dd4 = get("dd4");
        /****************************************************************************/
        if ((strlen($dd1) > 0) and (strlen($dd2) >= 0)) {

            $title = trim($dd1) . ': ' . trim($dd2);
            $id_n = $this -> frbr -> frbr_name($title);

            if ($id_n > 0) {
                $class = 'Work';
                $p_id = $this -> frbr -> rdf_concept($id_n, $class);
                $this -> frbr -> set_propriety($p_id, 'hasTitle', 0, $id_n);

                /* Administrativo */
            }
            redirect(base_url('index.php/main/v/' . $p_id));
            exit ;
        }
        $data = array();
        //$data['form'] = $this -> vocabularies -> list_vc_attr($cla2);
        //$data['acqu'] = $this -> vocabularies -> list_vc_type('TypeOfAcquisition');
        $tela = $this -> load -> view('find/form/cat_manifestation', $data, true);
        return ($tela);

    }

    function work_catalog() {
        /***************************/
        $dd1 = get("dd1");
        $dd2 = get("dd2");
        $dd3 = get("dd3");
        $dd4 = get("dd4");
        /****************************************************************************/
        if ((strlen($dd1) > 0) and (strlen($dd2) >= 0)) {

            if (strlen(trim($dd2)) > 0) {
                $title = trim($dd1) . ': ' . trim($dd2);
            } else {
                $title = trim($dd1);
            }
            $id_n = $this -> frbr -> frbr_name($title);

            if ($id_n > 0) {
                $class = 'Work';
                $p_id = $this -> frbr -> rdf_concept($id_n, $class);
                $this -> frbr -> set_propriety($p_id, 'hasTitle', 0, $id_n);

                /* Administrativo */
            }
            redirect(base_url('index.php/main/v/' . $p_id));
            exit ;
        }
        //$cla2 = $this->frbr->le_class("ItemStatusCataloging");
        $cla2 = 49;
        $data = array();
        //$data['form'] = $this -> vocabularies -> list_vc_attr($cla2);
        //$data['acqu'] = $this -> vocabularies -> list_vc_type('TypeOfAcquisition');
        $tela = $this -> load -> view('find/form/cat_work', $data, true);
        return ($tela);
    }

    function marc_to_frbr($m) {
        /* PREFERENCIAL NAME */
        $p_id = '';

        if (isset($m['100a'])) {
            //echo '<h1>' . $m['100a'] . '</h1>';

            /* prefLabel */
            $name = $m['100a'];
            /* NAME */
            $id = $this -> frbr_name($name);

            /* concept */
            $class = 'Person';
            $p_id = $this -> rdf_concept($id, $class);
            $this -> set_propriety($p_id, 'prefLabel', 0, $id);

            /* DATAS ASSOCIADAS AO NOME */
            $da = $m['100d'];
            $da1 = '';
            $da2 = '';
            if (strpos($da, '-')) {
                $pos = strpos($da, '-');
                $da1 = trim(substr($da, 0, $pos));
                $da2 = trim(substr($da, $pos + 1, strlen($da)));
            }
            if (strlen($da1) > 0) {
                $dai1 = $this -> frbr_name($da1);
                $dai1a = $dai1;
                $dai1 = $this -> rdf_concept($dai1, 'Date');
                $this -> set_propriety($dai1, 'prefLabel', 0, $dai1a);
                $this -> set_propriety($p_id, 'hasBorn', $dai1);

            }
            if (strlen($da2) > 0) {
                $dai2 = $this -> frbr_name($da2);
                $dai2a = $dai2;
                $dai2 = $this -> rdf_concept($dai2, 'Date');
                $this -> set_propriety($dai2, 'prefLabel', 0, $dai2a);
                $this -> set_propriety($p_id, 'hasDie', $dai2);
            }

            /* author date associated */

        }
        /* ALTERNATIVE NAMES */

        if (count($m['400']) > 0) {
            for ($r = 0; $r < count($m['400']); $r++) {
                $nm = $m['400'][$r]['400a'];

                $altn = $this -> frbr_name($nm);
                $m['400'][$r]['400z'] = $altn;
                $this -> set_propriety($p_id, 'altLabel', 0, $altn);
            }
        }

        if (count($m['670']) > 0) {
            for ($r = 0; $r < count($m['670']); $r++) {
                $nm = $m['670'][$r]['670a'];

                $altn = $this -> frbr_name($nm);
                $m['670'][$r]['670z'] = $altn;
                $this -> set_propriety($p_id, 'sourceNote', 0, $altn);
            }
        }
        //echo '<pre>' . $this -> show($p_id) . '</pre>';
        return ($m);
    }

    function search($d) {
        if (!isset($d['dd1'])) {
            return ('');
        }
        $dd1 = $d['dd1'];
        $dd1 = troca($dd1, ' ', ';') . ';';
        $dd1 = troca($dd1, "'", "´");
        $lns = splitx(';', $dd1);
        $sx = '';
        $wh = '';
        for ($r = 0; $r < count($lns); $r++) {
            if (strlen($wh) > 0) { $wh .= ' AND ';
            }
            $wh .= " (n_name like '%" . $lns[$r] . "%')";
        }
        if (strlen($wh) == 0) {
            return ('');
        }

        $sql = "select * from rdf_concept
                    INNER JOIN rdf_name ON id_n = cc_pref_term 
                    INNER JOIN rdf_class ON id_c = cc_class
                    WHERE $wh AND c_find = 1 ";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx .= '<div class="container">' . cr();
        $sx .= '<div class="row">' . cr();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $class = $line['c_class'];

            switch ($class) {
                case 'Work' :
                    $idw = $line['id_cc'];
                    $sx .= '<div class="col-lg-2 col-md-4 col-xs-3 col-sm-6 text-center" style="line-height: 80%; margin-top: 40px;">' . cr();
                    $sx .= $this -> show_manifestation_by_works($idw) . cr();
                    $sx .= '</div>' . cr();
                    break;
                default :
                    $link = '<a href="' . base_url('index.php/main/v/' . $line['id_c']) . '" target="_new">';
                    $sx .= '<div class="col-lg-2 col-md-4 col-xs-3 col-sm-6 text-center" style="line-height: 80%; margin-top: 40px;">' . cr();
                    echo '[' . $class . ']';
                    $sx .= $link;
                    $sx .= $line['n_name'];
                    $sx .= '</a>';
                    $sx .= ' (';
                    $sx .= msg($line['c_class']);
                    $sx .= ')' . cr();
                    $sx .= '</div>' . cr();
                    break;
            }
        }
        $sx .= '</div>';
        $sx .= '</div>';
        return ($sx);
    }

    function set_propriety($r1, $prop, $r2, $lit = 0) {
        /********* propriedade com o prefixo ***************/
        if ((strlen($r1) == 0) or (strlen($r2) == 0) or (strlen($prop) == 0)) {
            echo "<hr>OPS";
            echo '<br>R1=' . $r1;
            echo '<br>Prop=' . $prop;
            echo '<br>R2=' . $r2;
            echo '<br>Lit=' . $lit;
        }
        if (strpos($prop, ':')) {
            $prop = substr($prop, strpos($prop, ':') + 1, strlen($prop));
        }
        /*********************** recupera propriedade ID ***/
        $pr = $this -> find_class($prop);
        $sql = "select * from rdf_data 
						WHERE 
						((d_r1 = $r1 AND d_r2 = $r2)
							OR
						 (d_r1 = $r2 AND d_r2 = $r1))
						AND d_p = $pr 
						AND d_literal = $lit ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sql = "insert into rdf_data
								(d_r1, d_p, d_r2, d_literal)
								values
								('$r1','$pr','$r2',$lit)";
            $rlt = $this -> db -> query($sql);
        } else {

        }
    }

    function find_class($class) {
        $sql = "select * from rdf_class
                        WHERE c_class = '$class' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            echo '<h1>Ops, ' . $class . ' não localizada';
            exit ;
        }
        $line = $rlt[0];
        return ($line['id_c']);
    }

    function rdf_concept($term, $class, $orign = '') {
        $cl = $this -> find_class($class);
        $dt = date("Y/m/d H:i:s");
        if ($term == 0) {
            $sql = "select * from rdf_concept
                     WHERE cc_class = $cl and cc_created = '$dt'
                     ORDER BY id_cc";
        } else {
            if (strlen($orign) > 0) {
                $sql = "select * from rdf_concept
                        WHERE cc_class = $cl and (cc_pref_term = $term or cc_origin = '$orign')";
            } else {
                $sql = "select * from rdf_concept
                        WHERE cc_class = $cl and (cc_pref_term = $term)";
            }
        }
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $id = 0;
        $date = date("Y-m-d");

        if (count($rlt) == 0) {

            $sqli = "insert into rdf_concept
                            (cc_class, cc_pref_term, cc_created, cc_origin, cc_update)
                            VALUES
                            ($cl,$term,'$dt','$orign', '$date')";
            $rlt = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            $id = $rlt[0]['id_cc'];
        } else {
            $id = $rlt[0]['id_cc'];
            $line = $rlt[0];
            $compl = '';
            if ((strlen($orign) > 0) and ((strlen(trim($line['cc_origin'])) == 0) or ($line['cc_origin'] == 'ERRO:'))) {
                $compl = "', cc_origin = '$orign' ";
            }
            $sql = "update rdf_concept set cc_status = 1, cc_update = '$date' $compl where id_cc = " . $line['id_cc'];
            $rlt = $this -> db -> query($sql);
        }
        return ($id);
    }

    function frbr_name($n = '') {
        $n = trim($n);
        $n = troca($n, "'", "´");
        if (strlen($n) == 0) {
            return (0);
        }
        $sql = "select * from rdf_name where n_name = '" . $n . "'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sqli = "insert into rdf_name (n_name) values ('$n')";
            $rlt = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
        }
        $line = $rlt[0];
        return ($line['id_n']);
    }

    function mostra_imagem($id) {
        $rlt = $this -> frbr -> le_data($id);
        $alt_sizew = '';
        $alt_sizeh = '';
        $desc = '';
        $alt = '';
        $img = '<img src="' . base_url('img/no_cover.png') . '" height="40" title="$alt">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $class = $line['c_class'];
            switch ($class) {
                case 'hasImageWidth' :
                    $alt_sizew .= $line['n_name'];
                    break;
                case 'hasImageHeight' :
                    $alt_sizeh .= $line['n_name'];
                    break;
                case 'hasFileStorage' :
                    $img = $img = '<img src="' . base_url(trim($line['n_name'])) . '" class="img-fluid" title="$alt">';
                    break;
                case 'hasImageDescription' :
                    $desc = trim($line['n_name']);
                    break;
            }
            //echo '<br>' . $class . '=' . $line['n_name'];
        }
        if (strlen($alt_sizew . $alt_sizew) > 0) {
            if (strlen($desc) > 0) {
                $alt = $desc . ' - ';
            }
            $alt .= $alt_sizew . 'x' . $alt_sizeh . 'px';
        }
        $img = troca($img, '$alt', $alt);
        return ($img);
    }

    function viaf_inport($url) {
        $url2 = substr($url, 0, strpos($url, '#'));
        if (strlen($url2) > 0) {
            $url = $url2 . 'marc21.xml';
            $context = '';
            $t = read_link($url);
            $t = troca($t, 'mx:', '');
            $xml = simplexml_load_string($t);

            $id = '';
            $names = array();
            $dt_born = '';
            $dt_die = '';
            $genere = '';
            $tt = 0;
            $sx = '<br>';

            //echo '<pre>';
            //print_r($xml);
            //echo '</pre>';
            /************ ERRO DE ARQUIVO ***/
            if (count($xml) < 2) {
                $sx = "ERRO DE ARQUIVO - LEITURA";
                return ($sx);
            }

            for ($r = 0; $r < count($xml -> datafield); $r++) {
                $tag = '';
                foreach ($xml->datafield[$r]->attributes() as $a => $b) {
                    if ($a == 'tag') {
                        $tag = $b;
                        $totz = count($xml -> datafield[$r] -> subfield);
                        for ($z = 0; $z < $totz; $z++) {
                            $vlr = (string)$xml -> datafield[$r] -> subfield[$z];
                            foreach ($xml->datafield[$r]->subfield[$z]->attributes() as $code => $vld) {
                                //echo '<br>==>' . $tag . ' ' . $vlr . '--' . $code . '--' . $vld;
                                switch ($tag) {
                                    case '024' :
                                        if ($vld == 'a') {
                                            $id = $this -> frbr -> find_prefix($vlr);
                                        }
                                        break;
                                    case '700' :
                                        if ($vld == 'a') {
                                            $vlr = $this -> frbr -> trata_autor($vlr);
                                            $names[$vlr] = $tt;
                                            $form = 'Person';
                                            $tt++;
                                        }

                                        if ($vld == 'd') {
                                            if (strpos($vlr, '-')) {
                                                $dt_born = substr($vlr, 0, strpos($vlr, '-'));
                                                $dt_die = substr($vlr, strpos($vlr, '-') + 1, strlen($vlr));
                                            } else {
                                                $dt_born = $vlr;
                                            }
                                        }
                                        break;
                                    case '400' :
                                        if (($vld == 'a') or ($vld == 'e')) {
                                            $vlr = $this -> frbr -> trata_autor($vlr);
                                            $names['*' . $vlr] = $tt;
                                            $form = 'Person';
                                            $tt++;
                                        }

                                        if ($vld == 'd') {
                                            if (strpos($vlr, '-')) {
                                                $dt_born = substr($vlr, 0, strpos($vlr, '-'));
                                                $dt_die = substr($vlr, strpos($vlr, '-') + 1, strlen($vlr));
                                            } else {
                                                $dt_born = $vlr;
                                            }
                                        }
                                        break;
                                    case '410' :
                                        if ($vld == 'a') {
                                            $vlr = $this -> frbr -> trata_autor($vlr);
                                            $names[$vlr] = $tt;
                                            $form = 'Corporate Body';
                                            $tt++;
                                        }

                                        if ($vld == 'b') {

                                        }
                                        break;
                                    case '710' :
                                        if ($vld == 'a') {
                                            $vlr = $this -> frbr -> trata_autor($vlr);
                                            $names[$vlr] = $tt;
                                            $form = 'Corporate Body';
                                            $tt++;
                                        }

                                        if ($vld == 'b') {

                                        }
                                        break;
                                    case '375' :
                                        if (strlen($genere) == 0) {
                                            $genere = $vlr;
                                        }
                                        break;
                                    default :
                                        //  $sx .= '<tt>' . $tag . '</tt><br>';
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }
        /* create */
        if ((count($names) > 0) and (strlen($id) > 0)) {
            $tt = 0;
            foreach ($names as $autor => $value) {
                //echo '<br>'.$tt.'-'.$autor.'=>'.$value;
                $hd = 0;
                if (substr($autor, 0, 1) == '*') { $hd = 1;
                }
                $autor = troca($autor, '*', '');
                if ($tt == 0) {
                    $name_pref = troca($autor, "'", "´");
                    $id_t = $this -> frbr -> frbr_name($name_pref);
                    $p_id = $this -> frbr -> rdf_concept($id_t, $form, $id);
                    $this -> frbr -> set_propriety($p_id, 'prefLabel', 0, $id_t);

                    if (strlen($dt_born) > 0) {
                        $id_t = $this -> frbr -> frbr_name($dt_born);
                        $this -> frbr -> set_propriety($p_id, 'hasBorn', 0, $id_t);
                    }
                    if (strlen($dt_die) > 0) {
                        $id_t = $this -> frbr -> frbr_name($dt_die);
                        $this -> frbr -> set_propriety($p_id, 'hasDie', 0, $id_t);
                    }
                } else {
                    $name_pref = troca($autor, "'", "´");
                    //echo mb_detect_encoding($name_pref).' '.$name_pref;

                    $id_t = $this -> frbr -> frbr_name($name_pref);
                    if ($hd == 1) {
                        $this -> frbr -> set_propriety($p_id, 'hiddenLabel', 0, $id_t);
                    } else {
                        $this -> frbr -> set_propriety($p_id, 'altLabel', 0, $id_t);
                    }
                }
                $tt++;
            }
            /* 375 genero */
            if (strlen($genere) > 0) {
                $p_id2 = $this -> frbr -> find_conecpt($genere);
                if ($p_id2 > 0) {
                    $this -> frbr -> set_propriety($p_id, 'hasGender', $p_id2, 0);
                } else {
                    $sx .= '
                                        <div class="alert alert-danger" role="alert">
                                          <strong>Warning!</strong> Genero não localizado <b>' . $genere . '</b>
                                        </div>
                                ';
                }

            }

            $sx .= '
                        <div class="alert alert-success" role="alert">
                          <strong>Sucesso!</strong> Importação finalizada com sucesso.
                        </div>
                ';
            $sx .= '<h1>' . $name_pref . '</h1>';
            $sx .= '<h4>' . $dt_born . '-' . $dt_die . '</h4>';
            if (strlen($genere) > 0) {
                $sx .= 'Genero: ' . $genere;
            }
            $sx .= '<ul>';
            foreach ($names as $autor => $value) {
                $sx .= '<li>' . $autor . '</li>' . cr();
            }
            $sx .= '</ul>';
            $sx .= '<br>';
            return ($sx);
        }
    }

    function geonames_inport($url) {
        if (strpos($url, 'www.geonames.org')) {
            $url = troca($url, 'http://www.geonames.org/', '|');
            $url = substr($url, 0, strpos($url, '/')) . '/about.rdf';
            $url = troca($url, '|', 'http://sws.geonames.org/');
        }
        $url2 = $url;
        $urlo = troca($url, 'http://sws.geonames.org/', 'gn:');
        $urlo = troca($urlo, '/about.rdf', '');

        if (strpos($url, 'sws.geonames.org')) {
            $context = '';
            $t = read_link($url);
            $t = troca($t, 'gn:', '');
            $t = troca($t, 'wgs84_pos:', '');
            $xml = simplexml_load_string($t);

            $id = '';
            $name = '';
            $names = array();
            $countryCode = '';
            $population = '';
            $lat = '';
            $long = '';
            $tt = 0;
            $sx = '<br>';

            //echo '<pre>';
            //print_r($xml);
            //echo '</pre>';
            /************ ERRO DE ARQUIVO ***/
            if (count($xml) < 1) {
                $sx = "ERRO DE ARQUIVO - LEITURA";
                return ($sx);
            }
            for ($r = 0; $r < count($xml -> Feature); $r++) {
                $tag = '';
                //foreach ($xml->datafield[$r]->attributes() as $a => $b) {
                foreach ($xml->Feature[$r] as $a => $b) {

                    //echo '<br>' . $a . '--' . $b;
                    switch($a) {
                        case 'name' :
                            $name = trim($b);
                            echo '<h4>' . $name . '</h4>';
                            break;
                        case 'countryCode' :
                            break;
                        case '' :
                            break;
                        case '' :
                            break;
                        case '' :
                            break;
                        case 'alternateName' :
                            foreach ($xml->Feature[$r]->alternateName[0]->attributes() as $c => $d) {
                                echo "<hr>====>" . $c . "===" . $d;
                            }
                            break;
                    }
                }
            }

            /* create */
            $id_t = $this -> frbr -> frbr_name($name);
            $form = 'Place';
            $id = 0;
            $p_id = $this -> frbr -> rdf_concept($id_t, $form, $urlo);
            $this -> frbr -> set_propriety($p_id, 'prefLabel', 0, $id_t);

            if (strlen($lat) > 0) {
                $id_t = $this -> frbr -> frbr_name($lat);
                $this -> frbr -> set_propriety($p_id, 'lat', 0, $id_t);
            }
            if (strlen($long) > 0) {
                $id_t = $this -> frbr -> frbr_name($long);
                $this -> frbr -> set_propriety($p_id, 'long', 0, $id_t);
            }

            $sx .= '
                        <div class="alert alert-success" role="alert">
                          <strong>Sucesso!</strong> Importação finalizada com sucesso.
                        </div>
                ';
            $sx .= '<h1>' . $name . '</h1>';
            $sx .= '<h4>' . $lat . '-' . $long . '</h4>';

            $sx .= '<ul>';
            foreach ($names as $autor => $value) {
                $sx .= '<li>' . $autor . '</li>' . cr();
            }
            $sx .= '</ul>';
            $sx .= '<br>';
            return ($sx);
        }
    }

    function find_conecpt($term) {
        $rs = 0;
        $sql = "select * from rdf_data
                        INNER JOIN rdf_name ON d_literal = id_n
                        WHERE n_name like '%" . $term . "%' order by id_d limit 1";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $rs = $line['d_r1'];
        }
        return ($rs);
    }

    function trata_autor($n) {
        $n = (string)$n;
        $n = trim($n);

        //echo '<h3>'.$n.'</h3>';
        //echo hex_dump($n);

        # RULE 3 - special chars
        for ($r = 1; $r < 32; $r++) {
            $n = troca($n, chr($r), '');
        }

        # RULE 2 - dois espacos
        while (strpos($n, '  ')) {
            $n = troca($n, '  ', ' ');
        }
        # RULE 1 - ponto no final, virgula ou dois pontos no final
        $final = substr($n, strlen($n) - 1, 1);

        if (($final == '.') or ($final == ';') or ($final == ',')) {
            $n = trim(substr($n, 0, strlen($n) - 1));
        }
        return ($n);
    }

    function find_prefix($url) {
        $sql = "select * from rdf_prefix";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $prefix = 'ERRO:';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $pre = trim($line['prefix_url']);

            if ($pre == substr($url, 0, strlen($pre))) {
                $prefix = $line['prefix_ref'] . ':';
                $prefix .= substr($url, strlen($pre), strlen($url));
            }
        }
        return ($prefix);
    }

    function form_class() {
        $admin = perfil("#ADM");

        $cp = 'id_sc, sc_ativo, sc_ord, tb1.c_class as c1, tb2.c_class as c2, tb3.c_class as c3';
        $sql = "select $cp from rdf_form_class 
                        INNER JOIN rdf_class as tb1 ON sc_class = tb1.id_c
                        INNER JOIN rdf_class as tb2 ON sc_propriety = tb2.id_c
                        LEFT JOIN rdf_class as tb3 ON sc_range = tb3.id_c
                        order by c1, sc_ord, c2, c3
                        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';
        $link = '<a href="#" class="btn btn-secondary" onclick="newwin(\'' . base_url('index.php/main/pop_config/forms/') . '\',800,600);">Novo registro</a>';
        $sx .= '<br>' . $link;
        $sx .= '<table width="100%">' . cr();
        $sx .= '<tr style="border-bottom: 2px solid #505050;">
                        <th width="30%">' . msg('resource') . '</th>
                        <th width="35%">' . msg('propriety') . '</th>
                        <th width="30%">' . msg('range') . '</th>
                        <th width="5%">' . msg('ed') . '</th>';
        if ($admin == 1) {
            $sx .= '            <th>ac</th>' . cr();
        }
        $sx .= '            </tr>' . cr();
        $x = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $st = $line['sc_ativo'];
            if ($st == '1') {
                $st = '';
                $sta = '';
            } else {
                $st = '<s>';
                $sta = '</s>';
            }

            if ($x == $line['c1']) {
                $sx .= '<tr style="border-top: 1px solid #a0a0a0;">';
                $sx .= '<td></td>';
            } else {
                $sx .= '<tr style="border-top: 3px solid #a0a0a0; ' . $st . '">';
                $x = $line['c1'];
                $sx .= '<td><b>' . $st . $line['c1'] . $sta . '</b></td>';
            }

            $sx .= '<td>' . $st . msg($line['c2']) . ' (' . $line['c2'] . ')' . $sta . '</td>';
            $sx .= '<td>' . $st . msg($line['c3']) . $sta . '</td>';
            $sx .= '<td align="center">' . $st . ($line['sc_ord']) . $sta . '</td>';
            if ($admin == 1) {
                $link = '<a href="#" onclick="newwin(\'' . base_url('index.php/main/pop_config/forms/' . $line['id_sc']) . '\',800,600);">[ed]</a>';
                $sx .= '<td align="center">' . $link . '</td>';
            }
            $sx .= '</tr>' . cr();
        }
        $sx .= '</table>';
        return ($sx);
    }

    function show_works_2($id = '') {
        $class = $this -> find_class('work');
        $sql = "select id_cc as w 
                            from rdf_concept 
                            where cc_class = " . $class . "
                            ORDER BY id_cc desc
                            limit 18
                            ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<br><br><h4>' . msg('last_buy') . '</h4>';
        $sx .= '<div class="row">' . cr();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $sx .= '<div class="col-lg-2 col-md-4 col-xs-3 col-sm-6 text-center" style="line-height: 80%; margin-top: 40px;">';
            $sx .= $this -> show_manifestation_by_works($line['w']);
            $sx .= '</div>';
        }
        $sx .= '</div>' . cr();
        return ($sx);
    }

    function show_works($id = '') {
        $class = $this -> find_class('work');
        $sql = "select id_cc as w 
                            from rdf_concept 
                            where cc_class = " . $class . "
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

    function show_manifestation_by_works($id = '') {
        $img = base_url('img/no_cover.png');
        $data = $this -> le_data($id);
        $year = '';

        $title = '';
        $autor = '';
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
                    }
                    $link = '<a href="' . base_url('index.php/main/v/' . $line['id_cc']) . '" class="small">';
                    $autor .= $link . $line['n_name'] . ' (org.)' . '</a>';
                    break;
                case 'hasAuthor' :
                    if (strlen($autor) > 0) {
                        $autor .= '; ';
                    }

                    //echo '<hr>';
                    $link = '<a href="' . base_url('index.php/main/v/' . $line['id_cc']) . '" class="small">';
                    $autor .= $link . $line['n_name'] . '</a>';
                    break;
            }
        }
        /* expression */
        $class = "isRealizedThrough";
        $id_cl = $this -> find_class($class);
        $sql = "select * from rdf_data 
                            WHERE d_r1 = $id and
                                    d_p = $id_cl ";
        $xrlt = $this -> db -> query($sql);
        $xrlt = $xrlt -> result_array();

        if (count($xrlt) > 0) {
            $ide = $xrlt[0]['d_r2'];
            /************************************ manifestation ********/
            $class = "isEmbodiedIn";
            $id_cl = $this -> find_class($class);
            $sql = "select * from rdf_data 
                            WHERE d_r1 = $ide and
                                    d_p = $id_cl ";
            $xrlt = $this -> db -> query($sql);
            $xrlt = $xrlt -> result_array();
            if (count($xrlt) > 0) {
                $idm = $xrlt[0]['d_r2'];

                /* Image */
                $dt2 = $this -> le_data($idm);
                //print_r($dt2);
                //echo '<hr>';    
                for ($r = 0; $r < count($dt2); $r++) {
                    $line = $dt2[$r];
                    $class = $line['c_class'];
                    if ($class == 'hasCover') {
                        $img = base_url('_repositorio/image/' . $line['n_name']);
                    }
                    if ($class == 'dateOfPublication')
                        {
                            $year = '<br>'.$line['n_name'];
                        }
                }
            }
        }

        $sx = '';
        $link = '<a href="' . base_url('index.php/main/v/' . $id) . '" style="line-height: 120%;">';
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
        $sx .= '<img src="' . $img . '" height="200" style="box-shadow: 5px 5px 8px #888888; margin-bottom: 10px;"><br>' . cr();
        $sx .= '<span>' . $title_nr . '</span>';
        $sx .= '</a>';
        $sx .= '<br>';
        $sx .= '<i>' . $autor . '</i>';
        $sx .= $year;
        //echo $line['c_class'].'<br>';
        return ($sx);
    }

    function expressions($id) {
        $class = "isRealizedThrough";
        $class = $this -> find_class($class);
        $sql = "select d_r2 as id from rdf_data where d_r1 = $id and d_p = $class ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $rs = array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            array_push($rs, $line['id']);
        }
        return ($rs);

    }

    function changePrefTerm($id, $it) {
        $id = round($id);
        $it = round($it);
        if (($id > 0) and ($it > 0)) {
            $sx = '<h1>Updating...</h1>';
            $sql = "select * from rdf_data where id_d = " . $id;
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();

            if (count($rlt) > 0) {
                $line = $rlt[0];
                $idz = $line['d_literal'];
                $c = $line['d_r1'];

                $class = $this -> find_class('prefLabel');
                $sql = "select * from rdf_data where d_p = $class and d_r1 = " . $c;
                $xrlt = $this -> db -> query($sql);
                $xrlt = $xrlt -> result_array();
                $line2 = $xrlt[0];

                $idt1 = $line['id_d'];
                $it1 = $line['d_literal'];
                $idc = $line['d_r1'];
                /* conecpt */

                $idt2 = $line2['id_d'];
                $it2 = $line2['d_literal'];

                $sql = "update rdf_data set d_literal = $it1 where id_d = $idt2";
                $zrlt = $this -> db -> query($sql);
                $sql = "update rdf_data set d_literal = $it2 where id_d = $idt1";
                $zrlt = $this -> db -> query($sql);
                $sql = "update rdf_concept set cc_pref_term = $it1 where id_cc = $idc";
                $zrlt = $this -> db -> query($sql);
                $sx .= '<meta http-equiv="Refresh" content="0">';
            }

        } else {
            $sx = 'ERRO!';
        }
        return ($sx);
    }

    function form_msg_ed($id = '') {
        $form = new form;
        $form -> id = $id;
        $cp = array();
        array_push($cp, array('$H8', 'id_msg', '', false, true));
        array_push($cp, array('$S100', 'msg_term', msg('label'), false, false));
        array_push($cp, array('$S100', 'msg_label', msg('description'), true, true));

        $tela = $form -> editar($cp, 'msg');

        if ($form -> saved) {
            msg('MAKE_MESSAGES');
            $tela .= '
                        <script>
                            window.opener.location.reload();
                            close();
                        </script>
                        ';
        }
        return ($tela);

    }

    function form_class_ed($id) {
        $form = new form;
        $form -> id = $id;
        $cp = array();
        array_push($cp, array('$H8', 'id_sc', '', false, true));

        $sqlc = "select * from rdf_class where c_type = 'C'";
        $sqlp = "select * from rdf_class where c_type = 'P'";
        array_push($cp, array('$Q id_c:c_class:' . $sqlc, 'sc_class', msg('resource'), true, true));
        array_push($cp, array('$Q id_c:c_class:' . $sqlp, 'sc_propriety', msg('propriety'), true, true));
        array_push($cp, array('$Q id_c:c_class:' . $sqlc, 'sc_range', msg('range'), true, true));
        array_push($cp, array('$[1:99]', 'sc_ord', msg('ordem'), true, true));
        array_push($cp, array('$O 1:Ativo&0:Inativo', 'sc_ativo', msg('ativo'), true, true));

        $tela = $form -> editar($cp, 'rdf_form_class');

        if ($form -> saved) {
            $tela .= '
                        <script>
                            window.opener.location.reload();
                            close();
                        </script>
                        ';
        }
        return ($tela);
    }

    function rdf_update_set($id) {
        $date = date("Y-m-d");
        $sql = "update rdf_concept set cc_status = 9, cc_update = '$date' where id_cc = " . $id;
        $rlt = $this -> db -> query($sql);
    }

    function viaf_update() {
        $sx = '';
        $date = date("Y-m-d");
        $tela = '';
        $class = $this -> find_class('Person');
        $wh = ' AND (cc_class = ' . $class . ') ';

        $sql = "select * from rdf_concept
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        WHERE cc_origin <> '' $wh AND (cc_update <> '$date' or cc_update is null)
                        ORDER BY id_cc limit 1";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $data = $rlt[0];
            $this -> frbr -> rdf_update_set($data['id_cc']);
            $url = $this -> frbr -> rdf_prefix($data['cc_origin']);
            $url .= $this -> frbr -> rdf_sufix($data['cc_origin']) . '/#';
            $tela = $this -> frbr -> viaf_inport($url);
            $tela .= '<meta http-equiv="refresh" content="1;url=' . base_url('index.php/main/config/authority/update') . '" />';
        }
        return ($tela);
    }

    function authority_class() {
        $sx = '';
        $class = $this -> find_class('Person');
        $wh = ' AND (cc_class = ' . $class . ') ';

        $sql = "select * from rdf_concept
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        WHERE cc_origin <> '' $wh
                        ORDER BY cc_update DESC, n_name    
                   ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx .= '<table width="100%">';
        $sx .= '<tr>
                        <th>' . msg('authority') . '</th>
                        <th>' . msg('conecpt') . '</th>
                        <th>' . msg('last_update') . '</th>
                        <th>' . msg('status') . '</th>
                    </tr>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];

            $link = '<a href="' . base_url('index.php/main/v/' . $line['id_cc']) . '" class="_new' . $line['id_cc'] . '">';
            $linka = '</a>';

            $sx .= '<tr style="border-top: 1px solid #a0a0a0;">';
            $sx .= '<td>';
            $sx .= $link . $line['n_name'] . $linka;
            $sx .= '</td>';

            $sx .= '<td>';
            $sx .= $link . $line['cc_origin'] . $linka;
            $sx .= '</td>';

            $sx .= '<td>';
            $sx .= $link . stodbr($line['cc_update']) . $linka;
            $sx .= '</td>';

            $sx .= '<td>';
            $sx .= msg('status_' . $line['cc_status']);
            $sx .= '</td>';

            $sx .= '</tr>';
        }
        $sx .= '</table>';
        return ($sx);
    }

    function btn_editar($id) {
        $sx = '<a href="' . base_url('index.php/main/a/' . $id) . '" class="btn btn-secondary">editar</a>';
        return ($sx);
    }

    function btn_update($id) {
        $sx = '<a href="' . base_url('index.php/main/authority_inport_rdf/' . $id) . '" class="btn btn-secondary">atualizar dados</a> ';
        $sx .= '<a href="' . base_url('index.php/main/authority_cutter/' . $id) . '" class="btn btn-secondary">atualizar Cutter</a>';

        return ($sx);
    }

    function related($id) {
        $pg = round('0' . get("pg"));
        $limit = $this -> limit;
        $offset = $limit * $pg;
        /******************************************** by manifestation ********/
        $cl1 = $this -> find_class('isEmbodiedIn');
        $cl2 = $this -> find_class('isRealizedThrough');

        /** div **/
        $sx = '<div class="container">' . cr();
        $sx .= '<div class="row">' . cr();

        $sql = "SELECT dd3.d_r1 as w, count(*) as mn FROM `rdf_data` as dd1 
                    left JOIN rdf_data as dd2 ON dd1.d_r1 = dd2.d_r2 
                    left JOIN rdf_data as dd3 ON dd2.d_r1 = dd3.d_r2 
                    LEFT JOIN rdf_class ON dd2.d_p = id_c
                where dd1.d_r2 = $id and dd2.d_p = 88 and dd3.d_p = 37
                group by w";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $pags = $this -> pagination($rlt);
        for ($r = 0; $r < count($rlt); $r++) {
            if (($r >= $offset) and ($r < ($offset + $limit))) {
                $sx .= '<div class="col-lg-2 col-md-4 col-xs-3 col-sm-6 text-center" style="line-height: 80%; margin-top: 40px;">' . cr();
                $line = $rlt[$r];
                $idm = $line['w'];
                $sx .= $this -> show_manifestation_by_works($idm, 0, 0);
                if ($line['mn'] > 1) {
                    $sx .= '<br>';
                    $sx .= '<a href="' . base_url('index.php/main/v/' . $idm) . '" class="small">';
                    $sx .= '<span style="color:red"><i>' . msg('see_others_editions') . '</i></span>';
                    $sx .= '</a>' . cr();
                }
                $sx .= '</div>';
            }
        }

        /******************************************** by expression ***********/
        if (count($rlt) == 0) {
            $sql = "SELECT dd2.d_r1 as w, count(*) as mn FROM `rdf_data` as dd1 
                    left JOIN rdf_data as dd2 ON dd1.d_r1 = dd2.d_r2 
                    /* left JOIN rdf_data as dd3 ON dd2.d_r1 = dd3.d_r2 */ 
                    LEFT JOIN rdf_class ON dd2.d_p = id_c
                where dd1.d_r2 = $id and dd2.d_p = 37
                group by w ";
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            $pags = $this -> pagination($rlt);
            for ($r = 0; $r < count($rlt); $r++) {
                if (($r >= $offset) and ($r < ($offset + $limit))) {
                    $sx .= '<div class="col-lg-2 col-md-4 col-xs-3 col-sm-6 text-center" style="line-height: 80%; margin-top: 40px;">' . cr();
                    $line = $rlt[$r];
                    $idm = $line['w'];
                    $sx .= $this -> show_manifestation_by_works($idm, 0, 0);
                    if ($line['mn'] > 1) {
                        $sx .= '<br>';
                        $sx .= '<a href="' . base_url('index.php/main/v/' . $idm) . '" class="small">';
                        $sx .= '<span style="color:red"><i>' . msg('see_others_editions') . '</i></span>';
                        $sx .= '</a>' . cr();
                    }
                    $sx .= '</div>';
                }
            }
        }

        /** div **/
        $sx .= '</div>';
        $sx .= '</div>';

        return ($pags . $sx);
    }

    function pagination($t) {
        $pg = round('0' . get("pg"));
        $t = count($t);
        $l = $this -> limit;
        /***************************** math *************/
        if ($l == 0) {
            return ('');
        }
        $p = ($t / $l);
        $p = (int)$p;
        if (($t / $l) > $p) { $p++;
        }

        $sx = '<div class="container">' . cr();
        $sx .= '<div class="row">' . cr();
        $sx .= '  
            <nav aria-label="Page navigation example">
              <ul class="pagination">' . cr();
        $ds = 'disabled';
        if ($pg > 0) { $ds = '';
        }
        $sx .= '<li class="page-item ' . $ds . '"><a class="page-link" href="?pg=' . ($pg - 1) . '">Previous</a></li>' . cr();
        for ($r = 0; $r < $p; $r++) {
            $ac = '';
            if ($pg == $r) { $ac = 'active';
            }
            $sx .= '<li class="page-item ' . $ac . '"><a class="page-link " href="?pg=' . $r . '">' . ($r + 1) . '</a></li>' . cr();
        }
        $ps = 'disabled';
        if ($pg < ($p - 1)) { $ps = '';
        }
        $sx .= '<li class="page-item ' . $ps . '"><a class="page-link " href="?pg=' . ($pg + 1) . '">Next</a></li>' . cr();
        $sx .= '</ul></nav>' . cr();
        $sx .= '</div>';
        $sx .= '</div>';
        return ($sx);
    }

    function recover_work_with_manifestation($id) {
        $prop = $this -> find_class('Expression');
        $sql = "select * from rdf_data as dt1 
								where dt1.d_r2 = $id
										and dt1.d_p = $prop";
        echo $sql;
    }

    function bookcase($id = '', $prop = 'hasClassificationCDU') {
        $propid = $this -> find_class($prop);
        $sql = "select * from rdf_data
                    INNER JOIN rdf_concept ON d_r2 = id_cc
                    INNER JOIN rdf_name ON cc_pref_term = id_n 
                    where d_p = " . $propid . "
                    ORDER BY n_name";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $idm = $line['d_r1'];
            $note = $line['n_name'];
            $sx .= $this -> frbr -> show_manifestation_by_works($idm);
        }
        return ($sx);
    }

    function index_other($lt = '', $class = 'isPublisher') {
        $f = $this -> find_class($class);

        $sql = "select d_r2, n_name, id_cc from rdf_data
						LEFT JOIN rdf_concept on d_r2 = id_cc  
						LEFT JOIN rdf_name ON cc_pref_term = id_n
						where d_P = " . $f . " 
						GROUP BY d_r2, n_name, id_cc
						ORDER BY n_name";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<ul>';
        $l = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $xl = substr($line['n_name'], 0, 1);
            if ($xl != $l) {
                $sx .= '<h4>' . $xl . '</h4>';
                $l = $xl;
            }
            $link = '<a href="' . base_url('index.php/main/v/' . $line['id_cc']) . '">';
            $name = $link . $line['n_name'] . '</a>';
            $sx .= '<li>' . $name . '</li>' . cr();
        }
        $sx .= '<ul>';
        return ($sx);
    }

    function index_author($lt = '') {
        $class = "Person";
        $f = $this -> find_class($class);

        $sql = "select * from rdf_concept 
						INNER JOIN rdf_name ON cc_pref_term = id_n
						where cc_class = " . $f . " 
						ORDER BY n_name";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<ul>';
        $l = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $xl = substr($line['n_name'], 0, 1);
            if ($xl != $l) {
                $sx .= '<h4>' . $xl . '</h4>';
                $l = $xl;
            }
            $link = '<a href="' . base_url('index.php/main/v/' . $line['id_cc']) . '">';
            $name = $link . $line['n_name'] . '</a>';
            $sx .= '<li>' . $name . '</li>' . cr();
        }
        $sx .= '<ul>';
        return ($sx);
    }

    function classes_lista() {
        /**************** class *************************/
        $sql = "select * from rdf_class where c_type = 'C' order by c_type, c_class";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';
        $tp = '';
        $lg = array('C' => 'Classe', 'P' => 'Propriedade');
        $sx .= '<div class="row">';
        $sx .= '<div class="col-md-1">';
        $sx .= '<b>' . $lg['C'] . '</b>';
        $sx .= '</div>';

        $sx .= '<div class="col-md-5">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $link = '<a href="' . base_url('index.php/main/vocabulary_ed/' . $line['id_c']) . '">';

            $sx .= msg($line['c_class']);
            $sx .= ' (' . $link . $line['c_class'] . '</a>' . ')';
            $sx .= '<br>';
        }
        $sx .= '</div>';

        /**************** propriety **********************/
        $sql = "select * from rdf_class where c_type = 'P' order by c_type, c_class";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx .= '<div class="col-md-1">';
        $sx .= '<b>' . $lg['P'] . '</b>';
        $sx .= '</div>';

        $sx .= '<div class="col-md-5">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $xtp = $line['c_type'];
            $link = '<a href="' . base_url('index.php/main/vocabulary_ed/' . $line['id_c']) . '">';
            $sx .= msg($line['c_class']);
            $sx .= ' (' . $link . $line['c_class'] . '</a>' . ')';
            $sx .= '<br>';
        }
        $sx .= '</div>';
        $sx .= '</div>';
        return ($sx);
    }

    function vv($id) {
        $data = $this -> frbr -> le($id);
        if (count($data) == 0) {
            $this -> load -> view('error', $data);
        } else {
            $tela = '';
            if (strlen($data['n_name']) > 0) {
                $tela .= '<div class="row">';
                $tela .= '<div class="col-md-12">';
                $linkc = '<a href="' . base_url('index.php/main/v/' . $id) . '" class="middle">';
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
                $tela .= '<a href="' . base_url('index.php/main/a/' . $id) . '" class="btn btn-secondary">' . msg('edit') . '</a>';
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
                        $tela .= '<div class="container"><div class="row">' . cr();
                        $tela .= $this -> frbr -> show_class($wks);
                        $tela .= '</div></div>' . cr();
                    }

                    break;
                case 'Work' :
                    /* Modo 2 */
                    $tela .= $this -> frbr -> work_show_2($id);
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
        }
        return ($tela);
    }

    function tombo_file() {
        $prop = $this -> find_class('hasFileName');
        $sql = "select count(*) as total from rdf_data where d_p = " . $prop;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $line = $rlt[0];
        $id = ($line['total'] + 1);
        return ($id);
    }

    function item_add_file($id) {
        $lib = $this -> lib;
        $tombo = $this -> tombo_file();
        $nrz = strzero(round($tombo) + $lib, 11);
        $nrz = $nrz . $this -> barcodes -> ean13($nrz);
        $tombo = 'FILE' . $nrz;

        /* SAVE FILE */
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir);
        }
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $ok = 0;
        if ($FileType == 'pdf') {
            $ok = 1;
            $biblioteca = get("dd2");
            /******/
            $target_dir .= date("Y");
            if (!is_dir($target_dir)) { mkdir($target_dir);
            }
            /******/
            $target_dir .= '/' . date("m");
            if (!is_dir($target_dir)) { mkdir($target_dir);
            }

            /*****************/
            $target_file = $target_dir . '/' . $tombo . '.' . $FileType;
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
            $item_nome = $this -> frbr -> frbr_name($tombo);
            $item_filename = $this -> frbr -> frbr_name($target_file);

            $p_id = $this -> frbr -> rdf_concept($item_nome, 'Item');
            $this -> frbr -> set_propriety($p_id, 'hasRegisterId', 0, $item_nome);
            $this -> frbr -> set_propriety($p_id, 'isExemplifiedBy', $id, 0);
            $this -> frbr -> set_propriety($p_id, 'isOwnedBy', $biblioteca, 0);
            $this -> frbr -> set_propriety($p_id, 'hasFileName', 0, $item_filename);
            //$this -> frbr -> set_propriety($p_id, 'wayOfAcquisition', $aquisicao, 0);
        }
        return (true);
    }

}
?>
