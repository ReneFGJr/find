<?php

namespace App\Models;

use CodeIgniter\Model;

class Classification extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'classifications';
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

	function sections()
		{
			$sx = '<ul>';
			$sx .= '<li>';
			$sx .= 'Literatura';
			$sx .= '</li>';
			$sx .= '</ul>';

			$sx .= $this->indexes();

			return $sx;
		}

	function indexes()
		{
			$ndx = array('author','publisher','subject','year');
			$sx = '';
			$sx .= h(lang('find.indexes'),6);
			$sx .= '<ul class="small">';
			foreach($ndx as $type)
				{				
					$sx .= '<li>';
					$sx .= '<a href="#'.$type.'">';
					$sx .= lang('find.'.$type);
					$sx .= '</a>';
					$sx .= '</li>';
				}
			$sx .= '</ul>';
			return $sx;	
		}
}
