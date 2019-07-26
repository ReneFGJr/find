<?php
/***************************************************************************** WORK *******
 *******************************************************************************************/
$title = '';
$author = '';
$linkw = '<a href="#">';
$linked_i_new = '';

if ((perfil("#ADM") == 1) and (isset($expression[0]['d_r1']))) {
    $linked_e = '<a href="' . base_url(PATH . 'a/' . $expression[0]['d_r1']) . '" class="btn btn-secondary">';
    if (isset($manifestation)) {
        $linked_m = '<a href="' . base_url(PATH . 'a/' . $idm) . '" class="btn btn-secondary">';
    } else {
        $linked_m = '';
    }

    $sx = '';
    $data = array();
    $data['id'] = $id;
    $linked_e_new = $this -> load -> view('find/view/expression_void', $data, true);
    //$data['id'] = $expression[0]['d_r1'];
    $linked_m_new = $this -> load -> view('find/view/manifestation_void', $data, true);
    if (isset($manifestation)) {
        $data['id'] = $idm;
        $data['idw'] = $id;
        $linked_i_new = $this -> load -> view('find/view/item_void', $data, true);
    } else {
        $linked_i_new = '';
        $data['linkm'] = '';
    }

    echo $linked_e . msg('edit_expression') . '</a> | ';
    if (isset($manifestation)) {
        echo $linked_m . msg('edit_manifestation') . '</a> | ';
    }
    //echo $linked_e_new . ' | ';
    echo $linked_m_new . ' | ';
    if (strlen($linked_i_new) > 0) {
        echo $linked_i_new . ' | ';
    }

}

for ($r = 0; $r < count($work); $r++) {
    $type = $work[$r]['c_class'];
    $value = $work[$r]['n_name'];
    //echo '<br>'.$type.'->'.$value;
    $link = '<a href="' . base_url(PATH . 'v/' . $work[$r]['id_cc']) . '">';
    $linka = '</a>';
    switch($type) {
        case 'hasTitle' :
            $linkw = '<a href="' . base_url(PATH . 'v/' . $id) . '">';
            $title = $value;
            break;
        case 'hasAuthor' :
            if (strlen($author) > 0) { $author .= '; ';
            }
            $author .= $link . $value . $linka;
            break;
        case 'hasOrganizator' :
            if (strlen($author) > 0) { $author .= '; ';
            }
            $author .= $link . $value . $linka . ' (org.)';
            break;
    }
}
/***************************************************************************** EXPRESSION *
 *******************************************************************************************/
$form = '';
$language = '';
for ($r = 0; $r < count($expression); $r++) {
    $type = $expression[$r]['c_class'];
    $value = $expression[$r]['n_name'];
    //echo '<br>'.$type.'->'.$value;
    $link = '<a href="' . base_url(PATH . 'v/' . $expression[$r]['id_cc']) . '">';
    $linka = '</a>';
    switch($type) {
        case 'hasFormExpression' :
            if (strlen($form) > 0) { $form .= '; ';
            }
            $form = $link . $value . $linka;
            break;
        case 'hasLanguageExpression' :
            if (strlen($language) > 0) { $language .= '; ';
            }
            $language .= $link . $value . $linka;
            break;
    }
}
/*************************************************************************** MANIFESTATION *
 *******************************************************************************************/

$cover = 'img/no_cover.png';
$editor = msg('[s.n.]');
;
$editor_n = 0;
$year = '';
$place = msg('[s.l.]');
$place_n = 0;
$isbn = '';
$cdu = '';
$cdd = '';
$title_alt = '';
$linkm = '';
$linka = '';
$serie = '';
$pag = '';
$class = '';
if (isset($manifestation)) {
    for ($r = 0; $r < count($manifestation); $r++) {
        $type = $manifestation[$r]['c_class'];
        $value = $manifestation[$r]['n_name'];

        ///echo '<br>' . $type . '->' . $value;
        $link = '<a href="' . base_url(PATH . 'v/' . $manifestation[$r]['id_cc']) . '">';
        $linkm = '<a href="' . base_url(PATH . 'v/' . $manifestation[0]['d_r1']) . '">';
        $linka = '</a>';
        switch($type) {
            case 'hasColorclassification' :
                $valuec = '#888888';
                if (strpos($value, '#') > 0) {
                    $valuec = substr($value, strpos($value, '#'), strlen($value) - 2);
                    $value = substr($value, 0, strpos($value, '#'));
                }
                $class .= $link . '<div style="background-color: ' . $valuec . '; width: 400px; padding: 5px 10px;"><span style="color: #ffffff;">' . $value . $linka . '</span></div>';
                break;
            case 'hasPage' :
                if (strlen($pag) > 0) { $pag .= '; ';
                }
                $pag .= $link . $value . $linka;
                break;
            case 'hasSerieName' :
                if (strlen($serie) > 0) { $serie .= '; ';
                }
                $serie .= $link . $value . $linka;
                break;
            case 'hasTitleAlternative' :
                $title = $value;
                break;
            case 'hasCover' :
                $cover = '_repositorio/image/' . $value;
                break;
            case 'isPublisher' :
                if ($editor_n == 0) {
                    $editor = '';
                    $editor_n = 1;
                }
                if (strlen($editor) > 0) { $editor .= '; ';
                }
                $editor .= $link . $value . $linka;
                break;
            case 'dateOfPublication' :
                if (strlen($year) > 0) { $year .= '; ';
                }
                $year .= $link . $value . $linka;
                break;
            case 'isPlaceOfPublication' :
                if ($place_n == 0) {
                    $place = '';
                    $place_n = 1;
                }
                if (strlen($place) > 0) { $place .= '; ';
                }
                $place .= $link . trim($value) . $linka;
                break;
            case 'hasISBN' :
                if (strlen($isbn) > 0) { $isbn .= '; ';
                }
                $isbn .= $link . $value . $linka;
                break;
            case 'hasClassificationCDU' :
                if (strlen($cdu) > 0) { $cdu .= '; ';
                }
                $cdu .= $link . $value . $linka;
                break;
        }
    }
}

