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
                        WHERE cl1.c_class = '".$path."' and cl1.c_type = 'P' ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $type = $path;
        if (count($rlt) > 0)
            {
                $line = $rlt[0];
                $type = $line['rg']; 
            }
        /**********************************************************************************/
        switch($type) {
            case 'Date' :
                $tela .= 'Date';
                //$this->case_author($path,$id);
                break;
            case 'Agent' :
                $tela .= $this->case_author($path,$id);
                break;				            
            case 'hasAuthor' :
                $tela .= $this->case_author($path,$id);
                break;
            case 'hasOrganizator' :
                $tela .= $this->case_author($path,$id);
                break;
            default :
                $tela .= '
                        <div class="alert alert-danger" role="alert">
                          <strong>Error! (544)</strong> Método não implementado "' . $path . ' - '.$type.'".
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

    function ajax2($path,$id)
        {
                $tela = '<select name="dd51" id="dd51" size=5 class="form-control" onchange="change();">' . cr();
                $vlr = get("q");
                if (strlen($vlr) < 3) {
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

                    /***********************************************************************/
                    if (strlen($wh) > 0) {
                        $sql = "select * from rdf_name
                                    INNER JOIN rdf_data ON id_n = d_literal
                                    INNER JOIN rdf_concept ON d_r1 = id_cc
                                    INNER JOIN rdf_class ON id_c = d_p 
                                    WHERE ($wh) and (n_name <> '') 
                                    LIMIT 50";
                        $rlt = $this -> db -> query($sql);
                        $rlt = $rlt -> result_array();

                        for ($r = 0; $r < count($rlt); $r++) {
                            $line = $rlt[$r];
                            //print_r($line);
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
                return($tela);            
        }

    function le($id) {
        $sql = "select * from rdf_concept 
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

    function case_author($path, $id) {
        $tela = '';
        $tela .= '<span style="font-size: 75%">filtro do autor</span><br>';
        $tela .= '<input type="text" id="dd50" name="dd50" class="form-control">';
        $tela .= '<span style="font-size: 75%">selecione o autor</span><br>';
        $tela .= '<div id="dd51a"><select class="form-control" size=5 name="dd51" id="dd51"></select></div>';
        $tela .= '
                    <script>
                        /************ keyup *****************/
                        jQuery("#dd50").keyup(function() 
                            {
                                var $key = jQuery("#dd50").val();
                                
                                $.ajax({
                                    type: "POST",
                                    url: "' . base_url('index.php/main/ajax2/' . $path . '/' . $id) . '",
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

    function work($title = '', $subtitle = '') {
        $id_t = $this -> frbr_name($title);
        $id_s = 0;
        if (strlen($subtitle) > 0) {
            $id_s = $this -> frbr_name($subtitle);
        }

        /* concept */
        $class = 'Work';
        $p_id = $this -> rdf_concept($id_t, $class);
        $this -> set_propriety($p_id, 'hasTitle', 0, $id_t);
        if ($id_s > 0) {
            $this -> set_propriety($p_id, 'hasSubtitle', 0, $id_s);
        }
        return ($p_id);
    }

    function form($id, $dt) {
        $class = $dt['cc_class'];
        $sql = "select * from rdf_form_class
                    INNER JOIN rdf_class ON id_c = sc_propriety 
                        where sc_class = $class ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<ul>';
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
            </div>       
        ';
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
            $link = '<a href="'.base_url('index.php/main/a/'.$line['id_cc']).'">';
            $linka = '</a>';
            if (strlen($line['id_cc']) == 0)
                {
                    $link = '';
                    $linka = '';
                }
            $sx .= '<tr>';
            $sx .= '<td class="text-right" style="font-size: 60%;">';
            $sx .= $line['c_class'];
            $sx .= '</td>';
            $sx .= '<td>';
            $sx .= $link.$line['n_name'].$linka;
            $sx .= '</td>';
            $sx .= '</tr>' . cr();
        }
        $sx .= '</table>';
        return ($sx);
    }

    function marc_to_frbr($m) {
        /* PREFERENCIAL NAME */
        $p_id = '';

        if (isset($m['100a'])) {
            echo '<h1>' . $m['100a'] . '</h1>';

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
                $dai1 = $this -> rdf_concept($dai1, 'Date');
                $this -> set_propriety($p_id, 'hasBorn', $dai1);
            }
            if (strlen($da2) > 0) {
                $dai2 = $this -> frbr_name($da2);
                $dai2 = $this -> rdf_concept($dai2, 'Date');
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
        echo '<pre>' . $this -> show($p_id) . '</pre>';
        return ($m);
    }

    function set_propriety($r1, $prop, $r2, $lit = 0) {
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

    function rdf_concept($term, $class) {
        $cl = $this -> find_class($class);
        $sql = "select * from rdf_concept
                            WHERE cc_class = $cl and cc_pref_term = $term";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {

            $sqli = "insert into rdf_concept
                            (cc_class, cc_pref_term)
                            VALUES
                            ($cl,$term)";
            $rlt = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
        }
        return ($rlt[0]['id_cc']);
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

}
?>
