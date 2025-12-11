<?php
/* Documentacao: https://api.mercadoeditorial.org/documentacao/ */
class Mercadoeditorial_api extends CI_model
{
	var $api_key = '1a0e9c158963e5afa858d552091225e3';
	/* Link para testes */
	//var $url = 'https://sandbox.mercadoeditorial.org/api/v1/requisitar_livro_unico';

	/* Link produção */
	//var $url = 'https://api.mercadoeditorial.org/api/v1/requisitar_livro_unico';
	var $url = 'https://api.mercadoeditorial.org/api/v1.2/book';

	function book($isbn,$id) {
		$rsp = array('count' => 0);
		$rsp['totalItems'] = 0;
		$type = 'MERCA';
		$t = $this->isbn->get($isbn,$type);
		$w = (array)json_decode($t);

		$erro = (array)$w['status'];
		$erro = $erro['code'];
		if ($erro == '101')
		{
			return($rsp);
		}


		$w = (array)$w['books'];
		$w = (array)$w[0];

		$rsp['serie'] = '';
		$rsp['cover'] = '';
		$rsp['editora'] = '';
		$rsp['subject']= array();
		$rsp['item'] = $id;
		$rsp['url'] = 'https://mercadoeditorial.org/books/view/'.$isbn;
		/*******************************************************************************/
	
		$rsp['expressao']['genere'] = (string)$w['formato'];
		if ($rsp['expressao']['genere'] == 'BOOK') { $rsp['expressao']['genere'] = 'LIVRO'; }
		$rsp['title'] = trim((string)$w['titulo']);
		if ((isset($w['subtitulo']) and strlen($w['subtitulo']) > 0)) {
			$rsp['title'] .= ': ' . trim($w['subtitulo']);
		}
		$rsp['title'] = troca($rsp['title'], ' - ',': ');
		$rsp['title'] = nbr_author($rsp['title'],18);

		/********** Autores ****************************/

		for ($q=0;$q < count($w['contribuicao']);$q++)
		{
			$au = (array)$w['contribuicao'][$q];
			
			$autor = trim($au['nome']).' '.trim($au['sobrenome']);
			$rsp['authors'][$q] = nbr_author($autor,7);
		}	

		/********** DESCRICAO **************************/
		$rsp['descricao'] = $w['sinopse'];


		/********** IMAGENS **************************/
		$cover = (array)$w['imagens'];
		$cover = (array)$cover['imagem_primeira_capa'];
		if (isset($cover['grande']))
		{
			$cover = $cover['grande'];
		}		
		$rsp['cover'] = $cover;

		/********* medidas **************************/
		$m = (array)$w['medidas'];
		/********** Páginas ****************************/
		if (isset($m['paginas'])) {
			$rsp['pages'] = $m['paginas'];
		} else {
			$rsp['pages'] = '';
		}		

		/********** Idioma ****************************/
		if (isset($w['idioma'])) {
			$rsp['expressao']['idioma'] = $this->languages->code($w['idioma']);
		} else {
			$rsp['expressao']['idioma'] = '';
		}	
		
		/********** Data ****************************/
		if (isset($w['ano_edicao'])) {
			$rsp['data'] = $w['ano_edicao'];
		} else {
			$rsp['data'] = '';
		}

		/*********************************************/
		
		if (isset($w['colecao'])) {
			$rsp['serie'] = $w['colecao'];
		} else {
			$rsp['serie'] = '';
		}	


		/******* editora ******************************/
		$ed = (array)$w['editora'];
		if (isset($ed['nome_editora']))
		{
			$editora = $ed['nome_editora'];
			$editora = $ed['selo_editorial'];
			$rsp['editora'] = $editora;
		}	
		/******** assuntos ****************************/

		
		$sub = (array)$w['catalogacao'];
		$sub = troca($sub['palavras_chave'],',',';');
		$sub = splitx(';',$sub);
		$rsp['subject'] = $sub;

		$rsp['totalItems'] = 1;	


		return($rsp);		
	}

	function lista_editoras()
			{
				$rdf = new rdf;
				$idioma = 'pt';

				$url = $this->url;
				$url = troca($url,'requisitar_livro_unico','requisitar_lista_de_editoras');
				$t = read_link($url);
				$w = json_decode($t);
				$nome = 'Mercadoeditorial.org';
				$ids = $rdf->rdf_concept_create('CorporateBody', $nome, '', $idioma);
				$idn = $rdf->rdf_name('https://mercadoeditorial.org/companies/');
				$rdf->set_propriety($ids,'hasURL',0,$idn);


				$ed = (array)$w->editora;
				
				for ($r=0;$r < count($ed);$r++)
				{
					$line = (array)$ed[$r];
					$nome = $line['nome_editora'];
					$cnpj = $line['cnpj_editora'];
					
					$idc = $rdf->rdf_concept_create('Editora', $nome, '', $idioma);
					$idn = $rdf->rdf_concept_create('CNPJ', $cnpj, '', $idioma);
					$rdf->set_propriety($idc,'brgov:is_cnpj',$idn);
					$rdf->set_propriety($idc,'brgov:is_source',$ids);

					//$rdf->set_propriety($ide,'isAppellationOfManifestation',$idm);
					echo '<hr>';
				}
				echo '<pre>';
				print_r($ed);
				echo '</pre>';

			}	
}
?>