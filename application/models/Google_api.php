<?php
class google_api extends CI_model
{
	function book($isbn) {
		$rsp = array('count' => 0);

		$type = 'GOOGL';
		$t = $this->isbn->get($isbn,$type);
		$w = (array)json_decode($t);
		$w['serie'] = '';
		$w['subject']= array();
		/*******************************************************************************/

		if ($w['totalItems'] > 0) {
			$rsp['expressao']['genere'] = $w['kind'];
			$w = (array)$w['items'][0];
			$w = (array)$w['volumeInfo'];
			$rsp['titulo'] = trim((string)$w['title']);
			if (isset($w['subtitle'])) {
				$rsp['titulo'] .= ': ' . trim($w['subtitle']);
			}
			$rsp['titulo'] = troca($rsp['titulo'], ' - ',': ');
			$rsp['titulo'] = nbr_author($rsp['titulo'],18);
			/********** Autores ****************************/
			$rsp['authors'] = $w['authors'];
			for ($q=0;$q < count($rsp['authors']);$q++)
			{
				$rsp['authors'][$q] = nbr_author($rsp['authors'][$q],7);
			}


			/********** Data ****************************/
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
			$rsp['totalItems'] = 1;
			$rsp['url'] = $this->isbn->vurl;
			$rsp['type'] = $type;
		} else {
			$rsp = $w;
			$rsp['error'] = 1;
			$rsp['error_msg'] = msg('ISBN_not_found');
			$rsp['totalItems'] = 0;
			$rsp['url'] = '';
		}
		return ($rsp);
	}

}
?>