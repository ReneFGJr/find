<?php

namespace App\Models;

use CodeIgniter\Model;

class FindSearch extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'findsearches';
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

	function search()
		{
			$sx = bs('
			<form>
			<div class="input-group mb-3 my-4">				
				<input type="text" class="form-control" 
							placeholder="'.lang('find.search_info').'" 
							aria-label="'.lang('find.search_info').'" 
							aria-describedby="basic-addon2"
							>
				<div class="input-group-append">
				<button class="btn btn-danger" type="button">'.lang('find.search').'</button>				
				</div>
			</div>
			</form>
			');
			return $sx;
		}
}
