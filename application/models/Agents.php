<?php
class Agents extends CI_model {
    function inport_marc21($txt) {
        $txt = troca($txt, '|', '$');
        $txt = troca($txt, chr(10), '¢');
        $txt = troca($txt, chr(12), '');
        $txt = troca($txt, chr(11), '');
        $txt = troca($txt, chr(13), '');
        $ln = splitx('¢', $txt);

        $t = '';
        
        $dt = array();
        $dt['400'] = array();
        $dt['670'] = array();
        
        for ($r = 0; $r < count($ln); $r++) {
            $fld = sonumero(substr($ln[$r], 0, 4));
            $txt .= '<h5>' . $fld . '</h5>';
            $t = $ln[$r];
            
            switch($fld) {
                /* SOURCE */
                case '040' :
                    $dt['040a'] = $this -> bfield($t, '$a');
                    $dt['040b'] = $this -> bfield($t, '$b');
                    $dt['040c'] = $this -> bfield($t, '$c');
                    break;                
                case '100' :
                    $dt['100a'] = $this -> bfield($t, '$a');
                    $dt['100d'] = $this -> bfield($t, '$d');
                    $dt['100q'] = $this -> bfield($t, '$q');
                    break;
                case '400' :
                    $ar = array();
                    $fn = $this -> bfield($t, '$a');
                    $ar['400a'] = $fn; 
                    $ar['400b'] = $this -> bfield($t, '$b');
                    $ar['400c'] = $this -> bfield($t, '$c');
                    if (strlen($fn) > 0) {
                        $n = count($dt['400']);
                        $dt['400'][$n] = $ar;
                    }
                    break;
                case '670' :
                    $ar = array();
                    $fn = $this -> bfield($t, '$a');
                    $ar['670a'] = $fn; 
                    $ar['670b'] = $this -> bfield($t, '$b');
                    $ar['670c'] = $this -> bfield($t, '$c');
                    if (strlen($fn) > 0) {
                        $n = count($dt['670']);
                        $dt['670'][$n] = $ar;
                    }
                    break;                    
            }
        }
        return ($dt);
    }

    function bfield($f, $c) {
        $pos = strpos($f, $c);
        $rs = '';
        if ($pos > 0) {
            $rs = trim(substr($f, $pos + 2, strlen($f)));
            if (strpos($rs, '$')) {
                $pos = strpos($rs, '$');
                if ($pos > 1) {
                    $rs = trim(substr($rs, 0, $pos));
                }
            }         
        }
        return ($rs);
    }
}
?>
