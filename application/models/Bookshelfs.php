<?php
class bookshelfs extends CI_model {
    function lastupdate() {
        $class = $this -> frbr -> find_class('work');
        $sql = "select id_cc as w 
                            from rdf_concept 
                            where cc_class = " . $class . "
                            AND cc_library = " . LIBRARY . "
                            ORDER BY id_cc desc
                            limit 24
                            ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<div class="'.bscol(12).' header">';
        $sx .= '<h2>'.msg('acquisitions').'<small>- '.msg('latest acquisitions').'</small></h2>';
        $sx.= '</div>';
        
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $sx .= '<div class="'.bscol(4).' book_small" style="margin-top: 40px;">';
            $sx .= $this -> bookshelfs -> show_manifestation_by_works($line['w']);
            $sx .= '</div>' . cr();
        }
        return ($sx);
    }

    function show_manifestation_by_works($id = '', $img_size = 200, $mini = 0) {
        $img = base_url('img/no_cover.png');
        $data = $this -> frbr -> le_data($id);
        $year = '';

        $title = '';
        $autor = '';
        $nautor = '';
        for ($r = 0; $r < count($data); $r++) {
            $line = $data[$r];
            $class = $line['c_class'];
            //echo '<br>'.$class;
            $tota = 0;
            switch($class) {
                case 'hasTitle' :
                    $title = $line['n_name'];
                    break;
                case 'hasOrganizator' :
                    if (strlen($autor) > 0) {
                        $autor .= '; ';
                        $nautor .= '; ';
                    }
                    $link = '<a href="' . base_url(PATH . 'v/' . $line['id_cc']) . '" class="book_author">';
                    $autor .= $link . $line['n_name'] . ' (org.)' . '</a>';
                    break;
                case 'hasAuthor' :
                    if ($tota == 0) {
                        if (strlen($autor) > 3) {
                            $link = '<a href="' . base_url(PATH . 'v/' . $line['id_cc']) . '" class="book_author">';
                            $autor .= $link . $line['n_name'] . '</a>';
                            $nautor .= $line['n_name'];
                        }
                    }
                    $tota++;
                    break;
            }
        }
        /* expression */
        $class = "isRealizedThrough";
        $id_cl = $this -> frbr -> find_class($class);
        $sql = "select * from rdf_data 
                            WHERE d_r1 = $id and
                                    d_p = $id_cl ";
        $xrlt = $this -> db -> query($sql);
        $xrlt = $xrlt -> result_array();

        if (count($xrlt) > 0) {
            $ide = $xrlt[0]['d_r2'];
            /************************************ manifestation ********/
            $class = "isEmbodiedIn";
            $id_cl = $this -> frbr -> find_class($class);
            $sql = "select * from rdf_data 
                            WHERE d_r1 = $ide and
                                    d_p = $id_cl ";
            $xrlt = $this -> db -> query($sql);
            $xrlt = $xrlt -> result_array();
            if (count($xrlt) > 0) {
                $idm = $xrlt[0]['d_r2'];

                /* Image */
                $dt2 = $this -> frbr -> le_data($idm);
                for ($r = 0; $r < count($dt2); $r++) {
                    $line = $dt2[$r];
                    $class = $line['c_class'];
                    if ($class == 'hasCover') {
                        $img = base_url('_repositorio/image/' . $line['n_name']);
                    }
                    if ($class == 'dateOfPublication') {
                        $year = '<br>' . $line['n_name'];
                    }
                }
            }
        }

        $sx = '';
        $link = '<a href="' . base_url(PATH . 'v/' . $id) . '">';
        $sx .= $link;
        $title_nr = $title;
        $sz = 60;
        if (strlen($title_nr) > $sz) {
            $title_nr = substr($title_nr, 0, $sz);
            while (substr($title_nr, strlen($title_nr) - 1, 1) != ' ') {
                $title_nr = substr($title_nr, 0, strlen($title_nr) - 1);
            }
            $title_nr = trim($title_nr) . '...';
        }

        $sx .= '<div class="book_image_div">';
        $sx .= '<img src="' . $img . '" class="img-fluid book_image">' . cr();
        $sx .= '</div>';
        $sx .= '<b>' . $title_nr . '</b>';
        $sx .= '</a>';
        if (strlen($autor) > 0) {
            $sx .= '<br>';
            $sx .= '<i>' . $autor . '</i>';
        }
        $sx .= $year;

        return ($sx);
    }

}
?>
