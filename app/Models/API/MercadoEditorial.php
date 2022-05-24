<?php

namespace App\Models\API;

use CodeIgniter\Model;

class MercadoEditorial extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'mercadoeditorials';
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

	var $api_key = 'chaveAPI';
	/* Link para testes */
	//var $url = 'https://sandbox.mercadoeditorial.org/api/v1/requisitar_livro_unico';
	//https://api.mercadoeditorial.org/documentacao/v1.2

	function __construct()
	{
		$this->api_key = getenv("api_key_mercadoeditorial");
	}

	function book($isbn,$id) {

		$ISBN = new \App\Models\Isbn\Isbn();
		$Language = new \App\Models\Languages\Language();
		$rsp = array();

		/* Recupera Dados */
		$type = 'MERCA';
		$t = $ISBN->get($isbn,$type);

		$w = (array)json_decode($t);

		if (count($w) == 0) { return $rsp; }
		$erro = $w['status_code'];
		if ($erro == '101')
		{
			return($rsp);
		}

		$w = (array)$w['livro'];
		$rsp['serie'] = '';
		$rsp['cover'] = '';
		$rsp['editora'] = '';
		$rsp['subject']= array();
		$rsp['item'] = $id;
		//$rsp['url'] = 'https://mercadoeditorial.org/books/view/'.$isbn;
		$rsp['url'] = 'https://api.mercadoeditorial.org/api/v1.2/book?isbn='.$isbn;
		/*******************************************************************************/

		$rsp['expressao']['genere'] = (string)$w['formato'];
		$rsp['title'] = trim((string)$w['titulo']);
		if ((isset($w['subtitulo']) and strlen($w['subtitulo']) > 0)) {
			$rsp['title'] .= ': ' . trim($w['subtitulo']);
		}
		$rsp['title'] = troca($rsp['title'], ' - ',': ');
		$rsp['title'] = nbr_author($rsp['title'],8);

		/********** Autores ****************************/
		for ($q=0;$q < count($w['autores']);$q++)
		{
			$au = (array)$w['autores'][$q];
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
			$rsp['expressao']['idioma'] = $Language->code($w['idioma']);
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
		$sub = troca($sub['assuntos'],',',';');
		$sub = explode(';',$sub);
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
				}
			}		
}
