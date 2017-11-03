<?php
class frbr extends CI_model {
	function show($r)
		{
			$sx = '';
			$sql = "select * from rdf_concept 
						INNER JOIN rdf_class as prop ON cc_class = id_c
						WHERE id_cc = ".$r;
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			$line = $rlt[0];
			$sx .= '<h3>class:'.$line['c_class'].'</h3>';
			
			$cp = '*';
			$sql = "select $cp from rdf_data as rdata
						INNER JOIN rdf_class as prop ON d_p = prop.id_c 
						INNER JOIN rdf_concept ON d_r2 = id_cc 
						INNER JOIN rdf_name on cc_pref_term = id_n
						WHERE d_r1 = $r";
						echo $sql;
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			
			for ($r=0;$r < count($rlt);$r++)
				{
					$line = $rlt[$r];
					$sx .= $line['c_class'];
					$sx .= ' ==> ';
					$sx .= $line['n_name'];
					$sx .= '<br>';
				}
			return($sx);
		}
    function marc_to_frbr($m) {
        /* PREFERENCIAL NAME */
        if (isset($m['100a'])) {
            echo '<h1>' . $m['100a'] . '</h1>';
            $id = $this -> frbr_name($m['100a']);
			/* prefLabel */
            $class = 'Person';			
            $p_id = $this -> rdf_concept($id, $class);
			$this->set_propriety($p_id,'prefLabel',$id);		
			
            /* DATAS ASSOCIADAS AO NOME */
            $da = $m['100d'];		
			$da1 = ''; $da2 = '';
			if (strpos($da,'-'))
				{
					$pos = strpos($da,'-');
					$da1 = trim(substr($da,0,$pos));
					$da2 = trim(substr($da,$pos+1,strlen($da)));
				}
            if (strlen($da1) > 0)
				{
					$dai1 = $this -> frbr_name($da1);
					$dai1 = $this -> rdf_concept($dai1, 'Date');
					$this->set_propriety($p_id,'hasBorn',$dai1);
				}
            if (strlen($da2) > 0)
				{
					$dai2 = $this -> frbr_name($da2);
					$dai2 = $this -> rdf_concept($dai2, 'Date');
					$this->set_propriety($p_id,'hasDie',$dai2);
				}
			
            /* author date associated */
            
        }
        /* ALTERNATIVE NAMES */
        
        if (count($m['400']) > 0) {
        	
            for ($r = 0; $r < count($m['400']); $r++) {            	
                $nm = $m['400'][$r]['400a'];
				
				$altn = $this -> frbr_name($nm);			
				$m['400'][$r]['400z'] = $altn;				
				$this->set_propriety($p_id,'altLabel',$altn);
            }
        }
		echo '<pre>'.$this->show($p_id).'</pre>';
        return ($m);
    }

	function set_propriety($r1,$prop,$r2)
		{
			$pr = $this->find_class($prop);
			$sql = "select * from rdf_data 
						WHERE 
						((d_r1 = $r1 AND d_r2 = $r2)
							OR
						 (d_r1 = $r2 AND d_r2 = $r1))
						AND d_p = $pr ";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) == 0)
				{
					$sql = "insert into rdf_data
								(d_r1, d_p, d_r2)
								values
								('$r1','$pr','$r2')";
					$rlt = $this->db->query($sql);
				} else {

				}
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
        return($line['id_c']);
    }

    function rdf_concept($term, $class) {
        $cl = $this->find_class($class);
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
