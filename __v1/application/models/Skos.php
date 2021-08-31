<?php
class skos extends CI_Model
    {
        function row_type()
            {
                $sx = '<h4>'.msg('controller_vocabulary').'</h4>';

                $link = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/1/skos';
                return($sx);
            }
    }