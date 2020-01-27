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
        $sx = breadcrumb();
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
            $sx .= $this->row($a);
            break;
        }
        $sx .= '</div>';
        return($sx);                
    }      
}
?>    
