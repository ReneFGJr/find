<?php 
class superadmin extends CI_model
    {
        function index($a='',$i='')
            {
                if (!perfil("#ADMIN"))
                    {
                        redirect(PATH);
                    }
                switch($a)  
                    {
                    case '':
                        break;
                    default:
                        break;
                    }                
            }      
    }
?>    
