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
        $sx = '';
        $sx .= breadcrumb();

        $sx .= '<div class="row">';        

        if (!perfil("#ADMIN"))
        {
            redirect(base_url(PATH));
        }

        switch($a)  
        {
            case 'classification':
            $this->load->model("classifications");
            $sx .= $this->classifications->action($action,$id);
            if (perfil("#ADM") > 0)
            {
                $sx .= '<a href="'.base_url(PATH.'admin/classification/thesa/'.$id).'" class="btn btn-outline-primary">Import from Thesa</a>';
            }
            break;
            default:
            $sx .= $this->menu();
            break;
        }
        $sx .= '</div>';
        return($sx);                
    } 
    function menu()
        {
            $sx = '<div class="row">';
            $sx .= '<div class="col-12">';
            $sx .= '<ul>';
            $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/classification').'">'.msg("Classification").'</a>';
            $sx .= '</ul>';
            $sx .= '</div>';
            $sx .= '</div>';
            return($sx);
        }     
}
?>    
