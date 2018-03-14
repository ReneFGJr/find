<?php
class labs extends CI_model
    {
        function corpus()
            {
                if (isset($_SESSION['corpus']))
                    {                    
                        $s = $_SESSION['corpus'];
                        return($s);
                    } else {
                        redirect(base_url('index.php/bibliometric/main'));
                    }
            }
        function le($id)
            {
                $sql = "select * from bm_analysis where id_a = ".$id;
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                if (count($rlt) > 0)
                    {
                        $line = $rlt[0];
                        return($line);
                    } else {
                        return(array());
                    }
            }
        function select_corpus()
            {
                $q = get("q");
                if (strlen($q) > 0)
                    {
                        $_SESSION['corpus'] = $q;
                        redirect(base_url('index.php/bibliometric/corpus'));
                        exit;
                    }
                $sql = "select * from bm_analysis order by a_name";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                $sx = '<h5>Corpus</h5>';
                for ($r=0;$r < count($rlt);$r++)
                    {
                        $line = $rlt[$r];
                        $sx .= '<h2><a href="?q='.$line['id_a'].'">'.$line['a_name'].'</a></h2>'.cr();
                    }
                return($sx);
            }
    }
?>