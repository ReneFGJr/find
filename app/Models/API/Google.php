<?php

namespace App\Models\API;

use CodeIgniter\Model;

class Google extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'googles';
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

function book($isbn,$id) {
		$rsp = array('count' => 0);

		$ISBN = new \App\Models\Isbn\Isbn();
		$Language = new \App\Models\Languages\Language();		
		
		$type = 'GOOGLE';
		$t = $ISBN->get($isbn,$type);
		$w = (array)json_decode($t);
		
		$rsp['serie'] = '';
		$rsp['cover'] = '';
		if (isset($w['items']))
			{
				$wi = (array)$w['items'][0];
				if (isset($wi['volumeInfo']))
					{
						$wv = (array)$wi['volumeInfo'];
						if (isset($wv['imageLinks']))
							{
								$wimg = (array)$wv['imageLinks'];
								if (isset($wimg['thumbnail']))
									{	
										$rsp['cover'] = $wimg['thumbnail'];
									}							
							}						
					}
				}
		$rsp['editora'] = '';
		$rsp['subject']= array();
		$rsp['item'] = $id;
		/*******************************************************************************/

		if (count($w)> 0) {
			$rsp['expressao']['genere'] = $w['kind'];
			if (strpos($rsp['expressao']['genere'],'#') > 0)
				{ 
					$rsp['expressao']['genere'] = substr($rsp['expressao']['genere'],0,strpos($rsp['expressao']['genere'],'#'));
				}
			$w = (array)$w['items'][0];
			$w = (array)$w['volumeInfo'];
			$rsp['title'] = trim((string)$w['title']);
			if (isset($w['subtitle'])) {
				$rsp['title'] .= ': ' . mb_strtolower(trim($w['subtitle']));
			}
			$rsp['title'] = troca($rsp['title'], ' - ',': ');
			//$rsp['title'] = nbr_author($rsp['title'],8);

			/********** Autores ****************************/
			if (isset($w['authors']))
			{
				$authors = $w['authors'];
				for ($q=0;$q < count($authors);$q++)
					{
						if (strpos($authors[$q],'/') > 0)
							{
								$nome2 = trim(substr($authors[$q],strpos($authors[$q],'/')+1,strlen($authors[$q])));
								array_push($authors,$nome2);
								$authors[$q] = trim(substr($authors[$q],0,strpos($authors[$q],'/')));
							}
					}
				$rsp['authors'] = $authors;
				for ($q=0;$q < count($authors);$q++)
				{
					$rsp['authors'][$q] = nbr_author($authors[$q],7);
				} 
			} else {
				$rsp['authors'] = array();
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
			$rsp['url'] = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn;
			$rsp['type'] = $type;
		} else {
			$rsp = $w;
		}
		return ($rsp);
	}	
}
