<?php
class isbn extends CI_model
{
	function get($isbn,$type)
	{
		$file = $this->file_locate($isbn,$type);
		if (file_exists($file))
		{
			$t = file_get_contents($file);
			return($t);
		}

		$isbns = $this->isbns($isbn);

		/***** File Cached not found ***************************************/
		switch($type)
		{
			/**************************************** GOOGLE *******************/
			case 'GOOGL':
			$url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn;
			$t = read_link($url);
			break;

			/***************************************** AMAZON ********************/
			case 'AMAZO':
			$url = 'https://www.amazon.com.br/dp/' . $isbns['isbn10'];
			$t = read_link($url);
			if (strlen($t)==0)
			{
				$url = 'https://www.amazon.com.br/dp/' . $isbns['isbn13'];
				$t = read_link($url);			
			}
			break;
		}
		if (isset($t))
		{
			file_put_contents($file, $t);	
		} else {
			$t = '';
		}		
		return($t);
	}
	function file_locate($isbn,$type)
	{
		/* 978-8585-7461-62 */
		$isbn = $this->isbns($isbn);
		$isbn = $isbn['isbn13'];
		$part1 = substr($isbn,0,3);
		$part2 = substr($isbn,3,4);
		$part3 = substr($isbn,7,10);
		$dir = '__cached';
		check_dir($dir);
		check_dir($dir.'/'.$part1);
		check_dir($dir.'/'.$part1 .'/'.$part2);
		$file = $dir.'/'.$part1.'/'.$part2.'/'.$isbn.'-'.$type.'.file';
		return($file);
	}

	/************* ISBNS ***********************************/
	function isbns($isbn)
	{
		$rsp = array();
		if (strlen($isbn) == 13) {
			$rsp['isbn13'] = $isbn;
			$rsp['isbn10'] = isbn13to10($isbn);
		} else {
			$rsp['isbn10'] = $isbn;
			$rsp['isbn13'] = isbn10to13($isbn);
		}
		return($rsp);	
	}

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