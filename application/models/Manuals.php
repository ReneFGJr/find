<?php
class manuals extends CI_model {
    function structure() {
        $sql = "CREATE TABLE IF NOT EXISTS `_manual_concept` (
            `id_mc` serial NOT NULL,
              `mc_term` int(11) NOT NULL,
              `mc_creadted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `mc_use` int(11) NOT NULL,
              `mc_class` char(15) COLLATE utf8_bin NOT NULL,
              `mc_rel` int(11) NOT NULL DEFAULT '0'
            ) DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;";
        $rlt = $this -> db -> query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `_manual_name` (
            `id_m` serial NOT NULL,
              `m_txt` text COLLATE utf8_bin NOT NULL,
              `m_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;";
        $rlt = $this -> db -> query($sql);
    }

    function cab() {
        $data = array();
        $data['title'] = 'Ajuda';
        $this -> load -> view('header/books_header', $data);
        $sx = '';
        $sx .= '<div class="row" style="background-color: #80ff80; padding: 10px 20px;">';
        $sx .= '<div class="col-md-1"><a href="'.base_url(PATH.'help').'">HOME</a>';
        $sx .= '</div>';
        $data['content'] = $sx;
        $data['fluid'] = true;
        $this -> load -> view('content', $data);
        $data['fluid'] = false;
    }

    function navbar() {
        $sx = '
                    <nav class="navbar navbar-expand-lg navbar-light bg-light">
                      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#conteudoNavbarSuportado" aria-controls="conteudoNavbarSuportado" aria-expanded="false" aria-label="Alterna navegação">
                        <span class="navbar-toggler-icon"></span> ' . msg('help') . '
                      </button>
                    
                      <div class="collapse navbar-collapse" id="conteudoNavbarSuportado">
                        <ul class="navbar-nav mr-auto">
                          <li class="nav-item active">
                            <a class="nav-link" href="' . base_url(PATH . 'help') . '">Home <span class="sr-only">(página atual)</span></a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="#">Link</a>
                          </li>
                          <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Dropdown
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                              <a class="dropdown-item" href="#">Ação</a>
                              <a class="dropdown-item" href="#">Outra ação</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item" href="#">Algo mais aqui</a>
                            </div>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link disabled" href="#">Desativado</a>
                          </li>
                        </ul>
                        <form class="form-inline my-2 my-lg-0">
                          <input class="form-control mr-sm-2" type="search" placeholder="Pesquisar" aria-label="Pesquisar">
                          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Pesquisar</button>
                        </form>
                      </div>
                    </nav>              
                ';
        return ($sx);

    }

    function index($act = '', $id = '') {
        $this -> cab();
        $data = array();
        switch($act) {
            case 'structure' :
                $this -> structure();
                $data['content'] = "Estrutura de Tabelas Criadas";
                break;
            case 'pg' :
                $t = get("q");
                $data['content'] = $this -> busca_pg($t);
                break;
            case 'pc' :
                $t = get("q") . get("dd0");
                $data['content'] = $this -> edit_pg($t);
                break;
            default :
                $data['content'] = $this -> indice();
                break;
        }
        $this -> load -> view('content', $data);
    }

    function indice() {
        $sx = 'PG INICIAL';
        $sx = $this -> busca_pg('index');
        return ($sx);
    }

