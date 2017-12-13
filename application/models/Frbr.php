<?php
class frbr extends CI_model {

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

        switch($type) {
            case 'Date' :
                $dt['label1'] = 'Data';
                $tela .= $this -> cas_ajax($path, $id, $dt);
                break;
            case 'FormWork' :
                $dt['label1'] = 'Formato';
                $tela .= $this -> cas_ajax($path, $id, $dt);
                break;
            case 'Agent' :
                $tela .= $this -> cas_ajax($path, $id, $dt);
                break;
            case 'hasAuthor' :
                $tela .= $this -> cas_ajax($path, $id, $dt);
                break;
            case 'hasOrganizator' :
                $tela .= $this -> cas_ajax($path, $id, $dt);
                break;
            case 'Image' :
                $tela .= $this -> upload_image($path, $id, $dt);
                break;
            default :
                $dt['type'] = $type;
                $tela .= $this -> cas_ajax($path, $id, $dt);
                break;
                $tela .= '
                        <div class="alert alert-danger" role="alert">
                          <strong>Error! (544)</strong> Método não implementado "' . $path . ' - ' . $type . '".
                        </div>
                        ';
                break;
        }

        $tela .= '
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="submt" disabled>Salvar</button>
                  </div>                  
            ';
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

    function manifestation_show($id, $hd = 0) {
        $sx = '';
        $item = array();
        $item = $this -> frbr -> recupera_resource($id, 'isEmbodiedIn');
        
		$sx .= '<div class="container">'.cr();
		$sx .= '<div class="row">'.cr();
        if (count($item) > 0) {
            for ($r = 0; $r < count($item); $r++) {
                $idw = $item[$r];
                $data['id'] = $idw;
                echo '-->'.$idw;
                $data['rlt'] = $this -> le_data($idw);
                
                $data['hd'] = $hd;
                $sx .= $this -> load -> view('find/view/manifestation', $data, true);
            }
        } else {
            $data['id'] = $id;
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
            $sx .= $this -> load -> view('find/view/expression_void', null, true);
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

        return ($r);
    }

    function itens_show($id) {
        $sx = '';
        $item = $this -> frbr -> recupera_resource($id, 'isAppellationOfWork');
        for ($r = 0; $r < count($item); $r++) {
            $idw = $item[$r];
            $data['id'] = $idw;
            $data['item'] = $this -> le_data($idw);
            $sx .= $this -> load -> view('find/view/item', $data, true);
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

    function person_show($id) {
        $data = array();
        $sx = '';

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
            case 35 :
                $sx .= $this -> frbr -> form_work($id);
                $sx .= $this -> frbr -> form_expression($id);
                $sx .= $this -> frbr -> form_manifestation($id);

                /* WORK */
                $js = 'jQuery("#action_Work").click(function() 
                      {
                          carrega("isAppellationOfWork");
                          jQuery("#dialog").modal("show"); 
                      });' . cr();
                break;
            default :

                /*****************/
                $sql = "select * from rdf_form_class
                    INNER JOIN rdf_class ON id_c = sc_propriety 
                        where sc_class = $class 
                        order by c_order";
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

            $sx .= '</td>';

            $sx .= '</tr>' . cr();
        }
        $sx .= '</table>';
        $sx .= $this -> load -> view('find/modal/modal_exclude', null, true);
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
        $sql = "SELECT * FROM rdf_data 
                        INNER JOIN rdf_name ON d_literal = id_n
                        INNER JOIN rdf_class ON d_p = id_c
                    WHERE $wh AND c_find = 1 ";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $link = '<a href="' . base_url('index.php/main/v/' . $line['d_r1']) . '" target="_new">';
            $sx .= '<div class="row">';
            $sx .= '<div class="col-md-10">';
            $sx .= $link;
            $sx .= $line['n_name'];
            $sx .= '</a>';
            $sx .= ' (';
            $sx .= msg($line['c_class']);
            $sx .= ')';
            $sx .= '</div>';
            $sx .= '</div>';
        }
        return ($sx);
    }

