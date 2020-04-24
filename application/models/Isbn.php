<?php
class isbn extends CI_model
{
	var $vurl = '';
	function urls($isbn,$type)
	{
		switch($type)
		{
			case 'FINDS':
			$url = 'http://192.168.0.115/sisdoc/find/index.php/main/x/' . $isbn;
			break;

			case 'GOOGL':
			$url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn;
			break;

			case 'AMAZO':
			$url = 'https://www.amazon.com.br/dp/'.$isbn;
			break;

			case 'MERCA':
			$url = $this->mercadoeditorial_api->url.'?isbn='.$isbn;
			break;			
		}
		return($url);
	}

	/**********************************************************************/
	function get($isbn,$type)
	{
		$file = $this->file_locate($isbn,$type);
		if (file_exists($file))
		{
			$t = file_get_contents($file);
			return($t);
		}

		/***** File Cached not found ***************************************/
		switch($type)
		{
			/**************************************** GOOGLE *******************/
			case 'MERCA':
			$url = $this->urls($isbn,$type);
			$t = read_link($url);
			$this->vurl = $url;
			break;

			/**************************************** GOOGLE *******************/
			case 'FINDS':
			$url = $this->urls($isbn,$type);
			$t = read_link($url);
			$this->vurl = $url;
			break;

			/**************************************** GOOGLE *******************/
			case 'GOOGL':
			$url = $this->urls($isbn,$type);
			$t = read_link($url);
			$this->vurl = $url;
			break;

			/***************************************** AMAZON ********************/
			case 'AMAZO':
			$isbns = $this->isbn->isbns($isbn);
			$url = $this->urls($isbns['isbn13'],$type);
			$t = read_link($url);
			if (strlen($t)==0)
			{
				$url = $this->urls($isbns['isbn10'],$type);
				$t = read_link($url);
			}
			$this->vurl = $url;
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
		if (is_array($isbn))
		{
			$isbn = $isbn['isbn13'];
		}
		$isbn = troca($isbn,'-','');
		$isbn = troca($isbn,'.','');
		$rsp = array();

		if (strlen($isbn) == 13) {
			$rsp['isbn13'] = $isbn;
			$rsp['isbn10'] = isbn13to10($isbn);
		} else {
			$rsp['isbn10'] = $isbn;
			$rsp['isbn13'] = isbn10to13($isbn);
		}

		$rsp['isbn10f'] = substr($rsp['isbn10'],0,2).'-'.substr($rsp['isbn10'],2,5).'-'.substr($rsp['isbn10'],7,2).'-'.substr($rsp['isbn10'],9,1);
		$rsp['isbn13f'] = substr($rsp['isbn13'],0,3).'-'.substr($rsp['isbn13'],3,4).'-'.substr($rsp['isbn13'],7,5).'-'.substr($rsp['isbn13'],12,1);
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
?>