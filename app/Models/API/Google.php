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
		
		$type = 'GOOGL';
		$t = $this->isbn->get($isbn,$type);
		$w = (array)json_decode($t);
		$rsp['serie'] = '';
		$rsp['cover'] = '';
		$rsp['editora'] = '';
		$rsp['subject']= array();
		$rsp['item'] = $id;
		/*******************************************************************************/
		
		if ($w['totalItems'] > 0) {
			$rsp['expressao']['genere'] = $w['kind'];
			$w = (array)$w['items'][0];
			$w = (array)$w['volumeInfo'];
			$rsp['title'] = trim((string)$w['title']);
			if (isset($w['subtitle'])) {
				$rsp['title'] .= ': ' . trim($w['subtitle']);
			}
			$rsp['title'] = troca($rsp['title'], ' - ',': ');
			$rsp['title'] = nbr_author($rsp['title'],18);
			/********** Autores ****************************/
			if (isset($w['authors']))
			{
				$rsp['authors'] = $w['authors'];
				for ($q=0;$q < count($rsp['authors']);$q++)
				{
					$rsp['authors'][$q] = nbr_author($rsp['authors'][$q],7);
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
