<?php
/***************************************************************************** WORK *******
 *******************************************************************************************/
$title = '';
$author = '';
$linkw = '<a href="#">';
$linked_i_new = '';

if ((perfil("#ADM") == 1) and (isset($expression[0]['d_r1'])) ) {
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
            case 'hasColorclassification':
                $valuec = '#888888';
                if (strpos($value,'#') > 0)
                    {
                        $valuec = substr($value,strpos($value,'#'),strlen($value)-2);
                        $value = substr($value,0,strpos($value,'#'));
                    }
                $class .= $link.'<div style="background-color: '.$valuec.'; width: 400px; padding: 5px 10px;"><span style="color: #ffffff;">'.$value.$linka.'</span></div>';               
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
                    if (strlen($autor) > 0)
                        {
                            $autor .='; ';
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
            $chapter_text .= $link.'[ed]</a> ';
        }
        $chapter_text .= $titulo;
        $chapter_text .= '<br><i>' . $autor . '</i>';
        $chapter_text .= '</br></br>';
    }
    $chapter_text = '<div class="summary" style="margin-left: 100px;">'.$chapter_text.'</div>';
}
?>
<div class="row" style="margin-bottom: 20px;">
<div class="col-lg-2 col-md-3 col-xs-4 col-sm-4">
<?php echo $linkm; ?>
<img src="<?php echo base_url($cover); ?>" width="150" style="box-shadow: 5px 5px 8px #888888;">
</a>
</div>
<div class="col-lg-10 col-md-9 col-xs-8  col-sm-8">
<b><span style="font-size: 140%; color: #000000;"><?php echo $linkm . $title . $linka; ?>
</span></b><br>
<b><i><?php echo $author; ?>
</i><br></b>
<?php echo $form; ?>:
<?php echo $language; ?>
<br>
<?php
/**************************/
if (!isset($manifestation) and isset($linked_m_new)) {
    echo '
<div class="alert alert-warning" role="alert">
' . msg('manifestation_does_not_exist') . ' ' . $linked_m_new . '
</div>
';
} else {
    /**************** serie **************/
    if (strlen($serie) > 0) { echo msg('serie') . ': ' . $serie . '<br>';
    }

    if (strlen($pag) > 0) { $pag = ', ' . $pag;
    } else { $pag = '.';
    }
    echo $place . ': ' . $editor . ', ' . $year . $pag . '<br>';
    if (strlen($cdu) > 0) { echo 'CDU: ' . $cdu . '<br>';
    }
    if (strlen($isbn) > 0) { echo 'ISBN: ' . $isbn . '<br>';
    }

    /*************************************************************************** Chapters ***/
    echo '<br/>';
/*********************************/
    if (strlen($class) > 0)
        {             
            echo 'Localização:'.$class;
            echo '<br>';
        }
    /***************************** SUMARY ***/            
    echo '<h5>' . msg('Summary') . '</h5>';
    echo $chapter_text;
    if (perfil("#ADM#CAT")) {
        echo '</br>';
        echo '<span id="new_chapter" style="cursor: pointer;" >' . msg('inclue_chapter_new') . ' >>></span>';
        echo '<script> $("#new_chapter").click(function() {
                            $("#form_chapter").toggle();
                            $("#new_chapter").toggle();                            
                             
                        });</script>';
        
        echo '<form method="post" action="' . base_url(PATH . 'v/' . $id) . '">'.cr();
        echo '<div id="form_chapter" style="display: none;">'.cr();
        echo '<h6>'.msg('title_chapter_inform').'</h6>';
        echo '<textarea class="form-control" name="dd50"></textarea>'.cr();
        echo '<input type="hidden" name="action" value="chapter">';
        echo '<input type="hidden" name="dd1" value="' . count($chapter) . '">';
        echo '<input type="hidden" name="dd2" value="' . $idm . '">';
        echo '<input type="hidden" name="chk" value="' . checkpost_link($id) . '">';
        echo '<input type="submit" name="acao" value="' . msg('chapter_new') . '" class="btn btn-secondary">';        
        echo '</form>';
        echo '<br>';
        echo '</div>';
        echo '<br>';        
    }


    /************************************************************************** Itens *******/
    if (isset($itens) and (strlen($itens) > 0)) {
        echo $itens;
    } else {
        echo '
<div class="alert alert-warning" role="alert">
' . msg('itens_does_not_exist') . ' ' . $linked_i_new . '
</div>';
    }
}
?>

</div>
</div>
