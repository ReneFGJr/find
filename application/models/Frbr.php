<?php
class frbr extends CI_model {
    function marc_to_frbr($m) {
        print_r($m);
        /* PREFERENCIAL NAME */
        if (isset($m['100a'])) {
            echo '<h1>' . $m['100a'] . '</h1>';
            $id = $this -> frbr_name($m['100a']);
            $class = 'Person';
            $p_id = $this -> rdf_concept($id, $class);
            /* pref_term */
        }
        /* ALTERNATIVE NAMES */
        if (count($m['400']) > 0) {
            for ($r = 0; $r < count($m['400']); $r++) {
                $nm = $m['400'][$r]['400a'];
                $m['400'][$r]['400z'] = $this -> frbr_name($nm);
                echo $nm . '<br>';
            }
        }
        return ($m);
    }

    function find_class($class) {
        $sql = "select * from rdf_class
                        WHERE c_class = '$class' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            echo '<h1>Ops, '.$class.' não localizada';
            exit;
        }
        $line = $rlt[0];
        print_r($line);
        return($line['id_c']);
    }

    function rdf_concept($term, $class) {
        $cl = $this->find_class($class);
        $sql = "select * from rdf_concept
                            WHERE c_class = $cl and c_pref_term = $term";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {

            $sqli = "insert into rdf_concept
                            (c_class, c_pref_term)
                            VALUES
                            ($cl,$term)";
            $rlt = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
        }
        return ($rlt[0]['id_c']);
    }

    function frbr_name($n = '') {
        $n = trim($n);
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
