<?php
class Languages extends CI_model
	{
		function code($l)
			{
				$l = trim($l);
				$o = array('Português'=>'pt', 'pt'=>'pt' , 'en' => 'en', 'por'=>'pt');
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