/************** Capítulos do Livro ******************************************/
$chapter_text = '';
if (isset($chapter)) {
    foreach ($chapter as $key => $cap) {
        $autor = '';
        $titulo = '';

        for ($y = 0; $y < count($cap); $y++) {
            $type = $cap[$y]['c_class'];
            $value = $cap[$y]['n_name'];

            //echo '<br>' . $type . '->' . $value;

            $link = '<a href="' . base_url(PATH . 'v/' . $cap[$y]['id_cc']) . '">';
            $linkm = '<a href="' . base_url(PATH . 'v/' . $cap[0]['d_r1']) . '">';
            $linka = '</a>';

            switch($type) {
                case 'hasAuthor' :
                    if (strlen($autor) > 0) {
                        $autor .= '; ';
                    }
                    $autor .= $link . $value . $linka;
                    break;
                case 'hasTitleChapter' :
                    $titulo .= $linkm . $value . $linka;
                    break;
            }
        }
        if (strlen($titulo) == 0) {
            $titulo = msg('without_title');
        }
        if (perfil("#ADM#CAT")) {
            $link = '<a href="' . base_url(PATH . 'a/' . $key) . '">';
            $chapter_text .= $link . '[ed]</a> ';
        }
        $chapter_text .= $titulo;
        $chapter_text .= '<br><i>' . $autor . '</i>';
        $chapter_text .= '</br></br>';
    }
    $chapter_text = '<div class="summary" style="margin-left: 100px;">' . $chapter_text . '</div>';
}

/* Manifestacao */
/**************************/
$manifestacao = '';
if (!isset($manifestation) and isset($linked_m_new)) {
    $manifestacao .= '<div class="alert alert-warning" role="alert">
    ' . msg('manifestation_does_not_exist') . ' ' . $linked_m_new . '</div>';
} else {
}

/**************** serie **************/
if (strlen($serie) > 0) { $serie = msg('serie') . ': ' . $serie . '<br>';
}

/**************** paginacao **************/
if (strlen($pag) > 0) { $pag = ', ' . $pag;
} else { $pag = '.';
}
$manifestacao .= $place . ': ' . $editor . ', ' . $year . $pag . '<br>';
if (strlen($cdu) > 0) { $manifestacao .= 'CDU: ' . $cdu . '<br>';
}
if (strlen($isbn) > 0) { $manifestacao .= 'ISBN: ' . $isbn . '<br>';
}

if (strlen($class) > 0) {
    $manifestacao .= 'Localização:' . $class;
    $manifestacao .= '<br>';
}
/******************** ITENS ********************/
if (isset($itens) and (strlen($itens) > 0)) {

} else {
    $itens = '<div class="alert alert-warning" role="alert">' . msg('itens_does_not_exist') . ' ' . $linked_i_new . '</div>';
}

/********************* Summary *********************/
/***************************** SUMARY ***/
$summary = '<h5>' . msg('Summary') . '</h5>';
echo $chapter_text;
if (perfil("#ADM#CAT")) {
    $summary .= '</br>';
    $summary .= '<span id="new_chapter" style="cursor: pointer;" >' . msg('inclue_chapter_new') . ' >>></span>';
    $summary .= '<script> $("#new_chapter").click(function() {
                            $("#form_chapter").toggle();
                            $("#new_chapter").toggle();                            
                             
                        });</script>';

    $summary .= '<form method="post" action="' . base_url(PATH . 'v/' . $id) . '">' . cr();
    $summary .= '<div id="form_chapter" style="display: none;">' . cr();
    $summary .= '<h6>' . msg('title_chapter_inform') . '</h6>';
    $summary .= '<textarea class="form-control" name="dd50"></textarea>' . cr();
    $summary .= '<input type="hidden" name="action" value="chapter">';
    $summary .= '<input type="hidden" name="dd1" value="' . count($chapter) . '">';
    $summary .= '<input type="hidden" name="dd2" value="' . $idm . '">';
    $summary .= '<input type="hidden" name="chk" value="' . checkpost_link($id) . '">';
    $summary .= '<input type="submit" name="acao" value="' . msg('chapter_new') . '" class="btn btn-secondary">';
    $summary .= '</form>';
    $summary .= '<br>';
    $summary .= '</div>';
    $summary .= '<br>';
}
?>