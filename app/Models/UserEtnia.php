<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEtnia extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'users_add';
	protected $primaryKey           = 'id_ua';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $form			        = 'formUserEtnia';
	protected $allowedFields        = [
		'id_ua','us_raca'
	];

	protected $typeFields        = [
		'hi',
		'select:opendata.breed_0:opendata.breed_1:opendata.breed_2:opendata.breed_3:opendata.breed_4:opendata.breed_5',
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

	function form($th)
		{
			$dt = $this->where('ua_us',$th->id)->findAll();
			$this->id = $dt[0]['id_ua'];
			echo '===>'.$this->id;
			$this->path = $th->path;
			$this->path_back = $th->path_back;

			$sx = form($this);

			return $sx;
		}
}
