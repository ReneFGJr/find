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

            /************************ MENU ***********/
            default:
            $sx .= $this->menu();
            break;
        }
        $sx .= '</div>'; /* Container */
        return($sx);                
    } 
    function menu()
    {
        $sx = '<div class="row">';
        $sx .= '<div class="col-12">';
        $sx .= '<h1>Menu de administração</h1>';
        $sx .= '<ul>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/mercadoeditorial_editoras').'">'.msg("mercadoeditorial_editoras").'</a></li>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'config/class').'">'.msg("RDF Classes").'</a></li>';        
        $sx .= '</ul>';
        $sx .= '<h1>Menu das Bibliotecas</h1>';
        $sx .= '<ul>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'library/list').'">'.msg("library_list").'</a></li>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'library/admin').'">'.msg("library_row").'</a></li>';
        $sx .= '</ul>';
        $sx .= '</div>';
        $sx .= '</div>'; /* Row */
        return($sx);
    }     
}
?>    
