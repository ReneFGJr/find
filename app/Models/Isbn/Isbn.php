<?php

namespace App\Models\Isbn;

use CodeIgniter\Model;

class Isbn extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'isbns';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	var $vurl = '';

	/*************************************************************************** GET */
	function get($isbn, $type)
	{
		$tmp = '../.tmp/';
		dircheck($tmp);
		$tmp = '../.tmp/isbn/';
		dircheck($tmp);

		$file = $tmp . 'isbn-' . $isbn . '.' . strtolower($type);
		if (file_exists($file)) {
			$txt = file_get_contents($file);
			return $txt;
		}

		switch ($type) {
			case 'OCLC':
				$code = 999;
				$url = 'http://classify.oclc.org/classify2/Classify?isbn=' . $isbn . '&summary=true';
				$json = read_link($url);
				$xml = simplexml_load_string($json);

				$json = json_encode($xml);
				$array = (array)json_decode($json, TRUE);

				if (isset($array['response'])) {
					$rsp = (array)$array['response'];
					$att = (array)$rsp['@attributes'];
					$code = round($att['code']);
				}

				if ($code == 0) {
					file_put_contents($file, $json);
				} else {
					$array = array();
				}
				return $array;
				break;

			case 'GOOGLE':
				$isbn = '9786589367307';
				$url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn;
				$json = read_link($url);
				if (strlen($json) > 10) {
					file_put_contents($file, $json);
					return $json;
				}
				break;

			case 'MERCA':
				/* Link produção */
				$url = 'https://api.mercadoeditorial.org/api/v1/requisitar_livro_unico';
				$url .= '?isbn=' . $isbn;
				$json = read_link($url);
				$rsp = json_decode($json, true);
				if ($rsp['status_code'] != '101') {
					file_put_contents($file, $json);
				} else {
					$json = json_encode(array('status_code' => '101'));
				}
				return $json;
				break;

			default:
				/* Link para testes */
				$sx = bsmessage('TYPE NOT EXIST ' . $type, 3);
				break;

				return $sx;
		}
	}


	function urls($isbn, $type)
	{
		switch ($type) {
			case 'FINDS':
				$url = 'http://192.168.0.115/sisdoc/find/index.php/main/x/' . $isbn;
				break;

			case 'GOOGL':
				$url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn;
				break;

			case 'AMAZO':
				$url = 'https://www.amazon.com.br/dp/' . $isbn;
				break;

			case 'OCLC':
				$url = 'http://classify.oclc.org/classify2/Classify?isbn=' . $isbn . '&summary=true';
				break;

			case 'MERCA':
				$url = $this->mercadoeditorial_api->url . '?isbn=' . $isbn;
				break;
		}
		return ($url);
	}

	/**********************************************************************/
	function xget($isbn, $type)
	{
		$file = $this->file_locate($isbn, $type);

		if (file_exists($file)) {
			$t = file_get_contents($file);
			if (strlen($t) > 0) {
				return ($t);
			}
		}

		/***** File Cached not found ***************************************/
		switch ($type) {
				/**************************************** GOOGLE *******************/
			case 'MERCA':
				$url = $this->urls($isbn, $type);
				$t = read_link($url);
				$this->vurl = $url;
				break;

				/**************************************** GOOGLE *******************/
			case 'FINDS':
				$url = $this->urls($isbn, $type);
				$t = read_link($url);
				$this->vurl = $url;
				break;

				/**************************************** GOOGLE *******************/
			case 'GOOGL':
				$url = $this->urls($isbn, $type);
				$t = read_link($url);
				$this->vurl = $url;
				break;

				/***************************************** AMAZON ********************/
			case 'AMAZO':
				$isbns = $this->isbn->isbns($isbn);
				$url = $this->urls($isbns['isbn13'], $type);
				$t = read_link($url);
				if (strlen($t) == 0) {
					$url = $this->urls($isbns['isbn10'], $type);
					$t = read_link($url);
				}
				$this->vurl = $url;
				break;
		}
		if (isset($t)) {
			file_put_contents($file, $t);
		} else {
			$t = '';
		}
		return ($t);
	}
	function file_locate($isbn, $type)
	{
		/* 978-8585-7461-62 */
		$isbn = $this->isbns($isbn);
		$isbn = $isbn['isbn13'];
		$part1 = substr($isbn, 0, 3);
		$part2 = substr($isbn, 3, 4);
		$part3 = substr($isbn, 7, 10);
		$dir = '__cached';
		check_dir($dir);
		check_dir($dir . '/' . $part1);
		check_dir($dir . '/' . $part1 . '/' . $part2);
		$file = $dir . '/' . $part1 . '/' . $part2 . '/' . $isbn . '-' . $type . '.file';
		return ($file);
	}

	/************* ISBNS ***********************************/
	function isbns($isbn)
	{
		if (is_array($isbn)) {
			$isbn = $isbn['isbn13'];
		}
		$isbn = troca($isbn, '-', '');
		$isbn = troca($isbn, '.', '');
		if (substr($isbn, 0, 3) == '978') {
			$isbn = substr($isbn, 0, 13);
		}
		if (substr($isbn, 0, 2) == '85') {
			$isbn = substr($isbn, 0, 10);
		}
		$rsp = array();

		if (strlen($isbn) == 13) {
			$rsp['isbn13'] = $isbn;
			$rsp['isbn10'] = isbn13to10($isbn);
		} else {
			$rsp['isbn10'] = $isbn;
			$rsp['isbn13'] = isbn10to13($isbn);
		}

		$rsp['isbn10f'] = substr($rsp['isbn10'], 0, 2) . '-' . substr($rsp['isbn10'], 2, 5) . '-' . substr($rsp['isbn10'], 7, 2) . '-' . substr($rsp['isbn10'], 9, 1);
		$rsp['isbn13f'] = substr($rsp['isbn13'], 0, 3) . '-' . substr($rsp['isbn13'], 3, 4) . '-' . substr($rsp['isbn13'], 7, 5) . '-' . substr($rsp['isbn13'], 12, 1);
		return ($rsp);
	}

	function format($isbn)
	{
		$isbn = strtoupper($isbn);
		$s = sonumero($isbn);
		if (substr($isbn, strlen($isbn) - 1, 1) == 'X') {
			$s .= 'X';
		}
		return ($s);
	}
}
