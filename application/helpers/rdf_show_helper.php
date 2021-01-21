<?php
function rdf_show_Person($line = array())
	{
        $sx = '<div class="col-md-12">';
		$sx .= '<h2>'.$line['n_name'].'</h2>';
        $sx .= '</div>';
        
        $id = $line['id_cc'];

        $rdf = new rdf;
        $dt = $rdf->le_data($id);


        $books = '';

        for ($r=0;$r < count($dt);$r++)
            {
                $l = $dt[$r];
                switch($l['c_class'])
                    {
                        case 'hasAuthor':
                            print_r($l);
                            $link = '<a href="'.base_url(PATH.'v/'.$l['d_r1']).'">';
                            $linka = '</a>';
                            $books .= '<div class="col-md-2"></div>';
                            $books .= '<div class="col-md-10">'.$link.$l['n_name'].$linka.'</div>';
                        break;
                    }
            }
        $sx .= '<div class="col-md-12"><h3>'.msg('books_author_titles').'</h3></div>'.$books;
        //id_cc cc_use
		return($sx);
	}