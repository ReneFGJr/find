<?php
class isbn extends CI_model
	{
		function format($isbn)
			{
				$isbn = strtoupper($isbn);
				$s = sonumero($isbn);
				if (substr($isbn,strlen($isbn)-1,1) == 'X')
				{
					$s .= 'X';
				}
				return($s);
			}
	}

	/*

		$isbn = sonumero($isbn);
		if (substr($isbn, strlen($isbn), 1) == 'X') {
			$isbn .= 'X';
		}

		if (strlen($isbn) == 13) {
			$rsp['isbn13'] = $isbn;
			$rsp['isbn10'] = isbn13to10($isbn);
		} else {
			$rsp['isbn10'] = $isbn;
			$rsp['isbn13'] = isbn10to13($isbn);
		}
		*/
?>	