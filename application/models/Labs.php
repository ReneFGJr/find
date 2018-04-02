<?php
class labs extends CI_model {
    function corpus() {
        if (isset($_SESSION['corpus'])) {
            $s = $_SESSION['corpus'];
            return ($s);
        } else {
            redirect(base_url('index.php/bibliometric/main'));
        }
    }

    function le($id) {
        $sql = "select * from bm_analysis where id_a = " . $id;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line);
        } else {
            return ( array());
        }
    }

    function select_corpus() {
        $q = get("q");
        if (strlen($q) > 0) {
            $_SESSION['corpus'] = $q;
            redirect(base_url('index.php/bibliometric/corpus'));
            exit ;
        }
        $sql = "select * from bm_analysis order by a_name";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<h5>Corpus</h5>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $sx .= '<h2><a href="?q=' . $line['id_a'] . '">' . $line['a_name'] . '</a></h2>' . cr();
        }
        return ($sx);
    }

    function cited_list($id) {
        $sql = "select * from bm_cited where ctd_source = " . $id . " order by id_ctd";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<h3>Referências</h3>';
        $sx .= '<ul>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $c = $line['ctd_ref'];
            $c = troca($c, '<', '&lt;');
            $c = troca($c, '>', '&gt;');
            $sx .= '<li>';
            $sx .= $c;
            $sx .= '</li>';
        }
        $sx .= '</ul>';
        if (count($rlt) == 0) { $sx = '';
        }
        return ($sx);
    }

    function cited($id) {
        $sx = $this -> frbr -> vv($id);

        $sx .= $this -> cited_list($id);

        $form = new form;
        $cp = array();
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$T100:20', '', 'Referências', True, True));

        $dd1 = get("dd1");
        if (strlen($dd1) > 0) {
            $sx .= $this -> process_cited($id, $dd1);
        }

        $sx .= $form -> editar($cp, '');

        return ($sx);
    }

    function process_cited($id, $t) {
        $sql = "delete from bm_cited where ctd_source = $id";
        $rlt = $this -> db -> query($sql);

        $t = troca($t, chr(13), '');
        $t = troca($t, chr(10), '¢');
        $t = troca($t, '.¢', '£');
        $t = troca($t, '¢', '');
        $t = troca($t, '£', '~');
        $ln = splitx('~', '' . $t);
        $autor = '';
        for ($r = 0; $r < count($ln); $r++) {
            $l = $ln[$r];
            $l = troca($l, chr(0), '');

            /********* Identifica autor ***********/
            if (substr($l, 1, 3) == '___') {
                $ini = strpos($l, '.');
                $l = $autor . '. ' . substr($l, $ini + 1, strlen($l));
            } else {
                $ini = strpos($l, '.');
                $autor = substr($l, 0, $ini);
            }
            /* Remove Disponível em */
            if (strpos($l, 'http://')) {
                $ini = strpos($l, 'http://');
                $l = substr($l, 0, $ini);
            }
            if (strpos($l, 'Disponível em:')) {
                $ini = strpos($l, 'Disponível em:');
                $l = substr($l, 0, $ini);
            }
            $l .= '.';
            $l = troca($l, '. .', '.');
            $l = troca($l, '..', '.');

            $sql = "insert into bm_cited 
                                (ctd_ref, ctd_status, ctd_source)
                                values
                                ('$l','0',$id)
                                ";
            $rlt = $this -> db -> query($sql);
        }
        return ('Processado ' . ($r) . ' referencias');

    }

    function menus() {
        $sx = '<a href="' . base_url('index.php/main/mod/labs/citeis/0') . '" class="btn btn-secondary">Ano</a>';
        $sx .= '<a href="' . base_url('index.php/main/mod/labs/citeis/10') . '" class="btn btn-secondary">Erros Ano</a>';
        $sx .= '<a href="' . base_url('index.php/main/mod/labs/citeis/20') . '" class="btn btn-secondary">Internet</a>';
        $sx .= '<a href="' . base_url('index.php/main/mod/labs/citeis/1') . '" class="btn btn-secondary">Phase I</a>';
        $sx .= '<a href="' . base_url('index.php/main/mod/labs/citeis/2') . '" class="btn btn-secondary">Source Find</a>';
        $sx .= '<a href="' . base_url('index.php/main/mod/labs/citeis/3') . '" class="btn btn-secondary">Without Source</a>';
        
        $sx .= '<a href="' . base_url('index.php/main/mod/labs/citeis/99') . '" class="btn btn-secondary">Status</a>';
        return ($sx);
    }

    function ref($id = '') {
        $sx = $this -> menus();
        $c = get("c");
        $q = get("i");
        $i = get("q");
        /********************************************************************/
        switch($c) {
            case 'set' :
                $sql = "update bm_cited set 
                            ctd_status = 1,
                            ctd_year = '$i'
                            where id_ctd = " . $q;
                $xrlt = $this -> db -> query($sql);
                redirect(base_url('index.php/main/mod/labs/ref/' . $id . '#' . $q));
                break;
        }
        /********************************************************************/
        $sql = "delete from bm_cited
                       where ctd_ref = ''";
        $rlt = $this -> db -> query($sql);
        /********************************************************************/
        $sql = "select * from bm_cited
                       where ctd_source = " . $id . " 
                order by id_ctd";
        $rlt = $this -> db -> query($sql);
        /********************************************************************/
        $rlt = $rlt -> result_array();
        $sx .= '<ul>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            if (isset($rlt[$r + 1])) {
                $line2 = $rlt[$r + 1];
            } else {
                $line2 = array();
                $line2['id_ctd'] = '';
            }

            $cor = '#0000FF';
            $st = $line['ctd_status'];
            $cmd = '';
            switch ($st) {
                case '1' :
                    $cor = '#0000FF';
                    break;
                case '10' :
                    $cor = '#FF0000';
                    break;
                case '11' :
                    $cor = '#990000';
                    $cmd = '<br>Anos:';
                    $cmd = $this -> data_seleciona($line['ctd_ref'], $id, $line['id_ctd']);
                    break;
                default :
                    $cor = '#a0a0a0';
                    break;
            }

            $tag = '<a name="' . $line['id_ctd'] . '"></a>' . cr();
            $link = '<a href="#' . $line['id_ctd'] . '" onclick="newxy(\'' . base_url('index.php/main/mod/labs/ref_edit/' . $line['id_ctd']) . '\',1024,600);" style="color: ' . $cor . ';">';
            $cmd .= '<a href="#' . $line['id_ctd'] . '" onclick="newxy(\'' . base_url('index.php/main/mod/labs/ref_join/' . $line['id_ctd'] . '/' . $line2['id_ctd']) . '\',1024,600);" style="color: ' . $cor . ';">[+]</a>';
            $sx .= '<li>' . $tag . $link . $line['ctd_ref'] . '</a>' . $cmd . '</li>' . cr();
        }
        $sx .= '</ul>';
        return ($sx);
    }

    function ref_join($id, $id2) {
        echo "-->" . $id . '==' . $id2;
        $sql = "select * from bm_cited
                       where id_ctd = " . $id;
        $rlt = $this -> db -> query($sql);
        $rlt1 = $rlt -> result_array();

        $sql = "select * from bm_cited
                       where id_ctd = " . $id2;
        $rlt = $this -> db -> query($sql);
        $rlt2 = $rlt -> result_array();

        $ref = trim($rlt1[0]['ctd_ref']) . ' ' . trim($rlt2[0]['ctd_ref']);

        $sql = "update bm_cited set
                    ctd_ref = '$ref',
                    ctd_status = 0
                    where id_ctd = $id ";
        $rlt = $this -> db -> query($sql);

        $sql = "update bm_cited set
                    ctd_ref = '',
                    ctd_status = 0
                    where id_ctd = $id2 ";
        $rlt = $this -> db -> query($sql);

        echo '
                          <script>  
                                window.opener.location.reload();
                                close(); 
                          </script>';
        exit ;
    }

    function ref_edit($id) {
        $sql = "select * from bm_cited
                       where id_ctd = " . $id;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line = $rlt[0];

        $ac = get("action");
        $dd1 = get("dd1");
        $dd2 = get("dd2");
        $ids = $line['ctd_source'];
        if (strlen($ac) > 0) {
            $sql = "update bm_cited set
                                ctd_ref = '" . $dd1 . "',
                                ctd_status = 0,
                                ctd_year = 0
                            where id_ctd = " . $id;
            $xrlt = $this -> db -> query($sql);
            if (strlen($dd2) > 0) {
                $sql = "insert into bm_cited
                                    (ctd_ref, ctd_status, ctd_year, ctd_source)
                                    values 
                                    ('$dd2',0,0,$ids)";
                $xrlt = $this -> db -> query($sql);
            }
            echo '
                          <script>  
                                window.opener.location.reload();
                                close(); 
                          </script>';
            exit ;
        } else {
            $dd1 = $line['ctd_ref'];
            $dd2 = '';
        }

        $sx = '<form method="post">';
        $sx .= 'Referência';
        $sx .= '<textarea name="dd1" class="form-control" style="height: 200px;">' . $dd1 . '</textarea>' . cr();
        $sx .= '<br>';
        $sx .= 'Desdobramento da Referência';
        $sx .= '<textarea name="dd2" class="form-control" style="height: 200px;">' . $dd2 . '</textarea>' . cr();
        $sx .= '<br>';
        $sx .= '<input type="submit" value="Gravar >>>" class="btn btn-primary" name="action">';
        $sx .= '</form>';
        $sx .= '<br><br><br><br><br><br><br><br>';
        echo $sx;

    }

    function data_seleciona($ln, $id, $idr) {
        $dts = array();
        $sx = '';
        for ($r = 1700; $r < (date("Y") + 1); $r++) {
            $y = (string)$r;
            $p = strpos($ln, $y);
            if ($p > 0) {
                $link = '<a href="' . base_url('index.php/main/mod/labs/ref/' . $id) . '?c=set&q=' . $r . '&i=' . $idr . '">';
                if (strlen($sx) > 0) { $sx .= ', ';
                }
                $sx .= $link . '[' . $r . ']' . '</a>';
            }
        }
        return ('<br>' . $sx);
    }

    function citeis($id = '') {
        $sx = '';
        $sx .= $this -> menus();
        switch($id) {
            case '0' :
                /********************************************************************/
                $sql = "delete from bm_cited
                       where ctd_ref = ''";
                $rlt = $this -> db -> query($sql);

                $sql = "select * from bm_cited
                                where ctd_status = 0 
                                order by id_ctd
                                limit 200";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                $sx .= '<ul>';
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $sx .= '<li>' . $line['ctd_ref'] . '</li>';
                    $this -> recupera_data($line['ctd_ref'], $line['id_ctd']);
                }
                $sx .= '</ul>';
                break;
            case '1' :
                $sql = "select * from bm_cited
                            left join bm_source ON id_bs = ctd_type
                                where ctd_status = 1 
                                order by id_ctd
                                limit 500";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                $sx .= '<ul>';
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $ab = $line['bs_abrev'];
                    if (strlen($ab) > 0)
                        {
                            $ab = ' <font color="red">['.$ab.']</font>';
                        }
                    $link = '<a href="' . base_url('index.php/main/mod/labs/ref/' . $line['ctd_source']) . '">';
                    $sx .= '<li>' . $link . $line['ctd_ref'] . '</a>' . $ab . '</li>';
                    //$this->recupera_data($line['ctd_ref'],$line['id_ctd']);
                }
                $sx .= '</ul>';
                break;
            case '99':
                $sql = "select * from (
                            SELECT ctd_type, count(*) as total FROM `bm_cited`  
                            group by ctd_type
                            ) as tabela
                            left join bm_source on ctd_type = id_bs
                            order by total desc";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                $sx .= '<ul>';
                for ($r=0;$r < count($rlt);$r++)
                    {
                        $line = $rlt[$r];
                        $sx .= '<li>';
                        $sx .= $line['bs_name'].' ('.$line['total'].')';
                        $sx .= '</li>';
                    }
                $sx .= '</ul>';
                return($sx);                            
            case '3' :
                $sql = "select * from bm_cited
                                where ctd_status = 1 and ctd_type = 0
                                order by id_ctd
                                limit 500";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                $sx .= '<ul>';
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $link = '<a href="' . base_url('index.php/main/mod/labs/ref/' . $line['ctd_source']) . '">';
                    $sx .= '<li>' . $link . $line['ctd_ref'] . '</a>'  . '</li>';
                    //$this->recupera_data($line['ctd_ref'],$line['id_ctd']);
                }
                $sx .= '</ul>';
                break;                
            case '2' :
                $sql = "select * from bm_cited where 
                            ctd_ref like '% :%' 
                            or ctd_ref like '%  %'
                            or ctd_ref like '%, 2%'
                            or ctd_ref like '%, 1%'
                            ";
                $rlt = $this->db->query($sql);
                $rlt = $rlt -> result_array();
                for ($r=0;$r < count($rlt);$r++)
                    {
                        $line = $rlt[$r];
                        $idc = $line['id_ctd'];
                        $s = $line['ctd_ref'];
                        $s = troca($s, ' :',':');
                        $s = troca($s, ', 2',': 2');
                        $s = troca($s, ', 1',': 1');
                        while (strpos($s,'  '))
                            {
                                $s = troca($s,'  ',' ');
                            }
                        $sql = "update bm_cited set ctd_ref = '$s' where id_ctd = $idc";
                        //echo '<br>'.$sql;
                        $xrlt = $this->db->query($sql);            
                    }
                
                $sql = "select * from bm_rules";
                $rlt = $this -> db -> query($sql);
                $rls = $rlt -> result_array();
                for ($q = 0; $q < count($rls); $q++) {
                    $line2 = $rls[$q];
                    $wh = $line2['br_rule'];
                    $type = $line2['br_type'];
                    $source = $line2['br_source'];
                    $sql = "select * from bm_cited
                                where ctd_status = 1 
                                and ($wh) and ctd_type = 0
                                order by id_ctd
                                limit 500";
                    $rlt = $this -> db -> query($sql);
                    $rlt = $rlt -> result_array();
                    $sx .= '<ul>';
                    for ($r = 0; $r < count($rlt); $r++) {
                        $line = $rlt[$r];
                        $id_ctd = $line['id_ctd'];
                        $sql = "update bm_cited set ctd_type = ".$type.",
                                    ctd_source_id = $source 
                                    where id_ctd = ".$id_ctd;
                        $xrlt = $this -> db -> query($sql);

                        $link = '<a href="' . base_url('index.php/main/mod/labs/ref/' . $line['ctd_source']) . '">';
                        $sx .= '<li>' . $link . $line['ctd_ref'] . '</a>' . '</li>';
                        //$this->recupera_data($line['ctd_ref'],$line['id_ctd']);
                    }
                    $sx .= '</ul>';
                }
                break;
            case '10' :
                $sql = "select * from bm_cited
                                where ctd_status = 10 
                                order by id_ctd
                                limit 200";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                $sx .= '<ul>';
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $link = '<a href="' . base_url('index.php/main/mod/labs/ref/' . $line['ctd_source']) . '">';
                    $sx .= '<li>' . $link . $line['ctd_ref'] . '</a>' . '</li>';
                    //$this->recupera_data($line['ctd_ref'],$line['id_ctd']);
                }
                $sx .= '</ul>';
                break;
            case '20' :
                $sql = "update bm_cited set ctd_type = 1 
                                where (ctd_status = 10 
                                        and ctd_ref like '%disponível em%')";
                $rlt = $this -> db -> query($sql);

                $sql = "select * from bm_cited
                                where ctd_type = 1
                                order by id_ctd
                                limit 200";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                $sx .= '<ul>';
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $link = '<a href="' . base_url('index.php/main/mod/labs/ref/' . $line['ctd_source']) . '">';
                    $sx .= '<li>' . $link . $line['ctd_ref'] . '</a>' . '</li>';
                    //$this->recupera_data($line['ctd_ref'],$line['id_ctd']);
                }
                $sx .= '</ul>';
                break;
        }
        return ($sx);
    }

    function recupera_data($ln, $id) {
        $dts = array();
        for ($r = 1700; $r < (date("Y") + 1); $r++) {
            $y = (string)$r;
            $p = strpos($ln, $y);
            if ($p > 0) {
                array_push($dts, $r);
            }
        }
        if (count($dts) > 1) {
            $sql = "update bm_cited set 
                            ctd_status = 11
                            where id_ctd = " . $id;
            $xrlt = $this -> db -> query($sql);
        }
        if (count($dts) == 0) {
            $sql = "update bm_cited set 
                            ctd_status = 10
                            where id_ctd = " . $id;
            $xrlt = $this -> db -> query($sql);
        }
        if (count($dts) == 1) {
            $sql = "update bm_cited set 
                            ctd_status = 1, 
                            ctd_year = '" . $dts[0] . "'
                            where id_ctd = " . $id;
            $xrlt = $this -> db -> query($sql);
        }
    }

}
?>