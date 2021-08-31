<?php 
class superadmin extends CI_model
    {
        function row($id='')
            {                
                $sx = $this->libraries->row($id);
                return($sx);
            }

        function index($a='',$i='')
            {
                $this->load->model('libraries');
                $sx = '';
                if (!perfil("#ADMIN"))
                    {
                        redirect(base_url(PATH));
                    }
                switch($a)  
                    {
                    case 'library_edit':
                        $rs = $this->libraries->edit($a,$i);
                        $sx = $rs[0];
                        if ($rs[1]->saved > 0)
                            {
                                redirect(base_url(PATH.'library/admin'));   
                            }
                        break;
                    default:
                        $sx = $this->row($a);
                        break;
                    }
                return($sx);                
            }      
    }