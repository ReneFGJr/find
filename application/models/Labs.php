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
        $sql = "select * from bm_cited where ctd_source = ".$id." order by id_ctd";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '<h3>Referências</h3>';
        $sx .= '<ul>';
        for ($r=0;$r < count($rlt);$r++)
            {
                $line = $rlt[$r];
                $c = $line['ctd_ref'];
                $c = troca($c,'<','&lt;');
                $c = troca($c,'>','&gt;');
                $sx .= '<li>';
                $sx .= $c;
                $sx .= '</li>';
            }
            $sx .= '</ul>';
        if (count($rlt) == 0) { $sx = ''; }
        return($sx);
    }

    function cited($id) {
        $sx = $this -> frbr -> vv($id);
        
        $sx .= $this->cited_list($id);

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

}
?>