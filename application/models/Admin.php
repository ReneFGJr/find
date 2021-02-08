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
            case 'mercadoeditorial_editoras':
            $this->load->model("Mercadoeditorial_api");
            $sx .= $this->Mercadoeditorial_api->lista_editoras();
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
        $sx .= '<li>'.'<a href="'.base_url(PATH.'config/class').'">'.msg("RDF Classes").'</a></li>';        
        $sx .= '</ul>';

        $sx .= '</div>';
        $sx .= '</div>'; /* Row */
        $sx .= '</div>'; /* Container */
        return($sx);
    }     
}
