<?php

namespace App\Models\Book;

use CodeIgniter\Model;

class Tombo extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'itens';
	protected $primaryKey           = 'id_i';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_i','i_tombo','i_manitestation','i_identifier',
		'i_type','i_library_classification','i_aquisicao',
		'i_year','i_status','i_usuario',
		'i_titulo','i_status','i_library','i_library_place',
		'i_exemplar'
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

	function exemplar($isbn)
		{
			$dt = $this
			->select("count(*) as total, i_identifier")
			->where('i_library',LIBRARY)
			->where('i_identifier',$isbn)
			->groupBy('i_identifier')
			->findAll();

			if (count($dt) > 0)
				{
					$total = $dt[0]['total'];
				} else {
					$total = 1;
				}
			return $total;
		}

	function next()
		{
			$dt = $this->select("max(i_tombo) as next")
				->where('i_tombo > 0')
				->where('i_library', LIBRARY)
				->findAll();

			if (count($dt) > 0)
				{
					$max = $dt[0]['next']+1;
				} else {
					$max = 1;
				}
			if ($max == 0) { $max = 1; }
			return $max;
		}

}
