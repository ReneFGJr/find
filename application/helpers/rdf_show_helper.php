<?php
function rdf_show_Person($line = array())
	{
        $sx = '<div class="col-md-12">';
		$sx .= '<h2>'.$line['n_name'].'</h2>';
        $sx .= '</div>';
        print_r($line);
        //id_cc cc_use
		return($sx);
	}