    function set_propriety($r1, $prop, $r2, $lit = 0) {
        /********* propriedade com o prefixo ***************/
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
                     WHERE cc_class = $cl and cc_created = '$dt'";
        } else {
            $sql = "select * from rdf_concept
                     WHERE cc_class = $cl and cc_pref_term = $term";
        }

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $id = 0;
        if (count($rlt) == 0) {

            $sqli = "insert into rdf_concept
                            (cc_class, cc_pref_term, cc_created, cc_origin)
                            VALUES
                            ($cl,$term,'$dt','$orign')";
            $rlt = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            $id = $rlt[0]['id_cc'];
        } else {
            $id = $rlt[0]['id_cc'];
			$line = $rlt[0];
			if ((strlen($orign) > 0) and ((strlen(trim($line['cc_origin'])) == 0) or ($line['cc_origin'] == 'ERRO:')))
				{
					$sql = "update rdf_concept set cc_origin = '$orign' where id_cc = ".$line['id_cc'];
					$rlt = $this -> db -> query($sql);
				}
        }
        return ($id);
    }

    function frbr_name($n = '') {
        $n = trim($n);
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
            $t = file_get_contents($url, false);
            $t = troca($t, 'mx:', '');
            $xml = simplexml_load_string($t);

            $id = '';
            $names = array();
            $dt_born = '';
            $dt_die = '';
            $genre = '';
            $tt = 0;
            $sx = '';
            /*
            echo '<pre>';
            print_r($xml);
            echo '</pre>';
            */
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
                                    case '375':
                                        $genre = $vlr;
                                        break; 
                                    default:
                                        $sx .= '<tt>'.$tag.'</tt><br>';
                                        break;                                       
                                }
                            }
                        }
                    }
                }
            }
        }

        /* create */
        if ((count($names) > 0) and (strlen($id) > 0))
            {
                $tt = 0;
                foreach ($names as $autor => $value) {
                   //echo '<br>'.$autor.'=>'.$value;
                    if ($tt == 0)
                        {
                            $name_pref = $autor;
                            $id_t = $this -> frbr -> frbr_name($name_pref);
                            $p_id = $this -> frbr -> rdf_concept($id_t, $form, $id);
                            $this -> frbr -> set_propriety($p_id, 'prefLabel', 0, $id_t);
                            
                            if (strlen($dt_born) > 0)
                                {
                                    $id_t = $this -> frbr -> frbr_name($dt_born);
                                    $this -> frbr -> set_propriety($p_id, 'hasBorn', 0, $id_t);
                                }
                            if (strlen($dt_die) > 0)
                                {
                                    $id_t = $this -> frbr -> frbr_name($dt_die);
                                    $this -> frbr -> set_propriety($p_id, 'hasDie', 0, $id_t);
                                }                                   
                        } else {
                            $name_pref = troca($autor,"'","´");
                            //echo mb_detect_encoding($name_pref).' '.$name_pref;
                            
                            $id_t = $this -> frbr -> frbr_name($name_pref);
                            $this -> frbr -> set_propriety($p_id, 'altLabel', 0, $id_t);                            
                        }
                     $tt++;
                }
                $sx .= '
                        <div class="alert alert-success" role="alert">
                          <strong>Sucesso!</strong> Importação finalizada com sucesso.
                        </div>
                ';
                $sx .= '<h1>' . $name_pref . '</h1>';
                $sx .= '<h3>' . $dt_born . '</h3>';
                $sx .= '<h4>' . $dt_die . '</h4>';
                $sx .= '<br>';
            }
    }

    function trata_autor($n) {
        $n = (string)$n;
        $n = trim($n);
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
    function form_class()
        {
            $cp = 'id_sc, tb1.c_class as c1, tb2.c_class as c2, tb3.c_class as c3';
            $sql = "select $cp from rdf_form_class 
                        INNER JOIN rdf_class as tb1 ON sc_class = tb1.id_c
                        INNER JOIN rdf_class as tb2 ON sc_propriety = tb2.id_c
                        LEFT JOIN rdf_class as tb3 ON sc_range = tb3.id_c
                        order by c1, c2, c3
                        ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx = '<table width="100%">'.cr();
            $sx .= '<tr style="border-bottom: 2px solid #505050;">
                        <th width="30%">'.msg('resource').'</th>
                        <th width="35%">'.msg('propriety').'</th>
                        <th width="30%">'.msg('range').'</th>
                        <th width="5%">'.msg('ed').'</th>
                    </tr>'.cr();
            $x = '';
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    
                    
                    if ($x == $line['c1'])
                        {
                            $sx .= '<tr style="border-top: 1px solid #a0a0a0;">';
                            $sx .= '<td></td>';
                        } else {
                            $sx .= '<tr style="border-top: 3px solid #a0a0a0;">';
                            $x = $line['c1'];
                            $sx .= '<td><b>'.$line['c1'].'</b></td>';        
                        }
                    
                    $sx .= '<td>'.msg($line['c2']).'</td>';
                    $sx .= '<td>'.msg($line['c3']).'</td>';
                    $sx .= '<td align="center">'.msg($line['id_sc']).'</td>';
                    $sx .= '</tr>'.cr();
                }
            $sx .= '</table>';
            return($sx);
        }
}
?>
