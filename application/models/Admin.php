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
            case 'classification':
            $this->load->model("classifications");
            $sx .= $this->classifications->action($action,$id);
            break;

            case 'indexing':
            $this->load->model("subjects");
            $sx .= $this->subjects->action($action,$id);
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
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/classification').'">'.msg("Classification").'</a></li>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/indexing').'">'.msg("Vocabulary").'</a></li>';        
        $sx .= '<li>'.'<a href="'.base_url(PATH.'config/class').'">'.msg("RDF Classes").'</a></li>';        
        $sx .= '</ul>';
        $sx .= '</div>';
        $sx .= '</div>'; /* Row */
        return($sx);
    }     
}
?>    
