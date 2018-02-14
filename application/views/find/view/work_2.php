<?php
/***************************************************************************** WORK *******
 *******************************************************************************************/
$title = '';
$author = '';
$linkw = '<a href="#">';

if (perfil("#ADM")) {
    $linked_e = '<a href="' . base_url('index.php/main/a/' . $expression[0]['d_r1']) . '">';
    if (isset($manifestation))
    {
        $linked_m = '<a href="' . base_url('index.php/main/a/' . $idm) . '">';
    } else {
        $linked_m = '';
    }
    

    $sx = '';
    $data = array();
    $data['id'] = $id;
    $linked_e_new = $this -> load -> view('find/view/expression_void', $data, true);
    //$data['id'] = $expression[0]['d_r1'];
    $linked_m_new = $this -> load -> view('find/view/manifestation_void', $data, true);
     if (isset($manifestation))
        {
        $data['id'] = $idm;
        $linked_i_new = $this -> load -> view('find/view/item_void', $data, true);
        } else {
            $linked_i_new = '';
            $data['linkm'] = '';
        }

    echo $linked_e . msg('edit_expression') . '</a> | ';
    echo $linked_m . msg('edit_manifestation') . '</a> | ';
    echo $linked_e_new . ' | ';
    echo $linked_m_new . ' | ';
    echo $linked_i_new . ' | ';
}

for ($r = 0; $r < count($work); $r++) {
    $type = $work[$r]['c_class'];
    $value = $work[$r]['n_name'];
    //echo '<br>'.$type.'->'.$value;
    $link = '<a href="' . base_url('index.php/main/v/' . $work[$r]['id_cc']) . '">';
    $linka = '</a>';
    switch($type) {
        case 'hasTitle' :
            $linkw = '<a href="' . base_url('index.php/main/v/' . $id) . '">';
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
    $link = '<a href="' . base_url('index.php/main/v/' . $expression[$r]['id_cc']) . '">';
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
if (isset($manifestation)) {
    for ($r = 0; $r < count($manifestation); $r++) {
        $type = $manifestation[$r]['c_class'];
        $value = $manifestation[$r]['n_name'];

        //echo '<br>'.$type.'->'.$value;
        $link = '<a href="' . base_url('index.php/main/v/' . $manifestation[$r]['id_cc']) . '">';
        $linkm = '<a href="' . base_url('index.php/main/v/' . $manifestation[0]['d_r1']) . '">';
        $linka = '</a>';
        switch($type) {
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
?>
<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-2">
        <?php echo $linkm; ?>
        <img src="<?php echo base_url($cover); ?>" width="150">
        </a>
    </div>
    <div class="col-md-8">
        <b><span style="font-size: 140%; color: #000000;"><?php echo $linkm . $title . $linka; ?></span></b><br>
        <b><i><?php echo $author; ?></i><br></b>
        <?php echo $form; ?>: <?php echo $language; ?><br>
        <?php echo $place; ?>: <?php echo $editor; ?>, <?php echo $year; ?>.<br>
        CDU: <?php echo $cdu; ?><br>
        ISBN: <?php echo $isbn; ?>
    </div>
</div>
