<?php 
class admin extends CI_model
{
    function row($id='')
    {                
        $sx = $this->libraries->row($id);
        return($sx);
    }

    function index($a='',$action='',$id='')
    {                
        $this->load->model('libraries');     

        if (!perfil("#ADMIN"))
        {
            redirect(base_url(PATH));
        }

        $sx = '<div class="container">';

        /**************************** MENU PRINCIPAL *****************/
        switch($a)
        {
            case 'index':
            $this->load->helper("ai");
            $ai = new ia_index;
            switch($action)
                {
                    case 'author_index':
                    $sx = $ai->export_index('Person',$id);
                    break;

                    case 'search_index':
                    $sx = $ai->export_search('All',$id);
                    break;

                    default:
                    $sx = $ai->export_index($a,$id);
                }
            
            break;

            case 'mercadoeditorial_editoras':
            $this->load->model("Mercadoeditorial_api");
            $sx .= $this->Mercadoeditorial_api->lista_editoras();
            break;

            case 'rede':
            $sx .= $this->rede($action,$id);
            break;

            case 'email':
            $sx .= email_menu('admin/email_test');            
            $sx .= email_data();
            break;

            case 'email_test':
            $dt = email_le();
            $sx .= 'Enviando ... '.$dt['smtp_protocol'].' '.msg('protocol').'.';
            $sx .= enviaremail('renefgj@gmail.com','E-mail de teste ','Teste de email '.date("d/m/Y H:i:s"),1);
            break;            

            case 'email_ed':
            $socials = new socials;            
            $sx .= $socials->social('email');
            $dt = email_edit();
            $sx .= $dt[0];
            if ($dt[1]->saved > 0)
            {
                redirect(base_url(PATH.'admin/email'));
            }
            break;            

            /************************ MENU ***********/
            default:
            $sx = $this->menu();            
            break;
        }
        $sx .= '</div>'; /* Container */
        return($sx);                
    }

    function rede($d1,$d2)
        {
            $sx = '<h1>Rede - '.$d1.'-'.$d2.'</h1>';
            switch($d1)
                {
                    case 'view':
                    $sx .= $this->libraries->view_rede($d2);
                    break;

                    default:
                    $sx .= $this->libraries->row_rede($d1,$d2);
                    break;
                }
            return($sx);
        } 
    function menu()
    {
        $sx = '<div class="container">';
        $sx .= '<div class="row">';
        $sx .= '<div class="'.bscol(12).'">';
        $sx .= '<h1>Relatórios</h1>';
        $sx .= '<ul>';        
        $sx .= '<li>'.'<a href="'.base_url(PATH.'reports').'">'.msg('Reports').'</a>'.'</li>';
        $sx .= '</ul>';

        $sx .= '<h1>Menu de administração</h1>';
        $sx .= '<ul>';
        //$sx .= '<li>'.'<a href="'.base_url(PATH.'admin/mercadoeditorial_editoras').'">'.msg("mercadoeditorial_editoras").'</a></li>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'social/group').'">Permissões de grupos e usuários</a></li>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/email/').'">'.msg("Email_configuration").'</a></li>';        
        $sx .= '<li>'.'<a href="'.base_url(PATH.'setup').'">Configurações</a></li>';
        $sx .= '</ul>';

        $sx .= '<h1>Menu das Bibliotecas</h1>';
        $sx .= '<ul>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'library/list').'">'.msg("library_list").'</a></li>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'library/admin').'">'.msg("library_row").'</a></li>';
        $sx .= '</ul>';

        $sx .= '<h1>FRBR</h1>';
        $sx .= '<ul>';
        //$sx .= '<li>'.'<a href="'.base_url(PATH.'admin/mercadoeditorial_editoras').'">'.msg("mercadoeditorial_editoras").'</a></li>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'config/forms').'">'.msg("Catalog Forms").'</a></li>';        
        $sx .= '<li>'.'<a href="'.base_url(PATH.'config/class').'">'.msg("RDF Classes").'</a></li>';        

        /* Index */
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/index/author_index').'">'.msg("Índice de autores").'</a></li>';
        $sx .= $this->menu_index();
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/index/bookselft').'">'.msg("Índice da estante").'</a></li>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/index/search_index').'">'.msg("Índice de busca").'</a></li>';

        $sx .= '<h1>SUPER ADMIN</h1>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/rede').'">'.msg("Redes de Bibliotecas").'</a></li>';

        $sx .= '</ul>';

        $sx .= '</div>';
        $sx .= '</div>'; /* Row */
        $sx .= '</div>'; /* Container */
        return($sx);
    }    
    function menu_index()
        {
            $sql = "SELECT c_class, count(*) as total FROM rdf_class 
                        INNER JOIN rdf_data ON d_p = id_c
                        INNER JOIN find_item ON i_manitestation = d_r1 and i_library = '".LIBRARY."'
                        WHERE (c_class like 'hasClass%') or (c_class like 'hasSub%')
                        group by c_class
                        ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx = '';
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/index/'.$line['c_class']).'">'.msg("Index of").' '.msg($line['c_class']).'</a></li>';
                }
            return($sx);
        } 
}
