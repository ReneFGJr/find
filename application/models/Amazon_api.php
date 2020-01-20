<?php
class Amazon_api extends CI_model
{
	function book($isbn) {
		$rsp = array('count' => 0);	

/* Imagem */

		/* https://www.amazon.com.br/dp/8571933421 */
		$url = 'https://www.amazon.com.br/dp/' . $isbn;
		$t = read_link($url);
		$w = array();

		/************************* Título */
		$f = 'id="productTitle"';
		$title = substr($t,strpos($t,$f)+strlen($f),strlen($t));
		$title = substr($title,strpos($title,'>')+1,strlen($title));
		$title = substr($title,0,strpos($title,'</span>'));
		$w['title'] = $title;

		/************************* Editora */
		$f = '<b>Editora:</b>';
		$s = substr($t,strpos($t,$f)+strlen($f),strlen($t));
		$s = substr($s,0,strpos($s,'</li>'));
		$w['editora'] = $s;	

		/************************* Idioma */
		$f = '<b>Idioma:</b>';
		$s = substr($t,strpos($t,$f)+strlen($f),strlen($t));
		$s = substr($s,0,strpos($s,'</li>'));
		$w['expressao'] = array('genere'=>'books','idioma'=>$s);

		/************************* Dimensões do produto */
		$f = 'Dimensões do produto:';
		$s = substr($t,strpos($t,$f)+strlen($f),strlen($t));
		$s = substr($s,0,strpos($s,'</li>'));
		$s = strip_tags($s);
		$s = troca($s,chr(13),'');
		$s = troca($s,chr(10),'');
		$s = trim($s);
		$w['size'] = $s;

		/************************* Peso */
		$f = '<b>Peso de envio:</b>';
		$s = substr($t,strpos($t,$f)+strlen($f),strlen($t));
		$s = substr($s,0,strpos($s,'</li>'));
		$s = trim($s);
		$w['weight'] = $s;		

		/************************* Paginas */
		$f = 'páginas</li>';
		$s = substr($t,strpos($t,$f)-strlen($f)-20,50);
		$s = substr($s,strpos($s,'<b>'),strlen($s));
		$s = substr($s,0,strpos($s,'</li>'));
		$w['pages'] = sonumero($s);		

		/************************* Authors */
		$f = 'class="author notFaded"';
		$ts = $t;
		$authors = array();
		while (strpos($ts,$f) > 0)
		{
			$s = substr($ts,strpos($ts,$f)+strlen($f),strlen($ts));
			$s = substr($s,strpos($s,'>')+1,strlen($s));
			$s = substr($s,0,strpos($s,'</span>'));
			$ts = substr($ts,strpos($ts,$f)+strlen($f),strlen($ts));			
			$s = trim(strip_tags($s));
			if (strpos($s,'(') > 0) { $s = substr($s,0,strpos($s,'(')); }
			$s = trim($s);
			if (strlen($s) > 0)
			{
				array_push($authors,$s);
			}
		}

		/************************* Imagem */
		$f = 'data-a-dynamic-image="';
		$s = substr($t,strpos($t,$f)+strlen($f)+1,strlen($t));
		$s = substr($s,0,strpos($s,';'));
		$w['cover'] = $s;

		$w['author'] = $authors;		
		$w['error'] = 0;
		$w['error_msg'] = 'ISBN_inported';
		$w['count'] = 1;
		return($w);
		/*******************************************************************************/
	}
}
?>