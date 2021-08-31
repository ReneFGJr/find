<?php
class generes extends CI_model
	{
		var $gn = array();
		function code($l)
			{
				$o = array('books#volumes'=>'book', 'books'=>'book','livro'=>'book');
				if (isset($o[$l]))
				{
					return($o[$l]);
				}
				return('');
			}

	}
?>