    function name($q) {
        $sql = "select * from _manual_name 
                        WHERE m_txt = '$q'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sqli = "insert into _manual_name (m_txt) value ('$q')";
            $rltq = $this -> db -> query($sqli);
            sleep(1);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
        }
        $idt = $rlt[0]['id_m'];
        return ($idt);
    }

    function edit_pg($q) {
        if (strlen(get("acao")) == 0) {
            /** Nova Página Name */
            $idt = $this -> name($q);

            /*** Conceito ***/
            $sql = "select * from _manual_concept where mc_term = $idt ";
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            if (count($rlt) == 0) {
                $sqli = "insert into _manual_concept (mc_term, mc_class) value ($idt,'PG')";
                $rltq = $this -> db -> query($sqli);
                sleep(1);
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
            }
            $idc = $rlt[0]['id_mc'];

            /* Recupera conteúdo */
            $sql = "select * from _manual_concept
                                LEFT JOIN _manual_name ON mc_term = id_m 
                                    where mc_rel = $idc
                                    order by mc_rel";
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $class = $line['mc_class'];
                $txt = $line['m_txt'];
                switch($class) {
                    case 'hasTITLE' :
                        $_POST['dd2'] = $txt;
                        break;
                    case 'hasCONTENT' :
                        $_POST['dd3'] = $txt;
                        break;
                }
            }
        } else {
            $idc = get("dd1");
        }

        $sx = '';
        $sx .= '<div class="row">';
        $sx .= '<div class="col-md-1"></div>';
        $sx .= '<div class="col-md-10">';
        $form = new form;
        $cp = array();
        array_push($cp, array('$HV', '', $q, false, false));
        array_push($cp, array('$HV', '', $idc, true, false));
        array_push($cp, array('$S100', '', msg('page_title'), true, true));
        array_push($cp, array('$T80:10', '', msg('page_content'), true, true));
        $tela = $form -> editar($cp, '');

        if ($form -> saved > 0) {
            $idc = get("dd1");
            $this -> rdf($idc, 'hasTITLE', get("dd2"));
            $this -> rdf($idc, 'hasCONTENT', get("dd3"));
            redirect(base_url(PATH . 'help/?q=' . $q));
        }
        $sx .= $tela;

        $sx .= '</div>';
        $sx .= '<div class="col-md-1"></div>';
        $sx .= '</div>';
        return ($sx);
    }

    function rdf($c, $prop, $t) {
        $idt = $this -> name($t);
        $sql = "select * from _manual_concept 
                        where mc_rel = $c AND mc_class = '$prop' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sql = "insert into _manual_concept
                            (mc_term, mc_class, mc_rel)
                            values
                            ($idt,'$prop',$c)";
            $rlt = $this -> db -> query($sql);
        } else {
            $sql = "update _manual_concept 
                            set mc_term = $idt
                            where id_mc = " . $rlt[0]['id_mc'];
            $rlt = $this -> db -> query($sql);
        }
        return (1);
    }

    function busca_pg($q = '') {
        $sx = '';
        $sql = "select * from _manual_name
                        INNER JOIN _manual_concept on id_m = mc_term 
                        WHERE m_txt = '$q'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sx .= '<div class="row">';
            $sx .= $this -> link_edit_content($q);
            $sx .= '</div>';
        } else {
            if (count($rlt) == 1) {
                $idc = $rlt[0]['id_mc'];
                $sx .= $this -> recupera_conteudo($idc);
            } else {
                $sx .= 'VARIOS CONTEÚDOS';
            }
        }
        return ($sx);
    }

    function link_edit_content($q, $erro = 1) {

        /** Mostrar erro de conteúdo **/
        if ($erro == 1) {
            $sx = '<div class="col-md-1"></div>';
            $sx .= '<div class="col-md-10">';
            $sx .= '<div class="alert alert-danger" role="alert">
                            sem página registrada!
                            </div>';
            $sx .= '</div>';
            $sx .= '<div class="col-md-1"></div>';
        }

        /** Link para o conteúdo **/
        $link = '<a href="' . base_url(PATH . 'help/pc?q=' . $q) . '">';
        $linka = '</a>';
        $sx .= '<div class="col-md-1"></div>';
        $sx .= '<div class="col-md-10">';
        $sx .= '<div class="alert alert-success" role="alert">
                            Criar página <b>' . $link . $q . $linka . '</b>!
                            </div>';
        $sx .= '</div>';
        $sx .= '<div class="col-md-1"></div>';
        return ($sx);
    }

    function recupera_conteudo($idc) {
        $idc = sonumero($idc);
        $sql = "select * from _manual_concept
                            LEFT JOIN _manual_name ON mc_term = id_m 
                                where mc_rel = $idc or id_mc = $idc
                                order by id_mc";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';
        $pg = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            switch($line['mc_class']) {
                case 'PG_INDEX' :
                    $q = 'index';
                    $sx .= '<a href="' . base_url(PATH . 'help/pc?q=' . $q) . '">editar</a>';
                    break;
                case 'hasTITLE' :
                    $sx .= '<div class="col-md-12">';
                    $sx .= '<h1>' . $this -> mst($line['m_txt']) . '</h1>';
                    $sx .= '</div>';
                    break;
                case 'hasCONTENT' :
                    $sx .= '<div class="col-md-12">';
                    $sx .= mst($this -> mst($line['m_txt']));
                    $sx .= '</div>';
                    break;
                case 'PG' :
                    $q = $line['m_txt'];
                    $sx .= '<a href="' . base_url(PATH . 'help/pc?q=' . $q) . '">editar</a>';
                    break;
                default :
                    $sx .= '<div class="col-md-12">';
                    $sx .= $this -> mst($line['m_txt']) . '<hr>';
                    $sx .= '</div>';
                    break;
            }
        }
        if (strlen($sx) == 0) {
            $sx .= $this -> link_edit_content($q);
        }

        $sx = '<div class="row">' . $sx . '</div>';
        return ($sx);
    }

    function image($n, $t) {
        $sx = '';
        if (isset($_FILES['userfile']['name'])) {
            /* Checa diretorio */
            $uploadfile = '_manual';
            check_dir($uploadfile);
            $uploadfile .= '/img';
            check_dir($uploadfile);
            /* Nome do arquivo */
            $uploadfile .= '/' . $n . '.jpg';
            echo '<br>===>' . $uploadfile;
            echo '<hr>';

            if (strlen($arq) > 0) {
                if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                    echo "Arquivo válido e enviado com sucesso.\n";
                } else {
                    echo "Possível ataque de upload de arquivo!\n";
                }
            }
        }

        $file = '_manual/img/' . $n . '.jpg';
        if (file_exists($file)) {
            $sx .= '</div>';
            $sx .= '<div class="col-md-1"></div>';
            $sx .= '<div class="col-md-10">';
            $sx .= '<img src="'.base_url($file).'" class="img-fluid" style="border:1px solid #333333; box-shadow: 10px 10px;">'.cr();
            $sx .= '</div>';
            $sx .= '<div class="col-md-1"></div>';
            $sx .= '<div class="col-md-12">';
        } else {
            $sx = '';
            $sx .= '<div style="width: 400px; border: 1px solid #000000;">';
            $sx .= '<center>';
            $sx .= '<h4>Sem imagem</h4>';
            $sx .= '<br>' . $t . ':' . $n;
            $sx .= '<form enctype="multipart/form-data" method="POST">
                        <!-- MAX_FILE_SIZE deve preceder o campo input -->
                        <input type="hidden" name="MAX_FILE_SIZE" value="3000000000" />
                        <!-- O Nome do elemento input determina o nome da array $_FILES -->
                        Enviar esse arquivo: <input name="userfile" type="file" />
                        <input type="submit" value="Enviar arquivo" />
                    </form>';
            $sx .= '</center><br><br>';
            $sx .= '</div>';
        }
        return ($sx);
    }

    function mst($t) {
        $loop = 0;
        /* Arquivos externos */
        $t = troca($t, '[[', '{');
        $t = troca($t, ']]', '} ');

        /*********************************** arquivos*************************/
        while ((strpos(' ' . $t, '{') > 0) and ($loop++ < 5)) {
            $t1 = substr($t, 0, strpos($t, '{'));
            $t2 = substr($t, strpos($t, '{') + 1, strlen($t));
            $t3 = substr($t2, 0, strpos($t2, ':'));
            $t4 = substr($t2, strlen($t3) + 1, strpos($t2, '}') - strlen($t3) - 1);
            $t5 = substr($t2, strpos($t2, '}') + 1, strlen($t2));
            if (strlen($t3) > 0) {
                switch($t3) {
                    case 'img' :
                        $t = $t1 . $this -> image($t4, $t3) . $t5;
                        break;
                    default :
                        $t = $t1 . 'FILE:' . $t3 . $t4 . $t5;
                }

            } else {
                $t = $t1 . '{' . $t2;
            }
        }

        /*********************************** LINK ***************************/
        while ((strpos(' ' . $t, '[') > 0) and ($loop++ < 5)) {
            $t1 = substr($t, 0, strpos($t, '['));
            $t2 = substr($t, strpos($t, '[') + 1, strlen($t));
            $t3 = substr($t2, 0, strpos($t2, ']'));
            $t4 = substr($t2, strpos($t2, ']') + 1, strlen($t2));
            if (strlen($t3) > 0) {
                $t3a = strtolower(troca($t3, ' ', '_'));
                $url = base_url(PATH . 'help/pg?q=' . $t3a);
                $url = troca($url, '[::1]', 'localhost');
                $t = $t1 . '<a href="' . $url . '">' . $t3 . '</a>' . $t4;
            } else {
                $t = $t1 . '{' . $t2;
            }
        }
        return ($t);
    }

}
?>
