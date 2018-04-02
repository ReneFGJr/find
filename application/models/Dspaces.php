<?php
/* http://creativecommons.org/licenses/by/3.0/us/ */
class dspaces extends CI_model {
    var $path = 'E:\DSSpace\collection_13\1';
    var $handle = '';
    var $thumbnai = '';
    var $files = array();

    function le($item) {
        $data = array();
        $data['id'] = $item;
        $data['thumbnail'] = '<img src="' . base_url('img/dspace/no_images.jpg') . '" class="img-responsive img-fluid">' . cr();
        $data['thumbnail_actions'] = '<a href="' . base_url('index.php/dspace/thumbnail/created/' . $item) . '" class="small">' . msg('dspace_create_thumbnail') . '</a>';
        return ($data);
    }

    function thumbnail_create($id) {
        $sx = '';
        $path = $this -> path;
        $fl = $path . '/thumbnail.jpg';
        if (file_exists($fl)) {
            $txt = $this -> readfile($fl);
            $dir = $_SERVER['SCRIPT_FILENAME'];
            $dir = troca($dir, 'index.php', '');
            $fl1 = $dir . '/img/dspace/no_images.jpg';
            $txt = $this -> readfile($fl1);

            $ff = fopen($path . '/thumbnail.jpg', 'w+');
            fwrite($ff, $txt);
            fclose($ff);

            /* file */
            $files = $this -> files;
            $files['THUMBNAIL'] = 'thumbnail.jpg';
            $this -> files = $files;

            $this -> save_content();
            $sx = 'Saved';
        }
        return ($sx);
    }

    function readfile($file) {
        $myfile = fopen($file, "r") or die("Unable to open file!");
        //$sx = '<h4>'.$file.'</h4>';
        $sx = fread($myfile, filesize($file));
        fclose($myfile);
        return ($sx);

        // ORIGINAL, ORIGINAL_AND_DERIVATIVES, TEXT, THUMBNAI
    }

    function le_dublic_core($path) {
        $sx = '';
        $fl = $path . '\dublin_core.xml';
        if (file_exists($fl)) {
            $txt = $this -> readfile($fl);
            $txt = troca($txt, '<', '&lt;');
            $txt = troca($txt, '>', '&gt;');

            $sx .= '<h4>Dublin Core</h4>';
            $sx .= '<pre>' . $txt . '</pre>';
        } else {
            $sx .= '<div class="alert alert-warning">
                              <strong>' . msg('Alert!') . '</strong> ' . msg('dspace_content_not_found') . '
                              <br>' . $fl . '
                            </div>';
        }
        return ($sx);

    }

    function le_licence($path) {
        $sx = '';
        $fl = $path . '\license.txt';
        if (file_exists($fl)) {
            $txt = $this -> readfile($fl);
            $sx .= '<h4>Licence</h4>';
            $sx .= '<pre>' . $txt . '</pre>';
        } else {
            $sx .= '<div class="alert alert-warning">
                              <strong>' . msg('Alert!') . '</strong> ' . msg('dspace_content_not_found') . '
                              <br>' . $fl . '
                            </div>';
        }
        return ($sx);
    }

    function save_content() {
        $path = $this -> path;
        $fl = $path . '\contents';
        $files = $this -> files;
        $sx = '';
        foreach ($files as $type => $value) {
            echo $type . '==' . $value;
            echo '<hr>';
            $sx .= $value . chr(9) . 'handle:' . $type . cr();
        }
        $rlt = fopen($path . '/contents', 'w+');
        fwrite($rlt, $sx);
        fclose($rlt);
        return (1);
    }

    function le_content() {
        $sx = '';
        $files = array();
        $path = $this -> path;
        $fl = $path . '\contents';

        if (file_exists($fl)) {
            $txt = $this -> readfile($fl);
            $sx .= '<h4>Contents</h4>';
            $sx .= '<pre>' . $txt . '</pre>';

            $fls = $txt;
            $fls = troca($fls, chr(9), '¢');
            $fls = troca($fls, chr(10), ';');
            $fls = troca($fls, chr(13), '');
            $f = splitx(';', $fls);
            for ($r = 0; $r < count($f); $r++) {
                $ln = $f[$r];
                $ln = troca($ln, '¢', ';');
                $lns = splitx(';', $ln);
                if (isset($lns[1])) {
                    $type = trim(troca($lns[1], 'bundle:', ''));
                    $files[$type] = $lns[0];
                    $sx .= $type . '==' . cr();
                    $sx .= '<tt>';
                    $sx .= $type;
                    $sx .= chr(9);
                    $sx .= $files[$type];
                    $sx .= chr(9);
                    $fls = $path . '/' . $files[$type];
                    if (file_exists($fls)) {
                        $sx .= ' <span style="color: green;"><b>OK</b></span>';
                    } else {
                        $sx .= ' <span style="color: red;"><b>' . msg('file_not_found') . '</b></span>';
                    }
                    $sx .= '<br>' . cr();
                    $sx .= '</tt>' . cr();
                }

            }
        } else {
            $sx .= '<div class="alert alert-warning">
                              <strong>' . msg('Alert!') . '</strong> ' . msg('dspace_content_not_found') . '
                              <br>' . $fl . '
                            </div>';
        }
        $this -> files = $files;
        return ($sx);
    }

    function le_handle($path) {
        $sx = '';
        $fl = $path . '\handle';
        if (file_exists($fl)) {
            $txt = $this -> readfile($fl);
            $sx .= '<h4>Handle</h4>';
            $sx .= '<pre>' . $txt . '</pre>';
        } else {
            $sx .= '<div class="alert alert-warning">
                              <strong>' . msg('Alert!') . '</strong> ' . msg('dspace_content_not_found') . '
                              <br>' . $fl . '
                            </div>';
        }
        return ($sx);
    }

    function directory($path = '') {
        $file = array();
        $sx = '<pre>';
        $sx .= "<h3>Handle: " . $this -> handle . '</h3>' . cr();
        $sx .= "<h4>Path: " . $this -> path . '</h4>' . cr();
        $d = dir($this -> path);
        while (($file = $d -> read()) !== false) {
            if (is_dir($file)) {
                $sx .= '[DIR]' . $file . cr();
            } else {
                $sx .= $file . cr();

            }

        }
        $d -> close();
        $sx .= '</pre>';
        return ($sx);
    }

    function sql() {
        /*
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('title.none', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('type.none', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('subject.none', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('relation.ispartofseries', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('publisher.none', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('language.iso', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('description.sponsorship', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('description.provenance', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('description.abstract', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('description.none', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('identifier.uri', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('identifier.citation', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('date.issued', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('date.available', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('date.accessioned', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('contributor.author', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('description.checksum', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         INSERT INTO find.rdf_class (c_class, c_prefix, c_created, c_class_main, c_type, c_order, c_pa, c_repetitive, c_vc, c_find, c_identify, c_contextualize, c_justify, c_url, c_url_update) VALUES ('description.size', '5', CURRENT_TIMESTAMP, '0', 'P', '99', '0', '1', '0', '0', '0', '0', '0', '', '0000-00-00');
         */
    }

}
?>
