<?php
class google_api extends CI_model
{
	function book($isbn) {
		$rsp = array('count' => 0);

		$url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn;
		$t = read_link($url);
		$w = (array)json_decode($t);

		/*******************************************************************************/

		if ($w['totalItems'] > 0) {
			$rsp['expressao']['genere'] = $w['kind'];
			$w = (array)$w['items'][0];
			$w = (array)$w['volumeInfo'];
			$rsp['titulo'] = trim((string)$w['title']);
			if (isset($w['subtitle'])) {
				$rsp['titulo'] .= ': ' . trim($w['subtitle']);
			}
			/********** Autores ****************************/
			$rsp['authors'] = $w['authors'];
			if (isset($w['publishedDate'])) {
				$rsp['data'] = $w['publishedDate'];
			} else {
				$rsp['data'] = '';
			}

			/********** Descricao ****************************/
			if (isset($w['description'])) {
				$rsp['descricao'] = $w['description'];
			} else {
				$rsp['descricao'] = '';
			}

			/********** Páginas ****************************/
			if (isset($w['pageCount'])) {
				$rsp['pages'] = $w['pageCount'];
			} else {
				$rsp['pages'] = '';
			}
			/********** Idioma ****************************/
			if (isset($w['language'])) {
				$rsp['expressao']['idioma'] = $w['language'];
			} else {
				$rsp['expressao']['idioma'] = '';
			}
			$rsp['error'] = 0;
			$rsp['error_msg'] = msg('ISBN_inported');
		} else {
			$rsp['error'] = 1;
			$rsp['error_msg'] = msg('ISBN_not_found');
		}

        //echo '<pre><span style="color: blue">';
        //print_r($rsp);
        //echo '</span></pre>';
		return ($rsp);
	}

}
?>