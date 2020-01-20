<?php
class Languages extends CI_model
	{
		function code($l)
			{
				$o = array('Português'=>'pt', 'pt'=>'pt');
				if (isset($o[$l]))
				{
					return($o[$l]);
				}
				return('');
			}
	}
?>	