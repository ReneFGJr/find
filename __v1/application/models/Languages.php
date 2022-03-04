<?php
class Languages extends CI_model
	{
		function form()
			{
				$o = array('pt'=>'Português','en'=>'Inglês','es'=>'Espanhol','fr'=>'Frances','ge'=>'Alemão');
				$sx = '';
				foreach ($o as $key => $value) {
					if ($sx != '') { $sx .= '&'; }
					$sx .= $key.':'.$value;
				}
				return($sx);
			}
		function code($l)
			{
				$l = trim($l);
				$o = array('Português'=>'pt', 'pt_BR'=>'pt' , 'en' => 'en', 'por'=>'pt','português'=>'pt','portugues'=>'pt');
				foreach ($o as $key => $value) {
					if ($l==$key)
					{
						return($value);	
					}
					
				}
				return('');
			}
	}
?>