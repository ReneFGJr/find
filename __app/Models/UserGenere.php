<?php

namespace App\Models;

use CodeIgniter\Model;

class UserGenere extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'users';
	protected $primaryKey           = 'id_us';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $form			        = 'formUserGenere';
	protected $allowedFields        = [
		'id_us','us_genero'
	];

	protected $typeFields        = [
		'hi',
		'select:opendata.genere_0:opendata.genere_1:opendata.genere_2:opendata.genere_3:opendata.genere_4:openda.tagenere_5',
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
			$this->id = $th->id;
			$this->path = $th->path;
			$this->path_back = $th->path_back;

			$sx = form($this);

			return $sx;
		}	
}
