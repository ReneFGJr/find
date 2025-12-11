<?php
class Languages extends CI_model
	{
		function form()
			{
				$o = array('pt'=>'Português','en'=>'Inglês','es'=>'Espanhol','fr'=>'Frances','ge'=>'Alemão','it'=>'Italiano','un'=>'Multilingue');
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
				$o = array('Português'=>'pt', 'pt_BR'=>'pt' , 'en' => 'en', 'por'=>'pt','português'=>'pt','portugues'=>'pt','Inglês'=>'en','ingles'=>'en','English'=>'en','Espanhol'=>'es','espanol'=>'es','Spanish'=>'es','Francês'=>'fr','frances'=>'fr','French'=>'fr','Alemão'=>'de','Alemao'=>'de','German'=>'de','Italiano'=>'it','Italian'=>'it','Multilingue'=>'un','Multilingual'=>'un');
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