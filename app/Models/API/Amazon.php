<?php

namespace App\Models\API;

use CodeIgniter\Model;

class Amazon extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'amazons';
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

	function book($isbn) {
		$type = 'AMAZO';
		$amazon = array();
		$amazon['totalItems'] = 0;
		return($amazon);
		
		$t = $this->isbn->get($isbn,$type);

		$w = array();
		$w['serie'] = '';
		$w['subject']= array();
		/************************* Título */
		$f = 'id="productTitle"';
		$title = substr($t,strpos($t,$f)+strlen($f),strlen($t));
		$title = substr($title,strpos($title,'>')+1,strlen($title));
		$title = substr($title,0,strpos($title,'</span>'));
		$w['title'] = nbr_author($title,18);

		/************************* Editora */
		$f = '<br/><br>Editora:</b>';
		if (strpos($t,$f) > 0)
		{
			$s = substr($t,strpos($t,$f)+strlen($f),strlen($t));
			$s = substr($s,0,strpos($s,'</li>'));
			$w['editora'] = nbr_author($s,17);
			$w['data'] = '';
		} else {
			$w['editora'] = '';
			$w['data'] = '';			
		}

		/************************************* Descricao */
		$f = '<div>';
		$s = substr($t,strpos($t,$f)+strlen($f),strlen($t));
		if (strpos($s,'<em></em>') > 0)
		{
			$s = substr($s,strpos($s,$f)+strlen($f),strlen($s));
			$s = substr($s,0,strpos($s,'</div>'));
			$w['descricao'] = trim(strip_tags($s));
		} else {
			$w['descricao'] = '';
		}

		/************************* Ano ****************/
		if (strpos($w['editora'],'(') > 0)
		{
			$w['editora'] = nbr_author(substr($w['editora'],0,strpos($w['editora'],'(')),7);
			$ano = sonumero(substr($w['editora'],strpos($w['editora'],'('),30));
			$ano = substr($ano,strlen($ano)-4,4);
			if (($ano >= 1900) and ($ano <= (date("Y")+2)))
			{
				$w['data'] = $ano;
			} else {
				$w['data'] = '';
			}

		}

		/************************* Idioma */
		$f = '<br/><br>Idioma:</b>';
		$s = substr($t,strpos($t,$f)+strlen($f),strlen($t));
		$s = substr($s,0,strpos($s,'</li>'));
		$w['expressao'] = array('genere'=>'books','idioma'=>$s);

		/************************* Dimensões do produto */			
		$f = 'Dimensões da embalagem:';
		if (strpos($t,$f) > 0)
		{
			$s = substr($t,strpos($t,$f)+strlen($f),strlen($t));
			$s = substr($s,0,strpos($s,'</li>'));
			$s = strip_tags($s);
			$s = troca($s,chr(13),'');
			$s = troca($s,chr(10),'');
			$s = trim($s);
			$w['size'] = $s;
		}

		$f = 'Dimensões do produto:';
		if (strpos($t,$f) > 0)
		{
			$s = substr($t,strpos($t,$f)+strlen($f),strlen($t));
			$s = substr($s,0,strpos($s,'</li>'));
			$s = strip_tags($s);
			$s = troca($s,chr(13),'');
			$s = troca($s,chr(10),'');
			$s = trim($s);
			$w['size'] = $s;
		}

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
			if (strpos($s,'(') > 0) 
			{ 
				$s = substr($s,0,strpos($s,'(')); 
			}
			$s = trim($s);
			if (strlen($s) > 0)
			{
				$s = nbr_author($s,7);
				array_push($authors,$s);
			}
		}

		/************************* Imagem */
		$f = 'data-a-dynamic-image="';
		$s = substr($t,strpos($t,$f)+strlen($f)+7,200);		
		$s = substr($s,0,strpos($s,'&quot;'));
		$s = troca($s,'200_.jpg','800_.jpg');
		$w['cover'] = $s;

		$w['authors'] = $authors;		
		$w['authors'] = array();		
		$w['error'] = 0;
		$w['type'] = $type;

		if (strlen($title) > 0)
		{
			$w['error_msg'] = 'ISBN_inported';
			$w['totalItems'] = 1;
			$w['error'] = 0;
			$w['url'] = $this->isbn->vurl;
		} else {
			$w['error'] = 1;
			$w['error_msg'] = 'ISBN_not_found';
			$w['totalItems'] = 0;
			$w['url'] = '';
		}
		return($w);
		/*******************************************************************************/
	}	
}
