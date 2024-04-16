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

	protected $typeFields        = [
		'hidden','string:100','text',
		'string:100','string:100','string:100',
		'set:'.LIBRARY,'string:100','string:100',
		'string:100','string:100','text',
		'text','string:100'

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

	function edit($lib,$act,$id)
		{
			$this->path = PATH.MODULE.'popup/place/'.$lib;
			$this->path_back = 'wclose';
			$this->id = $id;
			$sx = form($this);
			return $sx;
		}

	function view_library($id)
		{
			$this->where('lp_LIBRARY',$id);
			$this->orderBy('lp_name','ASC');
			$dt = $this->findAll();
			$sx = '<ul>';
			for ($r=0;$r < count($dt);$r++)
				{
					$line = $dt[$r];
					$link = onclick(PATH.MODULE.'popup/place/'.LIBRARY.'/edit/'.$line['id_lp']).bsicone('edit').'</span>';
					$sx .= '<li>'.h($line['lp_name'].' '.$link,4).'</li>';	
					$sx .= '<ul>';
					$fld = array('lp_address','lp_contato','lp_email','lp_telefone','lp_responsavel');
					for ($f=0;$f < count($fld);$f++)
						{
							$sx .= '<li>'.lang('find.'.$fld[$f]).': <b>' 
									. $line[$fld[$f]].'</b></li>';
						}
					$sx .= '</ul>';
				}
			$sx .= '</ul>';

			if (count($dt) == 0)
				{
					$sx = bsmessage('find.library_place.empty',3);
				}

			$sx .= onclick(PATH.MODULE.'popup/place/0',800,600,'btn btn-outline-primary');
			$sx .= lang('find.library_place.add');
			$sx .= '</span>';
			return $sx;
		}
}
