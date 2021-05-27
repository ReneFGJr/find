<?php
class reports extends CI_Model
    {
        function form()
            {
                $form = new form;
                $_GET['dd1'] = date("d/m/Y");
                $_GET['dd2'] = date("d/m/Y");
                $cp = array();
                array_push($cp,array('$H8','','',false,false));
                array_push($cp,array('$D8','','Data Inicial',true,true));
                array_push($cp,array('$D8','','Data Final',true,true));
                $sx = $form->editar($cp,'');
                return($sx);
            }
        function index($act,$d1,$d2,$d3)
            {
                switch($act)
                    {
                        case 'acervo':                            
                            $sx = $this->acervo($d1,$d2,$d3);
                            $sx .= $this->form();
                            break;
                        default:
                        $sx = $this->menu();
                    }
                return($sx);
            }

        function menu()
            {
                $sx = '<div class="container">';
                $sx .= '<div class="row">';
                $sx .= '<div class="'.bscol(6).'">';
                
                $sx .= '<h2>'.msg('Reports').'</h2>';
                /************************************************/
                $sx .= '<h4>'.msg('Reports_acervo').'</h4>';
                $sx .= '<ul>';
                $sx .= '<li><a href="'.base_url(PATH.'reports/acervo/1').'">'.msg('report_acervo_1').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'reports/acervo/2').'">'.msg('report_acervo_2').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'reports/acervo/3').'">'.msg('report_acervo_3').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'reports/acervo/4').'">'.msg('report_acervo_4').'</a></li>';
                $sx .= '</ul>';

                /************************************************/
                $sx .= '<h4>'.msg('Reports_users').'</h4>';
                $sx .= '<ul>';
                $sx .= '<li><a href="'.base_url(PATH.'reports/users/1').'">'.msg('report_users_1').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'reports/users/2').'">'.msg('report_users_2').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'reports/users/3').'">'.msg('report_users_3').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'reports/users/4').'">'.msg('report_users_4').'</a></li>';
                $sx .= '</ul>';                

                $sx .= '</div>';
                $sx .= '</div>';
                $sx .= '</div>';
                return($sx);                
            }

        function acervo($d1,$d2,$d3)
            {
                $d2 = brtod(get("dd1"));
                $d3 = brtod(get("dd2"));
                
                $this->load->helper('highcharts');
                $sx = '';
                switch($d1)
                    {
                        case '1':
                        $sx = $this->acervo_items($d2,$d3);
                        break;

                        case '2':
                        $sx = $this->catalogador($d2,$d3);
                        break;

                        case '3':
                        $sx = $this->emprestados($d2,$d3);
                        break;                        
                    }
                return($sx);
            }

        function emprestados($d2,$d3)
            {
                $sx = '';
                $sql = "select * 
                        from find_item 
                        INNER JOIN users ON id_us = i_usuario
                        where i_status = 6
                        and i_library = ".LIBRARY."
                        order by i_dt_emprestimo";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                $sx = '';
                $sx .= '<div class="container">';
                $sx .= '<div class="row">';
                $sx .= '<div class="'.bscol(12).'">';
                $sx .= '<table width="100%">';
                $sx .= '<tr>';
                $sx .= '<th width="5%">'.msg('i_tombo').'</th>';
                $sx .= '<th width="35%">'.msg('i_titulo').'</th>';
                $sx .= '<th width="35%">'.msg('us_nome').'</th>';
                $sx .= '<th width="5%">'.msg('i_dt_emprestimo').'</th>';
                $sx .= '<th width="5%">'.msg('i_dt_prev').'</th>';
                $sx .= '<th width="5%">'.msg('status').'</th>';
                $sx .= '</tr>';
                $tto = 0;
                for ($r=0;$r < count($rlt);$r++)
                    {
                        $line = $rlt[$r];
                        $link = '<a href="'.base_url(PATH.'mod/loans/loan_user/'.$line['id_us']).'">';
                        $linka = '</a>';
                        $sx .= '<tr>';
                        $sx .= '<td align="center">'.$line['i_tombo'].'</td>';
                        $sx .= '<td>'.$line['i_titulo'].'</td>';
                        $sx .= '<td>'.$link.$line['us_nome'].$linka.'</td>';
                        $sx .= '<td align="center">'.stodbr($line['i_dt_emprestimo']).'</td>';
                        $sx .= '<td align="center">'.stodbr($line['i_dt_prev']).'</td>';
                        $sta = msg('normal');
                        $sx .= '<td align="center">'.$sta.'</td>';
                        $sx .= '</tr>';
                        $tto++;
                    }
                $sx .= '<tr><td colspan=6>Total '.$tto.' '.msg('items').'</td></tr>';
                $sx .= '</table>';
                $sx .= '</div>';
                $sx .= '</div>';
                $sx .= '</div>';
                return($sx);
            }
        function catalogador($d2,$d3)
            {
                $sx = '';
                $data1 = '2021-01-01';
                $data2 = date("Y-m-d");
                $sql = "select count(*) as total, h_status, h_user, us_nome
                        from find_item
                        inner join library_place ON id_lp = i_library_place
                        inner join find_item_historic ON h_item = id_i
                        inner join users ON h_user = id_us
                            where i_library = '".LIBRARY."' and h_status = 1
                            and (i_created >= '$d2' and i_created <= '$d3')
                            group by h_status, h_user, us_nome
                            order by us_nome";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                $data = array();
                $cats = array();
                for ($r=0;$r < count($rlt);$r++)
                {
                    $ln = $rlt[$r];
                    $data[$r] = $ln['total'];
                    $cats[$r] = ascii($ln['us_nome']);
                    /*
                    print_r($ln);
                    echo '<hr>';
                    */
                }
                $dt = array();
                $dt['DATA'] = $data;
                $dt['CATS'] = $cats;
                $dt['TITLE'] = 'Catalogação entre '.stodbr($data1).' e '.stodbr($data2);
                $dt['TYPE'] = 'bar';
                $dt['LEG_HOR'] = 'Número de obras catalogadas';
                $hc = new highcharts;
                //$sx .= $hc->bar3d($dt);
                $sx .= $hc->grapho($dt);
                return($sx);
            }
        function acervo_items($d2,$d3)
            {   
                $sx = '';
                $sql = "select count(*) as total,
                        i_library_place, lp_name 
                        from find_item 
                        inner join library_place ON id_lp = i_library_place
                        where i_library = '".LIBRARY."'
                        and (i_created >= '$d2' and i_created <= '$d3')
                        group by i_library_place, lp_name
                        order by lp_name
                        ";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                $data = array();
                $cats = array();
                for ($r=0;$r < count($rlt);$r++)
                {
                    $ln = $rlt[$r];

                    $data[$r] = $ln['total'];
                    $cats[$r] = ascii($ln['lp_name']);
                    /*
                    print_r($ln);
                    echo '<hr>';
                    */
                }
                $dt = array();
                $dt['DATA'] = $data;
                $dt['CATS'] = $cats;
                $dt['TITLE'] = 'Acervo por Biblioteca';
                $dt['TYPE'] = 'bar';
                $dt['LEG_HOR'] = 'Número de obras';
                $hc = new highcharts;
                //$sx .= $hc->bar3d($dt);
                $sx .= $hc->grapho($dt);
                return($sx);
            }

    }