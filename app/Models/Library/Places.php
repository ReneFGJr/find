<?php

namespace App\Models\Library;

use CodeIgniter\Model;

class Places extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'library_place';
	protected $primaryKey           = 'id_lp';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_lp','lp_name','lp_address',
		'lp_coord_x','lp_coord_y','lp_email',
		'lp_LIBRARY','lp_contato','lp_responsavel',
		'lp_telefone','lp_site','lp_obs',
		'lp_obs','lp_class_type'

	];

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

	function view_library($id)
		{
			$this->where('lp_LIBRARY',$id);
			$this->orderBy('lp_name','ASC');
			$dt = $this->findAll();
			$tela = '<ul>';
			for ($r=0;$r < count($dt);$r++)
				{
					$line = $dt[$r];
					$tela .= '<li>'.$line['lp_name'].'</li>';	
				}
			$tela .= '</ul>';
			return $tela;
		}
}
